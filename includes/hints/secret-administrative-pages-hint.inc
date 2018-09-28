			<br/><br/>
			<span class="report-header">Overview</span>
			<br/><br/>
			Secret Administrative Pages are surprisingly common. Developers assume that it
			is not possible to determine the URL so the pages are secure.
<br/><br/>
<a href="#videos" class="label"><img alt="YouTube" src="/images/youtube-48-48.png" style="margin-right: 10px;" />Video Tutorials</a>
			<br/><br/>
			<span class="report-header">Discovery Methodology</span>
			<br/><br/>
			Try brute forcing the page names in the page parameter with 
			Burp-Intruder in sniper mode. Include some of the following 
			page names in the brute force list: 
			secret.php, admin.php, _adm.php, _admin.php, root.php, 
			administrator.php, auth.php, hidden.php,
	 		console.php, conf.php, _private.php, private.php, 
	 		access.php, control.php, control-panel.php, phpMyAdmin.php
			<br/><br/>
			<span class="report-header">Exploitation</span>
			<br/><br/>
			Same as discovery.
			<br/><br/>
			<span class="report-header">Example</span>
			<br/><br/>
	  		The phpinfo function dumps PHP server configuration information to a nice table.
			The phpMyAdmin.php hosts a secret phpMyAdmin console.
			<br/><br/>
			<span id="videos" class="report-header">Videos</span>
			<br/><br/>
			<?php echo $YouTubeVideoHandler->getYouTubeVideo($YouTubeVideoHandler->MutillidaeHowtoShowSecretPageinSecurityLevel5);?>
			<?php echo $YouTubeVideoHandler->getYouTubeVideo($YouTubeVideoHandler->BruteForcePageNamesusingBurpSuiteIntruder);?>
			<?php echo $YouTubeVideoHandler->getYouTubeVideo($YouTubeVideoHandler->IntroductiontoFuzzingWebApplicationswithBurpSuiteIntruderTool);?>
			<?php echo $YouTubeVideoHandler->getYouTubeVideo($YouTubeVideoHandler->UsingBurpIntruderSnipertoFuzzParameters);?>
			<?php echo $YouTubeVideoHandler->getYouTubeVideo($YouTubeVideoHandler->IntroductiontoBurpSuiteComparerTool);?>
			<?php echo $YouTubeVideoHandler->getYouTubeVideo($YouTubeVideoHandler->GainingAdministrativeShellAccessviaCommandInjection);?>
			<?php echo $YouTubeVideoHandler->getYouTubeVideo($YouTubeVideoHandler->HowtoLocatetheEastereggFileusingCommandInjection);?>
			<?php echo $YouTubeVideoHandler->getYouTubeVideo($YouTubeVideoHandler->HowtoInstalldirbonLinux);?>
			<?php echo $YouTubeVideoHandler->getYouTubeVideo($YouTubeVideoHandler->HowtoUsedirbtoLocateHiddenDirectoriesonaWebSite);?>
			<?php echo $YouTubeVideoHandler->getYouTubeVideo($YouTubeVideoHandler->HowtoInstallOWASPDirBusteronLinux);?>
			<?php echo $YouTubeVideoHandler->getYouTubeVideo($YouTubeVideoHandler->HowtouseOWASPDirBustertoDiscoverHiddenDirectoriesonWebSites);?>
			<br/><br/>	