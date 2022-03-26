<?php
    $lSecurityLevel = $_SESSION["security-level"];

    switch ($lSecurityLevel){
        case "0": // This code is insecure
            $lSecurityLevelMessage = "Security Level: ".$lSecurityLevel." (Hosed)";
            break;
        case "1": // This code is insecure
            // DO NOTHING: This is equivalent to using client side security
            $lSecurityLevelMessage = "Security Level: ".$lSecurityLevel." (Client-Side Security)";
            break;

        case "2":
        case "3":
        case "4":
        case "5": // This code is fairly secure
            $lSecurityLevelMessage = "Security Level: ".$lSecurityLevel." (Secure)";
            break;
    }// end switch

	if($_SESSION['loggedin'] == "True"){

	    switch ($lSecurityLevel){
	   		case "0": // This code is insecure
	   		case "1": // This code is insecure
	   			// DO NOTHING: This is equivalent to using client side security
				$logged_in_user = $_SESSION['logged_in_user'];
			break;

	   		case "2":
	   		case "3":
	   		case "4":
	   		case "5": // This code is fairly secure
	   			// encode the entire message following OWASP standards
	   			// this is HTML encoding because we are outputting data into HTML
				$logged_in_user = $Encoder->encodeForHTML($_SESSION['logged_in_user']);
			break;
	   	}// end switch

	   	$lUserID = $_SESSION['uid'];

	   	$lUserAuthorizationLevelText = 'User';

	   	if ($_SESSION['is_admin'] == 'TRUE'){
	   		$lUserAuthorizationLevelText = 'Admin';
	   	}// end if

		$lAuthenticationStatusMessage =
			'Logged In ' .
			$lUserAuthorizationLevelText . ": " .
			'<span class="logged-in-user">'.$logged_in_user.'</span>'.
			'<a href="index.php?page=edit-account-profile.php&uid='.$lUserID.'">'.
            '<img src="images/edit-icon-20-20.png" /></a>';
	} else {
		$logged_in_user = "anonymous";
		$lAuthenticationStatusMessage = "Not Logged In";
	}// end if($_SESSION['loggedin'] == "True")

	if ($_SESSION["EnforceSSL"] == "True"){
		$lEnforceSSLLabel = "Drop TLS";
	}else {
		$lEnforceSSLLabel = "Enforce TLS";
	}//end if

	$lHintsMessage = "Hints: ".$_SESSION["hints-enabled"];

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/1999/REC-html401-19991224/loose.dtd">
<html lang="en">
<head>
	<link rel="shortcut icon" href="./images/favicon.ico" type="image/x-icon" />
	<!-- Implementation of new Frontend -->
	<title>OWASP Mutillidae II</title>
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
	<!-- End custom implementation head -->
	<link rel="stylesheet" type="text/css" href="styles/global-styles.css" />
	<link rel="stylesheet" type="text/css" href="styles/ddsmoothmenu/ddsmoothmenu.css" />
	<link rel="stylesheet" type="text/css" href="javascript/jQuery/colorbox/colorbox.css" />
	<link rel="stylesheet" type="text/css" href="styles/gritter/jquery.gritter.css" />

	<script src="javascript/jQuery/jquery.js"></script>
	<script src="javascript/jQuery/colorbox/jquery.colorbox-min.js"></script>
	<script src="javascript/ddsmoothmenu/ddsmoothmenu.js"></script>
	<script src="javascript/gritter/jquery.gritter.min.js"></script>
	<script src="javascript/hints/hints-menu.js"></script>
	<script src="javascript/inline-initializers/jquery-init.js"></script>
	<script src="javascript/inline-initializers/ddsmoothmenu-init.js"></script>
	<script src="javascript/inline-initializers/populate-web-storage.js"></script>
	<script src="javascript/inline-initializers/gritter-init.js"></script>
	<script src="javascript/inline-initializers/hints-menu-init.js"></script>

