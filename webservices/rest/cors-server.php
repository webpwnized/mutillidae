<?php
    if (session_status() == PHP_SESSION_NONE){
        session_start();
    }// end if

    if (!isset($_SESSION["security-level"])){
        $_SESSION["security-level"] = 0;
    }// end if

	/* ------------------------------------------
	 * Constants used in application
	 * ------------------------------------------ */
	require_once('../../includes/constants.php');
	require_once('../../includes/minimum-class-definitions.php');

	function populatePOSTSuperGlobal(){
		$lParameters = Array();
		parse_str(file_get_contents('php://input'), $lParameters);
		$_POST = $lParameters + $_POST;
	}// end function populatePOSTArray

	function getPOSTParameter($pParameter, $lRequired){
		if(isset($_POST[$pParameter])){
			return $_POST[$pParameter];
		}else{
			if($lRequired){
				throw new Exception("POST parameter ".$pParameter." is required");
			}else{
				return "";
			}
		}// end if isset
	}// end function validatePOSTParameter

	function jsonEncodeQueryResults($pQueryResult){
		$lDataRows = array();
		while ($lDataRow = mysqli_fetch_assoc($pQueryResult)) {
			$lDataRows[] = $lDataRow;
		}// end while

		return json_encode($lDataRows);
	}//end function jsonEncodeQueryResults

	try {

	    $lVerb = $_SERVER['REQUEST_METHOD'];
	    $lReturnData = True;

	    switch($lVerb){
	        case "OPTIONS":
	            $lReturnData = False;
                break;
	        case "GET":
                break;
	        case "POST"://create
                break;
	        case "PUT":	//create or update
	        case "PATCH":	//create or update
	        case "DELETE":
	            /* $_POST array is not auto-populated for PUT,PATCH,DELETE method. Parse input into an array. */
	            populatePOSTSuperGlobal();
            break;
	        default:
	            throw new Exception("Could not understand HTTP REQUEST_METHOD verb");
            break;
	    }// end switch

	    if ($lVerb == "OPTIONS" ||
	        (isset($_GET['acao']) && $_GET['acao']=="True") ||
	        (isset($_POST['acao']) && $_POST['acao']=="True"))
	    {
	        header("Access-Control-Allow-Origin: {$_SERVER['REQUEST_SCHEME']}://mutillidae.local");
	    }

	    if ($lVerb == "OPTIONS" ||
	        (isset($_GET['acam']) && $_GET['acam']=="True") ||
	        (isset($_POST['acam']) && $_POST['acam']=="True"))
	    {
	        header("Access-Control-Allow-Methods: OPTIONS,GET,POST,PUT,PATCH,DELETE");
	    }

	    if ($lVerb == "OPTIONS" ||
	        (isset($_GET['acma']) && $_GET['acma']=="True") ||
	        (isset($_POST['acma']) && $_POST['acma']=="True"))
	    {
	        header("Access-Control-Max-Age: 5");
	    }

    	switch ($_SESSION["security-level"]){
    	    case "0": // This code is insecure. No input validation is performed.
    	        $lProtectAgainstCommandInjection=FALSE;
    	        $lProtectAgainstXSS = FALSE;
    	        break;

    	    case "1": // This code is insecure. No input validation is performed.
    	        $lProtectAgainstCommandInjection=FALSE;
    	        $lProtectAgainstXSS = FALSE;
    	        break;

    	    case "2":
    	    case "3":
    	    case "4":
    	    case "5": // This code is fairly secure
    	        $lProtectAgainstCommandInjection=TRUE;
    	        $lProtectAgainstXSS = TRUE;
    	        break;
    	}// end switch

    	if (isset($_GET["message"])) {
    	    $lMessage = $_GET["message"];
    	} elseif (isset($_POST["message"])){
    	    $lMessage = $_POST["message"];
    	}else{
    	    $lMessage="Hello";
    	}//end if

    	if ($lProtectAgainstXSS) {
    	    /* Protect against XSS by output encoding */
    	    $lMessageText = $Encoder->encodeForHTML($lMessage);
    	}else{
    	    $lMessageText = $lMessage; 		//allow XSS by not encoding output
    	}//end if

    	if ($lProtectAgainstCommandInjection) {
    	    $LogHandler->writeToLog("Executed PHP command: echo " . $lMessageText);
    	}else{
    	    $lMessage = shell_exec("echo -n " . $lMessage);
    	    $LogHandler->writeToLog("Executed operating system command: echo " . $lMessageText);
    	}//end if

    	if ($lReturnData) {
    	    echo '[';
    	    echo '{"Message":'.json_encode($lMessage).'},';
    	    echo '{"Method":'.json_encode($lVerb)."},";
    	    echo '{"Parameters":[';
    	    echo '{"GET":'.json_encode($_GET)."},";
    	    echo '{"POST":'.json_encode($_POST)."}";
    	    echo ']}';
    	    echo ']';
    	}

	}catch(Exception $e){
	    header("Access-Control-Allow-Origin: {$_SERVER['REQUEST_SCHEME']}://mutillidae.local");
	    echo $CustomErrorHandler->FormatError($e, "Error setting up configuration on page html5-storage.php");
	}// end try
?>