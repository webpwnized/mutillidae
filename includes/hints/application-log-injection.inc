<br/><br/>
<span class="report-header">Overview</span>
<br/><br/>
Application Log Injection may result when application logs store user
input and possess client-vulnerabilities such as XSS. For example, if
application logs are stored in HTML format and viewed in a browser, then
XSS vulnerabilities in the log viewer could allow execution of the XSS.
<br/><br/>
<a href="#videos" class="label"><img alt="YouTube" src="/images/youtube-play-icon-40-40.png" style="margin-right: 10px;" />Video Tutorials</a>
<br/><br/>
<span class="report-header">Discovery Methodology</span>
<br/><br/>
To discover vulnerabilities in the log viewer, it may be best to download and
install a copy of the target application locally, then use standard techniques
to test the application.
<br/><br/>
<span class="report-header">Exploitation</span>
<br/><br/>
Cross site scripting tends to be the easiest and most prevalent vulnerability
in HTML based application log viewers. Send hooks and other XSS into the logs.
Wait for an administrator to view the logs.
<br/><br/>
<span class="report-header">Example</span>
<br/><br/>
The user name entered on the login page is logged to the application log. Enter
a cross-site script in the user name field. Visit the
<a href="index.php?page=show-log.php">View Log</a> page. Check that the XSS works.
<br/><br/>
<br/><br/>
<span id="videos" class="report-header">Videos</span>
<br/><br/>
<br/><br/>