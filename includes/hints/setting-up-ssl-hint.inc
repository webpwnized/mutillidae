<br/><br/>
<span class="report-header">Overview</span>
<br/><br/>
While not appropriate for production environments, self-signed certificates work well for providing HTTPS in
isolated lab environments; especially those running only locally
<br/><br/>
<a href="#videos" class="label"><img alt="YouTube" src="/images/youtube-play-icon-40-40.png" style="margin-right: 10px;" />Video Tutorials</a>
<br/><br/>
<span class="report-header">Creating a self-signed certificate for localhost</span>
<br/><br/>
<pre>
sudo openssl req -x509 -nodes -days 365 -newkey rsa:2048 -keyout /etc/ssl/private/mutillidae-selfsigned.key -out /etc/ssl/certs/mutillidae-selfsigned.crt

	Generating a 2048 bit RSA private key
	.................................................+++
	............+++
	writing new private key to '/etc/ssl/private/mutillidae-selfsigned.key'
	-----
	You are about to be asked to enter information that will be incorporated
	into your certificate request.
	What you are about to enter is what is called a Distinguished Name or a DN.
	There are quite a few fields but you can leave some blank
	For some fields there will be a default value,
	If you enter '.', the field will be left blank.
	-----
	Country Name (2 letter code) [AU]:US
	State or Province Name (full name) [Some-State]:KY
	Locality Name (eg, city) []:Derby City
	Organization Name (eg, company) [Internet Widgits Pty Ltd]:Mutillidae Inc
	Organizational Unit Name (eg, section) []:
	Common Name (e.g. server FQDN or YOUR name) []:localhost
	Email Address []:
</pre>

<div>&nbsp;</div>
<span class="report-header">Backup the default Apache SSL configuration file</span>
<div>&nbsp;</div>
<pre>
sudo cp /etc/apache2/sites-available/default-ssl.conf /etc/apache2/sites-available/default-ssl.conf.bak
</pre>

<div>&nbsp;</div>
<span class="report-header">Edit the default Apache SSL configuration file to reference the newly created certificate files</span>
<div>&nbsp;</div>
<pre>
sudo nano /etc/apache2/sites-available/default-ssl.conf

    #   A self-signed (snakeoil) certificate can be created by installing
    #   the ssl-cert package. See
    #   /usr/share/doc/apache2/README.Debian.gz for more info.
    #   If both key and certificate are stored in the same file, only the
    #   SSLCertificateFile directive is needed.
    SSLCertificateFile      /etc/ssl/certs/mutillidae-selfsigned.crt
    SSLCertificateKeyFile /etc/ssl/private/mutillidae-selfsigned.key
</pre>

<div>&nbsp;</div>
<span class="report-header">Change the owner of the certificate files to Apache (username: www-data)</span>
<div>&nbsp;</div>
<pre>
sudo chown www-data:www-data /etc/ssl/certs/mutillidae-selfsigned.crt
sudo chown www-data:www-data /etc/ssl/private/mutillidae-selfsigned.key
</pre>

<div>&nbsp;</div>
<span class="report-header">Enable the ssl and headers modules in Apache</span>
<div>&nbsp;</div>
<pre>
sudo a2enmod ssl
sudo a2enmod headers
</pre>

<div>&nbsp;</div>
<span class="report-header">Enable the SSL site option in Apache</span>
<div>&nbsp;</div>
<pre>
sudo a2ensite default-ssl
</pre>

<div>&nbsp;</div>
<span class="report-header">Restart the Apache service</span>
<div>&nbsp;</div>
<pre>
sudo service apache2 restart
</pre>

<div>&nbsp;</div>
<span class="report-header">Test the site by browsing to the homepage over HTTPS</span>
<div>&nbsp;</div>
<pre>
Note: Because the certficate is self-signed, it is not trusted. Firefox will show an
"Untrusted Certificate" warning as result
</pre>
<br/>
<span id="videos" class="report-header">Videos</span>
<br/>
<?php echo $YouTubeVideoHandler->getYouTubeVideo($YouTubeVideoHandler->HowtoCreateSelfSignedCertificateinApache);?>
<?php echo $YouTubeVideoHandler->getYouTubeVideo($YouTubeVideoHandler->WhatisCertificateTransparencyPart1);?>
<?php echo $YouTubeVideoHandler->getYouTubeVideo($YouTubeVideoHandler->WhatisCertificateTransparencyPart2ExpectCTHeader);?>
<br/><br/>
