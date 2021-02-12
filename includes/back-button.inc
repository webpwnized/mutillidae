<?php
    if(isset($_SESSION["security-level"])){
        $lSecurityLevel = $_SESSION["security-level"];
    }else{
        $lSecurityLevel = 0;
    }

    switch ($lSecurityLevel){
   		case "0": // This code is insecure
   		case "1":
   			// DO NOTHING: This is insecure
			$lEncodeOutput = FALSE;
		break;

		case "2":
		case "3":
		case "4":
		case "5": // This code is fairly secure
  			/*
  			 * NOTE: Input validation is excellent but not enough. The output must be
  			 * encoded per context. For example, if output is placed in HTML,
  			 * then HTML encode it. Blacklisting is a losing proposition. You
  			 * cannot blacklist everything. The business requirements will usually
  			 * require allowing dangerous charaters.Output encoding is the answer.
  			 * Validate what you can, encode it all.
  			 */
			$lEncodeOutput = TRUE;
   		break;
   	}// end switch

   	/* On first request, $_SERVER['HTTP_REFERER']
   	 * will be null. Bug discovered by Tim Tomes.
   	 */
   	if(isset($_SERVER['HTTP_REFERER'])){
   		 $lHTTPReferer = $_SERVER['HTTP_REFERER'];
   	}else{
   		$lHTTPReferer = "index.php?page=home.php";
   	}// end if

   	/* Encode to defeat JavaScript string injection */
   	if ($lEncodeOutput) {
   		$lHTTPReferer = $Encoder->encodeForJavaScript($lHTTPReferer);
   	}// end if
?>

<div style="margin: 5px;">
	<span style="font-weight: bold; margin-right: 50px;">
	<a id="back-button-anchor">
		<img	src="./images/back-button-64-64.png"
				alt="Go Back"
				align="middle"
		/>
		&nbsp;
		Back
	</a>
	</span>
	<?php include_once('./includes/help-button.inc');?>
</div>
<script nonce="<?php echo $CSPNonce; ?>">
    document.addEventListener('DOMContentLoaded', function () {
        document.getElementById('back-button-anchor').addEventListener('click',
            function(){
                document.location.href='<?php echo $lHTTPReferer; ?>';
            });
    });
</script>