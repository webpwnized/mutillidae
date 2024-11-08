<?php

require_once '../../includes/constants.php';
require_once '../../classes/JWT.php';
require_once '../../classes/SQLQueryHandler.php';

$SQLQueryHandler = new SQLQueryHandler(0);

$lOrigin = isset($_SERVER['HTTP_ORIGIN']) ? $_SERVER['HTTP_ORIGIN'] : 'https://trusted-origin.com';

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
$data = json_decode(file_get_contents('php://input'), true);
$clientId = $data['client_id'] ?? null;
$clientSecret = $data['client_secret'] ?? null;
$audience = $data['audience'] ?? null;

if (!$clientId || !$clientSecret || !$audience) {
    http_response_code(400);
    echo json_encode(["error" => "Client ID, secret, or audience missing."]);
    exit();
}

// Validate credentials
$isValid = $SQLQueryHandler->authenticateByClientCredentials($clientId, $clientSecret);
if (!$isValid) {
    http_response_code(401);
    echo json_encode(["error" => "Invalid client credentials."]);
    exit();
}

// Define a list of valid audiences based on known endpoints
$validAudiences = [
    "https://your-domain.com/rest/ws-cors-echo.php",
    "https://your-domain.com/rest/ws-dns-lookup.php",
    "https://your-domain.com/rest/ws-echo.php",
    "https://your-domain.com/rest/ws-prototype-login.php",
    "https://your-domain.com/rest/ws-test-connectivity.php",
    "https://your-domain.com/rest/ws-user-account.php",
    "https://your-domain.com/soap/ws-dns-lookup.php",
    "https://your-domain.com/soap/ws-echo.php",
    "https://your-domain.com/soap/ws-test-connectivity.php",
    "https://your-domain.com/soap/ws-user-account.php"
];

// Check if the requested audience is valid
if (!in_array($audience, $validAudiences)) {
    http_response_code(400);
    echo json_encode(["error" => "Invalid audience specified."]);
    exit();
}

// Define JWT claims with audience
$payload = [
    'iss' => 'https://your-domain.com',       // Issuer is your domain
    'aud' => $audience,                       // Include audience in token
    'iat' => time(),
    'nbf' => time(),
    'exp' => time() + 3600,
    'sub' => $clientId,
    'jti' => bin2hex(random_bytes(16))
];

$jwt = JWT::encode($payload, getenv('JWT_SECRET_KEY') ?: 'default-secret-key');

// Respond with JWT token
http_response_code(200);
echo json_encode([
    'access_token' => $jwt,
    'token_type' => 'bearer',
    'expires_in' => 3600
]);

?>
