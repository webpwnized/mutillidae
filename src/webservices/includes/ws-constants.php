<?php

// 1xx Informational Responses
define('RESPONSE_CODE_CONTINUE', 100);
define('RESPONSE_CODE_SWITCHING_PROTOCOLS', 101);
define('RESPONSE_CODE_PROCESSING', 102);
define('RESPONSE_CODE_EARLY_HINTS', 103);

// 2xx Success
define('RESPONSE_CODE_OK', 200);
define('RESPONSE_CODE_CREATED', 201);
define('RESPONSE_CODE_ACCEPTED', 202);
define('RESPONSE_CODE_NON_AUTHORITATIVE_INFORMATION', 203);
define('RESPONSE_CODE_NO_CONTENT', 204);
define('RESPONSE_CODE_RESET_CONTENT', 205);
define('RESPONSE_CODE_PARTIAL_CONTENT', 206);
define('RESPONSE_CODE_MULTI_STATUS', 207);
define('RESPONSE_CODE_ALREADY_REPORTED', 208);
define('RESPONSE_CODE_IM_USED', 226);

// 3xx Redirection
define('RESPONSE_CODE_MULTIPLE_CHOICES', 300);
define('RESPONSE_CODE_MOVED_PERMANENTLY', 301);
define('RESPONSE_CODE_FOUND', 302);
define('RESPONSE_CODE_SEE_OTHER', 303);
define('RESPONSE_CODE_NOT_MODIFIED', 304);
define('RESPONSE_CODE_USE_PROXY', 305);
define('RESPONSE_CODE_SWITCH_PROXY', 306);
define('RESPONSE_CODE_TEMPORARY_REDIRECT', 307);
define('RESPONSE_CODE_PERMANENT_REDIRECT', 308);

// 4xx Client Errors
define('RESPONSE_CODE_BAD_REQUEST', 400);
define('RESPONSE_CODE_UNAUTHORIZED', 401);
define('RESPONSE_CODE_PAYMENT_REQUIRED', 402);
define('RESPONSE_CODE_FORBIDDEN', 403);
define('RESPONSE_CODE_NOT_FOUND', 404);
define('RESPONSE_CODE_METHOD_NOT_ALLOWED', 405);
define('RESPONSE_CODE_NOT_ACCEPTABLE', 406);
define('RESPONSE_CODE_PROXY_AUTHENTICATION_REQUIRED', 407);
define('RESPONSE_CODE_REQUEST_TIMEOUT', 408);
define('RESPONSE_CODE_CONFLICT', 409);
define('RESPONSE_CODE_GONE', 410);
define('RESPONSE_CODE_LENGTH_REQUIRED', 411);
define('RESPONSE_CODE_PRECONDITION_FAILED', 412);
define('RESPONSE_CODE_PAYLOAD_TOO_LARGE', 413);
define('RESPONSE_CODE_URI_TOO_LONG', 414);
define('RESPONSE_CODE_UNSUPPORTED_MEDIA_TYPE', 415);
define('RESPONSE_CODE_RANGE_NOT_SATISFIABLE', 416);
define('RESPONSE_CODE_EXPECTATION_FAILED', 417);
define('RESPONSE_CODE_IM_A_TEAPOT', 418);
define('RESPONSE_CODE_MISDIRECTED_REQUEST', 421);
define('RESPONSE_CODE_UNPROCESSABLE_ENTITY', 422);
define('RESPONSE_CODE_LOCKED', 423);
define('RESPONSE_CODE_FAILED_DEPENDENCY', 424);
define('RESPONSE_CODE_TOO_EARLY', 425);
define('RESPONSE_CODE_UPGRADE_REQUIRED', 426);
define('RESPONSE_CODE_PRECONDITION_REQUIRED', 428);
define('RESPONSE_CODE_TOO_MANY_REQUESTS', 429);
define('RESPONSE_CODE_REQUEST_HEADER_FIELDS_TOO_LARGE', 431);
define('RESPONSE_CODE_UNAVAILABLE_FOR_LEGAL_REASONS', 451);

