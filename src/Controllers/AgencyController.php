<?php

class AgencyController{
    private $conn;

    public function __construct($db){
        $this->conn = $db;
    }

    public function dashboard() {
        try {
            $agencyId = $_SESSION['user_id'];
            
            // 1. Handle Prince's flash messages (prevents the $flash undefined error)
            $flash = null; 

            // 2. Fetch Advanced Agency Stats (prevents undefined avg_rating and review_count)
            // We use subqueries to get the exact counts and averages from the agencyreviews table
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

            // If the agency has no packages yet, $stats might be false. Let's ensure it's an array.
            if (!$stats) {
                $stats = ['total_packages' => 0, 'review_count' => 0, 'avg_rating' => null];
            }

            // 3. Fetch Packages with all the extra metrics Prince's view wants
            // We use EXISTS for the group trip check, and subqueries for bookings/ratings
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

            // 4. Send all this rich data to Prince's View
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
        // Grab the ID from the URL (e.g., ?id=1)
        $packageId = $_GET['id'] ?? $_POST['id'] ?? $_GET['package_id'] ?? $_POST['package_id'] ?? null;

        if (!$packageId) {
            header("Location: /agency/dashboard?error=missing_id");
            exit;
        }

        try {
            // SECURITY: We include agency_id in the WHERE clause so an agency 
            // can only archive packages they actually own.
            $query = "UPDATE packages 
                      SET status = 'archived' 
                      WHERE package_id = :id AND agency_id = :agency_id";
            
            $stmt = $this->conn->prepare($query);
            $stmt->execute([
                ':id' => $packageId,
                ':agency_id' => $_SESSION['user_id']
            ]);

            // Bounce them back to the Command Center with a success flag
            header("Location: /agency/dashboard?success=package_archived");
            exit;

        } catch (PDOException $e) {
            error_log("Archive Error: " . $e->getMessage());
            header("Location: /agency/dashboard?error=archive_failed");
            exit;
        }
    }

    public function packageForm() {
        try {
            // 1. Get the package ID from the URL
            $packageId = $_GET['id'] ?? null;
            
            // Prince's view explicitly checks for lowercase 'edit' or 'create'
            $formMode = $packageId ? 'edit' : 'create';

            $package = null;
            $groupTrip = false;

            // INITIALIZE ARRAYS: This prevents the Fatal Error that killed your CSS!
            $selectedDestinations = [];
            $selectedFlights = [];
            $selectedAccommodations = [];
            $selectedAttractions = [];
            $selectedRestaurants = [];
            $errors = [];
            $old = [];

            // 2. If Editing, fetch the package and its currently linked components
            if ($packageId) {
                $pkgQuery = "SELECT * FROM packages WHERE package_id = :id AND agency_id = :agency_id";
                $stmt = $this->conn->prepare($pkgQuery);
                $stmt->execute([':id' => $packageId, ':agency_id' => $_SESSION['user_id']]);
                $package = $stmt->fetch(PDO::FETCH_ASSOC);

                if (!$package) {
                    header("Location: /agency/dashboard?error=package_not_found");
                    exit;
                }

                $gtQuery = "SELECT * FROM grouptrips WHERE package_id = :id";
                $gtStmt = $this->conn->prepare($gtQuery);
                $gtStmt->execute([':id' => $packageId]);
                $groupTrip = $gtStmt->fetch(PDO::FETCH_ASSOC);

                // Fetch linked components using FETCH_COLUMN to get clean 1D arrays of IDs
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

            // 3. Fetch ALL raw materials for the dropdowns (Now fully JOINed for Prince's view)
            
            $destStmt = $this->conn->query("
                SELECT d.*, l.city, l.country 
                FROM destinations d
                JOIN locations l ON d.location_id = l.location_id
            ");
            $allDestinations = $destStmt->fetchAll(PDO::FETCH_ASSOC);

            // Mapped to origin_city and dest_city to match Prince's exact HTML
            $flightStmt = $this->conn->query("
                SELECT f.*, dep.city AS origin_city, arr.city AS dest_city
                FROM flights f
                JOIN locations dep ON f.departure_location_id = dep.location_id
                JOIN locations arr ON f.arrival_location_id = arr.location_id
            ");
            $allFlights = $flightStmt->fetchAll(PDO::FETCH_ASSOC);

            $accStmt = $this->conn->query("
                SELECT a.*, l.city, l.country
                FROM accommodations a
                JOIN locations l ON a.location_id = l.location_id
            ");
            $allAccommodations = $accStmt->fetchAll(PDO::FETCH_ASSOC);

            $attrStmt = $this->conn->query("
                SELECT attr.*, l.city, l.country 
                FROM attractions attr
                JOIN locations l ON attr.location_id = l.location_id
            ");
            $allAttractions = $attrStmt->fetchAll(PDO::FETCH_ASSOC);

            $restStmt = $this->conn->query("
                SELECT r.*, l.city, l.country 
                FROM restaurants r
                JOIN locations l ON r.location_id = l.location_id
            ");
            $allRestaurants = $restStmt->fetchAll(PDO::FETCH_ASSOC);

            // 4. Send the data to the Edit View
            $title = ($formMode === 'edit' ? 'Edit' : 'New') . ' Package · Tripistry';
            ob_start();
            require_once __DIR__ . '/../Views/agency/edit_package.php'; 
            $content = ob_get_clean();
            
            // This file will finally load again!
            require_once __DIR__ . '/../Views/layout.php';

        } catch (PDOException $e) {
            error_log("Edit Form Fetch Error: " . $e->getMessage());
            echo "A database error occurred while loading the form.";
        }
    }

    public function savePackage() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                // Start the Transaction
                $this->conn->beginTransaction();

                // 1. Extract the base data from Prince's form
                $packageId = !empty($_POST['package_id']) ? (int)$_POST['package_id'] : null;
                $agencyId = $_SESSION['user_id'];
                
                $title = htmlspecialchars($_POST['title'] ?? '');
                $description = htmlspecialchars($_POST['description'] ?? '');
                $basePrice = (float)($_POST['base_price'] ?? 0);
                $duration = (int)($_POST['duration_days'] ?? 0);
                $capacity = !empty($_POST['max_capacity']) ? (int)$_POST['max_capacity'] : null;
                $coverImage = !empty($_POST['cover_image_url']) ? filter_var($_POST['cover_image_url'], FILTER_SANITIZE_URL) : null;
                $status = $_POST['status'] ?? 'draft';

                // 2. CREATE or UPDATE the Base Package
                if ($packageId) {
                    // It's an Edit: Update the existing row
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
                    // It's a New Package: Insert a brand new row
                    $insertQuery = "INSERT INTO packages (agency_id, title, description, base_price, duration_days, max_capacity, cover_image_url, status) 
                                    VALUES (:agency_id, :title, :description, :base_price, :duration, :capacity, :cover, :status)";
                    $stmt = $this->conn->prepare($insertQuery);
                    $stmt->execute([
                        ':agency_id' => $agencyId, ':title' => $title, ':description' => $description,
                        ':base_price' => $basePrice, ':duration' => $duration, ':capacity' => $capacity,
                        ':cover' => $coverImage, ':status' => $status
                    ]);
                    $packageId = $this->conn->lastInsertId(); // Grab the new ID
                }

                // 3. Clear out old components (The safest way to handle Multi-Select Edits)
                $this->conn->prepare("DELETE FROM packagedestinations WHERE package_id = ?")->execute([$packageId]);
                $this->conn->prepare("DELETE FROM packageflights WHERE package_id = ?")->execute([$packageId]);
                $this->conn->prepare("DELETE FROM packageaccommodations WHERE package_id = ?")->execute([$packageId]);
                $this->conn->prepare("DELETE FROM packageattractions WHERE package_id = ?")->execute([$packageId]);
                $this->conn->prepare("DELETE FROM packagerestaurants WHERE package_id = ?")->execute([$packageId]);
                $this->conn->prepare("DELETE FROM grouptrips WHERE package_id = ?")->execute([$packageId]);

                // 4. Re-attach the selected components from the dropdowns
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

                // 5. Build the Group Trip if the checkbox was ticked
                if (!empty($_POST['is_group_trip'])) {
                    $gtStmt = $this->conn->prepare("INSERT INTO grouptrips (package_id, departure_date, return_date, min_participants, max_participants, meeting_point) 
                                                    VALUES (?, ?, ?, ?, ?, ?)");
                    $gtStmt->execute([
                        $packageId,
                        $_POST['departure_date'],
                        $_POST['return_date'],
                        (int)$_POST['gt_min_participants'],
                        (int)$_POST['gt_max_participants'],
                        htmlspecialchars($_POST['meeting_point'] ?? '')
                    ]);
                }

                // Lock the transaction in!
                $this->conn->commit();
                
                // 6. THE FIX: Redirect back to the Command Center!
                header("Location: /agency/dashboard?success=package_saved");
                exit;

            } catch (PDOException $e) {
                $this->conn->rollBack();
                error_log("Package Save Error: " . $e->getMessage());
                
                // If it fails, send them back to the form they were just on
                $redirectUrl = !empty($_POST['package_id']) ? "/agency/package/edit?id=" . $_POST['package_id'] : "/agency/package/create";
                header("Location: " . $redirectUrl . "&error=save_failed");
                exit;
            }
        }
    }
}

?>
