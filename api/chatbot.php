<?php
include '../config/db.php';
include 'dbdata-chatbot.php';

header("Content-Type: text/plain");

$message = trim($_POST['message'] ?? '');

if (!$message) {
  echo "Ask something...";
  exit;
}

/**
 * ============================================================================
 *  KEYWORD MATCHING - coverage-scored instead of naive substring matching
 * ============================================================================
 *  OLD BEHAVIOR: the first row whose keyword appeared ANYWHERE in the
 *  message (via strpos) won - instantly, regardless of what else the
 *  message said. "hotel near lakeside" contains "hotel", so it always won
 *  the generic hotel FAQ, even though the real question was about location.
 *
 *  NEW BEHAVIOR: every row is scored by how much of the user's message its
 *  keyword phrase actually accounts for ("coverage"). A canned DB answer
 *  is only used when a keyword explains MOST of what the user typed - a
 *  short, generic message like "hotel" or "refund policy" still matches
 *  instantly, but a longer, more specific message like "hotel near
 *  lakeside" or "is there a hotel package for a family of 5" falls through
 *  to Gemini, which has real tour/flight/bus context and is instructed not
 *  to invent details it doesn't have.
 * ============================================================================
 */

function normalizeWords(string $text): array
{
  $text = strtolower($text);
  $text = preg_replace('/[^\p{L}\p{N}\s]/u', ' ', $text); // strip punctuation
  $words = preg_split('/\s+/', trim($text));
  return array_values(array_filter($words, fn($w) => $w !== ''));
}

$messageWords = normalizeWords($message);
$messageWordCount = count($messageWords);

// Minimum fraction of the message that a keyword phrase must account for
// before we trust it enough to skip Gemini entirely. Short messages (<=2
// words) are exempt from the ratio check below, since "hotel" alone IS
// the whole intent - there's nothing else it could mean.
const MATCH_COVERAGE_THRESHOLD = 0.6;

