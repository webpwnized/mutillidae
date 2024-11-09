<?php

/* Determine the root of the entire project.
 * Recall this file is in the "includes" folder so its "2 levels deep". */
if (!defined('__SITE_ROOT__')){if (!defined('__SITE_ROOT__')){define('__SITE_ROOT__', dirname(dirname(__FILE__)));}}

class SQLQueryHandler {
	protected $encodeOutput = false;
	protected $stopSQLInjection = false;
	protected $mLimitOutput = false;
	protected $mSecurityLevel = 0;

	// private objects
	protected $mMySQLHandler = null;
	protected $mEncoder = null;

	private function doSetSecurityLevel($pSecurityLevel){
		$this->mSecurityLevel = $pSecurityLevel;

		switch ($this->mSecurityLevel){
			default: // Default case: This code is insecure, we are not encoding output
	   		case "0": // This code is insecure, we are not encoding output
			case "1": // This code is insecure, we are not encoding output
				$this->encodeOutput = false;
				$this->stopSQLInjection = false;
				$this->mLimitOutput = false;
	   		break;

			case "2":
			case "3":
			case "4":
	   		case "5": // This code is fairly secure
	  			// If we are secure, then we encode all output.
	   			$this->encodeOutput = true;
	   			$this->stopSQLInjection = true;
	   			$this->mLimitOutput = true;
	   		break;
	   	}// end switch
	}// end function

	private function generateClientID($length = 16 /* 16 bytes = 128 bits */){
		// Generates a secure 16-byte token for use as a Client ID
		// The token is generated using a cryptographically secure pseudorandom number generator
		// The token is then converted to hexadecimal format
		// The token will be 32 characters long
		return bin2hex(random_bytes($length));
	}
	
	private function generateClientSecret($length = 32 /* 32 bytes = 256 bits */){
		// Generates a secure 32-byte token for use in API calls
		// The token is generated using a cryptographically secure pseudorandom number generator
		// The token is then converted to hexadecimal format
		// The token will be 64 characters long
		return bin2hex(random_bytes($length));
	}

	public function __construct($pSecurityLevel = 0) {
		// Ensure the provided level is valid; fall back to 0 if it's not.
		if (!is_int($pSecurityLevel) || $pSecurityLevel < 0 || $pSecurityLevel > 5) {
			$pSecurityLevel = 0;
		}

		$this->doSetSecurityLevel($pSecurityLevel);

		//initialize encoder
		require_once __SITE_ROOT__.'/classes/EncodingHandler.php';
		$this->mEncoder = new EncodingHandler();

		/* Initialize MySQL Connection handler */
		require_once 'MySQLHandler.php';
		$this->mMySQLHandler = new MySQLHandler($pSecurityLevel);
		$this->mMySQLHandler->connectToDefaultDatabase();

	}// end function

	public function setSecurityLevel($pSecurityLevel){
		$this->doSetSecurityLevel($pSecurityLevel);
		$this->mMySQLHandler->setSecurityLevel($pSecurityLevel);
	}// end function

	public function getSecurityLevel(){
		return $this->mSecurityLevel;
	}// end function

	public function affected_rows(){
		return $this->mMySQLHandler->affected_rows();
	}//end function

	/* **************************************************************
	 * BEGIN: Queries 												*
	 ****************************************************************/
	public function escapeDangerousCharacters($pData){
	    return $this->mMySQLHandler->escapeDangerousCharacters($pData);
	}

	public function getSecurityLevelFromDB() {
		// Query to retrieve the security level from the row with id = 1
		$lQueryString = "SELECT level FROM security_level WHERE id = 1";
	
		// Execute the query
		$lQueryResult = $this->mMySQLHandler->executeQuery($lQueryString);
	
		// Check if the query returned a valid result
		if ($lQueryResult && $lQueryResult->num_rows > 0) {
			$lRow = $lQueryResult->fetch_assoc();
			return (int) $lRow['level'];  // Return the level as an integer
		} else {
			return null;  // Return null if the row does not exist
		}
	} // end function getSecurityLevelFromDB
	
	public function setSecurityLevelInDB($pLevel) {
		if ($pLevel < 0 || $pLevel > 5) {
			throw new InvalidArgumentException("Security level must be between 0 and 5.");
		}
	
		$safeLevel = (int) $pLevel;
		$lQueryString = "UPDATE security_level SET level = $safeLevel WHERE id = 1";
		$this->mMySQLHandler->executeQuery($lQueryString);
	
		// Ensure the row was actually updated
		return $this->mMySQLHandler->affected_rows() > 0;
	} // end function setSecurityLevelInDB

