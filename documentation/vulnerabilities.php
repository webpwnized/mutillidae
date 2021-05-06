<div class="page-title">Listing of Vulnerabilities</div>

<?php include_once (__ROOT__.'/includes/back-button.inc');?>

<table style="width: 800px;">
	<tr>
		<td>
			<ul>
				<li>Application Exception</li>
				<li>Application log injection</li>
				<li>Application path disclosure</li>
				<li>Authentication Bypass via SQL injection</li>
				<li>Brute force secret admin pages</li>
				<li>Buffer overflow</li>
				<li>Cascading style sheet injection</li>
				<li>CBC bit flipping (latest)</li>
				<li>Click-jacking</li>
				<li>Client-side Security</li>
				<li>Comments with sensitive data</li>
				<li>Content type is not specified</li>
				<li>Cookie scoped to parent domain</li>
				<li>Credit card numbers disclosed</li>
				<li>Cross Site Request Forgery</li>
				<li>Denial of Service</li>
				<li>Directory Browsing</li>
				<li>DOM injection</li>
				<li>Forms caching</li>
				<li>Frame source injection</li>
				<li>HTML injection</li>
				<li>HTTP Parameter Pollution</li>
				<li>Information disclosure via HTML comments</li>
				<li>Insecure Cookies</li>
				<li>JavaScript Injection</li>
				<li>JavaScript validation bypass</li>
				<li>JSON injection</li>
				<li>LDAP injection</li>
			</ul>
		</td>
		<td>
			<ul>
				<li>Loading of any arbitrary file</li>
				<li>Local File Inclusion</li>
				<li>Log injection</li>
				<li>Method Tampering</li>
				<li>O/S Command injection</li>
				<li>Parameter addition</li>
				<li>Password field submitted using GET method</li>
				<li>Path Relative Style Sheet Injection</li>
				<li>PHP server configuration disclosure</li>
				<li>Phishing</li>
				<li>Platform path disclosure</li>
				<li>Privilege Escalation via Cookie Injection</li>
				<li>Reflected Cross Site Scripting via GET, POST, Cookies, and HTTP Headers</li>
				<li>Remote File Inclusion</li>
				<li>robots.txt information disclosure</li>
				<li>Stored Cross Site Scripting</li>
				<li>SSL Stripping</li>
				<li>SQL Injection</li>
				<li>XML Entity Expansion</li>
				<li>XML Injection</li>
				<li>XML External Entity Injection</li>
				<li>XPath Injection</li>
				<li>Unencrypted database credentials</li>
				<li>Unrestricted File Upload</li>
				<li>Username enumeration</li>
				<li>Un-validated Redirects and Forwards</li>
			</ul>
		</td>
	</tr>
</table>

<span class="error-message">Note: Pages marked with a <span class="big-asterik">*</span> are common. This means their vulnerabilities will appear on most pages.</span>

<p class="label">add-to-your-blog.php</p>
<div style="padding-left: 40px;">
	<ul>
		<li>SQL Injection on blog entry</li>
		<li>SQL Injection on logged in user name</li>
		<li>Cross site scripting on blog entry</li>
		<li>Cross site scripting on logged in user name</li>
		<li>Log injection on logged in user name</li>
		<li>Cross site request forgery</li>
		<li>JavaScript validation bypass</li>
		<li>XSS in the form title via logged in username</li>
		<li>HTML injection in blog input field</li>
		<li>Application Exception Output</li>
		<li>Application Log Injection</li>
		<li>Known Vulnerable Output: Name, Comment, "Add blog for" title</li>
	</ul>
</div>

<p class="label">arbitrary-file-inclusion.php</p>
<div style="padding-left: 40px;">
	<ul>
		<li>System file compromise</li>
		<li>Load any page from any site</li>
		<li>Reflected XSS via the value in the "page" URL parameter</li>
		<li>Server-side includes</li>
		<li>HTML injection</li>
		<li>Remote File Inclusion</li>
		<li>Local File Inclusion</li>
		<li>Method Tampering</li>
	</ul>
</div>

