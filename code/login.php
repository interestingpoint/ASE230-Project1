<?php

//taken almost directly from module1/code/7_Security/bearer/login.php
require_once 'bearer_auth.php';


header('Content-Type: application/json');


if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendJsonError(405, 'Method not allowed. Use POST.');
}


$input = json_decode(file_get_contents('php://input'), true);


if (!isset($input['username']) || !isset($input['password'])) {
    sendJsonError(400, 'Username and password required');
}

$username = $input['username'];
$password = $input['password'];


$users = [
    'admin' => 'admin',
];


if (!isset($users[$username]) || $users[$username] !== $password) {
    sendJsonError(401, 'Invalid username or password');
}




$demoTokens = [
    'admin' => 'add1233454433',
];

$token = $demoTokens[$username];


sendJsonSuccess([
    'message' => 'Login successful',
    'token' => $token,
    'user' => $username,
    'expires_in' => 3600 
]);
?>