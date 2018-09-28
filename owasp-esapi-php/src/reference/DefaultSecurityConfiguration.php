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
 * @author    Andrew van der Stock <vanderaj@owasp.org>
 * @author    Mike Boberski <boberski_michael@bah.com>
 * @copyright 2009-2010 The OWASP Foundation
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD license
 * @version   SVN: $Id$
 * @link      http://www.owasp.org/index.php/ESAPI
 */

require_once dirname(__FILE__) . '/../SecurityConfiguration.php';

/**
 * Reference Implementation of the SecurityConfiguration interface.
 *
 * @category  OWASP
 * @package   ESAPI_Reference
 * @author    Andrew van der Stock <vanderaj@owasp.org>
 * @author    Mike Boberski <boberski_michael@bah.com>
 * @copyright 2009-2010 The OWASP Foundation
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD license
 * @version   Release: @package_version@
 * @link      http://www.owasp.org/index.php/ESAPI
 */
class DefaultSecurityConfiguration implements SecurityConfiguration
{
    // SimpleXML reads the entire file into memory
    private $_xml = null;
    
    // Authenticator
    
    private $_RememberTokenDuration = null;
    private $_AllowedLoginAttempts = null;
    private $_MaxOldPasswordHashes = null;
    private $_UsernameParameterName = null;
    private $_PasswordParameterName = null;
    private $_IdleTimeoutDuration = null;
    private $_AbsoluteTimeoutDuration = null;
    
    // Encoder
    
    // Executor
    
    private $_AllowedExecutables = null;
    private $_WorkingDirectory = null;
    
    // Encryptor
    
    private $_CharacterEncoding = null;
    private $_MasterKey = null;
    private $_MasterSalt = null;
    private $_EncryptionAlgorithm = null;
    private $_HashAlgorithm = null;
    private $_DigitalSignatureAlgorithm = null;
    private $_RandomAlgorithm = null;
    
    // HTTPUtilities
    
    private $_AllowedFileExtensions = null;
    private $_maxUploadSize = null;
    private $_ResponseContentType = null;
    private $_AllowedIncludes = null;
    private $_AllowedResources = null;
    
    // Logger
    
    private $_ApplicationName = null;
    private $_LogApplicationName = null;
    private $_LogEncodingRequired = null;
    private $_LogLevel = null;
    private $_LogFileName = null;
    private $_MaxLogFileSize = null;
    private $_MaxLogFileBackups = null;
    private $_LogFileDateFormat = null;
    
    // Validator
    
    private $_patternCache = array();
    
    // IntrusionDetector
    
    private $_DisableIntrusionDetection = null;
    
    private $_events = null;
        
    private $_resourceDir = null;
    
    // Special Debugging
    
    private $_SpecialDebugging = null;
    
    /**
     * SecurityConfiguration constructor.
     * 
     * @param string $path configuration file path.
     * 
     * @return does not return a value.
     */
    function __construct($path = '')
    {
        try
        {
            $this->_loadConfiguration($path);
            $this->setResourceDirectory(dirname(realpath($path)));
        } 
        catch ( Exception $e ) 
        {
            $this->_logSpecial($e->getMessage());
        }
    }
    
    /**
     * Helper function.
     * 
     * @param string $path ESAPI configuration file path.
     * 
     * @return does not return a value.
     * @throws Exception thrown if configuration file does not exist.
     */
    private function _loadConfiguration($path)
    {
        if ( file_exists($path) ) {
            $this->_xml = simplexml_load_file($path);
            
            if ( $this->_xml === false ) {
                throw new Exception("Failed to load security configuration.");
            }
        } else {
            throw new Exception("Security configuration file does not exist.");
        }
    }

