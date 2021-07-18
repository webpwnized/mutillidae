<div class="page-title">Privilege Escalation</div>

<?php include_once (__ROOT__.'/includes/back-button.inc');?>
<?php include_once (__ROOT__.'/includes/hints/hints-menu-wrapper.inc'); ?>

<table style="margin-left:auto; margin-right:auto; width: 600px;">
	<tr>
		<td class="form-header">Privilege Escalation</td>
	</tr>
	<tr><td>&nbsp;</td></tr>
	<tr><td class="label">Cookies</td></tr>
	<tr><td>&nbsp;</td></tr>
	<tr>
		<td>
			Some sites keep authentication and/or authorization tokens in the 
			user-agent (i.e. browser, phone, tablet). This gives the user (and XSS)
			large amounts of control over these tokens.
			<br/><br/>
			For privilege escalation via cookies, alter the cookie values and monitor the
			effect. Also, regsiter for two (or more) accounts, log into both, and note any 
			differences between the respective cookies. 
		</td>
	</tr>
	<tr><td>&nbsp;</td></tr>
	<tr><td class="label">SQL Injection</td></tr>
	<tr><td>&nbsp;</td></tr>
	<tr>
		<td>
			Login pages can be vulnerable to SQL injection such that a password
			or possibly a username is required to authenticate.
		</td>
	</tr>
	<tr><td>&nbsp;</td></tr>
	<tr><td class="label">Brute Force</td></tr>
	<tr><td>&nbsp;</td></tr>
	<tr>
		<td>
			THC Hydra (http://www.thc.org/thc-hydra) and Burp Suite can be used to guess usernames and passwords quickly.
			Both tools can attempt to log into sites and report the result.
		</td>
	</tr>
	<tr><td>&nbsp;</td></tr>
	<tr><td class="label">Secret Adminnistrative Pages</td></tr>
	<tr><td>&nbsp;</td></tr>
	<tr>
		<td>
			Built in pages can sometimes be accessed without a login or using 
			privilege escalation. These pages can grant administrative authority
			to create other admin accounts.
		</td>
	</tr>
	<tr><td>&nbsp;</td></tr>
</table>
