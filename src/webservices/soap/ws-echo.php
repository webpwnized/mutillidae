<?php

class MethodExecutionException extends Exception {}
class CommandExecutionException extends Exception {}

// Include the nusoap library and required constants
require_once './lib/nusoap.php';
require_once '../includes/ws-constants.php';
require_once '../includes/ws-authenticate-jwt-token.php';

$lServerName = $_SERVER['SERVER_NAME'];

// Construct the full URL to the documentation
$lDocumentationURL = "http://{$lServerName}/webservices/soap/docs/soap-services.html";

// Create the SOAP server instance
$lSOAPWebService = new soap_server();

// Initialize WSDL (Web Service Definition Language) support
$lSOAPWebService->configureWSDL('echowsdl', 'urn:echowsdl');

// Register the "echoMessage" method to expose it as a SOAP function
$lSOAPWebService->register(
    'echoMessage',                     // Method name
    array('message' => 'xsd:string'),  // Input parameter
    array('return' => 'tns:EchoMessageResponse'),   // Output parameter defined as a complex type
    'urn:echowsdl',                    // Namespace
    'urn:echowsdl#echoMessage',        // SOAP action
    'rpc',                             // Style
    'encoded',                         // Use
    "Echoes the provided message back to the caller. For detailed documentation, visit: {$lDocumentationURL}"
);

// Define a complex type for the response
$lSOAPWebService->wsdl->addComplexType(
    'EchoMessageResponse',
    'complexType',
    'struct',
    'all',
    '',
    array(
        'message' => array('name' => 'message', 'type' => 'xsd:string'),
        'command' => array('name' => 'command', 'type' => 'xsd:string'),
        'securityLevel' => array('name' => 'securityLevel', 'type' => 'xsd:string'),
        'timestamp' => array('name' => 'timestamp', 'type' => 'xsd:string'),
        'output' => array('name' => 'output', 'type' => 'xsd:string')
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
 * Define the "echoMessage" method
 * 
 * @param string $pMessage The message to echo.
 * @return array An associative array containing the echoed message and metadata.
 * @throws MethodExecutionException If there is an error executing the method.
 */
function echoMessage($pMessage) {

    try {
        // Include required constants and utility classes
        require_once '../../includes/constants.php';
        require_once '../../classes/EncodingHandler.php';
        require_once '../../classes/SQLQueryHandler.php';
        require_once '../../classes/LogHandler.php';

        $SQLQueryHandler = new SQLQueryHandler(0);
        $lSecurityLevel = $SQLQueryHandler->getSecurityLevelFromDB();
        $LogHandler = new LogHandler($lSecurityLevel);
        $Encoder = new EncodingHandler();

        // Authenticate the request using the shared function
        authenticateRequest($lSecurityLevel);

        // Set security-related variables
        $lProtectAgainstCommandInjection = $lSecurityLevel >= SECURITY_LEVEL_SECURE;
        $lProtectAgainstXSS = $lProtectAgainstCommandInjection;

        // Apply XSS protection if enabled
        $lMessage = $lProtectAgainstXSS ? $Encoder->encodeForHTML($pMessage) : $pMessage;

        // Construct the command
        $lCommand = $lProtectAgainstCommandInjection
            ? escapeshellcmd("echo " . escapeshellarg($pMessage))
            : "echo $pMessage";

        // Execute the command and capture output
        $lOutput = shell_exec($lCommand);
        if ($lOutput === null) {
            throw new CommandExecutionException("Command execution failed.");
        }

        // Get the current timestamp
        $lTimestamp = date(DATE_TIME_FORMAT);

        // Create a structured response as an associative array
        $lResponse = array(
            'message' => $lMessage,
            'command' => $lCommand,
            'securityLevel' => $lSecurityLevel,
            'timestamp' => $lTimestamp,
            'output' => $lOutput
        );

        $LogHandler->writeToLog("Executed echo on: $lMessage");

        return $lResponse; // Return as an array for NuSOAP to serialize

    } catch (Exception $e) {
        $lMessage = "Error executing method echoMessage in webservice ws-echo.php: " . $e->getMessage();
        throw new MethodExecutionException($lMessage);
    }
}

// Handle the SOAP request with error handling
try {
    // Process the incoming SOAP request
    $lSOAPWebService->service(file_get_contents("php://input"));
} catch (Exception $e) {
    // Send a fault response back to the client
    $lSOAPWebService->fault('Server', "SOAP Service Error: " . htmlspecialchars($e->getMessage()));
}

?>
