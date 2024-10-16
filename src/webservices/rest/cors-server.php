<?php
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }

    if (!isset($_SESSION["security-level"])) {
        $_SESSION["security-level"] = 0;
    }

    require_once '../../includes/constants.php';
    require_once '../../includes/minimum-class-definitions.php';

    class UnsupportedHttpMethodException extends Exception {
        public function __construct($message) {
            parent::__construct($message);
        }
    }

    class MissingPostParameterException extends Exception {
        public function __construct($parameter) {
            parent::__construct("POST parameter " . $parameter . " is required");
        }
    }

    function populatePOSTSuperGlobal() {
        $lParameters = [];
        parse_str(file_get_contents('php://input'), $lParameters);
        $_POST = $lParameters + $_POST;
    }

    function getPOSTParameter($pParameter, $lRequired) {
        if (isset($_POST[$pParameter])) {
            return $_POST[$pParameter];
        } else {
            if ($lRequired) {
                throw new MissingPostParameterException($pParameter);
            } else {
                return "";
            }
        }
    }

    function jsonEncodeQueryResults($pQueryResult) {
        $lDataRows = [];
        while ($lDataRow = mysqli_fetch_assoc($pQueryResult)) {
            $lDataRows[] = $lDataRow;
        }
        return json_encode($lDataRows);
    }

    try {
        $lVerb = $_SERVER['REQUEST_METHOD'];
        $lDomain = $_SERVER['SERVER_NAME'];
        $lDomainParts = array_reverse(explode('.', $lDomain));
        $lParentDomain = $lDomainParts[1] . '.' . $lDomainParts[0];
        $lReturnData = true;

        if (in_array($lVerb, ["PUT", "PATCH", "DELETE"])) {
            populatePOSTSuperGlobal();
        }

        switch ($lVerb) {
            case "OPTIONS":
                $lReturnData = false;
                break;
            case "GET":
                $lMessage = "GET request received";
                break;
            case "POST":
                $lMessage = "POST request processed";
                break;
            case "PUT":
                $lMessage = "PUT request - resource created or updated";
                break;
            case "PATCH":
                $lMessage = "PATCH request - partial update successful";
                break;
            case "DELETE":
                $lMessage = "DELETE request - resource removed";
                break;
            default:
                throw new UnsupportedHttpMethodException("Unsupported HTTP method: $lVerb");
        }

        if ($lVerb == "OPTIONS" ||
            (isset($_GET['acao']) && $_GET['acao'] == "True") ||
            (isset($_POST['acao']) && $_POST['acao'] == "True")) {
            header("Access-Control-Allow-Origin: {$_SERVER['REQUEST_SCHEME']}://{$lParentDomain}");
        }

        if ($lVerb == "OPTIONS" ||
            (isset($_GET['acam']) && $_GET['acam'] == "True") ||
            (isset($_POST['acam']) && $_POST['acam'] == "True")) {
            header("Access-Control-Allow-Methods: OPTIONS, GET, POST, PUT, PATCH, DELETE");
        }

        if ($lVerb == "OPTIONS" ||
            (isset($_GET['acma']) && $_GET['acma'] == "True") ||
            (isset($_POST['acma']) && $_POST['acma'] == "True")) {
            header("Access-Control-Max-Age: 5");
        }

        $lMessageText = isset($_POST["message"]) ? $_POST["message"] : "Hello";

		$lMessageText = "Hello " . $lMessageText . ". " . $lMessage . ".";

        if ($lReturnData) {
            header('Content-Type: application/json');
            echo json_encode([
                "Message" => $lMessageText,
                "Method" => $lVerb,
                "Parameters" => [
                    "GET" => $_GET,
                    "POST" => $_POST
                ]
            ]);
        }
    } catch (Exception $e) {
        header("Access-Control-Allow-Origin: {$_SERVER['REQUEST_SCHEME']}://{$lParentDomain}");
        echo $CustomErrorHandler->FormatError($e, "Error in handling request");
    }
?>
