<?php

$envPath = __DIR__ . '/../.env';

if (!file_exists($envPath)) {
  error_log("Missing .env file at $envPath");
  http_response_code(500);
  die("Service temporarily unavailable.");
}

$env = parse_ini_file($envPath);

$host = $env['DB_HOST'] ?? '';
$user = $env['DB_USER'] ?? '';
$pass = $env['DB_PASS'] ?? '';
$db   = $env['DB_NAME'] ?? '';
$appEnv = $env['APP_ENV'] ?? 'production';

// Make mysqli throw exceptions on error instead of silently returning false,
// so bugs (e.g. a mistyped column name) fail loudly during development
// rather than producing confusing blank pages / undefined-index warnings.
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {
  $conn = new mysqli($host, $user, $pass, $db);
  $conn->set_charset('utf8mb4');
} catch (mysqli_sql_exception $e) {
  // Log the real error for developers, but NEVER show connection details
  // (host/user/db name) or raw driver errors to visitors - that's
  // information disclosure that helps an attacker fingerprint your stack.
  error_log("DB connection failed: " . $e->getMessage());

  http_response_code(500);
  if ($appEnv === 'production') {
    die("We're experiencing a technical issue. Please try again shortly.");
  } else {
    die("Database Connection Failed (dev mode): " . htmlspecialchars($e->getMessage()));
  }
}
