			<br/><br/>
			<span class="report-header">Overview</span>
			<br/><br/>
			Javascript injection (or Javascript source code injection)
			is closely related to HTML injection and
			Cross site scripting. In all these cases the input is interpreted as
			code rather than data. The code may
			break out of the current context and switch to another context.
			Alternatively
			an injection may stay in the current context but modify the source code.
<br/><br/>
<a href="#videos" class="label"><img alt="YouTube" src="/images/youtube-play-icon-40-40.png" style="margin-right: 10px;" />Video Tutorials</a>
			<br/><br/>
			"Context" is the type of code and object into which the injection occurs.
			For Javascript injection, this is often the value of a Javascript string
			being dynamically set.
			<br/><br/>
		  	An example of breaking context is injecting script tags into HTML output. The developer
		  	believes the context should be HTML (perhaps a table), but the input of
		  	script tags (with embedded script) causes the browser to stop processing HTML
		  	and switch to processing script. The context switch occurs when the browser
		  	stops executing the HTML instructions and instead executes the JS.
			<br/><br/>
			Injection within context could be injecting HTML into HTML output. Although
			the page source code is altered, the context remains the same.
			<br/><br/>
			<span class="report-header">Discovery Methodology</span>
			<br/><br/>
			To find the injection, a canary can be used to check for two conditions. The first condition
			is user supplied or user controlled input is incorporated into Javascript. The second
			condition is that input is not encoded when output.
			<br/>
			Identify the possible input parameters. Does the page
			take input from a form, URL parameter, cookie, or other input? HTTPFox and Burp Suite
			are a good tools to see all the input as you "GET" a page (the request) and
			all the output with which the server responds (the response).
			<br/><br/>
			Once you find the page input, try injecting a simple canary like "CANARY-INPUT-1"
			then search the resulting page to see where the canary showed up. If the input
			is used to create a portion of the Javascript, it may be possible to break out
			of the context and inject user-controlled Javascript. To test if the output is encoded,
			inject a test string such as &quot;CANARY123!@#$%^&amp;*()_+-=[]{};&#39;:&quot;,./&lt;&gt;?&quot;.
			<br/><br/>
			Searching for a canary on the actual browser output is not a good idea. Use the browsers
			"view source" to see the "real" response. Tools like HTTPFox are great for this as well.
			Tools with more features like Burp are even better but have more of a learning curve.
			Burp will remember the source of each page you visit as you spider the site.
			<br/><br/>
			<span class="report-header">Exploitation</span>
			<br/><br/>
			Once the canary(ies) is located, identify what characters need to be injected to
			"end" the current instruction. Identify the characters that are needed to block out
			any instruction that comes after the canary. Put your injection in the middle.
			<br/><br/>
			<span class="report-header">Example</span>
			<br/><br/>
			The
			<a href="./index.php?page=password-generator.php&username=canary">
			Password Generator
			</a>
			page allows JS injection. The JS that builds
			the password is a diversion. The injection point is elsewhere.
			<br/><br/>
			To help see if your Javascript injection forms valid syntax when
			incorporated with the sites Javascript, try validating
			the resulting Javascript at an online validator like
			<a href="http://www.jslint.com/">http://www.jslint.com/</a>
			<br/><br/>
			An injection that works on the Password generator page is
			<br/>
<code>
	canary";}catch(e){}alert(1);try{a="
</code>
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