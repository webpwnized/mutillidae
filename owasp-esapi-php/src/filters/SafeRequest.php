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
 * @package   ESAPI_Filters
 * @author    jah <jah@jahboite.co.uk>
 * @copyright 2009-2010 The OWASP Foundation
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD license
 * @version   SVN: $Id$
 * @link      http://www.owasp.org/index.php/ESAPI
 */


/**
 * SafeRequest requires the DefaultEncoder, HTMLEntityCodec and PercentCodec.
 */
require_once dirname(__FILE__) . '/../reference/DefaultEncoder.php';
require_once dirname(__FILE__) . '/../codecs/HTMLEntityCodec.php';
require_once dirname(__FILE__) . '/../codecs/PercentCodec.php';


/**
 * This request wrapper simply provides convenient and safe methods for the
 * retreival of HTTP request parameters, headers and PHP server globals defined in
 * the CGI 1.1 Specification.  The methods perform canonicalization and validation
 * of the requested values or return safe defaults such as empty strings.
 *
 * PHP version 5.2
 *
 * @category  OWASP
 * @package   ESAPI_Filters
 * @author    jah <jah@jahboite.co.uk>
 * @copyright 2009-2010 The OWASP Foundation
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD license
 * @version   Release: @package_version@
 * @link      http://www.owasp.org/index.php/ESAPI
 */
class SafeRequest
{
    /*
     * Ascii character sets defining printable, non-alphanumeric characters
     * permitted in various HTTP request contexts.
     * ' !"#$%&\'()*+,-./:;<=>?@[\]^_`{|}~'
     * tspecials: '()<>@,;:\"/[]?={}' +SP +HT
     */
    const CHARS_HTTP_COOKIE_NAME  = '_';
    const CHARS_HTTP_COOKIE_VALUE = '!"#$%&\'()*+-./:<=>?@[\]^_`{|}~';
    const CHARS_HTTP_HEADER_NAME  = '-_';
    const CHARS_HTTP_HEADER_VALUE = ' !"#$%&\'()*+,-./;:<=>?@[\]^_`{|}~';
    const CHARS_HTTP_QUERY_STRING = ' &()*+,-./;:=?_';
    const CHARS_HTTP_HOSTNAME     = '-._';
    const CHARS_HTTP_REMOTE_USER  = '!#$%&\'*+-.^_`|~';
    const CHARS_HTTP_REQUEST_URI  = '!$%&\'()*+-,./:=@_~';
    const CHARS_FILESYSTEM_PATH   = ' !#$%&\'()+,-./=@[\]^_`{}~';
    const CHARS_NUMERIC           = '0123456789';
    const CHARS_ALPHA = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';

    const ORD_TAB = 0x09;

    const PATTERN_REQUEST_METHOD   = '^(GET|HEAD|POST|TRACE|OPTIONS)$';
    const PATTERN_REQUEST_AUTHTYPE
        = '^([dD][iI][gG][eE][sS][tT]|[bB][aA][sS][iI][cC])$';
    const PATTERN_HOST_NAME
        = '^((?:(?:[0-9a-zA-Z][0-9a-zA-Z\-]{0,61}[0-9a-zA-Z])\.)*[a-zA-Z]{2,4}|[0-9a-zA-Z][0-9a-zA-Z\-]{0,61}[0-9a-zA-Z])$';
    const PATTERN_IPV4_ADDRESS
        = '^(?:(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.){3}(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)$';

    private $_serverGlobals      = null;

    private $_authType           = null;
    private $_contentLength      = null;
    private $_contentType        = null;
    private $_headers            = null;
    private $_pathInfo           = null;
    private $_pathTranslated     = null;
    private $_queryString        = null;
    private $_remoteAddr         = null;
    private $_remoteHost         = null;
    private $_remoteUser         = null;
    private $_method             = null;
    private $_requestURI         = null;
    private $_serverName         = null;
    private $_serverPort         = null;
    private $_protocol           = null;
    private $_cookies            = null;
    private $_parameterNames     = null;
    private $_parameterMap       = null;

