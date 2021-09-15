<?php
	/* ------------------------------------------------------
	 * INCLUDE CLASS DEFINITION PRIOR TO INITIALIZING
	 * ------------------------------------------------------ */
	require_once 'classes/MySQLHandler.php';

	$lErrorMessage = "";
	$lHostResolvedIP = "";
	$lPortScanMessage = "";
	$lDatabaseHost = MySQLHandler::$mMySQLDatabaseHost;
	$lDatabaseUsername = MySQLHandler::$mMySQLDatabaseUsername;
	$lDatabasePassword = MySQLHandler::$mMySQLDatabasePassword;
	$lDatabaseName = MySQLHandler::$mMySQLDatabaseName;
	$lDatabasePort = MySQLHandler::$mMySQLDatabasePort;

	try {
		MySQLHandler::databaseAvailable();
	} catch (Exception $e) {
		$lErrorMessage = $e->getMessage();

		try{
		    $lHostResolvedIP = gethostbyname($lDatabaseHost);
		}catch (Exception $e){
		    // do nothing
		}

		try{
		    $waitTimeoutInSeconds = 2;
		    if($fp = fsockopen($lDatabaseHost,$lDatabasePort,$errCode,$errStr,$waitTimeoutInSeconds)){
		        $lPortScanMessage = "Connected to database host " . $lDatabaseHost . " on port " . $lDatabasePort;
		    } else {
		        $lPortScanMessage = "Cound not connect to database host " . $lDatabaseHost . " on port " . $lDatabasePort. ". The hostname or port may be incorrect, the server offline, the service is down, or a firewall is blocking the connection.";
		    }
		    fclose($fp);
    	}catch (Exception $e){
    	    // do nothing
    	}

	}// end try MySQLHandler::databaseAvailable()

	if (session_status() == PHP_SESSION_NONE){
	    session_start();
	}// end if

	$lSubmitButtonClicked = isSet($_REQUEST["database-offline-php-submit-button"]);

	if ($lSubmitButtonClicked) {
		$_SESSION["UserOKWithDatabaseFailure"] = "TRUE";
		header("Location: index.php", true, 302);
	}//end if

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
	<link rel="stylesheet" type="text/css" href="./styles/global-styles.css" />
	<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1">
	<title>Database Offline</title>
</head>

<div class="page-title">The database server at
	<span class="label" style="color: #cc3333">
		<?php echo MySQLHandler::$mMySQLDatabaseHost ?>
	</span>
	appears to be offline.
</div>

<table>
	<tr><td>&nbsp;</td></tr>
	<tr>
		<td>
			<ol>
				<li><a class="label" href="set-up-database.php">Click here</a> to attempt to setup the database. Sometimes this works.</li>
				<li>Be sure the username and password to MySQL is the same as configured in includes/database-config.inc</li>
				<li>Be aware that MySQL disables password authentication for root user upon installation or update in some systems. This may happen even for a minor update. Please check the username and password to MySQL is the same as configured in includes/database-config.inc</li>
				<li>A <a style="font-weight: bold" href="https://www.youtube.com/watch?v=sG5Z4JqhRx8" target="_blank">video is available</a> to help reset MySQL root password</li>
				<li>Check the error message below for more hints</li>
				<li>If you think this message is a false-positive, you can opt-out of these warnings below</li>
			</ol>
		</td>
	</tr>
	<tr><td class="warning-message">Error Message</td></tr>
	<tr>
		<td style="width:700px;" class="warning-message">
			<?php echo "Error: ".$lErrorMessage ?>
		</td>
	</tr>
	<tr><td>&nbsp;</td></tr>
</table>

<div>
	<form 	action="database-offline.php"
			method="post"
			enctype="application/x-www-form-urlencoded"
			id="idDatabaseOffline">
		<table>
			<tr><td></td></tr>
			<tr>
				<td colspan="2" class="form-header">Opt out of database warnings</td>
			</tr>
			<tr><td></td></tr>
			<tr>
				<td class="label">You can opt out of database connection warnings for the remainder of this session</td>
			</tr>
			<tr>
				<td colspan="2" style="text-align:center;">
					<input name="database-offline-php-submit-button" class="button" type="submit" value="Opt Out" />
				</td>
			</tr>
		</table>
	</form>
</div>

<div class="report-header">Diagnostics Information</div>
<div><?php echo "Error message: ".$lErrorMessage; ?></div>
<div><?php echo "Database host: ".$lDatabaseHost; ?></div>
<div><?php echo "IP resolved from hostname: ".$lHostResolvedIP; ?></div>
<div><?php echo "Database username: ".$lDatabaseUsername; ?></div>
<div><?php echo "Database password: ".$lDatabasePassword; ?></div>
<div><?php echo "Database name: ".$lDatabaseName; ?></div>
<div><?php echo "Port scan results: ".$lPortScanMessage; ?></div>
