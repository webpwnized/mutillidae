			<br/><br/>
			<span class="report-header">Overview</span>
			<br/><br/>
			Secret Administrative Pages are surprisingly common. Developers assume that it
			is not possible to determine the URL so the pages are secure.
<br/><br/>
<a href="#videos" class="label"><img alt="YouTube" src="/images/youtube-play-icon-40-40.png" style="margin-right: 10px;" />Video Tutorials</a>
<br/><br/>
<span class="report-header">Discovery Methodology</span>
<br/><br/>
Try brute forcing the page names in the page parameter with
Burp-Intruder in sniper mode. Include some of the following
page names in the brute force list:
secret.php, admin.php, _adm.php, _admin.php, root.php,
administrator.php, auth.php, hidden.php,
console.php, conf.php, _private.php, private.php,
access.php, control.php, control-panel.php
<br/><br/>
<span class="report-header">Exploitation</span>
<br/><br/>
Same as discovery.
<br/><br/>
<span class="report-header">Example</span>
<br/><br/>
The phpinfo function dumps PHP server configuration information to a nice table.
<br/><br/>
<span id="videos" class="report-header">Videos</span>
<br/><br/>
<?php echo $YouTubeVideoHandler->getYouTubeVideo($YouTubeVideoHandler->HowtoInstallOWASPZaponLinux);?>
<?php echo $YouTubeVideoHandler->getYouTubeVideo($YouTubeVideoHandler->HowtoSpideraWebSitewithOWASPZAP);?>
<?php echo $YouTubeVideoHandler->getYouTubeVideo($YouTubeVideoHandler->HowtoInstalldirbonLinux);?>
<?php echo $YouTubeVideoHandler->getYouTubeVideo($YouTubeVideoHandler->HowtoInstallOWASPDirBusteronLinux);?>
<br/><br/>