    /**
     * Helper function.
     * 
     * @return bool TRUE, if able to load events.
     */
    private function _loadEvents() 
    {
        $_events = $this->_xml->xpath('/esapi-properties/IntrusionDetector/event');

        if ( $_events === false ) {
            $this->_events = null;
            $this->_logSpecial(
                'SecurityConfiguration for '.
                '/esapi-properties/IntrusionDetector/event not found in ESAPI.xml.'
            );
            return false;
        }

        $this->_events = array();

        // Cycle through each event
        foreach ($_events as $event) {
            // Obtain data for the event

            $name = (string) $event->attributes()->name;
            $count = (int) $event->attributes()->count;
            $interval = (int) $event->attributes()->interval;
            
            $actions = array();
            foreach ( $event->action as $node ) {
                $actions[] = (string) $node;    
            }
            
            // Validate the event

            if ( !empty($name) && $count > 0 && $interval > 0 && !empty($actions) ) {
                // Add a new threshold object to $_events array
                $this->_events[] = new Threshold(
                    $name, $count, $interval, $actions
                );
            }
        }        
    
        if ( count($this->_events) == 0 ) {
            $this->_events = null;
            $this->_logSpecial(
                'SecurityConfiguration found no valid events in '.
                'the Intrusion Detection section.' 
            );
            return false;
        }
        
        return true;
    }

    /**
     * Helper function.
     * 
     * @param string $msg Message to output to the console.
     * 
     * @return does not return a value.
     */
    private function _logSpecial($msg) 
    {
        echo $msg;
    }
    
    /**
     * Helper function.
     * 
     * @param string $prop Property name.
     * @param string $def  Default value.
     * 
     * @return string property name if found, default value otherwise.
     */
    private function _getESAPIStringProperty($prop, $def) 
    {
        $val = $def;
        
        $var = $this->_xml->xpath('/esapi-properties/'.$prop);

        if ( $var === false ) {
            $this->_logSpecial(
                'SecurityConfiguration for /esapi-properties/'.
                $prop.' not found in ESAPI.xml. Using default: '. $def
            );
        }

        if (isset($var[0]) ) {
            $val = (string) $var[0];
        }
        
        return $val;
    }
    
    /**
     * Helper function.
     * 
     * @param string $prop Property name.
     * @param string $def  Default value.
     * 
     * @return string property name if found, default value otherwise.
     */
    private function _getESAPIArrayProperty($prop, $def) 
    {
        $val = $def;
        
        $var = $this->_xml->xpath('/esapi-properties/'.$prop);

        if ( $var === false ) {
            $this->_logSpecial(
                'SecurityConfiguration for /esapi-properties/'.
                $prop.' not found in ESAPI.xml. Using default: '.$def
            );
        }

        $result = array();
        if (isset($var) ) {
            foreach ($var as $node) {
                $result[] = (string) $node;    
            }
            
            $val = $result;
        }
        
        return $val;
    }
    
    /**
     * Helper function.
     * 
     * @param string $type Regex name.
     * 
     * @return string property name if found, default value otherwise.
     */
    private function _getESAPIValidationExpression($type) 
    {

        $val = null;
        $found = false;
        $i = 0;
        
        $var = $this->_xml->xpath('//regexp');

        if ( $var === false ) {
            $this->_logSpecial(
                'getESAPIValidationExpression: No regular '.
                'expressions in the config file.'
            );
            return false;
        }
            
        if (isset($var[0]) ) {
            while (list( , $node) = each($var)) {
                 $result[] = (string) $node;
                
                foreach ($node->attributes() as $a => $b) {
                    if (!strcmp($a, "name")) {
                        if ( !strcmp((string) $b, $type)) {
                            $val = $var[$i];
                            $found = true;
                            break 2;
                        }
                    }
                }
                $i++;
            }                        
        }
        
        if ( $found && isset($val->attributes()->value) ) {
            return (string)$val->attributes()->value;
        } else {
            $this->_logSpecial(
                'getESAPIValidationExpression: Cannot find '.
                'regular expression: ' . $type 
            );
            return false;
        }
    }
    
    /**
     * Helper function.
     * 
     * @param string $prop Property name.
     * @param string $def  Default value.
     * 
     * @return string property name if found, default value otherwise.
     */
    private function _getESAPIEncodedStringProperty($prop, $def) 
    {
        return base64_decode($this->_getESAPIStringProperty($prop, $def));
    }

    /**
     * Helper function.
     * 
     * @param string $prop Property name.
     * @param string $def  Default value.
     * 
     * @return string property name if found, default value otherwise.
     */
    private function _getESAPIIntProperty($prop, $def) 
    {
        $val = $def;
        
        $var = $this->_xml->xpath('/esapi-properties/'.$prop);

        if ( $var === false ) {
            $this->_logSpecial(
                'SecurityConfiguration for /esapi-properties/'.
                $prop.' not found in ESAPI.xml. Using default: '. $def
            );
        }

        if (isset($var[0]) ) {
            $val = (int) $var[0];
        }
        
        return (string)$val;
    }
    
