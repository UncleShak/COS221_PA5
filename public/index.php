<?php 
session_start();

$baseDir= dirname($_SERVER['SCRIPT_NAME']);
$requestUri= parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

$route= str_replace($baseDir, '', $requestUri);
if(empty($route) || $route === '/'){
    $route ='/login';
}

switch($route){
    case '/login':
        //call controller to load the view
        require_once '../src/Controllers/AuthController.php';
        // $auth = new AuthController();
        //$auth->showLogin();

        //MVP scramble Bypass(will remove after finishing authcont.)
        require_once '../src/Views/auth/login.php';
        break;
    
    case '/register':
        require_once '../src/Views/auth/register.php';

    case '/traveller/dashboard':
        //Aeron's logic
        require_once '../src/Controllers/TravellerController.php';
        break;

    case '/agency/dashboard':
        //prince's logic
        require_once '../src/Controllers/AgencyController.php';
        break;
    
    case '/manage-data':
        // raw data manager gateway
        require_once '../src/Controllers/DataController.php';
        break;

    default:
        http_response_code(404);
        echo "<div style='text-align:center; padding: 5rem; font-family: sans-serif;'>";
        echo "<h1 style='color: #00a6c7;'>404 - Off the Map</h1>";
        echo "<p>The destination you are looking for does not exist.</p>";
        echo "</div>";
        break;

}

?>
