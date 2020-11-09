<?php 
	try {	    	
    	switch ($_SESSION["security-level"]){
    		case "0": // This code is insecure.
    			$lUseSafeJSONParser = FALSE;
    			$lUseJavaScriptValidation = FALSE;
    			$lUseServerSideValidation = FALSE;
   				$lEncodeOutput = FALSE;
				$lProtectAgainstMethodTampering = FALSE;	
			break;

    		case "1": // This code is insecure.
    			$lUseSafeJSONParser = FALSE;
    			$lUseJavaScriptValidation = TRUE;
    			$lUseServerSideValidation = FALSE;
				$lEncodeOutput = FALSE;
				$lProtectAgainstMethodTampering = FALSE;    		
			break;

	   		case "2":
	   		case "3":
	   		case "4":
    		case "5": // This code is fairly secure
    			$lUseSafeJSONParser = TRUE;
    			$lUseJavaScriptValidation = TRUE;
    			$lUseServerSideValidation = TRUE;
    			$lProtectAgainstMethodTampering = TRUE;
	  			/* 
	  			 * NOTE: Input validation is excellent but not enough. The output must be
	  			 * encoded per context. For example, if output is placed in HTML,
	  			 * then HTML encode it. Blacklisting is a losing proposition. You 
	  			 * cannot blacklist everything. The business requirements will usually
	  			 * require allowing dangerous charaters. In the example here, we can 
	  			 * validate username but we have to allow special characters in passwords
	  			 * least we force weak passwords. We cannot validate the signature hardly 
	  			 * at all. The business requirements for text fields will demand most
	  			 * characters. Output encoding is the answer. Validate what you can, encode it
	  			 * all.
	  			 */
	   			// encode the output following OWASP standards
	   			// this will be HTML encoding because we are outputting data into HTML
				$lEncodeOutput = TRUE;
    		break;
    	}// end switch
	}catch(Exception $e){
		echo $CustomErrorHandler->FormatError($e, "Error setting up configuration on page pentest-lookup-tool.php");
	}// end try	

	/* ----------------------------------------------------------
	 * Get the tools to populate the drop down box
	 * Create a list of options for the select box (dropdown box) 
	 * ---------------------------------------------------------- */
	try{
		$qPenTestToolOptions = $SQLQueryHandler->getPenTestTools();

		$lPenTestToolsOptions = "";
		
		while($result = $qPenTestToolOptions->fetch_object()){

			if(!$lEncodeOutput){
				$lToolID = $result->tool_id;
				$lToolName = $result->tool_name;
			}else{
				$lToolID = $Encoder->encodeForHTML($result->tool_id);
				$lToolName = $Encoder->encodeForHTML($result->tool_name);
			}// end if

		    //echo '<option value="' . $lToolID . '">' . $lToolName . '</option>' . PHP_EOL;
		    $lPenTestToolsOptions .= '<option value="' . $lToolID . '">' . $lToolName . '</option>' . PHP_EOL;
		}// end while 
		
	} catch (Exception $e) {
		echo $CustomErrorHandler->FormatError($e, $lQueryString);
	}// end try

	//--------------------------------------------------------
	//If the user selected a tool, get the data for that tool
	//--------------------------------------------------------
	
	if (isset($_POST["pen-test-tool-lookup-php-submit-button"]) || isset($_REQUEST["pen-test-tool-lookup-php-submit-button"])){
		
		try {
			if ($lProtectAgainstMethodTampering) {
				$lPostedButton = $_POST["pen-test-tool-lookup-php-submit-button"];
				$lPostedToolID = $_POST["ToolID"];
			}else{
				$lPostedButton = $_REQUEST["pen-test-tool-lookup-php-submit-button"];
				$lPostedToolID = $_REQUEST["ToolID"];
			}//end if
	
			if(!empty($lPostedButton)){
				
				$lErrorNoChoiceMade = FALSE;
				
				if ($lPostedToolID == "0923ac83-8b50-4eda-ad81-f1aac6168c5c" || strlen($lPostedToolID) == 0){
					$lErrorNoChoiceMade = TRUE;
				}else{
					$lErrorNoChoiceMade = FALSE;

					$qPenTestToolResults = $SQLQueryHandler->getPenTestTool($lPostedToolID);
					$lPenTestToolsDetails = "";
					/* We want to allow single quotes so the user can do SQL injection, but when they return from 
					 * the database, we escape the single quotes because they would otherwise break the JSON string. */
					$lPenTestToolsJSON = 
						'{"query": {"toolIDRequested": "'.str_replace("'", "\'", $lPostedToolID).'", "penTestTools": [';
					if($qPenTestToolResults->num_rows > 0){
						while($row = $qPenTestToolResults->fetch_object()){
							$lPenTestToolsDetails .= json_encode($row) . ",";
						}// end while
						$lPenTestToolsJSON .= substr($lPenTestToolsDetails, 0, strlen($lPenTestToolsDetails)-1);
					}//end if
					$lPenTestToolsJSON .= ']}}';
				    
				}// end if user didnt pick anything
				
			}// end if user didnt click submit
		} catch (Exception $e) {
			echo $CustomErrorHandler->FormatError($e, "Pen test tool lookup failed");
		}// end try	
	}//end if isset()	
