			<br/><br/>
			<span class="report-header">Overview</span>
			<br/><br/>
			Application Path Disclosure may result when internal application paths
			are disclosed to the user-agent (browser). These paths can be
			used in other attacks such as forced browsing.
<br/><br/>
<a href="#videos" class="label"><img alt="YouTube" src="/images/youtube-play-icon-40-40.png" style="margin-right: 10px;" />Video Tutorials</a>
<br/><br/>
<span class="report-header">Discovery Methodology</span>
<br/><br/>
Attempt to discover if it is possible to cause errors by injecting
all input parameters with characters that are reserved in various
contexts. Search web page sources (view source) for internal application
paths.
<br/><br/>
<span class="report-header">Exploitation</span>
<br/><br/>
Search pages with and without injection. Use the grep feature of
Burp-Suite to seach for application path patterns that match
the web application framework type.
<br/><br/>
<span id="videos" class="report-header">Videos</span>
<br/><br/>
<?php echo $YouTubeVideoHandler->getYouTubeVideo($YouTubeVideoHandler->IntroductiontoFuzzingWebApplicationswithBurpSuiteIntruderTool);?>
<?php echo $YouTubeVideoHandler->getYouTubeVideo($YouTubeVideoHandler->FindingCommentsandFileMetadatausingMultipleTechniques);?>
<?php echo $YouTubeVideoHandler->getYouTubeVideo($YouTubeVideoHandler->HowtoInstalldirbonLinux);?>
<?php echo $YouTubeVideoHandler->getYouTubeVideo($YouTubeVideoHandler->HowtoInstallOWASPDirBusteronLinux);?>
<br/><br/>