<?php
    // Required files
    require_once '../../includes/constants.php';
    require_once '../../classes/SQLQueryHandler.php';
    require_once '../../classes/CustomErrorHandler.php';

    class MissingPostParameterException extends Exception {
        public function __construct($parameter) {
            parent::__construct("POST parameter " . $parameter . " is required");
        }
    }

    class UnsupportedHttpVerbException extends Exception {
        public function __construct($verb) {
            parent::__construct("Unsupported HTTP verb: " . $verb);
        }
    }

    function populatePOSTSuperGlobal(){
        $lParameters = array();
        parse_str(file_get_contents('php://input'), $lParameters);
        $_POST = $lParameters + $_POST;
    }

    function getPOSTParameter($pParameter, $lRequired){
        if(isset($_POST[$pParameter])){
            return $_POST[$pParameter];
        }else{
            if($lRequired){
                throw new MissingPostParameterException($pParameter);
            }else{
                return "";
            }
        }
    }

    function jsonEncodeQueryResults($pQueryResult){
        $lDataRows = array();
        while ($lDataRow = mysqli_fetch_assoc($pQueryResult)) {
            $lDataRows[] = $lDataRow;
        }
        return json_encode($lDataRows);
    }

    try {
		$lContentTypeJSON = 'Content-Type: application/json';

        // Initialize handlers
        $SQLQueryHandler = new SQLQueryHandler(0);
        $lSecurityLevel = $SQLQueryHandler->getSecurityLevelFromDB();
        $CustomErrorHandler = new CustomErrorHandler($lSecurityLevel);

        $lVerb = $_SERVER['REQUEST_METHOD'];
        $lOrigin = isset($_SERVER['HTTP_ORIGIN']) ? $_SERVER['HTTP_ORIGIN'] : '*';

        // Set headers
        header('Access-Control-Allow-Origin: ' . $lOrigin);
        header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
        header('Access-Control-Allow-Headers: Content-Type, Authorization');

        switch ($lVerb) {
            case "GET":
                if (isset($_GET['username'])) {
                    $lUsername = $_GET['username'] ?? '';

                    if ($lUsername === "*") {
                        $lQueryResult = $SQLQueryHandler->getUsernames();
                    } else {
                        $lQueryResult = $SQLQueryHandler->getNonSensitiveAccountInformation($lUsername);
                    }

                    $lArrayResponse = [];
                    if ($lQueryResult->num_rows > 0) {
                        $lArrayAccounts = [];
                        while ($row = $lQueryResult->fetch_assoc()) {
                            $lArrayAccounts[] = $row;
                        }
                        $lArrayResponse['Result'] = ['Accounts' => $lArrayAccounts];
                    } else {
                        $lArrayResponse['Result'] = "User '$lUsername' does not exist";
                    }

                    http_response_code(200);
                    header($lContentTypeJSON); 
                    $lArrayResponse['SecurityLevel'] = $lSecurityLevel;
                    echo json_encode($lArrayResponse, JSON_PRETTY_PRINT);
                    exit(); // Exit after response

                } else {
                    http_response_code(400);
                    header($lContentTypeJSON);
                    echo json_encode(["error" => "Username parameter is required", "SecurityLevel" => $lSecurityLevel], JSON_PRETTY_PRINT);
                    exit(); // Exit after response
                }

            case "POST":
                $lUsername = getPOSTParameter("username", true);
                $lAccountPassword = getPOSTParameter("password", true);
                $lAccountFirstName = getPOSTParameter("firstname", true);
                $lAccountLastName = getPOSTParameter("lastname", true);
                $lAccountSignature = getPOSTParameter("signature", false);

                $lArrayResponse = [];

                if ($SQLQueryHandler->accountExists($lUsername)) {
                    $lArrayResponse['Result'] = "Account '$lUsername' already exists";
                    $lArrayResponse['Success'] = false;
                    http_response_code(409); // Conflict

                } else {
                    $lQueryResult = $SQLQueryHandler->insertNewUserAccount(
                        $lUsername, $lAccountPassword, $lAccountFirstName, $lAccountLastName, $lAccountSignature
                    );

                    if ($lQueryResult) {
                        $lArrayResponse['Result'] = "Inserted account '$lUsername'";
                        $lArrayResponse['Success'] = true;
                        http_response_code(201); // Created
                    } else {
                        $lArrayResponse['Result'] = "Failed to insert account '$lUsername'";
                        $lArrayResponse['Success'] = false;
                        http_response_code(500); // Internal Server Error
                    }
                }

                header($lContentTypeJSON);
                $lArrayResponse['SecurityLevel'] = $lSecurityLevel;
                echo json_encode($lArrayResponse, JSON_PRETTY_PRINT);
                exit(); // Exit after response

            case "PUT": // create or update
                /* $_POST array is not auto-populated for PUT method. Parse input into an array. */
                populatePOSTSuperGlobal();

                $lUsername = getPOSTParameter("username", true);
                $lAccountPassword = getPOSTParameter("password", true);
                $lAccountFirstName = getPOSTParameter("firstname", true);
                $lAccountLastName = getPOSTParameter("lastname", true);
                $lAccountSignature = getPOSTParameter("signature", false);

                $lArrayResponse = [];

                if ($SQLQueryHandler->accountExists($lUsername)) {
                    // Update the existing account
                    $lQueryResult = $SQLQueryHandler->updateUserAccount(
                        $lUsername,
                        $lAccountPassword,
                        $lAccountFirstName,
                        $lAccountLastName,
                        $lAccountSignature,
                        false
                    );

                    if ($lQueryResult > 0) {
                        $lArrayResponse['Result'] = "Updated account '$lUsername'.";
                        $lArrayResponse['RowsAffected'] = $lQueryResult;
                        $lArrayResponse['Success'] = true;
                        http_response_code(200); // OK
                    } else {
                        $lArrayResponse['Result'] = "No rows were updated for account '$lUsername'.";
                        $lArrayResponse['RowsAffected'] = 0;
                        $lArrayResponse['Success'] = false;
                        http_response_code(304); // Not Modified
                    }
                } else {
                    // Insert a new account
                    $lQueryResult = $SQLQueryHandler->insertNewUserAccount(
                        $lUsername,
                        $lAccountPassword,
                        $lAccountFirstName,
                        $lAccountLastName,
                        $lAccountSignature
                    );

                    if ($lQueryResult > 0) {
                        $lArrayResponse['Result'] = "Inserted account '$lUsername'.";
                        $lArrayResponse['RowsAffected'] = $lQueryResult;
                        $lArrayResponse['Success'] = true;
                        http_response_code(201); // Created
                    } else {
                        $lArrayResponse['Result'] = "Failed to insert account '$lUsername'.";
                        $lArrayResponse['RowsAffected'] = 0;
                        $lArrayResponse['Success'] = false;
                        http_response_code(500); // Internal Server Error
                    }
                }

                header($lContentTypeJSON);
                $lArrayResponse['SecurityLevel'] = $lSecurityLevel;
                echo json_encode($lArrayResponse, JSON_PRETTY_PRINT);
                exit(); // Exit after response

            case "DELETE":
                /* $_POST array is not auto-populated for DELETE method. Parse input into an array. */
                populatePOSTSuperGlobal();

                $lUsername = getPOSTParameter("username", true);
                $lAccountPassword = getPOSTParameter("password", true);

                $lArrayResponse = [];

                if ($SQLQueryHandler->accountExists($lUsername)) {
                    if ($SQLQueryHandler->authenticateAccount($lUsername, $lAccountPassword)) {
                        $lQueryResult = $SQLQueryHandler->deleteUser($lUsername);

                        if ($lQueryResult) {
                            $lArrayResponse['Result'] = "Deleted account '$lUsername'.";
                            $lArrayResponse['Success'] = true;
                            http_response_code(200); // OK
                        } else {
                            $lArrayResponse['Result'] = "Attempted to delete account '$lUsername', but the result returned was '$lQueryResult'.";
                            $lArrayResponse['Success'] = false;
                            http_response_code(500); // Internal Server Error
                        }
                    } else {
                        $lArrayResponse['Result'] = "Could not authenticate account '$lUsername'. Password incorrect.";
                        $lArrayResponse['Success'] = false;
                        http_response_code(401); // Unauthorized
                    }
                } else {
                    $lArrayResponse['Result'] = "User '$lUsername' does not exist.";
                    $lArrayResponse['Success'] = false;
                    http_response_code(404); // Not Found
                }

                header($lContentTypeJSON);
                $lArrayResponse['SecurityLevel'] = $lSecurityLevel;
                echo json_encode($lArrayResponse, JSON_PRETTY_PRINT);
                exit(); // Exit after response

            default:
                http_response_code(405);
                header('Allow: GET, POST, PUT, DELETE, OPTIONS');
                header($lContentTypeJSON);
                echo json_encode(["error" => "Method not allowed", "SecurityLevel" => $lSecurityLevel], JSON_PRETTY_PRINT);
                exit(); // Exit after response
        }
    } catch (Exception $e) {
        http_response_code(500);
        header($lContentTypeJSON);
        echo $CustomErrorHandler->FormatErrorJSON($e, "Unable to process request to web service ws-user-account");
        exit(); // Exit after response
    }
?>
