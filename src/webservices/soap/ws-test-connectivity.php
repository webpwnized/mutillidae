<?php
// Include the nusoap library
require_once './lib/nusoap.php';

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
    'Returns a simple message to confirm connectivity.
    <br/>
    <br/>Sample Request (Copy and paste into Burp Repeater):
    <br/>
    <br/>POST /webservices/soap/ws-test-connectivity.php HTTP/1.1
    <br/>Host: localhost
    <br/>Content-Type: text/xml;charset=UTF-8
    <br/>SOAPAction: "urn:connectivitywsdl#testConnectivity"
    <br/>Content-Length: 356
    <br/>Connection: Keep-Alive
    <br/>
    <br/>&lt;soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:urn="urn:connectivitywsdl"&gt;
    <br/>   &lt;soapenv:Header/&gt;
    <br/>   &lt;soapenv:Body&gt;
    <br/>      &lt;urn:testConnectivity/&gt;
    <br/>   &lt;/soapenv:Body&gt;
    <br/>&lt;/soapenv:Envelope&gt;'
);

// Define the "testConnectivity" method
function testConnectivity() {
    return 'Connection successful';
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
