<br/><br/>
<span class="report-header">Overview</span>
<br/><br/>
Virtual hosts allow us to run more than one web site on the same IP address. While not appropriate for 
production environments, name-based vulnerable hosts work well for providing production-like sites in 
isolated lab environments; especially those running only locally.
<br/><br/>
<a href="#videos" class="label"><img alt="YouTube" src="/images/youtube-play-icon-40-40.png" style="margin-right: 10px;" />Video Tutorials</a>
<br/><br/>
<span class="report-header">Creating Virtual Hosts</span>
<br/><br/>
<pre>
Virtual hosts can be used to allow both HTTP and HTTPS sites for the same application. 
An example of a possible configuration is below. This example limits mutillidae to running 
on localhost only at IP address 127.0.0.1 on standard ports 80 and 443 respectively.

&#x3C;VirtualHost 127.0.0.1:80&#x3E;
&#x9;ServerName mutillidae.local
&#x9;DocumentRoot /var/www/html/mutillidae

&#x9;ErrorLog ${APACHE_LOG_DIR}/mutillidae-error.log
&#x9;CustomLog ${APACHE_LOG_DIR}/mutillidae-access.log combined
&#x3C;/VirtualHost&#x3E;

&#x3C;VirtualHost 127.0.0.1:443&#x3E;
&#x9;DocumentRoot /var/www/html/mutillidae
&#x9;ServerName mutillidae.local

&#x9;ErrorLog ${APACHE_LOG_DIR}/mutillidae-error.log
&#x9;CustomLog ${APACHE_LOG_DIR}/mutillidae-access.log combined

&#x9;SSLEngine On
&#x9;SSLOptions +StrictRequire
&#x9;SSLCertificateFile /etc/ssl/certs/mutillidae-selfsigned.crt
&#x9;SSLCertificateKeyFile /etc/ssl/private/mutillidae-selfsigned.key
&#x9;SSLProtocol TLSv1
&#x3C;/VirtualHost&#x3E;
</pre>

<div>&nbsp;</div>
<span class="report-header">Creating Multiple "Named" Virtual Hosts</span>
<div>&nbsp;</div>
<pre>
Apache allows more than one website to run on a single IP address. This pattern
should not be used for production sites, but is convenient for lab
environments. In this example, we enable the default web site located at
/var/www/html and enable mutillidae at /var/www/html/mutillidae. Both sites
are running on IP address 127.0.0.1. However, the default site will respond to
http://localhost while Mutillidae will respond to http://mutillidae or 
http://mutillidae.local. In the case of a tie, "first match wins". For example,
http://127.0.0.1 will load the default site because there is no hostname on
which to match and the default site is listed first.

	# Localhost

    &lt;VirtualHost 127.0.0.1:80&gt;
        ServerName localhost
        DocumentRoot /var/www/html
    
    	ErrorLog ${APACHE_LOG_DIR}/localhost-error.log
    	CustomLog ${APACHE_LOG_DIR}/localhost-access.log combined
    &lt;/VirtualHost&gt;
    
    &lt;VirtualHost 127.0.0.1:443&gt;
        ServerName localhost
        DocumentRoot /var/www/html
    
    	ErrorLog ${APACHE_LOG_DIR}/localhost-error.log
    	CustomLog ${APACHE_LOG_DIR}/localhost-access.log combined
    
        SSLEngine On
        SSLOptions +StrictRequire
        SSLCertificateFile /etc/ssl/certs/mutillidae-selfsigned.crt
        SSLCertificateKeyFile /etc/ssl/private/mutillidae-selfsigned.key
        SSLProtocol -all +TLSv1.2
    &lt;/VirtualHost&gt;
    
    # Mutillidae
    
    &lt;VirtualHost 127.0.0.1:80&gt;
        ServerName mutillidae.local
    	ServerAlias mutillidae
        DocumentRoot /var/www/html/mutillidae
    
    	ErrorLog ${APACHE_LOG_DIR}/mutillidae-error.log
    	CustomLog ${APACHE_LOG_DIR}/mutillidae-access.log combined
    &lt;/VirtualHost&gt;
    
    &lt;VirtualHost 127.0.0.1:443&gt;
        ServerName mutillidae.local
    	ServerAlias mutillidae
        DocumentRoot /var/www/html/mutillidae
    
    	ErrorLog ${APACHE_LOG_DIR}/mutillidae-error.log
    	CustomLog ${APACHE_LOG_DIR}/mutillidae-access.log combined
    
        SSLEngine On
        SSLOptions +StrictRequire
        SSLCertificateFile /etc/ssl/certs/mutillidae-selfsigned.crt
        SSLCertificateKeyFile /etc/ssl/private/mutillidae-selfsigned.key
        SSLProtocol -all +TLSv1 +TLSv1.1 +TLSv1.2
    &lt;/VirtualHost&gt;

"Named Hosts" requires, well, the hosts be named. Otherwise, there is no
name on which Apache can match. To add localhost names for IP address
127.0.0.1, we can use the following.

    echo -e "\n127.0.0.1\tmutillidae.local" &gt;&gt; /etc/hosts
    echo -e "\n127.0.0.1\tmutillidae" &gt;&gt; /etc/hosts

The resulting /etc/hosts file might look something like this

    127.0.0.1 localhost
    
    # The following lines are desirable for IPv6 capable hosts
    ::1 ip6-localhost ip6-loopback
    fe00::0 ip6-localnet
    ff00::0 ip6-mcastprefix
    ff02::1 ip6-allnodes
    ff02::2 ip6-allrouters
    ff02::3 ip6-allhosts
    
    127.0.0.1	mutillidae.local
    127.0.0.1	mutillidae
</pre>
<br/>
<span id="videos" class="report-header">Videos</span>
<br/>
<?php echo $YouTubeVideoHandler->getYouTubeVideo($YouTubeVideoHandler->HowtoCreateVirtualHostsinApache);?>
<br/><br/>
