<?php 

	try{
		switch ($_SESSION["security-level"]){
	   		case "0": // This code is insecure
	   		case "1": // This code is insecure
	   			// DO NOTHING: This is insecure		
				$lEncodeOutput = FALSE;
				$luseSafeJavaScript = "false";
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
				$luseSafeJavaScript = "true";
	   		break;
	   	}// end switch		
	
		require_once (__ROOT__.'/classes/ClientInformationHandler.php');
		$lClientInformationHandler = new ClientInformationHandler();

		if ($lEncodeOutput){
			$lWhoIsInformation = $Encoder->encodeForHTML($lClientInformationHandler->whoIsClient());
			$lOperatingSystem = $Encoder->encodeForHTML($lClientInformationHandler->getOperatingSystem());
			$lBrowser = $Encoder->encodeForHTML($lClientInformationHandler->getBrowser());
			$lClientHostname = $Encoder->encodeForHTML($lClientInformationHandler->getClientHostname());
			$lClientIP = $Encoder->encodeForHTML($lClientInformationHandler->getClientIP());
			$lClientUserAgentString = $Encoder->encodeForHTML($lClientInformationHandler->getClientUserAgentString());
			$lClientReferrer = $Encoder->encodeForHTML($lClientInformationHandler->getClientReferrer());
			$lClientPort = $Encoder->encodeForHTML($lClientInformationHandler->getClientPort());
		}else{
			$lWhoIsInformation = $lClientInformationHandler->whoIsClient();
			$lOperatingSystem = $lClientInformationHandler->getOperatingSystem();
			$lBrowser = $lClientInformationHandler->getBrowser();
			$lClientHostname = $lClientInformationHandler->getClientHostname();
			$lClientIP = $lClientInformationHandler->getClientIP();
			$lClientUserAgentString = $lClientInformationHandler->getClientUserAgentString();
			$lClientReferrer = $lClientInformationHandler->getClientReferrer();
			$lClientPort = $lClientInformationHandler->getClientPort();
		}// end if
	
    } catch (Exception $e) {
		echo $CustomErrorHandler->FormatError($e, "Error collecting browser information");
    }// end try;
?>

<div class="page-title">Browser Information</div>

<?php include_once (__ROOT__.'/includes/back-button.inc');?>
<?php include_once (__ROOT__.'/includes/hints/hints-menu-wrapper.inc'); ?>

<table style="width:75%;" class="results-table">
	<tr class="report-header"><td colspan="3">Info obtained by PHP</td></tr>
	<tr><th class="report-label">Client IP</th><td class="report-data"><?php echo $lClientIP; ?></td></tr>
    <tr><th class="report-label">Client Hostname</th><td class="report-data"><?php echo $lClientHostname; ?></td></tr>
    <tr><th class="report-label">Operating System</th><td class="report-data"><?php echo $lOperatingSystem ?></td></tr>
    <tr><th class="report-label">User Agent String</th><td class="report-data"><?php echo $lClientUserAgentString; ?></td></tr>
    <tr><th class="report-label">Referrer</th><td class="report-data"><?php echo $lClientReferrer; ?></td></tr>
    <tr><th class="report-label">Remote Client Port</th><td class="report-data"><?php echo $lClientPort; ?></td></tr>
    <tr><th class="report-label">WhoIs info for client IP</th><td class="report-data"><pre><?php echo $lWhoIsInformation; ?></pre></td></tr>
	<?php 
	if ($lEncodeOutput){	
		foreach ($_COOKIE as $key => $value){
	    	echo '<tr><th class="report-label">Cookie '.$Encoder->encodeForHTML($key).'</th><td class="report-data">'.$Encoder->encodeForHTML($value).'</pre></td></tr>';
		}// end foreach
	}else{
		foreach ($_COOKIE as $key => $value){
	    	echo '<tr><th class="report-label" class="non-wrapping-label">Cookie '.$key.'</th><td class="report-data">'.$value.'</pre></td></tr>';
		}// end foreach
	}// end if
	?>    
