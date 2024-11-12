<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: ../src/views/auth/login.php');
    exit;
}

require_once '../src/views/dashboard.php';
?>