// 5xx Server Errors
define('RESPONSE_CODE_INTERNAL_SERVER_ERROR', 500);
define('RESPONSE_CODE_NOT_IMPLEMENTED', 501);
define('RESPONSE_CODE_BAD_GATEWAY', 502);
define('RESPONSE_CODE_SERVICE_UNAVAILABLE', 503);
define('RESPONSE_CODE_GATEWAY_TIMEOUT', 504);
define('RESPONSE_CODE_HTTP_VERSION_NOT_SUPPORTED', 505);
define('RESPONSE_CODE_VARIANT_ALSO_NEGOTIATES', 506);
define('RESPONSE_CODE_INSUFFICIENT_STORAGE', 507);
define('RESPONSE_CODE_LOOP_DETECTED', 508);
define('RESPONSE_CODE_NOT_EXTENDED', 510);
define('RESPONSE_CODE_NETWORK_AUTHENTICATION_REQUIRED', 511);

// Define constants for readability and maintainability
define('SECURITY_LEVEL_INSECURE', 0);
define('SECURITY_LEVEL_MEDIUM', 1);
define('SECURITY_LEVEL_SECURE', 5);

define('CONTENT_TYPE_JSON', 'Content-Type: application/json');
define('CONTENT_TYPE_XML', 'Content-Type: text/xml; charset=UTF-8');
define('DATE_TIME_FORMAT', 'Y-m-d H:i:s');

define('JWT_SECRET_KEY', 'snowman');
define('JWT_EXPIRATION_TIME', 3600); // Token expiration time in seconds
define('MAX_FAILED_ATTEMPTS', 5); // Maximum number of failed login attempts
define('JWT_EXPECTED_ISSUER', 'http://mutillidae.localhost');
define('JWT_EXPECTED_ALGORITHM', 'HS256');
define('JWT_BASE_URL', ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST']);

define('JWT_VALID_AUDIENCES', [
    JWT_BASE_URL . "/webservices/rest/ws-cors-echo.php",
    JWT_BASE_URL . "/webservices/rest/ws-dns-lookup.php",
    JWT_BASE_URL . "/webservices/rest/ws-echo.php",
    JWT_BASE_URL . "/webservices/rest/ws-test-connectivity.php",
    JWT_BASE_URL . "/webservices/rest/ws-user-account.php",
    JWT_BASE_URL . "/webservices/soap/ws-dns-lookup.php",
    JWT_BASE_URL . "/webservices/soap/ws-echo.php",
    JWT_BASE_URL . "/webservices/soap/ws-test-connectivity.php",
    JWT_BASE_URL . "/webservices/soap/ws-user-account.php"
]);

define('CORS_REQUEST_ORIGIN', isset($_SERVER['HTTP_ORIGIN']) && !empty($_SERVER['HTTP_ORIGIN']) ? $_SERVER['HTTP_ORIGIN'] : JWT_BASE_URL);
define('CORS_ACCESS_CONTROL_MAX_AGE', 'Access-Control-Max-Age: 600');
define('CORS_ACCESS_CONTROL_ALLOW_ORIGIN', 'Access-Control-Allow-Origin: ' . CORS_REQUEST_ORIGIN);
define('CORS_TRUSTED_ORIGINS', [
    'http://mutillidae.localhost'
]);

define('ERROR_MESSAGE_METHOD_NOT_ALLOWED', '<?xml version="1.0" encoding="UTF-8"?><error><message>Method Not Allowed. Use POST for this endpoint.</message></error>');
define('ERROR_MESSAGE_UNAUTHORIZED_PREFIX', '<?xml version="1.0" encoding="UTF-8"?><error><message>');
define('ERROR_MESSAGE_UNAUTHORIZED_SUFFIX', '</message></error>');

?>