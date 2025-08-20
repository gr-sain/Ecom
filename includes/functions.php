<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../traits/HelperTrait.php';

class Functions {
    use HelperTrait;
    
    public function isLoggedIn() {
        return isset($_SESSION['user_id']);
    }
    
    public function isAdmin() {
        return isset($_SESSION['role']) && $_SESSION['role'] == 'admin';
    }
    
    public function requireLogin() {
        if (!$this->isLoggedIn()) {
            $_SESSION['redirect_url'] = $_SERVER['REQUEST_URI'];
            $this->redirect(SITE_URL . '/public/login.php');
        }
    }
    
    public function requireAdmin() {
        $this->requireLogin();
        if (!$this->isAdmin()) {
            $this->redirect(SITE_URL . '/public/index.php');
        }
    }
    
    public function displayError($message) {
        return '<div class="alert alert-danger">' . $message . '</div>';
    }
    
    public function displaySuccess($message) {
        return '<div class="alert alert-success">' . $message . '</div>';
    }
    
    public function displayWarning($message) {
        return '<div class="alert alert-warning">' . $message . '</div>';
    }
    
    public function displayInfo($message) {
        return '<div class="alert alert-info">' . $message . '</div>';
    }
    
    public function paginate($totalItems, $itemsPerPage, $currentPage, $url) {
        $totalPages = ceil($totalItems / $itemsPerPage);
        $pagination = '';
        
        if ($totalPages > 1) {
            $pagination .= '<nav><ul class="pagination">';
            
            // Previous button
            if ($currentPage > 1) {
                $pagination .= '<li class="page-item"><a class="page-link" href="' . $url . '?page=' . ($currentPage - 1) . '">Previous</a></li>';
            }
            
            // Page numbers
            for ($i = 1; $i <= $totalPages; $i++) {
                $active = $i == $currentPage ? ' active' : '';
                $pagination .= '<li class="page-item' . $active . '"><a class="page-link" href="' . $url . '?page=' . $i . '">' . $i . '</a></li>';
            }
            
            // Next button
            if ($currentPage < $totalPages) {
                $pagination .= '<li class="page-item"><a class="page-link" href="' . $url . '?page=' . ($currentPage + 1) . '">Next</a></li>';
            }
            
            $pagination .= '</ul></nav>';
        }
        
        return $pagination;
    }
}

$functions = new Functions();
?>