<?php 
	/* ------------------------------------------------------
	 * INCLUDE CLASS DEFINITION PRIOR TO INITIALIZING
	 * ------------------------------------------------------ */
	require_once 'classes/MySQLHandler.php';
	$lErrorMessage="";
	try {
		MySQLHandler::databaseAvailable();
	} catch (Exception $e) {
		$lErrorMessage = $e->getMessage();
	}

	//Here because of very weird error
	session_start();

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

<div class="page-title">The database server appears to be offline.</div>

<table style="margin-left:auto; margin-right:auto;">
	<tr><td>&nbsp;</td></tr>
	<tr id="id-bad-page-tr">
		<th>
			The database server at 
			<span class="label" style="color: #cc3333">
			<?php echo MySQLHandler::$mMySQLDatabaseHost ?>
			</span> 
			appears to be offline.
		</th>
	</tr>
	<tr>
		<td>	
			<ol>
				<li>Be sure the username and password to MySQL is the same as configured in includes/database-config.php</li>
				<li>Be aware that MySQL disables password authentication for root user upon installation or update in some systesms. This may happen even for a minor update. Please check the username and password to MySQL is the same as configured in includes/database-config.php</li>
				<li>Try to <a style="font-weight: bold" href="set-up-database.php">setup/reset the DB</a> to see if that helps</li>
				<li>A <a style="font-weight: bold" href="https://www.youtube.com/watch?v=sG5Z4JqhRx8" target="_blank">video is available</a> to help reset MySQL root password</li>
				<li>The commands vary by system and version, but may be something similar to the following
					<ul>
						<li>mysql -u root</li>
						<li>use mysql;</li>
						<li>update user set authentication_string=PASSWORD('mutillidae') where user='root';</li>
						<li>update user set plugin='mysql_native_password' where user='root';</li>
						<li>flush privileges;</li>
						<li>quit;</li>
					</ul>
				</li>
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
		<table style="margin-left:auto; margin-right:auto;">
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
