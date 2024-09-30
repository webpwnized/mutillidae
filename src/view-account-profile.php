<?php

    switch ($_SESSION["security-level"]){
		default: // Default case: This code is insecure
        case "0": // This code is insecure
            // DO NOTHING: This is equivalent to using client side security
            $lEnableJavaScriptValidation = false;
            $lEnableHTMLControls = false;
            $lProtectAgainstMethodTampering = false;
            $lProtectAgainstIDOR = false;
            $lProtectAgainstPasswordLeakage = false;
            $lEncodeOutput = false;
            break;
            
        case "1": // This code is insecure
            // DO NOTHING: This is equivalent to using client side security
            $lEnableJavaScriptValidation = true;
            $lEnableHTMLControls = true;
            $lProtectAgainstMethodTampering = false;
            $lProtectAgainstIDOR = false;
            $lProtectAgainstPasswordLeakage = false;
            $lEncodeOutput = false;
            break;
            
        case "2":
        case "3":
        case "4":
        case "5": // This code is fairly secure
            /*
             * Concerning SQL Injection, use parameterized stored procedures. Parameterized
             * queries is not good enough. You cannot use least privilege with queries.
             */
            $lEnableJavaScriptValidation = true;
            $lEnableHTMLControls = true;
            $lProtectAgainstMethodTampering = true;
            $lProtectAgainstIDOR = true;
            $lProtectAgainstPasswordLeakage = true;
            $lEncodeOutput = true;
            break;
    }// end switch
    
?>

<div class="page-title">View Profile</div>

<?php include_once __SITE_ROOT__.'/includes/back-button.inc';?>
<?php include_once __SITE_ROOT__.'/includes/hints/hints-menu-wrapper.inc'; ?>

<?php

    if($lProtectAgainstIDOR){
	    if(isset($_SESSION['uid'])){
	       $lUserUID = $_SESSION['uid'];
	    }else{
			$lUserUID = null;
	    } // if isset
	}else{
	    if(isset($_REQUEST['uid'])){
	        $lUserUID = $_REQUEST['uid'];
	    }else{
	        if(isset($_COOKIE['uid'])){
	            $lUserUID = $_COOKIE['uid'];
	        }else{
				$lUserUID = null;
	        } // if isset
	    } // if isset
	} // $lProtectAgainstIDOR
	
	$lUserLoggedIn = !(is_null($lUserUID));

	$lUsername = "";
	$lPassword = "";
	$lSignature = "";
	$lResultsFound = false;
	
	if($lUserLoggedIn){
	    try {
	           $lQueryResult = $SQLQueryHandler->getUserAccountByID($lUserUID);
	           $LogHandler->writeToLog("Got account with UID : " . $lUserUID);
	           
	           if (isset($lQueryResult->num_rows)){
				   $lResultsFound = $lQueryResult->num_rows > 0;
	           }//end if

	           if($lResultsFound){
	               $row = $lQueryResult->fetch_object();
	               
	               if(!$lEncodeOutput){
	                   $lUsername = $row->username;
	                   if (!$lProtectAgainstPasswordLeakage){
	                       $lPassword = $row->password;
	                   }
	                   $lSignature = $row->mysignature;
	               }else{
	                   $lUsername = $Encoder->encodeForHTML($row->username);
	                   if (!$lProtectAgainstPasswordLeakage){
	                       $lPassword = $Encoder->encodeForHTML($row->password);
	                   }
	                   $lSignature = $Encoder->encodeForHTML($row->mysignature);
	               }// end if
                   $lAPIKey = $row->apikey; // immutable data
	           }
	           
	    } catch (Exception $e) {
	        echo $CustomErrorHandler->FormatError($e, "Failed to get account");
	        $LogHandler->writeToLog("Failed to get account with UID : " . $lUserUID);
	    }// end try
	} // if $lUserLoggedIn
?>

<script type="text/javascript">
<!--
	<?php 
		if($lEnableJavaScriptValidation){
			echo "var lValidateInput = \"TRUE\"" . PHP_EOL;
		}else{
			echo "var lValidateInput = \"FALSE\"" . PHP_EOL;
		}// end if
	?>

<span>
	<a style="text-decoration: none; cursor: pointer;" href="./webservices/rest/ws-user-account.php">
		<img style="vertical-align: middle;" src="./images/ajax_logo-75-79.jpg" height="75px" width="78px" alt="AJAX" />
		<span style="font-weight:bold;">Switch to RESTful Web Service Version of this Page</span>
	</a>
</span>

<div id="id-edit-account-profile-form-div" style="display: hidden;">
    <table>
        <tr><td>&nbsp;</td></tr>
        <tr>
            <td colspan="2" class="form-header">User Profile</td>
        </tr>
        <tr><td>&nbsp;</td></tr>
        <tr>
            <td class="label">Username</td>
            <td><?php echo $lUsername; ?></td>
        </tr>
        <tr>
            <td class="label">Password</td>
            <td><?php echo $lPassword; ?></td>
        </tr>
        <tr>
            <td class="label">Signature</td>
            <td><?php echo $lSignature; ?></td>
        </tr>
        <tr>
            <td class="label">API Key</td>
            <td><?php echo $lAPIKey; ?></td>
        </tr>
        <tr><td>&nbsp;</td></tr>
    </table>
</div>

<div id="id-profile-not-found-div" style="text-align: center; display: none;">
	<table>
		<th>
			<td class="label">User profile not found. You may <a href="index.php?page=login.php">login here</a></td>
		</th>
		<tr><td></td></tr>
		<tr><td></td></tr>
		<tr>
			<td style="text-align:center; font-style: italic;">
				Dont have an account? <a href="index.php?page=register.php">Please register here</a>
			</td>
		</tr>
	</table>
</div>

<script>
	var lResultsFound = <?php echo $lResultsFound?"true":"false"; ?>;
	if (lResultsFound){
		document.getElementById("id-edit-account-profile-form-div").style.display="";
		document.getElementById("id-profile-not-found-div").style.display="none";
	}else{
		document.getElementById("id-edit-account-profile-form-div").style.display="none";
		document.getElementById("id-profile-not-found-div").style.display="";		
	}// end if lResultsFound
</script>
