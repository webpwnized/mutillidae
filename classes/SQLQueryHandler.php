<?php
class SQLQueryHandler {
	protected $encodeOutput = FALSE;
	protected $stopSQLInjection = FALSE;
	protected $mLimitOutput = FALSE;
	protected $mSecurityLevel = 0;

	// private objects
	protected $mMySQLHandler = null;
	protected $mESAPI = null;
	protected $mEncoder = null;

	private function doSetSecurityLevel($pSecurityLevel){
		$this->mSecurityLevel = $pSecurityLevel;

		switch ($this->mSecurityLevel){
	   		case "0": // This code is insecure, we are not encoding output
			case "1": // This code is insecure, we are not encoding output
				$this->encodeOutput = FALSE;
				$this->stopSQLInjection = FALSE;
				$this->mLimitOutput = FALSE;
	   		break;

			case "2":
			case "3":
			case "4":
	   		case "5": // This code is fairly secure
	  			// If we are secure, then we encode all output.
	   			$this->encodeOutput = TRUE;
	   			$this->stopSQLInjection = TRUE;
	   			$this->mLimitOutput = TRUE;
	   		break;
	   	}// end switch
	}// end function

	public function __construct($pPathToESAPI, $pSecurityLevel){

		$this->doSetSecurityLevel($pSecurityLevel);

		//initialize OWASP ESAPI for PHP
		require_once $pPathToESAPI . 'ESAPI.php';
		$this->ESAPI = new ESAPI($pPathToESAPI . 'ESAPI.xml');
		$this->Encoder = $this->ESAPI->getEncoder();

		/* Initialize MySQL Connection handler */
		require_once 'MySQLHandler.php';
		$this->mMySQLHandler = new MySQLHandler($pPathToESAPI, $pSecurityLevel);
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

	public function getPageHelpTexts($pPageName){

		if ($this->stopSQLInjection == TRUE){
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

		if ($this->stopSQLInjection == TRUE){
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

		if ($this->stopSQLInjection == TRUE){
			$pPageName = $this->mMySQLHandler->escapeDangerousCharacters($pIncludeFileKey);
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

		if ($this->mLimitOutput == TRUE){
	    	$lQueryString .= " LIMIT 20";
	    }// end if

		return $this->mMySQLHandler->executeQuery($lQueryString);
	}//end public function getCapturedData()

	public function insertVoteIntoUserPoll(/*Text*/ $pToolName, /*Text*/ $pUserName){

		if ($this->stopSQLInjection == TRUE){
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

		if ($this->stopSQLInjection == TRUE){
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

		if ($this->stopSQLInjection == TRUE){
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
		if ($this->stopSQLInjection == TRUE){
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
		if ($this->mLimitOutput == TRUE){
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
	if ($this->stopSQLInjection == TRUE){
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

		if ($this->stopSQLInjection == TRUE){
			$pUsername = $this->mMySQLHandler->escapeDangerousCharacters($pUsername);
		}// end if

		$lQueryString =
		"SELECT username FROM accounts WHERE username='".$pUsername."';";

		$lQueryResult = $this->mMySQLHandler->executeQuery($lQueryString);

		if (isset($lQueryResult->num_rows)){
			return ($lQueryResult->num_rows > 0);
		}else{
			return FALSE;
		}// end if

	}//end public function getUsernames

	public function authenticateAccount($pUsername, $pPassword){

		if ($this->stopSQLInjection == TRUE){
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
			return ($lQueryResult->num_rows > 0);
		}else{
			return FALSE;
		}// end if

	}//end public function getUsernames

	public function getNonSensitiveAccountInformation($pUsername){
		/*
		 * Note: While escaping works ok in some case, it is not the best defense.
		* Using stored procedures is a much stronger defense.
		*/
		if ($this->stopSQLInjection == TRUE){
			$pUsername = $this->mMySQLHandler->escapeDangerousCharacters($pUsername);
		}// end if

		$lQueryString =
		"SELECT username, mysignature
			FROM accounts
			WHERE username='".$pUsername."'";

		return $this->mMySQLHandler->executeQuery($lQueryString);
	}//end public function getNonSensitiveAccountInformation

	public function getUserAccountByID($pUserID){

		if ($this->stopSQLInjection == TRUE){
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

		if ($this->stopSQLInjection == TRUE){
			$pUsername = $this->mMySQLHandler->escapeDangerousCharacters($pUsername);
			$pPassword = $this->mMySQLHandler->escapeDangerousCharacters($pPassword);
		}// end if

		$lQueryString =
			"SELECT * FROM accounts
			WHERE username='".$pUsername.
			"' AND password='".$pPassword."'";

		return $this->mMySQLHandler->executeQuery($lQueryString);
	}//end public function getUserAccount

	/* -----------------------------------------
	 * Insert Queries
	 * ----------------------------------------- */
	public function insertNewUserAccount($pUsername, $pPassword, $pSignature){
   		/*
  		 * Note: While escaping works ok in some case, it is not the best defense.
 		 * Using stored procedures is a much stronger defense.
 		 */
		if ($this->stopSQLInjection == TRUE){
			$pUsername = $this->mMySQLHandler->escapeDangerousCharacters($pUsername);
			$pPassword = $this->mMySQLHandler->escapeDangerousCharacters($pPassword);
			$pSignature = $this->mMySQLHandler->escapeDangerousCharacters($pSignature);
		}// end if

		$lQueryString = "INSERT INTO accounts (username, password, mysignature) VALUES ('" .
			$pUsername ."', '" .
			$pPassword . "', '" .
			$pSignature .
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
		if ($this->stopSQLInjection == TRUE){
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
	public function updateUserAccount($pUsername, $pPassword, $pSignature){
		/*
		 * Note: While escaping works ok in some case, it is not the best defense.
		* Using stored procedures is a much stronger defense.
		*/
		if ($this->stopSQLInjection == TRUE){
			$pUsername = $this->mMySQLHandler->escapeDangerousCharacters($pUsername);
			$pPassword = $this->mMySQLHandler->escapeDangerousCharacters($pPassword);
			$pSignature = $this->mMySQLHandler->escapeDangerousCharacters($pSignature);
		}// end if

		$lQueryString =
			"UPDATE accounts
			SET
				username = '".$pUsername."',
				password = '".$pPassword."',
				mysignature = '".$pSignature."'
			WHERE
				username = '".$pUsername."';";

		if ($this->mMySQLHandler->executeQuery($lQueryString)){
			return $this->mMySQLHandler->affected_rows();
		}else{
			return 0;
		}
	}//end function updateUserAccount

	/* -----------------------------------------
	 * Delete Queries
	* ----------------------------------------- */
	public function deleteUser($pUsername){
		if ($this->stopSQLInjection == TRUE){
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