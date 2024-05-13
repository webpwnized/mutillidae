			<br/><br/>
			<span class="report-header">Overview</span>
			<br/><br/>
			Server-side Request Forgery (SSRF) allows an attacker to cause the application to browse to
			an arbitrary HTTP endpoint of the attackers choosing. Since the application server may not be
			segmented properly, the application may have access to HTTP endpoints behind the firewall.
<br/><br/>
<a href="#videos" class="label"><img alt="YouTube" src="/images/youtube-play-icon-40-40.png" style="margin-right: 10px;" />Video Tutorials</a>
			<br/><br/>
			<span class="report-header">Discovery Methodology</span>
			<br/><br/>
			Locate an input parameter that allows control of the URL that the application browses to. The
			input parameter may be hidden.
			<br/><br/>
			<span class="report-header">Exploitation</span>
			<br/><br/>
			Change the value of the input parameter to point to an HTTP endpoint that the application server
			has access to.
			<br/><br/>
<br/>
<span id="videos" class="report-header">Videos</span>
<br/><br/>
<?php echo $YouTubeVideoHandler->getYouTubeVideo($YouTubeVideoHandler->WhatisInsecureDirectObjectReferenceIDOR);?>
<?php echo $YouTubeVideoHandler->getYouTubeVideo($YouTubeVideoHandler->WhatisServerSideRequestForgerySSRF);?>
<br/><br/>