</head>
<body class="">
	<header>
		<!-- Header - Section 1 -->
		<div class="section">
			<div class="row">
				<div class="col text-center">
					<h1>
						<!-- <img src="images/coykillericon-50-38.png" alt="logo"/> -->
						<span class="material-icons md-48 align-middle text-danger">
						bug_report
						</span>
					OWASP Mutillidae II: Keep Calm and Pwn On
					</h1>
				</div>
			</div>
			<div class="row py-2">
				<div class="col text-center text-muted">
				<?php /* Note: $C_VERSION_STRING in index.php */
					echo $C_VERSION_STRING; ?>
				<span class="px-2"><?php echo $lSecurityLevelMessage; ?></span>
				<span class="px-2"><?php echo $lHintsMessage; ?></span>
				<span class="px-2"><?php echo $lAuthenticationStatusMessage ?></span>
				</div>
				
			</div>
		</div>
		<!-- Header - NavBar -->
		<div class="section">
			<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
				<div class="container-fluid">
				<button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
				<span class="navbar-toggler-icon"></span>
				</button>
					<div class="collapse navbar-collapse justify-content-center" id="navbarNav">
						<ul class="navbar-nav">
							<li class="nav-item">
								<a class="nav-link text-white" href="index.php?page=home.php&popUpNotificationCode=HPH0">Home</a>
							</li>
							<li class="nav-item">
							<?php
								if ($_SESSION['loggedin'] == 'True'){
									echo '<a class="nav-link text-white" href="index.php?do=logout">Logout</a>';
								} else {
									echo '<a class="nav-link text-white" href="index.php?page=login.php">Login/Register</a>';
								}// end if
							?>
							</li>
							<li class="nav-item">
							<?php
								if ($_SESSION['security-level'] == 0){
									echo '<a class="nav-link text-white" href="index.php?do=toggle-hints&page='.$lPage.'">Toggle Hints</a>';
								}// end if
							?>
							</li>
							<li class="nav-item">
								<a class="nav-link text-white" href="index.php?do=toggle-security&page=<?php echo $lPage?>">Toggle Security</a>
							</li>
							<li class="nav-item">
								<a class="nav-link text-white" href="index.php?do=toggle-enforce-ssl&page=<?php echo $lPage?>"><?php echo $lEnforceSSLLabel; ?></a>
							</li>
							<li class="nav-item">
								<a class="nav-link text-white" href="set-up-database.php">Reset DB</a>
							</li>
							<li class="nav-item">
								<a class="nav-link text-white" href="index.php?page=show-log.php">View Log</a>
							</li>
							<li class="nav-item">
								<a class="nav-link text-white" href="index.php?page=captured-data.php">View Captured Data</a>
							</li>
						</ul>
					</div>
					
				</div>
			</nav>
		</div>
	</header>
	<main>
				
<!-- Header - Sidebar -->
<div class="container-fluid">
    <div class="row flex-nowrap">
        <div class="col-auto col-md-3 col-xl-2 px-sm-2 px-0 bg-dark">
            <div class="d-flex flex-column align-items-center align-items-sm-start px-3 pt-2 text-white min-vh-100">
                <a href="/" class="d-flex align-items-center pb-3 mb-md-0 me-md-auto text-white text-decoration-none">
                    <span class="fs-5 d-none d-sm-inline">Owasp</span>
                </a>
                <ul class="nav nav-pills flex-column mb-sm-auto mb-0 align-items-center align-items-sm-start" id="menu">
					<?php require_once 'main-menu.php'; ?>
					<p class="text-white">_____________________</p>
                    <li class="nav-item">
						<span class="ms-1 d-none d-sm-inline">Want to help?</span>
						<form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_blank">
							<input type="hidden" name="cmd" value="_s-xclick">
							<input type="hidden" name="hosted_button_id" value="45R3YEXENU97S">
							<input type="image" src="https://www.paypalobjects.com/en_US/i/btn/btn_donate_LG.gif" name="submit" alt="Donate Today!">
							<img alt="" src="https://www.paypalobjects.com/en_US/i/scr/pixel.gif" width="1" height="1">
						</form>
                        
                    </li>
					
					<p class="text-white">_____________________</p>
                    <li>
                        <a href="http://www.youtube.com/user/webpwnized" target="_blank" data-bs-toggle="collapse" class="nav-link px-0 align-middle text-white">
							
							<span class="ms-1 d-none d-sm-inline">
								Video Tutorials
							</span>
							<span class="material-icons md-48 align-middle text-danger">
								ondemand_video
							</span>
						</a>
                    </li>
                    <li>
                        <a href="https://twitter.com/webpwnized" target="_blank" class="nav-link px-0 align-middle text-white">
							<span class="ms-1 d-none d-sm-inline">Announcements</span>
							<span class="material-icons md-48 align-middle text-primary">
								announcement
							</span>
							</a>
                    </li>
                    <li>
                        <a
						 	href="https://www.sans.org/reading-room/whitepapers/application/introduction-owasp-mutillidae-ii-web-pen-test-training-environment-34380"
							target="_blank"
							title="Whitepaper: Introduction to OWASP Mutillidae II Web Pen Test Training Environment" 
							data-bs-toggle="collapse" class="nav-link px-0 align-middle text-white"
						>
                            <span class="ms-1 d-none d-sm-inline">Getting Started</span>
							<span class="material-icons md-48 align-middle text-warning">
								assignment
							</span>
						</a>
                    </li>
                </ul>
                
            </div>
        </div>
        <div class="col py-3">

		<!-- Section - Content -->
