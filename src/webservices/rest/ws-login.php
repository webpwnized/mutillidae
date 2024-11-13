<?php

require_once '../../includes/constants.php';
require_once '../../classes/JWT.php';
require_once '../../classes/SQLQueryHandler.php';

// Configuration Constants
define('JWT_SECRET_KEY', 'snowman');
define('JWT_EXPIRATION_TIME', 3600); // Token expiration time in seconds
define('MAX_FAILED_ATTEMPTS', 5); // Maximum number of failed login attempts
define('CORS_MAX_AGE', 600); // CORS preflight cache duration in seconds

define('TRUSTED_ORIGINS', [
    'http://mutillidae.localhost'
]);

// Define the Base URL dynamically based on the current request
define('BASE_URL', ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST']);

// Initialize SQLQueryHandler
$lSQLQueryHandler = new SQLQueryHandler(0);

// Get the origin of the request
$lOrigin = isset($_SERVER['HTTP_ORIGIN']) ? $_SERVER['HTTP_ORIGIN'] : BASE_URL;

// CORS Validation - Only allow trusted origins
if (!in_array($lOrigin, TRUSTED_ORIGINS)) {
    http_response_code(403);
    echo json_encode(["error" => "Origin not allowed."]);
    exit();
}

// Set CORS headers
header("Access-Control-Allow-Origin: $lOrigin");
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
header('Content-Type: application/json');

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    header('Access-Control-Allow-Origin: ' . $lOrigin);
    header('Access-Control-Max-Age: ' . CORS_MAX_AGE);
    http_response_code(204);
    exit();
}

// Only allow POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(["error" => "Method not allowed. Use POST."]);
    exit();
}

// Parse JSON input
$lData = json_decode(file_get_contents('php://input'), true);
$lClientId = $lData['client_id'] ?? null;
$lClientSecret = $lData['client_secret'] ?? null;
$lAudience = $lData['audience'] ?? null;

// Validate Inputs
if (!isset($lClientId) || !preg_match('/^[a-f0-9]{32}$/', $lClientId)) {
    http_response_code(400);
    echo json_encode(["error" => "Invalid Client ID format."]);
    exit();
}

if (!isset($lClientSecret) || !preg_match('/^[a-f0-9]{64}$/', $lClientSecret)) {
    http_response_code(400);
    echo json_encode(["error" => "Invalid Client Secret format."]);
    exit();
}

if (!isset($lAudience) || !filter_var($lAudience, FILTER_VALIDATE_URL)) {
    http_response_code(400);
    echo json_encode(["error" => "Invalid Audience format."]);
    exit();
}

// Define a list of valid audiences based on known endpoints
$lValidAudiences = [
    BASE_URL . "/webservices/rest/ws-cors-echo.php",
    BASE_URL . "/webservices/rest/ws-dns-lookup.php",
    BASE_URL . "/webservices/rest/ws-echo.php",
    BASE_URL . "/webservices/rest/ws-test-connectivity.php",
    BASE_URL . "/webservices/rest/ws-user-account.php",
    BASE_URL . "/webservices/soap/ws-dns-lookup.php",
    BASE_URL . "/webservices/soap/ws-echo.php",
    BASE_URL . "/webservices/soap/ws-test-connectivity.php",
    BASE_URL . "/webservices/soap/ws-user-account.php"
];

// Check if the requested audience is valid
if (!in_array($lAudience, $lValidAudiences)) {
    http_response_code(400);
    echo json_encode(["error" => "Invalid audience specified."]);
    exit();
}

// Rate limiting mechanism
session_start();
$lFailedAttemptsKey = "failed_attempts_" . $_SERVER['REMOTE_ADDR'];
if (!isset($_SESSION[$lFailedAttemptsKey])) {
    $_SESSION[$lFailedAttemptsKey] = 0;
}

// Lockout mechanism after MAX_FAILED_ATTEMPTS failed attempts
if ($_SESSION[$lFailedAttemptsKey] >= MAX_FAILED_ATTEMPTS) {
    http_response_code(429); // Too Many Requests
    echo json_encode(["error" => "Too many failed attempts. Please try again later."]);
    exit();
}

// Validate credentials
$lIsValid = $lSQLQueryHandler->authenticateByClientCredentials($lClientId, $lClientSecret);
if (!$lIsValid) {
    $_SESSION[$lFailedAttemptsKey]++;
    http_response_code(401);
    echo json_encode(["error" => "Authentication failed."]);
    exit();
} else {
    // Reset failed attempts on successful login
    $_SESSION[$lFailedAttemptsKey] = 0;
}

// Define JWT claims with audience
$lPayload = [
    'iss' => BASE_URL,                      // Issuer is your domain
    'aud' => $lAudience,                    // Audience for the token
    'iat' => time(),                        // Issued at
    'nbf' => time(),                        // Not before
    'exp' => time() + JWT_EXPIRATION_TIME,  // Expiration time
    'sub' => $lClientId,                    // Subject is the client ID
    'scope' => 'execute:method',            // Scope of the token
    'jti' => bin2hex(random_bytes(16))      // JWT ID
];

// Encode the JWT token with a specified algorithm
$lJwt = JWT::encode($lPayload, JWT_SECRET_KEY, 'HS256'); // Use a secure algorithm

// Respond with JWT token
http_response_code(200);
echo json_encode([
    'access_token' => $lJwt,
    'token_type' => 'bearer',
    'expires_in' => JWT_EXPIRATION_TIME
]);

?>
