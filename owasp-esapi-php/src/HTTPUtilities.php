<?php
/**
 * OWASP Enterprise Security API (ESAPI)
 *
 * This file is part of the Open Web Application Security Project (OWASP)
 * Enterprise Security API (ESAPI) project.
 *
 * PHP version 5.2
 *
 * LICENSE: This source file is subject to the New BSD license.  You should read
 * and accept the LICENSE before you use, modify, and/or redistribute this
 * software.
 *
 * @category  OWASP
 * @package   ESAPI
 * @author    Andrew van der Stock <vanderaj@owasp.org>
 * @author    Mike Boberski <boberski_michael@bah.com>
 * @copyright 2009-2010 The OWASP Foundation
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD license
 * @version   SVN: $Id$
 * @link      http://www.owasp.org/index.php/ESAPI
 */

/**
 * HTTPUtilities requires various Exceptions and SafeRequest.
 */
require_once dirname(__FILE__) . '/errors/AccessControlException.php';
require_once dirname(__FILE__) . '/errors/AuthenticationException.php';
require_once dirname(__FILE__) . '/errors/EncryptionException.php';
require_once dirname(__FILE__) . '/errors/EnterpriseSecurityException.php';
require_once dirname(__FILE__) . '/errors/IntrusionException.php';
require_once dirname(__FILE__) . '/errors/ValidationException.php';
require_once dirname(__FILE__) . '/filters/SafeRequest.php';

/**
 * Use this ESAPI security control to assist with HTTP security.
 * 
 * The idea behind this interface is to define a set of helper
 * functions related to HTTP requests, responses, sessions, cookies,
 * headers, and logging.
 * 
 * @category  OWASP
 * @package   ESAPI
 * @author    Andrew van der Stock <vanderaj@owasp.org>
 * @author    Mike Boberski <boberski_michael@bah.com>
 * @copyright 2009-2010 The OWASP Foundation
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD license
 * @version   Release: @package_version@
 * @link      http://www.owasp.org/index.php/ESAPI
 */
interface HTTPUtilities
{

    /**
     * Adds the CSRF token from the current session to the supplied URL for the
     * purposes of preventing CSRF attacks. This method should be used on all URLs
     * to be put into all links and forms the application generates.
     *
     * @param string $href the URL to which the CSRF token will be appended.
     *
     * @return string URL with the CSRF token parameter appended to it.
     */
    public function addCSRFToken($href);


    /**
     * Returns the CSRF token from the current session. If there is no current
     * session then null is returned. If the CSRF Token is not present in the
     * session it will be created.
     *
     * @return string|null CSRF token for the current session or
     *                     null.
     */
    public function getCSRFToken();


    /**
     * Searches the GET and POST parameters in a request for the CSRF token stored
     * in the current session and throws an IntrusionException if it is missing.
     *
     * @param SafeRequest $request A request object.
     *
     * @return null
     *
     * @throws IntrusionException if the CSRF token is missing or incorrect.
     */
    public function verifyCSRFToken($request);


    /**
     * Sets the CSRF Token for the current session.  If the session has not been
     * started at the time this method is called then the token will not be
     * generated.
     *
     * @return null
     */
    public function setCSRFToken();


    /**
     * Get the first cookie with the matching name.
     *
     * @param SafeRequest $request Request object.
     * @param string      $name    The name of the cookie to retreive.
     *
     * @return string|null value of the requested cookie or
     *                     null if the specified cookie is not present.
     */
    public function getCookie($request, $name);


    /**
     * Ensures that the supplied request was received with Transport Layer
     * Security and uses the HTTP POST to protect any sensitive parameters in
     * the request from being sniffed or logged. For example, this method should
     * be called from any method that uses sensitive data from a web form.
     *
     * @param SafeRequest $request The request object to test.
     *
     * @return null
     *
     * @throws AccessControlException if security constraints are not met.
     */
    public function assertSecureRequest($request);


    /**
     * Invalidate the old session after copying all of its contents to a newly
     * created session with a new session id. Note that this is different from
     * logging out and creating a new session identifier that does not contain
     * the existing session contents. Care should be taken to use this only when
     * the existing session does not contain hazardous contents.
     *
     * @return bool true if the change of Session Identifier was successful,
     *              false otherwise
     */
    public function changeSessionIdentifier();

    /**
     * A safer replacement for getParameter() in SafeRequest that returns the canonicalized
     * value of the named parameter after "global" validation against the general
     * type defined in ESAPI.properties. Ths should not be considered a replacement for
     * more specific validation. 
     *
     * @param SafeRequest $request Request object.
     * @param string $name 
     * @param string $default An optional default value to return if parameter does not pass validation
     * 
     * @return the requested parameter value or $default if the named parameter does not pass validation
     * 
     */
    public function getParameter($request, $name, $default = null);
    