    private $_validator = null;
    private $_encoder   = null;
    private $_auditor   = null;


    /**
     * SafeRequest can be forced to use the supplied cookies, headers and server
     * globals by passing an array containing the following keys: 'cookies',
     * 'headers', 'env'.  The values for each of the keys should be an associative
     * array e.g. 'headers' => array('REQUEST_METHOD' => 'GET').
     * If any of the three options keys are not supplied then those elements will be
     * extracted from the actual request.
     * TODO accept a string like: 'GET / HTTP/1.1\r\nHost:example.com\r\n\r\n'
     * TODO accept GET and REQUEST parameters.
     *
     * @param null|array $options array (optional) of HTTP Request elements.
     */
    public function __construct($options = null)
    {
        $codecs = array(
            new HTMLEntityCodec,
            new PercentCodec
        );
        $this->_encoder    = new DefaultEncoder($codecs);
        $this->_auditor    = ESAPI::getAuditor('SafeRequest');
        $this->_validator  = ESAPI::getValidator();

        if ($options !== null && is_array($options)) {
            if (array_key_exists('cookies', $options)) {
                $this->_cookies = $this->_validateCookies($options['cookies']);
            }
            if (array_key_exists('headers', $options)) {
                $this->_headers = $this->_validateHeaders($options['headers']);
            }
            if (array_key_exists('env', $options)) {
                $this->_serverGlobals
                    = $this->_canonicalizeServerGlobals($options['env']);
            }
        }
    }


    /**
     * Sets the encoder instance to be used for encoding/decoding, canonicalization
     * and validation.
     *
     * @param Encoder $encoder An instance of the Encoder interface.
     *
     * @return null
     */
    public function setEncoder($encoder)
    {
        if ($encoder instanceof Encoder == false) {
            throw new InvalidArgumentException(
                'setEncoder expects an object of class Encoder!'
            );
        }
        $this->_encoder = $encoder;
    }


    /**
     * Returns the value of $_SERVER['AUTH_TYPE'] if it is present or an
     * empty string if it is not.
     *
     * @return string Authentication Scheme.
     */
    public function getAuthType()
    {
        $defaultValue = '';
        
        if ($this->_authType !== null) {
            return $this->_authType;
        } else {
            $this->_authType = $defaultValue;
        }

        $key = 'AUTH_TYPE';
        $pattern = self::PATTERN_REQUEST_AUTHTYPE;
        $canon   = $this->getServerGlobal($key);
        $authType  = null;
        try {
            $authType = $this->_getIfValid(
                'HTTP Request Auth Scheme validation',
                $canon, $pattern, $key, 6, true
            );
        } catch (Exception $e) {
            // NoOp - already logged.
        }

        if ($authType !== null) {
            $this->_authType = $authType;
        }
        
        return $this->_authType;
    }


    /**
     * Returns the value of $_SERVER['CONTENT_LENGTH'] if it is present or zero
     * otherwise.
     *
     * @return int Length of the Request Entity.
     */
    public function getContentLength()
    {
        $defaultValue = 0;
        
        if ($this->_contentLength !== null) {
            return $this->_contentLength;
        } else {
            $this->_contentLength = $defaultValue;
        }

        $key   = 'CONTENT_LENGTH';
        $canon = $this->getServerGlobal($key);
        $isValid = $this->_validator->isValidInteger(
            'HTTP Request Content-Length validation',
            $canon, 0, PHP_INT_MAX, true
        );
        if ($isValid == true) {
            $this->_contentLength = (int) $canon;
        }

        return $this->_contentLength;
    }


