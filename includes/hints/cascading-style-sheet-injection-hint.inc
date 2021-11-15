			<br/><br/>
			<span class="report-header">Overview</span>
			<br/><br/>
			Cascading style injection may occcur
			when user or attacker controlled input is later incorporated
			without being encoded into the web server response with a style attribute.
			In other words, the attacker
			can send input which later is incorporated into the web page the user receives.
<br/><br/>
<a href="#videos" class="label"><img alt="YouTube" src="/images/youtube-play-icon-40-40.png" style="margin-right: 10px;" />Video Tutorials</a>
			<br/><br/>
			<span class="report-header">Discovery Methodology</span>
			<br/><br/>
			Inject all available parameters of the web page with a searchable string
			such as the word "CANARY" along with characters generally useful in
			writing HTML, JavaScript or other code. Search the response carefully
			noting any location where the test string appears unencoded in a style attribute.
			These locations may allow Cascading style injection.
			<br/><br/>
			Hint: An example injection might be &lt;CANARY={}&quot;&quot;()&#39;&#39;;#$--/&gt;1.
			Adding a sequencial integer to the test input can help determine which of the
			inputs parameters resulted in the response string found.
			<br/><br/>
			<span class="report-header">Exploitation</span>
			<br/><br/>
			Determine the prefix and suffix needed to make the injected code "fit" syntatically
			then add a payload between. Inject the exploit.
			<br/><br/>
			<span class="report-header">Example</span>
			<br/><br/>
			Example Target:&lt;body style="color:#{dynamic input}"&gt;
			<br/><br/>
			Possible Solution:<span class="label">style="&lt;body color:#"<span style="color:red;">"&gt;&lt;H1&gt;HELLO WORLD&lt;/H1&gt;&lt;</span> anything="</span>"&gt;
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