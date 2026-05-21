<?php
// public/index.php
session_start();
ini_set('display_errors', 1);
error_reporting(E_ALL);

// 1. BYPASS FOR STATIC FILES (CSS, Images, etc.)
// If the requested file actually exists in the public folder, serve it directly.
$filePath = __DIR__ . parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
if (is_file($filePath)) {
    return false; 
}

// 2. Get the clean URL path
// Removed the brittle $baseDir string replacement which was stripping the leading '/'
$route = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$route = rtrim($route, '/') ?: '/'; // Normalizes URL by removing trailing slashes

// Default routing to login if root is accessed
if($route === '/'){
    $route = '/login';
}

// 3. Route the request to the correct controller/view
switch($route){
    
    // --- Auth Routes ---
    case '/login':
        require_once '../src/Controllers/AuthController.php';
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
        // Aeron's logic
        require_once __DIR__ . '/../src/Controllers/TravellerController.php';
        $controller = new TravellerController();
        $controller->dashboard();
        break;

    case '/traveller/details':
        require_once __DIR__ . '/../src/Controllers/TravellerController.php';
        $controller = new TravellerController();
        $controller->details();
        break;

    // --- Agency Routes ---
    case '/agency/dashboard':
        // Prince's logic
        //require_once __DIR__ . '/../src/Controllers/AgencyController.php';
        // You will likely need to instantiate the controller here eventually:
        // $controller = new AgencyController();
        // $controller->dashboard();
        break;
    
    // --- System Routes ---
    case '/manage-data':
        // raw data manager gateway
        require_once __DIR__ . '/../src/Controllers/DataController.php';
        break;

    case 'traveller/checkout':
        $title = 'Checkout - Tripistry';

        ob_start();
        require_once __DIR__ . '/../src/Views/travller/checkout.php';
        $content = ob_get_clean();
        require_once __DIR__ . '/../src/Views/layout.php';
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