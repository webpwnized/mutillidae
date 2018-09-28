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

require_once  dirname(__FILE__).'/EnterpriseSecurityException.php';

/**
 * A ValidationException should be thrown to indicate that the data provided by
 * the user or from some other external source does not match the validation
 * rules that have been specified for that data.
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
class ValidationException extends EnterpriseSecurityException
{

    /** The UI reference that caused this ValidationException */
    private $_context;

    /**
     * Instantiates a new ValidationException.
     * Create a new ValidationAvailabilityException
     * 
     * @param string $userMessage the message displayed to the user
     * @param string $logMessage  the message logged
     * @param string $context     the source that caused this exception
     * 
     * @return does not return a value.
     */
    function __construct($userMessage = '', $logMessage = '', $context = '')
    {
        parent::__construct($userMessage, $logMessage);
        $this->setContext($context);
    }

    /**
     * Returns the UI reference that caused this ValidationException
     *  
     * @return string context, the source that caused the exception, stored as a 
     *                string
     */
    public function getContext()
    {
        return $this->_context;
    }

    /**
     * Set's the UI reference that caused this ValidationException
     *  
     * @param string $context the context to set, passed as a String
     * 
     * @return does not return a value.
     */
    public function setContext($context)
    {
        $this->_context = $context;
    }
}
?>