    /**
     * Helper function.
     * 
     * @param string $prop Property name.
     * @param string $def  Default value.
     * 
     * @return string property name if found, default value otherwise.
     */
    private function _getESAPIBooleanProperty($prop, $def) 
    {
        $val = $this->_getESAPIStringProperty($prop, $def);
        
        if ( $val !== $def ) {
            $val = ( strtolower($val) == "false" ) ? false : true;
        }
        
        return $val;
    }

    /**
     * @inheritdoc
     */
    function getApplicationName()
    {
        if ( $this->_ApplicationName === null ) {
            $this->_ApplicationName = $this->_getESAPIStringProperty(
                "Logger/ApplicationName", 'DefaultName'
            );
        }
        
        return $this->_ApplicationName;
    }

    /**
     * @inheritdoc
     */
    function getRememberTokenDuration()
    {
        if ( $this->_RememberTokenDuration === null ) {
            $this->_RememberTokenDuration = $this->_getESAPIIntProperty(
                "Authenticator/RememberTokenDuration", 14
            );
        }
        
        return $this->_RememberTokenDuration * 1000 * 60 * 60 * 24;
    }

    /**
     * @inheritdoc
     */
    function getAllowedLoginAttempts()
    {
        if ($this->_AllowedLoginAttempts === null) {
            $this->_AllowedLoginAttempts = $this->_getESAPIIntProperty(
                "Authenticator/AllowedLoginAttempts", 5
            );
        }
        
        return $this->_AllowedLoginAttempts;
    }

    /**
     * @inheritdoc
     */
    function getMaxOldPasswordHashes()
    {
        if ( $this->_MaxOldPasswordHashes === null ) {
            $this->_MaxOldPasswordHashes = $this->_getESAPIIntProperty(
                "Authenticator/MaxOldPasswordHashes", 12
            );
        }
        
        return $this->_MaxOldPasswordHashes;
    }

    /**
     * @inheritdoc
     */
    function getPasswordParameterName()
    {
        if ( $this->_PasswordParameterName === null ) {
            $this->_PasswordParameterName = $this->_getESAPIStringProperty(
                "Authenticator/PasswordParameterName", 'password'
            );
        }
        
        return $this->_PasswordParameterName;
    }

    /**
     * @inheritdoc
     */
    function getUsernameParameterName()
    {
        if ( $this->_UsernameParameterName === null ) {
            $this->_UsernameParameterName = $this->_getESAPIStringProperty(
                "Authenticator/UsernameParameterName", 'username'
            );
        }
        
        return $this->_UsernameParameterName;
    }

    /**
     * @inheritdoc
     */
    function getSessionIdleTimeoutLength()
    {
        if ( $this->_IdleTimeoutDuration === null ) {
            $this->_IdleTimeoutDuration = $this->_getESAPIIntProperty(
                "Authenticator/IdleTimeoutDuration", 20
            );
        }
        
        return $this->_IdleTimeoutDuration * 1000 * 60;
    }

    /**
     * @inheritdoc
     */
    function getSessionAbsoluteTimeoutLength()
    {
        if ( $this->_AbsoluteTimeoutDuration === null ) {
            $this->_AbsoluteTimeoutDuration = $this->_getESAPIIntProperty(
                "Authenticator/AbsoluteTimeoutDuration", 20
            );
        }
        
        return $this->_AbsoluteTimeoutDuration * 1000 * 60;
    }
    
    /**
     * @inheritdoc
     */
    function getMasterKey()
    {
        if ( $this->_MasterKey === null ) {
            $this->_MasterKey = $this->_getESAPIEncodedStringProperty(
                "Encryptor/secrets/MasterKey", null
            );
        }
        
        return $this->_MasterKey;
    }

    /**
     * @inheritdoc
     */
    function getMasterSalt()
    {
        if ( $this->_MasterSalt === null ) {
            $this->_MasterSalt = $this->_getESAPIEncodedStringProperty(
                "Encryptor/secrets/MasterSalt", null
            );
        }
        
        return $this->_MasterSalt;
    }