    /**
     * Returns the value of $_SERVER['CONTENT_TYPE'] if it is present or an
     * empty string if it is not.
     *
     * @return string Content type of the Request Entity.
     */
    public function getContentType()
    {
        $defaultValue = '';
        
        if ($this->_contentType !== null) {
            return $this->_contentType;
        } else {
            $this->_contentType = $defaultValue;
        }

        $key          = 'CONTENT_TYPE';
        $c            = array(self::CHARS_HTTP_HEADER_VALUE);
        $charset      = $this->_hexifyCharsForPattern($c);
        $pattern      = "^[a-zA-Z0-9{$charset}]+$";

        $canon        = $this->getServerGlobal($key);
        $contentType  = null;
        try {
            $contentType = $this->_getIfValid(
                'HTTP Request Content Type validation',
                $canon, $pattern, $key, 4096, true
            );
        } catch (Exception $e) {
            // NoOp - already logged.
        }

        if ($contentType !== null) {
            $this->_contentType = $contentType;
        }
        
        return $this->_contentType;
    }


    /**
     * Returns the value of $_SERVER['PATH_INFO'] if it is present or an
     * empty string if it is not.
     *
     * @return string Path Info.
     */
    public function getPathInfo()
    {
        $defaultValue = '';
        
        if ($this->_pathInfo !== null) {
            return $this->_pathInfo;
        } else {
            $this->_pathInfo = $defaultValue;
        }

        $key      = 'PATH_INFO';
        $c        = array(self::CHARS_HTTP_HEADER_VALUE);
        $charset  = $this->_hexifyCharsForPattern($c);
        $pattern  = "^[a-zA-Z0-9{$charset}]+$";

        $canon    = $this->getServerGlobal($key);
        $pathInfo = null;
        try {
            $pathInfo = $this->_getIfValid(
                'HTTP Request Path Info validation',
                $canon, $pattern, $key, 4096, true
            );
        } catch (Exception $e) {
            // NoOp - already logged.
        }

        if ($pathInfo !== null) {
            $this->_pathInfo = $pathInfo;
        }
        
        return $this->_pathInfo;
    }


    /**
     * Returns the value of $_SERVER['PATH_TRANSLATED'] if it is present or an
     * empty string if it is not.
     *
     * @return string OS filesystem equivalent of Path Info.
     */
    public function getPathTranslated()
    {
        $defaultValue = '';
        
        if ($this->_pathTranslated !== null) {
            return $this->_pathTranslated;
        } else {
            $this->_pathTranslated = $defaultValue;
        }

        $key      = 'PATH_TRANSLATED';
        $c        = array(self::CHARS_FILESYSTEM_PATH);
        $charset  = $this->_hexifyCharsForPattern($c);
        $pattern  = "^[a-zA-Z0-9{$charset}]+$";

        $canon          = $this->getServerGlobal($key);
        $pathTranslated = null;
        try {
            $pathTranslated = $this->_getIfValid(
                'HTTP Request Path Translated validation',
                $canon, $pattern, $key, 4096, true
            );
        } catch (Exception $e) {
            // NoOp - already logged.
        }

        if ($pathTranslated !== null) {
            $this->_pathTranslated = $pathTranslated;
        }

        return $this->_pathTranslated;
    }


    /**
     * Returns the value of $_SERVER['QUERY_STRING'] if it is present or an
     * empty string if it is not.
     *
     * @return string Query String.
     */
    public function getQueryString()
    {
        $defaultValue = '';
        
        if ($this->_queryString !== null) {
            return $this->_queryString;
        } else {
            $this->_queryString = $defaultValue;
        }

        $key      = 'QUERY_STRING';
        $c        = array(self::CHARS_HTTP_QUERY_STRING);
        $charset  = $this->_hexifyCharsForPattern($c);
        $pattern  = "^[a-zA-Z0-9{$charset}]+$";

        $canon       = $this->getServerGlobal($key);
        $queryString = null;
        try {
            $queryString = $this->_getIfValid(
                'HTTP Request Query String validation',
                $canon, $pattern, $key, 4096, true
            );
        } catch (Exception $e) {
            // NoOp - already logged.
        }

        if ($queryString !== null) {
            $this->_queryString = $queryString;
        }

        return $this->_queryString;
    }


