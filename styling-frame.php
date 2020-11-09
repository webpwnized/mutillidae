<?php

	try{
		switch ($_SESSION["security-level"]){
			case "0": // This code is insecure
			case "1": // This code is insecure
				$lProtectAgainstMethodTampering = FALSE;
				$lEncodeOutput = FALSE;
				break;
		   
			case "2":
			case "3":
			case "4":
			case "5": // This code is fairly secure
				$lProtectAgainstMethodTampering = TRUE;
				$lEncodeOutput = TRUE;
				break;
		};//end switch
	
		$lParameterSubmitted = FALSE;
		if (isset($_GET["page-to-frame"]) || isset($_POST["page-to-frame"]) || isset($_REQUEST["page-to-frame"])) {
			$lParameterSubmitted = TRUE;
		}// end if
	
		$lPageToFrame = "styling.php?page-title=Styling+with+Mutillidae";
		if ($lParameterSubmitted){
			if ($lProtectAgainstMethodTampering) {
				$lPageToFrame = $_GET["page-to-frame"];
			}else{
				$lPageToFrame = $_REQUEST["page-to-frame"];
			};// end if $lProtectAgainstMethodTampering
	
			if($lEncodeOutput){
				$lPageToFrame = $Encoder->encodeForHTML($lPageToFrame);
			};// end if
		};// end if $lFormSubmitted

		try {
			$LogHandler->writeToLog("Styling Frame: Framing URL " . $lPageToFrame . " based on user choice.");
		} catch (Exception $e) {
			//Do nothing. Do not interrupt page for failed log attempt.
		}//end try
		
	} catch (Exception $e) {
		echo $CustomErrorHandler->FormatError($e, $lQueryString);
	};// end try;

?>

<?php include_once (__ROOT__.'/includes/back-button.inc');?>
<?php include_once (__ROOT__.'/includes/hints/hints-menu-wrapper.inc'); ?>
<!-- Note: To encourage IE into compatibility mode add the following
	meta tag into the HTML head section -->
<meta http-equiv="X-UA-Compatible" content="IE=EmulateIE7">
<iframe src="<?php echo $lPageToFrame; ?>"
		style="margin-left:auto; margin-right:auto; border:none; overflow:hidden;"
		PathRelativeStylesheetInjectionArea="1" 
		width="100%" height="600px" 
		></iframe>