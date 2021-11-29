<div class="page-title"><span style="font-size: 18pt;"><?php echo "{$lTitle}"; ?></div>

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
			<?php echo "{$lQuestion}"; ?>
			</td>
		</tr>
		<tr><td></td></tr>
		<tr>
			<td>
                <input type="radio" name="answer" id="1" value="1">
                <label class="label" for="1"><?php echo "{$lChoice_1}"; ?></label><br>
                <input type="radio" name="answer" id="2" value="2">
                <label class="label" for="2"><?php echo "{$lChoice_2}"; ?></label><br>
                <input type="radio" name="answer" id="3" value="3">
                <label class="label" for="3"><?php echo "{$lChoice_3}"; ?></label><br>
                <input type="radio" name="answer" id="4" value="4">
                <label class="label" for="4"><?php echo "{$lChoice_4}"; ?></label><br>
                <input type="radio" name="answer" id="5" value="5">
            	<label class="label" for="5"><?php echo "{$lChoice_5}"; ?></label><br>
            </td>
		</tr>
		<tr><td></td></tr>
		<tr>
			<td style="text-align:center;">
				<input name="submit-button" class="button" type="submit" value="Submit" />
			</td>
		</tr>
		<tr><td></td></tr>
	</table>
</form>

<?php
	try{
	    $lResult = "Choose the best answer or view Hints and Videos";

	    if (isset($_POST["answer"])){

	    if ($_POST["answer"] == $lCorrectAnswer){
           $lResult = "Correct";
	    }else{
	        $lResult = "Incorrect";
	    }//end if

	    }// end if (isset($_POST))

	    echo "<div class='report-header'>{$lResult}</div>";

    }catch(Exception $e){
		echo $CustomErrorHandler->FormatError($e, "Input: " . $lMessage);
   	}// end try
?>

<br/>
<div class="lab-links">
<?php
    $lFirstLab = 1;
    $lFinalLab = 63;
    $lLastLab = $lLabNumber-1;
    $lNextLab = $lLabNumber+1;

    if ($lLabNumber > $lFirstLab){
        echo "<a href=\"index.php?page=labs/lab-{$lLastLab}.php\">Previous Lab</a>";
    }
    if ($lLabNumber > $lFirstLab && $lLabNumber < $lFinalLab){
        echo "&nbsp;&nbsp;|&nbsp;&nbsp;";
    }
    if ($lLabNumber < $lFinalLab){
        echo "<a href=\"index.php?page=labs/lab-{$lNextLab}.php\" >Next Lab</a>";
    }
?>
</div>