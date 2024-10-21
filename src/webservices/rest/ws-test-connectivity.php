<?php
// Get the origin of the request
$origin = isset($_SERVER['HTTP_ORIGIN']) ? $_SERVER['HTTP_ORIGIN'] : null;

// Set the allowed origins (your trusted domains)
$allowedOrigins = [
    'http://localhost',
    'http://mutillidae.localhost',
    'http://www.mutillidae.localhost',
    'http://mutillidae',
    'http://dragonfly.localhost',
    'http://www.dragonfly.localhost',
    'http://127.0.0.1',
    'http://cors.mutillidae.localhost',
    'http://webservice.mutillidae.localhost',
    'http://api.mutillidae.localhost'
];

// Check if the request origin is allowed
if ($origin && in_array($origin, $allowedOrigins)) {
    header("Access-Control-Allow-Origin: $origin"); // Reflect allowed origin
    header('Access-Control-Allow-Methods: GET, OPTIONS'); // Allowed methods
    header('Access-Control-Allow-Headers: Content-Type'); // Allowed headers
} else {
    // If origin is not allowed, return 403 Forbidden
    http_response_code(403); // Forbidden
    echo json_encode([
        "error" => "Forbidden: The origin $origin is not allowed to access this resource."
    ]);
    exit();
}

// Set the content type to JSON
header('Content-Type: application/json');

// Handle preflight requests (OPTIONS)
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204); // No Content
    exit();
}

// Handle the GET request to test connectivity
try {
    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        // Return a success message with 200 OK status
        http_response_code(200); // OK
        echo json_encode(["message" => "Connection succeeded"]);
    } else {
        // If the request method is not allowed, return 405 status
        http_response_code(405); // Method Not Allowed
        header('Allow: GET, OPTIONS'); // Inform allowed methods
        echo json_encode(["error" => "Method not allowed. Use GET or OPTIONS request."]);
    }
} catch (Exception $e) {
    // Handle any exceptions with a 500 Internal Server Error response
    http_response_code(500); // Internal Server Error
    echo json_encode([
        "error" => "Unable to process the request",
        "details" => $e->getMessage()
    ]);
}
?>
