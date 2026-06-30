<?php

function base64url_encode($data)
{
    return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
}

function generateJWT($serviceAccount)
{
    $now = time();

    $header = base64url_encode(json_encode([
        'alg' => 'RS256',
        'typ' => 'JWT'
    ]));

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
    curl_close($ch);

    if (!isset($response['access_token'])) {
        error_log('FCM: OAuth2 error: ' . json_encode($response));
        return null;
    }

    return $response['access_token'];
}

/**
 * Sends a push notification to ALL admins (all their registered devices).
 */
function sendAdminNotification($title, $body, $url = 'admin/dashboard.php')
{
    require_once __DIR__ . '/../config/db.php';

    $serviceAccount = json_decode(
        file_get_contents(__DIR__ . '/../config/firebase-service-account.json'),
        true
    );
    $projectId = $serviceAccount['project_id'];

    $accessToken = getAccessToken();
    if (!$accessToken) {
        error_log('FCM: Could not get access token');
        return false;
    }

    // Get every token from every admin
    $result = $conn->query("SELECT id, admin_id, token FROM admin_fcm_tokens");
    if (!$result || $result->num_rows === 0) {
        error_log('FCM: No admin tokens found in DB');
        return false;
    }

    $fcmUrl       = "https://fcm.googleapis.com/v1/projects/{$projectId}/messages:send";
    $successCount = 0;

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
                        'icon'  => '/Digital_Tourism_Platform/assets/images/logo.png',
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

        $response = json_decode(curl_exec($ch), true);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode === 200) {
            $successCount++;
        } else {
            $errorCode = $response['error']['details'][0]['errorCode'] ?? '';
            if (in_array($errorCode, ['UNREGISTERED', 'INVALID_ARGUMENT'])) {
                // Token expired/invalid on this specific device — remove just that row
                $stmt = $conn->prepare("DELETE FROM admin_fcm_tokens WHERE id = ?");
                $stmt->bind_param("i", $row['id']);
                $stmt->execute();
                error_log("FCM: Removed invalid token for admin_id {$row['admin_id']}");
            } else {
                error_log('FCM Error (' . $httpCode . '): ' . json_encode($response));
            }
        }
    }

    return $successCount > 0;
}

/**
 * Optional: send to ONE specific admin only (e.g. booking assigned to them)
 */
function sendNotificationToAdmin($adminId, $title, $body, $url = 'admin/dashboard.php')
{
    require_once __DIR__ . '/../config/db.php';

    $serviceAccount = json_decode(
        file_get_contents(__DIR__ . '/../config/firebase-service-account.json'),
        true
    );
    $projectId = $serviceAccount['project_id'];

    $accessToken = getAccessToken();
    if (!$accessToken) return false;

    $stmt = $conn->prepare("SELECT id, token FROM admin_fcm_tokens WHERE admin_id = ?");
    $stmt->bind_param("i", $adminId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) return false;

    $fcmUrl       = "https://fcm.googleapis.com/v1/projects/{$projectId}/messages:send";
    $successCount = 0;

    while ($row = $result->fetch_assoc()) {
        $payload = json_encode([
            'message' => [
                'token'        => $row['token'],
                'notification' => ['title' => $title, 'body' => $body],
                'webpush'      => [
                    'notification' => ['title' => $title, 'body' => $body],
                    'fcm_options'  => ['link' => 'http://localhost/Digital_Tourism_Platform/' . ltrim($url, '/')],
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
        $response = json_decode(curl_exec($ch), true);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode === 200) {
            $successCount++;
        } elseif (in_array($response['error']['details'][0]['errorCode'] ?? '', ['UNREGISTERED', 'INVALID_ARGUMENT'])) {
            $stmt2 = $conn->prepare("DELETE FROM admin_fcm_tokens WHERE id = ?");
            $stmt2->bind_param("i", $row['id']);
            $stmt2->execute();
        }
    }

    return $successCount > 0;
}

