<?php
declare(strict_types=1);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['isLoggedIn'])) {
    header('Location: login_form.php');
    die();
}
