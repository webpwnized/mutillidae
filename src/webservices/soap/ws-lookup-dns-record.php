<?php
	// Pull in the NuSOAP code
	require_once('./lib/nusoap.php');

	// Create the server instance
	$lSOAPWebService = new soap_server();

	// Initialize WSDL support
	$lSOAPWebService->configureWSDL('commandinjwsdl', 'urn:commandinjwsdl');

	// Register the method to expose
	$lSOAPWebService->register(
		'lookupDNS',							// method name
	    array('targetHost' => 'xsd:string'),	// input parameters
	    array('Answer' => 'xsd:xml'),    		// output parameters
	    'urn:commandinjwsdl',               	// namespace
	    'urn:commandinjwsdl#commandinj',    	// soapaction
	    'rpc',                              	// style
	    'encoded',                          	// use
	    'Returns the results of the DNS lookup
		<br/>
		<br/>Sample Request (Copy and paste into Burp Repeater)
		<br/>
		<br/>POST /mutillidae/webservices/soap/ws-lookup-dns-record.php HTTP/1.1
		<br/>Accept-Encoding: gzip,deflate
		<br/>Content-Type: text/xml;charset=UTF-8
		<br/>SOAPAction: &quot;urn:commandinjwsdl#commandinj&quot;
		<br/>Content-Length: 473
		<br/>Host: localhost
		<br/>Connection: Keep-Alive
		<br/>User-Agent: Apache-HttpClient/4.1.1 (java 1.5)
		<br/>
		<br/>&lt;soapenv:Envelope xmlns:xsi=&quot;http://www.w3.org/2001/XMLSchema-instance&quot; xmlns:xsd=&quot;http://www.w3.org/2001/XMLSchema&quot; xmlns:soapenv=&quot;http://schemas.xmlsoap.org/soap/envelope/&quot; xmlns:urn=&quot;urn:commandinjwsdl&quot;&gt;
		<br/>   &lt;soapenv:Header/&gt;
		<br/>   &lt;soapenv:Body&gt;
		<br/>      &lt;urn:lookupDNS soapenv:encodingStyle=&quot;http://schemas.xmlsoap.org/soap/encoding/&quot;&gt;
		<br/>         &lt;targetHost xsi:type=&quot;xsd:string&quot;&gt;www.google.com&lt;/targetHost&gt;
		<br/>      &lt;/urn:lookupDNS&gt;
		<br/>   &lt;/soapenv:Body&gt;
		<br/>&lt;/soapenv:Envelope&gt;' // documentation
	);

	// Define the method as a PHP function
	function lookupDNS($pTargetHost) {

	    if (session_status() == PHP_SESSION_NONE){
	        session_start();
	    }// end if

	    if (!isset($_SESSION["security-level"])){
	        $_SESSION["security-level"] = 0;
	    }// end if

		/* ------------------------------------------
		 * Constants used in application
		 * ------------------------------------------ */
		require_once('../../includes/constants.php');
		require_once('../../includes/minimum-class-definitions.php');

		try {
	    	switch ($_SESSION["security-level"]){
	    		case "0": // This code is insecure. No input validation is performed.
	    		case "1": // This code is insecure. No input validation is performed.
					$lProtectAgainstCommandInjection=FALSE;
					$lProtectAgainstXSS = FALSE;
	    		break;

		   		case "2":
		   		case "3":
		   		case "4":
	    		case "5": // This code is fairly secure
	   				$lProtectAgainstMethodTampering = TRUE;
	   				$lProtectAgainstXSS = TRUE;
	    		break;
	    	}// end switch

	    	if ($lProtectAgainstCommandInjection) {
				/* Protect against command injection.
				 * We validate that an IP is 4 octets, IPV6 fits the pattern, and that domain name is IANA format */
    			$lTargetHostValidated = preg_match(IPV4_REGEX_PATTERN, $pTargetHost) || preg_match(DOMAIN_NAME_REGEX_PATTERN, $pTargetHost) || preg_match(IPV6_REGEX_PATTERN, $pTargetHost);
	    	}else{
    			$lTargetHostValidated=TRUE; 			// do not perform validation
	    	}// end if

	    	if ($lProtectAgainstXSS) {
    			/* Protect against XSS by output encoding */
    			$lTargetHostText = $Encoder->encodeForHTML($pTargetHost);
	    	}else{
				//allow XSS by not encoding output
				$lTargetHostText = $pTargetHost;
	    	}//end if

		}catch(Exception $e){
			echo $CustomErrorHandler->FormatError($e, "Error setting up configuration on page dns-lookup.php");
		}// end try

	    try{
	    	$lResults = "";
	    	if ($lTargetHostValidated){
	    		$lResults .= '<results host="'.$lTargetHostText.'">';
    			$lResults .=  shell_exec("nslookup " . $pTargetHost);
    			$lResults .= '</results>';
				$LogHandler->writeToLog("Executed operating system command: nslookup " . $lTargetHostText);
	    	}else{
	    		$lResults .= "<message>Validation Error</message>";
	    	}// end if ($lTargetHostValidated){

	    	return $lResults;

    	}catch(Exception $e){
			echo $CustomErrorHandler->FormatError($e, "Input: " . $pTargetHost);
    	}// end try

	}//end function

	// Use the request to (try to) invoke the service
	$HTTP_RAW_POST_DATA = isset($HTTP_RAW_POST_DATA) ? $HTTP_RAW_POST_DATA : '';
	$php_version = phpversion();
    $php_major_version = (int)substr($php_version, 0, 1);
    if ($php_major_version >= 7) {
        $lSOAPWebService->service(file_get_contents("php://input"));
    } else {
        $lSOAPWebService->service($HTTP_RAW_POST_DATA);
    }
?>
