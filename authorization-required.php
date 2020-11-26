<?php 
	try {
		$LogHandler->writeToLog("User attempted to access forbidden page.");	
	} catch (Exception $e) {
		echo $CustomErrorHandler->FormatError($e, "Error writing to log");
	}// end try	
?>

<div class="page-title">Authorization Required</div>

<?php include_once (__ROOT__.'/includes/back-button.inc');?>

<table>
	<tr>
		<td class="error-message">
			Authorization Error: 403 - Page Requires Higher Privileges Than Current User Possesses
		</td>
	</tr>
</table>