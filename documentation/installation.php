<?php

	try{
		/* ------------------------------------------
		 * Constants used in application
		* ------------------------------------------ */
		require_once ('./includes/constants.php');

		/* We use the session on this page */
		if (session_status() == PHP_SESSION_NONE){
		    session_start();
		}// end if

		if (!isset($_SESSION["security-level"])){
			$_SESSION["security-level"] = 0;
		}// end if

		/* ------------------------------------------
		 * initialize custom error handler
		* ------------------------------------------ */
		require_once (__ROOT__.'/classes/CustomErrorHandler.php');
		if (!isset($CustomErrorHandler)){
			$CustomErrorHandler =
			new CustomErrorHandler(__ROOT__.'/owasp-esapi-php/src/', $_SESSION["security-level"]);
		}// end if

		/* ------------------------------------------
		 * initialize SQL Query Handler
		* ------------------------------------------ */
		require_once (__ROOT__.'/classes/SQLQueryHandler.php');
		$SQLQueryHandler = new SQLQueryHandler(__ROOT__."/owasp-esapi-php/src/", $_SESSION["security-level"]);

		/* ------------------------------------------
		 * initialize You Tube Video Handler Handler
		* ------------------------------------------ */
		require_once (__ROOT__.'/classes/YouTubeVideoHandler.php');
		$YouTubeVideoHandler = new YouTubeVideoHandler("owasp-esapi-php/src/", $_SESSION["security-level"]);

		if (isset($_REQUEST["level1HintIncludeFile"])) {
			$lIncludeFileKey = $_REQUEST["level1HintIncludeFile"];
		}else{
			$lIncludeFileKey = 52; // hints-not-found.inc;
		}// end if

		$lIncludeFileRecord = $SQLQueryHandler->getLevelOneHelpIncludeFile($lIncludeFileKey);

		if ($SQLQueryHandler->affected_rows()>0) {
			$lRecord = $lIncludeFileRecord->fetch_object();
			$lIncludeFile = $lRecord->level_1_help_include_file;
			$lIncludeFileDescription = $lRecord->level_1_help_include_file_description;
		}else{
			$lIncludeFile = 'hint-not-found.inc';
			$lIncludeFileDescription = 'Hint Not Found';
		}// end if

   	} catch (Exception $e) {
		echo $CustomErrorHandler->FormatError($e, $lQueryString);
   	}// end try;
?>

<div class="page-title">Installation Instructions</div>

<?php include_once (__ROOT__.'/includes/back-button.inc');?>