$query = mysqli_query($conn, "
    SELECT *
    FROM chatbot_quiries
    WHERE status = 1
    ORDER BY id ASC
");

$bestRow = null;
$bestScore = 0; // combination of coverage ratio + phrase length, see below

while ($row = mysqli_fetch_assoc($query)) {

  $keywords = array_filter(array_map('trim', explode(',', strtolower($row['keyword']))));

  foreach ($keywords as $phrase) {
    if ($phrase === '') continue;

    $phraseWords = normalizeWords($phrase);
    $phraseWordCount = count($phraseWords);
    if ($phraseWordCount === 0) continue;

    // Whole-phrase, word-boundary match (handles multi-word keywords
    // like "group booking" correctly, and avoids matching "hotel"
    // inside an unrelated longer word).
    $pattern = '/\b' . preg_quote($phrase, '/') . '\b/u';
    if (!preg_match($pattern, strtolower($message))) {
      continue;
    }

    $coverage = $messageWordCount > 0 ? $phraseWordCount / $messageWordCount : 0;

    // Score favors both HIGH coverage (keyword explains most of the
    // message) and LONGER, more specific keyword phrases (a 2-word
    // phrase match is more confident than a 1-word one).
    $score = $coverage + ($phraseWordCount * 0.1);

    $qualifies = ($messageWordCount <= 2) || ($coverage >= MATCH_COVERAGE_THRESHOLD);

    if ($qualifies && $score > $bestScore) {
      $bestScore = $score;
      $bestRow = $row;
    }
  }

  // Secondary signal: compare the whole message against question_pattern
  // (previously stored but never actually used). A high similarity here
  // is a strong standalone signal even if the keyword-coverage check
  // above didn't clearly win.
  if (!empty($row['question_pattern'])) {
    similar_text(strtolower($message), strtolower($row['question_pattern']), $percent);
    if ($percent >= 70 && ($percent / 100) > $bestScore) {
      $bestScore = $percent / 100;
      $bestRow = $row;
    }
  }
}

if ($bestRow !== null) {
  echo $bestRow['answer'];
  exit;
}

// ---------------------------------------------------------------------------
// TIER 2: semantic match via sentence embeddings + cosine similarity.
// Catches paraphrases the coverage check can't ("how much does it cost" vs
// stored keyword "price") without needing the exact words. Only reached
// when the free coverage check above found nothing confident, so this
// extra API call only happens on the messages that actually need it.
// ---------------------------------------------------------------------------
require_once __DIR__ . '/embeddings.php';

$env = parse_ini_file(__DIR__ . '/../.env');
$apiKey = $env['geminiAPIKey'] ?? null;

const SEMANTIC_MATCH_THRESHOLD = 0.775; // moderate: balances recall vs false-positive risk

if ($apiKey) {
  $messageEmbedding = getEmbedding($message, $apiKey);

  if ($messageEmbedding !== null) {
    $embeddedRows = mysqli_query($conn, "
            SELECT id, answer, embedding
            FROM chatbot_quiries
            WHERE status = 1 AND embedding IS NOT NULL AND embedding != ''
        ");

    $bestSemanticScore = 0.0;
    $bestSemanticAnswer = null;

    while ($row = mysqli_fetch_assoc($embeddedRows)) {
      $rowVector = json_decode($row['embedding'], true);
      if (!is_array($rowVector)) continue;

      $similarity = cosineSimilarity($messageEmbedding, $rowVector);

      if ($similarity > $bestSemanticScore) {
        $bestSemanticScore = $similarity;
        $bestSemanticAnswer = $row['answer'];
      }
    }

    if ($bestSemanticAnswer !== null && $bestSemanticScore >= SEMANTIC_MATCH_THRESHOLD) {
      echo $bestSemanticAnswer;
      exit;
    }
  }
}

// ---------------------------------------------------------------------------
// TIER 3: no confident canned match at all - fall through to Gemini with
// real site data.
// ---------------------------------------------------------------------------

$url = "https://generativelanguage.googleapis.com/v1/models/gemini-2.5-flash:generateContent?key=$apiKey";

$data = [
  "contents" => [
    [
      "parts" => [
        [
          "text" =>
          "
          IMPORTANT OUTPUT RULES:
          - Never use Markdown formatting.
          - Do not use bullet points with *.
          - Return plain text only.
          - Use simple paragraphs.
          
          You are a professional AI travel assistant for a digital tourism platform.

          Your job is to help users with:
          - Trip packages (Tours and Treks)
          - Bus ticket bookings
          - Flight information
          - Travel planning
          - Booking process
          - General travel questions
          - Blog articles (travel tips, destination guides, etc.)
          - Questions about the company (About, Services, Contact info)
          - Questions about our Privacy Policy or Terms and Conditions

          Rules:
          - Give short, clear, and friendly answers.
          - Use simple language that all travelers can understand.
          - Recommend suitable packages based on user's interests, budget, location, and duration.
          - If the user asks about available tours, bookings, prices, or services, guide them to the relevant section.
          - Do not invent unavailable packages, prices, or policies. You have access to website information only through provided instructions.
          - Never create fake tour names, prices, discounts, availability or booking details. But if you know information about a specific package, flight, or bus from the internet sources even if it's not in our database, you can provide it.
          - If user asks about a specific package and information is unavailable, ask them to check the Tours page or contact support.
          - Keep answers professional and helpful.
          - Ask follow-up questions when needed (example: destination, travel date, number of travelers, budget).
          - For complaints or problems, respond politely and guide the user toward support.
          - If a package, flight or bus exists in database, explain it clearly.
          - If user asks price, use provided price. PP means per Person price.
          - If the user asks about a blog topic, recommend a relevant article from the provided blog list by title, and tell them it's available at blog-details?slug=THE_ARTICLE_SLUG (use the real slug field from the blog data, not a made-up one). Do not describe blog content beyond the excerpt provided - you don't have the full article text.
          - If the user asks about the company (who we are, what we offer, how to contact us), answer using the provided site_content.about, site_content.services, and site_content.contact data.
          - If the user asks about privacy, data handling, or terms and conditions, answer ONLY using the provided site_content.privacy_policy and site_content.terms_conditions text. Do not add legal interpretations, guarantees, or advice beyond what is written there. For anything not explicitly covered in that text, tell the user to contact support directly rather than guessing.
          Use ONLY the provided database information if exist and user ask information from our website.
          Available trip packages, flights, buses, blog articles, and site information:
          $context

          Website:
          Digital Tourism Platform provides:
          - Domestic and international Trip packages (Tours and Treks)
          - Bus ticketing
          - Flight booking assistance
          - Travel services
          - A travel blog with tips and destination guides

          Other information:
          - If user asks about booking, explain the booking process clearly.
          - If user asks about cancellation, refund policies, travel insurance, visa requirements, or travel advisories, provide the relevant information from the website.
          - If user asks about group or number of persons discounts on trips packages, then tell them we provide discounts based on the number of persons: for example: 10% discount for 5+ persons, 20% discount for 10+ amd below 16 persons. If user asking for more than 15 then tell them to contact with our team for special offers or further assistance.

          REMEMBER:
          Your response must contain ZERO * symbols.

          User message:
          " . $message
        ]
      ]
    ]
  ]
];

$ch = curl_init($url);

curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
  "Content-Type: application/json"
]);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

$response = curl_exec($ch);
curl_close($ch);

$result = json_decode($response, true);

if (isset($result['error'])) {
  echo "Error: " . $result['error']['message'];
  exit;
}

$responseText = $result['candidates'][0]['content']['parts'][0]['text'] ?? "";

$responseText = str_replace("*", "", $responseText);

echo $responseText;
