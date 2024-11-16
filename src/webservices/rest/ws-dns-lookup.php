<?php
// ws-dns-lookup.php: REST-based Lookup DNS Service with Command Injection for Teaching

require_once '../../includes/constants.php';
require_once '../../classes/SQLQueryHandler.php';
require_once '../../classes/LogHandler.php';
require_once '../includes/ws-constants.php';

$SQLQueryHandler = new SQLQueryHandler(SECURITY_LEVEL_INSECURE);
$lSecurityLevel = $SQLQueryHandler->getSecurityLevelFromDB();
$LogHandler = new LogHandler($lSecurityLevel);

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

    // Allow only POST requests
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(RESPONSE_CODE_METHOD_NOT_ALLOWED);
        echo json_encode(['error' => 'Method Not Allowed. Use POST for this endpoint.']);
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
            echo json_encode(['error' => 'Unauthorized', 'details' => $e->getMessage()]);
            exit();
        }
    }

    // Parse JSON body
    $inputData = json_decode(file_get_contents('php://input'), true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        http_response_code(RESPONSE_CODE_BAD_REQUEST);
        echo json_encode(['error' => 'Invalid JSON input.']);
        exit();
    }

    $lHostname = $inputData['hostname'] ?? '';
    if (empty($lHostname)) {
        http_response_code(RESPONSE_CODE_BAD_REQUEST);
        echo json_encode(['error' => 'Hostname parameter is required.']);
        exit();
    }

    if ($lProtectAgainstCommandInjection) {
        $lHostnameValidated = preg_match(IPV4_REGEX_PATTERN, $lHostname) ||
                              preg_match(DOMAIN_NAME_REGEX_PATTERN, $lHostname) ||
                              preg_match(IPV6_REGEX_PATTERN, $lHostname);
        if (!$lHostnameValidated) {
            http_response_code(RESPONSE_CODE_BAD_REQUEST);
            echo json_encode(['error' => 'Invalid hostname format.']);
            exit();
        }
    }

    $lCommand = $lProtectAgainstCommandInjection
                ? escapeshellcmd("nslookup " . escapeshellarg($lHostname))
                : "nslookup " . $lHostname;

    $lOutput = shell_exec($lCommand);
    $LogHandler->writeToLog("Command executed from web service ws-dns-lookup.php: " . $lCommand);

    if ($lOutput === null) {
        throw new CommandExecutionException("Command execution failed.");
    }

    http_response_code(RESPONSE_CODE_OK);
    echo json_encode(['hostname' => $lHostname, 'command' => $lCommand, 'security-level' => $lSecurityLevel, 'timestamp' => date(DATE_TIME_FORMAT), 'result' => $lOutput], JSON_PRETTY_PRINT);

} catch (Exception $e) {
    http_response_code(RESPONSE_CODE_INTERNAL_SERVER_ERROR);
    echo json_encode(['error' => 'An unexpected error occurred.', 'details' => $e->getMessage()]);
}
?>