<p class="label">authorization-required.php</p>
<div style="padding-left: 40px;">
	<ul>
		<li>No known vulnerabilities. We should add something.</li>
		<li>This page is only used in secure mode. In insecure mode, the site does not authorize user.</li>
	</ul>
</div>

<p class="label">back-button-discussion.php</p>
<div style="padding-left: 40px;">
	<ul>
		<li>Reflected XSS via referer HTTP header</li>
		<li>JS Injection via referer HTTP header</li>
		<li>HTML injection via referer HTTP header</li>
		<li>Unvalidated redirect</li>
	</ul>
</div>

<p class="label">browser-info.php</p>
<div style="padding-left: 40px;">
	<ul>
		<li>Reflected XSS via referer HTTP header</li>
		<li>JS Injection via referer HTTP header</li>
		<li>HTML injection</li>
		<li>Reflected XSS via user-agent string HTTP header</li>
	</ul>
</div>

<p class="label">capture-data.php</p>
<div style="padding-left: 40px;">
	<ul>
		<li>XSS via any GET, POST, or Cookie</li>
		<li>Insert based SQL injection via any GET, POST, or Cookie</li>
		<li>HTML injection</li>
		<li>Application Log Injection</li>
	</ul>
</div>

<p class="label">captured-data.php</p>
<div style="padding-left: 40px;">
	<ul>
		<li>
			Stored XSS via any GET, POST, or Cookie sent to the capture
			data page. (capture-data.php page writes values captured to a table
			read by this page; captured-data.php (with a "d"))
		</li>
		<li>
			HTML injection via any GET, POST, or Cookie sent to the capture
			data page
		</li>
	</ul>
</div>

<p class="label">client-side-comments.php</p>
<div style="padding-left: 40px;">
	<ul>
		<li>
			Comments with sensitive data
		</li>
	</ul>
</div>

<p class="label">client-side-control-challenge.php</p>
<div style="padding-left: 40px;">
	<ul>
		<li>
			Reflected cross-site scripting
		</li>
		<li>
			HTML injection
		</li>
		<li>Method tampering</li>
		<li>Client-side control bypass</li>
	</ul>
</div>

<p class="label">conference-room-lookup.php</p>
<div style="padding-left: 40px;">
	<ul>
		<li>LDAP injection</li>
		<li>Method tampering</li>
	</ul>
</div>

<p class="label">config.inc<span class="big-asterik">*</span></p>
<div style="padding-left: 40px;">
	<ul>
		<li>Contains unencrytped database credentials</li>
		<li>
			NOTE: This page is a canary; a target. It is not used
			in the project. The credentials are only the default. If the
			project was set up differently the credentials may not be correct
		</li>
	</ul>
</div>

<p class="label">credits.php</p>
<div style="padding-left: 40px;">
	<ul>
		<li>Unvalidated Redirects and Forwards</li>
	</ul>
</div>

<p class="label">database-offline.php</p>
<div style="padding-left: 40px;">
	<ul>
		<li>Not that are known. Maybe we should add some.</li>
	</ul>
</div>

<p class="label">directory-browsing.php</p>
<div style="padding-left: 40px;">
	<ul>
		<li>Discusses Directory Browsing</li>
	</ul>
</div>

<p class="label">dns-lookup.php</p>
<div style="padding-left: 40px;">
	<ul>
		<li>Cross site scripting on the host/ip field</li>
		<li>O/S Command injection on the host/ip field</li>
		<li>This page writes to the log. SQLi and XSS on the log are possible</li>
		<li>HTML injection</li>
		<li>
			GET for POST (method tampering) is possible because only reading
			POSTed variables is not enforced.
		</li>
		<li>Application Log Injection</li>
		<li>JavaScript Validation Bypass</li>
	</ul>
</div>

<p class="label">document-viewer.php</p>
<div style="padding-left: 40px;">
	<ul>
		<li>Cross Site Scripting</li>
		<li>HTML injection</li>
		<li>HTTP Parameter Pollution</li>
		<li>Frame source injection</li>
		<li>Method Tampering</li>
		<li>Application Log Injection</li>
	</ul>
</div>

