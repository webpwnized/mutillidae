<?php 

/* Error output gets overlooked sometimes. On the one hand, no website
 * should actually output error diagnostic error information to 
 * the web page because the user can see it. However, that is not the responsibility
 * of this class anyway. This class is responsible for formatting the error. It is
 * up to the caller to decide where to output this information. Errors should be logged
 * then reported to the support team, but not shown on the web page. 
 * This error handler is responsible for outputting the information safety. If the 
 * input that caused the error is XSS for enample, then the log will have XSS in it.
 * If this error is emailed to support staff, then the email would have XSS in it.
 * So it is important that this error handler make sure all dynamic output is properly 
 * encoded. For both email and error logs, this typically calls for HTML encoding.
 * 
 * Known Vulnerabilities In This Class: Cross Site Scripting,
 * Cross Site Request Forgery, Application Exception,
 * SQL Exception
 */

class CustomErrorHandler{
	
	//default insecure: no output encoding.
	protected $encodeOutput = FALSE;
	protected $mSecurityLevel = 0;
	protected $ESAPI = null;
	protected $Encoder = null;
	protected $supressErrorMessages = FALSE;
	
	protected $mLine = "";
	protected $mCode = "";
	protected $mFile = "";
	protected $mMessage = "";
	protected $mTrace = "";
	protected $mDiagnosticInformation = "";

	private function doFormatErrorAsHTMLTable(Exception $e, $pDiagnosticInformation){

		$lSupressedMessage = "Sorry. An error occured. Support has been notified. Not allowed to give out errors at this security level.";
		
		$this->setErrorProperties($e, $pDiagnosticInformation);
		
		if($this->supressErrorMessages){
			$lHTML = '<tr><td class="error-label">Message</td><td class="error-detail">' . $lSupressedMessage . '</td></tr>';
		}else{
			$lHTML = 
				'<tr><td class="error-label">Line</td><td class="error-detail">' . $this->mLine . '</td></tr>
				<tr><td class="error-label">Code</td><td class="error-detail">' . $this->mCode . '</td></tr>
				<tr><td class="error-label">File</td><td class="error-detail">' . $this->mFile . '</td></tr>
				<tr><td class="error-label">Message</td><td class="error-detail">' . $this->mMessage . '</td></tr>
				<tr><td class="error-label">Trace</td><td class="error-detail">' . $this->mTrace . '</td></tr>
				<tr><td class="error-label">Diagnotic Information</td><td class="error-detail">' . $this->mDiagnosticInformation . '</td></tr>';
		}// end if

		return
		'<fieldset>
			<legend>Error Message</legend>
			<table>
				<tr><td colspan="2">&nbsp;</td></tr>
				<tr>
					<td colspan="2" class="error-header">Failure is always an option</td>
				</tr>
				'.$lHTML.'
				<tr>
					<td colspan="2" class="error-header" style="text-align: center;"><a href="set-up-database.php">Click here to reset the DB</a></td>
				</tr>
				<tr><td colspan="2">&nbsp;</td></tr>
			</table>
		</fieldset>';

	}// end private function FormatErrorTable
	
	private function doSetSecurityLevel($pSecurityLevel){
		$this->mSecurityLevel = $pSecurityLevel;
		
		switch ($this->mSecurityLevel){
	   		case "0": // This code is insecure, we are not encoding output
	   		case "1": // This code is insecure, we are not encoding output
				$this->encodeOutput = FALSE;
				$this->supressErrorMessages = FALSE;
	   		break;

	   		case "2":
			case "3":
			case "4":
	   		case "5": // This code is fairly secure
	  			// If we are secure, then we encode all output.
	   			$this->encodeOutput = TRUE;
	   			$this->supressErrorMessages = TRUE;
	   		break;
	   	}// end switch		
	}// end function

	private function formatExceptionMessage(Exception $e, $pDiagnosticInformation){
		return sprintf("%s on line %d: %s %s (%d) [%s] <br />\n", $e->getFile(), $e->getLine(), $e->getMessage(), $pDiagnosticInformation, $e->getCode(), get_class($e));
	}// end private function formatExceptionMessage()

