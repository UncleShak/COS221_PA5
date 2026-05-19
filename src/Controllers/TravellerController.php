<?php

class TravellerController {
    
    public function dashboard() {
        // You can pass data to the view here later (e.g., from a database)
        $this->render('traveller/dashboard', ['title' => 'Traveller Dashboard | Tripistry']);
    }

    public function details() {
        $this->render('traveller/details', ['title' => 'Package Details | Tripistry']);
    }

    /**
     * Helper method to render a view inside the master layout
     */
    private function render($viewPath, $data = []) {
        // Extract array keys into variables (so ['title' => '...'] becomes $title)
        extract($data);
        
        // 1. Turn on output buffering
        ob_start();
        
        // 2. Include the specific view (e.g., dashboard.php)
        require __DIR__ . "/../Views/{$viewPath}.php";
        
        // 3. Save the output to $content and clean the buffer
        $content = ob_get_clean();
        
        // 4. Require the master layout, which will echo the $content
        require __DIR__ . "/../Views/layout.php";
    }
}