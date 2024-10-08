<?php

// nusoap_server is the class you need from the nusoap library
require_once './lib/nusoap.php';

// Create the SOAP server instance
$server = new soap_server();

// Initialize WSDL (Web Service Definition Language) support
$server->configureWSDL('hellowsdl', 'urn:hellowsdl');

// Register the "hello" method to expose as a SOAP function
$server->register(
    'hello',                          // method name
    array('name' => 'xsd:string'),    // input parameter
    array('return' => 'xsd:string'),  // output parameter
    'urn:hellowsdl',                  // namespace
    'urn:hellowsdl#hello',            // SOAP action
    'rpc',                            // style
    'encoded',                        // use
    'Says hello to the caller
    <br/><br/>
    Sample Request (Copy and paste into Burp Repeater)<br/>
        <br/>POST /mutillidae/webservices/soap/ws-hello-world.php HTTP/1.1
        <br/>Accept-Encoding: gzip,deflate
        <br/>Content-Type: text/xml;charset=UTF-8
        <br/>SOAPAction: &quot;urn:hellowsdl#hello&quot;
        <br/>Content-Length: 438
        <br/>Host: localhost
        <br/>Connection: Keep-Alive
        <br/>User-Agent: Apache-HttpClient/4.1.1 (java 1.5)
        <br/>
        <br/>&lt;soapenv:Envelope xmlns:xsi=&quot;http://www.w3.org/2001/XMLSchema-instance&quot; xmlns:xsd=&quot;http://www.w3.org/2001/XMLSchema&quot; xmlns:soapenv=&quot;http://schemas.xmlsoap.org/soap/envelope/&quot; xmlns:urn=&quot;urn:hellowsdl&quot;&gt;
        <br/>   &lt;soapenv:Header/&gt;
        <br/>   &lt;soapenv:Body&gt;
        <br/>      &lt;urn:hello soapenv:encodingStyle=&quot;http://schemas.xmlsoap.org/soap/encoding/&quot;&gt;
        <br/>         &lt;name xsi:type=&quot;xsd:string&quot;&gt;Fred&lt;/name&gt;
        <br/>      &lt;/urn:hello&gt;
        <br/>   &lt;/soapenv:Body&gt;
        <br/>&lt;/soapenv:Envelope&gt;'            // end documentation
);

// Define the "hello" method
function hello($name) {
    return 'Hello, ' . $name;
}

// Handle the SOAP request with error handling
try {
    // Process the incoming SOAP request
    $server->service(file_get_contents("php://input"));
} catch (Exception $e) {
    // Send a fault response back to the client
    $server->fault('Server', "SOAP Service Error: " . $e->getMessage());
}
?>
