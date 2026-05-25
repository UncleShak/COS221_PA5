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

        $aiJson = $this->generateAIPrepKit(
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

    private function generateAIPrepKit(string $destination, string $travelDate, string $itinerarySummary): ?string {

        ini_set('log_errors', '1');
        ini_set('error_log', __DIR__ . '/../../logs/tripistry_debug.log');

        $apiKey = defined('GEMINI_API_KEY') ? GEMINI_API_KEY : 'AIzaSyD4PRyu6nIsi6e1RoH-cOiaZ8KioM2hVoM';
        $url    = 'https://generativelanguage.googleapis.com/v1/models/gemini-2.5-flash:generateContent?key=' . $apiKey;

        $prompt =
            "You are a professional travel concierge AI. A traveller is preparing for a trip.\n\n" .
            "Trip destination : {$destination}\n" .
            "Departure date   : {$travelDate}\n" .
            "Itinerary summary: {$itinerarySummary}\n\n" .
            "Your task: Provide exactly 3 essential packing items and exactly 2 local cultural etiquette tips for this specific destination.\n\n" .
            "CRITICAL INSTRUCTIONS - YOU MUST FOLLOW THESE EXACTLY:\n" .
            "1. Respond with ONLY a single valid JSON object. No prose, no markdown, no code fences.\n" .
            '2. The JSON must match this exact structure with no extra keys: {"packing_list": ["item1", "item2", "item3"], "etiquette": ["tip1", "tip2"]}' . "\n" .
            "3. All values must be plain strings. No nested objects or arrays within the values.\n" .
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

            $prettyPayload = json_encode(json_decode($payload),     JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
            $prettyBody    = json_encode(json_decode($responseBody), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
            $displayBody   = ($prettyBody && $prettyBody !== 'null') ? $prettyBody : $responseBody;

            echo "<div style='"
                . "font-family: monospace; font-size: 13px; line-height: 1.6;"
                . "padding: 1.5rem; margin: 1rem;"
                . "background: #0d0d0d; color: #f8f8f2;"
                . "border: 2px solid #ff5555;"
                . "border-radius: 6px; overflow-x: auto;"
                . "position: relative; z-index: 9999;'>";

            echo "<h3 style='color:#ff5555; margin-top:0;'>TRIPISTRY - GEMINI API FAILURE - NUCLEAR DEBUG</h3>";

            echo "<p style='color:#6272a4; margin:0 0 0.5rem;'>REQUEST URL</p>";
            echo "<pre style='color:#50fa7b; background:#1a1a2e; padding:0.75rem; border-radius:4px; margin:0 0 1rem;'>"
                . htmlspecialchars($url) . "</pre>";

            echo "<p style='color:#6272a4; margin:0 0 0.5rem;'>REQUEST PAYLOAD SENT</p>";
            echo "<pre style='color:#f1fa8c; background:#1a1a2e; padding:0.75rem; border-radius:4px; margin:0 0 1rem;'>"
                . htmlspecialchars($prettyPayload) . "</pre>";

            echo "<p style='color:#6272a4; margin:0 0 0.5rem;'>RESPONSE HEADERS</p>";
            $headerText = !empty($http_response_header)
                ? implode("\n", $http_response_header)
                : '(no headers - connection failed entirely)';
            echo "<pre style='color:#8be9fd; background:#1a1a2e; padding:0.75rem; border-radius:4px; margin:0 0 1rem;'>"
                . htmlspecialchars($headerText) . "</pre>";

            echo "<p style='color:#6272a4; margin:0 0 0.5rem;'>RESPONSE BODY (HTTP " . $httpStatus . ")</p>";
            echo "<pre style='color:#ff79c6; background:#1a1a2e; padding:0.75rem; border-radius:4px; margin:0;'>"
                . htmlspecialchars($displayBody ?: '(empty - no body received)') . "</pre>";

            echo "</div>";

            return null;
        }

        $responseData = json_decode($responseBody, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            error_log('[TRIPISTRY][AI] Failed to json_decode outer API response: ' . json_last_error_msg());
            error_log('[TRIPISTRY][AI] Raw body was: ' . $responseBody);
            return null;
        }

        if (isset($responseData['error'])) {
            $errCode = $responseData['error']['code']    ?? 'N/A';
            $errMsg  = $responseData['error']['message'] ?? 'Unknown API error';
            error_log("[TRIPISTRY][AI] API returned error object - Code: {$errCode}, Message: {$errMsg}");
            return null;
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
            return null;
        }

        $cleaned = trim($rawText);
        $cleaned = preg_replace('/^```(?:json)?\s*/i', '', $cleaned);
        $cleaned = preg_replace('/\s*```$/',            '', $cleaned);
        $cleaned = trim($cleaned);

        $decoded = json_decode($cleaned, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            error_log('[TRIPISTRY][AI] Model output was not valid JSON after cleaning: ' . json_last_error_msg());
            error_log('[TRIPISTRY][AI] Cleaned text was: ' . $cleaned);
            return null;
        }

        if (!isset($decoded['packing_list']) || !isset($decoded['etiquette'])) {
            error_log('[TRIPISTRY][AI] Model JSON missing expected keys (packing_list / etiquette).');
            error_log('[TRIPISTRY][AI] Decoded structure: ' . print_r($decoded, true));
            return null;
        }

        return $cleaned;
    }
}
?>