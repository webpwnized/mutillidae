<?php

// Define a dedicated exception for command execution failures
class CommandExecutionException extends Exception {}
class ValidationException extends Exception {}
class LookupException extends Exception {}

// Pull in the NuSOAP library
require_once './lib/nusoap.php';

$lServerName = $_SERVER['SERVER_NAME'];

// Construct the full URL to the documentation
$lDocumentationURL = "http://{$lServerName}/webservices/soap/docs/soap-services.html";

// Create the SOAP server instance
$lSOAPWebService = new soap_server();

// Initialize WSDL support for the SOAP service
$lSOAPWebService->configureWSDL('commandinjwsdl', 'urn:commandinjwsdl');

// Register the lookupDNS method to expose as a SOAP service
$lSOAPWebService->register(
    'lookupDNS',                           // Method name
    array('targetHost' => 'xsd:string'),   // Input parameter
    array('return' => 'tns:LookupDNSResponse'),  // Output parameter defined as a complex type
    'urn:commandinjwsdl',                  // Namespace
    'urn:commandinjwsdl#lookupDNS',        // SOAP action
    'rpc',                                 // Style
    'encoded',                             // Use
    "Executes a DNS lookup for the specified host and returns the result."
);

// Define a complex type for the response
$lSOAPWebService->wsdl->addComplexType(
    'LookupDNSResponse',
    'complexType',
    'struct',
    'all',
    '',
    array(
        'host' => array('name' => 'host', 'type' => 'xsd:string'),
        'command' => array('name' => 'command', 'type' => 'xsd:string'),
        'securityLevel' => array('name' => 'securityLevel', 'type' => 'xsd:string'),
        'timestamp' => array('name' => 'timestamp', 'type' => 'xsd:string'),
        'output' => array('name' => 'output', 'type' => 'xsd:string')
    )
);

/**
 * Method: lookupDNS
 * Performs a DNS lookup for a given target host.
 * 
 * @param string $pTargetHost The host name or IP address to look up.
 * @return array An associative array containing the nslookup output, timestamp, security level, and host.
 */
function lookupDNS($pTargetHost) {

    // Include required constants and utility classes
    require_once '../../includes/constants.php';
    require_once '../../classes/LogHandler.php';
    require_once '../../classes/EncodingHandler.php';
    require_once '../../classes/SQLQueryHandler.php';

    try {
        // Initialize classes
        $SQLQueryHandler = new SQLQueryHandler(0);
        $lSecurityLevel = $SQLQueryHandler->getSecurityLevelFromDB();
        $LogHandler = new LogHandler($lSecurityLevel);
        $Encoder = new EncodingHandler();

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
            echo ERROR_MESSAGE_METHOD_NOT_ALLOWED;
            exit();
        }

        switch ($lSecurityLevel) {
            default:
            case SECURITY_LEVEL_INSECURE:
                $lProtectAgainstCommandInjection = false;
                $lProtectAgainstXSS = false;
                $lRequireAuthentication = false;
                break;
            case SECURITY_LEVEL_MEDIUM:
                $lProtectAgainstCommandInjection = false;
                $lProtectAgainstXSS = false;
                $lRequireAuthentication = true;
                break;
            case 2:
            case 3:
            case 4:
            case SECURITY_LEVEL_SECURE:
                $lProtectAgainstCommandInjection = true;
                $lProtectAgainstXSS = true;
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
                echo ERROR_MESSAGE_UNAUTHORIZED;
                exit();
            }
        }

        // Validate the target host to protect against command injection, if security is enabled
        if ($lProtectAgainstCommandInjection) {
            $lTargetHostValidated = preg_match(IPV4_REGEX_PATTERN, $pTargetHost) ||
                                    preg_match(DOMAIN_NAME_REGEX_PATTERN, $pTargetHost) ||
                                    preg_match(IPV6_REGEX_PATTERN, $pTargetHost);
            if (!$lTargetHostValidated) {
                throw new ValidationException("Invalid target host: " . $pTargetHost);
            }
        }

        // Protect against XSS by encoding the target host, if enabled
        $lTargetHost = $lProtectAgainstXSS
            ? $Encoder->encodeForHTML($pTargetHost)
            : $pTargetHost;

        // Construct the command
        $lCommand = $lProtectAgainstCommandInjection
            ? escapeshellcmd("nslookup " . escapeshellarg($lTargetHost))
            : "nslookup $lTargetHost";

        // Execute the command and capture output
        $lOutput = shell_exec($lCommand);
        if ($lOutput === null) {
            throw new CommandExecutionException("Command execution failed.");
        }

        // Get the current timestamp
        $lTimestamp = date('Y-m-d H:i:s');

        // Create a structured response as an associative array
        $response = array(
            'host' => $lTargetHost,
            'command' => $lCommand,
            'securityLevel' => $lSecurityLevel,
            'timestamp' => $lTimestamp,
            'output' => $lOutput
        );

        $LogHandler->writeToLog("Executed nslookup on: $lTargetHost");

        return $response; // Return as an array for NuSOAP to serialize

    } catch (Exception $e) {
        throw new LookupException("Error in method lookupDNS: " . $e->getMessage());
    }
}

try {
    // Process the incoming SOAP request
    $lSOAPWebService->service(file_get_contents("php://input"));
} catch (Exception $e) {
    // Send a fault response back to the client if an error occurs
    $lSOAPWebService->fault('Server', "SOAP Service Error: " . htmlspecialchars($e->getMessage()));
}
?>
