			<br/><br/>
			<span class="report-header">Overview</span>
			<br/><br/>
			Applications may contain comments within the client-side source code. Common
			locations include HTML and JavaScript comments.
<br/><br/>
<a href="#videos" class="label"><img alt="YouTube" src="/images/youtube-play-icon-40-40.png" style="margin-right: 10px;" />Video Tutorials</a>
			<br/><br/>
			<span class="report-header">Discovery Methodology</span>
			<br/><br/>
			While these comments are not visible in the browser, they can be seen by
			using the browsers "View Source"/"View Page Source" feature. Also, tools
			and techniques are availble to automatically parse comments from an
			entire site.
			<br/><br/>
			<span class="report-header">Exploitation</span>
			<br/><br/>
			Comments can be seen in clear-text. Tools are available to help automate
			examination of entire sites.
			<br/><br/>
			<span class="label">Print comments of default pages for list of servers</span>
			<br/>
<code>
while read HOST; do echo -n $HOST:; curl -v --silent --connect-timeout 2 --max-time 3 $HOST 2&gt;&amp;1 | grep -A 100 "&lt;\!--"; echo; done &lt; hosts.txt
</code>
			<br/>
			<span class="label">Print comments from a copy of a site (i.e. from spidering with wget)</span>
			<br/>
<code>
wget -r http://localhost/mutillidae
grep -r -A 10 "&lt;\!--" *
</code>
			<br/>
			<span class="label">NMAP: Sweeping for comments</span>
			<br/>
<code>
nmap -p 80,443 -v -Pn --script=http-comments-displayer --open -iL hosts.txt
</code>
<br/>
<span class="report-header">Example</span>
<br/><br/>
There is a sensitive comment on the Mutillidae Home page. Visit the
<a href="index.php?page=home.php">Home</a> page. View the page source
to see comments.
<br/><br/>
<span id="videos" class="report-header">Videos</span>
<br/><br/>
<br/><br/>