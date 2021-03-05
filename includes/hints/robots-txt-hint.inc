	<br/><br/>
	<span class="report-header">Overview</span>
	<br/><br/>
	The Robot Exclusion Standard, Robots Exclusion Protocol or robots.txt protocol is a
	suggestion for "honest" browsers which should prevent web crawlers
	from accessing the parts of a website listed in the roboos.txt file.
<br/><br/>
<a href="#videos" class="label"><img alt="YouTube" src="/images/youtube-play-icon-40-40.png" style="margin-right: 10px;" />Video Tutorials</a>
	<br/><br/>
	<span class="report-header">Discovery Methodology</span>
	<br/><br/>
	Browse to the robots.txt file in Mutillidae and read the contents
	<br/><br/>
	<span class="report-header">Exploitation</span>
	<br/><br/>
	Follow the paths in the robots.txt file to see if any sensitive directories or files are exposed. Try
	to list the contents of directories since servers will sometimes be misconfigured to show
	directory contents.
	<br/><br/>
	<span class="label">Print robots.txt pages for list of servers</span>
	<br/>
<code>
while read HOST; do echo -n $HOST:; curl -v --silent --connect-timeout 2 --max-time 3 $HOST/robots.txt 2&gt;&amp;1 | grep -A 100 Disallow; echo; done &lt; hosts.txt
</code>
	<br/>
	<span class="label">Nmap: Sweeping for comments</span>
	<br/>
<code>
nmap -p 80,443 -v -Pn --script=http-robots.txt --open -iL hosts.txt
</code>
<br/>
<span class="report-header">Example</span>
<br/><br/>
Robots.txt is located at http://[server]/mutillidae/robots.txt with default installation. On Samurai
WTF the path will be http://mutillidae/robots.txt.
<br/><br/>
<span id="videos" class="report-header">Videos</span>
<br/><br/>
<br/><br/>
