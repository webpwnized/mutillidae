<?php
    // Required files
    require_once '../../includes/constants.php';
    require_once '../../classes/SQLQueryHandler.php';
    require_once '../../classes/CustomErrorHandler.php';
    require_once '../includes/ws-constants.php';

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
        // Initialize handlers
        $SQLQueryHandler = new SQLQueryHandler(SECURITY_LEVEL_INSECURE);
        $lSecurityLevel = $SQLQueryHandler->getSecurityLevelFromDB();
        $SQLQueryHandler->setSecurityLevel($lSecurityLevel);
        $CustomErrorHandler = new CustomErrorHandler($lSecurityLevel);

        // Set CORS headers
        header(CORS_ACCESS_CONTROL_ALLOW_ORIGIN);
        header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS'); // Allowed methods
        header('Access-Control-Allow-Headers: Content-Type, Authorization'); // Specify allowed headers
        header('Access-Control-Expose-Headers: Authorization'); // Expose headers if needed
        header(CONTENT_TYPE_JSON);

        // Handle preflight requests (OPTIONS)
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            header(CORS_ACCESS_CONTROL_MAX_AGE); // Cache the preflight response for 600 seconds (10 minutes)
            http_response_code(RESPONSE_CODE_NO_CONTENT); // No Content
            exit();
        }

        switch ($lSecurityLevel) {
            default:
            case SECURITY_LEVEL_INSECURE:
                $lRequireAuthentication = false;
                break;
            case SECURITY_LEVEL_MEDIUM:
            case 2:
            case 3:
            case 4:
            case SECURITY_LEVEL_SECURE:
                $lRequireAuthentication = true;
                break;
        }

        // Shared: Include the shared JWT token authentication function
        require_once '../includes/ws-authenticate-jwt-token.php';

        // Shared: Authenticate the user if required
        if ($lRequireAuthentication) {
            try {
                $lDecodedToken = authenticateJWTToken(); // Authenticate using the shared function
            } catch (InvalidTokenException $e) {
                http_response_code(RESPONSE_CODE_UNAUTHORIZED);
                header(CONTENT_TYPE_JSON);
                echo json_encode(['error' => 'Unauthorized', 'details' => $e->getMessage()]);
                exit;
            }
        }

        $lVerb = $_SERVER['REQUEST_METHOD'];

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

                    http_response_code(RESPONSE_CODE_OK);
                    header(CONTENT_TYPE_JSON);
                    $lArrayResponse['SecurityLevel'] = $lSecurityLevel;
                    $lArrayResponse['Timestamp'] = date(DATE_TIME_FORMAT);
                    echo json_encode($lArrayResponse, JSON_PRETTY_PRINT);
                    exit(); // Exit after response

                } else {
                    http_response_code(RESPONSE_CODE_BAD_REQUEST);
                    header(CONTENT_TYPE_JSON);
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
                    http_response_code(RESPONSE_CODE_CONFLICT);

                } else {
                    $lQueryResult = $SQLQueryHandler->insertNewUserAccount(
                        $lUsername, $lAccountPassword, $lAccountFirstName, $lAccountLastName, $lAccountSignature
                    );

                    if ($lQueryResult) {
                        $lArrayResponse['Result'] = "Inserted account '$lUsername'";
                        $lArrayResponse['Success'] = true;
                        http_response_code(RESPONSE_CODE_CREATED); // Created
                    } else {
                        $lArrayResponse['Result'] = "Failed to insert account '$lUsername'";
                        $lArrayResponse['Success'] = false;
                        http_response_code(RESPONSE_CODE_INTERNAL_SERVER_ERROR); // Internal Server Error
                    }
                }

                header(CONTENT_TYPE_JSON);
                $lArrayResponse['SecurityLevel'] = $lSecurityLevel;
                $lArrayResponse['Timestamp'] = date(DATE_TIME_FORMAT);
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
            
                // New boolean parameters to decide whether to update client_id and client_secret
                $lUpdateClientID = filter_var(getPOSTParameter("update_client_id", false), FILTER_VALIDATE_BOOLEAN);
                $lUpdateClientSecret = filter_var(getPOSTParameter("update_client_secret", false), FILTER_VALIDATE_BOOLEAN);
            
                $lArrayResponse = [];
            
                if ($SQLQueryHandler->accountExists($lUsername)) {
                    // Update the existing account
                    $lQueryResult = $SQLQueryHandler->updateUserAccount(
                        $lUsername,
                        $lAccountPassword,
                        $lAccountFirstName,
                        $lAccountLastName,
                        $lAccountSignature,
                        $lUpdateClientID,
                        $lUpdateClientSecret
                    );
            
                    if ($lQueryResult > 0) {
                        $lArrayResponse['Result'] = "Updated account '$lUsername'.";
                        $lArrayResponse['RowsAffected'] = $lQueryResult;
                        $lArrayResponse['Success'] = true;
                        http_response_code(RESPONSE_CODE_OK); // OK
                    } else {
                        $lArrayResponse['Result'] = "No rows were updated for account '$lUsername'.";
                        $lArrayResponse['RowsAffected'] = 0;
                        $lArrayResponse['Success'] = false;
                        http_response_code(RESPONSE_CODE_NOT_MODIFIED); // Not Modified
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
                        http_response_code(RESPONSE_CODE_CREATED);
                    } else {
                        $lArrayResponse['Result'] = "Failed to insert account '$lUsername'.";
                        $lArrayResponse['RowsAffected'] = 0;
                        $lArrayResponse['Success'] = false;
                        http_response_code(RESPONSE_CODE_INTERNAL_SERVER_ERROR); // Internal Server Error
                    }
                }
            
                header(CONTENT_TYPE_JSON);
                $lArrayResponse['SecurityLevel'] = $lSecurityLevel;
                $lArrayResponse['Timestamp'] = date(DATE_TIME_FORMAT);
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
                            http_response_code(RESPONSE_CODE_OK); // OK
                        } else {
                            $lArrayResponse['Result'] = "Attempted to delete account '$lUsername', but the result returned was '$lQueryResult'.";
                            $lArrayResponse['Success'] = false;
                            http_response_code(RESPONSE_CODE_INTERNAL_SERVER_ERROR); // Internal Server Error
                        }
                    } else {
                        $lArrayResponse['Result'] = "Could not authenticate account '$lUsername'. Password incorrect.";
                        $lArrayResponse['Success'] = false;
                        http_response_code(RESPONSE_CODE_UNAUTHORIZED); // Unauthorized
                    }
                } else {
                    $lArrayResponse['Result'] = "User '$lUsername' does not exist.";
                    $lArrayResponse['Success'] = false;
                    http_response_code(RESPONSE_CODE_NOT_FOUND); // Not Found
                }

                header(CONTENT_TYPE_JSON);
                $lArrayResponse['SecurityLevel'] = $lSecurityLevel;
                $lArrayResponse['Timestamp'] = date(DATE_TIME_FORMAT);
                echo json_encode($lArrayResponse, JSON_PRETTY_PRINT);
                exit(); // Exit after response

            default:
                http_response_code(RESPONSE_CODE_METHOD_NOT_ALLOWED);
                header('Allow: GET, POST, PUT, DELETE, OPTIONS');
                header(CONTENT_TYPE_JSON);
                echo json_encode(["error" => "Method not allowed", "SecurityLevel" => $lSecurityLevel], JSON_PRETTY_PRINT);
                exit(); // Exit after response
        }
    } catch (Exception $e) {
        http_response_code(RESPONSE_CODE_INTERNAL_SERVER_ERROR);
        header(CONTENT_TYPE_JSON);
        echo $CustomErrorHandler->FormatErrorJSON($e, "Unable to process request to web service ws-user-account");
        exit(); // Exit after response
    }
?>
