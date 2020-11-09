<?php

	class ClientField{

		private $mFieldname = "";
		private $mValue = "";
		private $mIsCorrect = FALSE;

		public function __construct ($pFieldname, $pValue){
			$this->mFieldname = $pFieldname;
			$this->mValue = $pValue;
			$this->mIsCorrect = FALSE;
		}//end constructor
		
		public function getFieldname(){
			return $this->mFieldname;
		}//end method

		public function getValue(){
			return $this->mValue;
		}//end method

		public function setIsCorrect($pIsCorrect){
			$this->mIsCorrect = $pIsCorrect;
		}//end method
		
		public function getIsCorrect(){
			return $this->mIsCorrect;
		}//end method

	};// end class

	class ClientFields{
	
		private $mDLList = NULL;
		private $mTargetNumber = -1;
		private $mEncodeOutput = FALSE;
		private $Encoder = null;
		
		public function __construct ($pPathToESAPI){
			try{
				$this->mDLList = new SplDoublyLinkedList();
				$this->mDLList->setIteratorMode(SplDoublyLinkedList::IT_MODE_FIFO|SplDoublyLinkedList::IT_MODE_KEEP);
				
				//initialize OWASP ESAPI for PHP
				require_once $pPathToESAPI . 'ESAPI.php';
				$this->ESAPI = new ESAPI($pPathToESAPI . 'ESAPI.xml');
				$this->Encoder = $this->ESAPI->getEncoder();
								
			} catch(Exception $e){
				echo $CustomErrorHandler->FormatError($e, "ClientFields.__construct()");
			}// end try
		}//end constructor
		
		public function EnableOutputEncoding(){
			$this->mEncodeOutput = TRUE;
		}// end try
		
		public function DisableOutputEncoding(){
			$this->mEncodeOutput = FALSE;
		}// end try
		
		public function addFieldHelper(/*ClientField*/ $pClientField){
			try{
				$lIsCorrect=($this->mTargetNumber==$pClientField->getValue());
				$pClientField->setIsCorrect($lIsCorrect);
				$this->mDLList->push($pClientField);
			} catch(Exception $e){
				echo $CustomErrorHandler->FormatError($e, "ClientFields.addFieldHelper()");
			}// end try
		}//end method
		
		public function addField(/*ClientField*/ $pClientField){
			try{
				$this->addFieldHelper($pClientField);
			} catch(Exception $e){
				echo $CustomErrorHandler->FormatError($e, "ClientFields.addField()");
			}// end try
		}//end method
		
		public function setTargetNumber(/*Integer*/ $pTargetNumber){
			$this->mTargetNumber = $pTargetNumber;
		}//end method

		public function prettyPrintFields(){
			try{
				$lHTMLHeader = "";
				$lHTMLDetails = "";
				$lImage = "";
				$lCountOfCorrectAnswers = 0;
				$lFieldName = "";
				$lFieldValue = "";
				
				for ($this->mDLList->rewind(); $this->mDLList->valid(); $this->mDLList->next()) {

					$lFieldName = $this->mDLList->current()->getFieldname();
					$lFieldValue = $this->mDLList->current()->getValue();
					
					if ($this->mEncodeOutput) {
						$lFieldName = $this->Encoder->encodeForHTML($lFieldName);
						$lFieldValue = $this->Encoder->encodeForHTML($lFieldValue);
					}//end if

					if ($this->mDLList->current()->getIsCorrect()){
						$lImage = 'green-checkmark-36-36.png';
						$lCountOfCorrectAnswers += 1;
					}else{
						$lImage = 'red-x-24-24.png';
					}// end if

					$lHTMLDetails .= 
						'<tr><td style="font-weight:bold;">'.$lFieldName.
						'</td><td>'.$lFieldValue.
						'</td><td style="text-align:center;"><img src="images/'.$lImage.
						'" /></td></tr>';
				}//end for

				$lHTMLHeader = 
						'<tr><td>&nbsp;<td></tr><tr><td style="font-weight:bold;text-align:center;" colspan="3">You got '.
						$lCountOfCorrectAnswers.' out of '.
						$this->mDLList->count().' correct</td></tr><tr><td>&nbsp;<td></tr>';
								
				return $lHTMLHeader.$lHTMLDetails;
			} catch(Exception $e){
				echo $CustomErrorHandler->FormatError($e, "ClientFields.prettyPrintFields()");
			}// end try		
		}//end method

	};// end class
	
	try{
    	switch ($_SESSION["security-level"]){
    		case "0": // This code is insecure.
				$lEnableJavaScriptValidation = TRUE;
				$lEnableHTMLControls = TRUE;
				$lProtectAgainstMethodSwitching = FALSE;
				$lEncodeOutput = FALSE;
    		break;

    		case "1": // This code is insecure.
				$lEnableJavaScriptValidation = TRUE;
				$lEnableHTMLControls = TRUE;
				$lProtectAgainstMethodSwitching = FALSE;
				$lEncodeOutput = FALSE;
    		break;

	   		case "2":
	   		case "3":
	   		case "4":
    		case "5": // This code is fairly secure
    			$lEnableJavaScriptValidation = TRUE;
				$lEnableHTMLControls = TRUE;
				$lProtectAgainstMethodSwitching = TRUE;
				$lEncodeOutput = TRUE;
    		break;
    	}// end switch
   
		// if we want to enforce POST method, we need to be careful to specify $_POST
	   	if(!$lProtectAgainstMethodSwitching){
			$lSubmitButtonClicked = isset($_REQUEST["client_side_control_challenge_php_submit_button"]);
	   	}else{
			$lSubmitButtonClicked = isset($_POST["client_side_control_challenge_php_submit_button"]);
	   	}//end if !$lProtectAgainstMethodSwitching
	   	
	   	if($lSubmitButtonClicked){

	   		if (isset($_SESSION['cscc-random-flag'])) {
	   			$lCurrentRandomFlag = $_SESSION['cscc-random-flag'];
	   		}else{
	   			$lCurrentRandomFlag = 0;
	   		}//end if
	   		
			// if we want to enforce POST method, we need to be careful to specify $_POST
		   	if(!$lProtectAgainstMethodSwitching){
		   		$lTextbox = isset($_REQUEST["textbox"])?$_REQUEST["textbox"]:"";
		   		$lReadonlyTextbox = isset($_REQUEST["readonly_textbox"])?$_REQUEST["readonly_textbox"]:"";
		   		$lShortTextbox = isset($_REQUEST["short_textbox"])?$_REQUEST["short_textbox"]:"";
		   		$lDisabledTextbox = isset($_REQUEST["disabled_textbox"])?$_REQUEST["disabled_textbox"]:"";
		   		$lHiddenTextbox = isset($_REQUEST["hidden_textbox"])?$_REQUEST["hidden_textbox"]:"";
		   		$lTrickyTextbox = isset($_REQUEST["tricky_textbox"])?$_REQUEST["tricky_textbox"]:"";
		   		$lVanishingTextbox = isset($_REQUEST["vanishing_textbox"])?$_REQUEST["vanishing_textbox"]:"";
		   		$lShyTextbox = isset($_REQUEST["shy_textbox"])?$_REQUEST["shy_textbox"]:"";
		   		$lPassword = isset($_REQUEST["password"])?$_REQUEST["password"]:"";
		   		$lCheckbox = isset($_REQUEST["checkbox"])?$_REQUEST["checkbox"]:"";
		   		$lSelect = isset($_REQUEST["select"])?$_REQUEST["select"]:"";
		   		$lRadio = isset($_REQUEST["radio"])?$_REQUEST["radio"]:"";
		   		$lEmail = isset($_REQUEST["email"])?$_REQUEST["email"]:"";
		   		$lFile = isset($_REQUEST["file"])?$_REQUEST["file"]:"";
		   		$lNumber = isset($_REQUEST["number"])?$_REQUEST["number"]:"";
		   		$lRange = isset($_REQUEST["range"])?$_REQUEST["range"]:"";
		   		$lSearch = isset($_REQUEST["search"])?$_REQUEST["search"]:"";
		   		$lSubmitButton = isset($_REQUEST["client_side_control_challenge_php_submit_button"])?$_REQUEST["client_side_control_challenge_php_submit_button"]:"";
		   	}else{
		   		$lTextbox = isset($_POST["textbox"])?$_POST["textbox"]:"";
		   		$lReadonlyTextbox = isset($_POST["readonly_textbox"])?$_POST["readonly_textbox"]:"";
		   		$lShortTextbox = isset($_POST["short_textbox"])?$_POST["short_textbox"]:"";
		   		$lDisabledTextbox = isset($_POST["disabled_textbox"])?$_POST["disabled_textbox"]:"";
		   		$lHiddenTextbox = isset($_POST["hidden_textbox"])?$_POST["hidden_textbox"]:"";
		   		$lTrickyTextbox = isset($_POST["tricky_textbox"])?$_POST["tricky_textbox"]:"";
		   		$lVanishingTextbox = isset($_POST["vanishing_textbox"])?$_POST["vanishing_textbox"]:"";
		   		$lShyTextbox = isset($_POST["shy_textbox"])?$_POST["shy_textbox"]:"";
		   		$lPassword = isset($_POST["password"])?$_POST["password"]:"";
		   		$lCheckbox = isset($_POST["checkbox"])?$_POST["checkbox"]:"";
		   		$lSelect = isset($_POST["select"])?$_POST["select"]:"";
		   		$lRadio = isset($_POST["radio"])?$_POST["radio"]:"";
		   		$lEmail = isset($_POST["email"])?$_POST["email"]:"";
		   		$lFile = isset($_POST["file"])?$_POST["file"]:"";
		   		$lNumber = isset($_POST["number"])?$_POST["number"]:"";
		   		$lRange = isset($_POST["range"])?$_POST["range"]:"";
		   		$lSearch = isset($_POST["search"])?$_POST["search"]:"";
		   		$lSubmitButton = isset($_POST["client_side_control_challenge_php_submit_button"])?$_POST["client_side_control_challenge_php_submit_button"]:"";
		   	}//end if !$lProtectAgainstMethodSwitching	

		   	$lFields = new ClientFields("owasp-esapi-php/src/");
		   	$lFields->setTargetNumber($lCurrentRandomFlag);
		   	if ($lEncodeOutput){
		   		$lFields->EnableOutputEncoding();
		   	}//end if
		   	
		   	$lFields->addField(new ClientField("Textbox", $lTextbox));
		   	$lFields->addField(new ClientField("Readonly Textbox", $lReadonlyTextbox));
		   	$lFields->addField(new ClientField("Short Textbox", $lShortTextbox));
		   	$lFields->addField(new ClientField("Disabled Textbox", $lDisabledTextbox));
		   	$lFields->addField(new ClientField("Hidden Textbox", $lHiddenTextbox));
		   	$lFields->addField(new ClientField("Tricky Textbox", $lTrickyTextbox));
		   	$lFields->addField(new ClientField("Vanishing Textbox", $lVanishingTextbox));
		   	$lFields->addField(new ClientField("Shy Textbox", $lShyTextbox));
		   	$lFields->addField(new ClientField("Search Textbox", $lSearch));
		   	$lFields->addField(new ClientField("Password Textbox", $lPassword));
		   	$lFields->addField(new ClientField("Drop-down Box", $lSelect));
		   	$lFields->addField(new ClientField("Checkbox", $lCheckbox));
		   	$lFields->addField(new ClientField("Radio Button", $lRadio));
		   	$lFields->addField(new ClientField("Email Control", $lEmail));
		   	$lFields->addField(new ClientField("File Control", $lFile));
		   	$lFields->addField(new ClientField("Number Control", $lNumber));
		   	$lFields->addField(new ClientField("Range Control", $lRange));
		   	$lFields->addField(new ClientField("Submit Button", $lSubmitButton));

	   	}//end if $lSubmitButtonClicked

	   	/*
	   	 * Create a random value for the user to submit.
	   	*/
	   	
	   	// if we want to enforce POST method, we need to be careful to specify $_POST
	   	if(!$lProtectAgainstMethodSwitching){
	   		$lResetTargetValue = isset($_REQUEST["resetTargetValue"])?$_REQUEST["resetTargetValue"]:"";
	   	}else{
	   		$lResetTargetValue = isset($_POST["resetTargetValue"])?$_POST["resetTargetValue"]:"";
	   	}//end if !$lProtectAgainstMethodSwitching

	   	if (!isset($_SESSION['cscc-random-flag']) || $lResetTargetValue == 1) {
	   		$_SESSION['cscc-random-flag'] = mt_rand(0, mt_getrandmax());;
	   	}//end if
	   	
	   	$lRandomFlag = $_SESSION['cscc-random-flag'];

	} catch(Exception $e){
		$lSubmitButtonClicked = FALSE;
		echo $CustomErrorHandler->FormatError($e, "Error creating client-side challenge");
	}// end try	
