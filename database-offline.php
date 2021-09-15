<?php
	/* ------------------------------------------------------
	 * INCLUDE CLASS DEFINITION PRIOR TO INITIALIZING
	 * ------------------------------------------------------ */
	require_once 'classes/MySQLHandler.php';

	$lErrorMessage = "";
	$lHostResolvedIP = "";
	$lPortScanMessage = "";
	$lPingResult = "";
	$lTracerouteResult = "";
	$lDatabaseHost = MySQLHandler::$mMySQLDatabaseHost;
	$lDatabaseUsername = MySQLHandler::$mMySQLDatabaseUsername;
	$lDatabasePassword = MySQLHandler::$mMySQLDatabasePassword;
	$lDatabaseName = MySQLHandler::$mMySQLDatabaseName;
	$lDatabasePort = MySQLHandler::$mMySQLDatabasePort;

	try{
	    $lHostResolvedIP = gethostbyname($lDatabaseHost);
	}catch (Exception $e){
	    // do nothing
	} //end try

	try{
	    $lPingResult = shell_exec("ping $lDatabaseHost -c 1");
	}catch (Exception $e){
	    // do nothing
	} //end try

	try{
	    $lTracerouteResult = shell_exec("traceroute $lDatabaseHost 2>&1");
	}catch (Exception $e){
	    // do nothing
	} //end try

	try{
	    $lTracerouteTCPResult = shell_exec("traceroute --tcp -p $lDatabasePort $lDatabaseHost 2>&1");
	}catch (Exception $e){
	    // do nothing
	} //end try

	try{
	    $lWaitTimeoutInSeconds = 2;
	    $lErrorCode = "";
	    $lErrorMessage = "";
	    if($fp = fsockopen($lDatabaseHost,$lDatabasePort,$lErrorCode,$lErrorMessage,$lWaitTimeoutInSeconds)){
	        $lPortScanMessage = "The database is reachable. Connected to database host " . $lDatabaseHost . " on port " . $lDatabasePort;
	    } else {
	        $lPortScanMessage = "Cound not connect to database host " . $lDatabaseHost . " on port " . $lDatabasePort. ". The hostname or port may be incorrect, the server offline, the service is down, or a firewall is blocking the connection.";
	    } // end if
	    fclose($fp);
	}catch (Exception $e){
	    // do nothing
	} //end try

	try {
		MySQLHandler::databaseAvailable();
	} catch (Exception $e) {
		$lErrorMessage = $e->getMessage();
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

<div>&nbsp;</div>
<div class="page-title">The database server at
	<span class="label" style="color: #cc3333">
		<?php echo MySQLHandler::$mMySQLDatabaseHost ?>
	</span>
	appears to be offline.
</div>

<table>
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
	<tr>
		<td style="width:700px;">
            <div class="warning-message">Diagnostics Information</div>
            <div>&nbsp;</div>
            <div><span class="label">Error message: </span><?php echo $lErrorMessage; ?></div>
            <div>&nbsp;</div>
            <div><span class="label">Database host: </span><?php echo $lDatabaseHost; ?></div>
            <div><span class="label">Database post: </span><?php echo $lDatabasePort; ?></div>
            <div><span class="label">Database username: </span><?php echo $lDatabaseUsername; ?></div>
            <div><span class="label">Database password: </span><?php echo $lDatabasePassword; ?></div>
            <div><span class="label">Database name: </span><?php echo $lDatabaseName; ?></div>
            <div>&nbsp;</div>
            <div><span class="label">IP resolved from hostname: </span><?php echo $lHostResolvedIP; ?></div>
            <div>&nbsp;</div>
            <div><span class="label">Ping database results: </span><pre><?php echo $lPingResult; ?></pre></div>
            <div><span class="label">Traceroute database results: </span><pre><?php echo $lTracerouteResult; ?></pre></div>
            <div><span class="label">Port scan database results: </span><?php echo $lPortScanMessage; ?></div>
		</td>
	</tr>
</table>

<div>
	<form 	action="database-offline.php"
			method="post"
			enctype="application/x-www-form-urlencoded"
			id="idDatabaseOffline">
		<table>
			<tr><td>&nbsp;</td></tr>
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
