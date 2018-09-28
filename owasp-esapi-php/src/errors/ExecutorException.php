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

require_once dirname(__FILE__).'/EnterpriseSecurityException.php';

/**
 * An ExecutorException should be thrown for any problems that arise during the
 * execution of a system executable.
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
class ExecutorException extends EnterpriseSecurityException
{
    /**
     * Create a new ExecutorException
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