?>

<script type="text/javascript">
<!--
	<?php
		if($lSubmitButtonClicked){
			echo "var lSubmitOccured = true" . PHP_EOL;
		}else{
			echo "var lSubmitOccured = false" . PHP_EOL;
		}// end if		

		if($lEnableJavaScriptValidation){
			echo "var lValidateInput = true" . PHP_EOL;
		}else{
			echo "var lValidateInput = false" . PHP_EOL;
		}// end if		
	?>

	function onSubmitOfForm(/*HTMLFormElement*/ theForm){
		try{
			if(lValidateInput){
				var lAlphaValidationPattern = RegExp("^[A-Za-z]$","i");
				var lNumericValidationPattern = RegExp("^[0-9]$","i");
				var lEmailValidationPattern = RegExp("^[a-zA-Z_0-9]+@[a-zA-Z_0-9]+?\.[a-zA-Z0-9]{2,3}$","i");
				var lMessage = 'Only letters are allowed into fields which is weird considering you are supposed to enter a number';
				var lEmailMessage = 'The email address does not look like an email address';
				var lNumericMessage = 'The email address does not look like an email address';
								
				if (theForm.id_textbox.value.match(lAlphaValidationPattern) == null){
					alert("Textbox: "+lMessage);return false;
				}// end if
				if (theForm.id_readonly_textbox.value.match(lAlphaValidationPattern) == null){
					alert("Read-only Textbox: "+lMessage);return false;
				}// end if
				if (theForm.id_short_textbox.value.match(lAlphaValidationPattern) == null){
					alert("Short Textbox: "+lMessage);return false;
				}// end if
				if (theForm.id_disabled_textbox.value.match(lAlphaValidationPattern) == null){
					alert("Disabled Textbox: "+lMessage);return false;
				}// end if
				if (theForm.id_hidden_textbox.value.match(lAlphaValidationPattern) == null){
					alert("Hidden Textbox: "+lMessage);return false;
				}// end if
				if (theForm.id_tricky_textbox.value.match(lAlphaValidationPattern) == null){
					alert("Tricky Textbox: "+lMessage);return false;
				}// end if
				if (theForm.id_vanishing_textbox.value.match(lAlphaValidationPattern) == null){
					alert("Vanishing Textbox: "+lMessage);return false;
				}// end if
				if (theForm.id_shy_textbox.value.match(lAlphaValidationPattern) == null){
					alert("Shy Textbox: "+lMessage);return false;
				}// end if
				if (theForm.id_password.value.match(lAlphaValidationPattern) == null){
					alert("Password Textbox: "+lMessage);return false;
				}// end if
				if (theForm.id_email.value.match(lEmailValidationPattern) == null){
					alert("Email: "+lEmailMessage);return false;
				}// end if
				if (theForm.id_number.value.match(lNumericValidationPattern) == null){
					alert("Number: "+lNumericMessage);return false;
				}// end if
				if (theForm.id_range.value.match(lNumericValidationPattern) == null){
					alert("Range: "+lNumericMessage);return false;
				}// end if	
				if (theForm.id_search.value.match(lAlphaValidationPattern) == null){
					alert("Search Textbox: "+lMessage);return false;
				}// end if
				
			}// end if(lValidateInput)

			return true;
		}catch(e){
			alert("Error: " + e.message);
		}// end catch
	}// end function onSubmitOfForm(/*HTMLFormElement*/ theForm)
