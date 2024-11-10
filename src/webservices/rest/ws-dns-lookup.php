<?php
// ws-dns-lookup.php: REST-based Lookup DNS Service with Command Injection for Teaching

require_once '../../includes/constants.php';
require_once '../../classes/SQLQueryHandler.php';
require_once '../../classes/LogHandler.php';
require_once '../../classes/JWT.php'; // Include the JWT handler from correct path

// Define constants for readability and maintainability
define('CONTENT_TYPE_JSON', 'Content-Type: application/json');
define('SECURITY_LEVEL_INSECURE', 0);
define('SECURITY_LEVEL_MEDIUM', 1);
define('SECURITY_LEVEL_SECURE', 5);
define('METHOD_NOT_ALLOWED_CODE', 405);
define('BAD_REQUEST_CODE', 400);
define('UNAUTHORIZED_CODE', 401);
define('SERVER_ERROR_CODE', 500);
define('SUCCESS_CODE', 200);
define('JWT_SECRET_KEY', getenv('JWT_SECRET_KEY') ?: 'snowman');

$SQLQueryHandler = new SQLQueryHandler(SECURITY_LEVEL_INSECURE);
$lSecurityLevel = $SQLQueryHandler->getSecurityLevelFromDB();
$LogHandler = new LogHandler($lSecurityLevel);

class CommandExecutionException extends Exception {}

try {
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

    if ($lRequireAuthentication) {
        $lAuthHeader = $_SERVER['REDIRECT_HTTP_AUTHORIZATION'] ?? $_SERVER['HTTP_AUTHORIZATION'] ?? '';
        $lToken = str_replace('Bearer ', '', $lAuthHeader);

        if (empty($lToken)) {
            http_response_code(UNAUTHORIZED_CODE);
            header(CONTENT_TYPE_JSON);
            echo json_encode(['error' => 'Authentication token required.']);
            exit;
        }

        try {
            $lDecodedToken = JWT::decode($lToken, JWT_SECRET_KEY, ['HS256']);
        } catch (Exception $e) {
            http_response_code(UNAUTHORIZED_CODE);
            header(CONTENT_TYPE_JSON);
            echo json_encode(['error' => 'Invalid or expired token.', 'details' => $e->getMessage()]);
            exit;
        }
    }

    if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
        http_response_code(METHOD_NOT_ALLOWED_CODE);
        header(CONTENT_TYPE_JSON);
        echo json_encode(['error' => 'Method Not Allowed. Use GET for this endpoint.']);
        exit;
    }

    $lHostname = isset($_GET['hostname']) ? trim($_GET['hostname']) : '';
    if (empty($lHostname)) {
        http_response_code(BAD_REQUEST_CODE);
        header(CONTENT_TYPE_JSON);
        echo json_encode(['error' => 'Hostname parameter is required.']);
        exit;
    }

    if ($lProtectAgainstCommandInjection) {
        $lHostnameValidated = preg_match(IPV4_REGEX_PATTERN, $lHostname) ||
                              preg_match(DOMAIN_NAME_REGEX_PATTERN, $lHostname) ||
                              preg_match(IPV6_REGEX_PATTERN, $lHostname);
        if (!$lHostnameValidated) {
            http_response_code(BAD_REQUEST_CODE);
            header(CONTENT_TYPE_JSON);
            echo json_encode(['error' => 'Invalid hostname format.']);
            exit;
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

    http_response_code(SUCCESS_CODE);
    header(CONTENT_TYPE_JSON);
    echo json_encode(['hostname' => $lHostname, 'command' => $lCommand, 'security-level' => $lSecurityLevel, 'result' => $lOutput]);

} catch (Exception $e) {
    http_response_code(SERVER_ERROR_CODE);
    header(CONTENT_TYPE_JSON);
    echo json_encode(['error' => 'An unexpected error occurred.', 'details' => $e->getMessage()]);
}
?>