<p class="label">echo.php</p>
<div style="padding-left: 40px;">
	<ul>
		<li>Cross site scripting on the message field</li>
		<li>O/S Command injection on the message field</li>
		<li>This page writes to the log. SQLi and XSS on the log are possible</li>
		<li>HTML injection</li>
		<li>
			GET for POST (method tampering) is possible because only reading
			POSTed variables is not enforced.
		</li>
		<li>Application Log Injection</li>
		<li>JavaScript Validation Bypass</li>
	</ul>
</div>

<p class="label">edit-account-profile.php</p>
<div style="padding-left: 40px;">
	<ul>
		<li>Insecure Direct Object Reference (IDOR) via UID parameter</li>
		<li>
			SQL injection, HTML injection and XSS
			via the username, signature and password field
		</li>
		<li>Method tampering</li>
		<li>Application Log Injection</li>
	</ul>
</div>

<p class="label">footer.php<span class="big-asterik">*</span></p>
<div style="padding-left: 40px;">
	<ul>
		<li>Cross site scripting via the HTTP_USER_AGENT HTTP header.</li>
	</ul>
</div>

<p class="label">framer.html</p>
<div style="padding-left: 40px;">
	<ul>
		<li>Forms caching</li>
		<li>Click-jacking</li>
	</ul>
</div>

<p class="label">framing.php</p>
<div style="padding-left: 40px;">
	<ul>
		<li>Click-jacking</li>
	</ul>
</div>

<p class="label">header.php<span class="big-asterik">*</span></p>
<div style="padding-left: 40px;">
	<ul>
		<li>XSS via logged in user name and signature</li>
		<li>The hints the DB menu item can be enabled by setting the uid value of the cookie to 1</li>
	</ul>
</div>

<p class="label">home.php</p>
<div style="padding-left: 40px;">
	<ul>
		<li>No known vulnerabilities. We should add something.</li>
	</ul>
</div>

<p class="label">html5-storage.php</p>
<div style="padding-left: 40px;">
	<ul>
		<li>
			DOM injection on the add-key error message because the key entered is output
			into the error message without being encoded.
		</li>
	</ul>
</div>

<p class="label">index.php<span class="big-asterik">*</span></p>
<div style="padding-left: 40px;">
	<ul>
		<li>You can XSS the hints-enabled output in the menu because it takes input from the hints-enabled cookie value.</li>
		<li>You can SQL injection the UID cookie value because it is used to do a lookup</li>
		<li>You can change your rank to admin by altering the UID value</li>
		<li>HTTP Response Splitting via the logged in user name because it is used to create an HTTP Header</li>
		<li>This page is responsible for cache-control but fails to do so</li>
		<li>This page allows the X-Powered-By HTTP header</li>
		<li>HTML comments</li>
		<li>There are secret pages that if browsed to will redirect user to the phpinfo.php page.
			This can be done via brute forcing
		</li>
		<li>The show-hints cookie can be changed by user to enable hints even though they are not suppose to show in secure mode</li>
	</ul>
</div>

<p class="label">installation.php</p>
<div style="padding-left: 40px;">
	<ul>
		<li>No known vulnerabilities. We should add something.</li>
	</ul>
</div>

<p class="label">log-visit.php</p>
<div style="padding-left: 40px;">
	<ul>
		<li>SQL injection and XSS via referer HTTP header</li>
		<li>SQL injection and XSS via user-agent string</li>
	</ul>
</div>

<p class="label">login.php</p>
<div style="padding-left: 40px;">
	<ul>
		<li>Authentication bypass SQL injection via the username field and password field</li>
		<li>SQL injection via the username field and password field</li>
		<li>XSS via username field</li>
		<li>JavaScript validation bypass</li>
		<li>HTML injection via username field</li>
		<li>Username enumeration</li>
		<li>Application Log Injection</li>
	</ul>
</div>

<p class="label">page-not-found.php</p>
<div style="padding-left: 40px;">
	<ul>
		<li>No known vulnerabilities. We should add something.</li>
		<li>This page is only used in secure mode. In insecure mode, the site does not validate the "page" parameter.</li>
	</ul>
</div>

<p class="label">password-generator.php</p>
<div style="padding-left: 40px;">
	<ul>
		<li>JavaScript injection</li>
	</ul>
</div>