//-->
</script>

<style>
	input.box:hover {
	    left: 200px;
	}
	input.box {
	    position: relative;
	    left: 0px;
	}
</style>

<div class="page-title">Client-side Control Challenge</div>

<?php include_once (__ROOT__.'/includes/back-button.inc');?>
<?php include_once (__ROOT__.'/includes/hints/hints-menu-wrapper.inc'); ?>

<div id="id-client-side-control-challenge-form-div" style="text-align:center;">
	<form 	action="index.php?page=client-side-control-challenge.php" 
			method="post" 
			enctype="application/x-www-form-urlencoded" 
			onsubmit="return onSubmitOfForm(this);"
			id="idclient-side-control-challengeForm"
			style="margin-left:auto; margin-right:auto; width:600px;">
		<table>
			<tr>
				<td colspan="2" class="form-header">Please enter flag into all form fields</td>
			</tr>
			<tr><td>&nbsp;</td></tr>
			<tr>
				<td style="text-align: left;" colspan="2">
					Please enter the following flag into each field and choose the flag in each control.
					For example enter the flag into all text fields and choose the flag in the drop down,
					check the box next to the flag, and select the radio button for the flag.
					<br />
					<br />
					Be certain <span style="font-weight: bold;">every</span> control contains the value of the flag.
					<br />
					<br />
					When all controls have the value of the flag submit the form.
				</td>
			</tr>
			<tr><td>&nbsp;</td></tr>
			<tr>
				<td class="label" style="text-align: left;">Flag</td>
				<td class="label" style="text-align: left;">
					<?php echo $lRandomFlag;?>
					<a 	href="index.php?page=client-side-control-challenge.php&resetTargetValue=1"
						style="margin-left: 50px;">
						Get New Value
					</a>
				</td>
			</tr>
			<tr><td>&nbsp;</td></tr>
			<tr>
				<td class="label" style="text-align: left;">Text Box</td>
				<td style="text-align: left;">
					<input type="text" name="textbox" id="id_textbox" size="15" maxlength="15" required="required" autofocus="autofocus" />
				</td>
			</tr>			<tr>
				<td class="label" style="text-align: left;">Read-only Text Box</td>
				<td style="text-align: left;">
					<input type="text" name="readonly_textbox" id="id_readonly_textbox" size="15" maxlength="15" required="required" autofocus="autofocus" readonly="readonly" value="42" />
				</td>
			</tr>			
			<tr>
				<td class="label" style="text-align: left;">Short Text Box</td>
				<td style="text-align: left;">
					<input type="text" name="short_textbox" id="id_short_textbox" size="3" maxlength="3" required="required" />
				</td>
			</tr>
			<tr>
				<td class="label" style="text-align: left;">Disabled Text Box</td>
				<td style="text-align: left;">
					<input type="text" name="disabled_textbox" id="id_disabled_textbox" size="15" maxlength="15" required="required" disabled="disabled" style="background-color:#dddddd;" />
				</td>
			</tr>
			<tr>
				<td class="label" style="text-align: left;">Hidden Text Box</td>
				<td style="text-align: left;">
					<input type="hidden" name="hidden_textbox" id="id_hidden_textbox" size="15" maxlength="15" required="required" />
				</td>
			</tr>
			<tr>
				<td class="label" style="text-align: left;">"Secured by JavaScript" Text Box</td>
				<td style="text-align: left;">
					<input type="text" name="tricky_textbox" id="id_tricky_textbox" size="15" maxlength="15" required="required" onfocus="javascript:this.blur();" />
				</td>
			</tr>
			<tr>
				<td class="label" style="text-align: left;">Vanishing Text Box</td>
				<td style="text-align: left;">
					<input type="text" name="vanishing_textbox" id="id_vanishing_textbox" size="15" maxlength="15" required="required" onmouseover="javascript:this.type='hidden';" />
				</td>
			</tr>
			<tr>
				<td class="label" style="text-align: left;">Shy Text Box</td>
				<td style="text-align: left;">
					<input type="text" name="shy_textbox" id="id_shy_textbox" size="15" maxlength="15" required="required" class="box" />
				</td>
			</tr>
			<tr>
				<td class="label" style="text-align: left;">Search Textbox</td>
				<td style="text-align: left;">
					<input type="search" name="search" id="id_search" pattern="[a-zA-z]" required="required" />
				</td>
			</tr>
			<tr>
				<td class="label" style="text-align: left;">Password</td>
				<td style="text-align: left;">
					<input type="password" name="password" id="id_password" size="15" maxlength="15" required="required" value="Hint: If you use Burp-Suite this exercise is much easier" />
				</td>
			</tr>
			<tr>
				<td class="label" style="text-align: left;">Drop-down Box</td>
				<td style="text-align: left;">
					<select name="select" id="id_select" required="required">
						<option value="1">One</option>
						<option value="2">Two</option>
						<option value="3">Buckle my shoe</option>
					</select>
				</td>
			</tr>			
			<tr>
				<td class="label" style="text-align: left;">Checkbox</td>
				<td style="text-align: left;">
					<input type="checkbox" name="checkbox" id="id_checkbox" value="<?php echo $lRandomFlag;?>" required="required" disabled="disabled" /><span class="label">Select <?php echo $lRandomFlag;?>?</span><br/>
				</td>
			</tr>
			<tr>
				<td class="label" style="text-align: left;">Radio Button</td>
				<td style="text-align: left;">
					<input type="radio" name="radio" id="id_radio" value="1" required="required" checked="checked" /><span class="label">1</span><br/>
					<input type="radio" name="radio" id="id_radio" value="2" required="required" /><span class="label">2</span><br/>
					<input type="radio" name="radio" id="id_radio" value="<?php echo $lRandomFlag;?>" required="required" disabled="disabled" /><span class="label"><?php echo $lRandomFlag;?></span><br/>
				</td>
			</tr>
			<tr>
				<td class="label" style="text-align: left;">Email Control</td>
				<td style="text-align: left;">
					<input type="email" name="email" id="id_email" required="required" pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,3}$" />
				</td>
			</tr>
			<tr>
				<td class="label" style="text-align: left;">File Upload</td>
				<td style="text-align: left;">
					<input type="file" name="file" id="id_file" required="required" />
				</td>
			</tr>
			<tr>
				<td class="label" style="text-align: left;">Number</td>
				<td style="text-align: left;">
					<input type="number" name="number" id="id_number" min="0" max="999" step="1" required="required" />
				</td>
			</tr>
			<tr>
				<td class="label" style="text-align: left;">Range</td>
				<td style="text-align: left;">
					<input type="range" name="range" id="id_range" min="0" max="999" step="1" required="required" />
				</td>
			</tr>		
			<tr><td>&nbsp;</td></tr>
			<tr>
				<td colspan="2" style="text-align:center;">
					<input name="client_side_control_challenge_php_submit_button" id="id_client_side_control_challenge_php_submit_button" class="button" type="submit" value="Submit" />
				</td>
			</tr>
			<tr><td>&nbsp;</td></tr>
		</table>
	</form>
</div>

<div id="id-client-side-control-challenge-output-div" style="text-align: center; display: none;">
	<table class="results-table" id="idClientFields" style="width: 500px; text-align: left;">	
		<tr class="report-header">
		    <td>Field</td>
		    <td>Value Submitted</td>
		    <td>&nbsp;</td>	
		</tr>
		<?php echo $lFields->prettyPrintFields(); ?>
		<tr><td>&nbsp;</td></tr>
	</table>	
</div>

<script type="text/javascript">
	if (lSubmitOccured){
		document.getElementById("id-client-side-control-challenge-output-div").style.display="";		
	}// end if lSubmitOccured	
</script>