<?php
require_once ('SQLQueryHandler.php');
require_once ('RemoteFileHandler.php');

class YouTubeVideo{
	public $mIdentificationToken = "";
	public $mTitle = "";
}// end class

class YouTubeVideos{

	private $mSQLQueryHandler = null;

	public function __construct($pPathToESAPI, $pSecurityLevel){
		/* ------------------------------------------
		 * initialize SQLQuery handler
		* ------------------------------------------ */
		$this->mSQLQueryHandler = new SQLQueryHandler($pPathToESAPI, $pSecurityLevel);
	
	}//end function
	
	public function getYouTubeVideo($pRecordIdentifier){
		$lQueryResult = $this->mSQLQueryHandler->getYouTubeVideo($pRecordIdentifier);
		$lNewYouTubeVideo = new YouTubeVideo();
		$lNewYouTubeVideo->mIdentificationToken = $lQueryResult->identificationToken;
		$lNewYouTubeVideo->mTitle = $lQueryResult->title;
		return $lNewYouTubeVideo;
	}//end function CreateYouTubeVideo()

}// end class YouTubeVideos

class YouTubeVideoHandler {

	const TWO_SECONDS = 2;
	
	/* private properties */
	private $mSecurityLevel = 0;
	private $mYouTubeVideos = null;
	private $mCurlIsInstalled = false;
	private $mYouTubeIsReachable = false;
	private $mRemoteFileHandler = null;

