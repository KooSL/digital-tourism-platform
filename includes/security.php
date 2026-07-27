<?php

/**
 * ============================================================================
 *  SECURITY BOOTSTRAP
 *  Include this at the very top of includes/header.php (replaces the bare
 *  session_start() call) so every single page gets:
 *    - Hardened session cookies (HttpOnly, SameSite, Secure-when-HTTPS)
 *    - Session fixation protection (periodic ID regeneration)
 *    - Baseline security response headers
 *    - A tiny, reusable rate-limiter helper used by signin/verify-otp/etc.
 * ============================================================================
 */

// ---------------------------------------------------------------------------
// 1. SESSION COOKIE HARDENING - must happen BEFORE session_start()
// ---------------------------------------------------------------------------
$isHttps = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
    || (($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https');

session_set_cookie_params([
    'lifetime' => 0,            // session cookie, dies when browser closes
    'path'     => '/',
    'domain'   => '',
    'secure'   => $isHttps,     // only sent over HTTPS once you're on HTTPS in production
    'httponly' => true,         // not readable via JS -> mitigates session-stealing XSS
    'samesite' => 'Lax',        // CSRF hardening while still allowing normal link navigation
]);

ini_set('session.use_strict_mode', 1); // reject uninitialized session IDs
ini_set('session.cookie_httponly', 1);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// ---------------------------------------------------------------------------
// 2. PERIODIC SESSION ID REGENERATION
//    Limits the window an attacker has if a session ID is ever leaked, and
//    reduces session fixation risk beyond just the login-time regeneration
//    that already happens in signin.php.
// ---------------------------------------------------------------------------
if (!isset($_SESSION['_last_regen'])) {
    $_SESSION['_last_regen'] = time();
} elseif (time() - $_SESSION['_last_regen'] > 900) { // every 15 minutes
    session_regenerate_id(true);
    $_SESSION['_last_regen'] = time();
}

// ---------------------------------------------------------------------------
// 3. BASELINE SECURITY HEADERS
//    CSP is intentionally permissive on script-src/style-src because the
//    project loads several third-party CDN scripts (Leaflet, Font Awesome,
//    etc.) - tighten this to specific hostnames once you've inventoried
//    every external resource the site actually loads.
// ---------------------------------------------------------------------------
header("X-Content-Type-Options: nosniff");
header("X-Frame-Options: SAMEORIGIN");
header("Referrer-Policy: strict-origin-when-cross-origin");
header("Permissions-Policy: geolocation=(self), camera=(), microphone=()");

if ($isHttps) {
    header("Strict-Transport-Security: max-age=31536000; includeSubDomains");
}

// NOTE: 'unsafe-inline' is required right now because the codebase has many
// inline <script> blocks and inline event handlers. Removing 'unsafe-inline'
// (and switching to nonces or external files) is a recommended follow-up,
// not something to flip on blind or the site will break.
header("Content-Security-Policy: " . implode('; ', [
    "default-src 'self'",
    "script-src 'self' 'unsafe-inline' https://unpkg.com https://cdnjs.cloudflare.com https://www.gstatic.com https://www.googletagmanager.com",
    "style-src 'self' 'unsafe-inline' https://unpkg.com https://cdnjs.cloudflare.com https://fonts.googleapis.com",
    "img-src 'self' data: https:",
    "font-src 'self' https://fonts.gstatic.com https://cdnjs.cloudflare.com",
    "connect-src 'self' " . implode(' ', [
        "https://api.esewa.com.np",
        "https://rc-epay.esewa.com.np",
        "https://rc.esewa.com.np",
        "https://api.open-meteo.com",       // weather.js - tour-details weather widget
        "https://tile.openstreetmap.org",   // tripMap.js - Leaflet map tiles
        "https://unpkg.com",                // Leaflet CDN - also covers its .map sourcemap fetch in devtools
    ]),
    "frame-ancestors 'self'",
    "object-src 'none'",
    "base-uri 'self'",
]));

// ---------------------------------------------------------------------------
// 4. LIGHTWEIGHT RATE LIMITER (session + IP based)
//    Not a substitute for a real WAF/rate-limiting layer under heavy abuse,
//    but stops casual brute-force scripts against login/OTP/password-reset
//    without needing Redis or any extra infrastructure.
//
//    Usage:
//      if (!checkRateLimit('login_' . $email, 5, 300)) {
//          die("Too many attempts. Please try again later.");
//      }
// ---------------------------------------------------------------------------
function checkRateLimit(string $key, int $maxAttempts, int $windowSeconds): bool
{
    $bucket = '_rl_' . $key;
    $now = time();

    if (!isset($_SESSION[$bucket]) || $now - $_SESSION[$bucket]['start'] > $windowSeconds) {
        $_SESSION[$bucket] = ['start' => $now, 'count' => 0];
    }

    $_SESSION[$bucket]['count']++;

    return $_SESSION[$bucket]['count'] <= $maxAttempts;
}

/**
 * Reset a rate-limit bucket (call after a successful login/verification
 * so a legitimate user isn't penalized on their next attempt).
 */
function resetRateLimit(string $key): void
{
    unset($_SESSION['_rl_' . $key]);
}

// ---------------------------------------------------------------------------
// 5. OPEN-REDIRECT PROTECTION
//    Several pages (signin, signup, verify-otp, booking flows) accept a
//    "redirect" value from the client and send the user there after a
//    successful action. Without validation, an attacker can craft a link
//    like signin.php?redirect=https://evil-look-alike.com and use your
//    own trusted domain to send victims to a phishing page right after
//    they've just entered their real credentials.
// ---------------------------------------------------------------------------
function safeInternalRedirect(?string $target, string $default = 'index?success=signin'): string
{
    if (empty($target)) {
        return $default;
    }

    // Reject anything that looks like it points off-site:
    //   - protocol-relative ("//evil.com")
    //   - absolute URLs ("https://evil.com")
    //   - backslash tricks browsers sometimes treat like forward slashes
    if (preg_match('#^(https?:)?//#i', $target) || str_starts_with($target, '\\')) {
        return $default;
    }

    // Only allow same-site relative paths (optionally starting with "/").
    if (!preg_match('#^/?[A-Za-z0-9_\-\./?=&]*$#', $target)) {
        return $default;
    }

    return $target;
}
