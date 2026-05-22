<?php
// src/Controllers/TravellerController.php
// Owner: Aeron - Handles all traveller-facing page logic

require_once __DIR__ . '/../Models/PackageModel.php';

class TravellerController {
    private $packageModel;
    
    public function __construct($pdo) {
        $this->packageModel = new PackageModel($pdo);
    }
    
    /**
     * Dashboard - Main browsing page with filters
     */
    public function dashboard() {
        // Build filters array from GET parameters
        $filters = [
            'destination' => trim($_GET['destination'] ?? ''),
            'min_price' => $this->getNumericParam('min_price'),
            'max_price' => $this->getNumericParam('max_price'),
            'duration' => $this->getNumericParam('duration'),
            'min_rating' => $this->getFloatParam('min_rating'),
            'search' => trim($_GET['search'] ?? '')
        ];
        
        // Remove empty filters
        $filters = array_filter($filters, function($value) {
            return $value !== null && $value !== '';
        });
        
        $sort = $_GET['sort'] ?? 'price_asc';
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $limit = 12;
        $offset = ($page - 1) * $limit;
        
        // Get data
        $packages = $this->packageModel->getFilteredPackages($filters, $sort, $limit, $offset);
        $totalPackages = $this->packageModel->getFilteredCount($filters);
        $totalPages = ceil($totalPackages / $limit);
        $filterOptions = $this->packageModel->getFilterOptions();
        $topRated = $this->packageModel->getTopRatedPackages(5);
        
        // Pass to view
        $currentFilters = $filters;
        $currentSort = $sort;
        $currentPage = $page;
        
        require_once __DIR__ . '/../Views/traveller/dashboard.php';
    }
    
    /**
     * Package Details Page
     */
    public function details() {
        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        
        if ($id <= 0) {
            header('Location: index.php?route=traveller/dashboard');
            exit;
        }
        
        $package = $this->packageModel->getPackageById($id);
        
        if (!$package) {
            http_response_code(404);
            echo "<h1>Package Not Found</h1><p>The package you're looking for doesn't exist.</p>";
            exit;
        }
        
        // Get similar packages
        $similarPackages = $this->packageModel->getPackagesByDestination($package['destination'], 4);
        
        require_once __DIR__ . '/../Views/traveller/details.php';
    }
    
    /**
     * For You Recommendations (Bonus Task)
     */
    public function forYou() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        $recommendations = [];
        
        if (isset($_SESSION['user_id']) && $_SESSION['user_type'] === 'traveller') {
            $userId = $_SESSION['user_id'];
            $preferredDestination = $this->getUserPreferredDestination($userId);
            
            if ($preferredDestination) {
                $recommendations = $this->packageModel->getPackagesByDestination($preferredDestination, 10);
            } else {
                $recommendations = $this->packageModel->getTopRatedPackages(10);
            }
        } else {
            $recommendations = $this->packageModel->getTopRatedPackages(10);
        }
        
        // Return JSON for AJAX requests
        if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest') {
            header('Content-Type: application/json');
            echo json_encode($recommendations);
            exit;
        }
        
        require_once __DIR__ . '/../Views/traveller/for_you.php';
    }
    
    /**
     * AJAX endpoint for filtering without page reload
     */
    public function filterAjax() {
        // Get input (supports both JSON and form data)
        $input = json_decode(file_get_contents('php://input'), true);
        if (!$input) {
            $input = $_POST;
        }
        
        $filters = [
            'destination' => trim($input['destination'] ?? ''),
            'min_price' => isset($input['min_price']) && $input['min_price'] !== '' ? (float)$input['min_price'] : null,
            'max_price' => isset($input['max_price']) && $input['max_price'] !== '' ? (float)$input['max_price'] : null,
            'duration' => isset($input['duration']) && $input['duration'] !== '' ? (int)$input['duration'] : null,
            'min_rating' => isset($input['min_rating']) && $input['min_rating'] !== '' ? (float)$input['min_rating'] : null,
            'search' => trim($input['search'] ?? '')
        ];
        
        $filters = array_filter($filters, function($value) {
            return $value !== null && $value !== '';
        });
        
        $sort = $input['sort'] ?? 'price_asc';
        $page = isset($input['page']) ? (int)$input['page'] : 1;
        $limit = 12;
        $offset = ($page - 1) * $limit;
        
        $packages = $this->packageModel->getFilteredPackages($filters, $sort, $limit, $offset);
        $total = $this->packageModel->getFilteredCount($filters);
        
        header('Content-Type: application/json');
        echo json_encode([
            'success' => true,
            'packages' => $packages,
            'total' => $total,
            'page' => $page,
            'hasMore' => ($page * $limit) < $total
        ]);
        exit;
    }
    
    // ========== PRIVATE HELPER METHODS ==========
    
    private function getNumericParam($key) {
        if (isset($_GET[$key]) && $_GET[$key] !== '') {
            return (int)$_GET[$key];
        }
        return null;
    }
    
    private function getFloatParam($key) {
        if (isset($_GET[$key]) && $_GET[$key] !== '') {
            return (float)$_GET[$key];
        }
        return null;
    }
    
    private function getUserPreferredDestination($userId) {
        global $pdo;
        
        $sql = "SELECT p.destination, COUNT(*) as count
                FROM bookings b
                JOIN packages p ON b.package_id = p.id
                WHERE b.user_id = ?
                GROUP BY p.destination
                ORDER BY count DESC
                LIMIT 1";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$userId]);
        $result = $stmt->fetch();
        
        return $result ? $result['destination'] : null;
    }
}
?>