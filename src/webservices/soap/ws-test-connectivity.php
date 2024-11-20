<?php

class TestConnectionException extends Exception {
    public $faultcode;
    public $detail;

    public function __construct($message, $faultcode = 'Server', $detail = null) {
        parent::__construct($message);
        $this->faultcode = $faultcode;
        $this->detail = $detail;
    }
}

// Include the nusoap library and required constants
require_once './lib/nusoap.php';
require_once '../includes/ws-constants.php';
require_once '../includes/ws-authenticate-jwt-token.php';

$lServerName = $_SERVER['SERVER_NAME'];

// Construct the full URL to the documentation
$lDocumentationURL = "http://{$lServerName}/webservices/soap/docs/soap-services.html";

// Create the SOAP server instance
$lSOAPWebService = new soap_server();

// Initialize WSDL support for the SOAP service
$lSOAPWebService->configureWSDL('connectivitywsdl', 'urn:connectivitywsdl');

// Register the "testConnectivity" method
$lSOAPWebService->register(
    'testConnectivity',                // Method name
    array(),                           // No input parameters
    array('return' => 'tns:TestConnectivityResponse'),   // Output parameter defined as a complex type
    'urn:connectivitywsdl',            // Namespace
    'urn:connectivitywsdl#testConnectivity', // SOAP action
    'rpc',                             // Style
    'encoded',                         // Use
    "Returns a simple message to confirm connectivity. For detailed documentation, visit: {$lDocumentationURL}"
);

// Define a complex type for the response
$lSOAPWebService->wsdl->addComplexType(
    'TestConnectivityResponse',
    'complexType',
    'struct',
    'all',
    '',
    array(
        'successMessage' => array('name' => 'successMessage', 'type' => 'xsd:string'),
        'securityLevel' => array('name' => 'securityLevel', 'type' => 'xsd:string'),
        'timestamp' => array('name' => 'timestamp', 'type' => 'xsd:string')
    )
);

/**
 * Function: authenticateRequest
 * Handles request authentication and CORS headers.
 * 
 * @param int $lSecurityLevel The security level.
 * @throws InvalidTokenException If the authentication fails.
 */
function authenticateRequest($lSecurityLevel) {

    // Set CORS headers
    header(CORS_ACCESS_CONTROL_ALLOW_ORIGIN);
    header('Access-Control-Allow-Methods: POST, OPTIONS'); // Allowed methods
    header('Access-Control-Allow-Headers: Content-Type, Authorization'); // Specify allowed headers
    header('Access-Control-Expose-Headers: Authorization'); // Expose headers if needed
    header(CONTENT_TYPE_XML); // Set content type as XML

    // Handle preflight requests (OPTIONS)
    if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
        header(CORS_ACCESS_CONTROL_MAX_AGE); // Cache the preflight response for 600 seconds (10 minutes)
        http_response_code(RESPONSE_CODE_NO_CONTENT); // No Content
        exit();
    }

    // Allow only POST requests
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(RESPONSE_CODE_METHOD_NOT_ALLOWED);
        header(CONTENT_TYPE_XML);
        echo ERROR_MESSAGE_METHOD_NOT_ALLOWED;
        exit();
    }

    // Authenticate the user if required
    if ($lSecurityLevel >= SECURITY_LEVEL_MEDIUM) {
        try {
            $lDecodedToken = authenticateJWTToken(); // Authenticate using the shared function
        } catch (InvalidTokenException $e) {
            http_response_code(RESPONSE_CODE_UNAUTHORIZED);
            header(CONTENT_TYPE_XML);
            echo ERROR_MESSAGE_UNAUTHORIZED_PREFIX . 'Unauthorized: ' . htmlspecialchars($e->getMessage()) . ERROR_MESSAGE_UNAUTHORIZED_SUFFIX;
            exit();
        }
    }
}

/**
 * Define the "testConnectivity" method
 * 
 * @return array An associative array containing a success message, security level, and timestamp.
 * @throws TestConnectionException If there is an error executing the method.
 */
function testConnectivity() {
    try {
        // Include required constants and utility classes
        require_once '../../includes/constants.php';
        require_once '../../classes/SQLQueryHandler.php';

        $SQLQueryHandler = new SQLQueryHandler(SECURITY_LEVEL_INSECURE);
        $lSecurityLevel = $SQLQueryHandler->getSecurityLevelFromDB();

        // Authenticate the request using the shared function
        authenticateRequest($lSecurityLevel);

        // Get the current timestamp
        $lTimestamp = date(DATE_TIME_FORMAT);

        // Return a structured response as an associative array
        return array(
            'successMessage' => 'Connection successful...',
            'securityLevel' => $lSecurityLevel,
            'timestamp' => $lTimestamp
        );

    } catch (Exception $e) {
        // Throw a SOAP-compliant custom exception
        throw new TestConnectionException(
            "Error executing method testConnectivity in webservice ws-connectivity.php: " . $e->getMessage(),
            'Server',
            array('method' => 'testConnectivity', 'details' => $e->getMessage())
        );
    }
}

// Handle the SOAP request with error handling
try {
    // Process the incoming SOAP request
    $lSOAPWebService->service(file_get_contents("php://input"));
} catch (TestConnectionException $e) {
    // Send a detailed SOAP fault response back to the client
    $detail = $e->detail ? json_encode($e->detail) : null;
    $lSOAPWebService->fault($e->faultcode, $e->getMessage(), '', $detail);
} catch (Exception $e) {
    // Send a generic SOAP fault response for unexpected errors
    $lSOAPWebService->fault('Server', "Unexpected SOAP Service Error: " . $e->getMessage());
}
?>
