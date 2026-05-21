-- Active: 1779372272824@@127.0.0.1@3306
<?php

class BookingModel {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function createBooking($travellerId, $packageId, $travelDate, $partySize, $specialRequests) {
        try {
            $paymentRef = 'TRP-' . strtoupper(substr(uniqid(), -6));
            $sql = "INSERT INTO bookings (
                        traveller_id, package_id, travel_date, num_travellers, 
                        special_requests, status, total_price, currency, payment_reference
                    ) 
                    VALUES (
                        :traveller_id, :package_id, :travel_date, :num_travellers, 
                        :special_requests, 'confirmed', 19700.00, 'ZAR', :payment_reference
                    )";
            
            $stmt = $this->pdo->prepare($sql);
            
            $success = $stmt->execute([
                ':traveller_id'      => $travellerId,
                ':package_id'        => $packageId,
                ':travel_date'       => $travelDate,
                ':num_travellers'    => $partySize, 
                ':special_requests'  => $specialRequests,
                ':payment_reference' => $paymentRef
            ]);

            return $success;

        } catch (PDOException $e) {
            echo "<br><div style='padding: 1rem; background: #ffebee; color: #c62828; border: 2px solid #c62828;'>";
            echo "<strong>MARIADB ERROR:</strong><br>" . $e->getMessage();
            echo "</div><br>";
            return false;
        }
    }
}
?>