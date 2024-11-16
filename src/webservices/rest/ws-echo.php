<?php

// Include required constants and utility classes
require_once '../../includes/constants.php';
require_once '../../classes/SQLQueryHandler.php';
require_once '../../classes/LogHandler.php';
require_once '../includes/ws-constants.php';

// Initialize SQL query handler with security level 0
$SQLQueryHandler = new SQLQueryHandler(SECURITY_LEVEL_INSECURE);

// Get the current security level from database instead of session
$lSecurityLevel = $SQLQueryHandler->getSecurityLevelFromDB();

$LogHandler = new LogHandler($lSecurityLevel);

// Define a dedicated exception for command execution failures
class CommandExecutionException extends Exception {}

try {
    // Set CORS headers
    header(CORS_ACCESS_CONTROL_ALLOW_ORIGIN);
    header('Access-Control-Allow-Methods: POST, OPTIONS'); // Allowed methods
    header('Access-Control-Allow-Headers: Content-Type, Authorization'); // Specify allowed headers
    header('Access-Control-Expose-Headers: Authorization'); // Expose headers if needed
    header(CONTENT_TYPE_JSON);

    // Handle preflight requests (OPTIONS)
    if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
        header(CORS_ACCESS_CONTROL_MAX_AGE); // Cache the preflight response for 600 seconds (10 minutes)
        http_response_code(RESPONSE_CODE_NO_CONTENT); // No Content
        exit();
    }

    switch ($lSecurityLevel) {
        default:
        case SECURITY_LEVEL_INSECURE:
            $lProtectAgainstCommandInjection = false;
            $lRequireAuthentication = false;
            break;
        case SECURITY_LEVEL_MEDIUM:
            $lProtectAgainstCommandInjection = false;
            $lRequireAuthentication = true;
            break;
        case 2:
        case 3:
        case 4:
        case SECURITY_LEVEL_SECURE:
            $lProtectAgainstCommandInjection = true;
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

    // Allow only POST requests for this endpoint
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(RESPONSE_CODE_METHOD_NOT_ALLOWED); // Method Not Allowed
        header(CONTENT_TYPE_JSON);
        echo json_encode(['error' => 'Method Not Allowed. Use POST for this endpoint.']);
        exit;
    }

    // Retrieve and sanitize the 'message' parameter from the POST request
    $lMessage = isset($_POST['message']) ? trim($_POST['message']) : '';

    if (empty($lMessage)) {
        http_response_code(RESPONSE_CODE_BAD_REQUEST); // Bad Request
        header(CONTENT_TYPE_JSON);
        echo json_encode(['error' => 'Message parameter is required.']);
        exit;
    }

    if (!$lProtectAgainstCommandInjection) {
        $lCommand = "echo " . $lMessage; // Vulnerable: Direct input usage
    } else {
        // Secure version: Use escapeshellcmd() and escapeshellarg() to sanitize input
        $lCommand = escapeshellcmd("echo " . escapeshellarg($lMessage));
    }

    // Execute the echo command and return the result
    $lOutput = shell_exec($lCommand);
    $LogHandler->writeToLog("Command executed from web service ws-echo.php: " . $lCommand);

    if ($lOutput === null) {
        throw new CommandExecutionException("Command execution failed.");
    }

    // Return the output as JSON
    http_response_code(RESPONSE_CODE_OK); // OK
    header(CONTENT_TYPE_JSON); // Set response format to JSON
    echo json_encode(['message' => $lMessage, 'command' => $lCommand, 'security-level' => $lSecurityLevel, 'timestamp' => date(DATE_TIME_FORMAT), 'result' => $lOutput], JSON_PRETTY_PRINT);

} catch (Exception $e) {
    // Handle errors during configuration setup
    http_response_code(RESPONSE_CODE_INTERNAL_SERVER_ERROR); // Internal Server Error
    header(CONTENT_TYPE_JSON); // Set response format to JSON
    echo json_encode(['error' => 'An unexpected error occurred.', 'details' => $e->getMessage()]);
}
?>