    /**
     * Kill all cookies received in the last request from the browser. Note that
     * new cookies set by the application in this response may not be killed by
     * this method.
     *
     * @param SafeRequest $request Request object.
     *
     * @return null.
     */
    public function killAllCookies($request);


    /**
     * Kills the specified cookie by setting a new cookie that expires
     * immediately. Note that this method does not delete new cookies that are
     * being set by the application for this response.
     *
     * @param SafeRequest $request Request object.
     * @param string      $name    Name of the cookie to be killed.
     *
     * @return null.
     *
     */
    public function killCookie($request, $name);


    /**
     * Stores the supplied SafeRequest object so that it may be readily accessed
     * throughout ESAPI (and elsewhere).
     *
     * @param SafeRequest $request Current Request object.
     *
     * @return null.
     */
    public function setCurrentHTTP($request);


    /**
     * Retrieves the current HttpServletRequest.
     *
     * @return SafeRequest the current request.
     */
    public function getCurrentRequest();


    /**
     * Format the Source IP address, URL, URL parameters, and all form parameters
     * into a string suitable for the log file. Be careful not to log sensitive
     * information, and consider masking with the logHTTPRequestObfuscate method.
     *
     * @param SafeRequest $request Current Request object.
     * @param Auditor     $auditor the auditor to write the request to.
     *
     * @return null
     */
    public function logHTTPRequest($request, $auditor);


    /**
     * Format the Source IP address, URL, URL parameters, and all form parameters
     * into a string suitable for the log file. The list of parameters to obfuscate
     * should be specified in order to prevent sensitive information from being
     * logged. If a null or empty list of parameters is provided, then all
     * parameters will be logged in the clear. If HTTP request logging is done in a
     * central place $paramsToObfuscate could be made a configuration parameter. We
     * include it here in case different parts of the application need to obfuscate
     * different parameters.
     *
     * @param SafeRequest $request           Current Request object.
     * @param Auditor     $auditor           The auditor to write the request to.
     * @param array|null  $paramsToObfuscate The sensitive parameters.
     *
     * @return null
     */
    public function logHTTPRequestObfuscate($request, $auditor, $paramsToObfuscate);


    /*
     * Set a cookie containing the current User's remember me token for
     * automatic authentication. The use of remember me tokens is generally not
     * recommended, but this method will help do it as safely as possible. The
     * user interface should strongly warn the user that this should only be
     * enabled on computers where no other users will have access.
     *
     * Implementations should save the user's remember me data in an encrypted
     * cookie and send it to the user. Any old remember me cookie should be
     * destroyed first. Setting this cookie should keep the user logged in until
     * the maxAge passes, the password is changed, or the cookie is deleted. If
     * the cookie exists for the current user, it should automatically be used
     * by ESAPI to log the user in, if the data is valid and not expired.
     *
     * The ESAPI reference implementation, DefaultHTTPUtilities.setRememberToken()
     * implements all these suggestions.
     *
     * @param SafeRequest  $request  Request object.
     * @param SafeResponse $response Response object.
     * @param string       $password the user's password.
     * @param int          $maxAge   the length of time that the token should be
     *                               valid for in relative seconds.
     * @param string|null  $domain   the domain to restrict the token to.
     * @param string|null  $path     the path to restrict the token to.
     *
     * @return string  encrypted "Remember Me" token.
     */
    // public function setRememberToken(
    //     $request, $response, $password, $maxAge, $domain, $path
    // );


    /*
     * Decrypts an encrypted hidden field value and returns the plain text. If
     * the field does not decrypt properly, an IntrusionException is thrown to
     * indicate tampering.
     *
     * @param string $encrypted hidden field value to decrypt.
     *
     * @return string decrypted hidden field value.
     *
     * @throws IntrusionException.
     */
    // public function decryptHiddenField($encrypted);


    /*
     * Takes an encrypted query string and returns an asscoiative array
     * containing the original, unencrypted parameters.
     *
     * @param string $encrypted The encrypted query string to be decrypted.
     *
     * @return array of name-value pairs from the decrypted query string.
     *
     * @throws EncryptionException
     */
    // public function decryptQueryString($encrypted);


    /*
     * Retrieves a map of data from a cookie encrypted with encryptStateInCookie().
     *
     * @param SafeRequest $request object.
     *
     * @return array a map containing the decrypted cookie state value.
     *
     * @throws EncryptionException.
     */
    // public function decryptStateFromCookie($request);


    /*
     * Encrypts a hidden field value for use in HTML.
     *
     * @param string $value Plain text value of the hidden field.
     *
     * @return string encrypted value of the hidden field.
     *
     * @throws EncryptionException
     */
    // public function encryptHiddenField($value);


    /**
     * Takes an HTTP query string (everything after the question mark in the
     * URL) and returns an encrypted string containing the parameters.
     *
     * @param string $query Query string to be encrypted.
     *
     * @return string encrypted query string.
     *
     * @throws EncryptionException
     */
    // public function encryptQueryString($query);


