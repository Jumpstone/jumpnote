<?php
require_once '../vendor/autoload.php';
require_once '../includes/DotEnv.php';

// Load environment variables
try {
    DotEnv::load(__DIR__ . '/../.env');
} catch (Exception $e) {
    die("Error loading .env file: " . $e->getMessage());
}

// Check if we're receiving a callback from Discord
if (isset($_GET['code'])) {
    // Exchange the authorization code for an access token
    $tokenUrl = 'https://discord.com/api/oauth2/token';
    $data = [
        'client_id' => $_ENV['DISCORD_CLIENT_ID'],
        'client_secret' => $_ENV['DISCORD_CLIENT_SECRET'],
        'grant_type' => 'authorization_code',
        'code' => $_GET['code'],
        'redirect_uri' => 'http://' . $_SERVER['HTTP_HOST'] . '/login.php'
    ];

    $options = [
        'http' => [
            'header' => "Content-Type: application/x-www-form-urlencoded\r\n",
            'method' => 'POST',
            'content' => http_build_query($data)
        ]
    ];

    $context = stream_context_create($options);
    $result = file_get_contents($tokenUrl, false, $context);
    
    if ($result === FALSE) {
        die('Error exchanging code for token');
    }

    $tokenData = json_decode($result, true);
    
    // Use the access token to get user information
    $userInfoUrl = 'https://discord.com/api/users/@me';
    $userInfoOptions = [
        'http' => [
            'header' => "Authorization: Bearer " . $tokenData['access_token'] . "\r\n",
            'method' => 'GET'
        ]
    ];

    $userInfoContext = stream_context_create($userInfoOptions);
    $userInfoResult = file_get_contents($userInfoUrl, false, $userInfoContext);
    
    if ($userInfoResult === FALSE) {
        die('Error getting user information');
    }

    $userData = json_decode($userInfoResult, true);
    
    // Store user ID in session
    session_start();
    $_SESSION['user_id'] = $userData['id'];
    
    // Redirect to dashboard
    header('Location: index.php');
    exit();
}

// If not receiving a callback, redirect to Discord for authentication
$authUrl = 'https://discord.com/api/oauth2/authorize?' . http_build_query([
    'client_id' => $_ENV['DISCORD_CLIENT_ID'],
    'redirect_uri' => 'http://' . $_SERVER['HTTP_HOST'] . '/login.php',
    'response_type' => 'code',
    'scope' => 'identify'
]);

header('Location: ' . $authUrl);
exit();