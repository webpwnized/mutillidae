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
 * @package   ESAPI_Reference
 * @author    Jeff Williams <jeff.williams@aspectsecurity.com>
 * @author    jah <jah@jahboite.co.uk>
 * @copyright 2009-2010 The OWASP Foundation
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD license
 * @version   SVN: $Id$
 * @link      http://www.owasp.org/index.php/ESAPI
 */


/**
 * DefaultIntrusionDetector requires the IntrusionDetector interface.
 */
require_once dirname(__FILE__) . '/../IntrusionDetector.php';


/**
 * Reference implementation of the IntrusionDetector interface.
 *
 * This implementation monitors EnterpriseSecurityExceptions, custom Exceptions
 * and other custom events to see if any user exceeds a configurable threshold
 * in a configurable time period.
 * For example, it can monitor to see if a user exceeds 10 input validation
 * issues in a 1 minute period. Or if there are more than 3 authentication
 * problems in a 10 second period. More complex implementations are certainly
 * possible, such as one that establishes a baseline of expected behaviour, and
 * then detects deviations from that baseline.
 * Events are persisted in the PHP Session, if one is available at the time they
 * are generated, to allow tracking of events across requests.  If a PHP Session
 * is not available then the events are persisted only as long as the current
 * DefaultIntrusionDetector instance.
 *
 * @category  OWASP
 * @package   ESAPI_Reference
 * @author    Jeff Williams <jeff.williams@aspectsecurity.com>
 * @author    jah <jah@jahboite.co.uk>
 * @copyright 2009-2010 The OWASP Foundation
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD license
 * @version   Release: @package_version@
 * @link      http://www.owasp.org/index.php/ESAPI
 */
class DefaultIntrusionDetector implements IntrusionDetector
{

    private $_auditor     = null;
    private $_userEvents = null;


    /**
     * Constructor stores an instance of Auditor for logging and initialises the
     * storage for events generated for a user.
     *
     * @return null
     */
    function __construct()
    {
        $this->_auditor = ESAPI::getAuditor('IntrusionDetector');
        $this->_userEvents = array();
    }


    /**
     * Adds an exception to the IntrusionDetector.
     *
     * This method immediately logs the supplied exception and stores it in
     * order to check if the request causes a threshold to be reached for any
     * EnterpriseSecurity Exceptions. If any security thresholds are reached
     * then the resultant IntrusionException is handled and the appropriate
     * security action taken and logged.
     *
     * @param Exception $exception The exception to add.
     *
     * @return null
     */
    public function addException($exception)
    {
        $secConfig = ESAPI::getSecurityConfiguration();

        if ($secConfig->getDisableIntrusionDetection()) {
            return;
        }

        if ($exception instanceof EnterpriseSecurityException) {
            $this->_auditor->warning(
                Auditor::SECURITY, false,
                $exception->getLogMessage(), $exception
            );
        } else {
            $this->_auditor->warning(
                Auditor::SECURITY, false,
                $exception->getMessage(), $exception
            );
        }

        // add the exception, which may trigger a detector
        $eventName = get_class($exception);
        try
        {
            $this->_addSecurityEvent($eventName);
        }
        catch (IntrusionException $intrusionException)
        {
            $quota = ESAPI::getSecurityConfiguration()->getQuota($eventName);
            $message = 'User exceeded quota of ' . $quota->count . ' per ' .
                $quota->interval . ' seconds for event ' . $eventName .
                sprintf(
                    '. Taking the following %d action%s - ',
                    count($quota->actions),
                    count($quota->actions) > 1 ? 's' : ''
                )
                . implode(', ', $quota->actions) . '.';

            foreach ($quota->actions as $action) {
                $this->_takeSecurityAction($action, $message);
            }
        }


    }


    /**
     * Adds an event to the IntrusionDetector.
     *
     * This method immediately logs the event and stores it in order to check if
     * the request causes a threshold to be reached for any Enterprise Security
     * Exceptions. If any security thresholds are reached then the resultant
     * IntrusionException is handled and the appropriate security action taken
     * and logged.
     *
     * @param string $eventName  The event to add.
     * @param string $logMessage Message to log with the event.
     *
     * @return null
     */
    public function addEvent($eventName, $logMessage)
    {
        $secConfig = ESAPI::getSecurityConfiguration();

        if ($secConfig->getDisableIntrusionDetection()) {
            return;
        }

        $this->_auditor->warning(
            Auditor::SECURITY,
            false,
            "Security event {$eventName} received - {$logMessage}"
        );

        // add the event, which may trigger an IntrusionException
        try
        {
            $this->_addSecurityEvent($eventName);
        }
        catch (IntrusionException $intrusionException)
        {
            $quota = $secConfig->getQuota($eventName);
            $message = 'User exceeded quota of ' . $quota->count . ' per ' .
                $quota->interval . ' seconds for event ' . $eventName .
                sprintf(
                    '. Taking the following %d action%s - ',
                    count($quota->actions),
                    count($quota->actions) > 1 ? 's' : ''
                )
                . implode(', ', $quota->actions) . '.';

            foreach ($quota->actions as $action) {
                $this->_takeSecurityAction($action, $message);
            }
        }
    }


