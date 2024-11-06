<?php

// Include required constants and utility classes
require_once '../../includes/constants.php';
require_once '../../classes/SQLQueryHandler.php';
require_once '../../classes/LogHandler.php';

// Initialize SQL query handler with security level 0
$SQLQueryHandler = new SQLQueryHandler(0);

// Get the current security level from database instead of session
$lSecurityLevel = $SQLQueryHandler->getSecurityLevelFromDB();

$LogHandler = new LogHandler($lSecurityLevel);

// Define a dedicated exception for command execution failures
class CommandExecutionException extends Exception {}

try {
    $lContentTypeJSON = 'Content-Type: application/json';

    // Determine security level and protection settings
    switch ($lSecurityLevel) {
        default: // Insecure
        case "0": // Insecure
        case "1": // Insecure
            $lProtectAgainstCommandInjection = false;
        break;
        case "2": // Moderate security
        case "3": // More secure
        case "4": // Secure
        case "5": // Fairly secure
            $lProtectAgainstCommandInjection = true;
        break;
    }

    // Allow only POST requests for this endpoint
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405); // Method Not Allowed
        header($lContentTypeJSON);
        echo json_encode(['error' => 'Method Not Allowed. Use POST for this endpoint.']);
        exit;
    }

    // Retrieve and sanitize the 'message' parameter from the POST request
    $lMessage = isset($_POST['message']) ? trim($_POST['message']) : '';

    if (empty($lMessage)) {
        http_response_code(400); // Bad Request
        header($lContentTypeJSON);
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
    http_response_code(200);
    header($lContentTypeJSON); // Set response format to JSON
    echo json_encode(['message' => $lMessage, 'command' => $lCommand, 'security-level' => $lSecurityLevel, 'result' => $lOutput]);

} catch (Exception $e) {
    // Handle errors during configuration setup
    http_response_code(500);
    header($lContentTypeJSON); // Set response format to JSON
    echo json_encode(['error' => 'An unexpected error occurred.', 'details' => $e->getMessage()]);
}
?>