</table>
<div>&nbsp;</div><div>&nbsp;</div>
<table style="width:75%;" class="results-table">
    <tr class="report-header"><td colspan="3">Info obtained by JavaScript</td></tr>
	<tr>
		<th class="report-label">Browser Name</th>
		<td class="report-data" id="id_browser_td"></td>
	</tr>
	<tr>
		<th class="report-label">Browser Codename</th>
		<td class="report-data" id="id_browser_codename_td"></td>
	</tr>
	<tr>
		<th class="report-label">Browser Version</th>
		<td class="report-data" id="id_browser_version_td"></td>
	</tr>
	<tr>
		<th class="report-label">Cookie Enabled?</th>
		<td class="report-data" id="id_cookie_enabled_td"></td>
	</tr>
	<tr>
		<th class="report-label">Platform</th>
		<td class="report-data" id="id_platform_td"></td>
	</tr>
	<tr>
		<th class="report-label">User Agent</th>
		<td class="report-data" id="id_user_agent_td"></td>
	</tr>
	<tr>
		<th class="report-label">CPU Class</th>
		<td class="report-data" id="id_java_enabled_td"></td>
	</tr>
	<tr>
		<th class="report-label">System Language</th>
		<td class="report-data" id="id_system_language_enabled_td"></td>
	</tr>
	<tr>
		<th class="report-label">Resolution</th>
		<td class="report-data" id="id_resolution_enabled_td"></td>
	</tr>
	<tr>
		<th class="report-label">Color Depth</th>
		<td class="report-data" id="id_color_depth_enabled_td"></td>
	</tr>
	<tr>
		<th class="report-label">Referrer</th>
		<td class="report-data" id="id_referrer_td"></td>
	</tr>
	<tr>
		<th class="report-label">Plug-Ins</th>
		<td class="report-data" id="id_plug_ins_td"></td>
	</tr>
</table>

<script type="text/javascript">

	var g_beSmart = <?php echo $luseSafeJavaScript; ?>;
	var g_usingIE = ('all' in document);

	var outputValue = function(p_elementId, p_elementValue, p_beSmart, p_usingIE){
		if(p_beSmart){
			//safe
			if(p_usingIE){
				document.getElementById(p_elementId).innerText = p_elementValue;
			}else{
				document.getElementById(p_elementId).textContent = p_elementValue;
			}// end if
		}else{
			// unsafe and low-skill - should be using DOM interface
			document.getElementById(p_elementId).innerHTML = p_elementValue;
		}//end if
	}// end function

	outputValue("id_browser_td", navigator.appName, g_beSmart, g_usingIE);
	outputValue("id_browser_codename_td", navigator.appCodeName, g_beSmart, g_usingIE);
	outputValue("id_browser_version_td", navigator.appVersion, g_beSmart, g_usingIE);
	outputValue("id_cookie_enabled_td", navigator.cookieEnabled, g_beSmart, g_usingIE);
	outputValue("id_platform_td", navigator.platform, g_beSmart, g_usingIE);
	outputValue("id_user_agent_td", navigator.userAgent, g_beSmart, g_usingIE);
	outputValue("id_cpu_class_td", navigator.cpuClass, g_beSmart, g_usingIE);
	outputValue("id_java_enabled_td", navigator.javaEnabled, g_beSmart, g_usingIE);
	outputValue("id_system_language_enabled_td", navigator.systemLanguage, g_beSmart, g_usingIE);
	outputValue("id_resolution_enabled_td", screen.width+"x"+screen.height, g_beSmart, g_usingIE);
	outputValue("id_color_depth_enabled_td", screen.colorDepth, g_beSmart, g_usingIE);
	outputValue("id_referrer_td", document.referrer, g_beSmart, g_usingIE);

	if (navigator.appName=="Netscape"){
		for (i in navigator.plugins){
			l_plugins =+ navigator.plugins[i].name.toString() + ';';
		}// end for
		outputValue("id_plug_ins_td", l_plugins, g_beSmart, g_usingIE);
	}// end if
</script>