	/* public properties */
	public $InstallingOWASPMutillidaeIIonWindowswithXAMPP = 2;
	public $InstallingMetasploitable2withMutillidaeonVirtualBox = 3;
	public $HowtoinstalllatestMutillidaeonSamuraiWTF20 = 4;
	public $IntroductiontoInstallingConfiguringandUsingBurpSuiteProxy = 5;
	public $HowtoinstallandconfigureBurpSuitewithFirefox = 6;
	public $HowtoremovePHPerrorsafterinstallingMutillidaeonWindowsXAMPP = 7;
	public $BuildingaVirtualLabtoPracticePenTesting = 8;
	public $HowtoUpgradetotheLatestMutillidaeonSamuraiWTF20 = 9;
	public $SpideringWebApplicationswithBurpSuite = 10;
	public $BasicsofBurpSuiteTargetsTab = 11;
	public $BruteForcePageNamesusingBurpSuiteIntruder = 12;
	public $UsingBurpIntruderSnipertoFuzzParameters = 13;
	public $ComparingBurpSuiteIntruderModesSniperBatteringramPitchforkClusterbomb = 14;
	public $IntroductiontoBurpSuiteComparerTool = 15;
	public $UsingBurpSuiteSequencertoCompareCSRFtokenstrengths = 16;
	public $BasicsofWebRequestandResponseInterceptionwithBurpSuite = 17;
	public $ISSA2013WebPentestingWorkshopPart1IntrotoMutillidaeBurpSuiteInjection = 18;
	public $OverviewofUsefulPenTestingAddonsforFirefox = 19;
	public $BypassAuthenticationusingSQLInjection = 20;
	public $AutomateSQLInjectionusingsqlmap = 21;
	public $BasicsofSQLInjectionTimingAttacks = 22;
	public $IntroductiontoUnionBasedSQLInjection = 23;
	public $BasicsofInsertingDatawithSQLInjection = 24;
	public $InjectWebShellBackdoorviaSQLInjection = 25;
	public $BasicsofusingSQLInjectiontoReadFiles = 26;
	public $GenerateCrossSiteScriptswithSQLInjection = 27;
	public $SQLInjectionviaAJAXrequestwithJSONresponse = 28;
	public $BasicsofusingsqlmapISSAKYWorkshopFebruary2013 = 29;
	public $ExplanationofHTTPOnlyCookiesinPresenceCrossSiteScripting = 30;
	public $TwoMethodstoStealSessionTokenusingCrossSiteScripting = 31;
	public $InjectingaCrossSiteScriptviaCascadingStylesheetContext = 32;
	public $BasicsofInjectingCrossSiteScriptintoHTMLonclickEvent = 33;
	public $IntroductiontolocatingReflectedCrosssiteScripting = 34;
	public $SendingPersistentCrosssiteScriptsintoWebLogstoSnagWebAdmin = 35;
	public $InjectingCrossSiteScriptsXSSintoLogPageviaCookie = 37;
	public $IntroductiontoHTMLInjectionHTMLiandCrossSiteScriptingXSSUsingMutillidae = 38;
	public $IntroductiontoCrossSiteScriptingXSSviaJavaScriptStringInjection = 39;
	public $AddingValuestoDOMStorageusingCrosssiteScripting = 41;
	public $AlterValuesinHTML5WebStorageusingCrosssiteScript = 42;
	public $AlterValuesinHTML5WebStorageusingPersistentCrosssiteScript = 43;
	public $AlterValuesinHTML5WebStorageusingReflectedCrosssiteScript = 44;
	public $WebPenTestingHTML5WebStorageusingJSONInjection = 45;
	public $StealingHTML5StorageviaJSONInjection = 46;
	public $ReadingHiddenValuesfromHTML5DomStorage = 47;
	public $CommandInjectiontoDumpFilesStartServicesandDisableFirewall = 48;
	public $HowtoLocatetheEastereggFileusingCommandInjection = 49;
	public $GainingAdministrativeShellAccessviaCommandInjection = 50;
	public $UsingCommandInjectiontoGainRemoteDesktop = 51;
	public $IntroductiontoHTTPParameterPollution = 52;
	public $UsingHydratoBruteForceWebFormsBasedAuthentication = 53;
	public $BypassAuthenticationviaAuthenticationTokenManipulation = 55;
	public $BruteForceAuthenticationusingBurpIntruder = 56;
	public $AnalyzeSessionTokenRandomnessusingBurpSuiteSequencer = 57;
	public $DetermineServerBannersusingNetcatNiktoandw3af = 58;
	public $UsingNmaptoFingerprintHTTPserversandWebApplications = 59;
	public $FindingCommentsandFileMetadatausingMultipleTechniques = 60;
	public $HowtoExploitLocalFileInclusionVulnerabilityusingBurpSuite = 61;
	public $ISSA2013WebPentestingWorkshopPart6LocalRemoteFileInclusion = 62;
	public $TwoMethodstoBypassJavaScriptValidation = 63;
	public $XSSbypassingJavaScriptValidation = 64;
	public $HowtoBypassMaxlengthRestrictionsonHTMLInputFields = 65;
	public $IntroductiontoCBCBitFlippingAttack = 66;
	public $UsingEttercapandSSLstriptoCaptureCredentials = 67;
	public $IntroductiontoXMLExternalEntityInjection = 68;
	public $DetermineHTTPMethodsusingNetcat = 69;
	public $ISSAIntroductiontoXMLExternalEntityInjection = 70;
	public $IntroductiontoUseragentImpersonation = 71;
	public $IntroductiontoPathRelativeStyleSheetInjection = 72;
	public $IntroductiontoSQLInjectionforBeginners = 73;
	public $IntroductiontoSQLInjectionwithSQLMap = 74;
	public $HowtoSolvetheShowHintsinSecurityLevel5Challenge = 75;
	public $IntroductiontoPasswordCrackingwithJohntheRipper = 76;
	public $IntroductiontoFuzzingWebApplicationswithBurpSuiteIntruderTool = 77;
	public $MutillidaeHowtoShowSecretPageinSecurityLevel5 = 78;
	public $SolvingPasswordChallengeInMutillidaeWithCommandInjection = 79;
	public $IntroductiontotheBrowserExploitationFramework= 80;	
	public $HowtoInstallBurpSuiteonLinux = 81;
	public $HowtoIdentifyWebTechnologywithWappalyzer = 82;
	public $HowtoSweepaWebSiteforHTMLComments = 83;
	public $HowtoUsedirbtoLocateHiddenDirectoriesonaWebSite = 84;
	public $HowtoInstallOWASPZaponLinux = 85;
	public $HowtoInstallOWASPDirBusteronLinux = 86;
	public $HowtouseOWASPDirBustertoDiscoverHiddenDirectoriesonWebSites = 87;
	public $HowtoInstalldirbonLinux = 88;
	public $HowtouseWGETtocloneaWebSite = 89;
	public $HowtoScanWordpressSitesforVulnerabilities = 90;
	public $CrossSiteScriptingExplainedPart10PathRelativeStylesheetInjection = 91;
	public $IntroductiontoXPathInjection = 92;
	public $IntroductiontoUnvalidatedRedirectsandForwards = 93;
	public $IntroductiontoParameterAddition = 94;
	public $HowtoTestforWeakSSLTLSHTTPSciphers = 95;
	public $IntroductiontoMethodTampering = 96;
	public $IntroductiontoFrameSourceInjection = 97;
	public $IntroductiontoBurpSuiteRepeaterTool = 98;
	public $IntroductiontoBurpSuiteIntrudersCharacterFrobberPayload = 99;
	public $IntroductiontoBurpSuiteIntrudersGrepExtractFeature = 100;
	public $HowtograbrobotstxtfilewithCURL = 101;
	public $HowtoInstallandConfigureFoxyProxywithFirefox = 102;
	public $HowtoSpideraWebSitewithOWASPZAP = 103;
	public $HowtoProxyWebTrafficthroughOWASPZAP = 104;
	public $HowtoInterceptHTTPRequestswithOWASPZAP = 105;
	public $HowtoFuzzWebApplicationswithOWASPZAPPart1 = 106;
	public $HowtoFuzzWebApplicationswithOWASPZAPPart2 = 107;	
	public $HowtolistHTTPMethodswithCURL = 108;
	public $HowtolistHTTPMethodswithNMap = 109;
	public $HowtograbHTTPServerBannerswithCURL = 110;
	public $HowtograbHTTPServerBannerswithNMap = 111;
	public $HowtoInstallBurpSuiteCommunityEditiononLinux = 112;
	public $OWASPZAPUsingForcedBrowseFeatureFindHiddenDirectories = 113;
	public $OWASPZAPWebAppVulnerabilityAssessmentPartialSite = 114;
	public $OWASPZAPAutomatedWebAppVulnerabilityAssessmentEntireSite = 115;
	public $OWASPZAPWebAppVulnerabilityAssessmentSinglePage = 116;
	public $HowtoCreateWordlistsfromWebSitesusingCEWL = 117;
	public $OWASPZAPBreakpointsPart1TrappingHTTPRequests = 118;
	public $OWASPZAPBreakpointsPart2TrappingSpecificHTTPRequests = 119;
	public $UsingOWASPZAPwithBurpSuite = 120;
	public $CommandInjectionExplainedPart1TheBasics = 121;
	public $CommandInjectionExplainedPart2Discovery = 122;
	public $CommandInjectionExplainedPart3BlindInjection = 123;
	public $CommandInjectionExplainedPart4ChainingCommands = 124;
	public $CommandInjectionExplainedPart5Shell = 125;
	public $CommandInjectionExplainedPart6DirectoryTraversal = 126;
	public $SQLInjectionExplainedPart1TheBasics = 127;
	public $SQLInjectionExplainedPart2Tautologies = 128;
	public $SQLInjectionExplainedPart3SelectiveInjections = 129;
	public $SQLInjectionExplainedPart4DiscoverybyError = 130;
	public $CrossSiteScriptingExplainedPart1TheBasics = 131;
	public $CrossSiteScriptingExplainedPart2DOMBasedXSS = 132;
	public $CrossSiteScriptingExplainedPart3ReflectedXSS = 133;
	public $CrossSiteScriptingExplainedPart4StoredXSS = 134;
	public $CrossSiteRequestForgeryExplainedPart1BasicCSRF = 135;
	public $CrossSiteRequestForgeryExplainedPart2AdvancedCSRF = 136;
	public $HowtoCreateUbuntuVirtualMachineVirtualBox = 137;
	public $HowtoInstallVirtualBoxGuestAdditionsLinux = 138;
	public $HowtoCreateUbuntuVirtualMachineVMware = 139;
	public $LAMPStackPart1HowtoInstallApacheWebServer = 140;
	public $LAMPStackPart2HowtoInstallPHP = 141;
	public $LAMPStackPart3HowtoInstallMySQLServer = 142;
	public $HowtoResetRootPasswordinMySQLMariaDB = 143;
	public $HowtoInstallPHPCurlLibrary = 144;
	public $HowtoInstallPHPXMLLibrary = 145;
	public $HowtoInstallPHPmbstringLibrary = 146;
	public $HowtoDisplayErrorsinPHPPages = 147;
	public $HowtoInstallMutillidaeonLinux = 148;
	public $HowtoCreateSelfSignedCertificateinApache = 149;
	public $HowtoCreateVirtualHostsinApache = 150;

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
		
