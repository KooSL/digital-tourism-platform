<?php

require_once __DIR__ . '/../config/db.php';

define('FCM_LOG_FILE', __DIR__ . '/../logs/fcm_notifications.log');

function writeLog($level, $message, $context = [])
{
    $timestamp = date('Y-m-d H:i:s');
    $line      = "[$timestamp] [$level] $message";

    if (!empty($context)) {
        $line .= ' | ' . json_encode($context, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }

    $line .= PHP_EOL;

    // Rotate log if over 5MB
    if (file_exists(FCM_LOG_FILE) && filesize(FCM_LOG_FILE) > 5 * 1024 * 1024) {
        $archive = str_replace('.log', '_' . date('Ymd_His') . '.log', FCM_LOG_FILE);
        rename(FCM_LOG_FILE, $archive);
    }

    file_put_contents(FCM_LOG_FILE, $line, FILE_APPEND | LOCK_EX);
}

function base64url_encode($data)
{
    return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
}

function generateJWT($serviceAccount)
{
    $now = time();

    $header  = base64url_encode(json_encode(['alg' => 'RS256', 'typ' => 'JWT']));
    $payload = base64url_encode(json_encode([
        'iss'   => $serviceAccount['client_email'],
        'scope' => 'https://www.googleapis.com/auth/firebase.messaging',
        'aud'   => 'https://oauth2.googleapis.com/token',
        'iat'   => $now,
        'exp'   => $now + 3600,
    ]));

    $data       = $header . '.' . $payload;
    $signature  = '';
    $privateKey = openssl_pkey_get_private($serviceAccount['private_key']);
    openssl_sign($data, $signature, $privateKey, 'SHA256');

    return $data . '.' . base64url_encode($signature);
}

function getAccessToken()
{
    $serviceAccount = json_decode(
        file_get_contents(__DIR__ . '/../config/firebase-service-account.json'),
        true
    );

    $jwt = generateJWT($serviceAccount);

    $ch = curl_init('https://oauth2.googleapis.com/token');
    curl_setopt_array($ch, [
        CURLOPT_POST           => true,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POSTFIELDS     => http_build_query([
            'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
            'assertion'  => $jwt,
        ]),
        CURLOPT_HTTPHEADER => ['Content-Type: application/x-www-form-urlencoded'],
    ]);

    $response = json_decode(curl_exec($ch), true);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if (!isset($response['access_token'])) {
        writeLog('ERROR', 'Failed to get OAuth2 access token', [
            'http_code' => $httpCode,
            'response'  => $response,
        ]);
        return null;
    }

    writeLog('INFO', 'OAuth2 access token obtained successfully');
    return $response['access_token'];
}

// ─── Send to all admins ───────────────────────────────────────────────────────
function sendAdminNotification($title, $body, $url = 'Digital_Tourism_Platform/admin/dashboard')
{
    global $conn;

    $serviceAccount = json_decode(
        file_get_contents(__DIR__ . '/../config/firebase-service-account.json'),
        true
    );
    $projectId = $serviceAccount['project_id'];

    writeLog('INFO', 'Starting notification dispatch', [
        'title' => $title,
        'body'  => $body,
        'url'   => $url,
    ]);

    $accessToken = getAccessToken();
    if (!$accessToken) {
        writeLog('ERROR', 'Aborting — could not obtain access token');
        return false;
    }

    $result = $conn->query("SELECT id, admin_id, token FROM admin_fcm_tokens");
    if (!$result || $result->num_rows === 0) {
        writeLog('WARNING', 'Aborting — no admin tokens found in database');
        return false;
    }

    $totalTokens  = $result->num_rows;
    $successCount = 0;
    $failCount    = 0;
    $fcmUrl       = "https://fcm.googleapis.com/v1/projects/{$projectId}/messages:send";

    writeLog('INFO', "Sending to $totalTokens token(s)");

    while ($row = $result->fetch_assoc()) {
        $payload = json_encode([
            'message' => [
                'token'        => $row['token'],
                'notification' => [
                    'title' => $title,
                    'body'  => $body,
                ],
                'webpush' => [
                    'notification' => [
                        'title' => $title,
                        'body'  => $body,
                        'icon'  => '/Digital_Tourism_Platform/assets/images/airplane-icon.png',
                    ],
                    'fcm_options' => [
                        'link' => 'http://localhost/Digital_Tourism_Platform/' . ltrim($url, '/'),
                    ],
                ],
            ],
        ]);

        $ch = curl_init($fcmUrl);
        curl_setopt_array($ch, [
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => $payload,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER     => [
                'Authorization: Bearer ' . $accessToken,
                'Content-Type: application/json',
            ],
        ]);

        $rawResponse = curl_exec($ch);
        $httpCode    = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError   = curl_error($ch);
        curl_close($ch);

        $response      = json_decode($rawResponse, true);
        $tokenSnippet  = substr($row['token'], 0, 20) . '...';

        if ($httpCode === 200) {
            $successCount++;
            writeLog('SUCCESS', "Notification delivered", [
                'admin_id'     => $row['admin_id'],
                'token_id'     => $row['id'],
                'token'        => $tokenSnippet,
                'http_code'    => $httpCode,
                'fcm_message'  => $response['name'] ?? 'N/A',
            ]);
        } else {
            $failCount++;
            $errorCode    = $response['error']['details'][0]['errorCode'] ?? 'UNKNOWN';
            $errorMessage = $response['error']['message'] ?? 'Unknown error';

            writeLog('ERROR', "Notification failed", [
                'admin_id'      => $row['admin_id'],
                'token_id'      => $row['id'],
                'token'         => $tokenSnippet,
                'http_code'     => $httpCode,
                'error_code'    => $errorCode,
                'error_message' => $errorMessage,
                'curl_error'    => $curlError ?: null,
                'full_response' => $response,
            ]);

            // Auto-remove invalid/expired tokens
            if (in_array($errorCode, ['UNREGISTERED', 'INVALID_ARGUMENT'])) {
                $stmt = $conn->prepare("DELETE FROM admin_fcm_tokens WHERE id = ?");
                $stmt->bind_param("i", $row['id']);
                $stmt->execute();
                writeLog('INFO', "Removed invalid token from DB", [
                    'admin_id' => $row['admin_id'],
                    'token_id' => $row['id'],
                    'token'    => $tokenSnippet,
                ]);
            }
        }
    }

    writeLog('INFO', 'Dispatch complete', [
        'title'   => $title,
        'total'   => $totalTokens,
        'success' => $successCount,
        'failed'  => $failCount,
    ]);

    return $successCount > 0;
}

// ─── Send to one specific admin ───────────────────────────────────────────────
function sendNotificationToAdmin($adminId, $title, $body, $url = 'Digital_Tourism_Platform/admin/dashboard')
{
    global $conn;

    $serviceAccount = json_decode(
        file_get_contents(__DIR__ . '/../config/firebase-service-account.json'),
        true
    );
    $projectId = $serviceAccount['project_id'];

    writeLog('INFO', 'Starting single-admin notification dispatch', [
        'admin_id' => $adminId,
        'title'    => $title,
        'body'     => $body,
        'url'      => $url,
    ]);

    $accessToken = getAccessToken();
    if (!$accessToken) {
        writeLog('ERROR', 'Aborting — could not obtain access token');
        return false;
    }

    $stmt = $conn->prepare("SELECT id, token FROM admin_fcm_tokens WHERE admin_id = ?");
    $stmt->bind_param("i", $adminId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        writeLog('WARNING', 'No tokens found for admin', ['admin_id' => $adminId]);
        return false;
    }

    $fcmUrl       = "https://fcm.googleapis.com/v1/projects/{$projectId}/messages:send";
    $successCount = 0;
    $failCount    = 0;

    while ($row = $result->fetch_assoc()) {
        $payload = json_encode([
            'message' => [
                'token'        => $row['token'],
                'notification' => ['title' => $title, 'body' => $body],
                'webpush'      => [
                    'notification' => ['title' => $title, 'body' => $body],
                    'fcm_options'  => [
                        'link' => 'http://localhost/Digital_Tourism_Platform/' . ltrim($url, '/'),
                    ],
                ],
            ],
        ]);

        $ch = curl_init($fcmUrl);
        curl_setopt_array($ch, [
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => $payload,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER     => [
                'Authorization: Bearer ' . $accessToken,
                'Content-Type: application/json',
            ],
        ]);

        $rawResponse  = curl_exec($ch);
        $httpCode     = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError    = curl_error($ch);
        curl_close($ch);

        $response     = json_decode($rawResponse, true);
        $tokenSnippet = substr($row['token'], 0, 20) . '...';

        if ($httpCode === 200) {
            $successCount++;
            writeLog('SUCCESS', 'Notification delivered to admin', [
                'admin_id'    => $adminId,
                'token_id'    => $row['id'],
                'token'       => $tokenSnippet,
                'http_code'   => $httpCode,
                'fcm_message' => $response['name'] ?? 'N/A',
            ]);
        } else {
            $failCount++;
            $errorCode    = $response['error']['details'][0]['errorCode'] ?? 'UNKNOWN';
            $errorMessage = $response['error']['message'] ?? 'Unknown error';

            writeLog('ERROR', 'Notification failed for admin', [
                'admin_id'      => $adminId,
                'token_id'      => $row['id'],
                'token'         => $tokenSnippet,
                'http_code'     => $httpCode,
                'error_code'    => $errorCode,
                'error_message' => $errorMessage,
                'curl_error'    => $curlError ?: null,
                'full_response' => $response,
            ]);

            if (in_array($errorCode, ['UNREGISTERED', 'INVALID_ARGUMENT'])) {
                $stmt2 = $conn->prepare("DELETE FROM admin_fcm_tokens WHERE id = ?");
                $stmt2->bind_param("i", $row['id']);
                $stmt2->execute();
                writeLog('INFO', 'Removed invalid token', [
                    'admin_id' => $adminId,
                    'token_id' => $row['id'],
                ]);
            }
        }
    }

    writeLog('INFO', 'Single-admin dispatch complete', [
        'admin_id' => $adminId,
        'total'    => $result->num_rows,
        'success'  => $successCount,
        'failed'   => $failCount,
    ]);

    return $successCount > 0;
}