<p class="label">pen-test-tool-lookup.php</p>
<div style="padding-left: 40px;">
	<ul>
		<li>JSON injection</li>
	</ul>
</div>

<p class="label">pen-test-tool-lookup-ajax.php</p>
<div style="padding-left: 40px;">
	<ul>
		<li>JSON injection</li>
	</ul>
</div>

<p class="label">php-errors.php</p>
<div style="padding-left: 40px;">
	<ul>
		<li>No known vulnerabilities. We should add something.</li>
	</ul>
</div>

<p class="label">phpinfo.php</p>
<div style="padding-left: 40px;">
	<ul>
		<li>This page gives away the PHP server configuration</li>
		<li>Application path disclosure</li>
		<li>Platform path disclosure</li>
		<li>Information disclosure</li>
	</ul>
</div>

<p class="label">privilege-escalation.php</p>
<div style="padding-left: 40px;">
	<ul>
		<li>None</li>
	</ul>
</div>

<p class="label">process-commands.php</p>
<div style="padding-left: 40px;">
	<ul>
		<li>Creates cookies but does not make them HTML only</li>
	</ul>
</div>

<p class="label">process-login-attempt.php</p>
<div style="padding-left: 40px;">
	<ul>
		<li>Same as login.php. This is the action page.</li>
	</ul>
</div>

<p class="label">redirectandlog.php</p>
<div style="padding-left: 40px;">
	<ul>
		<li>Same as credits.php. This is the action page.</li>
	</ul>
</div>

<p class="label">register.php</p>
<div style="padding-left: 40px;">
	<ul>
		<li>
			SQL injection, HTML injection and XSS
			via the username, signature and password field
		</li>
		<li>Method tampering</li>
		<li>Application Log Injection</li>
	</ul>
</div>

<p class="label">repeater.php</p>
<div style="padding-left: 40px;">
	<ul>
		<li>HTML injection and XSS</li>
		<li>Method tampering</li>
		<li>Parameter addition</li>
		<li>Buffer overflow</li>
	</ul>
</div>

<p class="label">rene-magritte.php</p>
<div style="padding-left: 40px;">
	<ul>
		<li>Click-jacking</li>
	</ul>
</div>

<p class="label">robots.txt</p>
<div style="padding-left: 40px;">
	<ul>
		<li>Contains directories that are supposed to be private.</li>
		<li>The directories are browsable and contain sensitive files.</li>
	</ul>
</div>

<p class="label">robots.txt.php</p>
<div style="padding-left: 40px;">
	<ul>
		<li>Discusses robots.txt</li>
	</ul>
</div>

<p class="label">secret-administrative-pages.php</p>
<div style="padding-left: 40px;">
	<ul>
		<li>This page gives hints about how to discover the server configuration.</li>
		<li>
			There are secret pages that if browsed to will redirect user to the phpinfo.php page.
			This can be done via brute forcing
		</li>
	</ul>
</div>

<p class="label">set-background-color.php</p>
<div style="padding-left: 40px;">
	<ul>
		<li>Cascading style sheet injection and XSS via the color field.</li>
	</ul>
</div>

<p class="label">set-up-database.php</p>
<div style="padding-left: 40px;">
	<ul>
		<li>No known vulnerabilities. We should add something.</li>
	</ul>
</div>

<p class="label">show-log.php</p>
<div style="padding-left: 40px;">
	<ul>
		<li>Denial of Service if you fill up the log</li>
		<li>XSS via the hostname, client IP, browser HTTP header, Referer HTTP header, and date fields.</li>
		<li>HTML Injection</li>
	</ul>
</div>

<p class="label">site-footer-xss-discusson.php</p>
<div style="padding-left: 40px;">
	<ul>
		<li>XSS and HTMLi via the user agent string HTTP header</li>
	</ul>
</div>

<p class="label">source-viewer.php</p>
<div style="padding-left: 40px;">
	<ul>
		<li>Loading of any arbitrary file including operating system files.</li>
		<li>HTML Injection</li>
		<li>Cross Site Scripting</li>
		<li>Application log injection</li>
	</ul>
</div>

<p class="label">sqlmap-targets.php</p>
<div style="padding-left: 40px;">
	<ul>
		<li>None</li>
	</ul>
