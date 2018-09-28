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
 * @author    Andrew van der Stock <vanderaj@owasp.org>
 * @author    Laura Bell <laura.d.bell@gmail.com>
 * @author    jah <jah@jahboite.co.uk>
 * @author    Mike Boberski <boberski_michael@bah.com>
 * @copyright 2009-2010 The OWASP Foundation
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD license
 * @version   SVN: $Id$
 * @link      http://www.owasp.org/index.php/ESAPI
 */

/**
 *
 */
require_once dirname(__FILE__) .
    '/../../lib/apache-log4php/trunk/src/main/php/Logger.php';
require_once dirname(__FILE__).'/../Auditor.php';


/**
 * Reference Implementation of the Auditor interface.
 *
 * @category  OWASP
 * @package   ESAPI_Reference
 * @author    Andrew van der Stock <vanderaj@owasp.org>
 * @author    Laura Bell <laura.d.bell@gmail.com>
 * @author    jah <jah@jahboite.co.uk>
 * @author    Mike Boberski <boberski_michael@bah.com>
 * @copyright 2009-2010 The OWASP Foundation
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD license
 * @version   Release: @package_version@
 * @link      http://www.owasp.org/index.php/ESAPI
 */
class DefaultAuditor implements Auditor
{

    /**
     * An instance of Apache Log4PHP.
     *
     * @var Logger
     */
    private $_log4php;
    private $_log4phpName;
    private $_appName = null;
    private static $_initialised = false;

    /**
     * DefaultAuditor constructor.
     * 
     * @param string $name logger name.
     * 
     * @return does not return a value.
     */
    function __construct($name)
    {
        if (self::$_initialised == false) {
            self::_initialise();
        }
        $this->_log4php = Logger::getLogger($name);
        $this->_log4phpName = $name;

        // set ApplicationName only if it is to be logged.
        $sc = ESAPI::getSecurityConfiguration();
        if ($sc->getLogApplicationName()) {
            $this->_appName = $sc->getApplicationName();
        }
    }

    /**
     * @inheritdoc
     */
    public function setLevel($level)
    {
        try
        {
            $this->_log4php->setLevel(
                $this->_convertESAPILeveltoLoggerLevel($level)
            );
        }
        catch (Exception $e)
        {
            $this->error(
                Logger::SECURITY,
                false,
                'IllegalArgumentException',
                $e
            );
        }
    }


    /**
     * @inheritdoc
     */
    function fatal($type, $success, $message, $throwable = null)
    {
        $this->_log(Auditor::FATAL, $type, $success, $message, $throwable);
    }


    /**
     * @inheritdoc
     */
    function isFatalEnabled()
    {
        return $this->_log4php->isEnabledFor(LoggerLevel::getLevelFatal());
    }


    /**
     * @inheritdoc
     */
    function error($type, $success, $message, $throwable = null)
    {
        $this->_log(Auditor::ERROR, $type, $success, $message, $throwable);
    }


    /**
     * @inheritdoc
     */
    function isErrorEnabled()
    {
        return $this->_log4php->isEnabledFor(LoggerLevel::getLevelError());
    }


    /**
     * @inheritdoc
     */
    function warning($type, $success, $message, $throwable = null)
    {
        $this->_log(Auditor::WARNING, $type, $success, $message, $throwable);
    }


    /**
     * @inheritdoc
     */
    function isWarningEnabled()
    {
        return $this->_log4php->isEnabledFor(LoggerLevel::getLevelWarn());
    }


    /**
     * @inheritdoc
     */
    function info($type, $success, $message, $throwable = null)
    {
        $this->_log(Auditor::INFO, $type, $success, $message, $throwable);
    }


    /**
     * @inheritdoc
     */
    function isInfoEnabled()
    {
        return $this->_log4php->isEnabledFor(LoggerLevel::getLevelInfo());
    }


