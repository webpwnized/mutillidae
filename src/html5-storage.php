<?php
	$lHTMLControlInput = 'minlength="1" maxlength="20" required="required"';
	$lHTMLControlRadio = 'required="required"';

	try {
    	switch ($_SESSION["security-level"]){
			default: // Default case: This code is insecure.
    		case "0": // This code is insecure.
    			$lUseClientSideStorageForSensitiveData = true;
    			$lUseJavaScriptValidation = false;
				$lEnableHTMLControls = false;
    		break;
    		case "1": // This code is insecure.
    			$lUseClientSideStorageForSensitiveData = true;
    			$lUseJavaScriptValidation = true;
				$lEnableHTMLControls = true;
    		break;

	   		case "2":
	   		case "3":
	   		case "4":
    		case "5": // This code is fairly secure
    			$lUseClientSideStorageForSensitiveData = false;
    			$lUseJavaScriptValidation = true;
				$lEnableHTMLControls = true;
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

<?php include_once __SITE_ROOT__.'/includes/back-button.inc';?>
<?php include_once __SITE_ROOT__.'/includes/hints/hints-menu-wrapper.inc'; ?>

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
			var lUnacceptableKeyPattern = "[\W]";

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

	function clearStorage(storageType) {
		switch (storageType) {
			case 'session':
				sessionStorage.clear();
				break;
			case 'local':
				localStorage.clear();
				break;
			case 'all':
				sessionStorage.clear();
				localStorage.clear();
				break;
		}

		// Clear the HTML table displaying storage items
		const node = document.getElementById("idSessionStorageTableBody");
		while (node.hasChildNodes()) {
			node.removeChild(node.firstChild);
		}

		// Re-initialize to reflect the current state
		init();
	}

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
	<table>
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
			<td colspan="3">
				<div style="margin-left:auto; margin-right:auto;">
					<span title="Click to delete session storage" 
						onclick="clearStorage('session')" 
						style="cursor: pointer;">
						<img height="24px" width="24px" src="./images/delete-icon-48-48.png" style="vertical-align: middle;" />
						<span style="font-weight: bold;">Session Storage</span>
					</span>
					<span title="Click to delete local storage" 
						onclick="clearStorage('local')" 
						style="cursor: pointer; margin-left: 20px;">
						<img height="24px" width="24px" src="./images/delete-icon-48-48.png" style="vertical-align: middle;" />
						<span style="font-weight: bold;">Local Storage</span>
					</span>
					<span title="Click to delete all HTML 5 storage" 
						onclick="clearStorage('all')" 
						style="cursor: pointer; margin-left: 20px;">
						<img height="24px" width="24px" src="./images/delete-icon-48-48.png" style="vertical-align: middle;" />
						<span style="font-weight: bold;">All Storage</span>
					</span>
				</div>
			</td>
		</tr>
		<tr>
			<td class="sub-header" colspan="3">Web Storage</td>
			<td>&nbsp;</td>
		</tr>
		<tr>
			<td class="sub-body">Key</td>
			<td class="sub-body">Item</td>
			<td class="sub-body">Storage Type</td>
		</tr>
		<tbody id="idSessionStorageTableBody" style="font-weight:bold;"></tbody>
		<tr><td colspan="3">&nbsp;</td></tr>
		<tr>
			<td class="label" colspan="3">
				<input	type="text" id="idDOMStorageKeyInput" name="DOMStorageKey" size="20"
						autofocus="autofocus"
				<?php if ($lEnableHTMLControls) { echo $lHTMLControlInput; } ?>
				/>
				<input type="text" id="idDOMStorageItemInput" name="DOMStorageItem" size="20"
				<?php if ($lEnableHTMLControls) { echo $lHTMLControlInput; } ?>
				/>
				<input type="radio" name="SessionStorageType" value="Session" checked="checked" 
				<?php if ($lEnableHTMLControls) { echo $lHTMLControlRadio; } ?>
				/>Session
				<input type="radio" name="SessionStorageType" value="Local"
				<?php if ($lEnableHTMLControls) { echo $lHTMLControlRadio; } ?>
				/>Local
				<input 	onclick="addItemToStorage(this.form);"
						class="button"
						type="button"
						value="Add New" />
			</td>
		</tr>
		<tr><td colspan="3">&nbsp;</td></tr>
		<tfoot id="idSessionStorageTableFooter">
			<tr><th colspan="3" scope="col"><span id="idAddItemMessageSpan"></span></th></tr>
			<tr><td colspan="3">&nbsp;</td></tr>
		</tfoot>
	</table>
</form>

<script>
	try{
		init();
	}catch(e){
		alert("Error when calling init()"+e.message);
	}
</script>