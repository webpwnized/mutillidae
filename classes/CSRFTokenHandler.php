<?php
class CSRFTokenHandler{

	/* objects */
	protected $ESAPI = null;
	protected $ESAPIEncoder = null;
	protected $ESAPIRandomizer = null;

	/* flag properties */
	protected $mEncodeOutput = FALSE;
	protected $mSecurityLevel = 0;
	protected $mCSRFTokenStrength = "NONE";
	protected $mProtectAgainstCSRF = FALSE;

	protected $mExpectedCSRFTokenForThisRequest = "";
	protected $mNewCSRFTokenForNextRequest = "";
	protected $mPageBeingProtected = "";
	protected $mPostedCSRFToken = "";
	protected $mTokenValid = "Validation not performed";

	protected $mRandomTokenBytes = 64;

	private function doSetSecurityLevel($pSecurityLevel){

		$this->mSecurityLevel = $pSecurityLevel;

		switch ($this->mSecurityLevel){
			case "0": // This code is insecure, we are not encoding output
				$this->mEncodeOutput = FALSE;
				$this->mCSRFTokenStrength = "NONE";
				$this->mProtectAgainstCSRF = FALSE;
				break;
			case "1": // This code is insecure, we are not encoding output
				$this->mEncodeOutput = FALSE;
				$this->mCSRFTokenStrength = "LOW";
				$this->mProtectAgainstCSRF = TRUE;
			break;

			case "2":
			case "3":
			case "4":
			case "5": // This code is fairly secure
				// If we are secure, then we encode all output.
				$this->mEncodeOutput = TRUE;
				$this->mCSRFTokenStrength = "HIGH";
				$this->mProtectAgainstCSRF = TRUE;
			break;
		}// end switch

	}// end function

	public function __construct($pPathToESAPI, $pSecurityLevel, $pPageBeingProtected){

		$this->doSetSecurityLevel($pSecurityLevel);

		//initialize OWASP ESAPI for PHP
		require_once $pPathToESAPI . 'ESAPI.php';
		$this->ESAPI = new ESAPI($pPathToESAPI . 'ESAPI.xml');
		$this->ESAPIEncoder = $this->ESAPI->getEncoder();
		$this->ESAPIRandomizer = $this->ESAPI->getRandomizer();
		$this->mPageBeingProtected = $pPageBeingProtected;

		if (isset($_SESSION['register-user']['csrf-token'])){
			$this->mExpectedCSRFTokenForThisRequest = $_SESSION[$this->mPageBeingProtected]['csrf-token'];
		}//end if

	}// end function

	public function setSecurityLevel($pSecurityLevel){
		$this->doSetSecurityLevel($pSecurityLevel);
	}// end function setSecurityLevel

	private function doGenerateCSRFToken(){

		$lCurrentCSRFToken = 0;
		switch ($this->mCSRFTokenStrength){
			case "HIGH":
			    $lCSRFToken = base64_encode(random_bytes($this->mRandomTokenBytes));
			break;
			case "MEDIUM":
				$lCSRFToken = mt_rand();
			break;
			case "LOW":
				if (isset($_SESSION[$this->mPageBeingProtected]['csrf-token'])) {
					$lCurrentCSRFToken = $_SESSION[$this->mPageBeingProtected]['csrf-token'];
				}// end if
				$lBase = 77;
				$lCSRFToken = ((int)$lBase + (int)$lCurrentCSRFToken);
			break;
			case "NONE":
				$lCSRFToken = "";
			break;
			default:break;
		}//end switch on $lCSRFTokenStrength

		return $lCSRFToken;

	}// end private function doGenerateCSRFToken()

	public function generateCSRFToken(){

		$lCSRFToken = $_SESSION[$this->mPageBeingProtected]['csrf-token'] = $this->mNewCSRFTokenForNextRequest = $this->doGenerateCSRFToken();
		return $lCSRFToken;

	}// end public function generateCSRFToken()

	public function validateCSRFToken($pPostedCSRFToken){
		$this->mPostedCSRFToken = $pPostedCSRFToken;
		if($this->mProtectAgainstCSRF){
			if ($pPostedCSRFToken == $this->mExpectedCSRFTokenForThisRequest){
				$this->mTokenValid = "Token is valid";
				return TRUE;
			}else{
				$this->mTokenValid = "Token is not valid";
				return FALSE;
			}//end if
		}else{
			$this->mTokenValid = "Validation not performed";
			return TRUE;
		}// end if
	}// end function validateCSRFToken()

	public function generateCSRFHTMLReport(){

		if($this->mEncodeOutput){
			$lPostedCSRFToken = $this->ESAPIEncoder->encodeForHTML($this->mPostedCSRFToken);
			$lExpectedCSRFTokenForThisRequest = $this->ESAPIEncoder->encodeForHTML($this->mExpectedCSRFTokenForThisRequest);
			$lNewCSRFTokenForNextRequest = $this->ESAPIEncoder->encodeForHTML($this->mNewCSRFTokenForNextRequest);
			$lTokenStoredInSession = $this->ESAPIEncoder->encodeForHTML($_SESSION[$this->mPageBeingProtected]['csrf-token']);
		}else{
			$lPostedCSRFToken = $this->mPostedCSRFToken;
			$lExpectedCSRFTokenForThisRequest = $this->mExpectedCSRFTokenForThisRequest;
			$lNewCSRFTokenForNextRequest = $this->mNewCSRFTokenForNextRequest;
			$lTokenStoredInSession = $_SESSION[$this->mPageBeingProtected]['csrf-token'];
		}// end if

		return
			'<div>&nbsp;</div>'.PHP_EOL.
			'<div>&nbsp;</div>'.PHP_EOL.
			'<fieldset>'.PHP_EOL.
			'<legend>CSRF Protection Information</legend>'.PHP_EOL.
			'<table>'.PHP_EOL.
			'<tr><td></td></tr>'.PHP_EOL.
			'<tr><td class="report-header">Posted Token: '.$lPostedCSRFToken.'<br/>('.$this->mTokenValid.')</td></tr>'.PHP_EOL.
			'<tr><td>Expected Token For This Request: '.$lExpectedCSRFTokenForThisRequest.'</td></tr>'.PHP_EOL.
			'<tr><td>Token Passed By User For This Request: '.$lPostedCSRFToken.'</td></tr>'.PHP_EOL.
			'<tr><td>&nbsp;</td></tr>'.PHP_EOL.
			'<tr><td>New Token For Next Request: '.$lNewCSRFTokenForNextRequest.'</td></tr>'.PHP_EOL.
			'<tr><td>Token Stored in Session: '.$lTokenStoredInSession.'</td></tr>'.PHP_EOL.
			'<tr><td></td></tr>'.PHP_EOL.
			'</table>'.PHP_EOL.
			'</fieldset>'.PHP_EOL;
	}// end public function generateCSRFHTMLReport()

}// end class
?>