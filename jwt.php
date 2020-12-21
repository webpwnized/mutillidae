<?php

	require_once (__ROOT__ . '/classes/JWT.php');

	// attack requires user - if not logged in, just display message and return
	if(!isset($_SESSION['uid']) || !is_numeric($_SESSION['uid'])) {
		echo '<p>Not logged in. Please <a href="index.php?page=login.php">login/register</a> first...</p>';
		return;
	}

	try {
    	switch ($_SESSION["security-level"]){
    		case "0": // This code is insecure.
				$lEnableSignatureValidation = FALSE;
				$lKey = 'snowman';
				break;
    		case "1": // This code is insecure.
				$lEnableSignatureValidation = TRUE;
				$lKey = 'snowman';
				break;
   		case "2":
   		case "3":
   		case "4":
    		case "5": // This code is fairly secure
				$lEnableSignatureValidation = TRUE;
				$lKey = 'MIIBPAIBAAJBANBs46xCKgSt8vSgpGlDH0C8znhqhtOZQQjFCaQzcseGCVlrbI';
			break;
    	}// end switch
	}catch(Exception $e){
		echo $CustomErrorHandler->getExceptionMessage($e, "Error setting up configuration on page jwt.php");
	}// end try

   // generate a token with the current user info
	$authToken = generate_token($lKey);

	function generate_token($key) {
     	$payload = array(
			"iss" => "http://mutillidae.local",
			"aud" => "http://mutillidae.local",
			"iat" => time(),
			"exp" => time() + (30 * 60),
			"userid" => $_SESSION["uid"]
		);
		$jwt = JWT::encode($payload, $key);
		return $jwt;
	}
?>

<div class="page-title">Current User Information</div>

<?php include_once (__ROOT__.'/includes/back-button.inc');?>
<?php include_once (__ROOT__.'/includes/hints/hints-menu-wrapper.inc'); ?>

<!-- BEGIN HTML OUTPUT  -->
<div id="loading-div">Loading user information, please wait...</div>
<div>&nbsp;</div>
<table id="idDisplayTable" style="display:none;">
	<thead>
		<tr>
			<td colspan="2" class="form-header">Current User Information</td>
		</tr>
		<tr><td>&nbsp;</td></tr>
	</thead>
	<tbody id="idDisplayTableBody"></tbody>
</table>

<script type="text/javascript">
	var authToken = "<?php echo $authToken ?>";
	try{
		var lXMLHTTP;
		lXMLHTTP = new XMLHttpRequest();
		lXMLHTTP.onreadystatechange=function() {
			if (lXMLHTTP.readyState==4 && lXMLHTTP.status==200) {
				var lUserDetailsJSON = JSON.parse(lXMLHTTP.response);
				loadingdiv = document.getElementById("loading-div");
				loadingdiv.style.display="none";
				displayUserDetails(lUserDetailsJSON);
			};
		};
		lXMLHTTP.open("POST", "./ajax/jwt.php", true);
		lXMLHTTP.setRequestHeader("AuthToken", authToken);
		lXMLHTTP.send();
	}catch(e){
		alert("Error trying execute AJAX call: " + e.message);
	}//end try

	var displayUserDetails = function(pUserInfoJSON){
		try {
			var laInfo = pUserInfoJSON;
			if(laInfo) {
				document.getElementById("idDisplayTable").style.display="";
				addRow('CID', pUserInfoJSON['cid']);
				addRow('User Name', pUserInfoJSON['username']);
				addRow('First Name', pUserInfoJSON['firstname']);
				addRow('Last Name', pUserInfoJSON['lastname']);
				addRow('Signature', pUserInfoJSON['mysignature']);
				addRow('Is Admin', pUserInfoJSON['is_admin']);
				addRow('Password', '*********');
			}
		}catch(/*Exception*/ e){
			alert("Error trying to parse JSON: " + e.message);
		}// end try
	};// end function

	var addRow = function(pFieldName, pFieldValue) {
		var lTBody = document.getElementById("idDisplayTableBody");
		var row = lTBody.insertRow();
		var newcell1 = row.insertCell(0);
		var newcell2 = row.insertCell(1);
		newcell1.innerText = pFieldName;
		newcell1.setAttribute("class","sub-header");
		newcell2.innerText = pFieldValue;
		newcell2.setAttribute("class","sub-body");
		newcell2.setAttribute("style","text-align:left");
	}

</script>