	private function fetchVideoPropertiesFromYouTube($pVideoIdentificationToken){
		$lYouTubeResponse = "";

		try{
			if ($this->mCurlIsInstalled) {
				$lConnectionTimeout = 2; //two seconds. Using constant messed up Metasploitable 2.
				$lCurlInstance = curl_init();
				curl_setopt($lCurlInstance, CURLOPT_URL, "http://gdata.youtube.com/feeds/api/videos/".$pVideoIdentificationToken);
				curl_setopt($lCurlInstance, CURLOPT_RETURNTRANSFER, 1);
				curl_setopt($lCurlInstance, CURLOPT_CONNECTTIMEOUT, $lConnectionTimeout);
				$lYouTubeResponse = curl_exec($lCurlInstance);
				curl_close($lCurlInstance);
			}//end if
		} catch (Exception $e) {
			//do nothing
		}//end try

		return $lYouTubeResponse;
	}// end function fetchVideoPropertiesFromYouTube
	
	private function getYouTubeIsNotReachableAdvice(){
		return '<br/><span style="background-color: #ffff99;">Warning: Could not reach YouTube via network connection. Failed to embed video.</span><br/>';
	}// end function getYouTubeIsNotReachableAdvice
	
	private function getNoCurlAdviceBasedOnOperatingSystem(){
		$lOperatingSystemAdvice = "";
		$lHTML = "";
		
		switch (PHP_OS){
			case "Linux":
				$lOperatingSystemAdvice = "The server operating system seems to be Linux. You may be able to install with sudo apt-get install php[verion]-curl where [version] is the version of PHP installed. For example, apt-get install php7.2-curl if PHP 7.2 is installed.";
				break;
			case "WIN32":
			case "WINNT":
			case "Windows":
				$lOperatingSystemAdvice = "The server operating system seems to be Windows. You may be able to enable by uncommenting extension=php_curl.dll in the php.ini file and restarting apache server.";
				break;
			default: $lOperatingSystemAdvice = ""; break;
		}// end switch
		
		$lHTML = '<br/><span style="background-color: #ffff99;">Warning: Failed to embed video because PHP Curl is not installed on the server. '.$lOperatingSystemAdvice.'</span><br/>';
		return $lHTML;
	}// end function getNoCurlAdviceBasedOnOperatingSystem

