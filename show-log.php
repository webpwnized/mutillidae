<?php
	/* Known Vulnerabilities
	 * Cross Site Scripting, Cross Site Scripting via HTTP Headers, 
	 * Denial of Service via Logging
	 */

	switch ($_SESSION["security-level"]){
   		case "0": // This code is insecure
   		case "1": // This code is insecure
   			// DO NOTHING: This is insecure		
			$lEncodeOutput = FALSE;
			$lLimitOutput = FALSE;
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
  			 * require allowing dangerous charaters. In the example here, we can 
  			 * validate username but we have to allow special characters in passwords
  			 * least we force weak passwords. We cannot validate the signature hardly 
  			 * at all. The business requirements for text fields will demand most
  			 * characters. Output encoding is the answer. Validate what you can, encode it
  			 * all.
  			 */
   			// encode the output following OWASP standards
   			// this will be HTML encoding because we are outputting data into HTML
			$lEncodeOutput = TRUE;
			$lLimitOutput = TRUE;
   		break;
   	}// end switch		

   	if(isset($_GET["deleteLogs"])){
   		$lQueryResult = $SQLQueryHandler->truncateHitLog();
	}// end if isset

	$lQueryResult = $SQLQueryHandler->getHitLogEntries();
?>



<div class="row">
	<?php include_once (__ROOT__.'/includes/back-button.inc');?>
	<?php include_once (__ROOT__.'/includes/hints/hints-menu-wrapper.inc'); ?>
</div>

<div class="row">
	<div class="col-md-8 offset-md-2 text-center">
		<h2>Log</h2>
	</div>
</div>
<div class="card my-3">
	<div class="card-body">
		<div class="row d-flex justify-content-between text-center">
			<div class="col">
				<span>
					<span class="material-icons align-middle text-warning">
					info
					</span>
					<?php echo $lQueryResult->num_rows; ?> log records found
				</span>
			</div>
			<div class="col">
				<span title="Click to refresh log file" onclick="document.location.href=document.location.href.replace('&deleteLogs=deleteLogs','').replace('&popUpNotificationCode=LFD1','').concat('&popUpNotificationCode=LFR1');" style="cursor: pointer;margin-left:35px;margin-right:35px;white-space:nowrap;font-weight:bold;">
					<span class="material-icons align-middle text-primary">
					refresh
					</span>
					Refresh Logs
				</span>
			</div>
			<div class="col">
				<span title="Click to delete log file" onclick="document.location='./index.php?page=show-log.php&deleteLogs=deleteLogs&popUpNotificationCode=LFD1';" style="cursor: pointer;white-space:nowrap;font-weight:bold;">
					<span class="material-icons align-middle text-danger">
					delete
					</span>
					Delete Logs
				</span>
			</div>
		</div>
	</div>
</div>

<table class="table table-hover" id="idLogRecords">
	<caption>Logs list</caption>
	<thead class="table-dark">
		
	<tr class="report-header">
		<th>Hostname</th>
		<th>IP</th>
		<th>Browser Agent</th>
		<th>Message</th>
		<th>Date/Time</th>
	</tr>
	</thead>
	<?php
		try{// to draw table		

			if ($lLimitOutput){
				echo '<tr><td class="error-header" colspan="10">Note: DOS defenses enabled. Rows limited to last 20.</td></tr>';
			}// end if

			if($lQueryResult->num_rows > 0){
				$lRowNumber = 0;
				while($row = $lQueryResult->fetch_object()){
					$lRowNumber++;
					
					if(!$lEncodeOutput){
						$lHostname = $row->hostname;
						$lClientIPAddress = $row->ip;
						$lBrowser = $row->browser;
						$lReferer = $row->referer;
						$lDate = $row->date;
					}else{
						$lHostname = $Encoder->encodeForHTML($row->hostname);
						$lClientIPAddress = $Encoder->encodeForHTML($row->ip);
						$lBrowser = $Encoder->encodeForHTML($row->browser);
						$lReferer = $Encoder->encodeForHTML($row->referer);
						$lDate = $Encoder->encodeForHTML($row->date);				
					}// end if
					
					echo "<tr>
							<td>{$lHostname}</td>
							<td>{$lClientIPAddress}</td>
							<td>{$lBrowser}</td>
							<td>{$lReferer}</td>
							<td>{$lDate}</td>
						</tr>\n";
				}//end while $row
			}else{
				echo '<tr><td colspan="10"><div class="alert alert-info" role="alert">No Records Found</div></td></tr>';
			}//end if
						
		} catch (Exception $e) {
			echo $CustomErrorHandler->FormatError($e, "Error writing log table rows.".$lQueryString);
		}// end try;
	?>
</table>