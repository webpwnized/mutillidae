<div class="page-title">Secret PHP Server Configuration Page</div>

<?php
	$lShowPHPInfo = FALSE;

	if(isset($_SESSION["security-level"])){
	    $lSecurityLevel = $_SESSION["security-level"];
	}else{
	    $lSecurityLevel = 0;
	}

	switch ($lSecurityLevel){
   		case "0": // This code is insecure
   		case "1": // This code is insecure
			$lShowPHPInfo = TRUE;
   		break;
   		case "2":
   		case "3":
   		case "4":
   		case "5": // This code is fairly secure
   			if(isset($_SESSION['is_admin'])){
   				if($_SESSION['is_admin'] == 'TRUE'){
					$lShowPHPInfo = TRUE;
   				}// end if is_admin
   			}// end if isseet $_SESSION['is_admin']
  		break;
	}// end switch

	if($lShowPHPInfo){
	    echo phpinfo(INFO_ALL);
	}else{
		echo '<table><tr><td class="error-message">Secure sites do not expose administrative or configuration pages to the Internet</td></tr></table>';
	}//end if $lShowPHPInfo
?>