<?php
session_start();
require_once '../../controllers/AuthController.php';

$auth = new AuthController();
$auth->logout();

header('Location: login.php'); // Redirect to login page after logout
exit();
