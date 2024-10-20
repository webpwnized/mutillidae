<?php
// Include the nusoap library
require_once './lib/nusoap.php';

// Create the SOAP server instance
$lSOAPWebService = new soap_server();

// Initialize WSDL (Web Service Definition Language) support
$lSOAPWebService->configureWSDL('echowsdl', 'urn:echowsdl');

// Register the "echoMessage" method to expose it as a SOAP function
$lSOAPWebService->register(
    'echoMessage',                     // Method name
    array('message' => 'xsd:string'),  // Input parameter
    array('return' => 'xsd:string'),   // Output parameter
    'urn:echowsdl',                    // Namespace
    'urn:echowsdl#echoMessage',        // SOAP action
    'rpc',                             // Style
    'encoded',                         // Use
    'Echoes the provided message back to the caller
    <br/><br/>
    Sample Request (Copy and paste into Burp Repeater)<br/>
        <br/>POST /webservices/soap/ws-echo.php HTTP/1.1
        <br/>Accept-Encoding: gzip,deflate
        <br/>Content-Type: text/xml;charset=UTF-8
        <br/>SOAPAction: "urn:echowsdl#echoMessage"
        <br/>Content-Length: 438
        <br/>Host: localhost
        <br/>Connection: Keep-Alive
        <br/>User-Agent: Apache-HttpClient/4.1.1 (java 1.5)
        <br/>
        <br/>&lt;soapenv:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:urn="urn:echowsdl"&gt;
        <br/>   &lt;soapenv:Header/&gt;
        <br/>   &lt;soapenv:Body&gt;
        <br/>      &lt;urn:echoMessage soapenv:encodingStyle="http://schemas.xmlsoap.org/soap/encoding/"&gt;
        <br/>         &lt;message xsi:type="xsd:string"&gt;Hello, world!&lt;/message&gt;
        <br/>      &lt;/urn:echoMessage&gt;
        <br/>   &lt;/soapenv:Body&gt;
        <br/>&lt;/soapenv:Envelope&gt;'
);

// Define the "echoMessage" method
function echoMessage($pMessage) {
    return 'You said: ' . $pMessage;
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