    /**
     * @inheritdoc
     */
    function debug($type, $success, $message, $throwable = null)
    {
        $this->_log(Auditor::DEBUG, $type, $success, $message, $throwable);
    }


    /**
     * @inheritdoc
     */
    function isDebugEnabled()
    {
        return $this->_log4php->isEnabledFor(LoggerLevel::getLevelDebug());
    }


    /**
     * @inheritdoc
     */
    function trace($type, $success, $message, $throwable = null)
    {
        $this->_log(Auditor::TRACE, $type, $success, $message, $throwable);
    }


    /**
     * @inheritdoc
     */
    function isTraceEnabled()
    {
        return $this->_log4php->isEnabledFor(LoggerLevel::getLevelAll());
    }


    /**
     * Helper function.
     *
     * If the supplied logging level is at or above the current logging
     * threshold then log the message after optionally encoding any special
     * characters that might be dangerous when viewed by an HTML based log
     * viewer. Also encode any carriage returns and line feeds to prevent log
     * injection attacks. This logs all the supplied parameters: level, event
     * type, whether the event represents success or failure and the log
     * message. In addition, the application name, logger name/category, local
     * IP address and port, the identity of the user and their source IP
     * address, a logging specific user session ID, and the current date/time
     * are also logged.
     * If the supplied logging level is below the current logging threshold then
     * the message will be discarded.
     *
     * @param int       $level     the priority level of the event - an Logger Level
     *                             constant.
     * @param int       $type      the type of the event - an Logger Event constant.
     * @param bool      $success   boolean true indicates this was a successful 
     *                             event, false indicates this was a failed event 
     *                             (the typical value).
     * @param string    $message   the message to be logged.
     * @param Exception $throwable the throwable Exception.
     * 
     * @return does not return a value.
     */
    private function _log($level, $type, $success, $message, $throwable)
    {
        // If this log level is below the threshold we can quit now.
        $logLevel = self::_convertESAPILeveltoLoggerLevel($level);
        if (! $this->_log4php->isEnabledFor($logLevel)) {
            return;
        }

        $encoder   = ESAPI::getEncoder();
        $secConfig = ESAPI::getSecurityConfiguration();

        // Add some context to log the message.
        $context = '';

        // The output of log level is handled here instead of providing a
        // LayoutPattern to Log4PHP.  This allows us to print TRACE instead of
        // ALL and WARNING instead of WARN.
        $levelStr = $logLevel->toString();
        if ($levelStr == 'ALL') {
            $levelStr = 'TRACE';
        } else if ($levelStr == 'WARN') {
            $levelStr = 'WARNING';
        }
        $context .= $levelStr;

        // Application name.
        // $this->appName is set only if it is to be logged.
        if ($this->_appName !== null) {
            $context .= ' ' . $this->_appName;
        }

        // Logger name (Category in Log4PHP parlance)
        $context .= ' ' . $this->_log4phpName;

        // Event Type
        if (! is_string($type)) {
            $type = 'EVENT_UNKNOWN';
        }
        $context .= ' ' . $type;

        // Success or Failure of Event
        if ($success === true) {
            $context .= '-SUCCESS';
        } else {
            $context .= '-FAILURE';
        }

        $request = ESAPI::getHttpUtilities()->getCurrentRequest();
        if ($request === null) {
            $request = new SafeRequest;
            ESAPI::getHttpUtilities()->setCurrentHTTP($request);
        }
        
        $laddr = $request->getServerName();
        if ($laddr === '') {
            $laddr = 'UnknownLocalHost';
        }
        $lport = $request->getServerPort();
        
        $ruser = $request->getRemoteUser();
        if ($ruser === '') {
            $ruser = 'AnonymousUser';
        }
        $raddr = $request->getRemoteAddr();
        if ($raddr === '') {
            $raddr = 'UnknownRemoteHost';
        }
        
        $context .= " {$laddr}:{$lport} {$ruser}@{$raddr}";

        // create a random session number for the user to represent the
        // user's session, if it doesn't exist already
        $userSessionIDforLogging = 'SessionUnknown';
        if (isset($_SESSION)) {
            if (isset($_SESSION['DefaultAuditor'])
                && isset($_SESSION['DefaultAuditor']['SessionIDForLogging'])
            ) {
                $userSessionIDforLogging
                    = $_SESSION['DefaultAuditor']['SessionIDForLogging'];
            } else {
                try
                {
                    $userSessionIDforLogging
                        = (string) ESAPI::getRandomizer()->getRandomInteger(
                            0, 1000000
                        );
                    $_SESSION['DefaultAuditor']['SessionIDForLogging']
                        = $userSessionIDforLogging;
                } catch( Exception $e ) {
                    // continue
                }
            }
        }
        $context .= "[ID:{$userSessionIDforLogging}]";


        // Now comes the message.
        if (! is_string($message)) {
            $message = '';
        }

        // Encode CRLF - this bit might have to go in a try block
        // Codec Debugging entries are not affected.
        if (defined('CD_LOG') == true && $this->_log4phpName === CD_LOG) {
            $crlfEncoded = $message;
        } else {
            $crlfEncoded = $this->_replaceCRLF($message, '_');
        }

        // Encode for HTML if ESAPI.xml says so
        $encodedMessage = null;
        if ($secConfig->getLogEncodingRequired() ) {
            try
            {
                $encodedMessage = $encoder->encodeForHTML($crlfEncoded);
                if ($encodedMessage !== $crlfEncoded) {
                    $encodedMessage .= ' (This log message was encoded for HTML)';
                }
            }
            catch (Exception $e)
            {
                $exType = get_type($e);
                $encodedMessage = "The supplied log message generated an ".
                    "Exception of type {$exType} and was not included";
            }
        } else {
            $encodedMessage = $crlfEncoded;
        }

        // Now handle the exception
        $dumpedException = '';
        if ($throwable !== null && $throwable instanceof Exception) {
            $dumpedException = ' ' . $this->_replaceCRLF($throwable, ' | ');
        }

        $messageForLog = $context . ' ' . $encodedMessage . $dumpedException;

        $this->_log4php->log($logLevel, $messageForLog, $this);
    }