    /**
     * Returns the value of $_SERVER['REMOTE_ADDR'] if it is present or an
     * empty string if it is not.
     *
     * @return string Requesting Agent IP Address.
     */
    public function getRemoteAddr()
    {
        $defaultValue = '';
        
        if ($this->_remoteAddr !== null) {
            return $this->_remoteAddr;
        } else {
            $this->_remoteAddr = $defaultValue;
        }

        $key      = 'REMOTE_ADDR';
        $pattern  = self::PATTERN_IPV4_ADDRESS;

        $canon      = $this->getServerGlobal($key);
        $remoteAddr = null;
        try {
            $remoteAddr = $this->_getIfValid(
                'HTTP Request Remote Address validation',
                $canon, $pattern, $key, 15, true
            );
        } catch (Exception $e) {
            // NoOp - already logged.
        }

        if ($remoteAddr !== null) {
            $this->_remoteAddr = $remoteAddr;
        }

        return $this->_remoteAddr;
    }


    /**
     * Returns the value of $_SERVER['REMOTE_HOST'] if it is present or an
     * empty string if it is not.
     *
     * @return string Requesting Agent FQDN.
     */
    public function getRemoteHost()
    {
        $defaultValue = '';
        
        if ($this->_remoteHost !== null) {
            return $this->_remoteHost;
        } else {
            $this->_remoteHost = $defaultValue;
        }

        $key      = 'REMOTE_HOST';
        $pattern  = self::PATTERN_HOST_NAME;

        $canon      = $this->getServerGlobal($key);
        $remoteHost = null;
        try {
            $remoteHost = $this->_getIfValid(
                'HTTP Request Remote Host FQDN validation',
                $canon, $pattern, $key, 255, true
            );
        } catch (Exception $e) {
            // NoOp - already logged.
        }

        if ($remoteHost !== null) {
            $this->_remoteHost = $remoteHost;
        }

        return $this->_remoteHost;
    }


    /**
     * Returns the value of $_SERVER['REMOTE_USER'] if it is present or an
     * empty string if it is not.
     *
     * @return string Remote User ID for Basic Authentication.
     */
    public function getRemoteUser()
    {
        $defaultValue = '';
        
        if ($this->_remoteUser !== null) {
            return $this->_remoteUser;
        } else {
            $this->_remoteUser = $defaultValue;
        }

        $key      = 'REMOTE_USER';
        $c        = array(self::CHARS_HTTP_REMOTE_USER);
        $charset  = $this->_hexifyCharsForPattern($c);
        $pattern  = "^[a-zA-Z0-9{$charset}]+$";

        $canon      = $this->getServerGlobal($key);
        $remoteUser = null;
        try {
            $remoteUser = $this->_getIfValid(
                'HTTP Request Remote User validation',
                $canon, $pattern, $key, 255, true
            );
        } catch (Exception $e) {
            // NoOp - already logged.
        }

        if ($remoteUser !== null) {
            $this->_remoteUser = $remoteUser;
        }

        return $this->_remoteUser;
    }


    /**
     * Returns the value of $_SERVER['REQUEST_METHOD'] if it is present or an
     * empty string if it is not.
     *
     * @return string Request Method.
     */
    public function getMethod()
    {
        $defaultValue = '';
        
        if ($this->_method !== null) {
            return $this->_method;
        } else {
            $this->_method = $defaultValue;
        }

        $key     = 'REQUEST_METHOD';
        $pattern = self::PATTERN_REQUEST_METHOD;

        $canon  = $this->getServerGlobal($key);
        $method = null;
        try {
            $method = $this->_getIfValid(
                'HTTP Request Method Validation',
                $canon, $pattern, $key, 7, false
            );
        } catch (Exception $e) {
            // NoOp - already logged.
        }
        if ($method !== null) {
            $this->_method = $method;
        }

        return $this->_method;
    }


