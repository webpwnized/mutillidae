<br/><br/>
<span class="report-header">Setting up LDAP server for Mutillidae</span>
<br/><br/>
Install Stand-Alone LDAP Daemon (slapd). When asked,
set the admin password to "mutillidae" (without the quotes). If these settings are not desired, ensure
whatever settings are chosen match those in includes/ldap-config.inc
<br/>
<code>
	sudo apt install slapd ldap-utils
</code>
<br/>
Check that the server is running. The status should be "active (running)".
<br/>
<code>
	systemctl status slapd
</code>
<br/>
Configure the LDAP server with dpkg-reconfigure
<br/>
<code>
	sudo dpkg-reconfigure slapd
</code>
<br/>
Enter the following values
<br/><br/>
Omit LDAP server configuration: <span class="label">NO</span><br/>
DNS domain name: <span class="label">mutillidae.local</span><br/>
Organization name: <span class="label">mutillidae</span><br/>
Administrator password: <span class="label">mutillidae</span><br/>
Database backend: <span class="label">MDB</span><br/>
Do you want the database to be removed when slapd is purged? <span class="label">No</span><br/>
Move old database? <span class="label">Yes</span><br/>
Allow LDAPv2 protocol? <span class="label">No</span> (Note: Not all packages ask this question)
<br/><br/>
Install php-ldap. Determine version of PHP with
<br/>
<code>
	php -v
</code>
<br/>
Install the version of php-ldap that matches the version of PHP. For example
<br/>
<code>
	sudo apt-get install php-ldap
	sudo service apache2 restart
</code>
<br/>
Import the directory used by Mutillidae into the local the LDAP directory.
LDIF files are copies of an LDAP directory (database).
LDIF files can be imported with tools like Apache Directory Studio. A copy of the
directory used by Mutillidae is available in file
<br/>
<code>
	ldapadd -c -x -D "cn=admin,dc=mutillidae,dc=local" -w mutillidae -H ldap:// -f /var/www/mutillidae/configuration/openldap/mutillidae.ldif
</code>
<br/><br/>