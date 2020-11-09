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

<div class="page-title">Log</div>

<?php include_once (__ROOT__.'/includes/back-button.inc');?>
<?php include_once (__ROOT__.'/includes/hints/hints-menu-wrapper.inc'); ?>

<style>
	#idLogRecords tr td{
		border: 1px solid black;
	}
	#idLogRecords{
		width: 100%;
	}
	img{
		vertical-align:middle;
	}
</style>
<table class="results-table" id="idLogRecords">
<tr class="report-header">
	<td colspan="10">	
		<span>
			<img width="32px" height="32px" src="./images/information-icon-64-64.png" />
			<?php echo $lQueryResult->num_rows; ?> log records found
		</span>
		<span title="Click to refresh log file" onclick="document.location.href=document.location.href.replace('&deleteLogs=deleteLogs','').replace('&popUpNotificationCode=LFD1','').concat('&popUpNotificationCode=LFR1');" style="cursor: pointer;margin-left:35px;margin-right:35px;white-space:nowrap;font-weight:bold;">
			<img width="32px" height="32px" src="./images/refresh-button-48px-by-48px.png" />
			Refresh Logs
		</span>
		<span title="Click to delete log file" onclick="document.location='./index.php?page=show-log.php&deleteLogs=deleteLogs&popUpNotificationCode=LFD1';" style="cursor: pointer;white-space:nowrap;font-weight:bold;">
			<img width="32px" height="32px" src="./images/delete-icon-48-48.png" />
			Delete Logs
		</span>
	</td>
</tr>		
<tr class="report-header">
    <td style="font-weight:bold;">Hostname</td>
    <td style="font-weight:bold;">IP</td>
    <td style="font-weight:bold;">Browser Agent</td>
    <td style="font-weight:bold;">Message</td>
    <td style="font-weight:bold;">Date/Time</td>
</tr>

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
	    	echo '<tr><td class="warning-message" colspan="10">No Records Found</td></tr>';
		}//end if
					
	} catch (Exception $e) {
		echo $CustomErrorHandler->FormatError($e, "Error writing log table rows.".$lQueryString);
	}// end try;
?>
</table>