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
    'literal',
    'Authenticates a client and returns a JWT token if successful.'
);

// Define the login function
function login($pClientID, $pClientSecret, $pAudience) {
    try {
        // Initialize the SQL query handler
        $SQLQueryHandler = new SQLQueryHandler(SECURITY_LEVEL_INSECURE);
        $lSecurityLevel = $SQLQueryHandler->getSecurityLevelFromDB();
        $SQLQueryHandler->setSecurityLevel($lSecurityLevel);

        // Validate Inputs
        if (!isset($pClientID) || !preg_match('/^[a-f0-9]{32}$/', $pClientID)) {
            return new soap_fault("ClientError", "", "Invalid Client ID format.");
        }

        if (!isset($pClientSecret) || !preg_match('/^[a-f0-9]{64}$/', $pClientSecret)) {
            return new soap_fault("ClientError", "", "Invalid Client Secret format.");
        }

        if (!isset($pAudience) || !filter_var($pAudience, FILTER_VALIDATE_URL)) {
            return new soap_fault("ClientError", "", "Invalid Audience format.");
        }

        // Check if the requested audience is valid
        if (!in_array($pAudience, JWT_VALID_AUDIENCES)) {
            return new soap_fault("ClientError", "", "Invalid audience specified.");
        }

        // Rate limiting mechanism
        session_start();
        $lFailedAttemptsKey = "failed_attempts_" . $_SERVER['REMOTE_ADDR'];
        if (!isset($_SESSION[$lFailedAttemptsKey])) {
            $_SESSION[$lFailedAttemptsKey] = 0;
        }

        // Lockout mechanism after MAX_FAILED_ATTEMPTS failed attempts
        if ($_SESSION[$lFailedAttemptsKey] >= MAX_FAILED_ATTEMPTS) {
            return new soap_fault("ClientError", "", "Too many failed attempts. Please try again later.");
        }

        // Validate credentials
        $lIsValid = $SQLQueryHandler->authenticateByClientCredentials($pClientID, $pClientSecret);
        if (!$lIsValid) {
            $_SESSION[$lFailedAttemptsKey]++;
            return new soap_fault("AuthenticationError", "", "Authentication failed.");
        } else {
            // Reset failed attempts on successful login
            $_SESSION[$lFailedAttemptsKey] = 0;
        }

        // Define JWT claims with audience
        $lPayload = [
            'iss' => JWT_BASE_URL,
            'aud' => $pAudience,
            'iat' => time(),
            'nbf' => time(),
            'exp' => time() + JWT_EXPIRATION_TIME,
            'sub' => $pClientID,
            'scope' => 'execute:method',
            'jti' => bin2hex(random_bytes(16))
        ];

        // Encode the JWT token with a specified algorithm
        $lJwt = JWT::encode($lPayload, JWT_SECRET_KEY, JWT_EXPECTED_ALGORITHM);

        // Construct a SOAP-compliant XML response without encoding
        $responseXML = "<response>
            <access_token>{$lJwt}</access_token>
            <token_type>bearer</token_type>
            <expires_in>" . JWT_EXPIRATION_TIME . "</expires_in>
            <timestamp>" . date(DATE_TIME_FORMAT) . "</timestamp>
        </response>";

        // Return as a soapval with type 'xsd:any' to prevent automatic escaping
        return new soapval('return', 'xsd:any', $responseXML);

    } catch (Exception $e) {
        // Ensure the exception message is returned as a SOAP fault
        return new soap_fault("ServerError", "", $e->getMessage());
    }
}

// Process the SOAP requests
$HTTP_RAW_POST_DATA = isset($HTTP_RAW_POST_DATA) ? $HTTP_RAW_POST_DATA : file_get_contents("php://input");
$lSOAPWebService->service($HTTP_RAW_POST_DATA);
?>