    /**
     * Helper function.
     * 
     * Helper method to replace carriage return and line feed characters in the
     * supplied message with the supplied substitute character(s). The sequence
     * CRLF (\r\n) is treated as one character.
     *
     * @param string $message    message to process.
     * @param string $substitute replacement for CR, LF or CRLF.
     *
     * @return string message with characters replaced.
     */
    private function _replaceCRLF($message, $substitute)
    {
        if ($message === null || $substitute === null) {
            return $message;
        }
        $detectedEncoding = Codec::detectEncoding($message);
        $len = mb_strlen($message, $detectedEncoding);
        $crlfEncoded = '';
        $nextChar = null;
        $index = 0;
        for ($i = 0; $i < $len; $i++) {
            if ($i < $index) {
                continue;
            }
            if ($nextChar === null) {
                $thisChar = mb_substr($message, $i, 1, $detectedEncoding);
            } else {
                $thisChar = $nextChar;
            }
            if ($i+1 < $len) {
                $nextChar = mb_substr($message, $i+1, 1, $detectedEncoding);
            } else {
                $nextChar = null;
            }
            if ($thisChar == "\r" && $nextChar == "\n") {
                $index = $i+2;
                $nextChar = null;
                $crlfEncoded .= $substitute;
            } else if ($thisChar == "\r" || $thisChar == "\n") {
                $crlfEncoded .= $substitute;
            } else {
                $crlfEncoded .= $thisChar;
            }
        }
        return $crlfEncoded;
    }

