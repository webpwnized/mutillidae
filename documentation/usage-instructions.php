<?php

	try{
		/* ------------------------------------------
		 * Constants used in application
		* ------------------------------------------ */
		require_once ('./includes/constants.php');

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

<div class="page-title">Usage Instructions</div>

<?php include_once (__ROOT__.'/includes/back-button.inc');?>

<table>
	<tr>
		<td style="width:800px;">
			Mutillidae implements vulnerabilities from the
			<a href="http://www.owasp.org/index.php/OWASP_Top_Ten_Project" target="_blank">OWASP Top 10</a>
			2013, 2010 and 2007 in PHP.
			Additionally vulnerabilities from the SANS Top 25 Programming Errors and select information
			disclosure vulnerabilities have been added on various pages.
			<br/><br/><br/>
			<span class="report-header">Optional Configuration</span>
			<br/><br/>
			Instructional videos are available to help set up an HTTPS TLS certificate or Apache virtual hosts
			<br/>
			<?php echo $YouTubeVideoHandler->getYouTubeVideo($YouTubeVideoHandler->HowtoCreateSelfSignedCertificateinApache);?>
			<?php echo $YouTubeVideoHandler->getYouTubeVideo($YouTubeVideoHandler->HowtoCreateVirtualHostsinApache);?>
			<br/><br/><br/>
			<span class="report-header">Top Menu Bar</span>
			<br/><br/>
			<span class="label">Home:</span> Takes user to Home page<br/>
			<span class="label">Login/Register:</span> Takes user to Login page<br/>
			<span class="label">Toggle Hints:</span> Shows or hides the Hints on vulnerable pages<br/>
			<span class="label">Toggle Security:</span> Changes the security level between insecure, client-side security and secure<br/>
			<span class="label">Enforce TLS:</span> When enforced, Mutillidae automatically redirects all HTTP requests to HTTPS<br/>
			<span class="label">Reset DB:</span> Drops and rebuilds all database tables and resets the project<br/>
			<span class="label">View Log:</span> Takes the user to view the log<br/>
			<span class="label">View Captured Data:</span> Takes the user to the view the captured data<br/>
			<br/><br/>
			<span class="report-header">Left Menu Bar</span>
			<br/><br/>
			The menu on the left is organized by category then vulnerability. Some vulnerabilities
			will be in more than one category as there is overlap between categories. Each
			page in Mutillidae will expose multiple vulnerabilities. Some pages have half a dozen
			and/or multiple critical vulnerabilities on the same page. The page will appear in the menu
			under each vulnerability.
			<br/><br/>
			A <a title="Listing of vulnerabilities" href="./index.php?page=./documentation/vulnerabilities.php">listing of vulnerabilities</a>
			is available in menu under documentation or by clicking
			<a title="Listing of vulnerabilities" href="./index.php?page=./documentation/vulnerabilities.php">here</a>.
			<br/><br/><br/>
			<span id="videos" class="report-header">Videos</span>
			<br/><br/>
			The videos on the Webpwnized YouTube Channel are likely to be a some assistance. Videos
			cover installation, using tools like Burp-Suite and exploits for various
			vulnerabilities.
			<br/><br/>
			<a href="http://www.youtube.com/user/webpwnized" target="_blank">
			<img align="middle" alt="Webpwnized YouTube Channel" src="./images/youtube-play-icon-40-40.png" />
				Video Tutorials
			</a>
			<br/><br/><br/>
			<span class="report-header">Page Hints</span>
			<br/><br/>
			Besides the menus, this will be the most important feature for newcomers. To enable hints,
			toggle the "Show Hints" button (top menu bar). A hints section will appear IF the page contains
			vulnerabilities. The Hints are "smart" showing only those hints that will help on the particular
			page.
			<br/><br/><br/>
			<span class="report-header">Security Modes</span>
			<br/><br/>
			Mutillidae currently has three modes: completely insecure, client-side security and secure.
			In insecure and client-side mode, the pages are vulnerable to at least the topic they
			fall under in the menu. Note that client-side security mode is just as vulnerable as
			insecure mode, but JavaScript validation or HTML controls make exploits somewhat more
			difficult.
			<br/><br/>
			In secure mode,
			Mutillidae attempts to protect the pages with server side scripts. Also, hints are disabled.
			<br/><br/>
			The mode can be changed using the "Toggle Security" button on the top menu bar.
			<br/><br/><br/>
			<span class="report-header">"Help Me" Button</span>
			<br/><br/>
			The "Help Me" button provides a basic
			description of the vulnerabilities on the page for which the user should try exploits.
			Use this button to get a quick list of issues. Use the Hints to see more details.
			<br/><br/><br/>
			<span class="report-header">Just give me the exploit</span>
			<br/><br/>
			Hints will typically provide some exploits.
			Known exploits that are used in testing Mutillidae are located in
			/documentation/mutillidae-test-scripts.txt. There is some documentation for each exploit
			which explains usage and location.
			<br/><br/><br/>
			<span class="report-header">Be Careful</span>
			<br/><br/>
			Mutillidae is a "live" system. The vulnerabilities are real rather than emulated. This eliminates
			the frustration of having to "know what the author wants". Because of this, there are likely
			undocumented vulnerabilities. Also, this project endangers any machine on which it runs. Best practice
			is to run Mutillidae in a virtual machine isolated from the network which is only booted
			when using Mutillidae. Every effort has been made to make Mutillidae ables run entirely off-line.
			<br/><br/><br/>
			<span class="report-header">Whitepaper</span>
			<br/><br/>
			A project whitepaper is available to explain the features of Mutillidae and suggested use-cases.
			<br/><br/>
			<a
				href="https://www.sans.org/reading-room/whitepapers/application/introduction-owasp-mutillidae-ii-web-pen-test-training-environment-34380"
				target="_blank"
				title="Whitepaper: Introduction to OWASP Mutillidae II Web Pen Test Training Environment"
			>
				<img align="middle" alt="Webpwnized Twitter Channel" src="./images/pdf-icon-48-48.png" />
				Introduction to OWASP Mutillidae II Web Pen Test Training Environment
			</a>
		</td>
	</tr>
</table>