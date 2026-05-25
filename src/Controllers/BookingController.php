<?php

class BookingController {
    private $pdo;
    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function processBooking() {
        ob_start();

        if (session_status() === PHP_SESSION_NONE) session_start();

        $travellerId     = $_SESSION['user_id'];
        $packageId       = $_GET['package_id']      ?? $_POST['package_id']      ?? null;
        $travelDate      = $_GET['travel_date']      ?? $_POST['travel_date']     ?? date('Y-m-d', strtotime('+1 month'));
        $partySize       = $_GET['party_size']       ?? $_POST['party_size']       ?? 1;
        $specialRequests = $_GET['special_requests'] ?? $_POST['special_requests'] ?? '';

        if (!$packageId) {
            ob_end_clean();
            header("Location: /traveller/packages");
            exit;
        }

        require_once __DIR__ . '/../Models/BookingModel.php';
        $bookingModel = new BookingModel($this->pdo);

        $stmt = $this->pdo->prepare("SELECT title, description, base_price FROM packages WHERE package_id = ?");
        $stmt->execute([$packageId]);
        $pkgData = $stmt->fetch();

        if (!$pkgData) {
            ob_end_clean();
            header("Location: /traveller/packages?error=package_not_found");
            exit;
        }

        $totalPrice = (float) $pkgData['base_price'] * (int) $partySize;

        $bookingId = $bookingModel->processGroupBooking(
            $travellerId, $packageId, $travelDate, $partySize, $specialRequests, $totalPrice
        );

        if (!$bookingId) {
            ob_end_clean();
            header("Location: /traveller/packages?error=booking_failed");
            exit;
        }

        $aiJson = $this->generateTripIntel(
            $pkgData['title'],
            $travelDate,
            $pkgData['description']
        );

        $debugOutput = ob_get_contents();

        if (!empty(trim($debugOutput))) {
            ob_end_flush();
            return;
        }
        ob_end_clean();

        $updateStmt = $this->pdo->prepare("UPDATE bookings SET prep_notes = ? WHERE booking_id = ?");
        $updateStmt->execute([$aiJson, $bookingId]);

        header("Location: /traveller/dashboard?success=booking_confirmed");
        exit;
    }

    private function generateTripIntel(string $destination, string $travelDate, string $itinerarySummary): ?string {

        ini_set('log_errors', '1');
        ini_set('error_log', __DIR__ . '/../../logs/tripistry_debug.log');

        $apiKey = getenv('GEMINI_API_KEY');
        if ($apiKey === false || trim($apiKey) === '') {
            $apiKey = defined('GEMINI_API_KEY') ? GEMINI_API_KEY : '';
        }

        if (trim($apiKey) === '') {
            error_log('[TRIPISTRY][AI] GEMINI_API_KEY is not configured. Using local fallback itinerary.');
            return $this->buildFallbackTripIntel($destination, $travelDate, $itinerarySummary);
        }

        $url    = 'https://generativelanguage.googleapis.com/v1/models/gemini-2.5-flash:generateContent?key=' . $apiKey;

        $prompt =
            "You are a professional travel concierge AI. A traveller is preparing for a trip.\n\n" .
            "Trip destination : {$destination}\n" .
            "Departure date   : {$travelDate}\n" .
            "Itinerary summary: {$itinerarySummary}\n\n" .
            "Your task: Provide a concise day-by-day itinerary plus exactly 3 essential packing items and exactly 2 local cultural etiquette tips for this specific destination.\n\n" .
            "CRITICAL INSTRUCTIONS - YOU MUST FOLLOW THESE EXACTLY:\n" .
            "1. Respond with ONLY a single valid JSON object. No prose, no markdown, no code fences.\n" .
            '2. The JSON must match this exact structure with no extra keys: {"itinerary": [{"day": 1, "title": "...", "description": "..."}], "packing_list": ["item1", "item2", "item3"], "etiquette": ["tip1", "tip2"]}' . "\n" .
            "3. All values must be plain strings. The itinerary value must be an array of objects with day, title, and description.\n" .
            "4. Do NOT wrap your response in backtick code fences or any other formatting.";

        $payload = json_encode([
            'contents' => [
                [
                    'parts' => [
                        ['text' => $prompt]
                    ]
                ]
            ]
        ]);

        if ($payload === false) {
            error_log('[TRIPISTRY][AI] FATAL: json_encode() failed building payload - ' . json_last_error_msg());
            return null;
        }

        $context = stream_context_create([
            'http' => [
                'method'        => 'POST',
                'header'        => "Content-Type: application/json\r\nAccept: application/json\r\n",
                'content'       => $payload,
                'timeout'       => 30,
                'ignore_errors' => true,
            ],
        ]);

        $responseBody = file_get_contents($url, false, $context);
        $httpStatus = 0;
        if (!empty($http_response_header)) {
            preg_match('/HTTP\/\d\.\d\s+(\d{3})/', $http_response_header[0], $matches);
            $httpStatus = isset($matches[1]) ? (int)$matches[1] : 0;
        }

        if ($responseBody === false || $httpStatus !== 200) {
            error_log("[TRIPISTRY][AI] API call failed. HTTP Status: {$httpStatus}. Body: " . ($responseBody ?: '(empty)'));
            return $this->buildFallbackTripIntel($destination, $travelDate, $itinerarySummary);
        }

        $responseData = json_decode($responseBody, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            error_log('[TRIPISTRY][AI] Failed to json_decode outer API response: ' . json_last_error_msg());
            error_log('[TRIPISTRY][AI] Raw body was: ' . $responseBody);
            return $this->buildFallbackTripIntel($destination, $travelDate, $itinerarySummary);
        }

        if (isset($responseData['error'])) {
            $errCode = $responseData['error']['code']    ?? 'N/A';
            $errMsg  = $responseData['error']['message'] ?? 'Unknown API error';
            error_log("[TRIPISTRY][AI] API returned error object - Code: {$errCode}, Message: {$errMsg}");
            return $this->buildFallbackTripIntel($destination, $travelDate, $itinerarySummary);
        }

        $parts   = $responseData['candidates'][0]['content']['parts'] ?? [];
        $rawText = null;

        foreach (array_reverse($parts) as $part) {
            if (empty($part['thought']) && isset($part['text'])) {
                $rawText = $part['text'];
                break;
            }
        }

        if ($rawText === null) {
            error_log('[TRIPISTRY][AI] Could not find a non-thought text part in response.');
            error_log('[TRIPISTRY][AI] Full response was: ' . $responseBody);
            return $this->buildFallbackTripIntel($destination, $travelDate, $itinerarySummary);
        }

        $cleaned = trim($rawText);
        $cleaned = preg_replace('/^```(?:json)?\s*/i', '', $cleaned);
        $cleaned = preg_replace('/\s*```$/',            '', $cleaned);
        $cleaned = trim($cleaned);

        $decoded = json_decode($cleaned, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            error_log('[TRIPISTRY][AI] Model output was not valid JSON after cleaning: ' . json_last_error_msg());
            error_log('[TRIPISTRY][AI] Cleaned text was: ' . $cleaned);
            return $this->buildFallbackTripIntel($destination, $travelDate, $itinerarySummary);
        }

        if (!isset($decoded['itinerary']) || !isset($decoded['packing_list']) || !isset($decoded['etiquette'])) {
            error_log('[TRIPISTRY][AI] Model JSON missing expected keys (itinerary / packing_list / etiquette).');
            error_log('[TRIPISTRY][AI] Decoded structure: ' . print_r($decoded, true));
            return $this->buildFallbackTripIntel($destination, $travelDate, $itinerarySummary);
        }

        return $cleaned;
    }

