<?php
	$lUserIDString = "";
	if($_SESSION['loggedin'] == "True"){
	    $lUserIDString = "&uid=" . $lUserID;
	} //end if
?>

<div id="smoothmenu1" class="ddsmoothmenu">
	<ul>
		<li>
			<a href="">OWASP 2017</a>
			<ul>
				<li>
					<a href="">A1 - Injection (SQL)</a>
					<ul>
						<li>
							<a href="">SQLi - Extract Data</a>
							<ul>
								<li><a href="index.php?page=user-info.php">User Info (SQL)</a></li>
							</ul>
						</li>
						<li>
							<a href="">SQLi - Bypass Authentication</a>
							<ul>
								<li><a href="index.php?page=login.php">Login</a></li>
							</ul>
						</li>
						<li>
							<a href="">SQLi - Insert Injection</a>
							<ul>
								<li><a href="?page=add-to-your-blog.php">Add to your blog</a></li>
								<li><a href="index.php?page=register.php">Register</a></li>
								<li><a href="index.php?page=captured-data.php">View Captured Data</a></li>
							</ul>
						</li>
						<li>
							<a href="">Blind SQL via Timing</a>
							<ul>
								<li><a href="index.php?page=login.php">Login</a></li>
								<li><a href="index.php?page=user-info.php">User Info (SQL)</a></li>
							</ul>
						</li>
						<li>
							<a href="">SQLMAP Practice</a>
							<ul>
								<li><a href="index.php?page=sqlmap-targets.php">SQLMAP Practice Targets</a></li>
								<li><a href="index.php?page=login.php">Login</a></li>
								<li><a href="index.php?page=view-someones-blog.php">View Someones Blog</a></li>
								<li><a href="index.php?page=user-info.php">User Info (SQL)</a></li>
							</ul>
						</li>
						<li>
							<a href="">Via JavaScript Object Notation (JSON)</a>
							<ul>
								<li><a href="index.php?page=pen-test-tool-lookup.php">Pen Test Tool Lookup</a></li>
								<li><a href="index.php?page=pen-test-tool-lookup-ajax.php">Pen Test Tool Lookup (AJAX)</a></li>
							</ul>
						</li>
						<li>
							<a href="">Via SOAP Web Service</a>
							<ul>
								<li><a href="./webservices/soap/ws-user-account.php">Lookup User</a></li>
							</ul>
						</li>
						<li>
							<a href="">Via REST Web Service</a>
							<ul>
								<li><a href="./webservices/rest/ws-user-account.php">User Account Management</a></li>
							</ul>
						</li>
					</ul>
				</li>
				<li>
					<a href="">A1 - Injection (Other)</a>
					<ul>
						<li>
							<a href="">Application Log Injection</a>
							<ul>
								<li><a href="index.php?page=add-to-your-blog.php">Add to your blog</a></li>
								<li><a href="index.php?page=dns-lookup.php">DNS Lookup</a></li>
								<li><a href="index.php?page=echo.php">Echo Message</a></li>
								<li><a href="index.php?page=cors.php">Echo Message (CORS)</a></li>
								<li><a href="index.php?page=content-security-policy.php">Echo Message (CSP)</a></li>
								<li><a href="index.php?page=document-viewer.php&PathToDocument=documentation/how-to-access-Mutillidae-over-Virtual-Box-network.php">Document Viewer</a></li>
								<li><a href="index.php?page=capture-data.php">Capture Data Page</a></li>
								<li><a href="index.php?page=login.php">Login</a></li>
								<li><a href="index.php?page=register.php">Register User</a></li>
								<li><a href="index.php?page=source-viewer.php">Source Viewer</a></li>
								<li><a href="index.php?page=text-file-viewer.php">Text File Viewer</a></li>
							</ul>
						</li>
						<li>
							<a href="">Buffer Overflow</a>
							<ul>
								<li><a href="index.php?page=repeater.php">Repeater</a></li>
							</ul>
						</li>
						<li>
							<a href="">Cascading Style Injection</a>
							<ul>
								<li><a href="index.php?page=set-background-color.php">Set Background Color</a></li>
							</ul>
						</li>
						<li>
							<a href="index.php?page=view-user-privilege-level.php&iv=6bc24fc1ab650b25b4114e93a98f1eba">CBC-bit Flipping</a>
						</li>
						<li>
							<a href="">Command Injection</a>
							<ul>
								<li><a href="index.php?page=dns-lookup.php">DNS Lookup</a></li>
								<li><a href="./webservices/soap/ws-lookup-dns-record.php">DNS Lookup (SOAP Web Service)</a></li>
								<li><a href="index.php?page=echo.php">Echo Message</a></li>
								<li><a href="index.php?page=cors.php">Echo Message (CORS)</a></li>
								<li><a href="index.php?page=content-security-policy.php">Echo Message (CSP)</a></li>
							</ul>
						</li>
						<li>
							<a href="">Frame Source Injection</a>
							<ul>
								<li><a href="index.php?page=document-viewer.php&PathToDocument=documentation/how-to-access-Mutillidae-over-Virtual-Box-network.php">Document Viewer</a></li>
								<li><a href="index.php?page=styling-frame.php&page-to-frame=styling.php%3Fpage-title%3DStyling+with+Mutillidae">Styling with Mutilidae</a></li>
							</ul>
						</li>
						<li>
							<a href="">HTML Injection (HTMLi)</a>
							<ul>
								<li><a href="index.php?page=add-to-your-blog.php">Add to your blog</a></li>
								<li><a href="index.php?page=browser-info.php">Browser Info</a></li>
								<li><a href="index.php?page=dns-lookup.php">DNS Lookup</a></li>
								<li><a href="index.php?page=echo.php">Echo Message</a></li>
								<li><a href="index.php?page=cors.php">Echo Message (CORS)</a></li>
								<li><a href="index.php?page=content-security-policy.php">Echo Message (CSP)</a></li>
								<li><a href="index.php?page=pen-test-tool-lookup.php">Pen Test Tool Lookup</a></li>
								<li><a href="index.php?page=text-file-viewer.php">Text File Viewer</a></li>
								<li><a href="index.php?page=user-info.php">User Info (SQL)</a></li>
								<li><a href="index.php?page=user-info-xpath.php">User Info (XPath)</a></li>
								<li><a href="index.php?page=set-background-color.php">Set Background Color</a></li>
								<li><a href="index.php?page=html5-storage.php">HTML5 Web Storage</a></li>
								<li><a href="index.php?page=capture-data.php">Capture Data Page</a></li>
								<li><a href="index.php?page=captured-data.php">View Captured Data</a></li>
								<li><a href="index.php?page=document-viewer.php&PathToDocument=documentation/how-to-access-Mutillidae-over-Virtual-Box-network.php">Document Viewer</a></li>
								<li><a href="index.php?page=arbitrary-file-inclusion.php">Arbitrary File Inclusion</a></li>
								<li><a href="index.php?page=user-poll.php">Poll Question</a></li>
								<li><a href="index.php?page=register.php">Register User</a></li>
								<li><a href="index.php?page=login.php">Login</a></li>
								<li><a href="index.php?page=back-button-discussion.php">Those &quot;Back&quot; Buttons</a></li>
								<li><a href="index.php?page=styling-frame.php&page-to-frame=styling.php%3Fpage-title%3DStyling+with+Mutillidae">Styling with Mutilidae</a></li>
								<li><a href="index.php?page=password-generator.php&username=<?php echo $logged_in_user ?>">Password Generator</a></li>
							</ul>
						</li>
						<li>
							<a href="">HTMLi via HTTP Headers</a>
							<ul>
								<li><a href="index.php?page=back-button-discussion.php">Those &quot;Back&quot; Buttons</a></li>
								<li><a href="index.php?page=browser-info.php">Browser Info</a></li>
								<li><a href="index.php?page=site-footer-xss-discussion.php">Site Footer</a><li>
								<li><a href="">HTTP Response Splitting (Hint: Difficult)</a></li>
							</ul>
						</li>
						<li>
							<a href="">HTMLi Via DOM Injection</a>
							<ul>
								<li><a href="index.php?page=html5-storage.php">HTML5 Web Storage</a></li>
								<li><a href="index.php?page=password-generator.php&username=<?php echo $logged_in_user ?>">Password Generator</a></li>
							</ul>
						</li>
						<li>
							<a href="">HTMLi Via Cookie Injection</a>
							<ul>
								<li><a href="index.php?page=capture-data.php">Capture Data Page</a></li>
							</ul>
						</li>
						<li>
							<a href="">HTTP Parameter Pollution</a>
							<ul>
								<li><a href="index.php?page=user-poll.php">Poll Question</a></li>
								<li><a href="index.php?page=document-viewer.php&PathToDocument=documentation/how-to-access-Mutillidae-over-Virtual-Box-network.php">Document Viewer</a></li>
							</ul>
						</li>
						<li>
							<a href="">JavaScript Injection</a>
							<ul>
								<li><a href="index.php?page=back-button-discussion.php">Those &quot;Back&quot; Buttons</a></li>
								<li><a href="index.php?page=password-generator.php&username=<?php echo $logged_in_user ?>">Password Generator</a></li>
								<li><a href="index.php?page=browser-info.php">Browser Info</a></li>
							</ul>
						</li>
						<li>
							<a href="">JavaScript Object Notation (JSON) Injection</a>
							<ul>
								<li><a href="index.php?page=pen-test-tool-lookup.php">Pen Test Tool Lookup</a></li>
								<li><a href="index.php?page=pen-test-tool-lookup-ajax.php">Pen Test Tool Lookup (AJAX)</a></li>
							</ul>
						</li>
						<li>
							<a href="">LDAP Injection</a>
							<ul>
								<li><a href="index.php?page=conference-room-lookup.php">Conference Room Lookup</a></li>
							</ul>
						</li>
						<li>
							<a href="">Parameter Addition</a>
							<ul>
								<li><a href="index.php?page=repeater.php">Repeater</a></li>
								<li><a href="index.php?page=view-user-privilege-level.php&iv=6bc24fc1ab650b25b4114e93a98f1eba">View User Privileges</a></li>
							</ul>
						</li>
						<li>
							<a href="">XML External Entity Injection</a>
							<ul>
								<li><a href="index.php?page=xml-validator.php">XML Validator</a></li>
							</ul>
						</li>
						<li>
							<a href="">XML Entity Expansion</a>
							<ul>
								<li><a href="index.php?page=xml-validator.php">XML Validator</a></li>
							</ul>
						</li>
						<li>
							<a href="">XML Injection</a>
							<ul>
								<li><a href="index.php?page=xml-validator.php">XML Validator</a></li>
							</ul>
						</li>
						<li>
							<a href="">XPath Injection</a>
							<ul>
								<li><a href="index.php?page=user-info-xpath.php">User Info (XPath)</a></li>
							</ul>
						</li>
					</ul>
				</li>
				<li>
					<a href="">
						A2 - Broken Authentication and Session Management
					</a>
					<ul>
						<li><a href="">Authentication Bypass</a>
							<ul>
								<li><a href="index.php?page=login.php">Via Brute Force</a></li>
								<li><a href="index.php?page=privilege-escalation.php">Via Cookies</a></li>
								<li><a href="index.php?page=login.php">Via SQL Injection</a></li>
								<li><a href="index.php?page=edit-account-profile.php<?php echo $lUserIDString; ?>">Via Account Hijacking</a></li>
							</ul>
						</li>
						<li><a href="">Priviliege Escalation</a>
							<ul>
								<li><a href="index.php?page=privilege-escalation.php">Via Cookies</a></li>
								<li><a href="index.php?page=login.php">Login</a></li>
								<li><a href="index.php?page=edit-account-profile.php<?php echo $lUserIDString; ?>">Via Account Hijacking</a></li>
								<li><a href="index.php?page=view-user-privilege-level.php&iv=6bc24fc1ab650b25b4114e93a98f1eba">Via CBC-bit Flipping</a></li>
							</ul>
						</li>
						<li><a href="">Username Enumeration</a>
							<ul>
								<li><a href="index.php?page=login.php">Login</a></li>
								<li><a href="index.php?page=edit-account-profile.php<?php echo $lUserIDString; ?>">Edit User Profile</a></li>
								<li><a href="./webservices/soap/ws-user-account.php">Lookup User (SOAP Web Service)</a></li>
								<li><a href="./webservices/rest/ws-user-account.php">User Account Management (REST Web Service)</a></li>
							</ul>
						</li>
        				<li>
        					<a href="">JSON Web Token (JWT)</a>
        					<ul>
        						<li><a href="index.php?page=jwt.php">Current User Information</a></li>
        					</ul>
        				</li>
					</ul>
				</li>
				<li>
					<a href="">A3 - Sensitive Data Exposure</a>
					<ul>
						<li>
							<a href="">Information Disclosure</a>
							<ul>
								<li><a href="index.php?page=edit-account-profile.php<?php echo $lUserIDString; ?>">Edit User Profile</a></li>
								<li><a href="index.php?page=phpinfo.php">PHP Info Page</a></li>
								<li><a href="index.php?page=robots-txt.php">Robots.txt</a></li>
								<li><a href="index.php?page=secret-administrative-pages.php">"Secret" Administrative Pages</a></li>
								<li><a href="index.php?page=html5-storage.php">HTML5 Web Storage</a></li>
								<li><a href="index.php?page=client-side-comments.php">HTML/JavaScript Comments</a></li>
								<li><a href="index.php?page=cache-control.php">Cache-Control</a></li>
								<li><a href="index.php?page=framing.php">Click-Jacking</a></li>
								<li><a href="framer.html">Cross-Site Framing</a></li>
								<li><a href="index.php?page=framing.php">X-Frame-Options (Click-Jacking)</a></li>
								<li><a href="framer.html">X-Frame-Options (Cross-frame Scripting)</a></li>
							</ul>
						</li>

						<li>
							<a href="">Application Path Disclosure</a>
							<ul>
								<li><a href="index.php?page=phpinfo.php">PHP Info Page</a></li>
								<li><a href="index.php?page=robots-txt.php">Robots.txt</a></li>
							</ul>
						</li>
						<li>
							<a href="">Platform Path Disclosure</a>
							<ul>
								<li><a href="index.php?page=phpinfo.php">PHP Info Page</a></li>
							</ul>
						</li>
						<li><a href="index.php?page=ssl-misconfiguration.php">SSL Misconfiguration</a></li>
        				<li>
        					<a href="">JSON Web Token (JWT)</a>
        					<ul>
        						<li><a href="index.php?page=jwt.php">Current User Information</a></li>
        					</ul>
        				</li>
					</ul>
				</li>
				<li>
					<a href="">A4 - XML External Entities</a>
					<ul>
						<li>
							<a href="">XML External Entity Injection</a>
							<ul>
								<li><a href="index.php?page=xml-validator.php">XML Validator</a></li>
							</ul>
						</li>
					</ul>
				</li>
				<li>
					<a href="">A5 - Broken Access Control</a>
					<ul>
						<li>
							<a href="">Insecure Direct Object References</a>
							<ul>
								<li><a href="index.php?page=edit-account-profile.php<?php echo $lUserIDString; ?>">Via Account Hijacking</a></li>
								<li><a href="index.php?page=arbitrary-file-inclusion.php">Local File Inclusion</a></li>
								<li><a href="index.php?page=arbitrary-file-inclusion.php">Remote File Inclusion</a></li>
								<li><a href="index.php?page=text-file-viewer.php">Text File Viewer</a></li>
								<li><a href="index.php?page=source-viewer.php">Source Viewer</a></li>
								<li><a href="index.php?page=credits.php">Credits</a></li>
							</ul>
						</li>
						<li>
							<a href="">Missing Function Level Access Control</a>
							<ul>
								<li><a href="index.php?page=privilege-escalation.php">Cookies as Auth Tokens</a></li>
								<li><a href="index.php?page=secret-administrative-pages.php">"Secret" Administrative Pages</a></li>
								<li><a href="index.php?page=client-side-control-challenge.php">Client-side Control Challenge</a></li>
								<li><a href="index.php?page=robots-txt.php">Robots.txt</a></li>
							</ul>
						</li>
        				<li>
        					<a href="">JSON Web Token (JWT)</a>
        					<ul>
        						<li><a href="index.php?page=jwt.php">Current User Information</a></li>
        					</ul>
        				</li>
        				<li>
        					<a href="">Cross-origin Resource Sharing (CORS)</a>
        					<ul>
								<li><a href="index.php?page=cors.php">Echo Message (CORS)</a></li>
        					</ul>
        				</li>
					</ul>
				</li>
				<li>
					<a href="">A6 - Security Misconfiguration</a>
					<ul>
        				<li>
        					<a href="">Cross-origin Resource Sharing (CORS)</a>
        					<ul>
								<li><a href="index.php?page=cors.php">Echo Message (CORS)</a></li>
        					</ul>
        				</li>
						<li><a href="index.php?page=directory-browsing.php">Directory Browsing</a></li>
						<li>
							<a href="">Method Tampering (GET for POST)</a>
							<ul>
								<li><a href="index.php?page=add-to-your-blog.php">Add to your blog</a></li>
								<li><a href="index.php?page=user-info.php">User Info (SQL)</a></li>
								<li><a href="index.php?page=user-info-xpath.php">User Info (XPath)</a></li>
								<li><a href="index.php?page=user-poll.php">Poll Question</a></li>
								<li><a href="index.php?page=dns-lookup.php">DNS Lookup</a></li>
								<li><a href="index.php?page=echo.php">Echo Message</a></li>
								<li><a href="index.php?page=cors.php">Echo Message (CORS)</a></li>
								<li><a href="index.php?page=content-security-policy.php">Echo Message (CSP)</a></li>
							</ul>
						</li>

						<li>
							<a href="">HTTP Headers</a>
							<ul>
								<li><a href="index.php?page=cors.php">Cross-Origin Resource Sharing (CORS)</a></li>
        						<li><a href="index.php?page=cache-control.php">Cache-Control</a></li>
        						<li><a href="index.php?page=framing.php">Click-Jacking</a></li>
        						<li><a href="framer.html">Cross-Site Framing (Third-Party Framing)</a></li>
							</ul>
						</li>
						<li><a href="index.php?page=user-agent-impersonation.php">User-Agent Impersonation</a></li>
						<li><a href="index.php?page=upload-file.php">Unrestricted File Upload</a></li>
						<li><a href="index.php?page=ssl-misconfiguration.php">SSL Misconfiguration</a></li>
						<li><a href="index.php?page=phpinfo.php">PHP Info Page</a></li>
						<li><a href="index.php?page=robots-txt.php">Robots.txt</a></li>
					</ul>
				</li>
				<li>
					<a href="">A7 - Cross Site Scripting (XSS)</a>
					<ul>
						<li>
							<a href="">Reflected (First Order)</a>
							<ul>
								<li><a href="index.php?page=dns-lookup.php">DNS Lookup</a></li>
								<li><a href="index.php?page=echo.php">Echo Message</a></li>
								<li><a href="index.php?page=cors.php">Echo Message (CORS)</a></li>
								<li><a href="index.php?page=content-security-policy.php">Echo Message (CSP)</a></li>
								<li><a href="index.php?page=pen-test-tool-lookup.php">Pen Test Tool Lookup</a></li>
								<li><a href="index.php?page=text-file-viewer.php">Text File Viewer</a></li>
								<li><a href="index.php?page=user-info.php">User Info (SQL)</a></li>
								<li><a href="index.php?page=set-background-color.php">Set Background Color</a></li>
								<li><a href="index.php?page=html5-storage.php">HTML5 Web Storage</a></li>
								<li><a href="index.php?page=capture-data.php">Capture Data Page</a></li>
								<li><a href="index.php?page=document-viewer.php&PathToDocument=documentation/how-to-access-Mutillidae-over-Virtual-Box-network.php">Document Viewer</a></li>
								<li><a href="index.php?page=arbitrary-file-inclusion.php">Arbitrary File Inclusion</a></li>
								<li><a href="index.php?page=xml-validator.php">XML Validator</a></li>
								<li><a href="index.php?page=user-info-xpath.php">User Info (XPath)</a></li>
								<li><a href="index.php?page=user-poll.php">Poll Question</a></li>
								<li><a href="index.php?page=register.php">Register User</a></li>
								<li><a href="index.php?page=browser-info.php">Browser Info</a></li>
								<li><a href="index.php?page=back-button-discussion.php">Those &quot;Back&quot; Buttons</a></li>
								<li><a href="index.php?page=styling-frame.php&page-to-frame=styling.php%3Fpage-title%3DStyling+with+Mutillidae">Styling with Mutilidae</a></li>
								<li><a href="index.php?page=password-generator.php&username=<?php echo $logged_in_user ?>">Password Generator</a></li>
								<li><a href="index.php?page=client-side-control-challenge.php">Client-side Control Challenge</a></li>
							</ul>
						</li>
						<li>
							<a href="">Persistent (Second Order)</a>
							<ul>
								<li><a href="index.php?page=add-to-your-blog.php">Add to your blog</a></li>
								<li><a href="index.php?page=view-someones-blog.php">View someone's blog</a></li>
								<li><a href="index.php?page=register.php">Register User</a></li>
								<li><a href="index.php?page=edit-account-profile.php<?php echo $lUserIDString; ?>">Edit User Profile</a></li>
								<li><a href="index.php?page=show-log.php">Show Log</a><li>
							</ul>
						</li>
						<li>
							<a href="">DOM-Based</a>
							<ul>
								<li><a href="index.php?page=html5-storage.php">HTML5 Web Storage</a></li>
								<li><a href="index.php?page=password-generator.php&username=<?php echo $logged_in_user ?>">Password Generator</a></li>
							</ul>
						</li>
						<li>
            				<a href="" target="_blank">Cross Site Request Forgery (CSRF)</a>
            				<ul>
            					<li><a href="index.php?page=add-to-your-blog.php">Add to your blog</a></li>
            					<li><a href="index.php?page=register.php">Register User</a></li>
            					<li><a href="index.php?page=user-poll.php">Poll Question</a></li>
            				</ul>
            			</li>
						<li>
							<a href="">Via "Input" (GET/POST)</a>
							<ul>
								<li><a href="index.php?page=add-to-your-blog.php">Add to your blog</a></li>
								<li><a href="index.php?page=view-someones-blog.php">View someone's blog</a></li>
								<li><a href="index.php?page=show-log.php">Show Log</a><li>
								<li><a href="index.php?page=text-file-viewer.php">Text File Viewer</a></li>
								<li><a href="index.php?page=dns-lookup.php">DNS Lookup</a></li>
								<li><a href="index.php?page=echo.php">Echo Message</a></li>
								<li><a href="index.php?page=cors.php">Echo Message (CORS)</a></li>
								<li><a href="index.php?page=content-security-policy.php">Echo Message (CSP)</a></li>
								<li><a href="index.php?page=user-info.php">User Info (SQL)</a></li>
								<li><a href="index.php?page=user-info-xpath.php">User Info (XPath)</a></li>
								<li><a href="index.php">Missing HTTPOnly Attribute</a></li>
								<li><a href="index.php?page=set-background-color.php">Set Background Color</a></li>
								<li><a href="index.php?page=pen-test-tool-lookup.php">Pen Test Tool Lookup</a></li>
								<li><a href="index.php?page=document-viewer.php&PathToDocument=documentation/how-to-access-Mutillidae-over-Virtual-Box-network.php">Document Viewer</a></li>
							</ul>
						</li>
						<li>
							<a href="">Via HTTP Headers</a>
							<ul>
								<li><a href="index.php?page=browser-info.php">Browser Info</a></li>
								<li><a href="index.php?page=show-log.php">Show Log</a><li>
								<li><a href="index.php?page=site-footer-xss-discussion.php">Site Footer</a><li>
								<li><a href="index.php?page=back-button-discussion.php">Those &quot;Back&quot; Buttons</a></li>
							</ul>
						</li>
						<li>
							<a href="">Via HTTP Attribute</a>
							<ul>
								<li><a href="index.php?page=document-viewer.php&PathToDocument=documentation/how-to-access-Mutillidae-over-Virtual-Box-network.php">Document Viewer</a></li>
							</ul>
						</li>
						<li>
							<a href="">Via Misconfiguration</a>
							<ul>
								<li><a href="index.php">Missing HTTPOnly Attribute</a></li>
							</ul>
						</li>
						<li>
							<a href="">Against HTML5 Web Storage</a>
							<ul>
								<li><a href="index.php?page=html5-storage.php">HTML5 Web Storage</a></li>
							</ul>
						</li>
						<li>
							<a href="">Against JSON</a>
							<ul>
								<li><a href="index.php?page=pen-test-tool-lookup.php">Pen Test Tool Lookup</a></li>
							</ul>
						</li>
						<li>
							<a href="">Via Cookie Injection</a>
							<ul>
								<li><a href="index.php?page=capture-data.php">Capture Data Page</a></li>
							</ul>
						</li>
						<li>
							<a href="">Via XML Injection</a>
							<ul>
								<li><a href="index.php?page=xml-validator.php">XML Validator</a></li>
							</ul>
						</li>
						<li>
							<a href="">Via XPath Injection</a>
							<ul>
								<li><a href="index.php?page=user-info-xpath.php">User Info (XPath)</a></li>
							</ul>
						</li>
						<li>
							<a href="">Via Path Relative Style Sheet Injection</a>
							<ul>
								<li><a href="index.php?page=styling-frame.php&page-to-frame=styling.php%3Fpage-title%3DStyling+with+Mutillidae">Styling with Mutilidae</a></li>
							</ul>
						</li>
						<li>
							<a href="">BeeF Framework Targets</a>
							<ul>
								<li><a href="index.php?page=add-to-your-blog.php">Add to your blog</a></li>
								<li><a href="index.php?page=view-someones-blog.php">View someone's blog</a></li>
								<li><a href="index.php?page=show-log.php">Show Log</a><li>
								<li><a href="index.php?page=dns-lookup.php">DNS Lookup</a></li>
								<li><a href="index.php?page=echo.php">Echo Message</a></li>
								<li><a href="index.php?page=cors.php">Echo Message (CORS)</a></li>
								<li><a href="index.php?page=content-security-policy.php">Echo Message (CSP)</a></li>
								<li><a href="index.php?page=pen-test-tool-lookup.php">Pen Test Tool Lookup</a></li>
								<li><a href="index.php?page=text-file-viewer.php">Text File Viewer</a></li>
								<li><a href="index.php?page=user-info.php">User Info (SQL)</a></li>
								<li><a href="index.php?page=set-background-color.php">Set Background Color</a></li>
								<li><a href="index.php?page=html5-storage.php">HTML5 Web Storage</a></li>
								<li><a href="index.php?page=capture-data.php">Capture Data Page</a></li>
								<li><a href="index.php?page=document-viewer.php&PathToDocument=documentation/how-to-access-Mutillidae-over-Virtual-Box-network.php">Document Viewer</a></li>
								<li><a href="index.php?page=arbitrary-file-inclusion.php">Arbitrary File Inclusion</a></li>
								<li><a href="index.php?page=xml-validator.php">XML Validator</a></li>
								<li><a href="index.php?page=user-info-xpath.php">User Info (XPath)</a></li>
								<li><a href="index.php?page=user-poll.php">Poll Question</a></li>
								<li><a href="index.php?page=register.php">Register User</a></li>
								<li><a href="index.php?page=password-generator.php&username=<?php echo $logged_in_user ?>">Password Generator</a></li>
							</ul>
						</li>
					</ul>
				</li>
				<li>
					<a href="">A8 - Insecure Deserialization</a>
				</li>
				<li>
					<a href="">A9 - Using Components with Known Vulnerabilities</a>
					<ul>
						<li><a href="index.php?page=phpinfo.php">PHP Info Page</a></li>
						<li><a href="index.php?page=view-user-privilege-level.php&iv=6bc24fc1ab650b25b4114e93a98f1eba">CBC-bit Flipping</a></li>
						<li><a href="index.php?page=ssl-misconfiguration.php">SSL Misconfiguration</a></li>
					</ul>
				</li>
				<li>
					<a href="">A10 - Insufficient Logging and Monitoring</a>
				</li>
			</ul>
		</li>
	</ul>
	<ul>
		<li>
			<a href="">OWASP 2013</a>
			<ul>
				<li>
					<a href="">A8 - Cross Site Request Forgery (CSRF)</a>
					<ul>
						<li><a href="index.php?page=add-to-your-blog.php">Add to your blog</a></li>
						<li><a href="index.php?page=register.php">Register User</a></li>
						<li><a href="index.php?page=user-poll.php">Poll Question</a></li>
					</ul>
				</li>
				<li>
					<a href="">A10 - Unvalidated Redirects and Forwards</a>
					<ul>
						<li><a href="?page=credits.php">Credits</a></li>
						<?php if (isset($_COOKIE["uid"]) && $_COOKIE["uid"]==1) { ?>
						<li><a href="set-up-database.php">Setup/reset the DB</a></li>
						<?php } else { ?>
						<li><a href="">Setup/reset the DB (Disabled: Not Admin)</a></li>
						<?php }; ?>
					</ul>
				</li>
			</ul>
		</li>
	</ul>
	<ul>
		<li>
			<a href="">OWASP 2010</a>
			<ul>
				<li>
					<a href="">A7 - Insecure Cryptographic Storage</a>
					<ul>
						<li><a href="index.php?page=user-info.php">User Info (SQL)</a></li>
						<li><a href="index.php?page=user-info-xpath.php">User Info (XPath)</a></li>
						<li><a href="index.php?page=html5-storage.php">HTML5 Web Storage</a></li>
						<li><a href="index.php?page=view-user-privilege-level.php&iv=6bc24fc1ab650b25b4114e93a98f1eba">View User Privileges</a></li>
					</ul>
				</li>
				<li>
					<a href="">A8 - Failure to Restrict URL Access</a>
					<ul>
						<li><a href="index.php?page=edit-account-profile.php<?php echo $lUserIDString; ?>">Edit User Profile</a></li>
						<li><a href="index.php?page=source-viewer.php">Source Viewer</a></li>
						<li><a href="index.php?page=secret-administrative-pages.php">"Secret" Administrative Pages</a></li>
						<li><a href="index.php?page=robots-txt.php">Robots.txt</a></li>
						<li><a href="index.php?page=arbitrary-file-inclusion.php">Arbitrary File Inclusion</a></li>
						<li><a href="index.php?page=phpinfo.php">PHP Info Page</a></li>
					</ul>
				</li>
				<li>
					<a href="">A9 - Insufficient Transport Layer Protection</a>
					<ul>
						<li><a href="index.php?page=ssl-misconfiguration.php">SSL Misconfiguration</a></li>
						<li><a href="index.php?page=login.php">Login</a></li>
						<li><a href="index.php?page=user-info.php">User Info (SQL)</a></li>
						<li><a href="index.php?page=user-info-xpath.php">User Info (XPath)</a></li>
					</ul>
				</li>
			</ul>
		</li>
	</ul>
	<ul>
		<li>
			<a href="">OWASP 2007</a>
			<ul>
				<li>
					<a href="">A3 - Malicious File Execution</a>
					<ul>
						<li><a href="index.php?page=text-file-viewer.php">Text File Viewer</a></li>
						<li><a href="index.php?page=source-viewer.php">Source Viewer</a></li>
					</ul>
				</li>
				<li>
					<a href="">A6 - Information Leakage</a>
					<ul>
						<li><a href="index.php?page=edit-account-profile.php<?php echo $lUserIDString; ?>">Edit User Profile</a></li>
						<li><a href="index.php">Cache-Control</a></li>
						<li><a href="index.php">X-Powered-By HTTP Header</a></li>
						<li><a href="index.php?page=client-side-comments.php">HTML/JavaScript Comments</a></li>
						<li><a href="index.php?page=framing.php">Click-Jacking</a></li>
						<li><a href="framer.html">Cross-Site Framing (Third-Party Framing)</a></li>
						<li><a href="index.php?page=html5-storage.php">HTML5 Web Storage</a></li>
						<li><a href="index.php?page=phpinfo.php">PHP Info Page</a></li>
						<li><a href="index.php?page=robots-txt.php">Robots.txt</a></li>
						<li><a href="index.php?page=ssl-misconfiguration.php">SSL Misconfiguration</a></li>
					</ul>
				</li>
				<li>
					<a href="">A6 - Improper Error Handling</a>
					<ul>
						<li><a href="index.php?page=user-info.php">User Info (SQL)</a></li>
						<li><a href="index.php?page=user-info-xpath.php">User Info (XPath)</a></li>
						<li><a href="index.php?page=login.php">Login</a></li>
						<li><a href="index.php?page=register.php">Register</a></li>
						<li><a href="index.php?page=edit-account-profile.php<?php echo $lUserIDString; ?>">Edit User Profile</a></li>
						<li><a href="index.php?page=pen-test-tool-lookup.php">Pen Test Tool Lookup</a></li>
						<li><a href="index.php?page=pen-test-tool-lookup-ajax.php">Pen Test Tool Lookup (AJAX)</a></li>
						<li><a href="./webservices/soap/ws-user-account.php">Lookup User (SOAP Web Service)</a></li>
					</ul>
				</li>
			</ul>
		</li>
	</ul>
	<ul>
		<li>
			<a href="">Web Services</a>
			<ul>
				<li>
					<a href="">SOAP</a>
					<ul>
						<li>
							<a href="">Test Page</a>
							<ul>
								<li><a href="./webservices/soap/ws-hello-world.php">Hello World</a></li>
							</ul>
						</li>
						<li>
							<a href="">Command Injection</a>
							<ul>
								<li><a href="./webservices/soap/ws-lookup-dns-record.php">DNS Lookup</a></li>
							</ul>
						</li>
						<li>
							<a href="">SQL Injection</a>
							<ul>
								<li><a href="./webservices/soap/ws-user-account.php">Lookup User</a></li>
							</ul>
						</li>
						<li>
							<a href="">Username Enumeration</a>
							<ul>
								<li><a href="./webservices/soap/ws-user-account.php">Lookup User</a></li>
							</ul>
						</li>
					</ul>
				</li>
				<li>
					<a href="">REST</a>
					<ul>
						<li>
							<a href="">SQL Injection</a>
							<ul>
								<li><a href="./webservices/rest/ws-user-account.php">User Account Management</a></li>
							</ul>
						</li>
						<li>
							<a href="">Username Enumeration</a>
							<ul>
								<li><a href="./webservices/rest/ws-user-account.php">User Account Management</a></li>
							</ul>
						</li>
					</ul>
				</li>
			</ul>
		</li>
		<li>
			<a href="">Others</a>
			<ul>
				<li>
					<a href="">Cross-origin Resource Sharing (CORS)</a>
					<ul>
						<li><a href="index.php?page=cors.php">Echo Message (CORS)</a></li>
					</ul>
				</li>
				<li>
					<a href="">Client-side "Security" Controls</a>
					<ul>
						<li><a href="index.php?page=client-side-control-challenge.php">Client-side Control Challenge</a></li>
					</ul>
				</li>
				<li>
					<a href="">Cross-Site Framing</a>
					<ul>
   						<li><a href="index.php?page=framing.php">Click-Jacking</a></li>
  						<li><a href="framer.html">Cross-Site Framing (Third-Party Framing)</a></li>
					</ul>
				</li>
				<li>
					<a href="">Tab-Nabbing</a>
					<ul>
   						<li><a href="index.php?page=nice-tabby-cat.php">Tabby Cat</a></li>
					</ul>
				</li>
				<li>
					<a href="">Unrestricted File Upload</a>
					<ul>
						<li><a href="index.php?page=upload-file.php">File Upload</a></li>
					</ul>
				</li>
				<li>
					<a href="">Denial of Service</a>
					<ul>
						<li><a href="?page=text-file-viewer.php">Text File Viewer</a></li>
						<li><a href="?page=show-log.php">Show Web Log</a><li>
					</ul>
				</li>
				<li>
					<a href="">JavaScript Validation Bypass</a>
					<ul>
						<li><a href="index.php?page=login.php">Login</a></li>
						<li><a href="index.php?page=user-info.php">User Info (SQL)</a></li>
						<li><a href="index.php?page=user-info-xpath.php">User Info (XPath)</a></li>
						<li><a href="index.php?page=add-to-your-blog.php">Add to your blog</a></li>
						<li><a href="index.php?page=html5-storage.php">HTML5 Web Storage</a></li>
						<li><a href="index.php?page=dns-lookup.php">DNS Lookup</a></li>
						<li><a href="index.php?page=echo.php">Echo Message</a></li>
						<li><a href="index.php?page=cors.php">Echo Message (CORS)</a></li>
						<li><a href="index.php?page=content-security-policy.php">Echo Message (CSP)</a></li>
						<li><a href="index.php?page=repeater.php">Repeater</a></li>
					</ul>
				</li>
				<li>
					<a href="index.php?page=user-agent-impersonation.php">User-Agent Impersonation</a>
				</li>
				<li>
					<a href="">Data Capture Pages</a>
					<ul>
						<li><a href="index.php?page=capture-data.php">Data Capture</a></li>
						<li><a href="index.php?page=captured-data.php">View Captured Data</a></li>
					</ul>
				</li>
				<li>
					<a href="">JSON Web Token (JWT)</a>
					<ul>
						<li><a href="index.php?page=jwt.php">Current User Information</a></li>
					</ul>
				</li>
			</ul>
		</li>
		<li>
			<a href="">Labs</a>
			<ul>
				<li>
					<a href="">How the Web Works</a>
					<ul>
						<li><a href="index.php?page=labs/lab-1.php">Lab 1 - Sending HTTP Requests with Netcat</a></li>
						<li><a href="index.php?page=labs/lab-2.php">Lab 2 - Capturing HTTP Packets with Wireshark</a></li>
						<li><a href="index.php?page=labs/lab-3.php">Lab 3 - Intercepting HTTP Requests and Responses</a></li>
						<li><a href="index.php?page=labs/lab-4.php">Lab 4 - Using Burp-Suite Proxy History</a></li>
						<li><a href="index.php?page=labs/lab-5.php">Lab 5 - Bypassing Client-side Security</a></li>
					</ul>
				</li>
				<li>
					<a href="">SQL Injection</a>
					<ul>
						<li><a href="index.php?page=labs/lab-6.php">Lab 6 - Testing for SQL Errors</a></li>
						<li><a href="index.php?page=labs/lab-7.php">Lab 7 - Using SQLi to Bypass Authentication</a></li>
						<li><a href="index.php?page=labs/lab-8.php">Lab 8 - Extracting data</a></li>
						<li><a href="index.php?page=labs/lab-9.php">Lab 9 - Finding Number of Columns</a></li>
						<li><a href="index.php?page=labs/lab-10.php">Lab 10 - Pivoting with SQL injection</a></li>
						<li><a href="index.php?page=labs/lab-11.php">Lab 11 - SQLMap</a></li>
					</ul>
				</li>
				<li>
					<a href="">Command Injection</a>
					<ul>
						<li><a href="index.php?page=labs/lab-12.php">Lab 12 - Extracting User Accounts with Command Injection</a></li>
						<li><a href="index.php?page=labs/lab-13.php">Lab 13 - Testing for Reverse Connection</a></li>
						<li><a href="index.php?page=labs/lab-14.php">Lab 14 - Reverse Bash Shell with Command Injection</a></li>
						<li><a href="index.php?page=labs/lab-15.php">Lab 15 - Reverse Meterpreter Shell with Command Injection</a></li>
						<li><a href="index.php?page=labs/lab-16.php">Lab 16 - Web Shell with Command injection</a></li>
					</ul>
				</li>
				<li>
					<a href="">Lightweight Directory Access Protocol (LDAP) Injection</a>
					<ul>
						<li><a href="index.php?page=labs/lab-17.php">Lab 17 - Extract User Accounts with LDAP Injection</a></li>
					</ul>
				</li>
				<li>
					<a href="">Insecure Direct Object Referece (IDOR)</a>
					<ul>
						<li><a href="index.php?page=labs/lab-18.php">Lab 18 - Edit Another User's Profile</a></li>
						<li><a href="index.php?page=labs/lab-19.php">Lab 19 - Extracting User Accounts with IDOR</a></li>
						<li><a href="index.php?page=labs/lab-20.php">Lab 20 - Extracting User Accounts with Local File Inclusion</a></li>
						<li><a href="index.php?page=labs/lab-21.php">Lab 21 - Web Shell with Local File Inclusion (LFI)</a></li>
						<li><a href="index.php?page=labs/lab-22.php">Lab 22 - Web Shell with Remote File Inclusion (RFI)</a></li>
					</ul>
				</li>
				<li>
					<a href="">Open Redirect</a>
					<ul>
						<li><a href="index.php?page=labs/lab-23.php">Lab 23 - Open Redirects - Part 1</a></li>
						<li><a href="index.php?page=labs/lab-24.php">Lab 24 - Open Redirects - Part 2</a></li>
					</ul>
				</li>
				<li>
					<a href="">Cross-site Scripting (XSS)</a>
					<ul>
						<li><a href="index.php?page=labs/lab-25.php">Lab 25 - Creating a Cross-site Script Proof of Concept (PoC)</a></li>
						<li><a href="index.php?page=labs/lab-26.php">Lab 26 - Browser Exploitation Framework (BeEF)</a></li>
						<li><a href="index.php?page=labs/lab-27.php">Lab 27 - Bypassing Client-side Defenses</a></li>
						<li><a href="index.php?page=labs/lab-28.php">Lab 28 - Defending Against Cross-site Scripting</a></li>
					</ul>
				</li>
				<li>
					<a href="">Cross-site Request Forgery (CSRF)</a>
					<ul>
						<li><a href="index.php?page=labs/lab-29.php">Lab 29 - Voting for NMap</a></li>
						<li><a href="index.php?page=labs/lab-30.php">Lab 30 - Adding a Blog</a></li>
					</ul>
				</li>
				<li>
					<a href="">HTML5 Web Storage</a>
					<ul>
						<li><a href="index.php?page=labs/lab-31.php">Lab 31 - Capturing HTML 5 Web Storage</a></li>
					</ul>
				</li>
				<li>
					<a href="">Session Management</a>
					<ul>
						<li><a href="index.php?page=labs/lab-32.php">Lab 32 - Capture Session with XSS</a></li>
						<li><a href="index.php?page=labs/lab-33.php">Lab 33 - Intercepting Session Token using Network Sniffer</a></li>
						<li><a href="index.php?page=labs/lab-34.php">Lab 34 - Exfiltrating Data with XSS</a></li>
						<li><a href="index.php?page=labs/lab-35.php">Lab 35 - Capture Session with XSS</a></li>
						<li><a href="index.php?page=labs/lab-36.php">Lab 36 - Using a captured session token</a></li>
					</ul>
				</li>
				<li>
					<a href="">Cookie Management</a>
					<ul>
						<li><a href="index.php?page=labs/lab-37.php">Lab 37 - Show Hints</a></li>
						<li><a href="index.php?page=labs/lab-38.php">Lab 38 - Privilege Escalation</a></li>
					</ul>
				</li>
				<li>
					<a href="">Password Management</a>
					<ul>
						<li><a href="index.php?page=labs/lab-39.php">Lab 39 - Password Cracking</a></li>
						<li><a href="index.php?page=labs/lab-40.php">Lab 40 - Intercepting Clear-Text Credentials</a></li>
						<li><a href="index.php?page=labs/lab-41.php">Lab 41 - Hydra</a></li>
					</ul>
				</li>
				<li>
					<a href="">Input Validation</a>
					<ul>
						<li><a href="index.php?page=labs/lab-42.php">Lab 42 - Input Validation</a></li>
						<li><a href="index.php?page=labs/lab-43.php">Lab 43 - File Identification</a></li>
					</ul>
				</li>
				<li>
					<a href="">Error Handling</a>
					<ul>
						<li><a href="index.php?page=labs/lab-44.php">Lab 44 - Grabbing Server Banners</a></li>
						<li><a href="index.php?page=labs/lab-45.php">Lab 45 - Creating Injection Files</a></li>
						<li><a href="index.php?page=labs/lab-46.php">Lab 46 - Scanning with Nikto</a></li>
						<li><a href="index.php?page=labs/lab-47.php">Lab 47 - Testing for Errors</a></li>
					</ul>
				</li>
				<li>
					<a href="">Logging</a>
					<ul>
						<li><a href="index.php?page=labs/lab-48.php">Lab 48 - Log Poisoning</a></li>
						<li><a href="index.php?page=labs/lab-49.php">Lab 49 - Log Disclosure</a></li>
					</ul>
				</li>
				<li>
					<a href="">Server Configuration</a>
					<ul>
						<li><a href="index.php?page=labs/lab-50.php">Lab 50 - Testing HTTP Response Headers</a></li>
						<li><a href="index.php?page=labs/lab-51.php">Lab 51 - Scanning with Nikto</a></li>
						<li><a href="index.php?page=labs/lab-52.php">Lab 52 - Scanning with SSLScan</a></li>
					</ul>
				</li>
				<li>
					<a href="">Content Security Policy (CSP)</a>
					<ul>
						<li><a href="index.php?page=labs/lab-53.php">Lab 53 - Testing HTTP Response Headers</a></li>
						<li><a href="index.php?page=labs/lab-54.php">Lab 54 - Scanning with SSLScan</a></li>
						<li><a href="index.php?page=labs/lab-55.php">Lab 55 - Testing Content Security Policy (CSP)</a></li>
					</ul>
				</li>
				<li>
					<a href="">JSON Web Token (JWT) Security</a>
					<ul>
						<li><a href="index.php?page=labs/lab-56.php">Lab 56 - Session Timeout</a></li>
						<li><a href="index.php?page=labs/lab-57.php">Lab 57 - Missing Signature Validation</a></li>
						<li><a href="index.php?page=labs/lab-58.php">Lab 58 - Cracking Signature Password</a></li>
						<li><a href="index.php?page=labs/lab-59.php">Lab 59 - SQL Injection via JWT</a></li>
					</ul>
				</li>
				<li>
					<a href="">Cross-origin Resource Sharing (CORS)</a>
					<ul>
						<li><a href="index.php?page=labs/lab-60.php">Lab 60 - Access Control Allow Origin Header</a></li>
						<li><a href="index.php?page=labs/lab-61.php">Lab 61 - Pre-Flight Requests</a></li>
					</ul>
				</li>
				<li>
					<a href="">Software Composition Analysis (SCA)</a>
					<ul>
						<li><a href="index.php?page=labs/lab-62.php">Lab 62 - Retire.js</a></li>
						<li><a href="index.php?page=labs/lab-63.php">Lab 63 - OWASP Depenency Check</a></li>
					</ul>
				</li>
			</ul>
		</li>
		<li>
			<a href="">Documentation</a>
			<ul>
				<li><a href="index.php?page=documentation/installation.php">Installation Instructions (Linux)</a></li>
				<li><a href="documentation/mutillidae-installation-on-xampp-win7.pdf" target="_blank">Installation Instructions (Windows)</a></li>
				<li><a href="index.php?page=documentation/usage-instructions.php">Usage Instructions</a></li>
				<li><a href="index.php?page=documentation/vulnerabilities.php">Listing of Vulnerabilities</a></li>
				<li><a href="index.php?page=credits.php">Credits</a></li>
				<li>
					<a href="https://www.sans.org/reading-room/whitepapers/application/introduction-owasp-mutillidae-ii-web-pen-test-training-environment-34380" target="_blank">
						Whitepaper: Introduction to the Mutillidae Web Pen Test Training Environment
					</a>
				</li>
			</ul>
		</li>
		<li>
			<a href="">Resources</a>
			<ul>
				<li>
					<a href="https://github.com/webpwnized/mutillidae" target="_blank">
						Latest Version of OWASP Mutillidae II
					</a>
				</li>
				<li>
					<a href="https://www.owasp.org/index.php/Top_Ten" target="_blank">
						OWASP Top Ten
					</a>
				</li>
				<li>
					<a href="https://addons.mozilla.org/en-US/firefox/collections/jdruin/pro-web-developer-qa-pack/" target="_blank">
					Web Penetration Testing Add-Ons
					</a>
				</li>
			</ul>
		</li>
	</ul>
</div>
