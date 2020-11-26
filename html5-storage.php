<?php 
	try {	    	
    	switch ($_SESSION["security-level"]){
    		case "0": // This code is insecure.
    			$lUseClientSideStorageForSensitiveData = TRUE;
    			$lUseJavaScriptValidation = FALSE;
				$lEnableHTMLControls = FALSE;
    		break;
    		case "1": // This code is insecure.
    			$lUseClientSideStorageForSensitiveData = TRUE;
    			$lUseJavaScriptValidation = TRUE;
				$lEnableHTMLControls = TRUE;
    		break;

	   		case "2":
	   		case "3":
	   		case "4":
    		case "5": // This code is fairly secure
    			$lUseClientSideStorageForSensitiveData = FALSE;
    			$lUseJavaScriptValidation = TRUE;
				$lEnableHTMLControls = TRUE;
    		break;
    	}// end switch
	}catch(Exception $e){
		echo $CustomErrorHandler->FormatError($e, "Error setting up configuration on page html5-storage.php");
	}// end try	
	
	if($lUseClientSideStorageForSensitiveData){
		echo "<script type=\"text/javascript\" src=\"javascript/html5-secrets.js\"></script>";		
	}// end if
?>

<div class="page-title">HTML 5 Storage</div>

<?php include_once (__ROOT__.'/includes/back-button.inc');?>
<?php include_once (__ROOT__.'/includes/hints/hints-menu-wrapper.inc'); ?>

<!-- BEGIN HTML OUTPUT  -->
<script type="text/javascript">
	/* 
		The Storage interface of the browser API
	
		interface Storage {
			  readonly attribute unsigned long length;
			  DOMString? key(unsigned long index);
			  getter DOMString getItem(DOMString key);
			  setter creator void setItem(DOMString key, DOMString value);
			  deleter void removeItem(DOMString key);
			  void clear();
		};
	*/

	<?php 
		if ($lUseJavaScriptValidation){
			echo "var gUseJavaScriptValidation = \"TRUE\";";
		}else{
			echo "var gUseJavaScriptValidation = \"FALSE\";";
		}
	?>

	var addRow = function(pKey, pItem, pStorageType){
		try{
			var lDocRoot = window.document;
			var lTBody = lDocRoot.getElementById("idSessionStorageTableBody");
			var lTR = lDocRoot.createElement("tr");
			var lKeyTD = lDocRoot.createElement("td");
			var lItemTD = lDocRoot.createElement("td");
			var lTypeTD = lDocRoot.createElement("td");			
			var lBlankTD = lDocRoot.createElement("td");

			//lKeyTD.addAttribute("class", "label");
			lItemTD.style.textAlign = "center";
			lKeyTD.appendChild(lDocRoot.createTextNode(pKey));
			lItemTD.appendChild(lDocRoot.createTextNode(pItem));
			lTypeTD.appendChild(lDocRoot.createTextNode(pStorageType));
			lBlankTD.appendChild(lDocRoot.createTextNode(""));
			
			lTR.appendChild(lKeyTD);
			lTR.appendChild(lItemTD);
			lTR.appendChild(lTypeTD);
			lTR.appendChild(lBlankTD);
			lTBody.appendChild(lTR);
		}catch(/*Exception*/ e){
			alert("Error trying to add row in function addRow(): " + e.name + "-" + e.message);
		};// end try
	};//end JavaScript function addRow

	var setMessage = function(/* String */ pMessage){
		var lMessageSpan = document.getElementById("idAddItemMessageSpan");
		lMessageSpan.innerHTML = pMessage;
		lMessageSpan.setAttribute("class","success-message");
	};// end function setMessage

	var addItemToStorage = function(theForm){
		try{			
			var lKey = theForm.DOMStorageKey.value;
			var lItem = theForm.DOMStorageItem.value;
			var lType = "";
			var lUnacceptableKeyPattern = "[^A-Za-z0-9]";

			if (gUseJavaScriptValidation == "TRUE" && lKey.match(lUnacceptableKeyPattern)){
				setMessage("Unable to add key " + lKey.toString() + " because it contains non-alphanumeric characters");
				return false;
			}// end if

			var lInvalidTR = document.getElementById("id-invalid-input-tr");
			if(lKey.length == 0 || lItem.length == 0){
				lInvalidTR.style.display = "";
				return false;
			}else{
				lInvalidTR.style.display = "none";
			}// end if

			if(theForm.SessionStorageType[0].checked){
				window.sessionStorage.setItem(lKey, lItem);
				lType = "Session";
			}else if (theForm.SessionStorageType[1].checked){
				window.localStorage.setItem(lKey, lItem);
				lType = "Local";
			}// end if

			addRow(lKey, lItem, lType);
			setMessage("Added key " + lKey.toString() + " to " + lType.toString() + " storage");

		}catch(/*Exception*/ e){
			alert("Error in function addItemToStorage(): " + e.name + "-" + e.message);
		}// end try
	};// end JavaScript function

	var init = function(){
		var s = window.sessionStorage;
		var l = window.localStorage;
		var lKey = "";

		// grab local storage
		for(var i=0;i<s.length;i++){
			lKey = s.key(i);
			if(!lKey.match(/^Secure/)){addRow(lKey, s.getItem(lKey), "Session");};
		}//end for

		// grab session storage
		for(var i=0;i<l.length;i++){
			lKey = l.key(i);
			if(!lKey.match(/^Secure/)){addRow(lKey, l.getItem(lKey), "Local");};
		}// end for

	};//end JavaScript function init
	
