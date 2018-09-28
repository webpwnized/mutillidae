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
 * @author    jah <jah@jahboite.co.uk>
 * @author    Mike Boberski <boberski_michael@bah.com>
 * @copyright 2009-2010 The OWASP Foundation
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD license
 * @version   SVN: $Id$
 * @link      http://www.owasp.org/index.php/ESAPI
 */

/**
 * Implementations require IntrusionException.
 */
require_once dirname(__FILE__) . '/errors/IntrusionException.php';


/**
 * Use this ESAPI security control to wrap intrusion detection functions
 * that are internal to your application.
 * 
 * The idea behind this interface is to define a set of functions to track 
 * security relevant events and identify attack behavior. 
 *
 * @category  OWASP
 * @package   ESAPI
 * @author    Andrew van der Stock <vanderaj@owasp.org>
 * @author    jah <jah@jahboite.co.uk>
 * @author    Mike Boberski <boberski_michael@bah.com>
 * @copyright 2009-2010 The OWASP Foundation
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD license
 * @version   Release: @package_version@
 * @link      http://www.owasp.org/index.php/ESAPI
 */
interface IntrusionDetector
{

    /**
     * Adds an exception to the IntrusionDetector.
     * 
     * This method should immediately log the exception so that developers
     * throwing an IntrusionException do not have to remember to log every
     * error.  The implementation should store the exception somewhere for the
     * current user in order to check if the User has reached the threshold for
     * any Enterprise Security Exceptions.  The User object is the recommended
     * location for storing the current user's security exceptions.  If the User
     * has reached any security thresholds, the appropriate security action can
     * be taken and logged.
     *
     * @param string $exception string exception thrown.
     * 
     * @return does not return a value
     */
    function addException($exception);


    /**
     * Adds an event to the IntrusionDetector.
     * 
     * This method should immediately log the event.  The implementation should
     * store the event somewhere for the current user in order to check if the
     * User has reached the threshold for any Enterprise Security Exceptions.
     * The User object is the recommended location for storing the current
     * user's security event.  If the User has reached any security thresholds,
     * the appropriate security action can be taken and logged.
     *
     * @param string $eventName  string event to add.
     * @param string $logMessage string message to log with the event.
     * 
     * @return does not return a value.
     */
    function addEvent($eventName, $logMessage);


}
