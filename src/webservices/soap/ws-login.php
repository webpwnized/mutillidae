<?php
// Include the nusoap library
require_once './lib/nusoap.php';
require_once '../../classes/SQLQueryHandler.php';
require_once '../../classes/JWT.php';
require_once '../includes/ws-constants.php';

$lServerName = $_SERVER['SERVER_NAME'];

// Construct the full URL to the documentation
$lDocumentationURL = "http://{$lServerName}/webservices/soap/docs/soap-services.html";

// Create the SOAP server instance
$lSOAPWebService = new soap_server();

// Configure WSDL for the SOAP service
$lSOAPWebService->configureWSDL('ws-login', 'urn:ws-login');

// Register the "login" method
$lSOAPWebService->register(
    'login',
    array(
        'client_id' => 'xsd:string',        // pragma: allowlist secret
        'client_secret' => 'xsd:string',    // pragma: allowlist secret
        'audience' => 'xsd:string'
    ),
    array('return' => 'xsd:string'),
    'urn:ws-login',
    'urn:ws-login#login',
    'rpc',
    'encoded',
    'Authenticates a client and returns a JWT token if successful.'
);

// Define the login function
function login($pClientID, $pClientSecret, $pAudience) {
    try {
        // Initialize the SQL query handler
        $SQLQueryHandler = new SQLQueryHandler(0);

        $lSecurityLevel = $SQLQueryHandler->getSecurityLevelFromDB();

        $SQLQueryHandler->setSecurityLevel($lSecurityLevel);

        // Validate Inputs
        if (!isset($lClientId) || !preg_match('/^[a-f0-9]{32}$/', $lClientId)) {
            http_response_code(BAD_REQUEST_CODE);
            echo json_encode(["error" => "Invalid Client ID format."]);
            exit();
        }

        if (!isset($lClientSecret) || !preg_match('/^[a-f0-9]{64}$/', $lClientSecret)) {
            http_response_code(BAD_REQUEST_CODE);
            echo json_encode(["error" => "Invalid Client Secret format."]);
            exit();
        }

        if (!isset($lAudience) || !filter_var($lAudience, FILTER_VALIDATE_URL)) {
            http_response_code(BAD_REQUEST_CODE);
            echo json_encode(["error" => "Invalid Audience format."]);
            exit();
        }

        // Check if the requested audience is valid
        if (!in_array($lAudience, VALID_AUDIENCES)) {
            http_response_code(NOT_FOUND_CODE);
            echo json_encode(["error" => "Invalid audience specified."]);
            exit();
        }

        // Rate limiting mechanism
        session_start();
        $lFailedAttemptsKey = "failed_attempts_" . $_SERVER['REMOTE_ADDR'];
        if (!isset($_SESSION[$lFailedAttemptsKey])) {
            $_SESSION[$lFailedAttemptsKey] = 0;
        }

        // Lockout mechanism after MAX_FAILED_ATTEMPTS failed attempts
        if ($_SESSION[$lFailedAttemptsKey] >= MAX_FAILED_ATTEMPTS) {
            http_response_code(TOO_MANY_REQUESTS_CODE);
            echo json_encode(["error" => "Too many failed attempts. Please try again later."]);
            exit();
        }

        // Validate credentials
        $lIsValid = $lSQLQueryHandler->authenticateByClientCredentials($lClientId, $lClientSecret);
        if (!$lIsValid) {
            $_SESSION[$lFailedAttemptsKey]++;
            http_response_code(UNAUTHORIZED_CODE);
            echo json_encode(["error" => "Authentication failed."]);
            exit();
        } else {
            // Reset failed attempts on successful login
            $_SESSION[$lFailedAttemptsKey] = 0;
        }

        // Define JWT claims with audience
        $lPayload = [
            'iss' => BASE_URL,                      // Issuer is your domain
            'aud' => $lAudience,                    // Audience for the token
            'iat' => time(),                        // Issued at
            'nbf' => time(),                        // Not before
            'exp' => time() + JWT_EXPIRATION_TIME,  // Expiration time
            'sub' => $lClientId,                    // Subject is the client ID
            'scope' => 'execute:method',            // Scope of the token
            'jti' => bin2hex(random_bytes(16))      // JWT ID
        ];

        // Encode the JWT token with a specified algorithm
        $lJwt = JWT::encode($lPayload, JWT_SECRET_KEY, EXPECTED_ALGORITHM); // Use a secure algorithm

        // Respond with JWT token
        http_response_code(SUCCESS_CODE);
        echo json_encode([
            'access_token' => $lJwt,
            'token_type' => 'bearer',
            'expires_in' => JWT_EXPIRATION_TIME,
            'timestamp' => date(DATE_TIME_FORMAT)
        ]);

    } catch (Exception $e) {
        return json_encode(["status" => "error", "message" => $e->getMessage()]);
    }
}

// Process the SOAP requests
$HTTP_RAW_POST_DATA = isset($HTTP_RAW_POST_DATA) ? $HTTP_RAW_POST_DATA : file_get_contents("php://input");
$lSOAPWebService->service($HTTP_RAW_POST_DATA);
?>