	private function setErrorProperties(Exception $pException, $pDiagnosticInformation){

		if (!$this->encodeOutput){
			// encode the entire message following OWASP standards
			// this is HTML encoding because we are outputting data into HTML
			$this->mLine = $pException->getLine();
			$this->mCode = $pException->getCode();
			$this->mFile = $pException->getFile();
			$this->mMessage = $pException->getMessage();
			$this->mTrace = $pException->getTraceAsString();
			$this->mDiagnosticInformation = $pDiagnosticInformation;
		}else{
			/* Cross site scripting defense */
			$this->mLine = $this->Encoder->encodeForHTML($pException->getLine());
			$this->mCode = $this->Encoder->encodeForHTML($pException->getCode());
			$this->mFile = $this->Encoder->encodeForHTML($pException->getFile());
			$this->mMessage = $this->Encoder->encodeForHTML($pException->getMessage());
			$this->mTrace = $this->Encoder->encodeForHTML($pException->getTraceAsString());
			$this->mDiagnosticInformation = $this->Encoder->encodeForHTML($pDiagnosticInformation);
		}// end if

	}// end private function setErrorProperties()	
	
	public function __construct($pPathToESAPI, $pSecurityLevel){
		
		$this->doSetSecurityLevel($pSecurityLevel);
		
		//initialize OWASP ESAPI for PHP
		require_once $pPathToESAPI . 'ESAPI.php';
		$this->ESAPI = new ESAPI($pPathToESAPI . 'ESAPI.xml');
		$this->Encoder = $this->ESAPI->getEncoder();
	}// end function
	   	
	public function setSecurityLevel($pSecurityLevel){
		$this->doSetSecurityLevel($pSecurityLevel);
	}// end function setSecurityLevel

	public function getExceptionMessage(Exception $e, $pDiagnosticInformation){		
		$lExceptionMessage = "";
		
		/* getPrevious introduced in PHP 5.3.0 */
		if (method_exists($e,"getPrevious")){
			do {
	        	$lExceptionMessage .= $this->formatExceptionMessage($e, $pDiagnosticInformation);
	    	} while($e = $e->getPrevious());
		}else{
			$lExceptionMessage = $this->formatExceptionMessage($e, $pDiagnosticInformation);						
		}// end if method_exists
		
    	return $lExceptionMessage;
	}//end function getExceptionMessage
	
	public function FormatError(Exception $e, $pDiagnosticInformation){
		return $this->doFormatErrorAsHTMLTable($e, $pDiagnosticInformation);
	}// end public function FormatError()

	private function doFormatErrorJSON(Exception $e, $pDiagnosticInformation){
		
		$lSupressedMessage = "Sorry. An error occured. Support has been notified. Not allowed to give out errors at this security level.";
		
		$this->setErrorProperties($e, $pDiagnosticInformation);
		
		if($this->supressErrorMessages){
			$lJSON = 
			'{
				"Exception": [
				"Message": "'.$lSupressedMessage.'",
				"DiagnoticInformation": "'.$lSupressedMessage.'"
				]
			}';			
		}else{
			$lJSON =
				'{
					"Exception": [
						"Line": "' . $this->mLine . '",
						"Code": "' . $this->mCode . '",
						"File": "' . $this->mFile . '",
						"Message": "' . $this->mMessage . '",
						"Trace": "' . $this->mTrace . '",
						"DiagnoticInformation": "' . $this->mDiagnosticInformation . '"
					]
				}';
		}// end if
		
		return $lJSON;
	}// end private function doFormatErrorJSON()

	private function doFormatErrorXML(Exception $e, $pDiagnosticInformation){
		$lXML = "";
		$lSupressedMessage = "Sorry. An error occured. Support has been notified. Not allowed to give out errors at this security level.";
	
		$this->setErrorProperties($e, $pDiagnosticInformation);
	
		if($this->supressErrorMessages){
			$lXML .= "<exception>";
			$lXML .= "<message>{$lSupressedMessage}</message>";
			$lXML .= "<diagnoticInformation>{$lSupressedMessage}</diagnoticInformation>";
			$lXML .= "</exception>";
		}else{
			$lXML .= "<exception>";
			$lXML .= "<line>{$this->mLine}</line>";
			$lXML .= "<code>{$this->mCode}</code>";
			$lXML .= "<file>{$this->mFile}</file>";
			$lXML .= "<message>{$this->mMessage}</message>";
			$lXML .= "<trace>{$this->mTrace}</trace>";
			$lXML .= "<diagnoticInformation>{$this->mDiagnosticInformation}</diagnoticInformation>";
			$lXML .= "</exception>";
		}// end if
	
		return $lXML;
	}// end private function doFormatErrorXML()
	
	public function FormatErrorJSON(Exception $e, $pDiagnosticInformation){
		return $this->doFormatErrorJSON($e, $pDiagnosticInformation);
	}// end public function FormatErrorJSON()

	public function FormatErrorXML(Exception $e, $pDiagnosticInformation){
		return $this->doFormatErrorXML($e, $pDiagnosticInformation);
	}// end public function FormatErrorXML()
	
}// end class

?>