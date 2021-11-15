			<br/><br/>
			<span class="report-header">Overview</span>
			<br/><br/>
			Authentication Bypass is generally custom for each web application
			which possesses the vulnerability. However, there are some common
			abuse case patterns.
<br/><br/>
<a href="#videos" class="label"><img alt="YouTube" src="/images/youtube-play-icon-40-40.png" style="margin-right: 10px;" />Video Tutorials</a>
			<br/><br/>
			<span class="report-header">Discovery Methodology</span>
			<br/><br/>
			Attempt to discover if SQL injection is present as this vulnerability
			often allows authentication bypass.
			<br/><br/>
			If possible aquire a standard user account and an administrator account
			from the client. Authenticate with both carefully noting any differences
			in the session tokens, cookies, and/or hidden fields.
			<br/><br/>
			If the testing is black box, register for multiple user accounts carefully
			noting differences between the accounts after authentication.
			<br/><br/>
			<span class="report-header">Exploitation (SQL Injection)</span>
			<br/><br/>
			<a href="index.php?page=login.php">Login</a>
			<br/><br/>
			Use SQL Injection to bypass authentication on the login page. See the SQL
			Injection hints or the SQL injection tutorial for help on SQL injection.
			<br/><br/>
			This can be done using the name field to authenticate as the first user
			found in the user table.
			<br/>
<code>
Username: ' or 'a' = 'a' --
Password: whatever
</code>
			<br/>
			To target a particular user, identify the user. (Note: This site is vulnerable
			to username disclosure.) Complete the "username" portion of the query with
			the target username then bypass the password portion of the query by
			creating a tautalogy or simply commenting out the password portion.
			<br/><br/>
			Bypass password with a tautalogy
			<br/>
<code>
Username: jeremy
Password: ' or ('a' = 'a' and username='jeremy') or '
</code>
			<br/>
			Comment out the password portion of the query
			<br/>
<code>
Username: jeremy' --
Password: whatever
</code>
<br/><br/>
<span class="report-header">Exploitation (Authentication Token Manipulation)</span>
<br/><br/>
Alter the values of any authentication and/or authorization
tokens found such as those in cookies. This will work on any page
post-authentication. Register for an account to explore how the
site uses cookies.
<br/><br/>
Gaining Access: Using insecure client-side authentication tokens
<br/><br/>
Page: Any page<br/>
Tool: Cookies Manager+ version 1.5.1 (verified with Firefox 4.0.1)
<br/><br/>
Note what cookies the site has by default<br/>
Create a test account<br/>
Login using test account<br/>
Check what cookies the site has after authentication<br/>
Differential Analysis: Change the value of the original auth cookie<br/>
Work backwards until admin account is found
<br/><br/>
<span class="report-header">Exploitation (CBC bit flipping attack)</span>
<br/><br/>
Try bit flipping, oracle padding, and cbc bit flipping attacks on
authentication and/or authorization tokens.
<br/><br/>
<a href="index.php?page=view-user-privilege-level.php&iv=6bc24fc1ab650b25b4114e93a98f1eba">View User Privileges</a>
<br/><br/>
<span class="report-header">Exploitation (Authentication Token Hijack)</span>
<br/><br/>
Use a cross site script attack to trick a lab partner into visiting the
data capture page while authenticated. Use Cookie Manager Plus to
create or edit cookies in order to become that user.
<br/><br/>
<a href="index.php?page=capture-data.php">Data Capture</a><br/>
<a href="index.php?page=captured-data.php">View Captured Data</a>
<br/><br/>
<span class="report-header">Exploitation (Browser Fingerprint Spoofing)</span>
<br/><br/>
On captive portal systems, spoofing an unsupported browser may bypass some
controls
<br/><br/>
<a href="index.php?page=user-agent-impersonation.php">User-Agent Impersonation</a>
<br/><br/>
<span id="videos" class="report-header">Videos</span>
<br/><br/>
<?php echo $YouTubeVideoHandler->getYouTubeVideo($YouTubeVideoHandler->AnalyzeSessionTokenRandomnessusingBurpSuiteSequencer);?>
<?php echo $YouTubeVideoHandler->getYouTubeVideo($YouTubeVideoHandler->WhatisInsecureDirectObjectReferenceIDOR);?>
<br/><br/>