</div>

<p class="label">ssl-misconfiguration.php</p>
<div style="padding-left: 40px;">
	<ul>
		<li>
			Discusses TLS downgrade attack due to a vulnerability in the site globally.
			No known vulnerabilities on the page itself.
		</li>
	</ul>
</div>

<p class="label">styling.php</p>
<div style="padding-left: 40px;">
	<ul>
		<li>Path Relative Style Sheet Injection</li>
		<li>HTML Injection</li>
		<li>Cross Site Scripting</li>
	</ul>
</div>

<p class="label">text-file-viewer.php</p>
<div style="padding-left: 40px;">
	<ul>
		<li>Loading of any arbitrary web page on the Interet or locally including the sites password files.</li>
		<li>Phishing</li>
		<li>Method Tampering</li>
		<li>Cross site scripting</li>
		<li>Application log injection</li>
	</ul>
</div>

<p class="label">upload-file.php<span class="big-asterik">*</span></p>
<div style="padding-left: 40px;">
	<ul>
		<li>Unrestricted File Upload</li>
		<li>Cross Site Scripting</li>
		<li>HTML injection</li>
	</ul>
</div>

<p class="label">usage-instructions.php</p>
<div style="padding-left: 40px;">
	<ul>
		<li>No known vulnerabilities. We should add some.</li>
	</ul>
</div>

<p class="label">user-agent-impersonation.php</p>
<div style="padding-left: 40px;">
	<ul>
		<li>Javascript String Injection</li>
		<li>Cross site scripting</li>
		<li>User agent impersonation</li>
	</ul>
</div>

<p class="label">user-info.php</p>
<div style="padding-left: 40px;">
	<ul>
		<li>SQL injection to dump all usernames and passwords via the username field or the password field</li>
		<li>XSS via any of the displayed fields. Inject the XSS on the register.php page.</li>
		<li>XSS via the username field</li>
		<li>JavaScript validation bypass</li>
	</ul>
</div>

<p class="label">user-info-xpath.php</p>
<div style="padding-left: 40px;">
	<ul>
		<li>XPath injection to dump all usernames and passwords via the username field or the password field</li>
		<li>XSS via any of the displayed fields. Inject the XSS on the register.php page.</li>
		<li>XSS via the username field</li>
		<li>JavaScript validation bypass</li>
	</ul>
</div>

<p class="label">user-poll.php</p>
<div style="padding-left: 40px;">
	<ul>
		<li>Parameter pollution</li>
		<li>Method Tampering</li>
		<li>XSS via the choice parameter</li>
		<li>Cross site request forgery to force user choice</li>
		<li>HTML injection</li>
	</ul>
</div>

<p class="label">view-someones-blog.php</p>
<div style="padding-left: 40px;">
	<ul>
		<li>
			Persistent XSS via any of the displayed fields.
			They are input on the add to your blog page.
		</li>
	</ul>
</div>

<p class="label">view-user-privilege-level.php</p>
<div style="padding-left: 40px;">
	<ul>
		<li>CBC bit flipping attack</li>
	</ul>
</div>

<p class="label">webservices/rest/ws-user-account.php</p>
<div style="padding-left: 40px;">
	<ul>
		<li>REST Web Service: SQL Injection</li>
		<li>REST Web Service: Username emuneration</li>
	</ul>
</div>

<p class="label">webservices/soap/ws-lookup-dns-record.php</p>
<div style="padding-left: 40px;">
	<ul>
		<li>SOAP Web Service: Command Injection</li>
		<li>SOAP Web Service: Username emuneration</li>
	</ul>
</div>

<p class="label">webservices/soap/ws-user-account.php</p>
<div style="padding-left: 40px;">
	<ul>
		<li>SOAP Web Service: SQL Injection</li>
		<li>SOAP Web Service: Username emuneration</li>
	</ul>
</div>

<p class="label">xml-validator.php</p>
<div style="padding-left: 40px;">
	<ul>
		<li>XML Entity Injection Attack</li>
		<li>XML Entity Expansion</li>
		<li>XML Injection</li>
		<li>Reflected Cross site scripting via XML Injection</li>
	</ul>
</div>
