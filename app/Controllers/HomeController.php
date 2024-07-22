<?php

namespace App\Controllers;

use App\Models\PhotoModel;

class HomeController {
    private $photoModel;

    public function __construct($pdo) {
        $this->photoModel = new PhotoModel($pdo);
    }

    private function getAllPages($allPhotos, $photosPerPage) {
        return ceil($allPhotos / $photosPerPage);
    }

    public function index() {
        session_start();
        $message = isset($_GET['message']) ? htmlspecialchars($_GET['message']) : '';
        
        $photosPerPage = 9;
        $allPhotos = $this->photoModel->getPhotoCount();
        $allPages = $this->getAllPages($allPhotos, $photosPerPage);
        $page = (isset($_GET['page']) && !empty($_GET['page']) && $_GET['page'] > 0 && $_GET['page'] <= $allPages) ? intval($_GET['page']) : 1;
        $start = ($page - 1) * $photosPerPage;
        $photos = $this->photoModel->getPhotos($start, $photosPerPage);

        $view = 'home/index.php';
        echo "Trying to load view from: " . __DIR__ . '/app/Views/template.php';
        require_once __DIR__ . '/app/Views/template.php';
    }
}


?>
