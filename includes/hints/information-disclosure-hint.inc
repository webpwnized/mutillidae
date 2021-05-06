<br/><br/>
<span class="report-header">Overview</span>
<br/><br/>
Information Disclosure may result when internal information is
disclosed to the user-agent (browser). These paths can be
used in other attacks.
<br/><br/>
<a href="#videos" class="label"><img alt="YouTube" src="/images/youtube-play-icon-40-40.png" style="margin-right: 10px;" />Video Tutorials</a>
<br/><br/>
<span class="report-header">Discovery Methodology</span>
<br/><br/>
Attempt to discover if it is possible to cause errors by injecting
all input parameters with characters that are reserved in various
contexts.
Search web page sources (view source) for internal information
disclosure.
Search for custom administrative pages and administrative consoles.
<br/><br/>
<span class="report-header">Exploitation</span>
<br/><br/>
Search pages with and without injection. Use the grep feature of
Burp-Suite to seach for inappropriate information. Search for known
common administrative consoles such as Drupal and Wordpress consoles.
<br/><br/>
<span id="videos" class="report-header">Videos</span>
<br/><br/>
<?php echo $YouTubeVideoHandler->getYouTubeVideo($YouTubeVideoHandler->HowtoInstalldirbonLinux);?>
<?php echo $YouTubeVideoHandler->getYouTubeVideo($YouTubeVideoHandler->HowtoInstallOWASPDirBusteronLinux);?>
<br/><br/>