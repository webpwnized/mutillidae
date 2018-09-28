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

/**
 * EnterpriseSecurityException is the base class for all security related 
 * exceptions. You should pass in the root cause exception where possible. 
 * Constructors for classes extending EnterpriseSecurityException should be 
 * sure to call the appropriate super() method in order to ensure that logging 
 * and intrusion detection occur properly.
 * <P>
 * All EnterpriseSecurityExceptions have two messages, one for the user and one 
 * for the log file. This way, a message can be shown to the user that doesn't 
 * contain sensitive information or unnecessary implementation details. Meanwhile,
 * all the critical information can be included in the exception so that it gets 
 * logged.
 * <P>
 * Note that the "logMessage" for ALL EnterpriseSecurityExceptions is logged in 
 * the log file. This feature should be used extensively throughout ESAPI 
 * implementations and the result is a fairly complete set of security log records.
 * ALL EnterpriseSecurityExceptions are also sent to the IntrusionDetector for use 
 * in detecting anomolous patterns of application usage.
 * <P>
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
class EnterpriseSecurityException extends Exception
{
    /** The logger. */
    protected $logger;
    protected $logMessage = null;

    /**
     * Creates a new instance of EnterpriseSecurityException that includes a 
     * root cause 
     * 
     * @param string $userMessage the message displayed to the user
     * @param string $logMessage  the message logged
     * 
     * @return does not return a value.
      */
    public function __construct($userMessage = '', $logMessage = '')
    {
        $cause = 0;
        
        if ( empty($userMessage) ) {
            $userMessage = null;
            
        }
                
        parent::__construct($userMessage);
        
        $this->logMessage = $logMessage;
        $this->logger = ESAPI::getAuditor("EnterpriseSecurityException");
        if (! ESAPI::getSecurityConfiguration()->getDisableIntrusionDetection()) {
            ESAPI::getIntrusionDetector()->addException($this);
        }
    }

    /**
     * Returns message that is safe to display to users
     * 
     * @return string a String containing a message that is safe to display to 
     *                users
     */
    public function getUserMessage()
    {
        return $this->getMessage();
    }

    /**
     * Returns a message that is safe to display in logs, but probably not to users
     * 
     * @return string a String containing a message that is safe to display in 
     *                logs, but probably not to users
     */
    public function getLogMessage()
    {
        return $this->logMessage;
    }

}
?>