	public function getPageHelpTexts($pPageName){

		if ($this->stopSQLInjection){
			$pPageName = $this->mMySQLHandler->escapeDangerousCharacters($pPageName);
		}// end if

		$lQueryString  = "
			SELECT CONCAT(
				'<div class=\"help-text\">
					<img src=\"./images/bullet_black.png\" style=\"vertical-align: middle;\" />',
				help_text,
				'</div>'
			) AS help_text
			FROM page_help
			INNER JOIN help_texts
			ON page_help.help_text_key = help_texts.help_text_key
			WHERE page_help.page_name = '" . $pPageName . "' " .
			"ORDER BY page_help.order_preference";

		return $this->mMySQLHandler->executeQuery($lQueryString);
	}//end public function getPageHelpTexts

	public function getPageLevelOneHelpIncludeFiles($pPageName){

		if ($this->stopSQLInjection){
			$pPageName = $this->mMySQLHandler->escapeDangerousCharacters($pPageName);
		}// end if

		$lQueryString  = "
			SELECT	level_1_help_include_files.level_1_help_include_file_key,
					level_1_help_include_files.level_1_help_include_file_description
			FROM page_help
			INNER JOIN level_1_help_include_files
			ON 	page_help.help_text_key =
				level_1_help_include_files.level_1_help_include_file_key
			WHERE page_help.page_name = '" . $pPageName . "' " .
			"ORDER BY page_help.order_preference";

		return $this->mMySQLHandler->executeQuery($lQueryString);
	}//end public function getPageLevelOneHelpIncludeFiles

	public function getLevelOneHelpIncludeFile($pIncludeFileKey){

		if ($this->stopSQLInjection){
			$pIncludeFileKey = $this->mMySQLHandler->escapeDangerousCharacters($pIncludeFileKey);
		}// end if

		$lQueryString  = "
			SELECT	level_1_help_include_files.level_1_help_include_file,
					level_1_help_include_files.level_1_help_include_file_description
			FROM level_1_help_include_files
			WHERE level_1_help_include_files.level_1_help_include_file_key = " . $pIncludeFileKey;

		return $this->mMySQLHandler->executeQuery($lQueryString);
	}//end public function getPageLevelOneHelpIncludeFiles

	public function deleteCapturedData(){
			$lQueryString = "TRUNCATE TABLE captured_data";
			return $this->mMySQLHandler->executeQuery($lQueryString);
	}//end public function deleteCapturedData

	public function getCapturedData(){
		$lQueryString = "
			SELECT ip_address, hostname, port, user_agent_string, referrer, data, capture_date
			FROM captured_data
			ORDER BY capture_date DESC";

		if ($this->mLimitOutput){
	    	$lQueryString .= " LIMIT 20";
	    }// end if

		return $this->mMySQLHandler->executeQuery($lQueryString);
	}//end public function getCapturedData()

