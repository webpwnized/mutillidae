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
    } catch (Exception $e) {
		echo $CustomErrorHandler->FormatError($e, "Error collecting browser information");
    }// end try;
?>

<div class="page-title">User-Agent Impersonation</div>

<?php include_once (__ROOT__.'/includes/back-button.inc');?>
<?php include_once (__ROOT__.'/includes/hints/hints-menu-wrapper.inc'); ?>

<fieldset>
	<legend>Browser Fingerprint</legend>
	<div>&nbsp;</div>
	<table style="width:80%;" class="results-table">
		<tr><td colspan="3" id="id_result"></td></tr>
	   	<tr><td colspan="3">&nbsp;</td></tr>
		<tr><th class="report-label">User Agent</th><td class="report-data" id="id_user_agent_td"></td></tr>
		<tr><th class="report-label">App Code Name</th><td class="report-data" id="id_browser_codename_td"></td></tr>
		<tr><th class="report-label">App Name</th><td class="report-data" id="id_browser_td"></td></tr>
		<tr><th class="report-label">Browser Version</th><td class="report-data" id="id_browser_version_td"></td></tr>
		<tr><th class="report-label">Platform</th><td class="report-data" id="id_platform_td"></td></tr>
		<tr><th class="report-label">Vendor</th><td class="report-data" id="id_browser_vendor_td"></td></tr>
		<tr><th class="report-label">Vendor Sub</th><td class="report-data" id="id_browser_vendor_sub_td"></td></tr>
		<tr><th class="report-label">Build ID</th><td class="report-data" id="id_build_id_td"></td></tr>
		<tr><th class="report-label">O/S CPU</th><td class="report-data" id="id_oscpu_td"></td></tr>
		<tr><th class="report-label">Product</th><td class="report-data" id="id_product_td"></td></tr>
		<tr><th class="report-label">Product Sub</th><td class="report-data" id="id_product_sub_td"></td></tr>
	</table>
</fieldset>

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
	};// end function

	// These can be changed with a tool like User-Agent Switcher or via Firefox about:config
	outputValue("id_platform_td", window.navigator.platform, g_beSmart, g_usingIE);
	outputValue("id_browser_vendor_td", window.navigator.vendor, g_beSmart, g_usingIE);
	outputValue("id_browser_vendor_sub_td", window.navigator.vendorSub, g_beSmart, g_usingIE);
	outputValue("id_browser_td", window.navigator.appName, g_beSmart, g_usingIE);
	outputValue("id_browser_codename_td", window.navigator.appCodeName, g_beSmart, g_usingIE);
	outputValue("id_browser_version_td", window.navigator.appVersion, g_beSmart, g_usingIE);
	outputValue("id_user_agent_td", window.navigator.userAgent, g_beSmart, g_usingIE);

	// These can only be changed in browser config via Firefox about:config.
	outputValue("id_build_id_td", window.navigator.buildID, g_beSmart, g_usingIE);
	outputValue("id_oscpu_td", window.navigator.oscpu, g_beSmart, g_usingIE);
	outputValue("id_product_td", window.navigator.product, g_beSmart, g_usingIE);
	outputValue("id_product_sub_td", window.navigator.productSub, g_beSmart, g_usingIE);

	var lResultTD = document.getElementById("id_result");
	if(
			window.navigator.platform === "iPad" && 
			window.navigator.appName === "Netscape" &&
			window.navigator.appCodeName === "Mozilla" &&
			window.navigator.appVersion.indexOf("iPad") !== -1 &&
			window.navigator.appVersion.indexOf("Mac") !== -1 &&
			window.navigator.appVersion.indexOf("AppleWebKit") !== -1 &&
			window.navigator.appVersion.indexOf("Safari") !== -1 &&
			window.navigator.product === "Gecko" &&
			window.navigator.productSub === "20030107"
	){
		lResultTD.innerHTML = "Congratulations. You look like an iPad using Safari Browser to me.";
		lResultTD.className = "success-header";
	}else{
		lResultTD.innerHTML = "Sorry. You do not look like an Apple iPad using Safari Browser.<br />This page uses JavaScript browser and O/S fingerprinting to decide if the user-agent is allowed.";
		lResultTD.className = "error-header";
	}// end if
		
	/* 
		Log the browser fingerprint using the capture data page so 
		we can copy and
		paste the results to the user-agent switcher easier
	*/
	try{ 
		var lXMLHTTP;	
		var lURL = "/mutillidae/capture-data.php";
		var lRequestMethod = "GET";
		var lAsyncronousRequestFlag = true;
		var lBrowserFingerprint = " Platform:" + window.navigator.platform + 
		" Vendor:" + window.navigator.vendor +
		" VendorSub:" + window.navigator.vendorSub +
		" AppName:" + window.navigator.appName +
		" CodeName:" + window.navigator.appCodeName +
		" Version:" + window.navigator.appVersion +
		" User Agent:" + window.navigator.userAgent;

		lXMLHTTP = new XMLHttpRequest();
		lXMLHTTP.onreadystatechange=function(){}; //end function
		lXMLHTTP.open(lRequestMethod, lURL+"?"+encodeURI("Browser Fingerprint:" + lBrowserFingerprint), lAsyncronousRequestFlag);
		lXMLHTTP.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		lXMLHTTP.send(); 
	}catch(e){
		alert("Error trying execute AJAX call: " + e.message);
	}//end try
	
</script>