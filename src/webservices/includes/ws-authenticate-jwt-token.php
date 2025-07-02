<?php
/**
 * ws-authenticate-jwt-token.php: Shared JWT authentication for endpoints.
 *
 * This file contains a reusable function `authenticate()` for verifying JWT tokens 
 * in REST API endpoints. It performs the following validations:
 * - Checks if the token is present in the `Authorization` header.
 * - Verifies the token's issuer, audience, and expiration.
 * - Throws an `InvalidTokenException` if any validation fails.
 */

require_once '../../classes/JWT.php'; // Adjust path if necessary

// Define constants for JWT validation if not already defined.
if (!defined('JWT_SECRET_KEY')) {
    // Secret key for decoding the JWT token, defaulting to 'snowman' if not set in environment.
    define('JWT_SECRET_KEY', getenv('JWT_SECRET_KEY') ?: 'snowman');
}

if (!defined('JWT_EXPECTED_ISSUER')) {
    // Expected token issuer (iss claim), to validate that the token is from a trusted source.
    define('JWT_EXPECTED_ISSUER', 'http://mutillidae.localhost');
}

if (!defined('EXPECTED_AUDIENCE')) {
    /**
     * Expected audience (aud claim) for the token, dynamically set to the current endpoint URL without query parameters.
     * This allows the token to be validated based on the endpoint it's intended for.
     */
    $lAudienceUrl = 'http://' . $_SERVER['HTTP_HOST'] . parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    define('EXPECTED_AUDIENCE', $lAudienceUrl);
}

/**
 * Custom exception for handling invalid or missing tokens.
 */
class InvalidTokenException extends Exception {}

/**
 * authenticate() - Validates the JWT token from the Authorization header.
 *
 * This function performs authentication by decoding and validating a JWT token from the 
 * Authorization header. It verifies the following claims:
 * - `iss` (issuer) matches the JWT_EXPECTED_ISSUER constant.
 * - `aud` (audience) matches the EXPECTED_AUDIENCE constant.
 * - `exp` (expiration) is greater than the current time.
 *
 * @throws InvalidTokenException If the token is missing, invalid, expired, or fails validation.
 * @return object The decoded token payload if authentication is successful.
 */

function authenticateJWTToken() {
    // Retrieve the Authorization header and extract the token.
    $lAuthHeader = $_SERVER['HTTP_AUTHORIZATION'] ?? '';
    $lToken = str_replace('Bearer ', '', $lAuthHeader);

    // Check if token is present.
    if (empty($lToken)) {
        throw new InvalidTokenException("Authentication token required.");
    }

    try {
        // Decode the token using the JWT_SECRET_KEY and verify the HS256 signature.
        $lDecodedToken = JWT::decode($lToken, JWT_SECRET_KEY, [JWT_EXPECTED_ALGORITHM]);

        // Validate the `iss` (issuer) claim.
        if ($lDecodedToken->iss !== JWT_EXPECTED_ISSUER) {
            throw new InvalidTokenException("Invalid token issuer.");
        }

        // Validate the `aud` (audience) claim. The audience can be a list or a single value.
        if (is_array($lDecodedToken->aud)) {
            // If `aud` is an array, ensure EXPECTED_AUDIENCE is in the list.
            if (!in_array(EXPECTED_AUDIENCE, $lDecodedToken->aud)) {
                throw new InvalidTokenException("Invalid token audience. Received: [" . implode(", ", $lDecodedToken->aud) . "]. Expected: " . EXPECTED_AUDIENCE);
            }
        } elseif ($lDecodedToken->aud !== EXPECTED_AUDIENCE) {
            // If `aud` is a single string, ensure it matches EXPECTED_AUDIENCE.
            throw new InvalidTokenException("Invalid token audience. Received: " . $lDecodedToken->aud . ". Expected: " . EXPECTED_AUDIENCE);
        }

        // Validate the `exp` (expiration) claim to ensure token hasn't expired.
        if ($lDecodedToken->exp < time()) {
            throw new InvalidTokenException("Token has expired.");
        }

    } catch (Exception $e) {
        // Catch any exceptions during token decoding and validation and throw as InvalidTokenException.
        throw new InvalidTokenException("Invalid or expired token: " . $e->getMessage());
    }

    // Return the decoded token if authentication is successful for further use if needed.
    return $lDecodedToken;
}
?>