?>

<div class="page-title">Pen Test Tool Lookup</div>

<?php include_once (__ROOT__.'/includes/back-button.inc');?>
<?php include_once (__ROOT__.'/includes/hints/hints-menu-wrapper.inc'); ?>

<!-- BEGIN HTML OUTPUT  -->
<script type="text/javascript">
	
	<?php
		if ($lUseSafeJSONParser){
			echo "var gUseSafeJSONParser = \"TRUE\";".PHP_EOL;
		}else{
			echo "var gUseSafeJSONParser = \"FALSE\";".PHP_EOL;
		}//end if
	
		if ($lUseJavaScriptValidation){
			echo "var gUseJavaScriptValidation = \"TRUE\";".PHP_EOL;
		}else{
			echo "var gUseJavaScriptValidation = \"FALSE\";".PHP_EOL;
		}//end if		

		if ($lErrorNoChoiceMade){
			echo "var gDisplayError = \"TRUE\";".PHP_EOL;
		}else{
			echo "var gDisplayError = \"FALSE\";".PHP_EOL;
		}//end if
		
		echo "var gPenTestToolsJSONString = '" . $lPenTestToolsJSON . "'";
	?>

	var addRow = function(pRowOfData){
		try{
			var lDocRoot = window.document;
			var lTBody = lDocRoot.getElementById("idDisplayTableBody");
			var lTR = lDocRoot.createElement("tr");

			//tool_id, tool_name, phase_to_use, tool_type, comment

			var lToolIDTD = lDocRoot.createElement("td");
			var lToolNameTD = lDocRoot.createElement("td");
			var lPhaseTD = lDocRoot.createElement("td");			
			var lToolTypeTD = lDocRoot.createElement("td");
			var lCommentTD = lDocRoot.createElement("td");

			//lKeyTD.addAttribute("class", "label");
			lToolIDTD.setAttribute("class","sub-body");
			lToolNameTD.setAttribute("class","sub-body");
			lToolNameTD.setAttribute("style","color:#770000");
			lPhaseTD.setAttribute("class","sub-body");
			lToolTypeTD.setAttribute("class","sub-body");
			lCommentTD.setAttribute("class","sub-body");
			lCommentTD.setAttribute("style","font-weight: normal");
			
			lToolIDTD.appendChild(lDocRoot.createTextNode(pRowOfData.tool_id));
			lToolNameTD.appendChild(lDocRoot.createTextNode(pRowOfData.tool_name));
			lPhaseTD.appendChild(lDocRoot.createTextNode(pRowOfData.phase_to_use));
			lToolTypeTD.appendChild(lDocRoot.createTextNode(pRowOfData.tool_type));
			lCommentTD.appendChild(lDocRoot.createTextNode(pRowOfData.comment));
			
			lTR.appendChild(lToolIDTD);
			lTR.appendChild(lToolNameTD);
			lTR.appendChild(lPhaseTD);
			lTR.appendChild(lToolTypeTD);
			lTR.appendChild(lCommentTD);
			
			lTBody.appendChild(lTR);
		}catch(/*Exception*/ e){
			alert("Error trying to add row in function addRow(): " + e.name + "-" + e.message);
		}// end try
	};//end JavaScript function addRow

	var displayError = function(){
		try{
			if(gDisplayError == "TRUE"){
				document.getElementById("id-invalid-input-tr").style.display="";
			}// end if		
		}catch(/*Exception*/ e){
			alert("Error trying to display error: " + e.message);
		}// end try
	};// end function
	
	var displayPenTestTools = function(){

		try{
			var gPenTestToolsJSON = "";
			if (gPenTestToolsJSONString.length > 0){
				if (gUseSafeJSONParser == "TRUE"){
					gPenTestToolsJSON = JSON.parse(gPenTestToolsJSONString);
				}else{
					gPenTestToolsJSON = eval("(" + gPenTestToolsJSONString + ")");
				}// end if gUseSafeJSONParser
				//alert(gPenTestToolsJSON.query.penTestTools.length);

				var laTools = gPenTestToolsJSON.query.penTestTools;
				if(laTools && laTools.length > 0){
					document.getElementById("idDisplayTable").style.display="";
					for (var i=0; i<laTools.length; i++){
						addRow(laTools[i]);
					}//end for i
				}// end if				
			}// end if gPenTestToolsJSONString.length > 0
		}catch(/*Exception*/ e){
			alert("Error trying to parse JSON: " + e.message);
		}// end try
	};// end function