    private function buildFallbackTripIntel(string $destination, string $travelDate, string $itinerarySummary): string {
        $safeDestination = trim($destination) !== '' ? $destination : 'your destination';
        $summaryText      = strip_tags($itinerarySummary);
        $summarySnippet   = trim(function_exists('mb_substr') ? mb_substr($summaryText, 0, 120) : substr($summaryText, 0, 120));

        $isCapeTownTrip = stripos($safeDestination, 'cape') !== false || stripos($summarySnippet, 'cape') !== false;

        if ($isCapeTownTrip) {
            $itinerary = [
                [
                    'day' => 1,
                    'title' => 'Arrival and V&A Waterfront Orientation',
                    'description' => 'Arrive in Cape Town, check in, and enjoy a relaxed evening at the V&A Waterfront.'
                ],
                [
                    'day' => 2,
                    'title' => 'Table Mountain and City Highlights',
                    'description' => 'Take the cableway up Table Mountain, then explore the city centre, Bo-Kaap, and nearby viewpoints.'
                ],
                [
                    'day' => 3,
                    'title' => 'Coastal Drive and Local Culture',
                    'description' => 'Spend the day along the Atlantic Seaboard or Cape Peninsula and finish with a local dining experience.'
                ]
            ];
        } else {
            $itinerary = [
                [
                    'day' => 1,
                    'title' => 'Arrival and Check-in',
                    'description' => "Arrive at {$safeDestination}, check in, and settle in. Use the evening to rest and review the trip briefing."
                ],
                [
                    'day' => 2,
                    'title' => 'Core Destination Experience',
                    'description' => "Explore the main highlights described in the package and keep a flexible pace for local experiences."
                ],
                [
                    'day' => 3,
                    'title' => 'Local Culture and Free Time',
                    'description' => "Visit a market, attraction, or viewpoint related to {$safeDestination}, then leave space for personal discovery."
                ]
            ];
        }

        $fallback = [
            'itinerary' => $itinerary,
            'packing_list' => [
                'Valid travel documents',
                'Weather-appropriate clothing',
                'Portable charger and day bag'
            ],
            'etiquette' => [
                'Greet locals politely and follow venue rules.',
                'Dress respectfully when visiting cultural or religious sites.'
            ],
            'fallback_reason' => 'Generated locally because the Gemini request failed or returned an unusable response.',
            'travel_date' => $travelDate,
            'destination' => $safeDestination,
            'summary' => $summarySnippet
        ];

        return json_encode($fallback, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }
}
?>