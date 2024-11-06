<?php
	/* Example SQL injection: jeremy' union select username,password from accounts -- */

	// Define a dedicated exception class for missing parameters
	class MissingParameterException extends Exception {}

	/* ------------------------------------------
	 * Constants used in application
	* ------------------------------------------ */
	require_once '../../includes/constants.php';
	require_once '../../classes/SQLQueryHandler.php';
	require_once '../../classes/EncodingHandler.php';
	require_once '../../classes/CustomErrorHandler.php';
	require_once '../../classes/LogHandler.php';

	// Initialize the SQL query handler
	$SQLQueryHandler = new SQLQueryHandler(0);

	$lSecurityLevel = $SQLQueryHandler->getSecurityLevelFromDB();

	// Initialize the encoder
	$Encoder = new EncodingHandler($lSecurityLevel);

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

	$lServerName = $_SERVER['SERVER_NAME'];

	// Construct the full URL to the documentation
	$lDocumentationURL = "http://{$lServerName}/webservices/soap/docs/soap-services.html";

	// Create the SOAP server instance
	$lSOAPWebService = new soap_server();

	// Initialize WSDL support
	$lSOAPWebService->configureWSDL('ws-user-account', 'urn:ws-user-account');

	// Define a complex type for the response
	$lSOAPWebService->wsdl->addComplexType(
		'UserAccountResponse',
		'complexType',
		'struct',
		'all',
		'',
		array(
			'message' => array('name' => 'message', 'type' => 'xsd:string'),
			'securityLevel' => array('name' => 'securityLevel', 'type' => 'xsd:string'),
			'timestamp' => array('name' => 'timestamp', 'type' => 'xsd:string'),
			'output' => array('name' => 'output', 'type' => 'xsd:string')
		)
	);

	// Register the "getUser" method
	$lSOAPWebService->register(
		'getUser',                            // Method name
		array('username' => 'xsd:string'),    // Input parameter (expects a username)
		array('return' => 'tns:UserAccountResponse'),         // Output parameter (returns XML)
		'urn:ws-user-account',                // Namespace
		'urn:ws-user-account#getUser',        // SOAP action
		'rpc',                                // Style (remote procedure call)
		'encoded',                            // Use (encoding style)
		// Documentation: Functionality and Sample SOAP Request
		"Fetches user information if the user exists, otherwise returns an error message. For detailed documentation, visit: {$lDocumentationURL}"
	);
	
	// Register the "registerUser" method
	$lSOAPWebService->register(
		'registerUser',                           // Method name
		array(
			'username' => 'xsd:string',
			'password' => 'xsd:string',
			'firstname' => 'xsd:string',
			'lastname' => 'xsd:string',
			'signature' => 'xsd:string'
		),                                        // Input parameters
		array('return' => 'tns:UserAccountResponse'), // Output parameters
		'urn:ws-user-account',                    // Namespace
		'urn:ws-user-account#registerUser',       // SOAP action
		'rpc',                                    // Style
		'encoded',                                // Use
		"Creates new user account. For detailed documentation, visit: {$lDocumentationURL}"
	);

	// Register the "updateUser" method
	$lSOAPWebService->register(
		'updateUser',                             // Method name
		array(
			'username' => 'xsd:string',
			'password' => 'xsd:string',
			'firstname' => 'xsd:string',
			'lastname' => 'xsd:string',
			'signature' => 'xsd:string'
		),                                        // Input parameters
		array('return' => 'tns:UserAccountResponse'), // Output parameters
		'urn:ws-user-account',                    // Namespace
		'urn:ws-user-account#updateUser',         // SOAP action
		'rpc',                                    // Style
		'encoded',                                // Use
		"If account exists, updates existing user account else creates new user account. For detailed documentation, visit: {$lDocumentationURL}"
	);

	// Register the "deleteUser" method
	$lSOAPWebService->register(
		'deleteUser',                             // Method name
		array(
			'username' => 'xsd:string',
			'password' => 'xsd:string'
		),                                        // Input parameters
		array('return' => 'tns:UserAccountResponse'), // Output parameters
		'urn:ws-user-account',                    // Namespace
		'urn:ws-user-account#deleteUser',         // SOAP action
		'rpc',                                    // Style
		'encoded',                                // Use
		"If account exists, deletes user account. For detailed documentation, visit: {$lDocumentationURL}"
	);

	function doXMLEncodeQueryResults($pUsername, $pQueryResult, $pEncodeOutput) {
		global $Encoder;
	
		// Start the XML result with the root element and a message attribute
		$lResults = "<accounts>";
	
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
			return "<accounts><message>User {$pUsername} does not exist</message></accounts>";
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

			$lTimestamp = date('Y-m-d H:i:s');

			$lResponse = array(
				'message' => "User data fetched successfully",
				'securityLevel' => $SQLQueryHandler->getSecurityLevelFromDB(),
				'timestamp' => $lTimestamp,
				'output' => $lResults
			);

			try {
				$LogHandler->writeToLog("ws-user-account.php: Fetched user-information for: {$pUsername}");
			} catch (Exception $e) {
				// do nothing
			}//end try

			return $lResponse;

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

			$lTimestamp = date('Y-m-d H:i:s');

			if ($SQLQueryHandler->accountExists($pUsername)){
				$lResponse = array(
					'message' => "User {$pUsername} already exists",
					'securityLevel' => $SQLQueryHandler->getSecurityLevelFromDB(),
					'timestamp' => $lTimestamp,
					'output' => ""
				);
				return $lResponse;
			}else{
				$lQueryResult = $SQLQueryHandler->insertNewUserAccount($pUsername, $pPassword, $pFirstname, $pLastname, $pSignature);
				$lResponse = array(
					'message' => "Inserted account {$pUsername}",
					'securityLevel' => $SQLQueryHandler->getSecurityLevelFromDB(),
					'timestamp' => $lTimestamp,
					'output' => ""
				);
				return $lResponse;
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

			$lTimestamp = date('Y-m-d H:i:s');

			if ($SQLQueryHandler->accountExists($pUsername)){
				$lQueryResult = $SQLQueryHandler->updateUserAccount($pUsername, $pPassword, $pFirstname, $pLastname, $pSignature, false);
				$lResponse = array(
					'message' => "Updated account {$pUsername}",
					'securityLevel' => $SQLQueryHandler->getSecurityLevelFromDB(),
					'timestamp' => $lTimestamp,
					'output' => ""
				);
				return $lResponse;
			}else{
				$lQueryResult = $SQLQueryHandler->insertNewUserAccount($pUsername, $pPassword, $pFirstname, $pLastname, $pSignature);
				$lResponse = array(
					'message' => "Inserted account {$pUsername}",
					'securityLevel' => $SQLQueryHandler->getSecurityLevelFromDB(),
					'timestamp' => $lTimestamp,
					'output' => ""
				);
				return $lResponse;
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

			$lTimestamp = date('Y-m-d H:i:s');

			if($SQLQueryHandler->accountExists($pUsername)){

				if($SQLQueryHandler->authenticateAccount($pUsername,$pPassword)){
					$lQueryResult = $SQLQueryHandler->deleteUser($pUsername);

					if ($lQueryResult){
						$lResponse = array(
							'message' => "Deleted account {$pUsername}",
							'securityLevel' => $SQLQueryHandler->getSecurityLevelFromDB(),
							'timestamp' => $lTimestamp,
							'output' => ""
						);
						return $lResponse;
					}else{
						$lResponse = array(
							'message' => "Attempted to delete account {$pUsername} but result returned was {$lQueryResult}",
							'securityLevel' => $SQLQueryHandler->getSecurityLevelFromDB(),
							'timestamp' => $lTimestamp,
							'output' => ""
						);
						return $lResponse;
					}//end if

				}else{
					$lResponse = array(
						'message' => "Could not authenticate account {$pUsername}. Password incorrect.",
						'securityLevel' => $SQLQueryHandler->getSecurityLevelFromDB(),
						'timestamp' => $lTimestamp,
						'output' => ""
					);
					return $lResponse;
				}// end if

			}else{
				$lResponse = array(
					'message' => "User {$pUsername} does not exist",
					'securityLevel' => $SQLQueryHandler->getSecurityLevelFromDB(),
					'timestamp' => $lTimestamp,
					'output' => ""
				);
				return $lResponse;
			}// end if

		} catch (Exception $e) {
			return $CustomErrorHandler->FormatErrorXML($e, "Unable to process request to web service ws-user-account->deleteUser()");
		}// end try

	}// end function deleteUser()

	// Handle the SOAP request with error handling
	try {
		// Process the incoming SOAP request
		$lSOAPWebService->service(file_get_contents("php://input"));
	} catch (Exception $e) {
		// Send a fault response back to the client if an error occurs
		$lSOAPWebService->fault('Server', "SOAP Service Error: " . $e->getMessage());
	}

?>
