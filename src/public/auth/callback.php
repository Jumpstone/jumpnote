<?php
require_once __DIR__ . '/../../../vendor/autoload.php';
require_once __DIR__ . '/../../includes/DotEnv.php';

// Load environment variables
try {
    DotEnv::load(__DIR__ . '/../../.env');
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
        'redirect_uri' => 'https://jumpnote.jumpstone4477.de/auth/callback'
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
    header('Location: /index.php');
    exit();
} else {
    // If no code parameter, redirect to login
    header('Location: /login.php');
    exit();
}