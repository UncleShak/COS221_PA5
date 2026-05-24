<?php

class AgencyController{
    private $conn;

    public function __construct($db){
        $this->conn = $db;
    }

    public function dashboard() {
        try {
            $agencyId = $_SESSION['user_id'];
            $flash = null; 

            $statsQuery = "
                SELECT 
                    COUNT(p.package_id) as total_packages,
                    (SELECT COUNT(*) FROM agencyreviews WHERE agency_id = :agency_id) as review_count,
                    (SELECT AVG(rating) FROM agencyreviews WHERE agency_id = :agency_id) as avg_rating
                FROM packages p 
                WHERE p.agency_id = :agency_id
            ";
            $stmt = $this->conn->prepare($statsQuery);
            $stmt->execute([':agency_id' => $agencyId]);
            $stats = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$stats) {
                $stats = ['total_packages' => 0, 'review_count' => 0, 'avg_rating' => null];
            }

            $pkgQuery = "
                SELECT 
                    p.*,
                    (SELECT COUNT(*) FROM bookings b WHERE b.package_id = p.package_id AND b.status != 'cancelled') as booking_count,
                    (SELECT AVG(rating) FROM packagereviews pr WHERE pr.package_id = p.package_id) as average_rating,
                    EXISTS(SELECT 1 FROM grouptrips gt WHERE gt.package_id = p.package_id) as is_group_trip
                FROM packages p
                WHERE p.agency_id = :agency_id
                ORDER BY p.created_at DESC
            ";
            $stmt = $this->conn->prepare($pkgQuery);
            $stmt->execute([':agency_id' => $agencyId]);
            $packages = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $title = 'Agency Command Center · Tripistry';
            ob_start();
            require_once __DIR__ . '/../Views/agency/dashboard.php';
            $content = ob_get_clean();
            require_once __DIR__ . '/../Views/layout.php';

        } catch (PDOException $e) {
            error_log("Dashboard Fetch Error: " . $e->getMessage());
            echo "A database error occurred while loading the command center.";
        }
    }

    public function archivePackage() {
        $packageId = $_GET['id'] ?? $_POST['id'] ?? $_GET['package_id'] ?? $_POST['package_id'] ?? null;
        if (!$packageId) {
            header("Location: /agency/dashboard?error=missing_id");
            exit;
        }
        try {
            $query = "UPDATE packages SET status = 'archived' WHERE package_id = :id AND agency_id = :agency_id";
            $stmt = $this->conn->prepare($query);
            $stmt->execute([':id' => $packageId, ':agency_id' => $_SESSION['user_id']]);
            header("Location: /agency/dashboard?success=package_archived");
            exit;
        } catch (PDOException $e) {
            header("Location: /agency/dashboard?error=archive_failed");
            exit;
        }
    }

    public function activatePackage() {
        $packageId = $_GET['id'] ?? $_POST['id'] ?? $_GET['package_id'] ?? $_POST['package_id'] ?? null;
        if (!$packageId) {
            header("Location: /agency/dashboard?error=missing_id");
            exit;
        }
        try {
            $query = "UPDATE packages SET status = 'active' WHERE package_id = :id AND agency_id = :agency_id";
            $stmt = $this->conn->prepare($query);
            $stmt->execute([':id' => $packageId, ':agency_id' => $_SESSION['user_id']]);
            header("Location: /agency/dashboard?success=package_activated");
            exit;
        } catch (PDOException $e) {
            header("Location: /agency/dashboard?error=activation_failed");
            exit;
        }
    }

    public function packageForm() {
        try {
            $packageId = $_GET['id'] ?? null;
            $formMode = $packageId ? 'edit' : 'create';
            $package = null;
            $groupTrip = false;

            $selectedDestinations = [];
            $selectedFlights = [];
            $selectedAccommodations = [];
            $selectedAttractions = [];
            $selectedRestaurants = [];

            if ($packageId) {
                $pkgQuery = "SELECT * FROM packages WHERE package_id = :id AND agency_id = :agency_id";
                $stmt = $this->conn->prepare($pkgQuery);
                $stmt->execute([':id' => $packageId, ':agency_id' => $_SESSION['user_id']]);
                $package = $stmt->fetch(PDO::FETCH_ASSOC);

                if (!$package) {
                    header("Location: /agency/dashboard?error=package_not_found");
                    exit;
                }

                $stmt = $this->conn->prepare("SELECT destination_id FROM packagedestinations WHERE package_id = :id");
                $stmt->execute([':id' => $packageId]);
                $selectedDestinations = $stmt->fetchAll(PDO::FETCH_COLUMN);

                $stmt = $this->conn->prepare("SELECT flight_id FROM packageflights WHERE package_id = :id");
                $stmt->execute([':id' => $packageId]);
                $selectedFlights = $stmt->fetchAll(PDO::FETCH_COLUMN);

                $stmt = $this->conn->prepare("SELECT accommodation_id FROM packageaccommodations WHERE package_id = :id");
                $stmt->execute([':id' => $packageId]);
                $selectedAccommodations = $stmt->fetchAll(PDO::FETCH_COLUMN);

                $stmt = $this->conn->prepare("SELECT attraction_id FROM packageattractions WHERE package_id = :id");
                $stmt->execute([':id' => $packageId]);
                $selectedAttractions = $stmt->fetchAll(PDO::FETCH_COLUMN);

                $stmt = $this->conn->prepare("SELECT restaurant_id FROM packagerestaurants WHERE package_id = :id");
                $stmt->execute([':id' => $packageId]);
                $selectedRestaurants = $stmt->fetchAll(PDO::FETCH_COLUMN);
            }

            // THE FIX: Raw Queries to bypass Prince's broken schema assumptions
            $destStmt = $this->conn->query("SELECT * FROM destinations");
            $allDestinations = $destStmt->fetchAll(PDO::FETCH_ASSOC);

            $flightStmt = $this->conn->query("SELECT * FROM flights");
            $allFlights = $flightStmt->fetchAll(PDO::FETCH_ASSOC);

            $accStmt = $this->conn->query("SELECT * FROM accommodations");
            $allAccommodations = $accStmt->fetchAll(PDO::FETCH_ASSOC);

            $attrStmt = $this->conn->query("SELECT * FROM attractions");
            $allAttractions = $attrStmt->fetchAll(PDO::FETCH_ASSOC);

            $restStmt = $this->conn->query("SELECT * FROM restaurants");
            $allRestaurants = $restStmt->fetchAll(PDO::FETCH_ASSOC);

            $title = ($formMode === 'edit' ? 'Edit' : 'New') . ' Package · Tripistry';
            ob_start();
            require_once __DIR__ . '/../Views/agency/edit_package.php'; 
            $content = ob_get_clean();
            require_once __DIR__ . '/../Views/layout.php';

        } catch (PDOException $e) {
            error_log("Edit Form Fetch Error: " . $e->getMessage());
            echo "A database error occurred while loading the form.";
        }
    }

    public function savePackage() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $this->conn->beginTransaction();

                $packageId = !empty($_POST['package_id']) ? (int)$_POST['package_id'] : null;
                $agencyId = $_SESSION['user_id'];
                
                $title = htmlspecialchars($_POST['title'] ?? '');
                $description = htmlspecialchars($_POST['description'] ?? '');
                $basePrice = (float)($_POST['base_price'] ?? 0);
                $duration = (int)($_POST['duration_days'] ?? 0);
                $capacity = !empty($_POST['max_capacity']) ? (int)$_POST['max_capacity'] : null;
                $coverImage = !empty($_POST['cover_image_url']) ? filter_var($_POST['cover_image_url'], FILTER_SANITIZE_URL) : null;
                $status = $_POST['status'] ?? 'active';

                if ($packageId) {
                    $updateQuery = "UPDATE packages 
                                    SET title = :title, description = :description, base_price = :base_price, 
                                        duration_days = :duration, max_capacity = :capacity, cover_image_url = :cover, status = :status
                                    WHERE package_id = :id AND agency_id = :agency_id";
                    $stmt = $this->conn->prepare($updateQuery);
                    $stmt->execute([
                        ':title' => $title, ':description' => $description, ':base_price' => $basePrice,
                        ':duration' => $duration, ':capacity' => $capacity, ':cover' => $coverImage,
                        ':status' => $status, ':id' => $packageId, ':agency_id' => $agencyId
                    ]);
                } else {
                    $insertQuery = "INSERT INTO packages (agency_id, title, description, base_price, duration_days, max_capacity, cover_image_url, status) 
                                    VALUES (:agency_id, :title, :description, :base_price, :duration, :capacity, :cover, :status)";
                    $stmt = $this->conn->prepare($insertQuery);
                    $stmt->execute([
                        ':agency_id' => $agencyId, ':title' => $title, ':description' => $description,
                        ':base_price' => $basePrice, ':duration' => $duration, ':capacity' => $capacity,
                        ':cover' => $coverImage, ':status' => $status
                    ]);
                    $packageId = $this->conn->lastInsertId();
                }

                $this->conn->prepare("DELETE FROM packagedestinations WHERE package_id = ?")->execute([$packageId]);
                $this->conn->prepare("DELETE FROM packageflights WHERE package_id = ?")->execute([$packageId]);
                $this->conn->prepare("DELETE FROM packageaccommodations WHERE package_id = ?")->execute([$packageId]);
                $this->conn->prepare("DELETE FROM packageattractions WHERE package_id = ?")->execute([$packageId]);
                $this->conn->prepare("DELETE FROM packagerestaurants WHERE package_id = ?")->execute([$packageId]);
                
                if (!empty($_POST['destinations'])) {
                    $stmt = $this->conn->prepare("INSERT INTO packagedestinations (package_id, destination_id) VALUES (?, ?)");
                    foreach ($_POST['destinations'] as $destId) $stmt->execute([$packageId, (int)$destId]);
                }
                if (!empty($_POST['flights'])) {
                    $stmt = $this->conn->prepare("INSERT INTO packageflights (package_id, flight_id) VALUES (?, ?)");
                    foreach ($_POST['flights'] as $flightId) $stmt->execute([$packageId, (int)$flightId]);
                }
                if (!empty($_POST['accommodations'])) {
                    $stmt = $this->conn->prepare("INSERT INTO packageaccommodations (package_id, accommodation_id) VALUES (?, ?)");
                    foreach ($_POST['accommodations'] as $accId) $stmt->execute([$packageId, (int)$accId]);
                }
                if (!empty($_POST['attractions'])) {
                    $stmt = $this->conn->prepare("INSERT INTO packageattractions (package_id, attraction_id) VALUES (?, ?)");
                    foreach ($_POST['attractions'] as $attrId) $stmt->execute([$packageId, (int)$attrId]);
                }
                if (!empty($_POST['restaurants'])) {
                    $stmt = $this->conn->prepare("INSERT INTO packagerestaurants (package_id, restaurant_id) VALUES (?, ?)");
                    foreach ($_POST['restaurants'] as $restId) $stmt->execute([$packageId, (int)$restId]);
                }

                $this->conn->commit();
                header("Location: /agency/dashboard?success=package_saved");
                exit;

            } catch (PDOException $e) {
                $this->conn->rollBack();
                $redirectUrl = !empty($_POST['package_id']) ? "/agency/package/edit?id=" . $_POST['package_id'] : "/agency/package/create";
                header("Location: " . $redirectUrl . "&error=save_failed");
                exit;
            }
        }
    }
}
?>