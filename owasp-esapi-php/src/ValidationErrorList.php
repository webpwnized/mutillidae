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
 * @author    Mike Boberski <boberski_michael@bah.com>
 * @author    Andrew van der Stock <vanderaj@owasp.org>
 * @copyright 2009-2010 The OWASP Foundation
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD license
 * @version   SVN: $Id$
 * @link      http://www.owasp.org/index.php/ESAPI
 */

require_once dirname(__FILE__).'/errors/ValidationException.php';

/**
 * Use this ESAPI security control to enumerate validation exceptions.
 * 
 * The idea behind this interface is to define a well-formed collection of 
 * ValidationExceptions so that groups of validation functions can be 
 * called in a non-blocking fashion.
 *
 * @category  OWASP
 * @package   ESAPI
 * @author    Mike Boberski <boberski_michael@bah.com>
 * @author    Andrew van der Stock <vanderaj@owasp.org>
 * @copyright 2009-2010 The OWASP Foundation
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD license
 * @version   Release: @package_version@
 * @link      http://www.owasp.org/index.php/ESAPI
 */
class ValidationErrorList
{

    /**
     * Error list of ValidationException's
     */
    // private HashMap errorList = new HashMap();

    /**
     * Adds a new error to list with a unique named context.
     * No action taken if either element is null. 
     * Existing contexts will be overwritten.
     * 
     * @param string $context unique named context for this ValidationErrorList
     * @param string $ve      todo
     * 
     * @return string todo
     */
    public function addError($context, $ve) 
    {
        //		if (getError(context) != null) throw new RuntimeException("Context (" + context + ") already exists, programmer error");
        //		
        //		if ((context != null) && (ve != null)) {
        //			errorList.put(context, ve);
        //		}
    }

    /**
     * Returns list of ValidationException, or empty list of no errors exist.
     * 
     * @return arrray todo
     */
    public function errors() 
    {
        //		return new ArrayList( errorList.values() );
    }

    /**
     * Retrieves ValidationException for given context if one exists.
     * 
     * @param string $context unique name for each error
     * 
     * @return ValidationException or null for given context
     */
    public function getError($context) 
    {
        //		if (context == null) return null;		
        //		return (ValidationException)errorList.get(context);
    }

    /**
     * Returns true if no error are present.
     * 
     * @return bool todo
     */
    public function isEmpty() 
    {
        //		return errorList.isEmpty();
    }

    /**
     * Returns the numbers of errors present.
     * 
     * @return bool todo
     */
    public function size() 
    {
        //		return errorList.size();
    }
}
