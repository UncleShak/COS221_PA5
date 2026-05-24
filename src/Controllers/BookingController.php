<?php

class BookingController {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function processBooking() {
        if (session_status() === PHP_SESSION_NONE) session_start();
        
        $travellerId = $_SESSION['user_id'];
        $packageId = $_GET['package_id'] ?? $_POST['package_id'] ?? null;
        $travelDate = $_GET['travel_date'] ?? $_POST['travel_date'] ?? date('Y-m-d', strtotime('+1 month'));
        $partySize = $_GET['party_size'] ?? $_POST['party_size'] ?? 1;
        $specialRequests = $_GET['special_requests'] ?? $_POST['special_requests'] ?? '';

        if (!$packageId) {
            header("Location: /traveller/packages");
            exit;
        }

        require_once __DIR__ . '/../Models/BookingModel.php';
        $bookingModel = new BookingModel($this->pdo);

        // 1. Let the Model do the fast, secure transaction
        $bookingId = $bookingModel->processGroupBooking($travellerId, $packageId, $travelDate, $partySize, $specialRequests);

        if ($bookingId) {
            // 2. The transaction is safely committed! Now we can take our time and talk to the AI.
            $stmt = $this->pdo->prepare("SELECT title, description FROM packages WHERE package_id = ?");
            $stmt->execute([$packageId]);
            $pkgData = $stmt->fetch();

            // 3. Generate the Intel
            $aiJson = $this->generateAIPrepKit($pkgData['title'], $travelDate, $pkgData['description']);

            // 4. Save the Intel to the database
            $updateStmt = $this->pdo->prepare("UPDATE bookings SET prep_notes = ? WHERE booking_id = ?");
            $updateStmt->execute([$aiJson, $bookingId]);

            // 5. Finally, redirect to dashboard
            header("Location: /traveller/dashboard?success=booking_confirmed");
            exit;
        } else {
            header("Location: /traveller/packages?error=booking_failed");
            exit;
        }
    }

    private function generateAIPrepKit($destination, $travelDate, $itinerarySummary) {
        $apiKey = 'AIzaSyD4PRyu6nIsi6e1RoH-cOiaZ8KioM2hVoM'; 
        $url = 'https://generativelanguage.googleapis.com/v1/models/gemini-1.5-flash:generateContent?key=' . $apiKey;

        // Force JSON format via prompt instructions
        $prompt = "You are a travel concierge. Destination: $destination. Activities: $itinerarySummary. 
                   Provide 3 packing items and 2 etiquette tips.
                   Format your response as a strictly valid JSON object like this: 
                   {\"packing_list\": [\"item1\", \"item2\", \"item3\"], \"etiquette\": [\"tip1\", \"tip2\"]}";

        $payload = json_encode([
            "contents" => [ ["parts" => [["text" => $prompt]]] ]
        ]);

        $options = [
            'http' => [
                'header'  => "Content-type: application/json\r\n",
                'method'  => 'POST',
                'content' => $payload,
                'timeout' => 20, // Give it time to generate
                'ignore_errors' => true
            ],
            'ssl' => ['verify_peer' => false, 'verify_peer_name' => false]
        ];
        
        $context = stream_context_create($options);
        $response = file_get_contents($url, false, $context);

        if ($response === FALSE) {
            error_log("AI_FEATURE: Connection failed completely.");
            return null; // Return null to trigger failsafe in calling method
        }

        $responseData = json_decode($response, true);
        
        // Log errors to your PHP error log (check your server logs if empty)
        if (isset($responseData['error'])) {
            error_log("AI_FEATURE API Error: " . json_encode($responseData['error']));
            return null;
        }

        // Extract the JSON string from the response
        $text = $responseData['candidates'][0]['content']['parts'][0]['text'] ?? '';
        
        // Clean markdown backticks just in case
        $jsonOnly = str_replace(['```json', '```'], '', $text);
        
        return trim($jsonOnly);
    }
}
?>