			<br/><br/>
			<span class="report-header">Overview</span>
			<br/><br/>
			Click Jacking may occcur when an attacker is able to frame the target web page.
			This can allow the attacker to overlay an invisible block of HTML such as an
			invisible form. When the user clicks on what they think is the legitimate web
			page, they are interacting with the overlay instead. If the overlay happens to
			be a form (for example), the user is filling out and submitting the fake form.
<br/><br/>
<a href="#videos" class="label"><img alt="YouTube" src="/images/youtube-play-icon-40-40.png" style="margin-right: 10px;" />Video Tutorials</a>
<br/><br/>
<span class="report-header">Discovery Methodology</span>
<br/><br/>
Check to see if the site send the X-FRAME-OPTIONS HTTP header with a value of
DENY or SAMEORIGIN. If the X-FRAME-OPTIONS HTTP header is not present in each
response, check if JavaScript "frame-busting" code is present.
<br/><br/>
<span class="report-header">Exploitation</span>
<br/><br/>
Frame the target web application page and overlay the page with the desired
HTML. Send a link to the framing page to target users.
<br/><br/>
<span class="report-header">Example</span>
<br/><br/>
Mutillidae overlays the page with a div which follows the mouse around.
When the user clicks anywhere, they inevitably click the div which then executes
the JavaScript in the onclick event.
<br/><br/>
<span id="videos" class="report-header">Videos</span>
<br/><br/>
<?php echo $YouTubeVideoHandler->getYouTubeVideo($YouTubeVideoHandler->WhatistheXFrameOptionsHeader);?>
<?php echo $YouTubeVideoHandler->getYouTubeVideo($YouTubeVideoHandler->HowtoSetHTTPHeadersUsingApacheServer);?>
<?php echo $YouTubeVideoHandler->getYouTubeVideo($YouTubeVideoHandler->CheckHTTPHeaderswithcURL);?>
<?php echo $YouTubeVideoHandler->getYouTubeVideo($YouTubeVideoHandler->HowtoEnableApacheModHeader);?>
<?php echo $YouTubeVideoHandler->getYouTubeVideo($YouTubeVideoHandler->HowtoCheckHTTPHeadersCommandLine);?>
<?php echo $YouTubeVideoHandler->getYouTubeVideo($YouTubeVideoHandler->HowtoCheckHTTPHeadersfromBrowser);?>
<br/><br/>