    /**
     * @inheritdoc
     */
    function getAllowedFileExtensions()
    {
        if ( $this->_AllowedFileExtensions === null ) {
            $this->_AllowedFileExtensions = $this->_getESAPIArrayProperty(
                "HttpUtilities/ApprovedUploadExtensions/extension", null
            );
        }
        
        return $this->_AllowedFileExtensions;
    }

    /**
     * @inheritdoc
     */
    function getAllowedFileUploadSize()
    {
        if ( $this->_maxUploadSize === null ) {
            $this->_maxUploadSize = $this->_getESAPIIntProperty(
                "HttpUtilities/maxUploadFileBytes", 20
            );
        }
        
        return $this->_maxUploadSize;
    }

    /**
     * @inheritdoc
     */
    function getEncryptionAlgorithm()
    {
        if ( $this->_EncryptionAlgorithm === null ) {
            $this->_EncryptionAlgorithm = $this->_getESAPIStringProperty(
                "Encryptor/EncryptionAlgorithm", 'AES'
            );
        }
        
        return $this->_EncryptionAlgorithm;
    }

    /**
     * @inheritdoc
     */
    function getHashAlgorithm()
    {
        if ( $this->_HashAlgorithm === null ) {
            $this->_HashAlgorithm = $this->_getESAPIStringProperty(
                "Encryptor/HashAlgorithm", 'SHA-512'
            );
        }
        
        return $this->_HashAlgorithm;
    }

    /**
     * @inheritdoc
     */
    function getCharacterEncoding()
    {
        if ( $this->_CharacterEncoding === null ) {
            $this->_CharacterEncoding = $this->_getESAPIStringProperty(
                "Encryptor/CharacterEncoding", 'UTF-8'
            );
        }
        
        return $this->_CharacterEncoding;
    }

    /**
     * @inheritdoc
     */
    function getDigitalSignatureAlgorithm()
    {
        if ( $this->_DigitalSignatureAlgorithm === null ) {
            $this->_DigitalSignatureAlgorithm = $this->_getESAPIStringProperty(
                "Encryptor/DigitalSignatureAlgorithm", 'DSA'
            );
        }
        
        return $this->_DigitalSignatureAlgorithm;
    }

    /**
     * @inheritdoc
     */
    function getRandomAlgorithm()
    {
        if ( $this->_RandomAlgorithm === null ) {
            $this->_RandomAlgorithm = $this->_getESAPIStringProperty(
                "Encryptor/RandomAlgorithm", 'SHA1PRNG'
            );
        }
        
        return $this->_RandomAlgorithm;
    }

    /**
     * @inheritdoc
     */
    function getQuota($eventName)
    {
        if ( $eventName == null ) {
            return null;
        }
        
        if ( $this->_events == null ) {
            $this->_loadEvents();
            if ( $this->_events == null) {
                return null;
            }
        }
        
        // Search for the event, and return it if it exists

        $theEvent = null;
        foreach ($this->_events as $event) {
            if ( $event->name == $eventName ) {
                $theEvent = $event;
                break;
            }
        }
        
        return $theEvent;
    }

    /**
     * @inheritdoc
     */
    function getDisableIntrusionDetection()
    {
        if ($this->_DisableIntrusionDetection === null) {
            $this->_DisableIntrusionDetection = $this->_getESAPIBooleanProperty(
                "IntrusionDetector/DisableIntrusionDetection", false
            );
        }
        
        return $this->_DisableIntrusionDetection;
    }

    /**
     * @inheritdoc
     */
    function getResourceDirectory()
    {
        return $this->_resourceDir;
    }

    /**
     * @inheritdoc
     */
    function setResourceDirectory($dir)
    {
        $this->_resourceDir = $dir;
    }

    /**
     * @inheritdoc
     */
    function getResponseContentType()
    {
        if ( $this->_ResponseContentType === null ) {
            $this->_ResponseContentType = $this->_getESAPIStringProperty(
                "HttpUtilities/ResponseContentType", 'UTF-8'
            );
        }
        
        return $this->_ResponseContentType;
    }

    /**
     * @inheritdoc
     */
    function getLogApplicationName()
    {
        if ( $this->_LogApplicationName === null ) {
            $this->_LogApplicationName = $this->_getESAPIBooleanProperty(
                "Logger/LogApplicationName", false
            );
        }
        
        return $this->_LogApplicationName;
    }

