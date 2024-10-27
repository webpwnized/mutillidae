<?php

require_once '../../includes/constants.php';
require_once '../../classes/JWT.php';
require_once '../../classes/SQLQueryHandler.php';

// Initialize SQL query handler with security level 0
$SQLQueryHandler = new SQLQueryHandler(0);

$lOrigin = isset($_SERVER['HTTP_ORIGIN']) ? $_SERVER['HTTP_ORIGIN'] : '*';

// Set CORS headers
header('Access-Control-Allow-Methods: GET, OPTIONS'); // Allowed methods
header('Access-Control-Allow-Headers: Content-Type, Authorization'); // Specify allowed headers
header('Access-Control-Expose-Headers: Authorization'); // Expose headers if needed
header('Access-Control-Allow-Credentials: true'); // Allow credentials (if required)
header('Content-Type: application/json');

// Handle preflight requests (OPTIONS)
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    header('Access-Control-Max-Age: 600'); // Cache the preflight response for 600 seconds (10 minutes)
    http_response_code(204); // No Content
    exit();
}

// Ensure request is POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405); // Method Not Allowed
    echo json_encode(["error" => "Method not allowed. Use POST."]);
    exit();
}

// Extract API key and secret from the request body
$data = json_decode(file_get_contents('php://input'), true);
$clientId = isset($data['client_id']) ? $data['client_id'] : null;
$clientSecret = isset($data['client_secret']) ? $data['client_secret'] : null;

if (!$clientId || !$clientSecret) {
    http_response_code(400); // Bad Request
    echo json_encode(["error" => "Client ID or secret missing."]);
    exit();
}

// Validate the client credentials using SQLQueryHandler
$queryHandler = new SQLQueryHandler();
$isValid = $queryHandler->validateClientCredentials($clientId, $clientSecret); // Assume this method exists

if (!$isValid) {
    http_response_code(401); // Unauthorized
    echo json_encode(["error" => "Invalid client credentials."]);
    exit();
}

// Generate JWT token (valid for 1 hour)
$payload = [
    'iss' => 'your-issuer',  // Issuer name
    'iat' => time(),          // Issued at
    'exp' => time() + 3600,   // Expires in 1 hour
    'sub' => $clientId        // Subject (client ID)
];

$jwt = JWT::encode($payload, 'your-secret-key'); // Use a strong secret key

// Return the access token
http_response_code(200); // OK
echo json_encode([
    'access_token' => $jwt,
    'token_type' => 'bearer',
    'expires_in' => 3600
]);
?>
