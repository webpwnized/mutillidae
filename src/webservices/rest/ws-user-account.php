<?php
	/* ------------------------------------------
	 * Documentation:
	 * - Domain: mutillidae.localhost
	 * - Description: This is a RESTful web service for managing user accounts
	 * - Endpoint: /webservices/rest/ws-user-account.php
	 * - CORS Headers:
	 *   - Access-Control-Allow-Origin: * (or specific domains)
	 *   - Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS
	 *   - Access-Control-Allow-Headers: Content-Type, Authorization
	 *   - Expected Response:
	 *     - Status: 200 OK with JSON response
	 *     - If method not allowed: 405 Method Not Allowed with allowed methods in response header
	 * ------------------------------------------ */

	/* ------------------------------------------
	 * Constants used in application
	 * ------------------------------------------ */
	require_once '../../includes/constants.php';
	require_once '../../classes/SQLQueryHandler.php';
	require_once '../../classes/CustomErrorHandler.php';

	class MissingPostParameterException extends Exception {
		public function __construct($parameter) {
			parent::__construct("POST parameter " . $parameter . " is required");
		}
	}

	class UnsupportedHttpVerbException extends Exception {
		public function __construct($verb) {
			parent::__construct("Unsupported HTTP verb: " . $verb);
		}
	}

	function populatePOSTSuperGlobal(){
		$lParameters = array();
		parse_str(file_get_contents('php://input'), $lParameters);
		$_POST = $lParameters + $_POST;
	}// end function populatePOSTArray

	function getPOSTParameter($pParameter, $lRequired){
		if(isset($_POST[$pParameter])){
			return $_POST[$pParameter];
		}else{
			if($lRequired){
				throw new MissingPostParameterException($pParameter);
			}else{
				return "";
			}
		}// end if isset
	}// end function validatePOSTParameter

	function jsonEncodeQueryResults($pQueryResult){
		$lDataRows = array();
		while ($lDataRow = mysqli_fetch_assoc($pQueryResult)) {
			$lDataRows[] = $lDataRow;
		}// end while

		return json_encode($lDataRows);
	}//end function jsonEncodeQueryResults

	try{
		$lContentTypeJSON = "Content-Type: application/json";

		// Initialize the SQL query handler
		$SQLQueryHandler = new SQLQueryHandler(0);
		$lSecurityLevel = $SQLQueryHandler->getSecurityLevelFromDB();
		
		$CustomErrorHandler = new CustomErrorHandler($lSecurityLevel);

		$lUsername = "";
		$lVerb = $_SERVER['REQUEST_METHOD'];

		// Get the origin of the request
		$lOrigin = isset($_SERVER['HTTP_ORIGIN']) ? $_SERVER['HTTP_ORIGIN'] : '*';

		header('Access-Control-Allow-Origin: ' . $lOrigin); // Allow requests from any origin domain
		header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS'); // Allowed methods
		header('Access-Control-Allow-Headers: Content-Type, Authorization'); // Specify allowed headers	

		switch($lVerb){
			case "GET":
				if(isset($_GET['username'])){
					$lUsername = $_GET['username'] ?? '';
					
					// Fetch data based on the username
					if ($lUsername === "*") {
						// List all accounts
						$lQueryResult = $SQLQueryHandler->getUsernames();
					} else {
						// Lookup specific user
						$lQueryResult = $SQLQueryHandler->getNonSensitiveAccountInformation($lUsername);
					}
					
					// Prepare the response
					$lArrayResponse = [];
					if ($lQueryResult->num_rows > 0) {
						// Fetch all results into an array
						$lArrayAccounts = [];
						while ($row = $lQueryResult->fetch_assoc()) {
							$lArrayAccounts[] = $row;
						}
						$lArrayResponse['Result'] = ['Accounts' => $lArrayAccounts];
					} else {
						// User not found message
						$lArrayResponse['Result'] = "User '$lUsername' does not exist";
					}
					
					// Output the response as JSON with the correct headers
					header($lContentTypeJSON);
					$lArrayResponse['SecurityLevel'] = $lSecurityLevel;
					echo json_encode($lArrayResponse, JSON_PRETTY_PRINT);
				} else {
					http_response_code(400);
					header($lContentTypeJSON);
					echo json_encode(["error" => "Username parameter is required", "SecurityLevel" => $lSecurityLevel]);
				}
				break;

			case "POST": // create
				// Fetch POST parameters
				$lUsername = getPOSTParameter("username", true);
				$lAccountPassword = getPOSTParameter("password", true);
				$lAccountFirstName = getPOSTParameter("firstname", true);
				$lAccountLastName = getPOSTParameter("lastname", true);
				$lAccountSignature = getPOSTParameter("signature", false);
				
				// Prepare response array
				$lArrayResponse = [];
				
				if ($SQLQueryHandler->accountExists($lUsername)) {
					// If the account already exists
					$lArrayResponse['Result'] = "Account '$lUsername' already exists";
					$lArrayResponse['Success'] = false;
				} else {
					// Insert new account and set the response message
					$lQueryResult = $SQLQueryHandler->insertNewUserAccount(
						$lUsername,
						$lAccountPassword,
						$lAccountFirstName,
						$lAccountLastName,
						$lAccountSignature
					);
				
					if ($lQueryResult) {
						$lArrayResponse['Result'] = "Inserted account '$lUsername'";
						$lArrayResponse['Success'] = true;
					} else {
						$lArrayResponse['Result'] = "Failed to insert account '$lUsername'";
						$lArrayResponse['Success'] = false;
					}
				}
				
				// Set the response header to JSON and output the response
				header($lContentTypeJSON);
				$lArrayResponse['SecurityLevel'] = $lSecurityLevel;
				echo json_encode($lArrayResponse, JSON_PRETTY_PRINT);
				break;

			case "PUT": // create or update
				/* $_POST array is not auto-populated for PUT method. Parse input into an array. */
				populatePOSTSuperGlobal();

				$lUsername = getPOSTParameter("username", true);
				$lAccountPassword = getPOSTParameter("password", true);
				$lAccountFirstName = getPOSTParameter("firstname", true);
				$lAccountLastName = getPOSTParameter("lastname", true);
				$lAccountSignature = getPOSTParameter("signature", false);

				// Initialize the response array
				$lArrayResponse = [];

				if ($SQLQueryHandler->accountExists($lUsername)) {
					// Update the existing account
					$lQueryResult = $SQLQueryHandler->updateUserAccount(
						$lUsername,
						$lAccountPassword,
						$lAccountFirstName,
						$lAccountLastName,
						$lAccountSignature,
						false
					);

					if ($lQueryResult > 0) {
						$lArrayResponse['Result'] = "Updated account '$lUsername'.";
						$lArrayResponse['RowsAffected'] = $lQueryResult;
						$lArrayResponse['Success'] = true;
					} else {
						$lArrayResponse['Result'] = "No rows were updated for account '$lUsername'.";
						$lArrayResponse['RowsAffected'] = 0;
						$lArrayResponse['Success'] = false;
					}
				} else {
					// Insert a new account
					$lQueryResult = $SQLQueryHandler->insertNewUserAccount(
						$lUsername, 
						$lAccountPassword, 
						$lAccountFirstName, 
						$lAccountLastName, 
						$lAccountSignature
					);

					if ($lQueryResult > 0) {
						$lArrayResponse['Result'] = "Inserted account '$lUsername'.";
						$lArrayResponse['RowsAffected'] = $lQueryResult;
						$lArrayResponse['Success'] = true;
					} else {
						$lArrayResponse['Result'] = "Failed to insert account '$lUsername'.";
						$lArrayResponse['RowsAffected'] = 0;
						$lArrayResponse['Success'] = false;
					}
				}

				// Set the response header to JSON and output the response
				header($lContentTypeJSON);
				$lArrayResponse['SecurityLevel'] = $lSecurityLevel;
				echo json_encode($lArrayResponse, JSON_PRETTY_PRINT);
				break;

			case "DELETE":
				/* $_POST array is not auto-populated for DELETE method. Parse input into an array. */
				populatePOSTSuperGlobal();

				// Fetch POST parameters
				$lUsername = getPOSTParameter("username", true);
				$lAccountPassword = getPOSTParameter("password", true);
				
				// Initialize the response array
				$lArrayResponse = [];
				
				if ($SQLQueryHandler->accountExists($lUsername)) {
					// Check if authentication is successful
					if ($SQLQueryHandler->authenticateAccount($lUsername, $lAccountPassword)) {
						// Attempt to delete the user
						$lQueryResult = $SQLQueryHandler->deleteUser($lUsername);
					
						if ($lQueryResult) {
							// Successful deletion
							$lArrayResponse['Result'] = "Deleted account '$lUsername'.";
							$lArrayResponse['Success'] = true;
						} else {
							// Failed deletion attempt
							$lArrayResponse['Result'] = "Attempted to delete account '$lUsername', but the result returned was '$lQueryResult'.";
							$lArrayResponse['Success'] = false;
						}
					} else {
						// Authentication failed
						$lArrayResponse['Result'] = "Could not authenticate account '$lUsername'. Password incorrect.";
						$lArrayResponse['Success'] = false;
					}
				} else {
					// Account does not exist
					$lArrayResponse['Result'] = "User '$lUsername' does not exist.";
					$lArrayResponse['Success'] = false;
				}
				
				// Set the response header to JSON and output the response
				header($lContentTypeJSON);
				$lArrayResponse['SecurityLevel'] = $lSecurityLevel;
				echo json_encode($lArrayResponse, JSON_PRETTY_PRINT);
				break;

			default:
				http_response_code(405);
				header('Allow: GET, POST, PUT, DELETE, OPTIONS');
				header($lContentTypeJSON);
				echo json_encode(["error" => "Method not allowed", "SecurityLevel" => $lSecurityLevel]);
				break;
		}// end switch

	} catch (Exception $e) {
		http_response_code(500);
		header($lContentTypeJSON);
		echo $CustomErrorHandler->FormatErrorJSON($e, "Unable to process request to web service ws-user-account");
	}// end try

?>
