<br/><br/>
<span class="report-header">Overview</span>
<br/><br/>
Content-Security-Policy (CSP) allows developers to tell the browser what client-side code can run in the browser. CSP may stop attacks that hackers launch against users of vulnerable web sites. These client-side code injection and click-jacking attacks take advantage of users using a vulnerable web application.
<br/><br/>
CSP protects sites by whitelisting which client-side source code files are allowed to execute. For this reason, except for HTML, all non-HTML client-side source code must be written in files rather than mixed the HTML. Otherwise, CSP has to effectively be disabled to allow the "unsafe inline" code to run or more complex "code hashes" or other identifiers have to be used to identify "friendly" code.
<br/><br/>
CSP does not patch vulnerabilities but may block certain attacks which may give developers time to issue patches. CSP also includes a reporting feature that can notify when CSP blocks. These reports can alert a suitably savvy developer that is auditing the reports that the site has a vulnerability.
<br/><br/>
CSP requires the following for maximum effectiveness.
<br/>
<ol>
<li>All non-HTML client-side source code must be written in files rather than mixed the HTML. Otherwise, CSP has to effectively be disabled to allow the "unsafe inline" code to run. Otherwise, be prepared to use code identifiers such as hashes to identify safe source code</li>
<li>The CSP policy has to be declared in any HTTP response that contains HTML</li>
<li>The CSP reporting feature must be enabled</li>
<li>The CSP reporting feature requires the application provide a server to send reports</li>
<li>The CSP reports must be audited to understand if the site contains a vulnerability allowing code injection or if the CSP configuration is incomplete or incorrect</li>
</ol>
<span class="report-header">How to use CSP</span>
<br/>
<ol>
<li>Except for HTML, move any "inline" client-side source code into an appropriate source code file(s). Otherwise mark inline source code with a hash or a nonce so inline code that should be allowed to run can be identified by the browser</li>
<li>Set up an endpoint for browsers to report CSP violations and errors</li>
<li>Implement the report-uri directive to inform the browser where to report CSP violations and errors</li>
<li>During regression testing, use Content-Security-Policy-Report-Only feature to have the browser report violations</li>
<li>Implement the appropriate Content-Security-Policy directives. The following are the most common.</li>
</ol>
<span class="report-header">All sites</span>
<br/><br/>
Generally, all sites can set default-src to 'self' indicating all client-side source code files are loaded from the same domain as the site itself. This setting protect any source code that does not have its own CSP directive. If any sources are loaded from other domains, those will be declared in the CSP policy for that type of source code file and in doing so override the default-src.
<br/>
<code>
Content-Security-Policy: default-src 'self';
</code>
<br/>
In addition to default-src, all sites should declare frame-ancestors policy. The value of frame-ancestors will depend on whether the site uses frames itself.
<br/><br/>
<span class="report-header">Sites that do not use frames</span>
<br/><br/>
If the site does not contain content that requires framing, use Content-Security-Policy: frame-ancestors 'none';
<br/><br/>
<span class="report-header">Sites that use frames</span>
<br/><br/>
If the site contains content that requires framing by the site itself, use Content-Security-Policy: frame-ancestors 'self'.
<br/><br/>
<span class="report-header">Sites that allow business partners to frame them</span>
<br/><br/>
If the site contains content that requires framing by another site, use Content Security Policy frame-ancestor feature. For example, Content-Security-Policy: frame-ancestors https://www.example.org;
<br/><br/>
Note that multiple CSP polices can be declared in the same Content-Security-Policy HTTP header.
<br/>
<code>
Content-Security-Policy: default-src 'self'; frame-ancestors 'none'
</code>
<br/>
<span class="report-header">Sites using JavaScript (JS)</span>
<br/><br/>
Sites that use JavaScript should declare from which domains the JavaScript source code files are allowed to load. Sites that load JavaScript from the same domain can set the 'self' directive. If JavaScript is loaded from other domains, include these partner domains in the directive.
<br/>
<code>
Content-Security-Policy: script-src 'self' https://&lt;partner domain&gt;
</code>
<br/>
<span class="report-header">Sites using Cascading Stylesheets (CSS)</span>
<br/><br/>
Sites that use Cascading Stylesheets (CSS) should declare from which domains the Cascading Stylesheets (CSS) source code files are allowed to load. Sites that load Cascading Stylesheets (CSS) from the same domain can set the 'self' directive. If Cascading Stylesheets (CSS) are loaded from other domains, include these partner domains in the directive.
<br/><br/>
Content-Security-Policy: style-src 'self' https://&lt;partner domain&gt;
<br/><br/>
<span class="report-header">Sites using images</span>
<br/><br/>
Sites that use images should declare from which domains the images source code files are allowed to load. Sites that load images from the same domain can set the 'self' directive. If images are loaded from other domains, include these partner domains in the directive.
<br/><br/>
Content-Security-Policy: img-src 'self' https://&lt;partner domain&gt;
<br/><br/>
<span class="report-header">Sites that call application programming interfaces (API)</span>
<br/><br/>
Sites that call out to a web API should declare the domain of the API. This affects sites using XMLHttpRequest (AJAX), WebSocket, fetch(), <a ping> or EventSource.
<br/>
<code>
Content-Security-Policy: connect-src https://&lt;domain of the API&gt;
</code>
<br/>
<span class="report-header">Sites using fonts</span>
<br/><br/>
Sites that use fonts loaded via @font-face should declare from which domains the font source code files are allowed to load. Sites that load fonts from the same domain can set the 'self' directive. If fonts are loaded from other domains, include these partner domains in the directive.
<br/>
<code>
Content-Security-Policy: font-src 'self' https://&lt;partner domain&gt;
</code>
<br/>
<span class="report-header">Other directives</span>
<br/><br/>
There are several other directives that may be applicable for some sites. Implement the applicable CSP directives
<br/><br/>
<span id="videos" class="report-header">Videos</span>
<br/><br/>
<?php echo $YouTubeVideoHandler->getYouTubeVideo($YouTubeVideoHandler->WhatisContentSecurityPolicyPart1);?>
<?php echo $YouTubeVideoHandler->getYouTubeVideo($YouTubeVideoHandler->WhatisContentSecurityPolicyPart2);?>
<?php echo $YouTubeVideoHandler->getYouTubeVideo($YouTubeVideoHandler->WhatisContentSecurityPolicyPart3);?>
<?php echo $YouTubeVideoHandler->getYouTubeVideo($YouTubeVideoHandler->WhatisContentSecurityPolicyPart4);?>
<?php echo $YouTubeVideoHandler->getYouTubeVideo($YouTubeVideoHandler->WhatisContentSecurityPolicyPart5);?>
<?php echo $YouTubeVideoHandler->getYouTubeVideo($YouTubeVideoHandler->ContentSecurityPolicyScriptSourcescriptsrc);?>
<?php echo $YouTubeVideoHandler->getYouTubeVideo($YouTubeVideoHandler->ContentSecurityPolicyFrameAncestors);?>
<?php echo $YouTubeVideoHandler->getYouTubeVideo($YouTubeVideoHandler->HowtoSetHTTPHeadersUsingApacheServer);?>
<?php echo $YouTubeVideoHandler->getYouTubeVideo($YouTubeVideoHandler->CheckHTTPHeaderswithcURL);?>
<?php echo $YouTubeVideoHandler->getYouTubeVideo($YouTubeVideoHandler->HowtoCheckHTTPHeadersCommandLine);?>
<?php echo $YouTubeVideoHandler->getYouTubeVideo($YouTubeVideoHandler->HowtoCheckHTTPHeadersfromBrowser);?>
<?php echo $YouTubeVideoHandler->getYouTubeVideo($YouTubeVideoHandler->HowtoEnableApacheModHeader);?>
<?php echo $YouTubeVideoHandler->getYouTubeVideo($YouTubeVideoHandler->CrossSiteScriptingPart1WhatisReflectedXSS);?>
<?php echo $YouTubeVideoHandler->getYouTubeVideo($YouTubeVideoHandler->CrossSiteScriptingPart2WhatisDOMbasedXSS);?>
<?php echo $YouTubeVideoHandler->getYouTubeVideo($YouTubeVideoHandler->CrossSiteScriptingPart3WhatisPersistentXSS);?>
<?php echo $YouTubeVideoHandler->getYouTubeVideo($YouTubeVideoHandler->OutputEncodingPart1HowItStopsCrosssiteScriptingXSS);?>
<?php echo $YouTubeVideoHandler->getYouTubeVideo($YouTubeVideoHandler->OutputEncodingPart2HowtoTestifitWorks);?>
<?php echo $YouTubeVideoHandler->getYouTubeVideo($YouTubeVideoHandler->HowtoEnableApacheModHeader);?>
<br/><br/>
