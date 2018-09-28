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
 * @author    Laura Bell <laura.d.bell@gmail.com>
 * @author    Mike Boberski <boberski_michael@bah.com>
 * @author    jah <jah@jahboite.co.uk>
 * @copyright 2009-2010 The OWASP Foundation
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD license
 * @version   SVN: $Id$
 * @link      http://www.owasp.org/index.php/ESAPI
 */

/**
 * Use this ESAPI security control to wrap your auditing functions.
 * 
 * The idea behind this interface is to define a set of functions that can 
 * be used to log security events. Implementors should use a well 
 * established logging library.
 *
 * @category  OWASP
 * @package   ESAPI
 * @author    Andrew van der Stock <vanderaj@owasp.org>
 * @author    Laura Bell <laura.d.bell@gmail.com>
 * @author    jah <jah@jahboite.co.uk>
 * @author    Mike Boberski <boberski_michael@bah.com>
 * @copyright 2009-2010 The OWASP Foundation
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD license
 * @version   Release: @package_version@
 * @link      http://www.owasp.org/index.php/ESAPI
 */
interface Auditor
{

    /*
     * The Logger interface defines 4 event types: SECURITY, USABILITY,
     * PERFORMANCE and FUNCTIONALITY.  The reference implementation of ESAPI
     * submits events for logging of the type SECURITY (exlusively).
     */

    /**
     * The SECURITY type of log event.
     */
    const SECURITY = 'SECURITY';

    /**
     * The USABILITY type of log event.
     */
    const USABILITY = 'USABILITY';

    /**
     * The PERFORMANCE type of log event.
     */
    const PERFORMANCE = 'PERFORMANCE';

    /**
     * The FUNCTIONALITY type of log event. This is the type of event that
     * non-security focused loggers typically log. If you are going to log your
     * existing non-security events in the same log with your security events,
     * you probably want to use this type of log event.
     */
    const FUNCTIONALITY = 'FUNCTIONALITY';

    /*
     * The Logger interface defines 6 logging levels: FATAL, ERROR,
     * WARNING, INFO, DEBUG, TRACE. It also supports ALL, an alias of TRACE
     * which logs all events, and OFF, which disables all logging. Your
     * implementation can extend or change this list if desired.
     */

    /**
     * OFF indicates that no messages should be logged.
     * This level is initialized to PHP_INT_MAX.
     */
    const OFF = PHP_INT_MAX;

    /**
     * FATAL indicates that only FATAL messages should be logged.
     * This level is initialized to 1000.
     */
    const FATAL   = 1000;

    /**
     * ERROR indicates that ERROR messages and above should be logged.
     * This level is initialized to 800.
     */
    const ERROR   = 800;

    /**
     * WARNING indicates that WARNING messages and above should be logged.
     * This level is initialized to 600.
     */
    const WARNING = 600;

    /**
     * INFO indicates that INFO messages and above should be logged.
     * This level is initialized to 400.
     */
    const INFO    = 400;

    /**
     * DEBUG indicates that DEBUG messages and above should be logged.
     * This level is initialized to 200.
     */
    const DEBUG   = 200;

    /**
     * TRACE indicates that TRACE messages and above should be logged.
     * This level is initialized to 100.
     */
    const TRACE   = 100;

    /**
     * ALL indicates that all messages should be logged.
     * This level is initialized to 0.
     */
    const ALL   = 0;


    /**
     * Dynamically set the logging severity level. All events of this level and
     * higher will be logged from this point forward for all logs. All events
     * below this level will be discarded.
     *
     * @param int $level The level to set the logging level to.
     * 
     * @return does not return a value.
     */
    function setLevel($level);


    /**
     * Log a fatal level security event if 'fatal' level logging is enabled and
     * also record the stack trace associated with the event.
     *
     * @param int    $type      the type of event - an Logger Type constant.
     * @param bool   $success   boolean true indicates this was a successful event, 
     *                          false indicates this was a failed event (the typical
     *                          value).
     * @param string $message   the message to log.
     * @param string $throwable the exception to be logged.
     * 
     * @return does not return a value.
     */
    function fatal($type, $success, $message, $throwable = null);


