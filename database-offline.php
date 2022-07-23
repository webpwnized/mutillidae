<?php
	/* ------------------------------------------------------
	 * INCLUDE CLASS DEFINITION PRIOR TO INITIALIZING
	 * ------------------------------------------------------ */
    require_once 'classes/MySQLHandler.php';

    /* Read ldap configuration file and populate class parameters */
    require_once(__SITE_ROOT__ . '/includes/ldap-config.inc');

	$lErrorMessage = "";
	$lDatabaseHostResolvedIP = "";
	$lDatabasePortScanMessage = "";
	$lDatabasePingResult = "";
	$lDatabaseTracerouteResult = "";
	$lDatabaseHost = MySQLHandler::$mMySQLDatabaseHost;
	$lDatabaseUsername = MySQLHandler::$mMySQLDatabaseUsername;
	$lDatabasePassword = MySQLHandler::$mMySQLDatabasePassword;
	$lDatabaseName = MySQLHandler::$mMySQLDatabaseName;
	$lDatabasePort = MySQLHandler::$mMySQLDatabasePort;

	$lLDAPHostResolvedIP = "";
	$lLDAPPortScanMessage = "";
	$lLDAPPingResult = "";
	$lLDAPTracerouteResult = "";
	$lLDAPHost = LDAP_HOST;
	$lLDAPPort = LDAP_PORT;
	$lLDAPBaseDN = LDAP_BASE_DN;
	$lLDAPBindDN = LDAP_BIND_DN;
	$lLDAPBindPassword = LDAP_BIND_PASSWORD;
	$lSocketErrorCode = "";
	$lSocketErrorMessage = "";

	try{
	    $lDatabaseHostResolvedIP = gethostbyname($lDatabaseHost);
	}catch (Exception $e){
	    // do nothing
	} //end try

	try{
	    $lDatabasePingResult = shell_exec("ping $lDatabaseHost -c 1");
	}catch (Exception $e){
	    // do nothing
	} //end try

	try{
	    $lDatabaseTracerouteResult = shell_exec("traceroute $lDatabaseHost 2>&1");
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
	    if($fp = fsockopen($lDatabaseHost,$lDatabasePort,$lSocketErrorCode,$lSocketErrorMessage,$lWaitTimeoutInSeconds)){
	        $lDatabasePortScanMessage = "The database is reachable. Connected to database host " . $lDatabaseHost . " on port " . $lDatabasePort;
	    } else {
	        $lDatabasePortScanMessage = "Cound not connect to database host $lDatabaseHost on port $lDatabasePort. The hostname or port may be incorrect, the server offline, the service is down, or a firewall is blocking the connection. $lSocketErrorCode - $lSocketErrorMessage";
	    } // end if

	    if (is_resource($fp)){
	        fclose($fp);
	    } // end if

	}catch (Exception $e){
	    // do nothing
	} //end try

	try {
		MySQLHandler::databaseAvailable();
	} catch (Exception $e) {
		$lErrorMessage = $e->getMessage();
	}// end try MySQLHandler::databaseAvailable()

	try{
	    $lLDAPHostResolvedIP = gethostbyname($lLDAPHost);
	}catch (Exception $e){
	    // do nothing
	} //end try

	try{
	    $lLDAPPingResult = shell_exec("ping $lLDAPHost -c 1");
	}catch (Exception $e){
	    // do nothing
	} //end try

	try{
	    $lLDAPTracerouteResult = shell_exec("traceroute $lLDAPHost 2>&1");
	}catch (Exception $e){
	    // do nothing
	} //end try

	try{
	    $lTracerouteTCPResult = shell_exec("traceroute --tcp -p $lLDAPPort $lLDAPHost 2>&1");
	}catch (Exception $e){
	    // do nothing
	} //end try

	try{
	    $lWaitTimeoutInSeconds = 2;
	    if($fp = fsockopen($lLDAPHost,$lLDAPPort,$lSocketErrorCode,$lSocketErrorMessage,$lWaitTimeoutInSeconds)){
	        $lLDAPPortScanMessage = "The LDAP service is reachable. Connected to LDAP service host " . $lLDAPHost . " on port " . $lLDAPPort;
	    } else {
	        $lLDAPPortScanMessage = "Cound not connect to LDAP service host $lLDAPHost on port $lLDAPPort. The hostname or port may be incorrect, the server offline, the service is down, or a firewall is blocking the connection. $lSocketErrorCode - $lSocketErrorMessage";
	    } // end if

	    if (is_resource($fp)){
	        fclose($fp);
	    } // end if
	}catch (Exception $e){
	    // do nothing
	} //end try

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
<html lang="en">

<head>
	<link rel="stylesheet" type="text/css" href="./styles/global-styles.css" />
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"
		integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
	<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1">
	<title>Database Offline</title>

</head>

