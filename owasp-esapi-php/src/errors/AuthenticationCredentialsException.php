<?php
/**
 * OWASP Enterprise Security API (ESAPI)
 *
 * This file is part of the Open Web Application Security Project (OWASP)
 * Enterprise Security API (ESAPI) project.
 *
 * LICENSE: This source file is subject to the New BSD license.  You should read
 * and accept the LICENSE before you use, modify, and/or redistribute this
 * software.
 * 
 * PHP version 5.2
 *
 * @category  OWASP
 * @package   ESAPI_Errors
 * @author    Andrew van der Stock <vanderaj@owasp.org>
 * @author    Mike Boberski <boberski_michael@bah.com>
 * @copyright 2009-2010 The OWASP Foundation
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD license
 * @version   SVN: $Id$
 * @link      http://www.owasp.org/index.php/ESAPI
 */

require_once dirname(__FILE__).'/AuthenticationException.php';

/**
 * An AuthenticationException should be thrown when anything goes wrong during
 * login or logout. They are also appropriate for any problems related to
 * identity management.
 *
 * @category  OWASP
 * @package   ESAPI_Errors
 * @author    Andrew van der Stock <vanderaj@owasp.org>
 * @author    Mike Boberski <boberski_michael@bah.com>
 * @copyright 2009-2010 The OWASP Foundation
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD license
 * @version   Release: @package_version@
 * @link      http://www.owasp.org/index.php/ESAPI
 */
class AuthenticationCredentialsException extends AuthenticationException
{
    /**
     * Instantiates a new authentication exception.
     * 
     * @param string $userMessage the message displayed to the user
     * @param string $logMessage  the message logged
     * 
     * @return does not return a value.
     */
    function __construct($userMessage = '', $logMessage = '')
    {
        parent::__construct($userMessage, $logMessage);
    }
}
?>