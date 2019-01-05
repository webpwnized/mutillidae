<br/><br/>
<span class="report-header">Overview</span>
<br/><br/>
Parameter addition may occur if the user submits a new parameter the site
was not expecting to see. The site will generally not validate parameters 
that are unexpected. The classic case is sites outputting all parameters
regardless of whether the site uses the parameter. The developer
may dump all parameters and their values regardless of whether the parameters
was expected. Many times this dump will be into an HTML comment so the user
will not see the diagnostic output in the browser.
<br/><br/>
<a href="#videos" class="label"><img alt="YouTube" src="/images/youtube-play-icon-40-40.png" style="margin-right: 10px;" />Video Tutorials</a>
<br/><br/>
<span class="report-header">Discovery Methodology</span>
<br/><br/>
Inject a new parameter that is not normally submitted by the application
with a searchable string
such as the word "CANARY" along with characters generally useful in 
writing HTML, JavaScript or other code. Search the response carefully
noting any location where the test string appears unencoded. These 
locations may allow cross-site scripting. 
<br/><br/>
<span class="report-header">Exploitation</span>
<br/><br/>
Determine the prefix and suffix needed to make the injected code "fit" syntatically
then add a payload between. Inject the exploit.
<br/><br/>
<span id="videos" class="report-header">Videos</span>
<br/><br/>
<?php echo $YouTubeVideoHandler->getYouTubeVideo($YouTubeVideoHandler->IntroductiontoParameterAddition);?>
<br/><br/>