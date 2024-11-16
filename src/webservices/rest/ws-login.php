<?php

require_once '../../includes/constants.php';
require_once '../../classes/JWT.php';
require_once '../../classes/SQLQueryHandler.php';
require_once '../includes/ws-constants.php';

// Initialize SQLQueryHandler
$lSQLQueryHandler = new SQLQueryHandler(0);

// CORS Validation - Only allow trusted origins
if (!in_array(CORS_REQUEST_ORIGIN, CORS_TRUSTED_ORIGINS)) {
    http_response_code(RESPONSE_CODE_NOT_FOUND);
    echo json_encode(["error" => "Origin not allowed."]);
    exit();
}

// Set CORS headers
header(CORS_ACCESS_CONTROL_ALLOW_ORIGIN);
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
header(CONTENT_TYPE_JSON);

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    header(CORS_ACCESS_CONTROL_ALLOW_ORIGIN);
    header(CORS_ACCESS_CONTROL_MAX_AGE);
    http_response_code(RESPONSE_CODE_NO_CONTENT);
    exit();
}

// Only allow POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(RESPONSE_CODE_METHOD_NOT_ALLOWED);
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
    http_response_code(RESPONSE_CODE_BAD_REQUEST);
    echo json_encode(["error" => "Invalid Client ID format."]);
    exit();
}

if (!isset($lClientSecret) || !preg_match('/^[a-f0-9]{64}$/', $lClientSecret)) {
    http_response_code(RESPONSE_CODE_BAD_REQUEST);
    echo json_encode(["error" => "Invalid Client Secret format."]);
    exit();
}

if (!isset($lAudience) || !filter_var($lAudience, FILTER_VALIDATE_URL)) {
    http_response_code(RESPONSE_CODE_BAD_REQUEST);
    echo json_encode(["error" => "Invalid Audience format."]);
    exit();
}

// Check if the requested audience is valid
if (!in_array($lAudience, JWT_VALID_AUDIENCES)) {
    http_response_code(RESPONSE_CODE_NOT_FOUND);
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
    http_response_code(RESPONSE_CODE_TOO_MANY_REQUESTS);
    echo json_encode(["error" => "Too many failed attempts. Please try again later."]);
    exit();
}

// Validate credentials
$lIsValid = $lSQLQueryHandler->authenticateByClientCredentials($lClientId, $lClientSecret);
if (!$lIsValid) {
    $_SESSION[$lFailedAttemptsKey]++;
    http_response_code(RESPONSE_CODE_UNAUTHORIZED);
    echo json_encode(["error" => "Authentication failed."]);
    exit();
} else {
    // Reset failed attempts on successful login
    $_SESSION[$lFailedAttemptsKey] = 0;
}

// Define JWT claims with audience
$lPayload = [
    'iss' => JWT_BASE_URL,                      // Issuer is your domain
    'aud' => $lAudience,                    // Audience for the token
    'iat' => time(),                        // Issued at
    'nbf' => time(),                        // Not before
    'exp' => time() + JWT_EXPIRATION_TIME,  // Expiration time
    'sub' => $lClientId,                    // Subject is the client ID
    'scope' => 'execute:method',            // Scope of the token
    'jti' => bin2hex(random_bytes(16))      // JWT ID
];

// Encode the JWT token with a specified algorithm
$lJwt = JWT::encode($lPayload, JWT_SECRET_KEY, JWT_EXPECTED_ALGORITHM); // Use a secure algorithm

// Respond with JWT token
http_response_code(RESPONSE_CODE_OK);
echo json_encode([
    'access_token' => $lJwt,
    'token_type' => 'bearer',
    'expires_in' => JWT_EXPIRATION_TIME,
    'timestamp' => date(DATE_TIME_FORMAT)
]);

?>
