			<br/><br/>
			<span class="report-header">Overview</span>
			<br/><br/>
			Javascript Validation Bypass is usually trivial since the user
			may disable Javascript entirely or fill in values which legitimately
			pass validation then alter those values in a client proxy such as
			Burp Suite after the validation has occured.
<br/><br/>
<a href="#videos" class="label"><img alt="YouTube" src="/images/youtube-play-icon-40-40.png" style="margin-right: 10px;" />Video Tutorials</a>
<br/><br/>
<span class="report-header">Discovery Methodology</span>
<br/><br/>
Set up a client proxy to capture all requests. Observe if validation
occurs prior to the request being captured. If so, the validation is
occuring in the browser.
<br/><br/>
<span class="report-header">Exploitation</span>
<br/><br/>
Disable Javascript entirely and observe if the page still operates.
Alternatively fill in values which legitimately
pass client-side validation then alter those values
in a client proxy such as
Burp Suite after the validation has occured.
<br/><br/>
<span id="videos" class="report-header">Videos</span>
<br/><br/>
<br/><br/>