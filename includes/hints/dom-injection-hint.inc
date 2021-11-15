			<br/><br/>
			<span class="report-header">Overview</span>
			<br/><br/>
			DOM Injection may occcur when the web page uses DOM manipulation to update
			the HTML of the page in response to user input. This is typically done
			using JavaScript.
<br/><br/>
<a href="#videos" class="label"><img alt="YouTube" src="/images/youtube-play-icon-40-40.png" style="margin-right: 10px;" />Video Tutorials</a>
<br/><br/>
<span class="report-header">Discovery Methodology</span>
<br/><br/>
Locate pages which use DOM manipulation to update the page without
returning to the server. This can often be detected when a client proxy
such as Burp does NOT intercept a request when a page updates.
<br/><br/>
<span class="report-header">Exploitation</span>
<br/><br/>
Inject the field or event from which the page will be updated and inject HTML or
JavaScript into the field or event.
<br/><br/>
<span class="report-header">Example</span>
<br/><br/>
Use Firebug or similar to examine the message that appears when a new
item is added to storage. The message appears in a label below the two input fields.
Inject XSS into the "key" field. This is output
into the message. Craft a XSS to read the DOM storage or perform other
action.
<br/><br/>
<span id="videos" class="report-header">Videos</span>
<br/><br/>
<?php echo $YouTubeVideoHandler->getYouTubeVideo($YouTubeVideoHandler->CrossSiteScriptingPart1WhatisReflectedXSS);?>
<?php echo $YouTubeVideoHandler->getYouTubeVideo($YouTubeVideoHandler->CrossSiteScriptingPart2WhatisDOMbasedXSS);?>
<?php echo $YouTubeVideoHandler->getYouTubeVideo($YouTubeVideoHandler->CrossSiteScriptingPart3WhatisPersistentXSS);?>
<?php echo $YouTubeVideoHandler->getYouTubeVideo($YouTubeVideoHandler->OutputEncodingPart1HowItStopsCrosssiteScriptingXSS);?>
<?php echo $YouTubeVideoHandler->getYouTubeVideo($YouTubeVideoHandler->OutputEncodingPart2HowtoTestifitWorks);?>
<?php echo $YouTubeVideoHandler->getYouTubeVideo($YouTubeVideoHandler->WhatisContentSecurityPolicyPart1);?>
<?php echo $YouTubeVideoHandler->getYouTubeVideo($YouTubeVideoHandler->WhatisContentSecurityPolicyPart2);?>
<?php echo $YouTubeVideoHandler->getYouTubeVideo($YouTubeVideoHandler->WhatisContentSecurityPolicyPart3);?>
<?php echo $YouTubeVideoHandler->getYouTubeVideo($YouTubeVideoHandler->WhatisContentSecurityPolicyPart4);?>
<?php echo $YouTubeVideoHandler->getYouTubeVideo($YouTubeVideoHandler->WhatisContentSecurityPolicyPart5);?>
<?php echo $YouTubeVideoHandler->getYouTubeVideo($YouTubeVideoHandler->ContentSecurityPolicyScriptSourcescriptsrc);?>
<?php echo $YouTubeVideoHandler->getYouTubeVideo($YouTubeVideoHandler->HowtoSetHTTPHeadersUsingApacheServer);?>
<?php echo $YouTubeVideoHandler->getYouTubeVideo($YouTubeVideoHandler->CheckHTTPHeaderswithcURL);?>
<?php echo $YouTubeVideoHandler->getYouTubeVideo($YouTubeVideoHandler->HowtoCheckHTTPHeadersCommandLine);?>
<?php echo $YouTubeVideoHandler->getYouTubeVideo($YouTubeVideoHandler->HowtoCheckHTTPHeadersfromBrowser);?>
<?php echo $YouTubeVideoHandler->getYouTubeVideo($YouTubeVideoHandler->CookiesPart1HowHTTPOnlyWorks);?>
<?php echo $YouTubeVideoHandler->getYouTubeVideo($YouTubeVideoHandler->WhatistheXSSProtectionHeader);?>
<?php echo $YouTubeVideoHandler->getYouTubeVideo($YouTubeVideoHandler->CheckforVulnerableLibrariesinYourWebApplication);?>
<?php echo $YouTubeVideoHandler->getYouTubeVideo($YouTubeVideoHandler->HowtoEnableApacheModHeader);?>
<br/><br/>