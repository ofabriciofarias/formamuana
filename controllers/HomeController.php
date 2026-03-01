<?php

class HomeController {
    /**
     * Exibe a página inicial (landing page).
     */
    public function index() {
        require_once __DIR__ . '/../views/home/index.php';
    }
}
