<?php
class HomeController {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function index() {
        $sqlTop = "SELECT p.package_id as id, p.title as name, 'Global' as destination, 
                          p.base_price as price_per_person, 
                          p.cover_image_url as image_filename, COALESCE(ROUND(AVG(r.rating), 1), 0) AS avg_rating
                   FROM packages p
                   LEFT JOIN packagereviews r ON r.package_id = p.package_id
                   GROUP BY p.package_id
                   ORDER BY avg_rating DESC
                   LIMIT 5";
        $stmtTop = $this->pdo->query($sqlTop);
        $topRated = $stmtTop->fetchAll(PDO::FETCH_ASSOC);

        $sqlPop = "SELECT p.package_id as id, p.title as name, 'Global' as destination, 
                          p.base_price as price_per_person, 
                          p.cover_image_url as image_filename, COUNT(b.booking_id) AS booking_count
                   FROM packages p
                   LEFT JOIN bookings b ON b.package_id = p.package_id
                   GROUP BY p.package_id
                   ORDER BY booking_count DESC
                   LIMIT 5";
        $stmtPop = $this->pdo->query($sqlPop);
        $mostPopular = $stmtPop->fetchAll(PDO::FETCH_ASSOC);

        $title = 'Tripistry · Your Next Adventure Awaits';
        ob_start();
        require __DIR__ . '/../Views/Landing.php';
        $content = ob_get_clean();
        require __DIR__ . '/../Views/layout.php';
    }
}
?>