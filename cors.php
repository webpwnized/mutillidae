<?php
	/* Command Injection
	 * Cross Site Scripting
	 * HTML Injection */

	try {
    	switch ($_SESSION["security-level"]){
    		case "0": // This code is insecure. No input validation is performed.
				$lEnableJavaScriptValidation = FALSE;
				$lEnableHTMLControls = FALSE;
    		break;

    		case "1": // This code is insecure. No input validation is performed.
				$lEnableJavaScriptValidation = TRUE;
				$lEnableHTMLControls = TRUE;
    		break;

	   		case "2":
	   		case "3":
	   		case "4":
    		case "5": // This code is fairly secure
				$lEnableHTMLControls = TRUE;
    			$lEnableJavaScriptValidation = TRUE;
    		break;
    	}// end switch
	}catch(Exception $e){
	    echo $CustomErrorHandler->FormatError($e, "Error setting up configuration on page cors.php");
	}// end try
?>

<div class="page-title">Cross-origin Resource Sharing (CORS)</div>

<?php include_once (__ROOT__.'/includes/back-button.inc');?>
<?php include_once (__ROOT__.'/includes/hints/hints-menu-wrapper.inc');?>

<!-- BEGIN HTML OUTPUT  -->
<script type="text/javascript">
	var onSubmitOfForm = function(theForm){

		lText = theForm.idMessageInput.value;

		<?php
		if($lEnableJavaScriptValidation){
			echo "var lOSCommandInjectionPattern = /[;&|<>]/;";
			echo "var lCrossSiteScriptingPattern = /[<>=()]/;";
		}else{
			echo "var lOSCommandInjectionPattern = /[]/;";
			echo "var lCrossSiteScriptingPattern = /[]/;";
		}// end if
		?>

		if(lText.search(lOSCommandInjectionPattern) > -1){
			alert("Malicious characters are not allowed.\n\nDo not listen to security people. Everyone knows if we just filter dangerous characters, injection is not possible.\n\nWe use JavaScript defenses combined with filtering technology.\n\nBoth are such great defenses that you are stopped in your tracks.");
			return false;
		}else if(lText.search(lCrossSiteScriptingPattern) > -1){
			alert("Characters used in cross-site scripting are not allowed.\n\nDon\'t listen to security people. Everyone knows if we just filter dangerous characters, injection is not possible.\n\nWe use JavaScript defenses combined with filtering technology.\n\nBoth are such great defenses that you are stopped in your tracks.");
			return false;
		}// end if

        var lXMLHTTP = new XMLHttpRequest();
		var lURL = "http://cors.mutillidae.local/webservices/rest/cors-server.php";
		var lAsynchronously = true;
		var lMessage = encodeURIComponent(lText);
		var lMethod = encodeURIComponent(theForm.idMethod.value);
		var lSendACAOHeader = theForm.idACAO.checked?"True":"False";
		var lSendACAMHeader = theForm.idACAM.checked?"True":"False";
		var lSendACMAHeader = theForm.idACMA.checked?"True":"False";
		var lQueryParameters = "message="+lMessage+"&method="+lMethod+"&acao="+lSendACAOHeader+"&acam="+lSendACAMHeader+"&acma="+lSendACMAHeader;

        lXMLHTTP.onreadystatechange = function() {
            if (this.readyState == 4) {
               // Typical action to be performed when the document is ready:
               document.getElementById("idMessageOutput").innerHTML = lXMLHTTP.responseText;
            }
        };

		switch(theForm.idMethod.value){
			case "GET":
                lXMLHTTP.open(lMethod, lURL+"?"+lQueryParameters, lAsynchronously);
                lXMLHTTP.send();
			break;
			case "POST":
			case "PUT":
			case "PATCH":
			case "DELETE":
                lXMLHTTP.open(lMethod, lURL, lAsynchronously);
				lXMLHTTP.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
                lXMLHTTP.send(lQueryParameters);
			break;
        };

	};// end JavaScript function onSubmitOfForm()
</script>

<a href="index.php?page=echo.php">
    <img src="images/malware-icon-75-75.png" />
    <span class="label">Switch to Cross-Site Scripting (XSS)</span>
</a>
<span class="buffer"></span>
<a href="index.php?page=content-security-policy.php">
    <img src="images/shield-icon-75-75.png" />
    <span class="label">Switch to Content Security Policy (CSP)</span>
</a>

<form>
    <table>
    	<tr><td></td></tr>
    	<tr>
    		<td colspan="2" class="form-header">Enter message to echo</td>
    	</tr>
    	<tr><td></td></tr>
    	<tr>
    		<td class="label">Message</td>
    		<td>
    			<input 	type="text" id="idMessageInput" name="message" size="20"
    					autofocus="autofocus"
    					onkeypress="if(event.keyCode==13){this.form.submit();}"
    					<?php if ($lEnableHTMLControls) {echo('minlength="1" maxlength="20" required="required"');} ?>
    			/>
    		</td>
    	</tr>
    	<tr>
    		<td class="label">HTTP Method</td>
    		<td>
    			<input type="radio" id="idMethod" name="method" value="GET" checked="checked" />
    			<label for="idMethod">GET</label><br>
    			<input type="radio" id="idMethod" name="method" value="POST" />
    			<label for="idMethod">POST</label><br>
    			<input type="radio" id="idMethod" name="method" value="PUT" />
    			<label for="idMethod">PUT</label><br>
    			<input type="radio" id="idMethod" name="method" value="PATCH" />
    			<label for="idMethod">PATCH</label><br>
    			<input type="radio" id="idMethod" name="method" value="DELETE" />
    			<label for="idMethod">DELETE</label><br>
    		</td>
    	</tr>
    	<tr>
    		<td class="label">Response Headers to Send</td>
    		<td>
    			<input type="checkbox" id="idACAO" name="acao" checked="checked" />
    			<label for="idACAO">Access-Control-Allow-Origin</label><br>
    			<input type="checkbox" id="idACAM" name="acam" checked="checked" />
    			<label for="idACAM">Access-Control-Allow-Methods</label><br>
    			<input type="checkbox" id="idACMA" name="acma" checked="checked" />
    			<label for="idACMA">Access-Control-Max-Age</label><br>
    		</td>
    	</tr>
    	<tr><td></td></tr>
    	<tr>
    		<td colspan="2" style="text-align:center;">
    			<input
        			onclick="onSubmitOfForm(this.form);"
        			onkeypress="if(event.keyCode==13){onSubmitOfForm(this.form);}"
        			name="echo-php-submit-button" class="button" type="button" value="Echo Message" />
    		</td>
    	</tr>
    	<tr><td></td></tr>
    </table>
</form>
<div id="idMessageOutput"></div>
