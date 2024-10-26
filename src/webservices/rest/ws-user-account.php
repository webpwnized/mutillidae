<?php
	/*  --------------------------------
	 *  We use the session on this page
	 *  --------------------------------*/
    if (session_status() == PHP_SESSION_NONE){
        session_start();
    }// end if

	/* ----------------------------------------
	 *	initialize security level to "insecure"
	 * ----------------------------------------*/
    if (!isset($_SESSION["security-level"])){
        $_SESSION["security-level"] = 0;
    }// end if

	/* ------------------------------------------
	 * Constants used in application
	 * ------------------------------------------ */
	require_once '../../includes/constants.php';
	require_once '../../includes/minimum-class-definitions.php';

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
		$lAccountUsername = "";
		$lVerb = $_SERVER['REQUEST_METHOD'];

		// Get the origin of the request
		$lOrigin = isset($_SERVER['HTTP_ORIGIN']) ? $_SERVER['HTTP_ORIGIN'] : '*';

		header('Access-Control-Allow-Origin: ' . $lOrigin); // Allow requests from any origin domain
		header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS'); // Allowed methods
		header('Access-Control-Allow-Headers: Content-Type, Authorization'); // Specify allowed headers	

		switch($lVerb){
			case "GET":
				if(isset($_GET['username'])){
					/* Example hack: username=jeremy'+union+select+concat('The+password+for+',username,'+is+',+password),mysignature+from+accounts+--+ */

					$lAccountUsername = $_GET['username'] ?? '';
					
					// Fetch data based on the username
					if ($lAccountUsername === "*") {
						// List all accounts
						$lQueryResult = $SQLQueryHandler->getUsernames();
					} else {
						// Lookup specific user
						$lQueryResult = $SQLQueryHandler->getNonSensitiveAccountInformation($lAccountUsername);
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
						$lArrayResponse['Result'] = "User '$lAccountUsername' does not exist";
					}
					
					// Output the response as JSON with the correct headers
					header('Content-Type: application/json');
					echo json_encode($lArrayResponse, JSON_PRETTY_PRINT);

				}else{
					header('Content-Type: text/html');

					// Enhanced help content for GET request without parameters
					echo '
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Account Web Service Documentation</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
        }
        a {
            text-decoration: none;
            font-weight: bold;
        }
        pre {
            background-color: #f4f4f4;
            padding: 10px;
            border-radius: 5px;
        }
    </style>
</head>
<body>

<a href="//mutillidae.localhost/index.php">Back to Home Page</a>
<br><br><br>

<div>
    <h2>Welcome to the User Account Web Service</h2>
    <p>This service allows you to <strong>create, read, update, and delete</strong> user accounts using various HTTP methods.</p>
    <p><strong>Note:</strong> This service is vulnerable to SQL injection at security level 0. Be cautious when testing or exploring its functionality.</p>
</div>

<hr>

<h3>Supported HTTP Methods</h3>

<h4>1. GET (Retrieve Data)</h4>
<p>Use GET requests to retrieve information about one or more accounts.</p>
<p><strong>Optional Parameter:</strong> <code>username</code> (as a URL parameter)</p>
<ul>
    <li>If <code>username=*</code>, all accounts will be returned.</li>
    <li>If <code>username</code> is specified, details for that user will be returned.</li>
</ul>
<strong>Examples:</strong><br>
<ul>
    <li>Retrieve a specific user: <a href="//mutillidae.localhost/webservices/rest/ws-user-account.php?username=adrian">/ws-user-account.php?username=adrian</a></li>
    <li>Retrieve all users: <a href="//mutillidae.localhost/webservices/rest/ws-user-account.php?username=*">/ws-user-account.php?username=*</a></li>
</ul>

<hr>

<h4>2. POST (Create New Account)</h4>
<p>Use POST requests to create a new user account.</p>
<p><strong>Required Parameters (POST body):</strong></p>
<ul>
    <li><code>username</code>: The username for the new account</li>
    <li><code>password</code>: The password for the new account</li>
    <li><code>firstname</code>: Users first name</li>
    <li><code>lastname</code>: Users last name</li>
</ul>
<p><strong>Optional Parameter:</strong> <code>signature</code> (Users signature)</p>
<strong>Example:</strong><br>
<pre>
POST /webservices/rest/ws-user-account.php HTTP/1.1
Host: mutillidae.localhost
Content-Type: application/x-www-form-urlencoded

username=john&password=pass123&firstname=John&lastname=Doe&signature=JDoe
</pre>

<hr>

<h4>3. PUT (Create or Update Account)</h4>
<p>Use PUT requests to <strong>create or update</strong> an existing user account.</p>
<p><strong>Required Parameters (POST body):</strong> Same as POST</p>
<p>If the account exists, it will be updated. If not, a new account will be created.</p>
<strong>Example:</strong><br>
<pre>
PUT /webservices/rest/ws-user-account.php HTTP/1.1
Host: mutillidae.localhost
Content-Type: application/x-www-form-urlencoded

username=john&password=newpass123&firstname=John&lastname=Doe&signature=JDoeUpdated
</pre>

<hr>

<h4>4. DELETE (Remove Account)</h4>
<p>Use DELETE requests to delete an existing user account.</p>
<p><strong>Required Parameters (POST body):</strong></p>
<ul>
    <li><code>username</code>: The username of the account to be deleted</li>
    <li><code>password</code>: The password for the account</li>
</ul>
<strong>Example:</strong><br>
<pre>
DELETE /webservices/rest/ws-user-account.php HTTP/1.1
Host: mutillidae.localhost
Content-Type: application/x-www-form-urlencoded

username=john&password=newpass123
</pre>

<hr>

<h4>Example Exploits (SQL Injection)</h4>
<p>This service is vulnerable to SQL injection at security level 0. Example:</p>
<pre>
GET /webservices/rest/ws-user-account.php?username=jeremy'+union+select+concat('The+password+for+',username,'+is+',password),mysignature+from+accounts+-- HTTP/1.1
Host: mutillidae.localhost
</pre>

</body>
</html>
';
				}// end if

			break;
			case "POST"://create

				// Fetch POST parameters
				$lAccountUsername = getPOSTParameter("username", true);
				$lAccountPassword = getPOSTParameter("password", true);
				$lAccountFirstName = getPOSTParameter("firstname", true);
				$lAccountLastName = getPOSTParameter("lastname", true);
				$lAccountSignature = getPOSTParameter("signature", false);
				
				// Prepare response array
				$lArrayResponse = [];
				
				if ($SQLQueryHandler->accountExists($lAccountUsername)) {
					// If the account already exists
					$lArrayResponse['Result'] = "Account '$lAccountUsername' already exists";
					$lArrayResponse['Success'] = false;
				} else {
					// Insert new account and set the response message
					$lQueryResult = $SQLQueryHandler->insertNewUserAccount(
						$lAccountUsername,
						$lAccountPassword,
						$lAccountFirstName,
						$lAccountLastName,
						$lAccountSignature
					);
				
					if ($lQueryResult) {
						$lArrayResponse['Result'] = "Inserted account '$lAccountUsername'";
						$lArrayResponse['Success'] = true;
					} else {
						$lArrayResponse['Result'] = "Failed to insert account '$lAccountUsername'";
						$lArrayResponse['Success'] = false;
					}
				}
				
				// Set the response header to JSON and output the response
				header('Content-Type: application/json');
				echo json_encode($lArrayResponse, JSON_PRETTY_PRINT);

			break;
			case "PUT":	//create or update
				/* $_POST array is not auto-populated for PUT method. Parse input into an array. */
				populatePOSTSuperGlobal();

				$lAccountUsername = getPOSTParameter("username", true);
				$lAccountPassword = getPOSTParameter("password", true);
				$lAccountFirstName = getPOSTParameter("firstname", true);
				$lAccountLastName = getPOSTParameter("lastname", true);
				$lAccountSignature = getPOSTParameter("signature", false);

				// Initialize the response array
				$lArrayResponse = [];

				if ($SQLQueryHandler->accountExists($lAccountUsername)) {
					// Update the existing account
					$lQueryResult = $SQLQueryHandler->updateUserAccount(
						$lAccountUsername,
						$lAccountPassword,
						$lAccountFirstName,
						$lAccountLastName,
						$lAccountSignature,
						false
					);

					if ($lQueryResult > 0) {
						$lArrayResponse['Result'] = "Updated account '$lAccountUsername'.";
						$lArrayResponse['RowsAffected'] = $lQueryResult;
						$lArrayResponse['Success'] = true;
					} else {
						$lArrayResponse['Result'] = "No rows were updated for account '$lAccountUsername'.";
						$lArrayResponse['RowsAffected'] = 0;
						$lArrayResponse['Success'] = false;
					}
				} else {
					// Insert a new account
					$lQueryResult = $SQLQueryHandler->insertNewUserAccount(
						$lAccountUsername, 
						$lAccountPassword, 
						$lAccountFirstName, 
						$lAccountLastName, 
						$lAccountSignature
					);

					if ($lQueryResult > 0) {
						$lArrayResponse['Result'] = "Inserted account '$lAccountUsername'.";
						$lArrayResponse['RowsAffected'] = $lQueryResult;
						$lArrayResponse['Success'] = true;
					} else {
						$lArrayResponse['Result'] = "Failed to insert account '$lAccountUsername'.";
						$lArrayResponse['RowsAffected'] = 0;
						$lArrayResponse['Success'] = false;
					}
				}

				// Set the response header to JSON and output the response
				header('Content-Type: application/json');
				echo json_encode($lArrayResponse, JSON_PRETTY_PRINT);

			break;
			case "DELETE":
				/* $_POST array is not auto-populated for DELETE method. Parse input into an array. */
				populatePOSTSuperGlobal();

				// Fetch POST parameters
				$lAccountUsername = getPOSTParameter("username", true);
				$lAccountPassword = getPOSTParameter("password", true);
				
				// Initialize the response array
				$lArrayResponse = [];
				
				if ($SQLQueryHandler->accountExists($lAccountUsername)) {
					// Check if authentication is successful
					if ($SQLQueryHandler->authenticateAccount($lAccountUsername, $lAccountPassword)) {
						// Attempt to delete the user
						$lQueryResult = $SQLQueryHandler->deleteUser($lAccountUsername);
				
						if ($lQueryResult) {
							// Successful deletion
							$lArrayResponse['Result'] = "Deleted account '$lAccountUsername'.";
							$lArrayResponse['Success'] = true;
						} else {
							// Failed deletion attempt
							$lArrayResponse['Result'] = "Attempted to delete account '$lAccountUsername', but the result returned was '$lQueryResult'.";
							$lArrayResponse['Success'] = false;
						}
					} else {
						// Authentication failed
						$lArrayResponse['Result'] = "Could not authenticate account '$lAccountUsername'. Password incorrect.";
						$lArrayResponse['Success'] = false;
					}
				} else {
					// Account does not exist
					$lArrayResponse['Result'] = "User '$lAccountUsername' does not exist.";
					$lArrayResponse['Success'] = false;
				}
				
				// Set the response header to JSON and output the response
				header('Content-Type: application/json');
				echo json_encode($lArrayResponse, JSON_PRETTY_PRINT);				

			break;
			default:
				throw new UnsupportedHttpVerbException($lVerb);
			break;
		}// end switch

	} catch (Exception $e) {
		echo $CustomErrorHandler->FormatErrorJSON($e, "Unable to process request to web service ws-user-account");
	}// end try

?>
