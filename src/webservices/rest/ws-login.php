<?php

require_once '../../includes/constants.php';
require_once '../../classes/JWT.php';
require_once '../../classes/SQLQueryHandler.php';

// Initialize SQLQueryHandler
$lSQLQueryHandler = new SQLQueryHandler(0);

// Get the origin of the request
$lOrigin = isset($_SERVER['HTTP_ORIGIN']) ? $_SERVER['HTTP_ORIGIN'] : 'https://mutillidae.localhost';

// Get the current domain dynamically
$lCurrentDomain = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : 'mutillidae.localhost';
$lScheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
$lBaseUrl = "{$lScheme}://{$lCurrentDomain}";

// Set CORS headers
header("Access-Control-Allow-Origin: $lOrigin");
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
header('Content-Type: application/json');

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    header('Access-Control-Allow-Origin: ' . $lOrigin);
    header('Access-Control-Max-Age: 600');
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

if (!$lClientId) {
    http_response_code(400);
    echo json_encode(["error" => "Client ID is missing."]);
    exit();
}

if (!$lClientSecret) {
    http_response_code(400);
    echo json_encode(["error" => "Client secret is missing."]);
    exit();
}

if (!$lAudience) {
    http_response_code(400);
    echo json_encode(["error" => "Audience is missing."]);
    exit();
}

// Validate credentials
$lIsValid = $lSQLQueryHandler->authenticateByClientCredentials($lClientId, $lClientSecret);
if (!$lIsValid) {
    http_response_code(401);
    echo json_encode(["error" => "Invalid client credentials."]);
    exit();
}

// Define a list of valid audiences based on known endpoints
$lValidAudiences = [
    "$lBaseUrl/rest/ws-cors-echo.php",
    "$lBaseUrl/rest/ws-dns-lookup.php",
    "$lBaseUrl/rest/ws-echo.php",
    "$lBaseUrl/rest/ws-prototype-login.php",
    "$lBaseUrl/rest/ws-test-connectivity.php",
    "$lBaseUrl/rest/ws-user-account.php",
    "$lBaseUrl/soap/ws-dns-lookup.php",
    "$lBaseUrl/soap/ws-echo.php",
    "$lBaseUrl/soap/ws-test-connectivity.php",
    "$lBaseUrl/soap/ws-user-account.php"
];

// Check if the requested audience is valid
if (!in_array($lAudience, $lValidAudiences)) {
    http_response_code(400);
    echo json_encode(["error" => "Invalid audience specified."]);
    exit();
}

// Define JWT claims with audience
$lPayload = [
    'iss' => $lBaseUrl,                        // Issuer is your domain
    'aud' => $lAudience,                       // Include audience in token
    'iat' => time(),
    'nbf' => time(),
    'exp' => time() + 3600,
    'sub' => $lClientId,
    'jti' => bin2hex(random_bytes(16))
];

// Encode the JWT token
$lSecretKey = getenv('JWT_SECRET_KEY') ?: 'snowman';
$lJwt = JWT::encode($lPayload, $lSecretKey);

// Respond with JWT token
http_response_code(200);
echo json_encode([
    'access_token' => $lJwt,
    'token_type' => 'bearer',
    'expires_in' => 3600
]);

?>
