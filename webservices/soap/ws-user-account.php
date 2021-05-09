<?php
	/* Example SQL injection: jeremy' union select username,password from accounts -- */

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

	try{
		switch ($_SESSION["security-level"]){
			case "0": // This code is insecure
			case "1": // This code is insecure
				$lEncodeOutput = FALSE;
				break;

			case "2":
			case "3":
			case "4":
			case "5": // This code is fairly secure
				$lEncodeOutput = TRUE;
				break;
		}//end switch

	} catch (Exception $e) {
		echo $CustomErrorHandler->FormatError($e, "ws-user-account.php: Unable to parse session");
	}// end try;

	// Pull in the NuSOAP code
	require_once('./lib/nusoap.php');

	// Create the server instance
	$lSOAPWebService = new soap_server();

	// Initialize WSDL support
	$lSOAPWebService->configureWSDL('ws-user-account', 'urn:ws-user-account');

	// Register the method to expose
	$lSOAPWebService->register('getUser',			                	// method name
	    array('username' => 'xsd:string'),								// input parameters
	    array('return' => 'xsd:xml'),      								// output parameters
	    'urn:ws-user-account',                      					// namespace
	    'urn:ws-user-account#getUser',                					// soapaction
	    'rpc',                                							// style
	    'encoded',                            							// use
	    'Fetches user information is user exists else returns error message
		<br/>
		<br/>Sample Request (Copy and paste into Burp Repeater)
		<br/>
		<br/>POST /mutillidae/webservices/soap/ws-user-account.php HTTP/1.1
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
		<br/>&lt;/soapenv:Envelope&gt;'	// end documentation
	);

	// Register the method to expose
	$lSOAPWebService->register('createUser',			                	// method name
			array(
				'username' => 'xsd:string',
				'password' => 'xsd:string',
				'signature' => 'xsd:string'
			),																// input parameters
			array('return' => 'xsd:xml'),      								// output parameters
			'urn:ws-user-account',                      					// namespace
			'urn:ws-user-account#createUser',                				// soapaction
			'rpc',                                							// style
			'encoded',                            							// use
			'Creates new user account
			<br/>
			<br/>Sample Request (Copy and paste into Burp Repeater)
			<br/>
			<br />POST /mutillidae/webservices/soap/ws-user-account.php HTTP/1.1
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
			<br />      &lt;urn:createUser soapenv:encodingStyle=&quot;http://schemas.xmlsoap.org/soap/encoding/&quot;&gt;
			<br />         &lt;username xsi:type=&quot;xsd:string&quot;&gt;Joe2&lt;/username&gt;
			<br />         &lt;password xsi:type=&quot;xsd:string&quot;&gt;Holly&lt;/password&gt;
			<br />         &lt;signature xsi:type=&quot;xsd:string&quot;&gt;Try Harder&lt;/signature&gt;
			<br />      &lt;/urn:createUser&gt;
			<br />   &lt;/soapenv:Body&gt;
			<br />&lt;/soapenv:Envelope&gt;'	// end documentation
	);

	// Register the method to expose
	$lSOAPWebService->register('updateUser',			                	// method name
			array(
					'username' => 'xsd:string',
					'password' => 'xsd:string',
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
			<br />POST /mutillidae/webservices/soap/ws-user-account.php HTTP/1.1
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
			<br/>POST /mutillidae/webservices/soap/ws-user-account.php HTTP/1.1
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

	function doXMLEncodeQueryResults($pUsername, $pQueryResult, $pEncodeOutput){

		$lResults = "<accounts message=\"Results for {$pUsername}\">";
		$lUsername = "";
		$lSignature = "";

		while($row = $pQueryResult->fetch_object()){

			$pEncodeOutput?$lSignature = $lUsername = $Encoder->encodeForHTML($row->username):$lUsername = $row->username;;

			if(isset($row->mysignature)){
				$pEncodeOutput?$lSignature = $Encoder->encodeForHTML($row->mysignature):$lSignature = $row->mysignature;
			}// end if

			$lResults.= "<account>";
			$lResults.= "<username>{$lUsername}</username>";
			if(isset($row->mysignature)){$lResults.= "<signature>{$lSignature}</signature>";};
			$lResults.= "</account>";

		}// end while

		$lResults.= "</accounts>";

		return $lResults;

	}//end function doXMLEncodeQueryResults

	function xmlEncodeQueryResults($pUsername, $pEncodeOutput, $SQLQueryHandler){

		$lQueryResult = "";

		if ($pUsername == "*"){
			/* List all accounts */
			$lQueryResult = $SQLQueryHandler->getUsernames();
		}else{
			/* lookup user */
			$lQueryResult = $SQLQueryHandler->getNonSensitiveAccountInformation($pUsername);
		}// end if

		if ($lQueryResult->num_rows > 0){
			return doXMLEncodeQueryResults($pUsername, $lQueryResult, $pEncodeOutput);
		}else{
			return "<accounts message=\"User {$pUsername} does not exist}\" />";
		}// end if

	}// end function xmlEncodeQueryResults()

	function assertParameter($pParameter){
		if(strlen($pParameter) == 0 || !isset($pParameter)){
			throw new Exception("Parameter ".$pParameter." is required");
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

			$lResults = xmlEncodeQueryResults($pUsername, $lEncodeOutput, $SQLQueryHandler);

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

	function createUser($pUsername, $pPassword, $pSignature){

		try{

			global $LogHandler;
			global $lEncodeOutput;
			global $SQLQueryHandler;
			global $CustomErrorHandler;

			assertParameter($pUsername);
			assertParameter($pPassword);
			assertParameter($pSignature);

			if ($SQLQueryHandler->accountExists($pUsername)){
				return "<accounts message=\"User {$pUsername} already exists\" />";
			}else{
				$lQueryResult = $SQLQueryHandler->insertNewUserAccount($pUsername, $pPassword, $pSignature);
				return "<accounts message=\"Inserted account {$pUsername}\" />";
			}// end if

		} catch (Exception $e) {
			return $CustomErrorHandler->FormatErrorXML($e, "Unable to process request to web service ws-user-account->createUser()");
		}// end try

	}// end function createUser()

	function updateUser($pUsername, $pPassword, $pSignature){

		try{

			global $LogHandler;
			global $lEncodeOutput;
			global $SQLQueryHandler;
			global $CustomErrorHandler;

			assertParameter($pUsername);
			assertParameter($pPassword);
			assertParameter($pSignature);

			if ($SQLQueryHandler->accountExists($pUsername)){
				$lQueryResult = $SQLQueryHandler->updateUserAccount($pUsername, $pPassword, $pSignature);
				return "<accounts message=\"Updated account {$pUsername}\" />";
			}else{
				$lQueryResult = $SQLQueryHandler->insertNewUserAccount($pUsername, $pPassword, $pSignature);
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

	}// end function createUser()

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
