<?php 
	try{
		switch ($_SESSION["security-level"]){
	   		case "0": // This code is insecure
	   		case "1": // This code is insecure
	   			// DO NOTHING: This is insecure		
				$lEncodeOutput = FALSE;
			break;
		    		
			case "2":
			case "3":
			case "4":
	   		case "5": // This code is fairly secure
	  			/* 
	  			 * NOTE: Input validation is excellent but not enough. The output must be
	  			 * encoded per context. For example, if output is placed	 in HTML,
	  			 * then HTML encode it. Blacklisting is a losing proposition. You 
	  			 * cannot blacklist everything. The business requirements will usually
	  			 * require allowing dangerous charaters. In the example here, we can 
	  			 * validate username but we have to allow special characters in passwords
	  			 * least we force weak passwords. We cannot validate the signature hardly 
	  			 * at all. The business requirements for text fields will demand most
	  			 * characters. Output encoding is the answer. Validate what you can, encode it
	  			 * all.
	  			 * 
	  			 * For JavaScript, always output using innerText (IE) or textContent (FF),
	  			 * Do NOT use innerHTML. Using innerHTML is weak anyway. When 
	  			 * attempting DHTML, program with the proper interface which is
	  			 * the DOM. Thats what it is there for.
	  			 */
	   			// encode the output following OWASP standards
	   			// this will be HTML encoding because we are outputting data into HTML
				$lEncodeOutput = TRUE;
	   		break;
	   	}// end switch		
	
		if ($lEncodeOutput){
			$lPage = $Encoder->encodeForHTML($_GET['page']);
		}else{
			$lPage = $_REQUEST['page'];
		}// end if
	
    } catch (Exception $e) {
		echo $CustomErrorHandler->FormatError($e, "Error attempting to set up page configuration");
    }// end try;
?>

<div class="page-title">Arbitrary File Inclusion</div>

<?php include_once (__ROOT__.'/includes/back-button.inc');?>
<?php include_once (__ROOT__.'/includes/hints/hints-menu-wrapper.inc'); ?>

<table style="margin-left:auto; margin-right:auto;width:600px;">
	<tr>
		<td class="form-header">Remote and Local File Inclusion</td>
	</tr>
	<tr><td>&nbsp;</td></tr>
	<tr style="text-align: left;">
		<td class="label">Current Page: <?php echo $lPage; ?></td>
	</tr>
	<tr>
		<td LocalFileInclusionVulnerability="1" class="label">
			Notice that the page displayed by Mutillidae is decided 
			by the value in the "page" URL parameter. What could possibly go wrong? 
		</td>
	</tr>
	<tr><td>&nbsp;</td></tr>
	<tr>
		<td>
			<div class="report-header">Local File Inclusion</div>
			<br/>
			PHP runs on an account (like any other user). The account has privileges
			to the local file system with the ability to read, write, and/or execute files.
			Ideally the account would only have enough privileges to execute php files
			in a certain, intended directory but sadly this is often not the case.
			Local File Inclusion occurs when a file to which the PHP account has 
			access is passed as a parameter to the PHP function "include", "include_once",
			"require", or "require_once". PHP incorporates the content into the page. If
			the content happens to be PHP source code, PHP executes the file.
		</td>
	</tr>
	<tr><td>&nbsp;</td></tr>
	<tr>
		<td>
			<div class="report-header">Remote File Inclusion</div>
			<br/>
			Remote File Inclusion occurs when the URI of a file located on a different 
			server is passed to  as a parameter to the PHP function "include", "include_once",
			"require", or "require_once". PHP incorporates the content into the page. If
			the content happens to be PHP source code, PHP executes the file.
			<br/><br/>
			<div class="informative-message">Note that on newer PHP servers the configuration parameters "allow_url_fopen"
			and "allow_url_include" must be set to "On".</div>
			<br/>
			By default, these may or may not 
			be "On" in newer versions. For example, by default in XAMPP 1.8.1, 
			"allow_url_fopen = On" by default but "allow_url_include = Off" by default. If you
			wish to try remote file inclusion, be sure these configuration parameters are set
			to "On" by going to the php.ini file, locating the parameters, setting their value to 
			"On", and restarting the Apache service. An example of this configuration appears below.
			<br/><br/>
			<span class="important-code">
			;;;;;;;;;;;;;;;;;;<br/>
			; Fopen wrappers ;<br/>
			;;;;;;;;;;;;;;;;;;<br/>
			<br/>
			; Whether to allow the treatment of URLs (like http:// or ftp://) as files.<br/>
			; http://php.net/allow-url-fopen<br/>
			allow_url_fopen = On<br/>
			<br/>
			; Whether to allow include/require to open URLs (like http:// or ftp://) as files.<br/>
			; http://php.net/allow-url-include<br/>
			allow_url_include = On<br/>
			</span>
		</td>
	</tr>
	<tr><td>&nbsp;</td></tr>
</table>