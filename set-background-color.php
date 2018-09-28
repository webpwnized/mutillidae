<?php
	try {
		switch ($_SESSION["security-level"]){
			case "0": // This code is insecure
				$lEnableJavaScriptValidation = FALSE;
				$lEnableHTMLControls = FALSE;
				$lEncodeBackgroundColor = FALSE;
				break;

			case "1": // This code is insecure
				$lEnableJavaScriptValidation = TRUE;
				$lEnableHTMLControls = TRUE;
				$lEncodeBackgroundColor = FALSE;
				break;
				 
			case "2":
			case "3":
			case "4":
			case "5": // This code is fairly secure
				$lEnableJavaScriptValidation = TRUE;
				$lEnableHTMLControls = TRUE;
				$lEncodeBackgroundColor = TRUE;
			break;
		}// end switch
	
	}catch (Exception $e){
		echo $CustomErrorHandler->FormatError($e, "Error setting security level");
	}// end try

	if (isset($_POST["set-background-color-php-submit-button"])){
		
		try{
			if ($lEncodeBackgroundColor){
				/* Protect against one form of patameter pollution 
				 * by grabbing inputs only from POST parameters. */ 

    			/* Protect against XSS by output encoding */
    			$lBackgroundColor = $Encoder->encodeForCSS($_POST["background_color"]);
				$lBackgroundColorText = $Encoder->encodeForHTML($_POST["background_color"]);
			}else{
				$lBackgroundColor = $lBackgroundColorText = $_REQUEST["background_color"];
			};

    	}catch (Exception $e){
			echo $CustomErrorHandler->FormatError($e, "Input: " . $lBackgroundColor);
    	}// end try
    	
	}else{
		$lBackgroundColor = $lBackgroundColorText = "eecccc";
	}// end if (isset($_POST)) 
?>

<!-- Bubble hints code -->
<?php 
	try{
   		$lCSSInjectionPointBallonTip = $BubbleHintHandler->getHint("CSSInjectionPoint");
   		$lReflectedXSSExecutionPointBallonTip = $BubbleHintHandler->getHint("ReflectedXSSExecutionPoint");
	} catch (Exception $e) {
		echo $CustomErrorHandler->FormatError($e, "Error attempting to execute query to fetch bubble hints.");
	}// end try
?>

<script type="text/javascript">
	$(function() {
		$('[CSSInjectionPoint]').attr("title", "<?php echo $lCSSInjectionPointBallonTip; ?>");
		$('[CSSInjectionPoint]').balloon();
		$('[ReflectedXSSExecutionPoint]').attr("title", "<?php echo $lReflectedXSSExecutionPointBallonTip; ?>");
		$('[ReflectedXSSExecutionPoint]').balloon();
	});
</script>

<script type="text/javascript">
	var onSubmitOfForm = function(/* HTMLForm */ theForm){

		try{
			<?php 
			if($lEnableJavaScriptValidation){
				echo 'var lValidateInput = "TRUE"' . PHP_EOL;
			}else{
				echo 'var lValidateInput = "FALSE"' . PHP_EOL;
			}// end if
			?>

			if(lValidateInput == "TRUE"){
				var lDigits = "/[A-Fa-f0-9]{6}/";

				if (theForm.id_background_color.value.search(lDigits) < 1){
						alert('The backgroud color must be 6 hexidecimal digits specified as RRGGBB where R is red, G is green and B is blue');
						return false;
				};// end if
			};// end if(lValidateInput)

			return true;
		}catch(e){
			alert("Error: " + e.message);
		};// end catch
	};// end function onSubmitOfForm(/*HTMLFormElement*/ theForm)
</script>

<div class="page-title">Set Background Color</div>

<?php include_once (__ROOT__.'/includes/back-button.inc'); ?>
<?php include_once (__ROOT__.'/includes/hints/hints-menu-wrapper.inc'); ?>

<form	action="index.php?page=set-background-color.php" 
		method="post" 
		enctype="application/x-www-form-urlencoded"
		onsubmit="return onSubmitOfForm(this);"
		style="background-color:#<?php echo $lBackgroundColor; ?>"
	>
	<table style="margin-left:auto; margin-right:auto;">
		<tr id="id-bad-cred-tr" style="display: none;">
			<td colspan="2" class="error-message">
				Error: Invalid Input
			</td>
		</tr>
		<tr><td>&nbsp;</td></tr>
		<tr>
			<td colspan="2" class="form-header">Please enter the background color you would like to see<br/><br/>Enter the color in RRGGBB format<br/>(Example: Red = FF0000)</td>
		</tr>
		<tr><td>&nbsp;</td></tr>
		<tr>
			<td class="label">Background Color</td>
			<td>
				<input CSSInjectionPoint="1" type="text" name="background_color" id="id_background_color" size="6" autofocus="autofocus"
					<?php
						if ($lEnableHTMLControls) {
							echo('minlength="6" maxlength="6" required="required"');
						}// end if
					?>
				/>
			</td>
		</tr>
		<tr><td>&nbsp;</td></tr>
		<tr>
			<td colspan="2" style="text-align:center;">
				<input name="set-background-color-php-submit-button" class="button" type="submit" value="Set Background Color" />
			</td>
		</tr>
		<tr><td>&nbsp;</td></tr>
		<tr>
			<td ReflectedXSSExecutionPoint="1" class="informative-message" colspan="2" style="text-align: center;">
				The current background color is <?php echo $lBackgroundColorText; ?>
			</td>
		</tr>
		<tr><td>&nbsp;</td></tr>
	</table>
</form>