<main class="container">
	<div class="alert alert-danger" role="alert">
		The database server at host
		<span class="label" style="color: #cc3333">
			<?php echo MySQLHandler::$mMySQLDatabaseHost ?>
		</span>
		appears to be offline.
	</div>

	<div class="h2">
		¿What can i do?
	</div>
	<div class="row">
		<div class="col">

			<ol class="list-group list-group-numbered">
				<li class="list-group-item"><a class="label" href="set-up-database.php">Click here</a> to attempt to
					setup the database. Sometimes this works.</li>
				<li class="list-group-item">Be sure the username and password to MySQL is the same as configured in
					includes/database-config.inc</li>
				<li class="list-group-item">Be aware that MySQL disables password authentication for root user upon
					installation or update in some systems. This may happen even for a minor update. Please check the
					username and password to MySQL is the same as configured in includes/database-config.inc</li>
				<li class="list-group-item">A <a style="font-weight: bold"
						href="https://www.youtube.com/watch?v=sG5Z4JqhRx8" target="_blank">video is available</a> to
					help reset MySQL root password</li>
				<li class="list-group-item">Check the error message below for more hints</li>
				<li class="list-group-item">If you think this message is a false-positive, you can opt-out of these
					warnings below</li>
			</ol>
		</div>
	</div>
	<div class="row my-5">
		<div class="col">
			<div class="card">
				<div class="card-body">
					<h5 class="card-title">
						Database Diagnostics Information
					</h5>
					<h6 class="card-subtitle">
						<div class="alert alert-danger" role="alert">
							Database Error message: <?php echo $lErrorMessage; ?>
						</div>
					</h6>

					<table class="table table-hover">
						<caption>Database Diagnostics Information</caption>
						<thead class="table-dark">
							<tr>
								<th scope="col">
									Name
								</th>
								<th scope="col">
									Value
								</th>
							</tr>
						</thead>
						<tbody>
							<tr>
								<th scope="row">
									MySQL Host
								</th>
								<td>
									<?php echo $lDatabaseHost; ?>
								</td>
							</tr>
							<tr>
								<th scope="row">
									MySQL Port
								</th>
								<td>
									<?php echo $lDatabasePort; ?>
								</td>
							</tr>
							<tr>
								<th scope="row">
									MySQL Username
								</th>
								<td>
									<?php echo $lDatabaseUsername; ?>
								</td>
							</tr>
							<tr>
								<th scope="row">
									MySQL Password
								</th>
								<td>
									<?php echo $lDatabasePassword; ?>
								</td>
							</tr>
							<tr>
								<th scope="row">
									MySQL Database Name
								</th>
								<td>
									<?php echo $lDatabaseName; ?>
								</td>
							</tr>
							<tr>
								<th scope="row">
									IP resolved from database hostname
								</th>
								<td>
									<?php echo $lDatabaseHostResolvedIP; ?>
								</td>
							</tr>
							<tr>
								<th scope="row">
									Traceroute database results
								</th>
								<td>
									<code>

										<?php echo $lDatabaseTracerouteResult; ?>
									</code>
								</td>
							</tr>
							<tr>
								<th scope="row">
									Port scan database results
								</th>
								<td>
									<?php echo $lDatabasePortScanMessage; ?>
								</td>
							</tr>
						</tbody>
					</table>

				</div>
			</div>
		</div>
	</div>

	<div class="row my-5">
		<div class="col">
			<div class="card">
				<div class="card-body">
					<h5 class="card-title">
						LDAP Diagnostics Information
					</h5>
					<table class="table table-hover">
						<caption>LDAP Diagnostics Information</caption>
						<thead class="table-dark">
							<tr>
								<th scope="col">
									Name
								</th>
								<th scope="col">
									Value
								</th>
							</tr>
						</thead>
						<tbody>
							<tr>
								<th scope="row">
									LDAP Host
								</th>
								<td>
									<?php echo $lLDAPHost; ?>
								</td>
							</tr>
							<tr>
								<th scope="row">
									LDAP Port
								</th>
								<td>
									<?php echo $lLDAPPort; ?>
								</td>
							</tr>
							<tr>
								<th scope="row">
									LDAP Username
								</th>
								<td>
									<?php echo $lLDAPBindDN; ?>
								</td>
							</tr>
							<tr>
								<th scope="row">
									LDAP Password
								</th>
								<td>
									<?php echo $lLDAPBindPassword; ?>
								</td>
							</tr>
							<tr>
								<th scope="row">
									LDAP Base DN
								</th>
								<td>
									<?php echo $lLDAPBaseDN; ?>
								</td>
							</tr>
							<tr>
								<th scope=row>
									IP resolved from LDAP service hostname
								</th>
								<td>
									<?php echo $lLDAPHostResolvedIP; ?>
								</td>
							</tr>
							<tr>
								<th scope="row">
									Ping LDAP service results
								</th>
								<td>
									<?php echo $lLDAPPingResult; ?>
								</td>
							</tr>
							<tr>
								<th scope="row">
									LDAP Traceroute results
								</th>
								<td>
									<code>
										<?php echo $lLDAPTracerouteResult; ?>
									</code>
								</td>
							</tr>
							<tr>
								<th scope="row">
									LDAP Port scan results
								</th>
								<td>
									<?php echo $lLDAPPortScanMessage; ?>
								</td>
							</tr>
						</tbody>

					</table>
				</div>
			</div>
		</div>
	</div>

	<div class="row">
		<div class="col">
			<div class="card text-dark bg-warning">
				<div class="card-body">
					<h5 class="card-title">
						Opt out of database warnings
					</h5>
					<p>
						You can opt out of database connection warnings for the remainder of this session
					</p>
					<form action="database-offline.php" method="post" enctype="application/x-www-form-urlencoded"
						id="idDatabaseOffline">
						<input name="database-offline-php-submit-button" class="btn btn-primary" type="submit"
							value="Opt Out" />
					</form>
				</div>
			</div>
		</div>
	</div>


</main>



<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"
	integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous">
</script>