    /**
     * Returns the URI from the HTTP Request line exlcuding any path info and the
     * query string.
     *
     * @return string Request URI.
     */
    public function getRequestURI()
    {
        $defaultValue = '';
        
        if ($this->_requestURI !== null) {
            return $this->_requestURI;
        } else {
            $this->_requestURI = $defaultValue;
        }

        $key     = 'SCRIPT_NAME';
        $c       = array(self::CHARS_HTTP_REQUEST_URI);
        $charset = $this->_hexifyCharsForPattern($c);
        $pattern = "^[a-zA-Z0-9{$charset}]+$";

        $canon  = $this->getServerGlobal($key);
        $path = null;
        try {
            $path = $this->_getIfValid(
                'HTTP Request URI Validation',
                $canon, $pattern, $key, PHP_INT_MAX, false
            );
        } catch (Exception $e) {
            // NoOp - already logged.
        }
        if ($path !== null) {
            $this->_requestURI = $path;
        }

        return $this->_requestURI;
    }


    /**
     * Returns the value of $_SERVER['SERVER_NAME'] if it is present or an
     * empty string if it is not.
     *
     * @return string Server Name or IP Address.
     */
    public function getServerName()
    {
        $defaultValue = '';
        
        if ($this->_serverName !== null) {
            return $this->_serverName;
        } else {
            $this->_serverName = '';
        }

        $key      = 'SERVER_NAME';
        $pattern  = '(';
        $pattern .= self::PATTERN_IPV4_ADDRESS;
        $pattern .= '|';
        $pattern .= self::PATTERN_HOST_NAME;
        $pattern .= ')';

        $canon      = $this->getServerGlobal($key);
        $serverName = null;
        try {
            $serverName = $this->_getIfValid(
                'HTTP Request Server Name validation',
                $canon, $pattern, $key, 255, true
            );
        } catch (Exception $e) {
            // NoOp - already logged.
        }

        if ($serverName !== null) {
            $this->_serverName = $serverName;
        }

        return $this->_serverName;
    }


    /**
     * Returns the value of $_SERVER['SERVER_PORT'] if it is present or zero if it
     * is not.
     *
     * @return int Server Port Number.
     */
    public function getServerPort()
    {
        $defaultValue = 0;
        
        if ($this->_serverPort !== null) {
            return $this->_serverPort;
        } else {
            $this->_serverPort = $defaultValue;
        }

        $key        = 'SERVER_PORT';
        $canon      = $this->getServerGlobal($key);
        $isValid = $this->_validator->isValidInteger(
            'HTTP Request Server Port validation',
            $canon, 0, 65535, true
        );
        if ($isValid == true) {
            $this->_serverPort = (int) $canon;
        }

        return $this->_serverPort;
    }


    /**
     * Returns an associative array of valid, canonical HTTP Headers.
     *
     * @return array Zero or more HTTP Headers.
     */
    public function getHeaders()
    {
        if ($this->_headers !== null) {
            return $this->_headers;
        }

        if ($this->_serverGlobals === null) {
            $this->getServerGlobals();
        }

        $this->_headers = $this->_validateHeaders($this->_serverGlobals);
        return $this->_headers;
    }


    /**
     * Retreives a named HTTP header value.
     *
     * @param string $key Name of the http header value to retreive.
     *
     * @return null|string valid, canonicalised header value or null if it is not
     *                     present in the header or was present, but invalid.
     */
    public function getHeader($key)
    {
        if (! is_string($key) || $key == '') {
            return null;
        }
        if ($this->_headers === null) {
            $this->getHeaders();
        }

        if (! array_key_exists($key, $this->_headers)) {
            return null;
        }

        return $this->_headers[$key];
    }


    /**
     * Returns an associative array of HTTP Cookies.
     *
     * @return array HTTP Cookies.
     */
    public function getCookies()
    {
        if ($this->_cookies !== null) {
            return $this->_cookies;
        }

        $this->_cookies = $this->_validateCookies($_COOKIE);

        return $this->_cookies;

    }