<br/><br/>
<span class="report-header">Overview</span>
<br/><br/>
<img alt="YouTube" src="/images/youtube-play-icon-40-40.png" style="margin-right: 10px;" />
Several videos provide comprehensive, step-by-step instructions
<br/>
<?php echo $YouTubeVideoHandler->getYouTubeVideo($YouTubeVideoHandler->HowtoCreateUbuntuVirtualMachineVirtualBox);?>
<?php echo $YouTubeVideoHandler->getYouTubeVideo($YouTubeVideoHandler->HowtoInstallVirtualBoxGuestAdditionsLinux);?>
<?php echo $YouTubeVideoHandler->getYouTubeVideo($YouTubeVideoHandler->HowtoCreateUbuntuVirtualMachineVMware);?>
<?php echo $YouTubeVideoHandler->getYouTubeVideo($YouTubeVideoHandler->LAMPStackPart1HowtoInstallApacheWebServer);?>
<?php echo $YouTubeVideoHandler->getYouTubeVideo($YouTubeVideoHandler->LAMPStackPart2HowtoInstallPHP);?>
<?php echo $YouTubeVideoHandler->getYouTubeVideo($YouTubeVideoHandler->LAMPStackPart3HowtoInstallMySQLServer);?>
<?php echo $YouTubeVideoHandler->getYouTubeVideo($YouTubeVideoHandler->HowtoInstallPHPCurlLibrary);?>
<?php echo $YouTubeVideoHandler->getYouTubeVideo($YouTubeVideoHandler->HowtoInstallPHPXMLLibrary);?>
<?php echo $YouTubeVideoHandler->getYouTubeVideo($YouTubeVideoHandler->HowtoInstallPHPmbstringLibrary);?>
<?php echo $YouTubeVideoHandler->getYouTubeVideo($YouTubeVideoHandler->HowtoDisplayErrorsinPHPPages);?>
<?php echo $YouTubeVideoHandler->getYouTubeVideo($YouTubeVideoHandler->HowtoInstallMutillidaeonLinux);?>
<?php echo $YouTubeVideoHandler->getYouTubeVideo($YouTubeVideoHandler->HowtoCreateSelfSignedCertificateinApache);?>
<?php echo $YouTubeVideoHandler->getYouTubeVideo($YouTubeVideoHandler->HowtoCreateVirtualHostsinApache);?>
<br/><br/><br/>
<span class="report-header">Other Installation Options</span>
<br/><br/>
<div style="margin:20px">
	<span class="label">Samurai Web Testing Framework</span>
	<div style="margin:20px">
		Samurai WTF is a free virtual environment.
		Within Samurai is several vulnerable web applications pre-configured to test for
		vulnerabilities. One of the applications is Mutillidae.
	</div>

	<span class="label">XAMPP (Windows , Linux , Mac OS X )</span>
	<div style="margin:20px">
		1.	XAMPP is a single installation package which bundles Apache web server,
			PHP application server, and MySQL database. XAMPP installs Apache and
			MySQL as either an executable or services and can optionally start these
			services automatically. Once installed XAMPP provides an "htdocs"
			directory. This directory is "root" meaning that if you browse to
			http://localhost/, the web site in that "htdocs" folder is what will
			be served. Mutillidae is installed by placing the multillidae folder
			into the htdocs folder. The result is that mutillidae is a sub-site
			served from the mutillidae folder. This makes the URL for mutillidae
			http://localhost/mutillidae.
	</div>
	<div style="margin:20px">
			The mutillidae files are already in a folder called "mutillidae" when
			the project is zipped. All that is required is to put the mutillidae
			folder into the htdocs directory.
	</div>
	<div style="margin:20px">
			The	Mutillidae package can be unzipped into htdocs to install Mutillidae.
			Simply unzip the compressed mutillidae folder right into the htdocs
			folder. When you are done, the "mutillidae" folder will be inside the
			"htdocs" folder of XAMMP. All the Mutillidae files are inside that
			"mutillidae" fodler. Assuming Apache and MySQL are running, the user
			can open a browser and immediately begin using Mutillidae at
			http://localhost/mutillidae. Apache automatically serves "index.php"
			which is located in the mutillidae folder.
	</div>
	<div style="margin:20px">
		2.	Download and install "XAMPP" or "XAMPP Lite" for Windows or Linux. If
			installing on Windows, when the installation asks if you want to install
			Apache and MySQL as services, answer "YES". This allows both to run as
			Windows services and be controlled via services.msc. Run services.msc
			by typing "services.msc" at the command line.
			(Start - Run - services.msc - Enter)
	</div>
	<div style="margin:20px">
		3. Download Mutillidae
	</div>
	<div style="margin:20px">
		4.	Unzip Mutillidae. Note the mutillidae project is in a folder called "mutillidae"
	</div>
	<div style="margin:20px">
		5.	Place the entire "mutillidae" directory into XAMPP " htdocs" directory
	</div>
	<div style="margin:20px">
		6.	Browse to mutillidae at http://localhost/mutillidae
	</div>
	<div style="margin:20px">
		7.	Click the "Setup/reset the DB" link in the main menu.
	</div>
	<div style="margin:20px">
		Important note: If you use XAMPP Lite or various version of XAMPP on various operating systems, the path for your
		php.ini file may vary. You may even have multiple php.ini files in which case try to modify the one in the Apache
		directory first, then the one in the PHP file if that doesnt do the trick.
	</div>
	<div style="margin:20px">
		Windows possible default location C:\xampp\php\php.ini, C:\XamppLite\PHP\php.ini, others
		Linux possible default locations: /XamppLite/PHP/php.ini, /XamppLite/apache/bin/php.ini, others
	</div>
	<div style="margin:20px">
		8.	By default, Mutillidae tries to connect to MySQL on the localhost with the username
		"root" and a password of "mutillidae". To change this, edit "includes/database-config.php"
		with the correct information for your environment.
	</div>
	<div style="margin:20px">
		9.	NOTE: Once PHP 6.0 arrives in XAMPP, E_ALL will include E_STRICT so the line
		to change will probably read "error_reporting = E_ALL". In any case, change
		the error_reporting line to
		"error_reporting = E_ALL &amp; ~E_NOTICE &amp; ~E_DEPRECIATED".
	</div>
	<div style="margin:20px">
		10. NOTE: Be sure magic quotes is disabled. In XAMMP it seems to be but using MMAP for
		Apple OS/X seems to have it enabled by default. Just make sure magic quotes is set to
		off in whatever framework is being used. This setting is in PHP.ini. This includes
		magic_quotes_gpc, magic_quotes_runtime, and magic_quotes_sybase.
	</div>

	<span class="label">Custom Linux ISO</span>
	<div style="margin:20px">
		Using the Samurai Web Testing Framework as the base operating system, any version of Mutillidae
		can be installed in addition to the version which comes standard with Samurai. From this custom set-up,
		a custom ISO can be generated using the Remastersys package.
	</div>
	<div style="margin:20px">
		With Samurai, Mutillidae is installed into the /srv/mutillidae directory. To install different
		versions of Mutillidae and make a custom Linux ISO, the following recipe can be followed:
	</div>
	<div style="margin:20px">
		1.	Locate the default installation directory of Mutillidae<br />
		2.	Rename the current installation. For example rename the "mutillidae" folder to "mutillidae.bak".<br />
		3.	Download the latest version of mutillidae<br />
		4.	Unzip the "mutillidae" folder from the latest version to the installation directory.<br />
		5.	Test that mutillidae is updated by browsing to http://localhost/mutillidae<br />
		6.	Make any changes to Linux, Firefox, or other software desired<br />
		7.	Ensure the current Remastersys installation is clean by running the command "sudo remastersys clean"<br />
		8.	When ready to create the new ISO, run the command "sudo remastersys backup"<br />
		9.	The custom ISO will be found in the /home/remastersys/remastersys directory<br />
	</div>

	<span class="label">Virtual Machine</span>
	<div style="margin:20px">
		Any of the previously mentioned installation options work equally well in virtual environments
	</div>
</div>
