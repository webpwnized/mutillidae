<?php

    require_once '../includes/ws-constants.php';
    
    class UnsupportedHttpMethodException extends Exception {
        public function __construct($message) {
            parent::__construct($message);
        }
    }

    class UnauthorizedOriginException extends Exception {
        public function __construct($message) {
            parent::__construct($message);
        }
    }

    function generateTransactionID() {
        // Generate a secure random hexadecimal transaction ID
        return bin2hex(random_bytes(16)); // 16-character random hex string
    }

    function populatePOSTSuperGlobal() {
        $lParameters = [];
        parse_str(file_get_contents('php://input'), $lParameters);
        $_POST = $lParameters + $_POST;
    }

    try {
        $lVerb = $_SERVER['REQUEST_METHOD'];
        $lDomain = $_SERVER['SERVER_NAME'];
        $lDomainParts = array_reverse(explode('.', $lDomain));
        $lParentDomain = $lDomainParts[1] . '.' . $lDomainParts[0];
        $lReturnData = true;

        // Validate origin against trusted origins
        $lOrigin = $_SERVER['HTTP_ORIGIN'] ?? '';
        if (!in_array($lOrigin, CORS_TRUSTED_ORIGINS)) {
            throw new UnauthorizedOriginException("Unauthorized Origin: $lOrigin");
        }

        // Populate $_POST if necessary for certain methods
        if (in_array($lVerb, ["PUT", "PATCH", "DELETE"])) {
            populatePOSTSuperGlobal();
        }

        // Retrieve max-age value from the request, defaulting to 600 seconds
        $lMaxAge = $_GET['acma'] ?? $_POST['acma'] ?? 600;

        // Get message from either GET or POST, defaulting to "Hello"
        $lMessageReceived = $_GET['message'] ?? $_POST['message'] ?? 'Hello';

        // Process based on HTTP method
        $lMethodMessage = '';
        switch ($lVerb) {
            case "OPTIONS":
                $lReturnData = false;
                break;
            case "GET":
                $lMethodMessage = "GET request received";
                break;
            case "POST":
                $lMethodMessage = "POST request processed";
                break;
            case "PUT":
                $lMethodMessage = "PUT request - resource created or updated";
                break;
            case "PATCH":
                $lMethodMessage = "PATCH request - partial update successful";
                break;
            case "DELETE":
                $lMethodMessage = "DELETE request - resource removed";
                break;
            default:
                throw new UnsupportedHttpMethodException("Unsupported HTTP method: $lVerb");
        }

        // Construct the final message
        $lMessageText = "Message received: " . $lMessageReceived . ". " . $lMethodMessage . ".";

        // Set CORS headers dynamically
        if ($lVerb == "OPTIONS" ||
            ($_GET['acao'] ?? '') == "True" || ($_POST['acao'] ?? '') == "True") {
            header("Access-Control-Allow-Origin: {$_SERVER['REQUEST_SCHEME']}://{$lParentDomain}");
        }

        if ($lVerb == "OPTIONS" ||
            ($_GET['acam'] ?? '') == "True" || ($_POST['acam'] ?? '') == "True") {
            header("Access-Control-Allow-Methods: OPTIONS, GET, POST, PUT, PATCH, DELETE");
        }

        // Apply the max-age header with the provided or default value
        if ($lVerb == "OPTIONS") {
            header("Access-Control-Max-Age: $lMaxAge");
            header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");
        }

        // Return JSON response if needed
        if ($lReturnData) {
            header(CONTENT_TYPE_JSON);
            echo json_encode([
                "TransactionID" => generateTransactionID(),
                "Message" => $lMessageText,
                "Method" => $lVerb,
                "Parameters" => [
                    "GET" => $_GET,
                    "POST" => $_POST
                ],
                "Max-Age" => $lMaxAge,
                "Timestamp" => date(DATE_TIME_FORMAT)
            ], JSON_PRETTY_PRINT);
        }
    } catch (Exception $e) {
        header(CONTENT_TYPE_JSON);
        header("Access-Control-Allow-Origin: {$_SERVER['REQUEST_SCHEME']}://{$lParentDomain}");
        echo json_encode([
            "TransactionID" => generateTransactionID(),
            "Error" => $e->getMessage(),
            "Method" => $lVerb,
            "Parameters" => [
                "GET" => $_GET,
                "POST" => $_POST
            ],
            "Timestamp" => date(DATE_TIME_FORMAT)
        ], JSON_PRETTY_PRINT);
    }
?>
