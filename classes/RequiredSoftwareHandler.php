<?php
class RequiredSoftwareHandler {

	/* private properties */
	private $mSecurityLevel = 0;
	private $mPHPCurlIsInstalled = false;
	private $mPHPJSONIsInstalled = false;
	private $mPHPXMLIsInstalled = false;
	
	/* --------------------------------
	 *  private methods
	* --------------------------------*/	
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
	}// end function doSetSecurityLevel()

	private function doPHPXMLIsInstalled(){
		return function_exists("simplexml_load_file");
	}// end function doPHPXMLIsInstalled

	private function doPHPCurlIsInstalled(){
	    return function_exists("curl_init");
	}// end function doPHPCurlIsInstalled
	
	private function doPHPJSONIsInstalled(){
		return function_exists("json_encode");
	}// end function doPHPJSONIsInstalled
	
	/* --------------------------------
	 *  public methods 
	 * --------------------------------*/
	
	/* constructor */
	public function __construct($pPathToESAPI, $pSecurityLevel){
		$this->doSetSecurityLevel($pSecurityLevel);
		$this->mPHPCurlIsInstalled = $this->doPHPCurlIsInstalled();
		$this->mPHPJSONIsInstalled = $this->doPHPJSONIsInstalled();
		$this->mPHPXMLIsInstalled = $this->doPHPXMLIsInstalled();
	}// end function __construct

	public function setSecurityLevel($pSecurityLevel){
		$this->doSetSecurityLevel($pSecurityLevel);
	}// end function
	
	public function getSecurityLevel($pSecurityLevel){
		return $this->mSecurityLevel;
	}// end function
	
	public function isPHPXMLIsInstalled(){
	    return $this->isPHPXMLIsInstalled;
	}// end function isPHPXMLIsInstalled()
	
	public function isPHPCurlIsInstalled(){
	    return $this->mPHPCurlIsInstalled;
	}// end function isPHPCurlIsInstalled()
	
	public function isPHPJSONIsInstalled(){
		return $this->mPHPJSONIsInstalled;
	}// end function isPHPJSONIsInstalled()

	public function getNoXMLAdviceBasedOnOperatingSystem(){
	    $lOperatingSystemAdvice = "";
	    $lHTML = "";
	    
	    switch (PHP_OS){
	        case "Linux":
	            $lOperatingSystemAdvice = "The server operating system seems to be Linux. You may be able to install with sudo apt-get install php5-xml";
	            break;
	        case "WIN32":
	        case "WINNT":
	        case "Windows":
	            $lOperatingSystemAdvice = "The server operating system seems to be Windows. Ensure the PHP-XML module is installed and activated.";
	            break;
	        default: $lOperatingSystemAdvice = ""; break;
	    }// end switch
	    
	    $lHTML = '<br/><span style="background-color: #ffff99;">Warning: Detected PHP XML is not installed on the server. This will cause issues with pages and services that use the extention. '.$lOperatingSystemAdvice.'</span><br/><br/>';
	    return $lHTML;
	}// end function getNoXMLAdviceBasedOnOperatingSystem
	
	public function getNoCurlAdviceBasedOnOperatingSystem(){
		$lOperatingSystemAdvice = "";
		$lHTML = "";
		
		switch (PHP_OS){
			case "Linux":
			    $lOperatingSystemAdvice = "The server operating system seems to be Linux. You may be able to install with sudo apt-get install php[verion]-curl where [version] is the version of PHP installed. For example, apt-get install php7.2-curl if PHP 7.2 is installed.";
			    break;
			case "WIN32":
			case "WINNT":
			case "Windows":
				$lOperatingSystemAdvice = "The server operating system seems to be Windows. You may be able to enable by uncommenting extension=php_curl.dll in the php.ini file and restarting apache server. Otherwise install php_curl.dll.";
				break;
			default: $lOperatingSystemAdvice = ""; break;
		}// end switch
		
		$lHTML = '<br/><span style="background-color: #ffff99;">Warning: Detected PHP Curl is not installed on the server. This may cause issues detecting or downloading remote files. '.$lOperatingSystemAdvice.'</span><br/><br/>';
		return $lHTML;
	}// end function getNoCurlAdviceBasedOnOperatingSystem

	public function getNoJSONAdviceBasedOnOperatingSystem(){
		$lOperatingSystemAdvice = "";
		$lHTML = "";
	
		switch (PHP_OS){
			case "Linux":
				$lOperatingSystemAdvice = "The server operating system seems to be Linux. You may be able to install with sudo apt-get install php5-json";
				break;
			case "WIN32":
			case "WINNT":
			case "Windows":
				$lOperatingSystemAdvice = "The server operating system seems to be Windows. You may be able to enable by uncommenting extension=php_json.dll in the php.ini file and restarting apache server. Otherwise install php_json.dll.";
				break;
			default: $lOperatingSystemAdvice = ""; break;
		}// end switch
	
		$lHTML = '<br/><span style="background-color: #ffff99;">Warning: Detected PHP JSON is not installed on the server. This may cause issues with pages and services that parse JSON messages. '.$lOperatingSystemAdvice.'</span><br/><br/>';
		return $lHTML;
	}// end function getNoJSONAdviceBasedOnOperatingSystem

}// end class
?>