	private function curlIsInstalled(){
		return function_exists("curl_init");
	}// end function curlIsInstalled
	
	private function generateYouTubeFrameHTML($pVideoIdentificationToken){
		$lRandomNumber = rand(1, 10000000);
		$lHTML = '
			<script>
				var lYouTubeFrameCode'.$lRandomNumber.' = \'<iframe width=640px height=480px src=https://www.youtube.com/embed/'.$pVideoIdentificationToken.'?autoplay=1 frameborder=0 allowfullscreen=1></iframe>\';
			</script>
			<span>
				<a
					href="#"
					id="btn-load-video'.$lRandomNumber.'"
					onclick="document.getElementById(\'the-player'.$lRandomNumber.'\').innerHTML=lYouTubeFrameCode'.$lRandomNumber.';"
				>
				Load the video</a>
			</span>
			<div id="the-player'.$lRandomNumber.'"></div>
		';
		return $lHTML;
	}// end function generateYouTubeFrameHTML()

	private function isYouTubeReachable(){
		/* Pick any video and see if we can fetch its properties.
		 * 'DJaX4HN2gwQ' is 'Introduction to XML External Entity Injection'
		* */
		$lYouTubeResponse = "";
		if ($this->curlIsInstalled()){
			$lYouTubeResponse = $this->fetchVideoPropertiesFromYouTube("DJaX4HN2gwQ");
		}// end if $mCurlIsInstalled
		return (strlen($lYouTubeResponse) > 0);
	}// end function isYouTubeReachable()
	
	/* public methods */
	
	/* constructor */
	public function __construct($pPathToESAPI, $pSecurityLevel){
		$this->doSetSecurityLevel($pSecurityLevel);
		$this->mYouTubeVideos = new YouTubeVideos($pPathToESAPI, $pSecurityLevel);
		$this->mRemoteFileHandler = new RemoteFileHandler($pPathToESAPI, $pSecurityLevel);
		$this->mCurlIsInstalled = $this->curlIsInstalled();
		$this->mYouTubeIsReachable = $this->isYouTubeReachable();
	}// end function __construct

	public function getYouTubeVideo($pVideo) {
		$lHTML = "";
		$lVideo = $this->mYouTubeVideos->getYouTubeVideo($pVideo);
		$lVideoIdentificationToken = $lVideo->mIdentificationToken;
		$lVideoTitle = $lVideo->mTitle;
		$lOperatingSystemAdvice = "";

		try {
			//if (!$this->mCurlIsInstalled){
				//$lHTML .= $this->getNoCurlAdviceBasedOnOperatingSystem();
			//}//end if curl is not installed

			if (!$this->mYouTubeIsReachable){
				$lHTML .= $this->getYouTubeIsNotReachableAdvice();
			}//end if YouTube is not reachable

			//$lHTML .= '<br/><span class="label">'.$lVideoTitle.': </span>';

			//if($this->mYouTubeIsReachable){
				//$lHTML .= $this->generateYouTubeFrameHTML($lVideoIdentificationToken);
			//}else {
				$lHTML .= '<br/><a href="https://www.youtube.com/watch?v='.
				$lVideoIdentificationToken.
				'" target="_blank"><img style="margin-right: 5px;" src="images/youtube-play-icon-40-40.png" alt="YouTube" /><span class="label">'.
				$lVideoTitle.
				'</span></a>';
			//}// end if

		} catch (Exception $e) {
			//do nothing
		}//end try

		return $lHTML;
		
	}// end function getYouTubeVideo
	
}// end class YouTubeVideoHandler