    /*
     * Stores a Map of data in an encrypted cookie. Generally the session is a
     * better place to store state information, as it does not expose it to the
     * user at all. If there is a requirement not to use sessions, or the data
     * should be stored across sessions (for a long time), the use of encrypted
     * cookies is an effective way to prevent the exposure.
     *
     * @param SafeResponse $response  response object.
     * @param array        $cleartext state information.
     *
     * @return null.
     */
    // public function encryptStateInCookie($response, $cleartext);


    /*
     * Extract uploaded files from a multipart HTTP requests. Implementations
     * must check the content to ensure that it is safe before making a permanent
     * copy on the local filesystem. Checks should include length and content
     * checks, possibly virus checking, and path and name checks. Refer to the
     * file checking methods in Validator for more information.
     *
     * @param SafeRequest $request  Request object.
     * @param string      $tempDir  the temporary directory.
     * @param string      $finalDir the final directory.
     *
     * @return array List of new File objects from upload.
     *
     * @throws ValidationException if the file fails validation.
     */
    // public function getSafeFileUploads($request, $tempDir, $finalDir);


    /*
     * This method performs a forward to any resource located inside the WEB-INF
     * directory. Forwarding to publicly accessible resources can be dangerous,
     * as the request will have already passed the URL based access control
     * check. This method ensures that you can only forward to non-publicly
     * accessible resources.
     *
     * @param SafeRequest  $request  Request object.
     * @param SafeResponse $response Response object.
     * @param string       $context  This value is used by any logging or error
     *                                handling that is done with respect to the
     *                                value passed in.
     * @param string       $location The URL to forward to.
     *
     * @return null.
     *
     * @throws AccessControlException
     * @throws ServletException
     * @throws IOException
     */
    // public function safeSendForward($request, $response, $context, $location);


    /*
     * Set the content type character encoding header on every HttpServletResponse
     * in order to limit the ways in which the input data can be represented. This
     * prevents malicious users from using encoding and multi-byte escape sequences
     * to bypass input validation routines.
     *
     * Implementations of this method should set the content type header to a safe
     * value for your environment. The default is text/html; charset=UTF-8 character
     * encoding, which is the default in early versions of HTML and HTTP.
     * See RFC 2045 (http://ds.internic.net/rfc/rfc2045.txt) for more information
     * about character encoding and MIME.
     *
     * The DefaultHTTPUtilities reference implementation sets the content type as
     * specified.
     *
     * @param SafeResponse $response The response object to set the content type
     *                               for.
     *
     * @return null.
     */
    // public function setSafeContentType($response);


    /*
     * Set headers to protect sensitive information against being cached in the
     * browser. Developers should make this call for any HTTP responses that contain
     * any sensitive data that should not be cached within the browser or any
     * intermediate proxies or caches. Implementations should set headers for the
     * expected browsers. The safest approach is to set all relevant headers to
     * their most restrictive setting. These include:
     *
     * <PRE>
     *
     * Cache-Control: no-store<BR>
     * Cache-Control: no-cache<BR>
     * Cache-Control: must-revalidate<BR>
     * Expires: -1<BR>
     *
     * </PRE>
     *
     * Note that the header "pragma: no-cache" is only useful in HTTP requests,
     * not HTTP responses. So even though there are many articles recommending the
     * use of this header, it is not helpful for preventing browser caching. For
     * more information, please refer to the relevant standards:
     *
     * HTTP/1.1 Cache-Control "no-cache" {@link
     * http://www.w3.org/Protocols/rfc2616/rfc2616-sec14.html}
     * HTTP/1.1 Cache-Control "no-store" {@link
     * http://www.w3.org/Protocols/rfc2616/rfc2616-sec14.html#sec14.9.1}
     * HTTP/1.0 Pragma "no-cache" {@link
     * http://www.w3.org/Protocols/rfc2616/rfc2616-sec14.html#sec14.9.2}
     * HTTP/1.0 Expires {@link
     * http://www.w3.org/Protocols/rfc2616/rfc2616-sec14.html#sec14.32}
     * IE6 Caching Issues {@link
     * http://www.w3.org/Protocols/rfc2616/rfc2616-sec14.html#sec14.21}
     * Firefox browser.cache.disk_cache_ssl {@link
     * http://support.microsoft.com/kb/937479}
     * Mozilla Networking Preferences {@link
     * https://developer.mozilla.org/en/Mozilla_Networking_Preferences#Cache}
     *
     * @param SafeResponse $response Response object.
     *
     * @return null.
     */
    // public function setNoCacheHeaders($response);


    /*
     * Retrieves the current HttpServletResponse.
     *
     * @return SafeResponse the current response.
     */
    // public function getCurrentResponse();

}