    /**
     * Take a specified security action.
     *
     * At the moment the only acceptable action in this implementation is: log.
     * Other actions will be ignored.
     *
     * @param string $action  The action to take.
     * @param string $message Message to log where the action is 'log'.
     *
     * @return null
     */
    private function _takeSecurityAction($action, $message)
    {
        if ($action == 'log' ) {
            $this->_auditor->fatal(
                Auditor::SECURITY,
                false,
                "INTRUSION - {$message}"
            );
        }
    }


     /**
     * Adds a security event.  These events are used to check that the user has
     * not reached the security thresholds set in the properties file.  If a PHP
     * session has been started the events are stored there, otherwise they are
     * merely stored as an instance property.  This means that if a session has
     * not been started prior to calling this function then events will not be
     * tracked across requests.
     *
     * @param string $eventName The name of the event that occurred.
     *
     * @return null
     */
    private function _addSecurityEvent($eventName)
    {
        // if there is a threshold, then track this event
        $threshold = ESAPI::getSecurityConfiguration()->getQuota($eventName);
        if ($threshold === null) {
            return;
        }

        // determine the storage for events
        if (isset($_SESSION)) {
            if (! array_key_exists('ESAPI', $_SESSION)) {
                $_SESSION['ESAPI'] = array();
            }
            if (! array_key_exists('IntrusionDetector', $_SESSION['ESAPI'])) {
                $_SESSION['ESAPI']['IntrusionDetector'] = array();
            }
            if (! array_key_exists('UserEvents', $_SESSION['ESAPI']['IntrusionDetector'])) {
                $_SESSION['ESAPI']['IntrusionDetector']['UserEvents'] = array();
            }
            // If a session was started after events existed then ensure those
            // events are added to the session store
            if (   is_array($this->_userEvents)
                && $this->_userEvents !== $_SESSION['ESAPI']['IntrusionDetector']['UserEvents']
            ) {
                $_SESSION['ESAPI']['IntrusionDetector']['UserEvents']
                    = $this->_userEvents;
            }
            // Assign a reference to the session store
            $this->_userEvents =&
                $_SESSION['ESAPI']['IntrusionDetector']['UserEvents'];
        } else if (! isset($this->_userEvents)) {
            $this->_userEvents = array();
        }

        $event = null;
        if (array_key_exists($eventName, $this->_userEvents)) {
            $event = $this->_userEvents[$eventName];
        }
        if ($event == null) {
            $this->_userEvents[$eventName] = new Event($eventName);
            $event = $this->_userEvents[$eventName];
        }
        if ($threshold->count > 0) {
            $event->increment($threshold->count, $threshold->interval);
        }
    }
}



/**
 * Reference implementation of an Intrusion Event.
 *
 * Represents the count of and times at which a user generated an event that
 * corresponds to a defined IntrusionDetector threshold.  The intrusion detector
 * stores instances of events and invokes their increment method which
 * determines whether the corresponding threshold has been reached.
 *
 * @category  OWASP
 * @package   ESAPI_Reference
 * @author    Jeff Williams <jeff.williams@aspectsecurity.com>
 * @author    jah <jah@jahboite.co.uk>
 * @copyright 2009-2010 The OWASP Foundation
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD license
 * @version   Release: @package_version@
 * @link      http://www.owasp.org/index.php/ESAPI
 */
class Event
{
    private $_key;
    private $_times = array();


    /**
     * @var int $count The number of times this event occurred for a given user.
     */
    public $count = 0;


    /**
     * Constructor stores the supplied key as the event name.
     *
     * @param string $key A name by which the event is known e.g.
     *                   'IntegrityException'.
     *
     * @return null
     */
    public function __construct($key)
    {
        $this->_key = $key;
    }


    /**
     * The increment method increments the number of times this event occurred
     * for this user.
     *
     * Each time increment is called it will decide whether or not to throw an
     * IntrusionException based on the supplied count and interval parameters.
     * If $count is exceeded within $interval seconds then the exception will be
     * thrown.  This implementation maintains a kind of sliding window of
     * timestamps so that it can track event occurrences over time.
     *
     * @param int $count    The event count that will trigger Intrusion Detection
     *                      within the supplied interval.
     * @param int $interval The number of seconds within which the supplied quota of
     *                      event occurrences will trigger Intrusion Detection.
     *
     * @return null
     */
    public function increment($count, $interval)
    {
        $now = null;
        if (function_exists('microtime')) {
            $now = microtime(true);
            $interval = (float) $interval;
        } else {
            $now = time();
        }

        $this->count++;
        array_push($this->_times, $now);

        // if the threshold has been exceeded
        while (sizeof($this->_times) > $count) {
            array_shift($this->_times);
        }

        if (sizeof($this->_times) == $count) {
            $past = reset($this->_times);
            if ($past === false) {
                // this should not happen because events are validated in
                // SecurityConfiguration...
                $past = $now;
            }
            $present = $now;
            if ($present - $past < $interval) {
                throw new IntrusionException(
                    "Threshold exceeded",
                    "Exceeded threshold for " . $this->_key
                );
            }
        }
    }

}
