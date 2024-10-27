<?php
// ws-dns-lookup.php: REST-based Lookup DNS Service with Command Injection for Teaching

// Start session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Initialize security level if not already set
if (!isset($_SESSION["security-level"])) {
    $_SESSION["security-level"] = 0;
}

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

    // Allow only GET requests for this endpoint
    if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
        http_response_code(405); // Method Not Allowed
        header($lContentTypeJSON);
        echo json_encode(['error' => 'Method Not Allowed. Use GET for this endpoint.']);
        exit;
    }

    // Retrieve and sanitize the 'hostname' parameter from the GET request
    $lHostname = isset($_GET['hostname']) ? trim($_GET['hostname']) : '';

    if (empty($lHostname)) {
        http_response_code(400); // Bad Request
        header($lContentTypeJSON);
        echo json_encode(['error' => 'Hostname parameter is required.']);
        exit;
    }

    // Validate the target host to protect against command injection, if security is enabled
    if ($lProtectAgainstCommandInjection) {
        $lHostnameValidated = preg_match(IPV4_REGEX_PATTERN, $lHostname) ||
                                preg_match(DOMAIN_NAME_REGEX_PATTERN, $lHostname) ||
                                preg_match(IPV6_REGEX_PATTERN, $lHostname);
        if (!$lHostnameValidated) {
            http_response_code(400); // Bad Request
            header($lContentTypeJSON);
            echo json_encode(['error' => 'Invalid hostname format.']);
            exit;
        }
    }

    if (!$lProtectAgainstCommandInjection) {
        $lCommand = "nslookup " . $lHostname; // Vulnerable: Direct input usage
    } else {
        // Secure version: Use escapeshellcmd() and escapeshellarg() to sanitize input
        $lCommand = escapeshellcmd("nslookup " . escapeshellarg($lHostname));
    }

    // Execute the nslookup command and return the result
    $lOutput = shell_exec($lCommand);
    $LogHandler->writeToLog("Command executed from web service ws-dns-lookup.php: " . $lCommand);

    if ($lOutput === null) {
        throw new CommandExecutionException("Command execution failed.");
    }

    // Return the output as JSON
    http_response_code(200);
    header($lContentTypeJSON); // Set response format to JSON
    echo json_encode(['hostname' => $lHostname, 'command' => $lCommand, 'security-level' => $lSecurityLevel, 'result' => $lOutput]);

} catch (Exception $e) {
    // Handle errors during configuration setup
    http_response_code(500);
    header($lContentTypeJSON); // Set response format to JSON
    echo json_encode(['error' => 'An unexpected error occurred.', 'details' => $e->getMessage()]);
}
?>
