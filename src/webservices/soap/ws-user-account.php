<?php
	/* Example SQL injection: jeremy' union select username,password from accounts -- */

	// Define a dedicated exception class for missing parameters
	class MissingParameterException extends Exception {}

	/* ------------------------------------------
	 * Constants used in application
	* ------------------------------------------ */
	require_once '../../includes/constants.php';
	require_once '../../classes/MySqlHandler.php';
	require_once '../../classes/Encoder.php';
	require_once '../../classes/CustomErrorHandler.php';
	require_once '../../classes/LogHandler.php';

	// Initialize the SQL query handler
	$SQLQueryHandler = new MySqlHandler(0);

	$lSecurityLevel = $SQLQueryHandler->getSecurityLevelFromDB();

	// Initialize the encoder
	$Encoder = new Encoder($lSecurityLevel);

	// Initialize the custom error handler
	$CustomErrorHandler = new CustomErrorHandler($lSecurityLevel);

	// Initialize the log handler
	$LogHandler = new LogHandler($lSecurityLevel);

	try{
		switch ($lSecurityLevel){
			default: // Insecure
			case "0": // This code is insecure
			case "1": // This code is insecure
				$lEncodeOutput = false;
			break;
			case "2":
			case "3":
			case "4":
			case "5": // This code is fairly secure
				$lEncodeOutput = true;
			break;
		}//end switch

	} catch (Exception $e) {
		$lErrorMessage = "ws-user-account.php: Unable to parse session";
		echo $CustomErrorHandler->FormatError($e, $lErrorMessage);
	}// end try

	// Pull in the NuSOAP code
	require_once './lib/nusoap.php';

	// Create the server instance
	$lSOAPWebService = new soap_server();

	// Initialize WSDL support
	$lSOAPWebService->configureWSDL('ws-user-account', 'urn:ws-user-account');

	// Register the method to expose
	$lSOAPWebService->register(
		'getUser',                            // Method name
		array('username' => 'xsd:string'),    // Input parameter (expects a username)
		array('return' => 'xsd:xml'),         // Output parameter (returns XML)
		'urn:ws-user-account',                // Namespace
		'urn:ws-user-account#getUser',        // SOAP action
		'rpc',                                // Style (remote procedure call)
		'encoded',                            // Use (encoding style)
		// Documentation: Functionality and Sample SOAP Request
		'Fetches user information if the user exists, otherwise returns an error message.
		<br/>
		<br/>Sample Request (Copy and paste into Burp Repeater)
		<br/>
		<br/>POST /webservices/soap/ws-user-account.php HTTP/1.1
		<br/>Accept-Encoding: gzip,deflate
		<br/>Content-Type: text/xml;charset=UTF-8
		<br/>Content-Length: 458
		<br/>Host: localhost
		<br/>Connection: Keep-Alive
		<br/>User-Agent: Apache-HttpClient/4.1.1 (java 1.5)
		<br/>
		<br/>&lt;soapenv:Envelope xmlns:xsi=&quot;http://www.w3.org/2001/XMLSchema-instance&quot; xmlns:xsd=&quot;http://www.w3.org/2001/XMLSchema&quot; xmlns:soapenv=&quot;http://schemas.xmlsoap.org/soap/envelope/&quot; xmlns:urn=&quot;urn:ws-user-account&quot;&gt;
		<br/>   &lt;soapenv:Header/&gt;
		<br/>   &lt;soapenv:Body&gt;
		<br/>      &lt;urn:getUser soapenv:encodingStyle=&quot;http://schemas.xmlsoap.org/soap/encoding/&quot;&gt;
		<br/>         &lt;username xsi:type=&quot;xsd:string&quot;&gt;Jeremy&lt;/username&gt;
		<br/>      &lt;/urn:getUser&gt;
		<br/>   &lt;/soapenv:Body&gt;
		<br/>&lt;/soapenv:Envelope&gt;' // End of documentation
	);
	
	// Register the method to expose
	$lSOAPWebService->register('registerUser',			                	// method name
			array(
				'username' => 'xsd:string',
				'password' => 'xsd:string',
				'firstname' => 'xsd:string',
				'lastname' => 'xsd:string',
				'signature' => 'xsd:string'
			),																// input parameters
			array('return' => 'xsd:xml'),      								// output parameters
			'urn:ws-user-account',                      					// namespace
			'urn:ws-user-account#registerUser',                				// soapaction
			'rpc',                                							// style
			'encoded',                            							// use
			'Creates new user account
			<br/>
			<br/>Sample Request (Copy and paste into Burp Repeater)
			<br/>
			<br />POST /webservices/soap/ws-user-account.php HTTP/1.1
			<br />Accept-Encoding: gzip,deflate
			<br />Content-Type: text/xml;charset=UTF-8
			<br />Content-Length: 587
			<br />Host: localhost
			<br />Connection: Keep-Alive
			<br />User-Agent: Apache-HttpClient/4.1.1 (java 1.5)
			<br />
			<br />&lt;soapenv:Envelope xmlns:xsi=&quot;http://www.w3.org/2001/XMLSchema-instance&quot; xmlns:xsd=&quot;http://www.w3.org/2001/XMLSchema&quot; xmlns:soapenv=&quot;http://schemas.xmlsoap.org/soap/envelope/&quot; xmlns:urn=&quot;urn:ws-user-account&quot;&gt;
			<br />   &lt;soapenv:Header/&gt;
			<br />   &lt;soapenv:Body&gt;
			<br />      &lt;urn:registerUser soapenv:encodingStyle=&quot;http://schemas.xmlsoap.org/soap/encoding/&quot;&gt;
			<br />         &lt;username xsi:type=&quot;xsd:string&quot;&gt;Joe2&lt;/username&gt;
			<br />         &lt;password xsi:type=&quot;xsd:string&quot;&gt;Holly&lt;/password&gt;
			<br />         &lt;firstname xsi:type=&quot;xsd:string&quot;&gt;Joe&lt;/firstname&gt;
			<br />         &lt;lastname xsi:type=&quot;xsd:string&quot;&gt;Holly&lt;/lastname&gt;
			<br />         &lt;signature xsi:type=&quot;xsd:string&quot;&gt;Try Harder&lt;/signature&gt;
			<br />      &lt;/urn:registerUser&gt;
			<br />   &lt;/soapenv:Body&gt;
			<br />&lt;/soapenv:Envelope&gt;'	// end documentation
	);

	// Register the method to expose
	$lSOAPWebService->register('updateUser',			                	// method name
			array(
					'username' => 'xsd:string',
					'password' => 'xsd:string',
					'firstname' => 'xsd:string',
					'lastname' => 'xsd:string',
					'signature' => 'xsd:string'
			),																// input parameters
			array('return' => 'xsd:xml'),      								// output parameters
			'urn:ws-user-account',                      					// namespace
			'urn:ws-user-account#updateUser',                				// soapaction
			'rpc',                                							// style
			'encoded',                            							// use
			'If account exists, updates existing user account else creates new user account
			<br/>
			<br/>Sample Request (Copy and paste into Burp Repeater)
			<br/>
			<br />POST /webservices/soap/ws-user-account.php HTTP/1.1
			<br />Accept-Encoding: gzip,deflate
			<br />Content-Type: text/xml;charset=UTF-8
			<br />Content-Length: 587
			<br />Host: localhost
			<br />Connection: Keep-Alive
			<br />User-Agent: Apache-HttpClient/4.1.1 (java 1.5)
			<br />
			<br />&lt;soapenv:Envelope xmlns:xsi=&quot;http://www.w3.org/2001/XMLSchema-instance&quot; xmlns:xsd=&quot;http://www.w3.org/2001/XMLSchema&quot; xmlns:soapenv=&quot;http://schemas.xmlsoap.org/soap/envelope/&quot; xmlns:urn=&quot;urn:ws-user-account&quot;&gt;
			<br />   &lt;soapenv:Header/&gt;
			<br />   &lt;soapenv:Body&gt;
			<br />      &lt;urn:updateUser soapenv:encodingStyle=&quot;http://schemas.xmlsoap.org/soap/encoding/&quot;&gt;
			<br />         &lt;username xsi:type=&quot;xsd:string&quot;&gt;Joe2&lt;/username&gt;
			<br />         &lt;password xsi:type=&quot;xsd:string&quot;&gt;Holly&lt;/password&gt;
			<br />         &lt;firstname xsi:type=&quot;xsd:string&quot;&gt;Joe&lt;/firstname&gt;
			<br />         &lt;lastname xsi:type=&quot;xsd:string&quot;&gt;Holly&lt;/lastname&gt;
			<br />         &lt;signature xsi:type=&quot;xsd:string&quot;&gt;Try Harder&lt;/signature&gt;
			<br />      &lt;/urn:updateUser&gt;
			<br />   &lt;/soapenv:Body&gt;
			<br />&lt;/soapenv:Envelope&gt;'	// end documentation
				);

	// Register the method to expose
	$lSOAPWebService->register('deleteUser',			                	// method name
			array(
					'username' => 'xsd:string',
					'password' => 'xsd:string'
			),											// input parameters
			array('return' => 'xsd:xml'),      			// output parameters
			'urn:ws-user-account',                      // namespace
			'urn:ws-user-account#deleteUser',           // soapaction
			'rpc',                                		// style
			'encoded',                            		// use
			'If account exists, deletes user account
			<br/>
			<br/>Sample Request (Copy and paste into Burp Repeater)
			<br/>
			<br/>POST /webservices/soap/ws-user-account.php HTTP/1.1
			<br/>Accept-Encoding: gzip,deflate
			<br/>Content-Type: text/xml;charset=UTF-8
			<br/>Content-Length: 587
			<br/>Host: localhost
			<br/>Connection: Keep-Alive
			<br/>User-Agent: Apache-HttpClient/4.1.1 (java 1.5)
			<br/>
			<br/>&lt;soapenv:Envelope xmlns:xsi=&quot;http://www.w3.org/2001/XMLSchema-instance&quot; xmlns:xsd=&quot;http://www.w3.org/2001/XMLSchema&quot; xmlns:soapenv=&quot;http://schemas.xmlsoap.org/soap/envelope/&quot; xmlns:urn=&quot;urn:ws-user-account&quot;&gt;
			<br/>   &lt;soapenv:Header/&gt;
			<br/>   &lt;soapenv:Body&gt;
			<br/>      &lt;urn:deleteUser soapenv:encodingStyle=&quot;http://schemas.xmlsoap.org/soap/encoding/&quot;&gt;
			<br/>         &lt;username xsi:type=&quot;xsd:string&quot;&gt;Joe&lt;/username&gt;
			<br/>         &lt;password xsi:type=&quot;xsd:string&quot;&gt;Holly&lt;/password&gt;
			<br/>      &lt;/urn:deleteUser&gt;
			<br/>   &lt;/soapenv:Body&gt;
			<br/>&lt;/soapenv:Envelope&gt;
			'	// documentation
	);

	function doXMLEncodeQueryResults($pUsername, $pQueryResult, $pEncodeOutput) {
		global $Encoder;
	
		// Start the XML result with the root element and a message attribute
		$lResults = "<accounts message=\"Results for {$pUsername}\">";
	
		// Iterate over each row in the query result
		while ($row = $pQueryResult->fetch_object()) {
			// Handle encoding of username
			$lUsername = $pEncodeOutput ? $Encoder->encodeForHTML($row->username) : $row->username;
	
			// Handle encoding of signature if it exists
			$lSignature = null;
			if (isset($row->mysignature)) {
				$encodedSignature = $pEncodeOutput ? $Encoder->encodeForHTML($row->mysignature) : $row->mysignature;
				$lSignature = $encodedSignature;
			}
	
			// Construct the XML for each account
			$lResults .= "<account>";
			$lResults .= "<username>{$lUsername}</username>";
			$lResults .= "<firstname>{$row->firstname}</firstname>";
			$lResults .= "<lastname>{$row->lastname}</lastname>";
			if ($lSignature) {
				$lResults .= "<signature>{$lSignature}</signature>";
			}
			$lResults .= "</account>";
		}
	
		// Close the root element
		$lResults .= "</accounts>";
	
		return $lResults;
	}//end function doXMLEncodeQueryResults	

	function xmlEncodeQueryResults($pUsername, $pEncodeOutput) {
		global $SQLQueryHandler;

		// Fetch query results based on the username
		if ($pUsername == "*") {
			// List all accounts
			$lQueryResult = $SQLQueryHandler->getUsernames();
		} else {
			// Lookup specific user account
			$lQueryResult = $SQLQueryHandler->getNonSensitiveAccountInformation($pUsername);
		}
	
		// Check if the query returned valid results
		if ($lQueryResult && $lQueryResult->num_rows > 0) {
			return doXMLEncodeQueryResults($pUsername, $lQueryResult, $pEncodeOutput);
		} else {
			// Return a message if no user is found
			return "<accounts message=\"User {$pUsername} does not exist\" />";
		}
		
	}//end function xmlEncodeQueryResults

	function assertParameter($pParameter){
		if(strlen($pParameter) == 0 || !isset($pParameter)){
			throw new MissingParameterException("Parameter ".$pParameter." is required");
		}// end if
	}// end function assertParameter

	// Define the method as a PHP function
	function getUser($pUsername) {

		try{
		   	$lResults = "";
		   	global $LogHandler;
		   	global $lEncodeOutput;
		   	global $SQLQueryHandler;
		   	global $CustomErrorHandler;

		   	assertParameter($pUsername);

			$lResults = xmlEncodeQueryResults($pUsername, $lEncodeOutput);

			try {
				$LogHandler->writeToLog("ws-user-account.php: Fetched user-information for: {$pUsername}");
			} catch (Exception $e) {
				// do nothing
			}//end try

		    return $lResults;

	    } catch (Exception $e) {
	    	return $CustomErrorHandler->FormatErrorXML($e, "Unable to process request to web service ws-user-account->getUser()");
	    }// end try

	}// end function getUser()

	function registerUser($pUsername, $pPassword, $pFirstname, $pLastname, $pSignature){

		try{

			global $LogHandler;
			global $lEncodeOutput;
			global $SQLQueryHandler;
			global $CustomErrorHandler;

			assertParameter($pUsername);
			assertParameter($pPassword);
			assertParameter($pFirstname);
			assertParameter($pLastname);
			assertParameter($pSignature);

			if ($SQLQueryHandler->accountExists($pUsername)){
				return "<accounts message=\"User {$pUsername} already exists\" />";
			}else{
				$lQueryResult = $SQLQueryHandler->insertNewUserAccount($pUsername, $pPassword, $pFirstname, $pLastname, $pSignature);
				return "<accounts message=\"Inserted account {$pUsername}\" />";
			}// end if

		} catch (Exception $e) {
			return $CustomErrorHandler->FormatErrorXML($e, "Unable to process request to web service ws-user-account->registerUser()");
		}// end try

	}// end function registerUser()

	function updateUser($pUsername, $pPassword, $pFirstname, $pLastname, $pSignature){

		try{

			global $LogHandler;
			global $lEncodeOutput;
			global $SQLQueryHandler;
			global $CustomErrorHandler;

			assertParameter($pUsername);
			assertParameter($pPassword);
			assertParameter($pFirstname);
			assertParameter($pLastname);
			assertParameter($pSignature);

			if ($SQLQueryHandler->accountExists($pUsername)){
				$lQueryResult = $SQLQueryHandler->updateUserAccount($pUsername, $pPassword, $pFirstname, $pLastname, $pSignature, false);
				return "<accounts message=\"Updated account {$pUsername}\" />";
			}else{
				$lQueryResult = $SQLQueryHandler->insertNewUserAccount($pUsername, $pPassword, $pFirstname, $pLastname, $pSignature);
				return "<accounts message=\"Inserted account {$pUsername}\" />";
			}// end if

		} catch (Exception $e) {
			return $CustomErrorHandler->FormatErrorXML($e, "Unable to process request to web service ws-user-account->updateUser()");
		}// end try

	}// end function updateUser()

	function deleteUser($pUsername, $pPassword){

		try{

			global $LogHandler;
			global $lEncodeOutput;
			global $SQLQueryHandler;
			global $CustomErrorHandler;

			assertParameter($pUsername);
			assertParameter($pPassword);

			if($SQLQueryHandler->accountExists($pUsername)){

				if($SQLQueryHandler->authenticateAccount($pUsername,$pPassword)){
					$lQueryResult = $SQLQueryHandler->deleteUser($pUsername);

					if ($lQueryResult){
						return "<accounts message=\"Deleted account {$pUsername}\" />";
					}else{
						return "<accounts message=\"Attempted to delete account {$pUsername} but result returned was {$lQueryResult}\" />";
					}//end if

				}else{
					return "<accounts message=\"Could not authenticate account {$pUsername}. Password incorrect.\" />";
				}// end if

			}else{
				return "<accounts message=\"User {$pUsername} does not exist\" />";
			}// end if

		} catch (Exception $e) {
			return $CustomErrorHandler->FormatErrorXML($e, "Unable to process request to web service ws-user-account->deleteUser()");
		}// end try

	}// end function registerUser()

	// Handle the SOAP request with error handling
	try {
		// Process the incoming SOAP request
		$lSOAPWebService->service(file_get_contents("php://input"));
	} catch (Exception $e) {
		// Send a fault response back to the client if an error occurs
		$lSOAPWebService->fault('Server', "SOAP Service Error: " . $e->getMessage());
	}

?>
