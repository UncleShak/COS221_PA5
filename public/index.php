<?php
// public/index.php
session_start();
ini_set('display_errors', 1);
error_reporting(E_ALL);

$filePath = __DIR__ . parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
if (is_file($filePath)) {
    return false; 
}

// 2. Get the clean URL path
// Removed the brittle $baseDir string replacement which was stripping the leading '/'
$route = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
// NEW: Strip out your local subfolders so the router only sees the end of the URL
$baseDir = '/COS221/COS221_PA5/public';
if (strpos($route, $baseDir) === 0) {
    $route = substr($route, strlen($baseDir));
}
$route = rtrim($route, '/') ?: '/'; // Normalizes URL by removing trailing slashes

// Default routing to login if root is accessed
if($route === '/'){
    $route = '/login';
}

// 3. Route the request to the correct controller/view
switch($route){
    
    // --- Auth Routes ---
    case '/login':
        require_once __DIR__ . '/../src/Controllers/AuthController.php';
        $auth= new AuthController();

        if($_SERVER['REQUEST_METHOD']==='POST'){
            $auth->login();
        }else {
            $auth->showLogin();
        }
        break;
    
    case '/register':
        $title = 'Register · Tripistry';
        ob_start();
        require_once __DIR__ . '/../src/Views/auth/register.php';
        $content = ob_get_clean();
        require_once __DIR__ . '/../src/Views/layout.php';
        break;

    // --- Traveller Routes ---
    case '/traveller/dashboard':
        require_once __DIR__ . '/../config/database.php';
        $database = new Database();
        $pdo = $database->getConnection();
        require_once __DIR__ . '/../src/Controllers/AuthController.php';
        AuthController::checkRole('traveller');
        require_once __DIR__ . '/../src/Controllers/TravellerController.php';
        $controller = new TravellerController($pdo); 
        $controller->dashboard();
        break;

    case '/traveller/details':
        require_once __DIR__ . '/../config/database.php';
        $database = new Database();
        $pdo = $database->getConnection();

        require_once __DIR__ . '/../src/Controllers/AuthController.php';
        AuthController::checkRole('traveller');

        require_once __DIR__ . '/../src/Controllers/TravellerController.php';
        $controller = new TravellerController($pdo); 
        $controller->details();
        break;

    case '/traveller/submit-review':
        require_once __DIR__ . '/../config/database.php';
        $database = new Database();
        $pdo = $database->getConnection();

        require_once __DIR__ . '/../src/Controllers/TravellerController.php';
        $controller = new TravellerController($pdo);
        $controller->submitReview();
        break;

    case '/traveller/checkout':
        require_once __DIR__ . '/../config/database.php';
        $database = new Database();
        $pdo = $database->getConnection();
        require_once __DIR__ . '/../src/Controllers/AuthController.php';
        AuthController::checkRole('traveller'); 
        require_once __DIR__ . '/../src/Controllers/TravellerController.php';
        $controller = new TravellerController($pdo); 
        $controller->checkout();
        break;

    case '/traveller/process_booking':
        // 1. Establish the Database Connection
        require_once __DIR__ . '/../config/database.php';
        $database = new Database();
        $pdo = $database->getConnection();

        // 2. Pass connection to your Controller
        require_once __DIR__ . '/../src/Controllers/BookingController.php';
        $controller = new BookingController($pdo); 
        
        // 3. Execute the process (which includes the Trapdoor)
        $controller->processBooking();
        break;
    case '/traveller/cancel-booking':
        require_once __DIR__ . '/../config/database.php';
        $database = new Database();
        $pdo = $database->getConnection();

        require_once __DIR__ . '/../src/Controllers/AuthController.php';
        AuthController::checkRole('traveller');

        require_once __DIR__ . '/../src/Controllers/TravellerController.php';
        $controller = new TravellerController($pdo);
        $controller->cancelBooking();
        break;
    case '/traveller/packages':
        // 1. Get the database connection
        require_once __DIR__ . '/../config/database.php';
        $database = new Database();
        $pdo = $database->getConnection();
        
        // 2. Verify they are logged in
        require_once __DIR__ . '/../src/Controllers/AuthController.php';
        AuthController::checkRole('traveller');
        
        // 3. Hand the request off to the Controller
        require_once __DIR__ . '/../src/Controllers/TravellerController.php';
        $controller = new TravellerController($pdo); 
        $controller->packages();
        break;

    case '/traveller/group':
        require_once __DIR__ . '/../config/database.php';
        $database = new Database();
        $pdo = $database->getConnection();
        
        require_once __DIR__ . '/../src/Controllers/AuthController.php';
        AuthController::checkRole('traveller');
        
        require_once __DIR__ . '/../src/Controllers/TravellerController.php';
        $controller = new TravellerController($pdo); 
        $controller->groupHub();
        break;
    
    // --- System Routes ---
    case '/manage-data':
        // raw data manager gateway
        require_once __DIR__ . '/../src/Controllers/DataController.php';
        AuthController::checkRole('agency');

        require_once __DIR__ . '/../src/Controllers/DataController.php';
        break;

    

    // --- Fallback ---
    default:
        http_response_code(404);
        $title = '404 Not Found · Tripistry';
        $content = "
            <div style='text-align:center; padding: 10rem 2rem;'>
                <h1 style='font-family: var(--font-display); font-size: 4rem; color: var(--ocean);'>404</h1>
                <p style='color: var(--text-soft); font-size: 1.2rem;'>The destination you are looking for does not exist.</p>
                <a href='/login' class='btn-primary' style='display:inline-block; margin-top:2rem; text-decoration:none;'>Return to Base</a>
            </div>
        ";
        require_once __DIR__ . '/../src/Views/layout.php';
        break;
}
?>