<?php

/* Determine the root of the entire project.
 * Recall this file is in the "includes" folder so its "2 levels deep". */
if (!defined('__SITE_ROOT__')){if (!defined('__SITE_ROOT__')){define('__SITE_ROOT__', dirname(dirname(__FILE__)));}}

class LogHandler {
	//default insecure: no output encoding.
	protected $encodeOutput = false;
	protected $stopSQLInjection = false;
	protected $mSecurityLevel = 0;
	protected $mEncoder = null;
	protected $mMySQLHandler = null;

	private function doSetSecurityLevel($pSecurityLevel){
		$this->mSecurityLevel = $pSecurityLevel;

		switch ($this->mSecurityLevel){
			default: // Insecure
	   		case "0": // This code is insecure, we are not encoding output
			case "1": // This code is insecure, we are not encoding output
				$this->encodeOutput = false;

				/* stopSQLInjection is set to true even in
				 * insecure configuration because trying to log
				 * sql injections breaks the log handler which then
				 * breaks the calling page. Since SQL injections are
				 * allowed, we dont want the logger to break and stop
				 * the SQL injection.
				 */
				$this->stopSQLInjection = true;
	   		break;

			case "2":
			case "3":
			case "4":
	   		case "5": // This code is fairly secure
	  			// If we are secure, then we encode all output.
	   			$this->encodeOutput = true;
				$this->stopSQLInjection = true;
	   		break;
	   	}// end switch
	}// end function

	public function __construct($pSecurityLevel){

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

	public function getSecurityLevel($pSecurityLevel){
		return $this->mSecurityLevel;
	}// end function

	public function writeToLog($TransactionDescription){

	    $lUserAgent = "";
	    if(isset($_SERVER['HTTP_USER_AGENT'])){
	        $lUserAgent = $_SERVER['HTTP_USER_AGENT'];
	    }// end if

		if ($this->encodeOutput){
			/* Cross site scripting defense */
   			// encode the entire message following OWASP standards
   			// this is HTML encoding because we are outputting data into HTML
		    $lUserAgent = $this->mEncoder->encodeForHTML($lUserAgent);
		}// end if

		/*Here we are protecting against SQL injection and other types of
		 * database injection.	   				 *
		 *
		 * Note: This is fairly secure, but $this->mMySQLHandler->escapeDangerousCharacters is not the best
		 * defense. A parameterized stored procedure would be better.
		 */
		if (!$this->stopSQLInjection) {
			/* gethostbyaddr() causing a lot of issues because there is not
			 * a way to set timeout settings. It is being removed for now.
			 */
			//$lClientName = gethostbyaddr($_SERVER['REMOTE_ADDR']);
			$lClientName = $_SERVER['REMOTE_ADDR'];
			$lClientIP = $_SERVER['REMOTE_ADDR'];
			//$lUserAgent = $lUserAgent;
			//$TransactionDescription = $TransactionDescription;
		}else{
			//$lClientName = $this->mMySQLHandler->escapeDangerousCharacters(gethostbyaddr($_SERVER['REMOTE_ADDR']));
			$lClientName = $this->mMySQLHandler->escapeDangerousCharacters($_SERVER['REMOTE_ADDR']);
			$lClientIP = $this->mMySQLHandler->escapeDangerousCharacters($_SERVER['REMOTE_ADDR']);
			$lUserAgent = $this->mMySQLHandler->escapeDangerousCharacters($lUserAgent);
			$TransactionDescription = $this->mMySQLHandler->escapeDangerousCharacters($TransactionDescription);
		}// end if

		$lQuery = "INSERT INTO hitlog(hostname, ip, browser, referer, date) VALUES ('".
			$lClientName . "', '".
			$lClientIP . "', '".
			$lUserAgent . "', '".
			$TransactionDescription . "', ".
			" now() )";

		try{
    		$lResult = $this->mMySQLHandler->executeQuery($lQuery);
		} catch (Exception $e) {
			throw(new Exception("Error attempting to write to log table: ".$e->getMessage(), $e->getCode(), $e));
		}// end try

	}// end method

}// end class