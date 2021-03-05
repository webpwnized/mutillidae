	<br/><br/>
	<span class="report-header">Overview</span>
	<br/><br/>
	HTTP Parameter Pollution may occur when multiple parameters with the same name are sent to
	a web page. The web server may exhibit odd behavior and process the request despite
	having multiple parameters with the same name. There is nothing in the HTTP protocol that
	forbids passing multiple parameters with the same name. Web application servers will
	sometimes take the first parameter, the second parameter, or concatenate both together.
	This can allow an attacker to poison the input.
<br/><br/>
<a href="#videos" class="label"><img alt="YouTube" src="/images/youtube-play-icon-40-40.png" style="margin-right: 10px;" />Video Tutorials</a>
	<br/><br/>
	<span class="report-header">Discovery Methodology</span>
	<br/><br/>
	Choose any parameter and duplicate that parameter. Give the copy
	a different value to make detection easier. Submit the request and
	observer how the web server deals with the polluted parameter.
	<br/><br/>
	Note carefully whether the server processes the value from the first parameter, the second parameter, or merges both values together.
	<br/><br/>
	<span class="report-header">Exploitation</span>
	<br/><br/>
	Once it is know how the web server will use the parameters, use
	parameter pollution. For example, if the web server concatenates values, submit
	half of the payload in the first copy and half in the second allowing
	the web server to put the values together. This allows WAF and validation
	bypass in some cases.
	<br/><br/>
	<span class="report-header">Example</span>
	<br/><br/>
	Example for page user-poll.php
	<br/><br/>
	Change the value of the submit button. Add "&amp;choice=something" to the current value. This will create
	a second "choice" parameter in the GET. Note which choice parameter is actually processed. Is it the correct
	parameter or the injected parameter?
	<br/><br/>
	Change the value of the hidden parameter which comes before the real "choice" parameter. Add "&amp;choice=something" to the current value. This will create
	a second "choice" parameter in the GET. Note which choice parameter is actually processed. Is it the correct
	parameter or the injected parameter?
	<br/><br/>
	<span id="videos" class="report-header">Videos</span>
	<br/><br/>
	<br/><br/>