</script>

<form 	action="index.php?page=html5-storage.php" 
		method="post" 
		enctype="application/x-www-form-urlencoded" 
		onsubmit="return false;"
		id="idForm">		
	<table style="margin-left:auto; margin-right:auto; width: 600px;">
		<tr id="id-invalid-input-tr" style="display: none;">
			<td class="error-message">
				Error: Invalid Input - Both Key and Item are required fields
			</td>
		</tr>
		<tr>
			<td class="form-header">HTML 5 Web Storage</td>
		</tr>
		<tr><td>&nbsp;<td></tr>
	</table>
	<table>
		<tr>
			<td class="sub-header" colspan="3">Web Storage</td>
			<td>&nbsp;</td>
		</tr>
		<tr>
			<td class="sub-body">Key</td>
			<td class="sub-body">Item</td>
			<td class="sub-body">Storage Type</td>
			<td>&nbsp;</td><td>&nbsp;</td>
		</tr>
		<tbody id="idSessionStorageTableBody" style="font-weight:bold;"></tbody>
		<tr><td>&nbsp;</td></tr>
		<tr>
			<td><input	type="text" id="idDOMStorageKeyInput" name="DOMStorageKey" size="20"
						autofocus="autofocus"
				<?php
					if ($lEnableHTMLControls) {
						echo('minlength="1" maxlength="20" required="required"');
					}// end if
				?>			
			></td>
			<td><input type="text" id="idDOMStorageItemInput" name="DOMStorageItem" size="20"
				<?php
					if ($lEnableHTMLControls) {
						echo('minlength="1" maxlength="20" required="required"');
					}// end if
				?>
			></td>
			<td class="label">
				<input type="radio" name="SessionStorageType" value="Session" checked="checked" 
					<?php
						if ($lEnableHTMLControls) {
							echo('required="required"');
						}// end if
					?>
				/>Session
				<input type="radio" name="SessionStorageType" value="Local"
					<?php
						if ($lEnableHTMLControls) {
							echo('required="required"');
						}// end if
					?>
				/>Local
			</td>
			<td>
			<input 	onclick="addItemToStorage(this.form);"
					class="button" 
					type="button" 
					value="Add New" />
			</td>
		</tr>
		<tr><td>&nbsp;</td></tr>
		<tfoot id="idSessionStorageTableFooter">
			<tr><th colspan="5"><span id="idAddItemMessageSpan"></span></th></tr>
			<tr><td>&nbsp;</td></tr>
		</tfoot>
	</table>
</form>
<div style="margin-left:auto; margin-right:auto; width:600px;">
	<span title="Click to delete session storage" onclick='sessionStorage.clear(); var node=window.document.getElementById("idSessionStorageTableBody"); while(node.hasChildNodes()){node.removeChild(node.firstChild)}; init();' style="cursor: pointer;" >
		<img height="24px" width="24px" src="./images/delete-icon-48-48.png" style="vertical-align: middle;" />
		<span style="font-weight: bold;">Session Storage</span>
	</span>
	<span title="Click to delete locate storage" onclick='localStorage.clear(); var node=window.document.getElementById("idSessionStorageTableBody"); while(node.hasChildNodes()){node.removeChild(node.firstChild)}; init();' style="cursor: pointer;" >
		<img height="24px" width="24px" src="./images/delete-icon-48-48.png" style="vertical-align: middle;margin-left: 20px;" />
		<span style="font-weight: bold;">Local Storage</span>
	</span>
	<span title="Click to delete all html 5 storage" onclick='sessionStorage.clear();localStorage.clear(); var node=window.document.getElementById("idSessionStorageTableBody"); while(node.hasChildNodes()){node.removeChild(node.firstChild)}; init();' style="cursor: pointer;" >
		<img height="24px" width="24px" src="./images/delete-icon-48-48.png" style="vertical-align: middle;margin-left: 20px;" />
		<span style="font-weight: bold;">All Storage</span>
	</span>
</div>

<script>
	try{
		init();
	}catch(e){
		alert("Error when calling init()"+e.message);
	}
</script>