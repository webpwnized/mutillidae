<?php 
	 /* Known Vulnerabilities
	 * 
	 * SQL Injection, (Fix: Use Schematized Stored Procedures)
	 * Cross Site Scripting, (Fix: Encode all output)
	 * Cross Site Request Forgery, (Fix: Tokenize transactions)
	 * Denial of Service, (Fix: Truncate Log Queries)
	 * Improper Error Handling, (Fix: Employ custom error handler)
	 * SQL Exception, (Fix: Employ custom error handler)
	 */
	try {
		if (empty($lPage)){
			$currentFile = $_SERVER["PHP_SELF"];
			$parts = Explode('/', $currentFile);
			$lPage = $parts[count($parts) - 1];;
		}//end if
		$LogHandler->writeToLog("User visited: " . $lPage);	
	}catch (Exception $e){
		echo $CustomErrorHandler->FormatError($e, $query);
	}// end try
?>