<br/><br/>
<span class="report-header">Overview</span>
<br/><br/>
SSL Misconfiguration causes a variety of issues such as sensitive pages being
accessible via HTTP, use of weak SSL ciphers, and SSL stripping.
<br/><br/>
<a href="#videos" class="label"><img alt="YouTube" src="/images/youtube-48-48.png" style="margin-right: 10px;" />Video Tutorials</a>
<br/><br/>
<span class="report-header">Discovery Methodology</span>
<br/><br/>
Use an SSL cipher audit tool such as SSLScan to test cipher strength. Observe 
how the site handles an HTTP request. If the site redirects the user to 
HTTPS, the site can be stripped. 
<br/><br/>
<span class="report-header">Exploitation</span>
<br/><br/>
For SSLStripping, after ARP poisoning the client and the gateway, use SSLStrip to
remove SSL connection.
<br/><br/>
To test cipher strength, the following tools are helpful
<br/><br/>
<span class="label">sslscan</span>
<br/>
<code>
sslscan --targets=hosts.txt	
</code>
<br/>
<span class="label">NMap</span>
<br/>
<code>
nmap -p 443 -v -Pn --script=ssl-enum-ciphers --open -iL hosts.txt
</code>
<br/>
<span id="videos" class="report-header">Videos</span>
<br/><br/>
<?php echo $YouTubeVideoHandler->getYouTubeVideo($YouTubeVideoHandler->HowtoTestforWeakSSLTLSHTTPSciphers);?>
<?php echo $YouTubeVideoHandler->getYouTubeVideo($YouTubeVideoHandler->UsingEttercapandSSLstriptoCaptureCredentials);?>
<br/><br/>
