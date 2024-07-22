<?php

namespace App\Utils;

class SessionHelper {
    public static function checkLoggedIn() {
        session_start();
        if (empty($_SESSION['loggedin'])) {
            header('Location: /index.php');
            exit;
        }
    }
}