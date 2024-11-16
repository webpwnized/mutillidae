<?php

	require_once __SITE_ROOT__.'/classes/JWT.php';

	// Configuration Constants
	define('JWT_EXPIRATION_TIME', 3600); // Token expiration time in seconds
	define('JWT_BASE_URL', ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST']);
	define('ONE_HOUR', 60 * 60);

	function generateJWT($pSigningKey) {
		// Define JWT claims with audience
		$lClaims = [
			'iss' => JWT_BASE_URL,   // Issuer is your domain
			'aud' => JWT_BASE_URL,  // Audience for the token
			'iat' => time(),      // Issued at
			'nbf' => time(),      // Not before
			'exp' => time() + JWT_EXPIRATION_TIME, // Expiration time
			'sub' => $_SESSION["uid"],  // Subject is the client ID
			'userid' => $_SESSION["uid"],
			'jti' => bin2hex(random_bytes(16)) // JWT ID
		];

	   return JWT::encode($lClaims, $pSigningKey);
   }

	try {
    	switch ($_SESSION["security-level"]){
			default:
    		case "0": // This code is insecure.
				$lEnableSignatureValidation = false;
				$lSigningKey = 'snowman';
				break;
    		case "1": // This code is insecure.
				$lEnableSignatureValidation = true;
				$lSigningKey = 'snowman';
				break;
   		case "2":
   		case "3":
   		case "4":
    		case "5": // This code is fairly secure
				$lEnableSignatureValidation = true;
				$lSigningKey = 'MIIBPAIBAAJBANBs46xCKgSt8vSgpGlDH0C8znhqhtOZQQjFCaQzcseGCVlrbI';
			break;
    	}// end switch
	}catch(Exception $e){
		$lErrorMessage = "Error setting up configuration on page jwt.php";
		echo $CustomErrorHandler->getExceptionMessage($e, $lErrorMessage);
	}// end try

   // generate a token with the current user info
	$lAuthorizationToken = generateJWT($lSigningKey);

?>

<div class="page-title">Current User Information</div>

<?php include_once __SITE_ROOT__.'/includes/back-button.inc';?>
<?php include_once __SITE_ROOT__.'/includes/hints/hints-menu-wrapper.inc'; ?>

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
	var authToken = "<?php echo $lAuthorizationToken ?>";
	try{
		var lXMLHTTP;
		const READY_STATE_DONE = 4;
		const HTTP_STATUS_OK = 200;

		lXMLHTTP = new XMLHttpRequest();
		lXMLHTTP.onreadystatechange=function() {
			if (lXMLHTTP.readyState==READY_STATE_DONE && lXMLHTTP.status==HTTP_STATUS_OK) {
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

	const displayUserDetails = function(pUserInfoJSON) {
		try {
			if (pUserInfoJSON && Object.keys(pUserInfoJSON).length > 0) {
				document.getElementById("idDisplayTable").removeAttribute("style");;  // Show the table

				// Safely access properties with default fallbacks
				addRow('CID', pUserInfoJSON['cid'] ?? 'N/A');
				addRow('User Name', pUserInfoJSON['username'] ?? 'N/A');
				addRow('First Name', pUserInfoJSON['firstname'] ?? 'N/A');
				addRow('Last Name', pUserInfoJSON['lastname'] ?? 'N/A');
				addRow('Signature', pUserInfoJSON['mysignature'] ?? 'N/A');
				addRow('Is Admin', pUserInfoJSON['is_admin'] ? 'Yes' : 'No');  // Handle boolean properly
				addRow('Password', pUserInfoJSON['password']);
				addRow('Client ID', pUserInfoJSON['client_id']);
				addRow('Client Secret', pUserInfoJSON['client_secret']);
			} else {
				alert("No user details available.");
			}
		} catch (e) {
			alert("Error trying to parse JSON: " + e.message);
		}
	};

	var addRow = function(pFieldName, pFieldValue) {
		var lTBody = document.getElementById("idDisplayTableBody");
		var row = lTBody.insertRow();
		var newcell1 = row.insertCell(0);
		var newcell2 = row.insertCell(1);
		newcell1.innerText = pFieldName;
		newcell1.setAttribute("class","report-label");
		newcell2.innerText = pFieldValue;
		newcell2.setAttribute("class","report-data");
	}

</script>
