			<br/><br/>
			<span class="report-header">Overview</span>
			<br/><br/>
			Directory Browsing is allowed when the web server is misconfigured to show the
			user the contents of directories on the server.
<br/><br/>
<a href="#videos" class="label"><img alt="YouTube" src="/images/youtube-48-48.png" style="margin-right: 10px;" />Video Tutorials</a>
			<br/><br/>
			<span class="report-header">Discovery Methodology</span>
			<br/><br/>
			Use search engines to look for pages which include "index of" in the title.
			Additionally attempt to read the robots.txt file and spider the application
			with a tool such as Burp-Suite, OWASP ZAP, or Nikto, and directories
			named by search engines.
			<br/><br/>
			<span class="report-header">Exploitation</span>
			<br/><br/>
			Catalog and inspect the folders named in robots.txt and any directories
			discovered during spidering. Use a tool such as Burp-Intruder to brute-force
			sub-directory names in the root directory and other discovered directories. 
			<br/><br/>
			<span id="videos" class="report-header">Videos</span>
			<br/><br/>
			<?php echo $YouTubeVideoHandler->getYouTubeVideo($YouTubeVideoHandler->SpideringWebApplicationswithBurpSuite);?>
			<?php echo $YouTubeVideoHandler->getYouTubeVideo($YouTubeVideoHandler->HowtouseWGETtocloneaWebSite);?>
			<?php echo $YouTubeVideoHandler->getYouTubeVideo($YouTubeVideoHandler->HowtoInstalldirbonLinux);?>
			<?php echo $YouTubeVideoHandler->getYouTubeVideo($YouTubeVideoHandler->HowtoUsedirbtoLocateHiddenDirectoriesonaWebSite);?>
			<?php echo $YouTubeVideoHandler->getYouTubeVideo($YouTubeVideoHandler->HowtoInstallOWASPDirBusteronLinux);?>
			<?php echo $YouTubeVideoHandler->getYouTubeVideo($YouTubeVideoHandler->HowtouseOWASPDirBustertoDiscoverHiddenDirectoriesonWebSites);?>
			<?php echo $YouTubeVideoHandler->getYouTubeVideo($YouTubeVideoHandler->HowtoInstallOWASPZaponLinux);?>
			<br/><br/>