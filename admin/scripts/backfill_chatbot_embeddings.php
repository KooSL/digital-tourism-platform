<?php

/**
 * ============================================================================
 *  ONE-TIME BACKFILL: embeddings for existing chatbot_quiries rows
 * ============================================================================
 *  Run this ONCE from the command line after applying
 *  chatbot_embedding_migration.sql:
 *
 *      php admin/scripts/backfill_chatbot_embeddings.php
 *
 *  Safe to re-run later (e.g. after editing a row's keyword/question_pattern
 *  in the admin panel) - it only re-embeds rows where embedding_updated_at
 *  is NULL or older than the row's own updated timestamp isn't tracked, so
 *  for now it re-embeds anything with a NULL embedding. Pass --force to
 *  re-embed everything regardless.
 *
 *  NOT meant to be run from a browser - no session/auth context, and it can
 *  take a while / make many API calls depending on row count.
 * ============================================================================
 */

if (php_sapi_name() !== 'cli') {
    http_response_code(403);
    die("This script must be run from the command line, not a browser.");
}

require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../api/embeddings.php';

$env = parse_ini_file(__DIR__ . '/../../.env');
$apiKey = $env['geminiAPIKey'] ?? null;

if (!$apiKey) {
    die("ERROR: geminiAPIKey not found in .env\n");
}

$force = in_array('--force', $argv, true);

$where = $force ? "1=1" : "(embedding IS NULL OR embedding = '')";
$result = mysqli_query($conn, "SELECT * FROM chatbot_quiries WHERE $where ORDER BY id ASC");

$total = mysqli_num_rows($result);

if ($total === 0) {
    echo "Nothing to do - all rows already have embeddings. Use --force to re-embed everything.\n";
    exit;
}

echo "Backfilling embeddings for $total row(s)...\n";

$success = 0;
$failed = 0;

while ($row = mysqli_fetch_assoc($result)) {
    $sourceText = buildEmbeddingSourceText($row);

    if ($sourceText === '') {
        echo "  [skip] id={$row['id']} - no keyword/question_pattern text to embed\n";
        continue;
    }

    $vector = getEmbedding($sourceText, $apiKey);

    if ($vector === null) {
        echo "  [FAIL] id={$row['id']} - embedding API call failed, skipping\n";
        $failed++;
        // Small pause before continuing so a transient API issue doesn't
        // just immediately fail every subsequent row too.
        sleep(1);
        continue;
    }

    $vectorJson = json_encode($vector);

    $stmt = mysqli_prepare($conn, "
        UPDATE chatbot_quiries
        SET embedding = ?, embedding_updated_at = NOW()
        WHERE id = ?
    ");
    mysqli_stmt_bind_param($stmt, "si", $vectorJson, $row['id']);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    echo "  [ok]   id={$row['id']} - \"" . mb_substr($sourceText, 0, 60) . "\"\n";
    $success++;

    // Gentle rate-limiting - avoids hammering the API in a tight loop if
    // you have a large table. If you hit a 429 (rate limit) error, increase
    // this value.
    usleep(300000); // 300ms
}

echo "\nDone. $success embedded, $failed failed.\n";

if ($failed > 0) {
    echo "Re-run the script (without --force) to retry only the rows that are still missing an embedding.\n";
}