	public function insertVoteIntoUserPoll(/*Text*/ $pToolName, /*Text*/ $pUserName){

		if ($this->stopSQLInjection){
			$pToolName = $this->mMySQLHandler->escapeDangerousCharacters($pToolName);
			$pUserName = $this->mMySQLHandler->escapeDangerousCharacters($pUserName);
		}// end if

		$lQueryString  = "
			INSERT INTO user_poll_results(tool_name, username, date) VALUES ('".
				$pToolName . "', '".
				$pUserName  . "', " .
				" now() );";

		return $this->mMySQLHandler->executeQuery($lQueryString);
	}//end public function insertVoteIntoUserPoll

	public function getUserPollVotes(){

		$lQueryString  = "
			SELECT tool_name, COUNT(tool_name) as tool_count
			FROM user_poll_results
			GROUP BY tool_name";

		return $this->mMySQLHandler->executeQuery($lQueryString);
	}//end public function insertVoteIntoUserPoll

	public function insertBlogRecord($pBloggerName, $pBlogEntry){

		if ($this->stopSQLInjection){
			$pBloggerName = $this->mMySQLHandler->escapeDangerousCharacters($pBloggerName);
			$pBlogEntry = $this->mMySQLHandler->escapeDangerousCharacters($pBlogEntry);
		}// end if

		$lQueryString  = "
			INSERT INTO blogs_table(blogger_name, comment, date) VALUES ('".
				$pBloggerName . "', '".
				$pBlogEntry  . "', " .
				" now() )";

		return $this->mMySQLHandler->executeQuery($lQueryString);
	}//end public function insertBlogRecord

	public function getBlogRecord($pBloggerName){

		if ($this->stopSQLInjection){
			$pBloggerName = $this->mMySQLHandler->escapeDangerousCharacters($pBloggerName);
		}// end if

		$lQueryString = "
			SELECT * FROM blogs_table
			WHERE blogger_name like '{$pBloggerName}%'
			ORDER BY date DESC
			LIMIT 0 , 100";

		return $this->mMySQLHandler->executeQuery($lQueryString);
	}//end public function getBlogRecord

	public function getPenTestTool($pPostedToolID){
   		/*
  		 * Note: While escaping works ok in some case, it is not the best defense.
 		 * Using stored procedures is a much stronger defense.
 		 */
		if ($this->stopSQLInjection){
			$pPostedToolID = $this->mMySQLHandler->escapeDangerousCharacters($pPostedToolID);
		}// end if

		if ($pPostedToolID != "c84326e4-7487-41d3-91fd-88280828c756"){
			$lWhereClause = " WHERE tool_id = '".$pPostedToolID."';";
		}else{
			$lWhereClause = ";";
		}// end if

		$lQueryString  = "
			SELECT	tool_id, tool_name, phase_to_use, tool_type, comment
			FROM 	pen_test_tools" .
			$lWhereClause;
		return $this->mMySQLHandler->executeQuery($lQueryString);
	}//end public function getPenTestTool

	public function getPenTestTools(){
		/* Note: No possibility of SQL injection because the query
		 * is static.
		*/
		$lQueryString  = "SELECT tool_id, tool_name FROM pen_test_tools;";
		return $this->mMySQLHandler->executeQuery($lQueryString);
	}// end function getPenTestTools

	public function getHitLogEntries(){
	/* Note: No possibility of SQL injection because the query
		* is static.
	*/
		$lLimitString = "";
		if ($this->mLimitOutput){
		$lLimitString .= " LIMIT 20";
	}// end if

	$lQueryString  = "SELECT * FROM `hitlog` ORDER BY date DESC".$lLimitString.";";
	return $this->mMySQLHandler->executeQuery($lQueryString);
	}// end function getHitLogEntries

	public function getYouTubeVideo($pRecordIdentifier){
	/*
	* Note: While escaping works ok in some case, it is not the best defense.
		* Using stored procedures is a much stronger defense.
	*/
	if ($this->stopSQLInjection){
		$pRecordIdentifier = $this->mMySQLHandler->escapeDangerousCharacters($pRecordIdentifier);
	}// end if

	$lQueryString  = "SELECT identificationToken, title FROM youTubeVideos WHERE recordIndetifier = " .	$pRecordIdentifier . ";";
	$lQueryResult = $this->mMySQLHandler->executeQuery($lQueryString);
	return $lQueryResult->fetch_object();
	}//end public function getYouTubeVideo

	public function getUsernames(){

		$lQueryString  = "SELECT username FROM accounts;";

		return $this->mMySQLHandler->executeQuery($lQueryString);
	}//end public function getUsernames

	public function accountExists($pUsername){

		if ($this->stopSQLInjection){
			$pUsername = $this->mMySQLHandler->escapeDangerousCharacters($pUsername);
		}// end if

		$lQueryString =
		"SELECT username FROM accounts WHERE username='".$pUsername."';";

		$lQueryResult = $this->mMySQLHandler->executeQuery($lQueryString);

		if (isset($lQueryResult->num_rows)){
			return $lQueryResult->num_rows > 0;
		}else{
			return false;
		}// end if

	}//end public function getUsernames

	public function authenticateAccount($pUsername, $pPassword){

		if ($this->stopSQLInjection){
			$pUsername = $this->mMySQLHandler->escapeDangerousCharacters($pUsername);
			$pPassword = $this->mMySQLHandler->escapeDangerousCharacters($pPassword);
		}// end if

		$lQueryString =
			"SELECT username ".
			"FROM accounts ".
			"WHERE username='".$pUsername."' ".
			"AND password='".$pPassword."';";

		$lQueryResult = $this->mMySQLHandler->executeQuery($lQueryString);

		if (isset($lQueryResult->num_rows)){
			return $lQueryResult->num_rows > 0;
		}else{
			return false;
		}// end if

	}//end public function getUsernames

	public function getNonSensitiveAccountInformation($pUsername){
		/*
		 * Note: While escaping works ok in some case, it is not the best defense.
		* Using stored procedures is a much stronger defense.
		*/
		if ($this->stopSQLInjection){
			$pUsername = $this->mMySQLHandler->escapeDangerousCharacters($pUsername);
		}// end if

		$lQueryString =
		"SELECT username, firstname, lastname, mysignature
		 FROM accounts
		 WHERE username='".$pUsername."'";

		return $this->mMySQLHandler->executeQuery($lQueryString);
	}//end public function getNonSensitiveAccountInformation

	public function getUserAccountByID($pUserID){

		if ($this->stopSQLInjection){
			$pUserID = $this->mMySQLHandler->escapeDangerousCharacters($pUserID);
		}// end if

		$lQueryString = "SELECT * FROM accounts WHERE cid='" . $pUserID . "'";

		return $this->mMySQLHandler->executeQuery($lQueryString);
	}//end public function getUserAccountByID

	public function getUserAccount($pUsername, $pPassword){
   		/*
  		 * Note: While escaping works ok in some case, it is not the best defense.
 		 * Using stored procedures is a much stronger defense.
 		 */

		if ($this->stopSQLInjection){
			$pUsername = $this->mMySQLHandler->escapeDangerousCharacters($pUsername);
			$pPassword = $this->mMySQLHandler->escapeDangerousCharacters($pPassword);
		}// end if

		$lQueryString =
			"SELECT * FROM accounts
			WHERE username='".$pUsername.
			"' AND password='".$pPassword."'";

		return $this->mMySQLHandler->executeQuery($lQueryString);
	}//end public function getUserAccount

	public function getAccountByClientId($pClientId){
		/*
		 * Vulnerability: Using direct user input in SQL without escaping or parameterization,
		 * making it vulnerable to SQL injection.
		 */
		if ($this->stopSQLInjection) {
			$pClientId = $this->mMySQLHandler->escapeDangerousCharacters($pClientId);
		}
	
		$lQueryString = "SELECT * FROM accounts WHERE client_id='" . $pClientId . "'";
		return $this->mMySQLHandler->executeQuery($lQueryString);
	}
	
	public function authenticateByClientCredentials($pClientId, $pClientSecret){
		/*
		 * Vulnerability: Directly using user-supplied client_id and client_secret without proper escaping,
		 * making this function vulnerable to SQL injection.
		 */
		if ($this->stopSQLInjection) {
			$pClientId = $this->mMySQLHandler->escapeDangerousCharacters($pClientId);
			$pClientSecret = $this->mMySQLHandler->escapeDangerousCharacters($pClientSecret);
		}
	
		$lQueryString =
			"SELECT COUNT(*) AS count FROM accounts " .
			"WHERE client_id='" . $pClientId . "' " .
			"AND client_secret='" . $pClientSecret . "'";
		
		$result = $this->mMySQLHandler->executeQuery($lQueryString);
		$row = $result->fetch_assoc();
	
		return $row['count'] > 0;
	}
	
	/* -----------------------------------------
	 * Insert Queries
	 * ----------------------------------------- */
	public function insertNewUserAccount($pUsername, $pPassword, $pFirstName, $pLastName, $pSignature){
		/*
		 * Note: While escaping works ok in some cases, it is not the best defense.
		 * Using stored procedures is a much stronger defense.
		 */
		if ($this->stopSQLInjection){
			$pUsername = $this->mMySQLHandler->escapeDangerousCharacters($pUsername);
			$pPassword = $this->mMySQLHandler->escapeDangerousCharacters($pPassword);
			$pFirstName = $this->mMySQLHandler->escapeDangerousCharacters($pFirstName);
			$pLastName = $this->mMySQLHandler->escapeDangerousCharacters($pLastName);
			$pSignature = $this->mMySQLHandler->escapeDangerousCharacters($pSignature);
		}// end if
	
		$lClientID = $this->generateClientID();
		$lClientSecret = $this->generateClientSecret();
			
		$lQueryString = "INSERT INTO accounts (username, password, firstname, lastname, mysignature, client_id, client_secret) VALUES ('" .
			$pUsername ."', '" .
			$pPassword . "', '" .
			$pFirstName . "', '" .
			$pLastName . "', '" .
			$pSignature . "', '" .
			$lClientID . "', '" .
			$lClientSecret .
			"')";
	
		if ($this->mMySQLHandler->executeQuery($lQueryString)){
			return $this->mMySQLHandler->affected_rows();
		}else{
			return 0;
		}
	}//end function insertNewUserAccount
	
	public function insertCapturedData(
		$pClientIP,
		$pClientHostname,
		$pClientPort,
		$pClientUserAgentString,
		$pClientReferrer,
		$pCapturedData
	){
		if ($this->stopSQLInjection){
			$pClientIP = $this->mMySQLHandler->escapeDangerousCharacters($pClientIP);
			$pClientHostname = $this->mMySQLHandler->escapeDangerousCharacters($pClientHostname);
			$pClientPort = $this->mMySQLHandler->escapeDangerousCharacters($pClientPort);
			$pClientUserAgentString = $this->mMySQLHandler->escapeDangerousCharacters($pClientUserAgentString);
			$pClientReferrer = $this->mMySQLHandler->escapeDangerousCharacters($pClientReferrer);
		}// end if

		/* Always encode to prevent captured data from breaking query */
		$pCapturedData = $this->mMySQLHandler->escapeDangerousCharacters($pCapturedData);

		$lQueryString =
			"INSERT INTO captured_data(" .
				"ip_address, hostname, port, user_agent_string, referrer, data, capture_date" .
			") VALUES ('".
				$pClientIP . "', '".
				$pClientHostname . "', '".
				$pClientPort . "', '".
				$pClientUserAgentString . "', '".
				$pClientReferrer . "', '".
				$pCapturedData . "', ".
				" now()" .
			")";

		return $this->mMySQLHandler->executeQuery($lQueryString);
	}//end public function insertBlogRecord

	/* -----------------------------------------
	 * Update Queries
	* ----------------------------------------- */
	public function updateUserAccount($pUsername, $pPassword, $pFirstName, $pLastName, $pSignature, $pUpdateClientID, $pUpdateClientSecret){
		/*
		 * Note: While escaping works ok in some cases, it is not the best defense.
		 * Using stored procedures is a much stronger defense.
		 */
		if ($this->stopSQLInjection){
			$pUsername = $this->mMySQLHandler->escapeDangerousCharacters($pUsername);
			$pPassword = $this->mMySQLHandler->escapeDangerousCharacters($pPassword);
			$pFirstName = $this->mMySQLHandler->escapeDangerousCharacters($pFirstName);
			$pLastName = $this->mMySQLHandler->escapeDangerousCharacters($pLastName);
			$pSignature = $this->mMySQLHandler->escapeDangerousCharacters($pSignature);
		}
	
		if ($pUpdateClientID){
			$lClientID = $this->generateClientID();
		} else {
			$lClientID = "";
		}
	
		if ($pUpdateClientSecret){
			$lClientSecret = $this->generateClientSecret();
		} else {
			$lClientSecret = "";
		}
	
		$lQueryString = 
			"UPDATE accounts
			SET
				username = '".$pUsername."',
				password = '".$pPassword."',
				firstname = '".$pFirstName."',
				lastname = '".$pLastName."',
				mysignature = '".$pSignature."'";
	
		if ($pUpdateClientID){
			$lQueryString .= "," .
				"client_id = '".$lClientID."'";
		}
	
		if ($pUpdateClientSecret){
			$lQueryString .= "," .
				"client_secret = '".$lClientSecret."'";
		}
	
		$lQueryString .= " WHERE username = '".$pUsername."';";
	
		if ($this->mMySQLHandler->executeQuery($lQueryString)){
			return $this->mMySQLHandler->affected_rows();
		} else {
			return 0;
		}
	}//end function updateUserAccount

	/* -----------------------------------------
	 * Delete Queries
	* ----------------------------------------- */
	public function deleteUser($pUsername){
		if ($this->stopSQLInjection){
			$pUsername = $this->mMySQLHandler->escapeDangerousCharacters($pUsername);
		}// end if

		$lQueryString  = "DELETE FROM accounts WHERE username = '".$pUsername."';";
		if ($this->mMySQLHandler->executeQuery($lQueryString)){
			return $this->mMySQLHandler->affected_rows();
		}else{
			return 0;
		}
	}// end function deleteUser

	/* -----------------------------------------
	 * Truncate Queries
	* ----------------------------------------- */
	public function truncateHitLog(){
		/* Note: No possibility of SQL injection because the query is static.*/
		$lQueryString  = "TRUNCATE TABLE hitlog;";
		return $this->mMySQLHandler->executeQuery($lQueryString);
	}// end function truncateHitLog

}// end class