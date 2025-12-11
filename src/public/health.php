<?php
header('Content-Type: application/json');

// Check if required files exist
$requiredFiles = [
    __DIR__ . '/../../vendor/autoload.php',
    __DIR__ . '/../includes/DotEnv.php',
    __DIR__ . '/../includes/Database.php',
    __DIR__ . '/../models/Link.php',
    __DIR__ . '/../models/ShortlinkElement.php',
    __DIR__ . '/../controllers/DashboardController.php',
    __DIR__ . '/../controllers/ShortlinkController.php'
];

$missingFiles = [];
foreach ($requiredFiles as $file) {
    if (!file_exists($file)) {
        $missingFiles[] = $file;
    }
}

// Check if .env file exists
$envFile = __DIR__ . '/../.env';
$envExists = file_exists($envFile);

// Check PHP version
$phpVersion = phpversion();
$phpVersionOk = version_compare($phpVersion, '8.0', '>=');

// Check if required extensions are loaded
$requiredExtensions = ['pdo', 'pdo_mysql', 'session', 'json'];
$missingExtensions = [];
foreach ($requiredExtensions as $ext) {
    if (!extension_loaded($ext)) {
        $missingExtensions[] = $ext;
    }
}

$response = [
    'status' => 'ok',
    'timestamp' => date('c'),
    'php_version' => $phpVersion,
    'php_version_ok' => $phpVersionOk,
    'env_file_exists' => $envExists,
    'missing_files' => $missingFiles,
    'missing_extensions' => $missingExtensions
];

if (!empty($missingFiles) || !$envExists || !$phpVersionOk || !empty($missingExtensions)) {
    $response['status'] = 'error';
    http_response_code(500);
}

echo json_encode($response, JSON_PRETTY_PRINT);