    /**
     * Helper function.
     * 
     * Converts a logging level.
     *
     * Converts the ESAPI logging level (a number) or level defined in the ESAPI
     * properties file (a string) into the levels used by Apache's log4php. Note
     * that log4php does not define a TRACE level and so TRACE is simply an
     * alias of ALL which log4php does define.
     *
     * @param int $level The logging level to convert.
     *
     * @return int The log4php logging Level equivalent.
     * @throws Exception if the supplied level doesn't match a level currently
     *                   defined.
     */
    private static function _convertESAPILeveltoLoggerLevel($level)
    {
        if (is_string($level)) {
            switch (strtoupper($level)) {
            case 'ALL':
                /* Same as TRACE */
            case 'TRACE':
                return LoggerLevel::getLevelAll();
            case 'DEBUG':
                return LoggerLevel::getLevelDebug();
            case 'INFO':
                return LoggerLevel::getLevelInfo();
            case 'WARN':
                return LoggerLevel::getLevelWarn();
            case 'ERROR':
                return LoggerLevel::getLevelError();
            case 'FATAL':
                return LoggerLevel::getLevelFatal();
            case 'OFF':
                return LoggerLevel::getLevelOff();
            default:
                throw new Exception(
                    "Invalid logging level Value was: {$level}"
                );
            }
        } else {
            switch ($level) {
            case Auditor::ALL:
                /* Same as TRACE */
            case Auditor::TRACE:
                return LoggerLevel::getLevelAll();
            case Auditor::DEBUG:
                return LoggerLevel::getLevelDebug();
            case Auditor::INFO:
                return LoggerLevel::getLevelInfo();
            case Auditor::WARNING:
                return LoggerLevel::getLevelWarn();
            case Auditor::ERROR:
                return LoggerLevel::getLevelError();
            case Auditor::FATAL:
                return LoggerLevel::getLevelFatal();
            case Auditor::OFF:
                return LoggerLevel::getLevelOff();
            default:
                throw new Exception(
                    "Invalid logging level Value was: {$level}"
                );
            }
        }
    }


    /**
     *  Helper function.
     *  
     *  Configures Apache's Log4PHP RootLogger based on values obtained from the
     *  ESAPI properties file.  All instances of Log4PHP Logger will inherit the
     *  configuration.
     *  
     *  @return does not return a value.
     */
    private static function _initialise()
    {
        self::$_initialised = true;

        $secConfig = ESAPI::getSecurityConfiguration();
        $logLevel = $secConfig->getLogLevel();

        // Patterns representing the format of Log entries
        // d date, p priority (level), m message, n newline
        $dateFormat = $secConfig->getLogFileDateFormat();
        $logfileLayoutPattern = "%d{{$dateFormat}} %m %n";

        // LogFile properties.
        $logFileName = $secConfig->getLogFileName();
        $maxLogFileSize = $secConfig->getMaxLogFileSize();
        $maxLogFileBackups = $secConfig->getMaxLogFileBackups();

        // LogFile layout
        $logfileLayout = new LoggerLayoutPattern();
        $logfileLayout->setConversionPattern($logfileLayoutPattern); 

        // LogFile RollingFile Appender
        $appenderLogfile = new LoggerAppenderRollingFile('ESAPI LogFile');
        $appenderLogfile->setFile($logFileName, true);
        $appenderLogfile->setMaxFileSize($maxLogFileSize);
        $appenderLogfile->setMaxBackupIndex($maxLogFileBackups);
        $appenderLogfile->setLayout($logfileLayout);
        if ($logLevel !== 'OFF') {
            $appenderLogfile->activateOptions();
        }

        // Get the RootLogger and reset it, before adding our Appenders and
        // setting our Loglevel
        $rootLogger = Logger::getRootLogger();
        $rootLogger->removeAllAppenders();
        $rootLogger->addAppender($appenderLogfile);
        $rootLogger->setLevel(
            self::_convertESAPILeveltoLoggerLevel($logLevel)
        );
    }
}
