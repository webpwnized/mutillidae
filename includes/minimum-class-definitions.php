<?php
		/* ------------------------------------------
		 * initialize OWASP ESAPI for PHP
		 * ------------------------------------------ */
	    require_once (__ROOT__.'/owasp-esapi-php/src/ESAPI.php');
		if (!isset($ESAPI)){
			$ESAPI = new ESAPI((__ROOT__.'/owasp-esapi-php/src/ESAPI.xml'));
			$Encoder = $ESAPI->getEncoder();
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
	 	* initialize log error handler
	 	* ------------------------------------------ */
	    require_once (__ROOT__.'/classes/LogHandler.php');
		$LogHandler = new LogHandler(__ROOT__.'/owasp-esapi-php/src/', $_SESSION["security-level"]);
		
		/* ------------------------------------------
	 	* initialize SQL Query Handler
	 	* ------------------------------------------ */
		require_once (__ROOT__.'/classes/SQLQueryHandler.php');
		$SQLQueryHandler = new SQLQueryHandler(__ROOT__."/owasp-esapi-php/src/", $_SESSION["security-level"]);
?>