    /**
     * Retreives a named http cookie value.
     *
     * @param string $name Name of the cookie value to retreive.
     *
     * @return null|string valid, canonicalised cookie value or null if it is not
     *                     present in the header or was present, but invalid.
     */
    public function getCookie($name)
    {
        if (! is_string($name) || $name == '') {
            return null;
        }
        if ($this->_cookies === null) {
            $this->getCookies();
        }

        if (! array_key_exists($name, $this->_cookies)) {
            return null;
        }

        return $this->_cookies[$name];
    }


    /**
     * Returns the value of the PHP Server Global with the supplied name. If the
     * variable does not exist then null is returned.
     *
     * @param string $key Index name for a value in the $_SERVER array.
     *
     * @return string|null Value of a $_SERVER variable or null.
     */
    public function getServerGlobal($key)
    {
        if (! is_string($key) || $key == '') {
            return null;
        }
        if ($this->_serverGlobals === null) {
            $this->_getServerGlobals();
        }

        if (array_key_exists($key, $this->_serverGlobals)) {
            return $this->_serverGlobals[$key];
        }
        $key = strtoupper($key);
        if (array_key_exists($key, $this->_serverGlobals)) {
            return $this->_serverGlobals[$key];
        }

        return null;
    }


    /**
     * Returns the value of a request parameter as a String, or null if the
     * parameter does not exist. Request parameters are contained in the query
     * string or posted form data and are retreived from the $_GET and $_POST PHP
     * globals {@see getParameterMap}.
     * You should only use this method when you are sure the parameter has only one
     * value. If the parameter might have more than one value, use
     * getParameterValues. If you use this method with a multivalued parameter, the
     * value returned is equal to the first value in the array returned by
     * getParameterValues.
     *
     * @param string $name The name of a parameter to retreive.
     *
     * @return string|null The first or only value of a parameter or null if the
     *                     parameter is not present in the request.
     */
    public function getParameter($name)
    {
        if (! is_string($name) || empty($name)) {
            return null;
        }
        if ($this->_parameterMap === null) {
            $this->getParameterMap();
        }
        if (! array_key_exists($name, $this->_parameterMap)) {
            return null;
        }
        if (! is_array($this->_parameterMap[$name])) {
            return $this->_parameterMap[$name];
        }
        return $this->_parameterMap[$name][0];
    }


    /**
     * Returns an array containing the names of all parameters for this request.
     * If the request has no parameters the returned array will be empty.
     *
     * @return array Zero or more request parameter names.
     */
    public function getParameterNames()
    {
        if ($this->_parameterNames !== null) {
            return $this->_parameterNames;
        }
        if ($this->_parameterMap === null) {
            $this->getParameterMap();
        }
        $tmp = array();
        foreach ($this->_parameterMap as $name => $ignore) {
            $tmp[] = $name;
        }
        $this->_parameterNames = $tmp;
        return $this->_parameterNames;

    }


    /**
     * Retrieves all values for the supplied parameter of this request as an array.
     * Request parameters are contained in the query string or posted form data and
     * are retreived from the $_GET and $_POST PHP globals {@see getParameterMap}.
     * Values retreived from $_POST are added to the array before those from $_GET.
     *
     * @param string $name The name of the parameter to retreive.
     *
     * @return array|null Array of request parameter values asscoiated with the
     *                    supplied name or null if the parameter name was not found
     *                    in this request.
     */
    public function getParameterValues($name)
    {
        if (! is_string($name) || empty($name)) {
            return null;
        }
        if ($this->_parameterMap === null) {
            $this->getParameterMap();
        }
        if (! array_key_exists($name, $this->_parameterMap)) {
            return null;
        }
        return $this->_parameterMap[$name];
    }


