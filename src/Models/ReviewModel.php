<?php
class ReviewModel {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function createReview($travellerId, $packageId, $bookingId, $rating, $comment) {
        try {
            $sql = "INSERT INTO packagereviews (traveller_id, package_id, booking_id, rating, comment, created_at) 
                    VALUES (:traveller_id, :package_id, :booking_id, :rating, :comment, NOW())";
            
            $stmt = $this->pdo->prepare($sql);
            $success = $stmt->execute([
                ':traveller_id' => $travellerId,
                ':package_id'   => $packageId,
                ':booking_id'   => $bookingId,   
                ':rating'       => (int)$rating, 
                ':comment'      => htmlspecialchars(strip_tags($comment)) 
            ]);

            return $success;

        } catch (PDOException $e) {
            echo "<br><div style='padding: 1rem; background: #ffebee; color: #c62828; border: 2px solid #c62828; border-radius: 8px; font-family: monospace;'>";
            echo "<strong>MARIADB REVIEW ERROR:</strong><br>" . $e->getMessage();
            echo "</div><br>";
            return false;
        }
    }
}
?>