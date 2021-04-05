<div class="page-title"><span style="font-size: 18pt;">Lab 1: Sending HTTP Requests with Netcat</div>

<?php include_once (__ROOT__.'/includes/back-button.inc');?>
<?php include_once (__ROOT__.'/includes/hints/hints-menu-wrapper.inc'); ?>

<!-- BEGIN HTML OUTPUT  -->
<form 	action=""
			method="post"
			enctype="application/x-www-form-urlencoded"
			id="idForm">
	<table>
		<tr><td></td></tr>
		<tr>
			<td class="form-header">
			What version of web application server is running according to the X-Powered-By response header?
			</td>
		</tr>
		<tr><td></td></tr>
		<tr>
			<td>
                <input type="radio" name="answer" id="1" value="1">
                <label for="1">ASP.NET</label><br>
                <input type="radio" name="answer" id="2" value="2">
                <label for="2">PHP</label><br>
                <input type="radio" name="answer" id="3" value="3">
                <label for="3">Java</label><br>
                <input type="radio" name="answer" id="4" value="4">
                <label for="4">Ruby on Rails</label><br>
                <input type="radio" name="answer" id="5" value="5">
            	<label for="5">None of the Above</label><br>
            </td>
		</tr>
		<tr><td></td></tr>
		<tr>
			<td style="text-align:center;">
				<input name="php-submit-button" class="button" type="submit" value="Submit" />
			</td>
		</tr>
		<tr><td></td></tr>
	</table>
</form>

<?php
/* Output results of shell command sent to operating system */
if (isset($_POST["answer"])){
	try{
	    $lResult = "Incorrect";

        if ($_POST["answer"] == 2){
           $lResult = "Correct";
	    }

	    echo "<div class='report-header'>{$lResult}</div>";

    	}catch(Exception $e){
			echo $CustomErrorHandler->FormatError($e, "Input: " . $lMessage);
    	}// end try

}// end if (isset($_POST))
?>