    /**
     * Returns an associative array of the parameters of this request. Request
     * parameters are contained in the query string or posted form data and are
     * retreived from the $_GET and $_POST PHP globals.  The keys of the array are
     * canonicalized strings and the values are arrays of canonicalized strings.
     * Values retreived from $_POST are added to the array before those from $_GET.
     *
     * @return array of canonicalized request parameters.
     */
    public function getParameterMap()
    {
        if ($this->_parameterMap !== null) {
            return $this->_parameterMap;
        }

        $tmp = array();
        foreach ($_POST as $unsafePname => $unsafePvalue) {
            try {
                $canonName  = $this->_encoder->canonicalize($unsafePname);
                $canonValue = $this->_encoder->canonicalize($unsafePvalue);
                $tmp[$canonName][] = $canonValue;
            } catch (Exception $e) {
                // NoOp
            }
        }
        foreach ($_GET as $unsafePname => $unsafePvalue) {
            try {
                $canonName  = $this->_encoder->canonicalize($unsafePname);
                $canonValue = $this->_encoder->canonicalize($unsafePvalue);
                $tmp[$canonName][] = $canonValue;
            } catch (Exception $e) {
                // NoOp
            }
        }
        $this->_parameterMap = $tmp;
        return $this->_parameterMap;
    }


    /**
     * A convenience method to retrieve an array of PHP Server Globals.  Both the
     * keys and values are canonicalized and those that generate exceptions are
     * not added to the array.
     *
     * @return array Zero or more Canonicalized PHP Server Globals.
     */
    private function _getServerGlobals()
    {
        if ($this->_serverGlobals !== null) {
            return $this->_serverGlobals;
        }

        $this->_serverGlobals = $this->_canonicalizeServerGlobals($_SERVER);

        return $this->_serverGlobals;
    }


    /**
     * Performs strict canonicalization of the indices and values of the supplied
     * array which may be the PHP server globals or a custom array of name value
     * pairs.
     *
     * @param array $ary Name value pairs.
     *
     * @return array Associative array with canonicalized indices and values.
     */
    private function _canonicalizeServerGlobals($ary)
    {
        $tmp = array();
        foreach ($ary as $unsafeKey => $unsafeVal) {
            if (! is_string($unsafeVal)) {
                continue;
            }
            try {
                $canonKey = $this->_encoder->canonicalize($unsafeKey);
                $canonVal = $this->_encoder->canonicalize($unsafeVal);
                $tmp[$canonKey] = $canonVal;
            } catch (Exception $e) {
                // Validation or Intrusion Exceptions perform auto logging.
            }
        }
        return $tmp;
    }


    /**
     * This helper method accepts either the server globals array $_SERVER or a
     * similar array containing values with string indices in the form HTTP_*
     * The indices are canonicalized and validated against a pattern for valid
     * HTTP header names and those that pass are returned along with their
     * canonicalised valid HTTP header values.
     *
     * @param array $ary Associative array that includes HTTP header name value
     *                   pairs.
     *
     * @return array Zero or more validated HTTP header name value pairs.
     */
    private function _validateHeaders($ary)
    {
        $charset = array(self::CHARS_HTTP_HEADER_NAME);
        $keyCharset = $this->_hexifyCharsForPattern($charset);
        $ptnKey = "^[a-zA-Z0-9{$keyCharset}]+$";

        $charset = array(self::CHARS_HTTP_HEADER_VALUE, self::ORD_TAB);
        $valCharset = $this->_hexifyCharsForPattern($charset);
        $ptnVal = "^[a-zA-Z0-9{$valCharset}]+$";

        $tmp = array();
        foreach ($ary as $unvalidatedKey => $unvalidatedVal) {
            try
            {
                $safeKey = $this->_getIfValid(
                    '$_SERVER Index', $unvalidatedKey, $ptnKey,
                    'HTTP Header Validator', PHP_INT_MAX, false
                );
                if (mb_substr($safeKey, 0, 5, 'ASCII') == 'HTTP_') {
                    $safeVal = $this->_getIfValid(
                        '$_SERVER HTTP_* Value', $unvalidatedVal, $ptnVal,
                        'HTTP Header Validator', PHP_INT_MAX, false
                    );
                    $tmp[$safeKey] = $safeVal;
                }
            }
            catch (Exception $e)
            {
                // NoOp
            }
        }
        return $tmp;
    }