    /**
     * @inheritdoc
     */
    function getLogEncodingRequired()
    {
        if ( $this->_LogEncodingRequired === null ) {
            $this->_LogEncodingRequired = $this->_getESAPIBooleanProperty(
                "Logger/LogEncodingRequired", false
            );
        }
        
        return $this->_LogEncodingRequired;
    }

    /**
     * @inheritdoc
     */
    function getLogLevel()
    {
        if ( $this->_LogLevel === null ) {
            $this->_LogLevel = $this->_getESAPIStringProperty(
                "Logger/LogLevel", 'WARNING'
            );
        }
        
        return $this->_LogLevel;
    }

    /**
     * @inheritdoc
     */
    function getLogFileName()
    {
        if ( $this->_LogFileName === null ) {
            $this->_LogFileName = $this->_getESAPIStringProperty(
                "Logger/LogFileName", 'ESAPI_logging_file'
            );
        }
        
        return $this->_LogFileName;
    }

    /**
     * @inheritdoc
     */
    function getMaxLogFileSize()
    {
        if ( $this->_MaxLogFileSize === null ) {
            $this->_MaxLogFileSize = $this->_getESAPIIntProperty(
                "Logger/MaxLogFileSize", 10000000
            );
        }
        
        return $this->_MaxLogFileSize;
    }
    
    /**
     * @inheritdoc
     */
    function getMaxLogFileBackups()
    {
        if ( $this->_MaxLogFileBackups === null ) {
            $this->_MaxLogFileBackups = $this->_getESAPIIntProperty(
                "Logger/MaxLogFileBackups", 10
            );
        }
        
        return $this->_MaxLogFileBackups;
    }
    
    /**
     * @inheritdoc
     */
    function getLogFileDateFormat()
    {
        if ( $this->_LogFileDateFormat === null ) {
            $this->_LogFileDateFormat = $this->_getESAPIStringProperty(
                "Logger/LogFileDateFormat", 'Y-m-d H:i:s P'
            );
        }
        
        return $this->_LogFileDateFormat;
    }
    
    /**
     * @inheritdoc
     */
    function getValidationPattern($type)
    {        
        return $this->_getESAPIValidationExpression($type);
    }
    
    /**
     * @inheritdoc
     */
    function getWorkingDirectory() {
        
        if ( $this->_WorkingDirectory === null ) {
            $path = ( substr(PHP_OS, 0, 3) == 'WIN' )? 
                'ExecutorWindows/WorkingDirectory':
                'ExecutorUnix/WorkingDirectory';
            $this->_WorkingDirectory = $this->_getESAPIStringProperty($path, '');
        }

        return $this->_WorkingDirectory;
    }
    
    /**
     * @inheritdoc
     */
    function getAllowedExecutables() {
        if ( $this->_AllowedExecutables === null ) {
            $path = ( substr(PHP_OS, 0, 3) == 'WIN' )?
                'ExecutorWindows/ApprovedExecutables/command':
                'ExecutorUnix/ApprovedExecutables/command';
            $this->_AllowedExecutables = $this->_getESAPIArrayProperty($path, null);
        }
        
        return $this->_AllowedExecutables;
    }
    
    /**
     * @inheritdoc
     */
    function getAllowedIncludes() {
        if ( $this->_AllowedIncludes === null ) {
            $path = 'HttpUtilities/ApprovedIncludes/include';
            $this->_AllowedIncludes = $this->_getESAPIArrayProperty($path, null);
        }
        
        return $this->_AllowedIncludes;
    }
    
    /**
     * @inheritdoc
     */
    function getAllowedResources() 
    {
        if ( $this->_AllowedResources === null ) {
            $path = 'HttpUtilities/ApprovedResources/resource';
            $this->_AllowedResources = $this->_getESAPIArrayProperty($path, null);
        }
        
        return $this->_AllowedResources;
    }
    
    /**
     * getSpecialDebugging returns boolean true if special debugging should be
     * enabled. Default is false.
     * At the moment, special debugging is used for producing output from
     * CodecDebug.
     * 
     * @return bool True if special debugging should be enabled. Default is false.
     */
    function getSpecialDebugging() 
    {
        if ( $this->_SpecialDebugging === null ) {
            $path = 'SpecialDebugging/Enabled';
            $this->_SpecialDebugging = $this->_getESAPIBooleanProperty($path, false);
        }
        
        return $this->_SpecialDebugging;
    }
}
?>