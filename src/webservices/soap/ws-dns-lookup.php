<?php
// Pull in the NuSOAP library
require_once './lib/nusoap.php';

// Create the SOAP server instance
$lSOAPWebService = new soap_server();

// Initialize WSDL support for the SOAP service
$lSOAPWebService->configureWSDL('commandinjwsdl', 'urn:commandinjwsdl');

// Register the lookupDNS method to expose as a SOAP service
$lSOAPWebService->register(
    'lookupDNS',                           // Method name
    array('targetHost' => 'xsd:string'),   // Input parameters
    array('Answer' => 'xsd:xml'),          // Output parameters (returns XML result)
    'urn:commandinjwsdl',                  // Namespace
    'urn:commandinjwsdl#commandinj',       // SOAP action
    'rpc',                                 // Style
    'encoded',                             // Use
    // Detailed documentation for the method, including a sample SOAP request
    'Returns the results of a DNS lookup.
    <br/>
    <br/>Sample Request (Copy and paste into Burp Repeater)
    <br/>
    <br/>POST /webservices/soap/ws-lookup-dns.php HTTP/1.1
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
    // Start session if not already started
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }

    // Initialize security level if not already set
    if (!isset($_SESSION["security-level"])) {
        $_SESSION["security-level"] = 0;
    }

    // Include required constants and utility classes
    require_once '../../includes/constants.php';
    require_once '../../includes/minimum-class-definitions.php';

    try {
        // Determine security level and protection settings
        switch ($_SESSION["security-level"]) {
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
        } else {
            $lTargetHostValidated = true;  // No validation
        }

        // Protect against XSS by encoding the target host, if enabled
        if ($lProtectAgainstXSS) {
            $lTargetHostText = $Encoder->encodeForHTML($pTargetHost);
        } else {
            $lTargetHostText = $pTargetHost;  // Allow XSS by not encoding
        }

    } catch (Exception $e) {
        // Handle errors during configuration setup
        $lErrorMessage = "Error setting up configuration on webservice ws-dns-lookup.php";
        echo $CustomErrorHandler->FormatError($e, $lErrorMessage);
    }

    // Execute the nslookup command and return the result
    try {
        $lResults = "";  // Initialize results string

        if ($lTargetHostValidated) {
            $lResults .= '<results host="' . $lTargetHostText . '">';
            $lResults .= shell_exec("nslookup " . $pTargetHost);  // Execute the command
            $lResults .= '</results>';
            $LogHandler->writeToLog("Executed operating system command: nslookup " . $lTargetHostText);  // Log the command execution
        } else {
            $lResults .= "<message>Validation Error</message>";  // Validation error message
        }

        return $lResults;

    } catch (Exception $e) {
        // Handle errors during DNS lookup
        echo $CustomErrorHandler->FormatError($e, "Input: " . $pTargetHost);
    }
}

// Handle the SOAP request with error handling
try {
    // Process the incoming SOAP request
    $lSOAPWebService->service(file_get_contents("php://input"));
} catch (Exception $e) {
    // Send a fault response back to the client if an error occurs
    $lSOAPWebService->fault('Server', "SOAP Service Error: " . $e->getMessage());
}
?>
