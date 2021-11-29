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
	public $JWTSecurityPart1WhatisaJWT = 1;
	public $JWTSecurityPart2HowtoViewJWTinBurpSuite = 2;
	public $JWTSecurityPart3HowTimeoutsWork = 3;
	public $JWTSecurityPart4HowSignaturesProtectAgainstForgery = 4;
	public $JWTSecurityPart5WhyuseCertificatebasedSignatures = 5;
	public $JWTSecurityPart6ImportanceofInputValidation = 6;
	public $HowtoScanforWebVulnerabilitieswithNikto = 7;
	public $WhatisSQLInjection = 8;
	public $OWASPDependencyCheckPart1HowtoInstall = 9;
	public $OWASPDependencyCheckPart2HowtoScanYourProject = 10;
	public $UsingBurpIntruderSnipertoFuzzParameters = 13;
	public $CrossSiteScriptingPart1WhatisReflectedXSS = 14;
	public $CrossSiteScriptingPart2WhatisDOMbasedXSS = 15;
	public $CrossSiteScriptingPart3WhatisPersistentXSS = 16;
	public $WhatisInsecureDirectObjectReferenceIDOR = 17;
	public $WhatisanOpenRedirect = 18;
	public $WhatisCrossSiteRequestForgeryCSRF = 19;
	public $IntroductiontoBurpSuiteIntrudersCharacterFrobberPayload = 99;
	public $IntroductiontoBurpSuiteIntrudersGrepExtractFeature = 100;
	public $HowtoInstallBurpSuiteCommunityEditiononLinux = 112;
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
	public $HowtoremovePHPerrorsafterinstallingMutillidae = 7;
	public $BurpSuite2HowtoInstallonWindows = 163;
	public $BurpSuite2HowtoInstallonLinux = 164;
	public $BurpSuite2CreateShortcutonDesktopLinux = 165;
	public $BurpSuite2ConfigureFirefoxwithBurpSuite = 166;
	public $BurpSuite2AddingBurpsCertificatetoFirefox = 167;
	public $BurpSuite2ConfiguringInterceptFeature = 168;
	public $BurpSuite2SettingScope = 169;
	public $BurpSuite2ConfiguringSiteMapandTargets = 170;
	public $BurpSuite2SiteMapFilters = 171;
	public $BurpSuite2ProxyHistory = 172;
	public $BurpSuite2RepeaterTool = 173;
	public $BurpSuite2IntruderToolSniperMode = 174;
	public $BurpSuite2IntruderToolBatteringRamMode = 175;
	public $BurpSuite2IntruderToolPitchforkMode = 176;
	public $BurpSuite2IntruderToolClusterMode = 177;
	public $BurpSuite2ComparerTool = 178;
	public $BurpSuite2DecoderTool = 179;
	public $BurpSuite2AddingExtentions = 180;
	public $BurpSuite2ConfiguringUpstreamProxy = 181;
	public $HowtoInstallJavaonWindows = 182;
	public $HowtoInstallOWASPZAPonWindows = 183;
	public $HowtoinstallJavaonLinuxDebianUbuntuKali = 184;
	public $HowtoInstallOWASPZAPonUbuntu = 185;
	public $HowtoInstallOWASPZAPonLinux = 186;
	public $HowtoCreateShortcutforOWASPZAPLinux = 187;
	public $HowtoInstallandConfigureFoxyProxywithFirefox = 188;
	public $HowtoProxyWebTrafficthroughOWASPZAP = 189;
	public $HowtoInterceptHTTPRequestswithOWASPZAP = 190;
	public $HowtoSpideraWebSitewithOWASPZAP = 191;
	public $OWASPZAPBreakpointsPart1TrappingHTTPRequests = 192;
	public $OWASPZAPBreakpointsPart2TrappingSpecificHTTPRequests = 193;
	public $HowtoFuzzWebApplicationswithOWASPZAPPart1 = 194;
	public $HowtoFuzzWebApplicationswithOWASPZAPPart2 = 195;
	public $OWASPZAPWebAppVulnerabilityAssessmentSinglePage = 196;
	public $OWASPZAPAutomatedWebAppVulnerabilityAssessmentEntireSite = 197;
	public $OWASPZAPWebAppVulnerabilityAssessmentPartialSite = 198;
	public $HowtoStartOWASPZAPfromCommandLine = 199;
	public $ExtendingOWASPZAPwithAddOns = 200;
	public $UsingOWASPZAPwithBurpSuiteBestofBothWorlds = 201;
	public $HTMLControlsarenotSecurityControls = 202;
	public $BurpSuite2InspectingWebSockets = 203;
	public $HowtocheckHTTPScertificatefromcommandline = 204;
	public $HowtocheckHTTPSCertificatesforcommonIssues = 205;
	public $cURLErrorSSLCertificateProblem = 206;
	public $WhatisContentSecurityPolicyPart1 = 207;
	public $WhatisContentSecurityPolicyPart2 = 208;
	public $WhatisContentSecurityPolicyPart3 = 209;
	public $WhatisContentSecurityPolicyPart4 = 210;
	public $WhatisContentSecurityPolicyPart5 = 211;
	public $ContentSecurityPolicyScriptSourcescriptsrc = 212;
	public $ContentSecurityPolicyFrameAncestors = 213;
	public $WhatareWebServerBanners = 214;
	public $HowtoSetHTTPHeadersUsingApacheServer = 215;
	public $CheckHTTPHeaderswithcURL = 216;
	public $HowCacheControlHeadersWork = 217;
	public $WhatisHTTPStrictTransportSecurityHSTS = 218;
	public $WhatistheHSTSPreloadlist = 219;
	public $CookiesPart1HowHTTPOnlyWorks = 220;
	public $CookiesPart2HowSecureCookiesWork = 221;
	public $CookiesPart3HowSameSiteWorks = 222;
	public $WhatistheXFrameOptionsHeader = 223;
	public $WhatistheXContentTypeOptionsHeader = 224;
	public $WhatistheReferrerPolicyHeader = 225;
	public $WhatistheXSSProtectionHeader = 226;
	public $SSLScanPart1HowtotestHTTPS = 227;
	public $SSLScanPart2HowtoInterprettheResults = 228;
	public $HowtoInstallSSLScanonWindows = 229;
	public $HowtoinstallJavaonLinuxDebian = 231;
	public $OutputEncodingPart1HowItStopsCrosssiteScriptingXSS = 234;
	public $OutputEncodingPart2HowtoTestifitWorks = 235;
	public $HowCrosssiteRequestForgeryCSRFTokensWork = 236;
	public $WhatisCORSPart1Explanation = 237;
	public $WhatisCORSPart2Demonstration = 238;
	public $CheckforVulnerableLibrariesinYourWebApplication = 239;
	public $HowtoEnableApacheModHeader = 240;
	public $WhatisCertificateTransparencyPart1 = 241;
	public $WhatisCertificateTransparencyPart2ExpectCTHeader = 242;
	public $HowtoCheckHTTPHeadersCommandLine = 243;
	public $HowtoCheckHTTPHeadersfromBrowser = 244;
	public $MutillidaeLab1Walkthrough = 245;
	public $HowtoInstallWiresharkinWindows10 = 246;
	public $IntroductiontoNetworkPacketAnalysiswithWireshark = 247;
	public $IntroductiontoPacketAnalysisPart7CapturingNetworkTrafficwithTCPDumpPart1 = 248;
	public $IntroductiontoPacketAnalysisPart8CapturingNetworkTrafficwithTCPDumpPart2 = 249;
	public $IntroductiontoPacketAnalysisPart9PacketAnalysiswithWiresharkPart1 = 250;
	public $IntroductiontoPacketAnalysisPart10PacketAnalysiswithWiresharkPart2 = 251;
	public $MutillidaeLab2Walkthrough = 252;
	public $MutillidaeLab3Walkthrough = 253;
	public $MutillidaeLab4Walkthrough = 254;
	public $MutillidaeLab5Walkthrough = 255;
	public $MutillidaeLab6Walkthrough = 256;
	public $MutillidaeLab7Walkthrough = 257;
	public $MutillidaeLab8Walkthrough = 258;
	public $MutillidaeLab9Walkthrough = 259;
	public $MutillidaeLab10Walkthrough = 260;
	public $MutillidaeLab11Walkthrough = 261;
	public $MutillidaeLab12Walkthrough = 262;
	public $MutillidaeLab13Walkthrough = 263;
	public $MutillidaeLab14Walkthrough = 264;
	public $MutillidaeLab15Walkthrough = 265;
	public $MutillidaeLab16Walkthrough = 266;
	public $MutillidaeLab17Walkthrough = 267;
	public $MutillidaeLab18Walkthrough = 268;
	public $MutillidaeLab19Walkthrough = 269;
	public $MutillidaeLab20Walkthrough = 270;
	public $MutillidaeLab21Walkthrough = 271;
	public $MutillidaeLab22Walkthrough = 272;
	public $MutillidaeLab23Walkthrough = 273;
	public $MutillidaeLab24Walkthrough = 274;
	public $MutillidaeLab25Walkthrough = 275;
	public $MutillidaeLab26Walkthrough = 276;
	public $MutillidaeLab27Walkthrough = 277;
	public $MutillidaeLab28Walkthrough = 278;
	public $MutillidaeLab29Walkthrough = 279;
	public $MutillidaeLab30Walkthrough = 280;
	public $MutillidaeLab31Walkthrough = 281;
	public $MutillidaeLab32Walkthrough = 282;
	public $MutillidaeLab33Walkthrough = 283;
	public $MutillidaeLab34Walkthrough = 284;
	public $MutillidaeLab35Walkthrough = 285;
	public $MutillidaeLab36Walkthrough = 286;
	public $MutillidaeLab37Walkthrough = 287;
	public $MutillidaeLab38Walkthrough = 288;
	public $MutillidaeLab39Walkthrough = 289;
	public $MutillidaeLab40Walkthrough = 290;
	public $MutillidaeLab41Walkthrough = 291;
	public $MutillidaeLab42Walkthrough = 292;
	public $MutillidaeLab43Walkthrough = 293;
	public $MutillidaeLab44Walkthrough = 294;
	public $MutillidaeLab45Walkthrough = 295;
	public $MutillidaeLab46Walkthrough = 296;
	public $MutillidaeLab47Walkthrough = 297;
	public $MutillidaeLab48Walkthrough = 298;
	public $MutillidaeLab49Walkthrough = 299;
	public $MutillidaeLab50Walkthrough = 300;
	public $MutillidaeLab51Walkthrough = 301;
	public $MutillidaeLab52Walkthrough = 302;
	public $MutillidaeLab53Walkthrough = 303;
	public $MutillidaeLab54Walkthrough = 304;
	public $MutillidaeLab55Walkthrough = 305;
	public $MutillidaeLab56Walkthrough = 306;
	public $MutillidaeLab57Walkthrough = 307;
	public $MutillidaeLab58Walkthrough = 308;
	public $MutillidaeLab59Walkthrough = 309;
	public $MutillidaeLab60Walkthrough = 310;
	public $MutillidaeLab61Walkthrough = 311;
	public $MutillidaeLab62Walkthrough = 312;
	public $MutillidaeLab63Walkthrough = 313;

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
