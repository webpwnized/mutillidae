
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
			$lLimitOutput= FALSE;
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
			
			/*
			 *  If DOS defenses are enabled, we limit output. An attacker can easily cause 
			 *  the logs to fill . This in itself may or may not pose a problem. 
			 *  But filling logs which display is a type of ampliphication attack. 
			 *  If one attacker puts 10,000 rows in the log then 10 innocent 
			 *  users view those logs, the system will have to display 100,000 log rows (10 users * 10,000 rows). 
			 *  Amplifications attacks are also done by sending single IP packets to networks 
			 *  which will broadcast the packet thus ampliphying the packet many times.
			 */
			$lLimitOutput= TRUE;
   		break;
   	}// end switch		

   	if(isset($_GET["deleteLogs"])){
		try{
			$SQLQueryHandler->deleteCapturedData();
		} catch (Exception $e) {
			echo $CustomErrorHandler->FormatError($e, $lQueryString);
		}// end try
	}// end if isset
   	
?>

<script>
	var DeleteCapturedData = function(){
		if (window.confirm("Please confirm all captured data should be deleted")){
			document.location="index.php?page=captured-data.php&deleteLogs=deleteLogs";
		};
	};
</script>

<!-- <div class="page-title">Captured Data</div> -->

<div class="row">
	<?php include_once (__ROOT__.'/includes/back-button.inc');?>
	<?php include_once (__ROOT__.'/includes/hints/hints-menu-wrapper.inc'); ?>
</div>


<!-- Section Content -->

<div class="row">
	<div class="col-md-8 offset-md-2 text-center">
		<h2>Captured Data</h2>
	</div>
</div>

<div class="row">
	<div class="col">
		<p>
			This page shows the data captured by page <a href="index.php?page=capture-data.php">capture-data.php</a>.
			There should also be a file with the same data since capture-data.php tries to save the data to a table
			and a file. The table contents are being displayed on this page. On this system, the 
			file should be found in <?php print pathinfo($_SERVER["SCRIPT_FILENAME"], PATHINFO_DIRNAME); ?>.
			The database table is named captured_data.
		</p>
	</div>
</div>

<ul class="nav py-3">
	<li class="nav-item">
		<span class="material-icons md-36 text-primary align-middle">
		replay_circle_filled
		</span>
		<span title="Click to refresh captured data log" onclick="document.location.reload(true);" style="cursor: pointer;margin-right:35px;font-weight: bold;">
			Refresh
		</span>
	</li>
	<li class="nav-item">
	<span class="material-icons md-36 text-danger align-middle">
		delete_forever
	</span>
		<span title="Click to delete captured data log. This deletes the database table only. The text file is not affected." onclick="DeleteCapturedData();" style="cursor: pointer;margin-right:35px;font-weight: bold;">
			Delete Captured Data
		</span>
	</li>
	<li class="nav-item">
		<span class="material-icons md-36 text-warning align-middle">
			file_copy
		</span>
		<span title="Click to visit capture data page. Your data will be captured." onclick="document.location='./index.php?page=capture-data.php';" style="cursor: pointer;font-weight: bold;">
			Capture Data
		</span>
	</li>
</ul>

<!-- BEGIN HTML OUTPUT  -->

<?php
	try{// to draw table
		$lQueryResult = $SQLQueryHandler->getCapturedData();

		// we have rows. Begin drawing output.
		echo '<table class="table table-hover">';
		
	    echo '<thead class="table-dark">
			    <td>Hostname</td>
			    <td>Client IP Address</td>
			    <td>Client Port</td>
    			<td>User Agent</td>
    			<td>Referrer</td>			    
			    <td>Data</td>
			    <td>Date/Time</td>
		    </thead>';
		echo '<tbody>';
	    if ($lLimitOutput){
	    	echo '<tr><td class="error-header" colspan="10">Note: DOS defenses enabled. Rows limited to last 20.</td></tr>';
	    }// end if

	    $lRowNumber = 0;
	    while($row = $lQueryResult->fetch_object()){
	    	$lRowNumber++;
			
			if(!$lEncodeOutput){
				$lHostname = $row->hostname;
				$lClientIPAddress = $row->ip_address;
				$lClientPort = $row->port;
				$lClientUserAgentString = $row->user_agent_string;
				$lClientReferrer = $row->referrer;				
				$lData = $row->data;
				$lCaptureDate = $row->capture_date;
			}else{
				$lHostname = $Encoder->encodeForHTML($row->hostname);
				$lClientIPAddress = $Encoder->encodeForHTML($row->ip_address);
				$lClientPort = $Encoder->encodeForHTML($row->port);
				$lClientUserAgentString = $Encoder->encodeForHTML($row->user_agent_string);
				$lClientReferrer = $Encoder->encodeForHTML($row->referrer);
				$lData = $Encoder->encodeForHTML($row->data);
				$lCaptureDate = $Encoder->encodeForHTML($row->capture_date);				
			}// end if
							
			echo "<tr>
					<td>{$lHostname}</td>
					<td>{$lClientIPAddress}</td>
					<td>{$lClientPort}</td>
					<td>{$lClientUserAgentString}</td>
					<td>{$lClientReferrer}</td>
					<td>{$lData}</td>
					<td>{$lCaptureDate}</td>
				</tr>\n";
		}//end while $row

		echo '</tbody>';
		echo '<tfoot><td colspan="7">'.$lQueryResult->num_rows.' captured records found</tfoot></tr>';
		echo "</table>";
	} catch (Exception $e) {
		echo $CustomErrorHandler->FormatError($e, "Error writing rows.");
	}// end try;
?>