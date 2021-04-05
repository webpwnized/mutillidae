<br/><br/>
<span class="report-header">Overview</span>
<br/><br/>
Without a properly configured HTTPS connection from the web server
to the client, traffic traveling over the HTTP protocol can be
interecepted with packet sniffers.
<br/><br/>
<a href="#videos" class="label"><img alt="YouTube" src="/images/youtube-play-icon-40-40.png" style="margin-right: 10px;" />Video Tutorials</a>
<br/><br/>
<span class="report-header">Discovery Methodology</span>
<br/><br/>
Use the application noting if pages are available over HTTP. Also, check
if the application issues HTTP Strict Transport Security (HSTS) headers
in the HTTP responses.
<br/><br/>
<span class="report-header">Exploitation</span>
<br/><br/>
While on the same subnet as either the client or the server, use a packet sniffer to
capture the HTTP packets while a user authenticates or performs other
sensitive transactions.
<br/><br/>
<span class="report-header">Example</span>
<br/><br/>
Proceed to the login page in Mutillidae (<a href="index.php?page=login.php">Login</a>).
Start a packet sniffer to capture packets. Be careful to start the sniffer on a
network inteface that is listening to the same subnet as Mutillidae.
<br/><br/>
Ping or other programs can be used  to confirm connectivity with the web
server and confirm the subnet being used to connect.
<br/>
<code>
	root@ubuntu:~# ping mutillidae.local
	PING mutillidae.local (127.0.0.1) 56(84) bytes of data
	64 bytes from localhost (127.0.0.1): icmp_seq=1 ttl=64 time=0.017 ms
</code>
			<br/>
			The ifconfig program shows the names of the network interfaces. Notice the interface that
			provides the route to mutillidae.local (127.0.0.1) is the "lo" interface.
			<br/>
<code>
	root@ubuntu:~# ifconfig
	eth0      Link encap:Ethernet  HWaddr 00:0c:29:f0:34:2a
	inet addr:10.0.0.133  Bcast:10.0.0.255  Mask:255.255.255.0
	...SNIP...

	eth1      Link encap:Ethernet  HWaddr 00:0c:29:f0:34:34
	inet addr:172.16.0.248  Bcast:172.16.0.255  Mask:255.255.255.0
	...SNIP...

	lo        Link encap:Local Loopback
	inet addr:127.0.0.1  Mask:255.0.0.0
	...SNIP...
</code>
			<br/>
			Wireshark can capture packets, but using a dedicated packet capture
			tool like tcpdump or tshark can give the advanage of low overhead.
			<br/>
<code>
	root@ubuntu:~# tcpdump -i lo -nn -v -w /tmp/login-packets.pcap
	tcpdump: listening on lo, link-type EN10MB (Ethernet), capture size 65535 bytes
</code>
			<br/>
			Later the packet capture can be opened in Wireshark to take advantage
			of the graphical view and advanced filtering capabilities.
			<br/>
<code>
	root@ubuntu:~# wireshark /tmp/login-packets.pcap &amp;
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
