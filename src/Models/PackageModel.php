<?php
//Handles all package database queries

class PackageModel {
    private $pdo;
    
    public function __construct($pdo) {
        $this->pdo = $pdo;
    }
    
    /**
     * Get all active packages with basic info
     */
    public function getAllPackages($limit = 20) {
        $sql = "SELECT p.package_id as id, 
                       p.title, 
                       p.description,
                       p.description as destination,
                       p.base_price as price, 
                       p.duration_days,
                       p.cover_image_url as image_url,
                       'Tripistry Agency' as agency_name,
                       COALESCE(AVG(r.rating), 0) as avg_rating,
                       COUNT(DISTINCT r.review_id) as review_count
                FROM packages p
                LEFT JOIN packagereviews r ON p.package_id = r.package_id
                WHERE p.status = 'active'
                GROUP BY p.package_id
                ORDER BY p.created_at DESC
                LIMIT ?";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$limit]);
        return $stmt->fetchAll();
    }
    
    /**
     * Get single package by ID with full details (itinerary, inclusions, reviews)
     */
    public function getPackageById($id) {
        $sql = "SELECT p.package_id as id, 
                       p.title, 
                       p.description,
                       p.description as destination,
                       p.base_price as price, 
                       p.duration_days,
                       p.cover_image_url as image_url,
                       'Tripistry Agency' as agency_name,
                       1 as verified,
                       COALESCE(AVG(r.rating), 0) as avg_rating
                FROM packages p
                LEFT JOIN packagereviews r ON p.package_id = r.package_id
                WHERE p.package_id = ? AND p.status = 'active'
                GROUP BY p.package_id";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$id]);
        $package = $stmt->fetch();
        
        // THE FIX: Fetch the extra data if the package exists
        if ($package) {
            // Wrapping in try-catch just in case Prince hasn't finalized these tables yet!
                $package['itinerary'] = $this->getItinerary($id);
                $package['inclusions'] = $this->getInclusions($id);
                $package['reviews'] = $this->getReviews($id);
            
        }
        
        return $package;
    }
    
