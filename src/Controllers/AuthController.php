<?php
require_once '../config/database.php';
// require_once '../src/Models/UserModel.php'; 

class AuthController{
    public function showLogin(){ // self explanatory
        $title ='Sign In - Tripistry';
        ob_start();
        require_once '../src/Views/auth/login.php';
        $content = ob_get_clean();
        require_once '../src/Views/layout.php';
    }

    public function login(){
        session_start();

        
    }
}
?>