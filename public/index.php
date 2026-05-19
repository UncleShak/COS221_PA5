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
        $title = 'Register · Tripistry';
        ob_start();
        require_once '../src/Views/auth/register.php';
        $content = ob_get_clean();
        require_once '../src/Views/layout.php';
        break;

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
        $title = '404 Not Found';
        $content = "
            <div style='text-align:center; padding: 10rem 2rem;'>
                <h1 style='font-family: var(--font-display); font-size: 4rem; color: var(--ocean);'>404</h1>
                <p style='color: var(--text-soft); font-size: 1.2rem;'>The destination you are looking for does not exist.</p>
                <a href='/login' class='btn-primary' style='display:inline-block; margin-top:2rem; text-decoration:none;'>Return to Base</a>
            </div>
        ";
        require_once '../src/Views/layout.php';
        break;

}

?>
