<br/><br/>
<span class="report-header">Setting up LDAP server for Mutillidae</span>
<br/><br/>
Install Stand-Alone LDAP Daemon (slapd). When asked, 
set the admin password to "mutillidae" (without the quotes). If these settings are not desired, ensure 
whatever settings are chosen match those in includes/ldap-config.inc
<br/>
<code>
	sudo apt install slapd ldap-utils
</code>
<br/>
Check that the server is running. The status should be "active (running)".
<br/>
<code>
	systemctl status slapd
</code>
<br/>
Configure the LDAP server with dpkg-reconfigure
<br/>
<code>
	sudo dpkg-reconfigure slapd
</code>
<br/>
Enter the following values
<br/><br/>
Omit LDAP server configuration: <span class="label">NO</span><br/>
DNS domain name: <span class="label">mutillidae.local</span><br/>
Organization name: <span class="label">mutillidae</span><br/>
Administrator password: <span class="label">mutillidae</span><br/>
Database backend: <span class="label">MDB</span><br/>
Do you want the database to be removed when slapd is purged? <span class="label">No</span><br/>
Move old database? <span class="label">Yes</span><br/>
Allow LDAPv2 protocol? <span class="label">No</span> (Note: Not all packages ask this question)
<br/><br/>
Install php-ldap. Determine version of PHP with
<br/>
<code>
	php -v
</code>
<br/>
Install the version of php-ldap that matches the version of PHP. For example
<br/>
<code>
	sudo apt-get install php-ldap
	sudo service apache2 restart
</code>
<br/>
Import the LDIF file to recreate the LDAP directory used by Mutillidae. Apache Directory Studio is 
an easy-to-use tool capible of importing LDIF files. The LDIF file is located in file
<br/>
<code>
	data/mutillidae.ldif
</code>
<br/>
<span class="report-header">Overview</span>
<br/><br/>
LDAP injection allows an attacker to bypass access controls on LDAP data stores. This may
result in data disclosure. Depending on how the application interprets the data extracted,
LDAP injection may result in authentication or authorization bypass or other 
logical vulnerabilities.
<br/><br/>
<a href="#videos" class="label"><img alt="YouTube" src="/images/youtube-48-48.png" style="margin-right: 10px;" />Video Tutorials</a>
<br/><br/>
<span class="report-header">Discovery Methodology</span>
<br/><br/>
Ideally, attempt to fuzz input fields to cause error message. For example, 
injection of "(" produces an error. Even if system does not display an explicit 
message, injection may cause a change detectable with differential analysis
<ul>
	<li>Additional or missing output</li>
	<li>Failure to function as expected</li>
	<li>Faster or slower response times aka Time-Delay Inference</li>
</ul>
LDAP String reserved characters are defined in RFC 4514: String Representation of Distinguished Names
at www.ietf.org/rfc/rfc4514.txt. RFC 4514 states "The following characters need escaping". Therefore,
they make excellent values for fuzzing.
<ul>
	<li>Space (' ' U+0020)</li>
	<li>Number sign ('#' U+0023)</li>
	<li>Double-quote, plus, comma, semi-colon, less-than, greater-than, backslash</li>
	<li>Null (U+0000)</li>
</ul>
<br/>
<span class="report-header">Exploitation</span>
<br/><br/>
Similar to SQL injection, there is no particular pattern that will work. It depends on 
the code implemented by the application. However, the following general patterns 
apply to many applications.
<br/><br/>
If there is no logic operator (OR or AND), the bolded injections may work assuming they fit
into the assumed application code segments (non-bolded parts)
<br/>
<ul>
	<li>(attribute=<span class="label">*</span>)</li>
	<li>(attribute=<span class="label">value)(attribute2=*</span>)</li>
</ul>
If there is an "OR" logic operator, the bolded injections may work assuming they fit
into the assumed application code segments (non-bolded parts)
<br/>
<ul>
	<li>(|(attribute1=<span class="label">*</span>)(second_filter))</li>
	<li>(|(attribute=<span class="label">whatever)(attribute2=*</span>)(second_filter))</li>
	<li>(|(attribute=<span class="label">*)(attribute2=*))(&amp;(1=0</span>)(second_filter))</li>
</ul>
If there is an "AND" logic operator, the bolded injections may work assuming they fit
into the assumed application code segments (non-bolded parts)
<br/>
<ul>
	<li>(&amp;(attribute1=<span class="label">*</span>)(second_filter))</li>
	<li>(&amp;(attribute=<span class="label">whatever)(attribute2=*</span>)(second_filter))</li>
	<li>(&amp;(attribute=<span class="label">*)(attribute2=*))(&amp;(1=0</span>)(second_filter))</li>
</ul>
The following injections work in Mutillidae Conference Room Lookup page
<ul>
	<li>*</li>
	<li>value)(cn=*</li>
	<li>﻿*)(whatever=*)(&(1=0)(1=1)</li>
</ul>
<br/>
<span id="videos" class="report-header">Videos</span>
<br/><br/>
I got nothing
<?php #echo $YouTubeVideoHandler->getYouTubeVideo($YouTubeVideoHandler->HowtoExploitLocalFileInclusionVulnerabilityusingBurpSuite);?>
<br/><br/>