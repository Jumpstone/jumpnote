<?php
require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/../includes/DotEnv.php';
require_once __DIR__ . '/../includes/Database.php';
require_once __DIR__ . '/../controllers/ShortlinkController.php';

// Load environment variables
try {
    DotEnv::load(__DIR__ . '/../.env');
} catch (Exception $e) {
    http_response_code(500);
    die("Error loading .env file: " . $e->getMessage());
}

// Check if user is authenticated
session_start();

if (!isset($_SESSION['user_id'])) {
    // Redirect to login if not authenticated
    header('Location: login.php');
    exit();
}

// Check if the authenticated user is allowed
if ($_SESSION['user_id'] != $_ENV['ALLOWED_USER_ID']) {
    // Destroy session and redirect to login
    session_destroy();
    header('Location: login.php');
    exit();
}

// Initialize database connection
try {
    $database = new Database();
    $db = $database->getConnection();
} catch (Exception $e) {
    http_response_code(500);
    die("Database connection error: " . $e->getMessage());
}

// Initialize controller
try {
    $controller = new ShortlinkController($db);
} catch (Exception $e) {
    http_response_code(500);
    die("Controller initialization error: " . $e->getMessage());
}

// User is authenticated and allowed, show shortlinks page
try {
    $controller->index();
} catch (Exception $e) {
    http_response_code(500);
    die("Error rendering page: " . $e->getMessage());
}