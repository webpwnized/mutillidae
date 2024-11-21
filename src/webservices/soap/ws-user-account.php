<?php
    // Define a dedicated exception class for missing parameters
    class MissingParameterException extends Exception {}

    /* ------------------------------------------
     * Constants used in application
    * ------------------------------------------ */
    require_once '../../includes/constants.php';
    require_once '../../classes/SQLQueryHandler.php';
    require_once '../../classes/EncodingHandler.php';
    require_once '../../classes/LogHandler.php';
    require_once '../includes/ws-constants.php';

    // Pull in the NuSOAP code
    require_once './lib/nusoap.php';

    $SQLQueryHandler = new SQLQueryHandler(0);
    $lSecurityLevel = $SQLQueryHandler->getSecurityLevelFromDB();
    $LogHandler = new LogHandler($lSecurityLevel);
    $Encoder = new EncodingHandler();

    $lServerName = $_SERVER['SERVER_NAME'];

    // Construct the full URL to the documentation
    $lDocumentationURL = "http://{$lServerName}/webservices/soap/docs/soap-services.html";

    // Create the SOAP server instance
    $lSOAPWebService = new soap_server();

    // Initialize WSDL support
    $lSOAPWebService->configureWSDL('ws-user-account', 'urn:ws-user-account');

    // Define a complex type for the response
    $lSOAPWebService->wsdl->addComplexType(
        'UserAccountResponse',
        'complexType',
        'struct',
        'all',
        '',
        array(
            'message' => array('name' => 'message', 'type' => 'xsd:string'),
            'securityLevel' => array('name' => 'securityLevel', 'type' => 'xsd:string'),
            'timestamp' => array('name' => 'timestamp', 'type' => 'xsd:string'),
            'output' => array('name' => 'output', 'type' => 'xsd:string')
        )
    );

    // Register the SOAP methods
    $methods = [
        'getUser' => [
            'params' => ['username' => 'xsd:string'],
            'doc' => "Fetches user information if the user exists, otherwise returns an error message. For detailed documentation, visit: {$lDocumentationURL}"
        ],
        'registerUser' => [
            'params' => [
                'username' => 'xsd:string',
                'password' => 'xsd:string',
                'firstname' => 'xsd:string',
                'lastname' => 'xsd:string',
                'signature' => 'xsd:string'
            ],
            'doc' => "Creates new user account. For detailed documentation, visit: {$lDocumentationURL}"
        ],
        'updateUser' => [
            'params' => [
                'username' => 'xsd:string',
                'password' => 'xsd:string',
                'firstname' => 'xsd:string',
                'lastname' => 'xsd:string',
                'signature' => 'xsd:string'
            ],
            'doc' => "If account exists, updates existing user account else creates new user account. For detailed documentation, visit: {$lDocumentationURL}"
        ],
        'deleteUser' => [
            'params' => [
                'username' => 'xsd:string',
                'password' => 'xsd:string'
            ],
            'doc' => "If account exists, deletes user account. For detailed documentation, visit: {$lDocumentationURL}"
        ]
    ];

    foreach ($methods as $method => $details) {
        $lSOAPWebService->register(
            $method,
            $details['params'],
            array('return' => 'tns:UserAccountResponse'),
            'urn:ws-user-account',
            "urn:ws-user-account#$method",
            'rpc',
            'encoded',
            $details['doc']
        );
    }

    /**
     * Function: authenticateRequest
     * Handles request authentication and CORS headers.
     * 
     * @param int $lSecurityLevel The security level.
     * @throws InvalidTokenException If the authentication fails.
     */
    function authenticateRequest($lSecurityLevel) {
        // Set CORS headers
        header(CORS_ACCESS_CONTROL_ALLOW_ORIGIN);
        header('Access-Control-Allow-Methods: POST, OPTIONS'); // Allowed methods
        header('Access-Control-Allow-Headers: Content-Type, Authorization'); // Specify allowed headers
        header('Access-Control-Expose-Headers: Authorization'); // Expose headers if needed
        header(CONTENT_TYPE_XML); // Set content type as XML

        // Handle preflight requests (OPTIONS)
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            header(CORS_ACCESS_CONTROL_MAX_AGE); // Cache the preflight response for 600 seconds (10 minutes)
            http_response_code(RESPONSE_CODE_NO_CONTENT); // No Content
            exit();
        }

        // Allow only POST requests
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(RESPONSE_CODE_METHOD_NOT_ALLOWED);
            header(CONTENT_TYPE_XML);
            echo ERROR_MESSAGE_METHOD_NOT_ALLOWED;
            exit();
        }

        // Shared: Include the shared JWT token authentication function
        require_once '../includes/ws-authenticate-jwt-token.php';

        // Shared: Authenticate the user if required
        if ($lSecurityLevel >= SECURITY_LEVEL_MEDIUM) {
            try {
                $lDecodedToken = authenticateJWTToken(); // Authenticate using the shared function
            } catch (InvalidTokenException $e) {
                http_response_code(RESPONSE_CODE_UNAUTHORIZED);
                header(CONTENT_TYPE_XML);
                echo ERROR_MESSAGE_UNAUTHORIZED_PREFIX . 'Unauthorized: ' . htmlspecialchars($e->getMessage()) . ERROR_MESSAGE_UNAUTHORIZED_SUFFIX;
                exit();
            }
        }
    }

    function doXMLEncodeQueryResults($pQueryResult, $pEncodeOutput) {
        global $Encoder;
        $lResults = "<accounts>";

        while ($row = $pQueryResult->fetch_object()) {
            $lUsername = $pEncodeOutput ? $Encoder->encodeForHTML($row->username) : $row->username;

            // Safely check and handle undefined properties
            $lFirstname = isset($row->firstname) ? $row->firstname : '';
            $lLastname = isset($row->lastname) ? $row->lastname : '';
            $lFirstname = $pEncodeOutput ? $Encoder->encodeForHTML($lFirstname) : $lFirstname;
            $lLastname = $pEncodeOutput ? $Encoder->encodeForHTML($lLastname) : $lLastname;

            $lResults .= "<account>";
            $lResults .= "<username>{$lUsername}</username>";
            $lResults .= "<firstname>{$lFirstname}</firstname>";
            $lResults .= "<lastname>{$lLastname}</lastname>";

            if (isset($row->mysignature)) {
                $lSignature = $pEncodeOutput ? $Encoder->encodeForHTML($row->mysignature) : $row->mysignature;
                $lResults .= "<signature>{$lSignature}</signature>";
            }

            $lResults .= "</account>";
        }

        $lResults .= "</accounts>";

        return $lResults;
    }

    function xmlEncodeQueryResults($pUsername, $pEncodeOutput) {
        global $SQLQueryHandler;

        // Fetch query results based on the username
        if ($pUsername == "*") {
            // List all accounts
            $lQueryResult = $SQLQueryHandler->getUsernames();
        } else {
            // Lookup specific user account
            $lQueryResult = $SQLQueryHandler->getNonSensitiveAccountInformation($pUsername);
        }

        // Check if the query returned valid results
        if ($lQueryResult && $lQueryResult->num_rows > 0) {
            return doXMLEncodeQueryResults($lQueryResult, $pEncodeOutput);
        } else {
            // Return a message if no user is found
            return "<accounts><message>User {$pUsername} does not exist</message></accounts>";
        }
    }

    function assertParameter($pParameter) {
        if(strlen($pParameter) == 0 || !isset($pParameter)){
            throw new MissingParameterException("Parameter ".$pParameter." is required");
        }
    }

    // Define the SOAP method implementations

    function getUser($pUsername) {
        global $lSecurityLevel, $LogHandler, $lEncodeOutput;

        try {
            authenticateRequest($lSecurityLevel);

            assertParameter($pUsername);

            $lResults = xmlEncodeQueryResults($pUsername, $lEncodeOutput);
            $lTimestamp = date(DATE_TIME_FORMAT);

            $lResponse = array(
                'message' => "User data fetched successfully",
                'securityLevel' => $lSecurityLevel,
                'timestamp' => $lTimestamp,
                'output' => new soapval('output', 'xsd:anyType', $lResults)
            );

            $LogHandler->writeToLog("ws-user-account.php: Fetched user-information for: {$pUsername}");
            return $lResponse;

        } catch (Exception $e) {
            throw new SoapFault("Server", "Error in getUser: " . $e->getMessage());
        }
    }

    function registerUser($pUsername, $pPassword, $pFirstname, $pLastname, $pSignature) {
        global $lSecurityLevel, $LogHandler, $SQLQueryHandler;

        try {
            authenticateRequest($lSecurityLevel);

            assertParameter($pUsername);
            assertParameter($pPassword);
            assertParameter($pFirstname);
            assertParameter($pLastname);
            assertParameter($pSignature);

            $lTimestamp = date(DATE_TIME_FORMAT);

            if ($SQLQueryHandler->accountExists($pUsername)) {
                $lResponse = array(
                    'message' => "User {$pUsername} already exists",
                    'securityLevel' => $lSecurityLevel,
                    'timestamp' => $lTimestamp,
                    'output' => new soapval('output', 'xsd:anyType', '')
                );
                return $lResponse;
            } else {
                $SQLQueryHandler->insertNewUserAccount($pUsername, $pPassword, $pFirstname, $pLastname, $pSignature);
                $lResponse = array(
                    'message' => "Inserted account {$pUsername}",
                    'securityLevel' => $lSecurityLevel,
                    'timestamp' => $lTimestamp,
                    'output' => new soapval('output', 'xsd:anyType', '')
                );
                $LogHandler->writeToLog("ws-user-account.php: Inserted account {$pUsername}");
                return $lResponse;
            }

        } catch (Exception $e) {
            throw new SoapFault("Server", "Error in registerUser: " . $e->getMessage());
        }
    }

    function updateUser($pUsername, $pPassword, $pFirstname, $pLastname, $pSignature) {
        global $lSecurityLevel, $LogHandler, $SQLQueryHandler;

        try {
            authenticateRequest($lSecurityLevel);

            assertParameter($pUsername);
            assertParameter($pPassword);
            assertParameter($pFirstname);
            assertParameter($pLastname);
            assertParameter($pSignature);

            $lTimestamp = date(DATE_TIME_FORMAT);

            if ($SQLQueryHandler->accountExists($pUsername)) {
                $SQLQueryHandler->updateUserAccount($pUsername, $pPassword, $pFirstname, $pLastname, $pSignature, false, false);
                $lResponse = array(
                    'message' => "Updated account {$pUsername}",
                    'securityLevel' => $lSecurityLevel,
                    'timestamp' => $lTimestamp,
                    'output' => new soapval('output', 'xsd:anyType', '')
                );
                $LogHandler->writeToLog("ws-user-account.php: Updated account {$pUsername}");
                return $lResponse;
            } else {
                $SQLQueryHandler->insertNewUserAccount($pUsername, $pPassword, $pFirstname, $pLastname, $pSignature);
                $lResponse = array(
                    'message' => "Inserted account {$pUsername}",
                    'securityLevel' => $lSecurityLevel,
                    'timestamp' => $lTimestamp,
                    'output' => new soapval('output', 'xsd:anyType', '')
                );
                $LogHandler->writeToLog("ws-user-account.php: Created account {$pUsername}");
                return $lResponse;
            }

        } catch (Exception $e) {
            throw new SoapFault("Server", "Error in updateUser: " . $e->getMessage());
        }
    }

    function deleteUser($pUsername, $pPassword) {
        global $lSecurityLevel, $LogHandler, $SQLQueryHandler;

        try {
            authenticateRequest($SQLQueryHandler->getSecurityLevelFromDB());

            assertParameter($pUsername);
            assertParameter($pPassword);

            $lTimestamp = date(DATE_TIME_FORMAT);

            if ($SQLQueryHandler->accountExists($pUsername)) {
                if ($SQLQueryHandler->authenticateAccount($pUsername, $pPassword)) {
                    $SQLQueryHandler->deleteUser($pUsername);
                    $lResponse = array(
                        'message' => "Deleted account {$pUsername}",
                        'securityLevel' => $lSecurityLevel,
                        'timestamp' => $lTimestamp,
                        'output' => new soapval('output', 'xsd:anyType', '')
                    );
                    $LogHandler->writeToLog("ws-user-account.php: Deleted account {$pUsername}");
                    return $lResponse;
                } else {
                    $lResponse = array(
                        'message' => "Could not authenticate account {$pUsername}. Password incorrect.",
                        'securityLevel' => $lSecurityLevel,
                        'timestamp' => $lTimestamp,
                        'output' => new soapval('output', 'xsd:anyType', '')
                    );
                    return $lResponse;
                }
            } else {
                $lResponse = array(
                    'message' => "User {$pUsername} does not exist",
                    'securityLevel' => $lSecurityLevel,
                    'timestamp' => $lTimestamp,
                    'output' => new soapval('output', 'xsd:anyType', '')
                );
                return $lResponse;
            }

        } catch (Exception $e) {
            throw new SoapFault("Server", "Error in deleteUser: " . $e->getMessage());
        }
    }

    // Handle the SOAP request with error handling
    try {
        // Process the incoming SOAP request
        $lSOAPWebService->service(file_get_contents("php://input"));
    } catch (Exception $e) {
        // Send a fault response back to the client if an error occurs
        $lSOAPWebService->fault('Server', "SOAP Service Error: " . $e->getMessage());
    }

?>
