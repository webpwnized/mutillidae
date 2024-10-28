<?php

// Define a dedicated exception for command execution failures
class CommandExecutionException extends Exception {}
class ValidationException extends Exception {}
class LookupException extends Exception {}

// Pull in the NuSOAP library
require_once './lib/nusoap.php';

// Create the SOAP server instance
$lSOAPWebService = new soap_server();

// Initialize WSDL support for the SOAP service
$lSOAPWebService->configureWSDL('commandinjwsdl', 'urn:commandinjwsdl');

// Register the lookupDNS method to expose as a SOAP service
$lSOAPWebService->register(
    'lookupDNS',                           // Method name
    array('targetHost' => 'xsd:string'), // Input parameter
    array('return' => 'xsd:string'),     // Output parameter (XML returned as string)
    'urn:commandinjwsdl',                // Namespace
    'urn:commandinjwsdl#lookupDNS',      // SOAP action
    'rpc',                               // Style
    'encoded',                           // Use
    // Detailed documentation for the method, including a sample SOAP request
    'Returns the results of a DNS lookup.
    <br/>
    <br/>Sample Request (Copy and paste into Burp Repeater)
    <br/>
    <br/>POST /webservices/soap/ws-dns-lookup.php HTTP/1.1
    <br/>Accept-Encoding: gzip,deflate
    <br/>Content-Type: text/xml;charset=UTF-8
    <br/>SOAPAction: "urn:commandinjwsdl#commandinj"
    <br/>Content-Length: 473
    <br/>Host: localhost
    <br/>Connection: Keep-Alive
    <br/>User-Agent: Apache-HttpClient/4.1.1 (java 1.5)
    <br/>
    <br/>&lt;soapenv:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:urn="urn:commandinjwsdl"&gt;
    <br/>   &lt;soapenv:Header/&gt;
    <br/>   &lt;soapenv:Body&gt;
    <br/>      &lt;urn:lookupDNS soapenv:encodingStyle="http://schemas.xmlsoap.org/soap/encoding/"&gt;
    <br/>         &lt;targetHost xsi:type="xsd:string"&gt;www.google.com&lt;/targetHost&gt;
    <br/>      &lt;/urn:lookupDNS&gt;
    <br/>   &lt;/soapenv:Body&gt;
    <br/>&lt;/soapenv:Envelope&gt;'
);

/**
 * Method: lookupDNS
 * Performs a DNS lookup for a given target host.
 * 
 * @param string $pTargetHost The host name or IP address to look up.
 * @return string XML-formatted result containing the nslookup output or validation error message.
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

        // Determine security level and protection settings
        switch ($lSecurityLevel) {
			default: // Insecure
            case "0": // Insecure
            case "1": // Insecure
                $lProtectAgainstCommandInjection = false;
                $lProtectAgainstXSS = false;
            break;
            case "2": // Moderate security
            case "3": // More secure
            case "4": // Secure
            case "5": // Fairly secure
                $lProtectAgainstCommandInjection = true;
                $lProtectAgainstXSS = true;
            break;
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

        // Build the XML response
        $lXmlResponse = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
        $lXmlResponse .= "<results>\n";
        $lXmlResponse .= "  <host>" . htmlspecialchars($lTargetHost) . "</host>\n";
        $lXmlResponse .= "  <output><![CDATA[\n$lOutput\n]]></output>\n";
        $lXmlResponse .= "</results>";

        $LogHandler->writeToLog("Executed nslookup on: $lTargetHost");

        return $lXmlResponse; // Return XML as string

    } catch (Exception $e) {
        throw new LookupException("Error in method lookupDNS: " . $e->getMessage());
    } // End try-catch
} // End function lookupDNS

try{
    // Process the incoming SOAP request
    $lSOAPWebService->service(file_get_contents("php://input"));
} catch (Exception $e) {
    // Send a fault response back to the client if an error occurs
    $lSOAPWebService->fault('Server', "SOAP Service Error: " . $e->getMessage());
}
?>
