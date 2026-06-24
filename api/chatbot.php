<?php
include '../config/db.php';
include 'dbdata-chatbot.php';

header("Content-Type: text/plain");

$message = trim($_POST['message'] ?? '');

if (!$message) {
  echo "Ask something...";
  exit;
}

$query = mysqli_query($conn, "SELECT * 
FROM chatbot_quiries 
WHERE status = 1
ORDER BY id ASC");


$messageLower = strtolower($message);


while ($row = mysqli_fetch_assoc($query)) {


  $keywords = explode(",", strtolower($row['keyword']));


  foreach ($keywords as $word) {

    $word = trim($word);


    if (strpos($messageLower, $word) !== false) {

      echo $row['answer'];
      exit;
    }
  }
}

$env = parse_ini_file(__DIR__ . '/../.env');

$apiKey = $env['geminiAPIKey'];

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

          Rules:
          - Give short, clear, and friendly answers.
          - Use simple language that all travelers can understand.
          - Recommend suitable packages based on user's interests, budget, location, and duration.
          - If the user asks about available tours, bookings, prices, or services, guide them to the relevant section.
          - Do not invent unavailable packages, prices, or policies. You have access to website information only through provided instructions.
          Never create fake tour names, prices, discounts, availability or booking details.
          If user asks about a specific package and information is unavailable, ask them to check the Tours page or contact support.
          - Keep answers professional and helpful.
          - Ask follow-up questions when needed (example: destination, travel date, number of travelers, budget).
          - For complaints or problems, respond politely and guide the user toward support.
          - If a package, flight or bus exists in database, explain it clearly.
          - If user asks price, use provided price. PP means per Person price.
          Use ONLY the provided database information if exist and user ask information from our website.
          Available trip packages, flights tickets, buses:
          $context

          Website:
          Digital Tourism Platform provides:
          - Domestic and international Trip packages (Tours and Treks)
          - Bus ticketing
          - Flight booking assistance
          - Travel services

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

// echo $result['candidates'][0]['content']['parts'][0]['text'] ?? "No response";

$responseText = $result['candidates'][0]['content']['parts'][0]['text'] ?? "";

$responseText = str_replace("*", "", $responseText);

echo $responseText;
