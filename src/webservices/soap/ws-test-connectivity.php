<?php
// Include the nusoap library
require_once './lib/nusoap.php';

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

// Define the "testConnectivity" method
function testConnectivity() {
    try {
        // Include required constants and utility classes
        require_once '../../includes/constants.php';
        require_once '../../classes/SQLQueryHandler.php';

        $SQLQueryHandler = new SQLQueryHandler(0);
        $lSecurityLevel = $SQLQueryHandler->getSecurityLevelFromDB();

        // Get the current timestamp
        $lTimestamp = date('Y-m-d H:i:s');

        // Create a structured response as an associative array
        $response = array(
            'successMessage' => 'Connection successful...',
            'securityLevel' => $lSecurityLevel,
            'timestamp' => $lTimestamp
        );

        return $response; // Return as an array for NuSOAP to serialize

    } catch (Exception $e) {
        throw new Exception("Error executing method testConnectivity in webservice ws-connectivity.php: " . $e->getMessage());
    }
}

// Handle the SOAP request with error handling
try {
    // Process the incoming SOAP request
    $lSOAPWebService->service(file_get_contents("php://input"));
} catch (Exception $e) {
    // Send a fault response back to the client
    $lSOAPWebService->fault('Server', "SOAP Service Error: " . $e->getMessage());
}
?>
