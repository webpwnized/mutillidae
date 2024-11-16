<?php

# -------------------------------
# Documentation:
# - Domain: mutillidae.localhost
# - Description: This is a GET request to test connectivity to the API
# - Endpoint: /
# - CORS Headers:
#   - Access-Control-Allow-Origin: * or specific domains
#   - Access-Control-Allow-Methods: GET, OPTIONS
#   - Access-Control-Allow-Headers: Content-Type, Authorization
#   - Access-Control-Max-Age: 600 (10 minutes)
#   - Access-Control-Allow-Credentials: true (if enabled)
# - Expected Response:
#   - Status: 200 OK with JSON message {"message": "Connection succeeded"}
#   - If method not allowed: 405 Method Not Allowed with allowed methods in response header
# -------------------------------

/* Example Request: Make sure to copy the new line characters as well

GET /webservices/rest/ws-test-connectivity.php HTTP/1.1
Host: mutillidae.localhost
Origin: http://mutillidae.localhost
User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/129.0.0.0 Safari/537.36
Content-Type: application/json
Authorization: Bearer <bearer_token>
Accept: application/json

*/

// Include required constants and utility classes
require_once '../../includes/constants.php';
require_once '../../classes/SQLQueryHandler.php';
require_once '../includes/ws-constants.php';

// Initialize SQL query handler with security level 0
$SQLQueryHandler = new SQLQueryHandler(0);

// Get the current security level from database instead of session
$lSecurityLevel = $SQLQueryHandler->getSecurityLevelFromDB();

// Handle the GET request to test connectivity
try {
    // Set CORS headers
    header(CORS_ACCESS_CONTROL_ALLOW_ORIGIN);
    header('Access-Control-Allow-Methods: GET, OPTIONS'); // Allowed methods
    header('Access-Control-Allow-Headers: Content-Type, Authorization'); // Specify allowed headers
    header('Access-Control-Expose-Headers: Authorization'); // Expose headers if needed
    header(CONTENT_TYPE_JSON);

    // Handle preflight requests (OPTIONS)
    if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
        header(CORS_ACCESS_CONTROL_MAX_AGE); // Cache the preflight response for 600 seconds (10 minutes)
        http_response_code(RESPONSE_CODE_NO_CONTENT);
        exit();
    }

    switch ($lSecurityLevel) {
        default:
        case SECURITY_LEVEL_INSECURE:
            $lRequireAuthentication = false;
            break;
        case SECURITY_LEVEL_MEDIUM:
        case 2:
        case 3:
        case 4:
        case SECURITY_LEVEL_SECURE:
            $lRequireAuthentication = true;
            break;
    }

    // Shared: Include the shared JWT token authentication function
    require_once '../includes/ws-authenticate-jwt-token.php';

    // Shared: Authenticate the user if required
    if ($lRequireAuthentication) {
        try {
            $lDecodedToken = authenticateJWTToken(); // Authenticate using the shared function
        } catch (InvalidTokenException $e) {
            http_response_code(RESPONSE_CODE_UNAUTHORIZED);
            header(CONTENT_TYPE_JSON);
            echo json_encode(['error' => 'Unauthorized', 'details' => $e->getMessage()]);
            exit;
        }
    }

    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        // Return a success message with 200 OK status
        http_response_code(RESPONSE_CODE_OK); // OK
        echo json_encode(["code" => RESPONSE_CODE_OK, "status" => "OK", "message" => "Connection succeeded...", 'security-level' => $lSecurityLevel, "timestamp" => date(DATE_TIME_FORMAT)], JSON_PRETTY_PRINT);
    } else {
        // If the request method is not allowed, return 405 status
        http_response_code(RESPONSE_CODE_METHOD_NOT_ALLOWED);
        header('Allow: GET, OPTIONS'); // Inform allowed methods
        echo json_encode(["error" => "Method not allowed. Use GET or OPTIONS request."]);
    }
} catch (Exception $e) {
    // Handle any exceptions with a 500 Internal Server Error response
    http_response_code(RESPONSE_CODE_INTERNAL_SERVER_ERROR);
    echo json_encode([
        "error" => "Unable to process the request",
        "details" => $e->getMessage()
    ]);
}
?>
