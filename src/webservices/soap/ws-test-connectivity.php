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
    array('return' => 'xsd:string'),   // Output parameter
    'urn:connectivitywsdl',            // Namespace
    'urn:connectivitywsdl#testConnectivity', // SOAP action
    'rpc',                             // Style
    'encoded',                         // Use
    // Documentation with a sample request
    "Returns a simple message to confirm connectivity. For detailed documentation, visit: {$lDocumentationURL}"
);

// Define the "testConnectivity" method
function testConnectivity() {
    return 'Connection successful...';
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
