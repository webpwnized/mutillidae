<div class="page-title">SSL Misconfiguration</div>

<?php include_once (__ROOT__.'/includes/back-button.inc');?>
<?php include_once (__ROOT__.'/includes/hints/hints-menu-wrapper.inc'); ?>

<table style="margin-left:auto; margin-right:auto; width: 700px;">
	<tr>
		<td class="form-header">SSL Misconfiguration</td>
	</tr>
	<tr><td></td></tr>
	<tr>
		<td>
			Some web servers which require SSL to secure transmissions are misconfigured
			to allow users to browse over HTTP. The application may use redirection code
			to redirect users from HTTP to HTTPS. Mutillidae uses the following code in 
			index.php.
			<br/>
<code>
if($_SERVER['HTTPS']!="on"){
	$lSecureRedirect = "https://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
	header("Location: $lSecureRedirect");
	exit();
}//end if
</code>
			<br/>
			If a mallicious agent is able to set up a MITM connection in between the user browser
			and the web server, a program such as SSLStrip can detect the redirection from HTTP to 
			HTTPS and downgrade the users connection.
		</td>
	</tr>
	<tr>
		<td>
		Besides redirecting users from HTTP to HTTPS, other misconfigurations include 
		using weak ciphers or using vulnerable, unpatched software (i.e. Heartbleed). Part
		of testing web application security is testing for misconfigured HTTPS.
		</td>
	</tr>
	<tr>
		<td>Open "Hints and Videos" for more information</td>
	</tr>
	<tr><td>&nbsp;</td></tr>
</table>