    /**
     * Allows the caller to determine if messages logged at this level will be
     * discarded, to avoid performing expensive processing.
     *
     * @return bool true if fatal level messages will be output to the log.
     */
    function isFatalEnabled();


    /**
     * Log an error level security event if 'error' level logging is enabled and
     * also record the stack trace associated with the event.
     *
     * @param int    $type      the type of event - an Logger Type constant.
     * @param bool   $success   boolean true indicates this was a successful event, 
     *                          false indicates this was a failed event (the typical
     *                          value).
     * @param string $message   the message to log.
     * @param string $throwable the exception to be logged.
     * 
     * @return does not return a value.
     */
    function error($type, $success, $message, $throwable = null);


    /**
     * Allows the caller to determine if messages logged at this level will be
     * discarded, to avoid performing expensive processing.
     *
     * @return bool true if error level messages will be output to the log.
     */
    function isErrorEnabled();


    /**
     * Log a warning level security event if 'warning' level logging is enabled and
     * also record the stack trace associated with the event.
     *
     * @param int    $type      the type of event - an Logger Type constant.
     * @param bool   $success   boolean true indicates this was a successful event,
     *                          false indicates this was a failed event (the typical
     *                          value).
     * @param string $message   the message to log.
     * @param string $throwable the exception to be logged.
     * 
     * @return does not return a value.
     */
    function warning($type, $success, $message, $throwable = null);


    /**
     * Allows the caller to determine if messages logged at this level will be
     * discarded, to avoid performing expensive processing.
     *
     * @return bool true if warning level messages will be output to the log.
     */
    function isWarningEnabled();


    /**
     * Log an info level security event if 'info' level logging is enabled and
     * also record the stack trace associated with the event.
     *
     * @param int    $type      the type of event - an Logger Type constant.
     * @param bool   $success   boolean true indicates this was a successful event,
     *                          false indicates this was a failed event (the 
     *                          typical value).
     * @param string $message   the message to log.
     * @param string $throwable the exception to be logged.
     * 
     * @return does not return a value
     */
    function info($type, $success, $message, $throwable = null);


    /**
     * Allows the caller to determine if messages logged at this level will be
     * discarded, to avoid performing expensive processing.
     *
     * @return bool true if info level messages will be output to the log.
     */
    function isInfoEnabled();


    /**
     * Log a debug level security event if 'debug' level logging is enabled and
     * also record the stack trace associated with the event.
     *
     * @param int    $type      the type of event - an Logger Type constant.
     * @param bool   $success   boolean true indicates this was a successful event,
     *                          false indicates this was a failed event (the 
     *                          typical value).
     * @param string $message   the message to log.
     * @param string $throwable the exception to be logged.
     * 
     * @return does not return a value
     */
    function debug($type, $success, $message, $throwable = null);


    /**
     * Allows the caller to determine if messages logged at this level will be
     * discarded, to avoid performing expensive processing.
     *
     * @return bool true if debug level messages will be output to the log.
     */
    function isDebugEnabled();


    /**
     * Log a trace level security event if 'trace' level logging is enabled and
     * also record the stack trace associated with the event.
     *
     * @param int    $type      the type of event - an Logger Type constant.
     * @param bool   $success   boolean true indicates this was a successful event, 
     *                          false indicates this was a failed event (the typical
     *                          value).
     * @param string $message   the message to log.
     * @param string $throwable the exception to be logged.
     * 
     * @return does not return a value.
     */
    function trace($type, $success, $message, $throwable = null);


    /**
     * Allows the caller to determine if messages logged at this level will be
     * discarded, to avoid performing expensive processing.
     *
     * @return bool true if trace level messages will be output to the log.
     */
    function isTraceEnabled();

}
