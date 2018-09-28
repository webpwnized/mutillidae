<?php
class RemoteFileHandler {

	/* private properties */
	private $mSecurityLevel = 0;
	
	/* private objects */
	protected $mRequiredSoftwareHandler = null;
		
	/* private methods */
	private function doSetSecurityLevel($pSecurityLevel){
		$this->mSecurityLevel = $pSecurityLevel;
	
		switch ($this->mSecurityLevel){
			case "0": // This code is insecure, we are not encoding output
			case "1": // This code is insecure, we are not encoding output
				break;
	
			case "2":
			case "3":
			case "4":
			case "5": // This code is fairly secure
				break;
		}// end switch
	}// end function

	private function startsWith($haystack, $needle){
		$length = strlen($needle);
		return (substr($haystack, 0, $length) === $needle);
	}// end startsWith()
	
	/* public methods */
	/* constructor */
	public function __construct($pPathToESAPI, $pSecurityLevel){
		$this->doSetSecurityLevel($pSecurityLevel);
		
		/* Initialize Required Software Handler */
		require_once ('RequiredSoftwareHandler.php');
		$this->mRequiredSoftwareHandler = new RequiredSoftwareHandler($pPathToESAPI, $pSecurityLevel);
				
	}// end function __construct

	public function setSecurityLevel($pSecurityLevel){
		$this->doSetSecurityLevel($pSecurityLevel);
		$this->mRequiredSoftwareHandler->setSecurityLevel($pSecurityLevel);
	}// end function
	
	public function getSecurityLevel($pSecurityLevel){
		return $this->mSecurityLevel;
	}// end function
	
	public function curlIsInstalled(){
		return $this->mRequiredSoftwareHandler->isPHPCurlIsInstalled();
	}// end function isCurlInstalled

	public function remoteSiteIsReachable($pPage){
		try{
			if ($this->mRequiredSoftwareHandler->isPHPCurlIsInstalled()){
				$ch = curl_init($pPage);
				curl_setopt($ch, CURLOPT_NOBODY, true);
				/* Status 4xx: Client messed up, Status 5xx: Server messed up */
				curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
				$data = curl_exec($ch);
				$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
				/* Status 4xx: Client messed up, Status 5xx: Server messed up */
				return ($this->startsWith($httpCode, '2') || $this->startsWith($httpCode, '3') || $this->startsWith($httpCode, '1'));
			}// end if $this->mRequiredSoftwareHandler->isPHPCurlIsInstalled()
		} catch (Exception $e) {
			return false;
		}//end try
	}// end function remoteSiteIsReachable()

	public function getNoCurlAdviceBasedOnOperatingSystem(){
		return $this->mRequiredSoftwareHandler->getNoCurlAdviceBasedOnOperatingSystem();
	}// end function getNoCurlAdviceBasedOnOperatingSystem()

}// end class RemoteFileHandler
?>
