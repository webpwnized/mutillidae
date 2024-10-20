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

		switch($lVerb){
			case "GET":
				if(isset($_GET['username'])){
					/* Example hack: username=jeremy'+union+select+concat('The+password+for+',username,'+is+',+password),mysignature+from+accounts+--+ */
					$lAccountUsername = $_GET['username'];

					if ($lAccountUsername == "*"){
						/* List all accounts */
						$lQueryResult = $SQLQueryHandler->getUsernames();
					}else{
						/* lookup user */
						$lQueryResult = $SQLQueryHandler->getNonSensitiveAccountInformation($lAccountUsername);
					}// end if

					if ($lQueryResult->num_rows > 0){
						echo "Result: {Accounts: {".jsonEncodeQueryResults($lQueryResult)."}}";
					}else{
						echo "Result: {User '".$lAccountUsername."' does not exist}";
					}// end if

				}else{

					// Enhanced help content for GET request without parameters
					echo "
					<a href='//mutillidae.localhost/index.php' style='cursor:pointer;text-decoration:none;font-weight:bold;'/>Back to Home Page</a>
					<br /><br /><br />

					<div>
						<h2>Welcome to the User Account Web Service</h2>
						<p>This service allows you to <strong>create, read, update, and delete</strong> user accounts using various HTTP methods.</p>
						<p><strong>Note:</strong> This service is vulnerable to SQL injection at security level 0. Be cautious when testing or exploring its functionality.</p>
					</div>

					<hr />

					<h3>Supported HTTP Methods</h3>

					<h4>1. GET (Retrieve Data)</h4>
					<p>Use GET requests to retrieve information about one or more accounts.</p>
					<p><strong>Optional Parameter:</strong> <code>username</code> (as a URL parameter)</p>
					<ul>
						<li>If <code>username=* </code>, all accounts will be returned.</li>
						<li>If <code>username</code> is specified, details for that user will be returned.</li>
					</ul>
					<strong>Examples:</strong><br />
					<ul>
						<li>Retrieve a specific user: <a href='//mutillidae.localhost/webservices/rest/ws-user-account.php?username=adrian'>/ws-user-account.php?username=adrian</a></li>
						<li>Retrieve all users: <a href='//mutillidae.localhost/webservices/rest/ws-user-account.php?username=*'>/ws-user-account.php?username=*</a></li>
					</ul>

					<hr />

					<h4>2. POST (Create New Account)</h4>
					<p>Use POST requests to create a new user account.</p>
					<p><strong>Required Parameters (POST body):</strong></p>
					<ul>
						<li><code>username</code>: The username for the new account</li>
						<li><code>password</code>: The password for the new account</li>
						<li><code>firstname</code>: User's first name</li>
						<li><code>lastname</code>: User's last name</li>
					</ul>
					<p><strong>Optional Parameter:</strong> <code>signature</code> (User's signature)</p>
					<strong>Example:</strong><br />
					<pre>
POST /webservices/rest/ws-user-account.php HTTP/1.1
Host: mutillidae.localhost
Content-Type: application/x-www-form-urlencoded

username=john&password=pass123&firstname=John&lastname=Doe&signature=JDoe
					</pre>

					<hr />

					<h4>3. PUT (Create or Update Account)</h4>
					<p>Use PUT requests to <strong>create or update</strong> an existing user account.</p>
					<p><strong>Required Parameters (POST body):</strong> Same as POST</p>
					<p>If the account exists, it will be updated. If not, a new account will be created.</p>
					<strong>Example:</strong><br />
					<pre>
PUT /webservices/rest/ws-user-account.php HTTP/1.1
Host: mutillidae.localhost
Content-Type: application/x-www-form-urlencoded

username=john&password=newpass123&firstname=John&lastname=Doe&signature=JDoeUpdated
					</pre>

					<hr />

					<h4>4. DELETE (Remove Account)</h4>
					<p>Use DELETE requests to delete an existing user account.</p>
					<p><strong>Required Parameters (POST body):</strong></p>
					<ul>
						<li><code>username</code>: The username of the account to be deleted</li>
						<li><code>password</code>: The password for the account</li>
					</ul>
					<strong>Example:</strong><br />
					<pre>
DELETE /webservices/rest/ws-user-account.php HTTP/1.1
Host: mutillidae.localhost
Content-Type: application/x-www-form-urlencoded

username=john&password=newpass123
					</pre>

					<hr />

					<h4>Example Exploits (SQL Injection)</h4>
					<p>This service is vulnerable to SQL injection at security level 0. Example:</p>
					<pre>
GET /webservices/rest/ws-user-account.php?username=jeremy'+union+select+concat('The+password+for+',username,'+is+',password),mysignature+from+accounts+-- HTTP/1.1
Host: mutillidae.localhost

					</pre>

					";
				}// end if

			break;
			case "POST"://create

				$lAccountUsername = getPOSTParameter("username", true);
				$lAccountPassword = getPOSTParameter("password", true);
				$lAccountFirstName = getPOSTParameter("firstname", true);
				$lAccountLastName = getPOSTParameter("lastname", true);
				$lAccountSignature = getPOSTParameter("signature", false);

				if ($SQLQueryHandler->accountExists($lAccountUsername)){
					echo "Result: {Account ".$lAccountUsername." already exists}";
				}else{
					$lQueryResult = $SQLQueryHandler->insertNewUserAccount($lAccountUsername, $lAccountPassword, $lAccountFirstName, $lAccountLastName, $lAccountSignature);
					echo "Result: {Inserted account ".$lAccountUsername."}";
				}// end if

			break;
			case "PUT":	//create or update
				/* $_POST array is not auto-populated for PUT method. Parse input into an array. */
				populatePOSTSuperGlobal();

				$lAccountUsername = getPOSTParameter("username", true);
				$lAccountPassword = getPOSTParameter("password", true);
				$lAccountFirstName = getPOSTParameter("firstname", true);
				$lAccountLastName = getPOSTParameter("lastname", true);
				$lAccountSignature = getPOSTParameter("signature", false);

				if ($SQLQueryHandler->accountExists($lAccountUsername)){
					$lQueryResult = $SQLQueryHandler->updateUserAccount($lAccountUsername, $lAccountPassword, $lAccountFirstName, $lAccountLastName, $lAccountSignature, false);
					echo "Result: {Updated account ".$lAccountUsername.". ".$lQueryResult." rows affected.}";
				}else{
					$lQueryResult = $SQLQueryHandler->insertNewUserAccount($lAccountUsername, $lAccountPassword, $lAccountFirstName, $lAccountLastName, $lAccountSignature);
					echo "Result: {Inserted account ".$lAccountUsername.". ".$lQueryResult." rows affected.}";
				}// end if

			break;
			case "DELETE":
				/* $_POST array is not auto-populated for DELETE method. Parse input into an array. */
				populatePOSTSuperGlobal();

				$lAccountUsername = getPOSTParameter("username", TRUE);
				$lAccountPassword = getPOSTParameter("password", TRUE);

				if($SQLQueryHandler->accountExists($lAccountUsername)){

					if($SQLQueryHandler->authenticateAccount($lAccountUsername,$lAccountPassword)){
						$lQueryResult = $SQLQueryHandler->deleteUser($lAccountUsername);

						if ($lQueryResult){
							echo "Result: {Deleted account ".$lAccountUsername."}";
						}else{
							echo "Result: {Attempted to delete account ".$lAccountUsername." but result returned was ".$lQueryResult."}";
						}//end if

					}else{
						echo "Result: {Could not authenticate account ".$lAccountUsername.". Password incorrect.}";
					}// end if authenticateAccount

				} else {
					echo "Result: {User '".$lAccountUsername."' does not exist}";
				}// end if isset $lQueryResult

			break;
			default:
				throw new UnsupportedHttpVerbException($lVerb);
			break;
		}// end switch

	} catch (Exception $e) {
		echo $CustomErrorHandler->FormatErrorJSON($e, "Unable to process request to web service ws-user-account");
	}// end try

?>