    /**
     * Get filtered packages with multiple criteria (Day 3)
     */
    public function getFilteredPackages($filters = [], $sort = 'price_asc', $limit = 12, $offset = 0) {
        $sql = "SELECT p.package_id as id, 
                       p.title, 
                       p.description,
                       p.description as destination,
                       p.base_price as price, 
                       p.duration_days,
                       p.cover_image_url as image_url,
                       'Tripistry Agency' as agency_name,
                       COALESCE(AVG(r.rating), 0) as avg_rating,
                       COUNT(DISTINCT r.review_id) as review_count
                FROM packages p
                LEFT JOIN packagereviews r ON p.package_id = r.package_id
                WHERE p.status = 'active'";
        
        $params = [];
        
        // Search filter (searches title and description)
        if (!empty($filters['search'])) {
            $sql .= " AND (p.title LIKE ? OR p.description LIKE ?)";
            $searchTerm = "%{$filters['search']}%";
            $params[] = $searchTerm;
            $params[] = $searchTerm;
        }

        // Destination filter (searches title and description since no dedicated column)
        if (!empty($filters['destination'])) {
            $sql .= " AND (p.title LIKE ? OR p.description LIKE ?)";
            $destTerm = "%{$filters['destination']}%";
            $params[] = $destTerm;
            $params[] = $destTerm;
        }
        
        // Price range filter
        if (!empty($filters['min_price'])) {
            $sql .= " AND p.base_price >= ?";
            $params[] = (float)$filters['min_price'];
        }
        if (!empty($filters['max_price'])) {
            $sql .= " AND p.base_price <= ?";
            $params[] = (float)$filters['max_price'];
        }
        
        $sql .= " GROUP BY p.package_id";
        
        // Sorting
        switch($sort) {
            case 'price_asc':
                $sql .= " ORDER BY p.base_price ASC";
                break;
            case 'price_desc':
                $sql .= " ORDER BY p.base_price DESC";
                break;
            case 'rating_desc':
                $sql .= " ORDER BY avg_rating DESC, review_count DESC";
                break;
            default:
                $sql .= " ORDER BY p.base_price ASC";
        }
        
        $sql .= " LIMIT " . (int)$limit . " OFFSET " . (int)$offset;
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params); 
        return $stmt->fetchAll();
    }
    
    /**
     * Get total count of filtered packages for pagination
     */
    public function getFilteredCount($filters = []) {
        $sql = "SELECT COUNT(DISTINCT p.package_id) as total
                FROM packages p
                LEFT JOIN packagereviews r ON p.package_id = r.package_id
                WHERE p.status = 'active'";
        
        $params = [];
        
        if (!empty($filters['search'])) {
            $sql .= " AND (p.title LIKE ? OR p.description LIKE ?)";
            $searchTerm = "%{$filters['search']}%";
            $params[] = $searchTerm;
            $params[] = $searchTerm;
        }

        if (!empty($filters['destination'])) {
            $sql .= " AND (p.title LIKE ? OR p.description LIKE ?)";
            $destTerm = "%{$filters['destination']}%";
            $params[] = $destTerm;
            $params[] = $destTerm;
        }
        
        if (!empty($filters['min_price'])) {
            $sql .= " AND p.base_price >= ?";
            $params[] = (float)$filters['min_price'];
        }
        
        if (!empty($filters['max_price'])) {
            $sql .= " AND p.base_price <= ?";
            $params[] = (float)$filters['max_price'];
        }
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        $result = $stmt->fetch();
        return $result['total'] ?? 0;
    }
    
    /**
     * Get top rated packages (for sidebar and recommendations)
     */
    public function getTopRatedPackages($limit = 6) {
        $sql = "SELECT p.*, a.name as agency_name,
                       COALESCE(AVG(r.rating), 0) as avg_rating,
                       COUNT(r.id) as review_count
                FROM packages p
                JOIN agencies a ON p.agency_id = a.id
                LEFT JOIN reviews r ON p.id = r.package_id
                WHERE p.status = 'active'
                GROUP BY p.id
                HAVING avg_rating >= 4.0 OR review_count > 0
                ORDER BY avg_rating DESC, review_count DESC
                LIMIT ?";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$limit]);
        return $stmt->fetchAll();
    }
    
    /**
     * Get packages by destination (for similar packages)
     */
    public function getPackagesByDestination($destination, $limit = 10) {
        $sql = "SELECT p.*, a.name as agency_name,
                       COALESCE(AVG(r.rating), 0) as avg_rating
                FROM packages p
                JOIN agencies a ON p.agency_id = a.id
                LEFT JOIN reviews r ON p.id = r.package_id
                WHERE (p.destination LIKE ? OR p.city LIKE ?) 
                  AND p.status = 'active'
                  AND p.id NOT IN (SELECT id FROM packages WHERE id = p.id LIMIT 1)
                GROUP BY p.id
                LIMIT ?";
        
        $stmt = $this->pdo->prepare($sql);
        $searchTerm = "%{$destination}%";
        $stmt->execute([$searchTerm, $searchTerm, $limit]);
        return $stmt->fetchAll();
    }
    
    /**
     * Search packages by keyword (for For You recommendations)
     */
    public function searchPackages($keyword, $limit = 10) {
        $sql = "SELECT p.*, a.name as agency_name,
                       COALESCE(AVG(r.rating), 0) as avg_rating
                FROM packages p
                JOIN agencies a ON p.agency_id = a.id
                LEFT JOIN reviews r ON p.id = r.package_id
                WHERE (p.title LIKE ? 
                       OR p.destination LIKE ? 
                       OR p.description LIKE ?)
                  AND p.status = 'active'
                GROUP BY p.id
                ORDER BY 
                    CASE 
                        WHEN p.title LIKE ? THEN 3
                        WHEN p.destination LIKE ? THEN 2
                        ELSE 1
                    END DESC
                LIMIT ?";
        
        $searchTerm = "%{$keyword}%";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$searchTerm, $searchTerm, $searchTerm, $searchTerm, $searchTerm, $limit]);
        return $stmt->fetchAll();
    }
    
    /**
     * Get filter options for UI dropdowns
     */
    public function getFilterOptions() {
        $options = [];
        
        // Get price range
        $stmt = $this->pdo->query("SELECT MIN(base_price) as min_price, MAX(base_price) as max_price FROM packages WHERE status = 'active'");
        $priceRange = $stmt->fetch();
        $options['min_price'] = floor($priceRange['min_price'] ?? 0);
        $options['max_price'] = ceil($priceRange['max_price'] ?? 10000);
        
        // Get durations
        $stmt = $this->pdo->query("SELECT DISTINCT duration_days FROM packages WHERE status = 'active' ORDER BY duration_days");
        $options['durations'] = $stmt->fetchAll(PDO::FETCH_COLUMN);

        // No dedicated destination column — use empty array (sidebar dropdown hidden or populated from titles)
        $options['destinations'] = [];
        
        return $options;
    }
    
    // ========== PRIVATE HELPER METHODS ==========
    
    private function getItinerary($packageId) {
        $sql = "SELECT pd.day_number, 
                       d.name as title, 
                       d.description 
                FROM packagedestinations pd
                JOIN destinations d ON pd.destination_id = d.destination_id
                WHERE pd.package_id = ? 
                ORDER BY pd.day_number ASC, pd.visit_order ASC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$packageId]);
        return $stmt->fetchAll();
    }
    
    private function getInclusions($packageId) {
        $inclusions = [];
        $sqlHotels = "SELECT 'hotel' as type, a.name, a.description 
                      FROM packageaccommodations pa
                      JOIN accommodations a ON pa.accommodation_id = a.accommodation_id
                      WHERE pa.package_id = ?";
        $stmtHotels = $this->pdo->prepare($sqlHotels);
        $stmtHotels->execute([$packageId]);
        foreach ($stmtHotels->fetchAll() as $item) {
            $inclusions['hotel'][] = $item;
        }

        $sqlFlights = "SELECT 'flight' as type, 
                              CONCAT(f.airline_name, ' - ', f.flight_number) as name, 
                              CONCAT(f.departure_airport, ' to ', f.arrival_airport) as description 
                       FROM packageflights pf
                       JOIN flights f ON pf.flight_id = f.flight_id
                       WHERE pf.package_id = ?";
        $stmtFlights = $this->pdo->prepare($sqlFlights);
        $stmtFlights->execute([$packageId]);
        foreach ($stmtFlights->fetchAll() as $item) {
            $inclusions['flight'][] = $item;
        }

        $sqlAttractions = "SELECT 'attraction' as type, a.name, a.description 
                           FROM packageattractions pa
                           JOIN attractions a ON pa.attraction_id = a.attraction_id
                           WHERE pa.package_id = ? AND pa.is_included = 1";
        $stmtAttractions = $this->pdo->prepare($sqlAttractions);
        $stmtAttractions->execute([$packageId]);
        foreach ($stmtAttractions->fetchAll() as $item) {
            $inclusions['attraction'][] = $item;
        }

        $sqlRestaurants = "SELECT 'restaurant' as type, r.name, r.description 
                           FROM packagerestaurants pr
                           JOIN restaurants r ON pr.restaurant_id = r.restaurant_id
                           WHERE pr.package_id = ?";
        $stmtRestaurants = $this->pdo->prepare($sqlRestaurants);
        $stmtRestaurants->execute([$packageId]);
        foreach ($stmtRestaurants->fetchAll() as $item) {
            $inclusions['restaurant'][] = $item;
        }

        return $inclusions;
    }
    
    private function getReviews($packageId) {
        $sql = "SELECT r.*, COALESCE(u.email, 'Verified Traveller') as username 
                FROM packagereviews r
                JOIN users u ON r.traveller_id = u.user_id
                WHERE r.package_id = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$packageId]);
        return $stmt->fetchAll();
    }   
}
?>