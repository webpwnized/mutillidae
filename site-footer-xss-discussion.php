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
	
		require_once 'classes/ClientInformationHandler.php';
		$lClientInformationHandler = new ClientInformationHandler();
		
		if ($lEncodeOutput){
			$lClientUserAgentString = $Encoder->encodeForHTML($lClientInformationHandler->getClientUserAgentString());
		}else{
			$lClientUserAgentString = $lClientInformationHandler->getClientUserAgentString();
		}// end if
	
    } catch (Exception $e) {
		echo $CustomErrorHandler->FormatError($e, $query);
    }// end try;
?>

<div class="page-title">Browser Version Site Footer</div>

<?php include_once (__ROOT__.'/includes/back-button.inc');?>
<?php include_once (__ROOT__.'/includes/hints/hints-menu-wrapper.inc'); ?>

<table>
	<tr>
		<td colspan="2" class="form-header">Browser Version</td>
	</tr>
	<tr><td>&nbsp;</td></tr>
	<tr><td>&nbsp;</td></tr>
	<tr>
		<td class="label">Browser Version: </td>
		<td><?php echo $lClientUserAgentString; ?></td>
	</tr>
	<tr><td>&nbsp;</td></tr>
	<tr><td>&nbsp;</td></tr>
	<tr>
		<td colspan="2" style="text-align:center;" class="label">
			Notice the browser version (shown above) being displayed in the site footer on every page. 
		</td>
	</tr>
	<tr>
		<td colspan="2" style="text-align:center;" class="label">
			What could possibly go wrong? 
		</td>
	</tr>
	<tr><td>&nbsp;</td></tr>
</table>