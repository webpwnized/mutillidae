<?php

class MethodExecutionException extends Exception {}

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

    try{
        // Include required constants and utility classes
        require_once '../../includes/constants.php';
        require_once '../../classes/EncodingHandler.php';
        require_once '../../classes/SQLQueryHandler.php';

        $SQLQueryHandler = new SQLQueryHandler(0);

        $lSecurityLevel = $SQLQueryHandler->getSecurityLevelFromDB();

        $Encoder = new EncodingHandler();

        switch ($lSecurityLevel){
            default: // Default case: This code is insecure
            case "0": // This code is insecure
            case "1": // This code is insecure
                $lProtectAgainstCommandInjection=false;
                $lProtectAgainstXSS = false;
            break;

            case "2":
            case "3":
            case "4":
            case "5": // This code is fairly secure
                $lProtectAgainstCommandInjection=true;
                $lProtectAgainstXSS = true;
            break;
        }// end switch
    	
        // Apply XSS protection if enabled
        if ($lProtectAgainstXSS) {
            global $Encoder;
            $lMessage = $Encoder->encodeForHTML($pMessage);
        } else {
            $lMessage = $pMessage;
        }

        // Handle command execution based on the protection flag
        if ($lProtectAgainstCommandInjection) {
            $lResult = $lMessage;
        } else {
            // Allow command injection vulnerability (insecure)
            $lResult = shell_exec("echo " . $lMessage);
        }

        return $lResult; // Return the result as SOAP response

	}catch(Exception $e){
        $lMessage = "Error executing method echoMessage in webservice ws-echo.php";
        throw new MethodExecutionException($lMessage);
    }// end try

} // end function echoMessage

// Handle the SOAP request with error handling
try {
    // Process the incoming SOAP request
    $lSOAPWebService->service(file_get_contents("php://input"));
} catch (Exception $e) {
    // Send a fault response back to the client
    $lSOAPWebService->fault('Server', "SOAP Service Error: " . $e->getMessage());
}

?>