</script>
<span>
	<a style="text-decoration: none; cursor: pointer;" href="./index.php?page=pen-test-tool-lookup-ajax.php">
		<img style="vertical-align: middle;" src="./images/ajax_logo-75-79.jpg" height="75px" width="78px" />
		<span style="font-weight:bold;">Switch to AJAX Version of page</span>
	</a>
</span>
<fieldset style="width: 500px;">
	<legend>Pen Test Tools</legend>
	<form 	action="index.php?page=pen-test-tool-lookup.php" 
			method="post" 
			enctype="application/x-www-form-urlencoded" 
			onsubmit=""
			id="idForm">
		<table>
			<tr id="id-invalid-input-tr" style="display: none;">
				<td class="error-message" colspan="2">
					Error: Invalid Input - Please choose a tool to lookup.
				</td>
			</tr>
			<tr><td>&nbsp;</td></tr>
			<tr>
				<td class="form-header" colspan="2">Select Pen Test Tool</td>
			</tr>
			<tr><td>&nbsp;</td></tr>
			<tr>
				<td class="label" style="text-align: right;">Pen Test Tool</td>
				<td>
					<select id="idToolSelect" name="ToolID" autofocus="autofocus">
						<option value="0923ac83-8b50-4eda-ad81-f1aac6168c5c" selected="selected">Please Choose Tool</option>
						<option value="c84326e4-7487-41d3-91fd-88280828c756">Show All</option>
						<?php echo $lPenTestToolsOptions; ?>
					</select>
				</td>
			</tr>
			<tr><td>&nbsp;</td></tr>
			<tr>
				<td colspan="2" style="text-align: center;">
					<input name="pen-test-tool-lookup-php-submit-button" type="submit" value="Lookup Tool" class="button" />
				</td>
			</tr>
		</table>
	</form>
</fieldset>

<table id="idDisplayTable" style="display:none;">
	<tr><td>&nbsp;</td></tr>
	<tr>
		<td class="sub-header" colspan="5">Pen Testing Tools</td>
		<td>&nbsp;</td>
	</tr>
	<tr>
		<td class="sub-header">Tool ID</td>
		<td class="sub-header">Tool Name</td>
		<td class="sub-header">Tool Type</td>
		<td class="sub-header">Phase Used</td>
		<td class="sub-header">Comments</td>
	</tr>
	<tbody id="idDisplayTableBody" style="font-weight:bold;"></tbody>
	<tr><td>&nbsp;</td></tr>
</table>

<script type="text/javascript">
<!--
	displayError();
	displayPenTestTools();
//-->
</script>