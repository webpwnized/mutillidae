<br/><br/>
<span class="report-header">Overview</span>
<br/><br/>
SSL Misconfiguration causes a variety of issues such as sensitive pages being
accessible via HTTP, use of weak SSL ciphers, and SSL stripping.
<br/><br/>
<a href="#videos" class="label"><img alt="YouTube" src="/images/youtube-play-icon-40-40.png" style="margin-right: 10px;" />Video Tutorials</a>
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
<br/><br/>
<span id="videos" class="report-header">Videos</span>
<br/><br/>
<?php echo $YouTubeVideoHandler->getYouTubeVideo($YouTubeVideoHandler->HowtocheckHTTPScertificatefromcommandline);?>
<?php echo $YouTubeVideoHandler->getYouTubeVideo($YouTubeVideoHandler->HowtocheckHTTPSCertificatesforcommonIssues);?>
<?php echo $YouTubeVideoHandler->getYouTubeVideo($YouTubeVideoHandler->cURLErrorSSLCertificateProblem);?>
<?php echo $YouTubeVideoHandler->getYouTubeVideo($YouTubeVideoHandler->HowtoSetHTTPHeadersUsingApacheServer);?>
<?php echo $YouTubeVideoHandler->getYouTubeVideo($YouTubeVideoHandler->CheckHTTPHeaderswithcURL);?>
<?php echo $YouTubeVideoHandler->getYouTubeVideo($YouTubeVideoHandler->HowtoCheckHTTPHeadersCommandLine);?>
<?php echo $YouTubeVideoHandler->getYouTubeVideo($YouTubeVideoHandler->HowtoCheckHTTPHeadersfromBrowser);?>
<?php echo $YouTubeVideoHandler->getYouTubeVideo($YouTubeVideoHandler->WhatisHTTPStrictTransportSecurityHSTS);?>
<?php echo $YouTubeVideoHandler->getYouTubeVideo($YouTubeVideoHandler->WhatistheHSTSPreloadlist);?>
<?php echo $YouTubeVideoHandler->getYouTubeVideo($YouTubeVideoHandler->CookiesPart2HowSecureCookiesWork);?>
<?php echo $YouTubeVideoHandler->getYouTubeVideo($YouTubeVideoHandler->SSLScanPart1HowtotestHTTPS);?>
<?php echo $YouTubeVideoHandler->getYouTubeVideo($YouTubeVideoHandler->SSLScanPart2HowtoInterprettheResults);?>
<?php echo $YouTubeVideoHandler->getYouTubeVideo($YouTubeVideoHandler->HowtoInstallSSLScanonWindows);?>
<?php echo $YouTubeVideoHandler->getYouTubeVideo($YouTubeVideoHandler->WhatisCertificateTransparencyPart1);?>
<?php echo $YouTubeVideoHandler->getYouTubeVideo($YouTubeVideoHandler->WhatisCertificateTransparencyPart2ExpectCTHeader);?>
<br/><br/>

