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
        $sql = "SELECT p.*, 
                       a.name as agency_name, 
                       a.rating as agency_rating,
                       COALESCE(AVG(r.rating), 0) as avg_rating,
                       COUNT(DISTINCT r.id) as review_count
                FROM packages p
                JOIN agencies a ON p.agency_id = a.id
                LEFT JOIN reviews r ON p.id = r.package_id
                WHERE p.status = 'active'
                GROUP BY p.id
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
        // Main package query
        $sql = "SELECT p.*, 
                       a.name as agency_name, 
                       a.description as agency_description,
                       a.rating as agency_rating,
                       a.verified,
                       COALESCE(AVG(r.rating), 0) as avg_rating
                FROM packages p
                JOIN agencies a ON p.agency_id = a.id
                LEFT JOIN reviews r ON p.id = r.package_id
                WHERE p.id = ? AND p.status = 'active'
                GROUP BY p.id";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$id]);
        $package = $stmt->fetch();
        
        if ($package) {
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
        $sql = "SELECT p.*, 
                       a.name as agency_name,
                       COALESCE(AVG(r.rating), 0) as avg_rating,
                       COUNT(DISTINCT r.id) as review_count
                FROM packages p
                JOIN agencies a ON p.agency_id = a.id
                LEFT JOIN reviews r ON p.id = r.package_id
                WHERE p.status = 'active'";
        
        $params = [];
        
        // Destination filter
        if (!empty($filters['destination'])) {
            $sql .= " AND (p.destination LIKE ? OR p.city LIKE ?)";
            $params[] = "%{$filters['destination']}%";
            $params[] = "%{$filters['destination']}%";
        }
        
        // Price range filter
        if (!empty($filters['min_price'])) {
            $sql .= " AND p.price >= ?";
            $params[] = (float)$filters['min_price'];
        }
        
        if (!empty($filters['max_price'])) {
            $sql .= " AND p.price <= ?";
            $params[] = (float)$filters['max_price'];
        }
        
        // Duration filter
        if (!empty($filters['duration'])) {
            $sql .= " AND p.duration_days = ?";
            $params[] = (int)$filters['duration'];
        }
        
        // Search filter
        if (!empty($filters['search'])) {
            $sql .= " AND (p.title LIKE ? OR p.destination LIKE ? OR p.description LIKE ?)";
            $searchTerm = "%{$filters['search']}%";
            $params[] = $searchTerm;
            $params[] = $searchTerm;
            $params[] = $searchTerm;
        }
        
        $sql .= " GROUP BY p.id";
        
        // Rating filter (must use HAVING)
        if (!empty($filters['min_rating'])) {
            $sql .= " HAVING avg_rating >= ?";
            $params[] = (float)$filters['min_rating'];
        }
        
        // Sorting
        switch($sort) {
            case 'price_asc':
                $sql .= " ORDER BY p.price ASC";
                break;
            case 'price_desc':
                $sql .= " ORDER BY p.price DESC";
                break;
            case 'rating_desc':
                $sql .= " ORDER BY avg_rating DESC, review_count DESC";
                break;
            case 'duration_asc':
                $sql .= " ORDER BY p.duration_days ASC";
                break;
            case 'duration_desc':
                $sql .= " ORDER BY p.duration_days DESC";
                break;
            case 'newest':
                $sql .= " ORDER BY p.created_at DESC";
                break;
            default:
                $sql .= " ORDER BY p.price ASC";
        }
        
        $sql .= " LIMIT ? OFFSET ?";
        $params[] = $limit;
        $params[] = $offset;
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }
    
    /**
     * Get total count of filtered packages for pagination
     */
    public function getFilteredCount($filters = []) {
        // Build subquery for counting
        $sql = "SELECT COUNT(DISTINCT p.id) as total
                FROM packages p
                JOIN agencies a ON p.agency_id = a.id
                LEFT JOIN reviews r ON p.id = r.package_id
                WHERE p.status = 'active'";
        
        $params = [];
        
        if (!empty($filters['destination'])) {
            $sql .= " AND (p.destination LIKE ? OR p.city LIKE ?)";
            $params[] = "%{$filters['destination']}%";
            $params[] = "%{$filters['destination']}%";
        }
        
        if (!empty($filters['min_price'])) {
            $sql .= " AND p.price >= ?";
            $params[] = (float)$filters['min_price'];
        }
        
        if (!empty($filters['max_price'])) {
            $sql .= " AND p.price <= ?";
            $params[] = (float)$filters['max_price'];
        }
        
        if (!empty($filters['duration'])) {
            $sql .= " AND p.duration_days = ?";
            $params[] = (int)$filters['duration'];
        }
        
        if (!empty($filters['search'])) {
            $sql .= " AND (p.title LIKE ? OR p.destination LIKE ? OR p.description LIKE ?)";
            $searchTerm = "%{$filters['search']}%";
            $params[] = $searchTerm;
            $params[] = $searchTerm;
            $params[] = $searchTerm;
        }
        
        $sql .= " GROUP BY p.id";
        
        if (!empty($filters['min_rating'])) {
            $sql .= " HAVING COALESCE(AVG(r.rating), 0) >= ?";
            $params[] = (float)$filters['min_rating'];
        }
        
        // Wrap in count query
        $countSql = "SELECT COUNT(*) as total FROM ($sql) as filtered";
        $stmt = $this->pdo->prepare($countSql);
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
        
        // Get unique destinations
        $stmt = $this->pdo->query("SELECT DISTINCT destination FROM packages WHERE status = 'active' ORDER BY destination");
        $options['destinations'] = $stmt->fetchAll(PDO::FETCH_COLUMN);
        
        // Get price range
        $stmt = $this->pdo->query("SELECT MIN(price) as min_price, MAX(price) as max_price FROM packages WHERE status = 'active'");
        $priceRange = $stmt->fetch();
        $options['min_price'] = floor($priceRange['min_price'] ?? 0);
        $options['max_price'] = ceil($priceRange['max_price'] ?? 10000);
        
        // Get durations
        $stmt = $this->pdo->query("SELECT DISTINCT duration_days FROM packages WHERE status = 'active' ORDER BY duration_days");
        $options['durations'] = $stmt->fetchAll(PDO::FETCH_COLUMN);
        
        return $options;
    }
    
    // ========== PRIVATE HELPER METHODS ==========
    
    private function getItinerary($packageId) {
        $sql = "SELECT day_number, title, description, activity_type 
                FROM itinerary 
                WHERE package_id = ? 
                ORDER BY day_number";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$packageId]);
        return $stmt->fetchAll();
    }
    
    private function getInclusions($packageId) {
        $sql = "SELECT type, name, description, details 
                FROM package_inclusions 
                WHERE package_id = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$packageId]);
        
        $inclusions = [];
        foreach($stmt->fetchAll() as $item) {
            $type = $item['type'];
            if (!isset($inclusions[$type])) {
                $inclusions[$type] = [];
            }
            $inclusions[$type][] = $item;
        }
        return $inclusions;
    }
    
    private function getReviews($packageId) {
        $sql = "SELECT r.*, u.username 
                FROM reviews r
                JOIN users u ON r.user_id = u.id
                WHERE r.package_id = ?
                ORDER BY r.created_at DESC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$packageId]);
        return $stmt->fetchAll();
    }
}
?>