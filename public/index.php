<?php
// public/index.php
session_start();
ini_set('display_errors', 1);
error_reporting(E_ALL);

// 1. BYPASS FOR STATIC FILES (CSS, Images, etc.)
$filePath = __DIR__ . parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
if (is_file($filePath)) {
    return false; 
}

// 2. Get the clean URL path
$route = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$baseDir = '/COS221/COS221_PA5/public';
if (strpos($route, $baseDir) === 0) {
    $route = substr($route, strlen($baseDir));
}
$route = rtrim($route, '/') ?: '/';

// 3. Route the request to the correct controller/view
switch($route){
    
    case '/':
    case '/home':
        require_once __DIR__ . '/../config/database.php';
        $database = new Database();
        $pdo = $database->getConnection();
        
        require_once __DIR__ . '/../src/Controllers/HomeController.php';
        $homeController = new HomeController($pdo);
        $homeController->index();
        break;
    
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
        require_once __DIR__ . '/../src/Controllers/RegistrationController.php';
        $controller = new RegistrationController();
        $controller->register();
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

    // FRANK'S NEW ROUTE: Safely merged
    case '/traveller/submit-agency-review':
        require_once __DIR__ . '/../config/database.php';
        $database = new Database();
        $pdo = $database->getConnection();
        require_once __DIR__ . '/../src/Controllers/TravellerController.php';
        $controller = new TravellerController($pdo);
        $controller->submitAgencyReview();
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
        require_once __DIR__ . '/../config/database.php';
        $database = new Database();
        $pdo = $database->getConnection();
        require_once __DIR__ . '/../src/Controllers/BookingController.php';
        $controller = new BookingController($pdo); 
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
        require_once __DIR__ . '/../config/database.php';
        $database = new Database();
        $pdo = $database->getConnection();
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

    case 'traveller/send-message':
    case '/traveller/send-message':
        require_once __DIR__ . '/../src/Controllers/TravellerController.php';
        $travellerController = new TravellerController();
        $travellerController->sendMessage();
        break;

    // --- Agency Routes ---
    case '/agency/dashboard':
        require_once __DIR__ . '/../config/database.php';
        $database = new Database();
        $pdo = $database->getConnection();
        require_once __DIR__ . '/../src/Controllers/AuthController.php';
        AuthController::checkRole('agency');
        require_once __DIR__ . '/../src/Controllers/AgencyController.php';
        $controller = new AgencyController($pdo);
        $controller->dashboard(); 
        break;

    case '/agency/package/create':
        require_once __DIR__ . '/../config/database.php';
        $database = new Database();
        $pdo = $database->getConnection();
        require_once __DIR__ . '/../src/Controllers/AuthController.php';
        AuthController::checkRole('agency');
        require_once __DIR__ . '/../src/Controllers/AgencyController.php';
        $controller = new AgencyController($pdo);
        $controller->packageForm(); 
        break;

    case '/agency/package/save':
        require_once __DIR__ . '/../config/database.php';
        $database = new Database();
        $pdo = $database->getConnection();
        require_once __DIR__ . '/../src/Controllers/AuthController.php';
        AuthController::checkRole('agency');
        require_once __DIR__ . '/../src/Controllers/AgencyController.php';
        $controller = new AgencyController($pdo);
        $controller->savePackage();
        break;

    case '/agency/package/edit':
        require_once __DIR__ . '/../config/database.php';
        $database = new Database();
        $pdo = $database->getConnection();
        require_once __DIR__ . '/../src/Controllers/AuthController.php';
        AuthController::checkRole('agency');
        require_once __DIR__ . '/../src/Controllers/AgencyController.php';
        $controller = new AgencyController($pdo);
        $controller->packageForm(); 
        break;

    case '/agency/package/archive':
        require_once __DIR__ . '/../config/database.php';
        $database = new Database();
        $pdo = $database->getConnection();
        require_once __DIR__ . '/../src/Controllers/AuthController.php';
        AuthController::checkRole('agency');
        require_once __DIR__ . '/../src/Controllers/AgencyController.php';
        $controller = new AgencyController($pdo);
        $controller->archivePackage();
        break;

    case '/agency/package/activate':
        require_once __DIR__ . '/../config/database.php';
        $database = new Database();
        $pdo = $database->getConnection();
        require_once __DIR__ . '/../src/Controllers/AuthController.php';
        AuthController::checkRole('agency');
        require_once __DIR__ . '/../src/Controllers/AgencyController.php';
        $controller = new AgencyController($pdo);
        $controller->activatePackage();
        break;

    // --- System Routes ---
    case '/agency/manage-data':
        require_once __DIR__ . '/../src/Controllers/AuthController.php';
        AuthController::checkRole('agency');

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            require_once __DIR__ . '/../config/database.php';
            require_once __DIR__ . '/../src/Controllers/DataController.php';
            $database = new Database();
            $pdo = $database->getConnection();
            $controller = new DataController($pdo);
            $controller->handleUpload();
            exit;
        }
        $title = 'Manage Raw Data - Tripistry';
        ob_start();
        require_once __DIR__ . '/../src/Views/agency/manage_data.php';
        $content = ob_get_clean();
        require_once __DIR__ . '/../src/Views/layout.php';
        break;

    case 'logout':
    case '/logout': 
        require_once __DIR__ . '/../src/Controllers/AuthController.php';
        $authController = new AuthController();
        $authController->logout();
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