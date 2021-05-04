
<?php

	try{
		/* ------------------------------------------
		 * Constants used in application
		* ------------------------------------------ */
		require_once ('./includes/constants.php');

		/* We use the session on this page */
		if (session_status() == PHP_SESSION_NONE){
			session_start();
		}// end if

		if (!isset($_SESSION["security-level"])){
		    $_SESSION["security-level"] = 0;
		}// end if

		/* ------------------------------------------
		 * initialize custom error handler
		* ------------------------------------------ */
		require_once (__ROOT__.'/classes/CustomErrorHandler.php');
		if (!isset($CustomErrorHandler)){
			$CustomErrorHandler =
			new CustomErrorHandler(__ROOT__.'/owasp-esapi-php/src/', $_SESSION["security-level"]);
		}// end if

		/* ------------------------------------------
		 * initialize SQL Query Handler
		* ------------------------------------------ */
		require_once (__ROOT__.'/classes/SQLQueryHandler.php');
		$SQLQueryHandler = new SQLQueryHandler(__ROOT__."/owasp-esapi-php/src/", $_SESSION["security-level"]);

		/* ------------------------------------------
		 * initialize You Tube Video Handler Handler
		* ------------------------------------------ */
		require_once (__ROOT__.'/classes/YouTubeVideoHandler.php');
		$YouTubeVideoHandler = new YouTubeVideoHandler(__ROOT__."/owasp-esapi-php/src/", $_SESSION["security-level"]);

		if (isset($_REQUEST["level1HintIncludeFile"])) {
			$lIncludeFileKey = $_REQUEST["level1HintIncludeFile"];
		}else{
			$lIncludeFileKey = 52; // hints-not-found.inc;
		}// end if

		$lIncludeFileRecord = $SQLQueryHandler->getLevelOneHelpIncludeFile($lIncludeFileKey);

		if ($SQLQueryHandler->affected_rows()>0) {
			$lRecord = $lIncludeFileRecord->fetch_object();
			$lIncludeFile = $lRecord->level_1_help_include_file;
			$lIncludeFileDescription = $lRecord->level_1_help_include_file_description;
		}else{
			$lIncludeFile = 'hint-not-found.inc';
			$lIncludeFileDescription = 'Hint Not Found';
		}// end if

   	} catch (Exception $e) {
		echo $CustomErrorHandler->FormatError($e, $lQueryString);
   	}// end try;
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
	<head>
		<link rel="stylesheet" type="text/css" href="./styles/global-styles.css" />
		<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1">
		<title><?php echo $lIncludeFileDescription; ?></title>
	</head>
	<body>
		<table class="hint-table">
			<tr class="hint-header">
				<td><?php echo $lIncludeFileDescription; ?></td>
			</tr>
			<tr>
				<td class="hint-body">
					<?php include_once ('./includes/hints/'.$lIncludeFile); ?>
				</td>
			</tr>
		</table>
	</body>
</html>