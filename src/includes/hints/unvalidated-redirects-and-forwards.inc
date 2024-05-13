			<br/><br/>
			<span class="report-header">Overview</span>
			<br/><br/>
		  	Sites which redirect users to a URL which is passed as a parameter
		  	are vulnerable to arbitrary redirection. An attacker can create
		  	a URL then embed the URL as a parameter which when passed to the
		  	vulnerable page will cause the user to be redirected to the URL
		  	Unvalidated redirects can make the job of Phishers easier
		  	since the URL can be made to look like part of a trusted site.
<br/><br/>
<a href="#videos" class="label"><img alt="YouTube" src="/images/youtube-play-icon-40-40.png" style="margin-right: 10px;" />Video Tutorials</a>
			<br/><br/>
			<span class="report-header">Discovery Methodology</span>
			<br/><br/>
			Spider the site and note any status codes of 3xx and/or "Location"
			HTTP headers. For each found, inject all parameters on the page
			leading up to the redirection including cookies. Check if any of
			the injected values makes up any part of value of the "Location"
			header (from Location header redirection)
			or if the URL on which the browser lands contains any
			of the injected values (from page rewriting).
			<br/><br/>
			Additionally, grep pages for JavaScript and HTML redirects. For example, look
			for calls to JavaScript document.location method or HTML meta tag redirection.
			If these are found, determine if user-input affects the location to which the
			user is redirected.
			<br/><br/>
			<span class="report-header">Exploitation</span>
			<br/><br/>
			<span class="label">Note: The "Credits" page in this site is vulnerable.</span>
			<br/><br/>
			Once an injectable location is found, inject a URL and observe if the injection
			results in being redirected to the URL. If the injection does not result
			in redirection, check the resulting syntax of the URL. It may be possible
			to correct some syntax errors.
			<br/><br/>
			<span class="report-header">Example</span>
			<br/><br/>
			On the "credits" page in this site, one of the links is
			<br/>
<code>
	http://localhost/mutillidae/index.php?page=redirectandlog.php&amp;forwardurl=http://www.owasp.org.
</code>
			<br/>
			If an internet connection is available, try using
			<br/>
<code>
	http://www.google.com
</code>
			<br/>
			as the URL passed to the "forwardurl" parameter. The following URL can be entered:
			<br/>
<code>
	http://localhost/mutillidae/index.php?page=redirectandlog.php&amp;forwardurl=http://www.google.com.
</code>
			<br/>
			If no Internet connection is available, try using
			<br/>
<code>
	http://localhost/mutillidae/capture-data.php
</code>
			<br/>
			as the URL passed to the value to the "forwardurl" parameter.
			<br/>
<code>
	http://localhost/mutillidae/index.php?page=redirectandlog.php&amp;forwardurl=http://localhost/mutillidae/capture-data.php
</code>
<br/><br/>
<span id="videos" class="report-header">Videos</span>
<br/><br/>
<?php echo $YouTubeVideoHandler->getYouTubeVideo($YouTubeVideoHandler->WhatisInsecureDirectObjectReferenceIDOR);?>
<?php echo $YouTubeVideoHandler->getYouTubeVideo($YouTubeVideoHandler->WhatisanOpenRedirect);?>
<?php echo $YouTubeVideoHandler->getYouTubeVideo($YouTubeVideoHandler->IntroductiontoUnvalidatedRedirectsandForwards);?>
<br/><br/>
