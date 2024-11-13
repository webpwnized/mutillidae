<?php

// Define constants for readability and maintainability
define('CONTENT_TYPE_JSON', 'Content-Type: application/json');
define('SECURITY_LEVEL_INSECURE', 0);
define('SECURITY_LEVEL_MEDIUM', 1);
define('SECURITY_LEVEL_SECURE', 5);
define('BAD_REQUEST_CODE', 400);
define('UNAUTHORIZED_CODE', 401);
define('METHOD_NOT_ALLOWED_CODE', 405);
define('FORBIDDEN_CODE', 403);
define('NOT_FOUND_CODE', 404);
define('CONFLICT_CODE', 409);
define('SERVER_ERROR_CODE', 500);
define('SUCCESS_CODE', 200);
define('SUCCESS_CREATED', 201);
define('SUCCESS_NO_CONTENT', 204);
define('NOT_MODIFIED_CODE', 304);
define('ACCESS_CONTROL_MAX_AGE', 'Access-Control-Max-Age: 600');

?>