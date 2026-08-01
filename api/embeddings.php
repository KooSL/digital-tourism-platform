<?php

/**
 * ============================================================================
 *  SENTENCE EMBEDDINGS + COSINE SIMILARITY - shared helper
 * ============================================================================
 *  Used by both the one-time backfill script (admin/scripts/
 *  backfill_chatbot_embeddings.php) and the live chatbot matcher
 *  (api/chatbot.php) - one implementation, so the vectors compared at
 *  request time are always produced the exact same way they were stored.
 * ============================================================================
 */

/**
 * Get a sentence embedding vector for a piece of text via Gemini's
 * embedding model. Returns an array of floats (3072-dim, gemini-embedding-001),
 * or null on failure.
 *
 * NOTE: text-embedding-004 (the model this originally used) was deprecated
 * by Google on Jan 14, 2026 and no longer works. gemini-embedding-001 is
 * its replacement - different output size (3072 vs 768 dims), but
 * cosineSimilarity() below works with any matching vector length so no
 * other code needs to change.
 */
function getEmbedding(string $text, string $apiKey): ?array
{
    $text = trim($text);
    if ($text === '') {
        return null;
    }

    $url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-embedding-001:embedContent?key=$apiKey";

    $payload = [
        "content" => [
            "parts" => [
                ["text" => $text]
            ]
        ]
    ];

    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_HTTPHEADER => ["Content-Type: application/json"],
        CURLOPT_POSTFIELDS => json_encode($payload),
        CURLOPT_TIMEOUT => 10,
    ]);

    $response = curl_exec($ch);
    $curlError = curl_error($ch);
    curl_close($ch);

    if ($curlError || !$response) {
        error_log("Embedding API call failed: $curlError");
        return null;
    }

    $result = json_decode($response, true);

    if (isset($result['error'])) {
        error_log("Embedding API error: " . $result['error']['message']);
        return null;
    }

    return $result['embedding']['values'] ?? null;
}

/**
 * Cosine similarity between two equal-length vectors, in the range -1..1
 * (in practice, embeddings of related text land somewhere around 0.5-1.0).
 */
function cosineSimilarity(array $a, array $b): float
{
    if (count($a) !== count($b) || count($a) === 0) {
        return 0.0;
    }

    $dot = 0.0;
    $normA = 0.0;
    $normB = 0.0;

    foreach ($a as $i => $val) {
        $bVal = $b[$i] ?? 0.0;
        $dot += $val * $bVal;
        $normA += $val * $val;
        $normB += $bVal * $bVal;
    }

    if ($normA <= 0 || $normB <= 0) {
        return 0.0;
    }

    return $dot / (sqrt($normA) * sqrt($normB));
}

/**
 * Combines a chatbot_quiries row's keyword + question_pattern into one
 * representative string to embed - gives the vector more context than
 * the keyword phrase alone would.
 */
function buildEmbeddingSourceText(array $row): string
{
    $parts = array_filter([
        $row['question_pattern'] ?? '',
        str_replace(',', ' ', $row['keyword'] ?? ''),
    ]);
    return trim(implode('. ', $parts));
}