    /**
     * This helper method accepts either the $_COOKIES array or a similar array
     * containing custom cookie name value pairs.  The names and values are
     * canonicalized and validated against a pattern for valid HTTP cookies and
     * those that pass are returned.
     *
     * @param array $ary Associative array that includes HTTP cookie name value
     *                   pairs.
     *
     * @return array Zero or more validated HTTP cookie name value pairs.
     */
    private function _validateCookies($ary)
    {
        if ($this->_cookies !== null) {
            return $this->_cookies;
        }

        $charset = array(self::CHARS_HTTP_COOKIE_NAME);
        $keyCharset = $this->_hexifyCharsForPattern($charset);
        $ptnKey = "^[a-zA-Z0-9{$keyCharset}]+$";

        $charset = array(self::CHARS_HTTP_COOKIE_VALUE);
        $valCharset = $this->_hexifyCharsForPattern($charset);
        $ptnVal = "^[a-zA-Z0-9{$valCharset}]+$";

        $tmp = array();
        foreach ($ary as $unvalidatedKey => $unvalidatedVal) {
            try {
                $safeKey = $this->_getIfValid(
                    '$_COOKIES Index', $unvalidatedKey, $ptnKey,
                    'HTTP Cookie Name Validator', 4094, false
                );
                $maxValLen = 4096 - 1 - mb_strlen($safeKey, 'ASCII');
                $safeVal = $this->_getIfValid(
                    '$_COOKIES Index', $unvalidatedVal, $ptnVal,
                    'HTTP Cookie Value Validator', $maxValLen, true
                );
                $tmp[$safeKey] = $safeVal;
            } catch (Exception $e) {
                // Validation or Intrusion Exceptions perform auto logging.
            }
        }
        return $tmp;

    }


    /**
     * Helper method to validate input and return the canonicalized, validated value
     * if valid.
     *
     * @param string $context   A description of the input to be validated.
     * @param string $input     The input to validate.
     * @param string $pattern   The regex pattern against which to validate the
     *                          supplied input.
     * @param string $type      A descriptive name for the StringValidationRule.
     * @param int    $maxLength The maximum post-canonicalized length of valid
     *                          inputs.
     * @param bool   $allowNull Whether an empty string is considered valid input.
     *
     * @return string canonicalized, valid inputs only.
     *
     * @throws ValidationException
     */
    private function _getIfValid($context, $input, $pattern, $type, $maxLength, $allowNull)
    {
        $validationRule = new StringValidationRule($type, $this->_encoder);

        if ($pattern != null) {
            $validationRule->addWhitelistPattern($pattern);
        }

        $validationRule->setMaximumLength($maxLength);
        $validationRule->setAllowNull($allowNull);

        return $validationRule->getValid($context, $input);
    }


    /**
     * Helper method which hex encodes characters in the supplied array of strings
     * and ordinal numbers for use as a pattern supplied to preg_match.
     *
     * @param array $charsets Array of strings and or single ordinal numbers.
     *
     * @return string hex encoded characters for a preg_match pattern.
     */
    private function _hexifyCharsForPattern($charsets)
    {
        $s = '';
        foreach ($charsets as $set) {
            if ($set === (int) $set) {
                $s .= chr($set);
            } else {
                $s .= $set;
            }
        }

        $hex = '';
        $limit = mb_strlen($s, 'ASCII');
        for ($i = 0; $i < $limit; $i++) {
            list(, $ord) = unpack("C", mb_substr($s, $i, 1, 'ASCII'));
            $h = dechex($ord);
            $pad = mb_strlen($h, 'ASCII') == 1 ? '0' : '';
            $hex .= '\\x' . $pad . $h;
        }
        return $hex;
    }

}
