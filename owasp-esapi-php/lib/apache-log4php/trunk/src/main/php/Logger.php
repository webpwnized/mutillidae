<?php
/**
 * Licensed to the Apache Software Foundation (ASF) under one or more
 * contributor license agreements. See the NOTICE file distributed with
 * this work for additional information regarding copyright ownership.
 * The ASF licenses this file to You under the Apache License, Version 2.0
 * (the "License"); you may not use this file except in compliance with
 * the License. You may obtain a copy of the License at
 * 
 *		http://www.apache.org/licenses/LICENSE-2.0
 * 
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 * 
 */

/**
 * LOG4PHP_DIR points to the log4php root directory.
 *
 * If not defined it will be set automatically when the first package classfile 
 * is included
 * 
 * @var string 
 */
if (!defined('LOG4PHP_DIR')) define('LOG4PHP_DIR', dirname(__FILE__));

spl_autoload_register(array('Logger', 'autoload'));

/**
 * This is the central class in the log4j package. Most logging operations, 
 * except configuration, are done through this class. 
 *
 * In log4j this class replaces the Category class. There is no need to 
 * port deprecated classes; log4php Logger class doesn't extend Category.
 *
 * @category   log4php
 * @package log4php
 * @license	   http://www.apache.org/licenses/LICENSE-2.0 Apache License, Version 2.0
 * @version	   SVN: $Id: Logger.php 806678 2009-08-21 19:15:49Z grobmeier $
 * @link	   http://logging.apache.org/log4php
 */
 /*
  * TODO:
  * Localization: setResourceBundle($bundle) : not supported
  * Localization: getResourceBundle: not supported
  * Localization: getResourceBundleString($key): not supported
  * Localization: l7dlog($priority, $key, $params, $t) : not supported
  */
class Logger {
	private static $_classes = array(
		'LoggerException' => '/LoggerException.php',
		'LoggerHierarchy' => '/LoggerHierarchy.php',
		'LoggerLayout' => '/LoggerLayout.php',
		'LoggerLevel' => '/LoggerLevel.php',
		'LoggerMDC' => '/LoggerMDC.php',
		'LoggerNDC' => '/LoggerNDC.php',
		'LoggerReflectionUtils' => '/LoggerReflectionUtils.php',
		'LoggerConfigurator' => '/LoggerConfigurator.php',
		'LoggerConfiguratorBasic' => '/configurators/LoggerConfiguratorBasic.php',
		'LoggerConfiguratorIni' => '/configurators/LoggerConfiguratorIni.php',
		'LoggerConfiguratorPhp' => '/configurators/LoggerConfiguratorPhp.php',
		'LoggerConfiguratorXml' => '/configurators/LoggerConfiguratorXml.php',
		'LoggerRoot' => '/LoggerRoot.php',
		'LoggerAppender' => '/LoggerAppender.php',
		'LoggerAppenderPool' => '/LoggerAppenderPool.php',
		'LoggerAppenderAdodb' => '/appenders/LoggerAppenderAdodb.php',
		'LoggerAppenderPDO' => '/appenders/LoggerAppenderPDO.php',
		'LoggerAppenderConsole' => '/appenders/LoggerAppenderConsole.php',
		'LoggerAppenderDailyFile' => '/appenders/LoggerAppenderDailyFile.php',
		'LoggerAppenderEcho' => '/appenders/LoggerAppenderEcho.php',
		'LoggerAppenderFile' => '/appenders/LoggerAppenderFile.php',
		'LoggerAppenderMail' => '/appenders/LoggerAppenderMail.php',
		'LoggerAppenderMailEvent' => '/appenders/LoggerAppenderMailEvent.php',
		'LoggerAppenderNull' => '/appenders/LoggerAppenderNull.php',
		'LoggerAppenderPhp' => '/appenders/LoggerAppenderPhp.php',
		'LoggerAppenderRollingFile' => '/appenders/LoggerAppenderRollingFile.php',
		'LoggerAppenderSocket' => '/appenders/LoggerAppenderSocket.php',
		'LoggerAppenderSyslog' => '/appenders/LoggerAppenderSyslog.php',
		'LoggerFormattingInfo' => '/helpers/LoggerFormattingInfo.php',
		'LoggerOptionConverter' => '/helpers/LoggerOptionConverter.php',
		'LoggerPatternConverter' => '/helpers/LoggerPatternConverter.php',
		'LoggerBasicPatternConverter' => '/helpers/LoggerBasicPatternConverter.php',
		'LoggerCategoryPatternConverter' => '/helpers/LoggerCategoryPatternConverter.php',
		'LoggerClassNamePatternConverter' => '/helpers/LoggerClassNamePatternConverter.php',
		'LoggerDatePatternConverter' => '/helpers/LoggerDatePatternConverter.php',
		'LoggerLiteralPatternConverter' => '/helpers/LoggerLiteralPatternConverter.php',
		'LoggerLocationPatternConverter' => '/helpers/LoggerLocationPatternConverter.php',
		'LoggerMDCPatternConverter' => '/helpers/LoggerMDCPatternConverter.php',
		'LoggerNamedPatternConverter' => '/helpers/LoggerNamedPatternConverter.php',
		'LoggerBasicPatternConverter' => '/helpers/LoggerBasicPatternConverter.php',
		'LoggerLiteralPatternConverter' => '/helpers/LoggerLiteralPatternConverter.php',
		'LoggerDatePatternConverter' => '/helpers/LoggerDatePatternConverter.php',
		'LoggerMDCPatternConverter' => '/helpers/LoggerMDCPatternConverter.php',
		'LoggerLocationPatternConverter' => '/helpers/LoggerLocationPatternConverter.php',
		'LoggerNamedPatternConverter' => '/helpers/LoggerNamedPatternConverter.php',
		'LoggerClassNamePatternConverter' => '/helpers/LoggerClassNamePatternConverter.php',
		'LoggerCategoryPatternConverter' => '/helpers/LoggerCategoryPatternConverter.php',
		'LoggerPatternParser' => '/helpers/LoggerPatternParser.php',
		'LoggerLayoutHtml' => '/layouts/LoggerLayoutHtml.php',
		'LoggerLayoutSimple' => '/layouts/LoggerLayoutSimple.php',
		'LoggerLayoutTTCC' => '/layouts/LoggerLayoutTTCC.php',
		'LoggerLayoutPattern' => '/layouts/LoggerLayoutPattern.php',
		'LoggerLayoutXml' => '/layouts/LoggerLayoutXml.php',
		'LoggerRendererDefault' => '/renderers/LoggerRendererDefault.php',
		'LoggerRendererObject' => '/renderers/LoggerRendererObject.php',
		'LoggerRendererMap' => '/renderers/LoggerRendererMap.php',
		'LoggerLocationInfo' => '/LoggerLocationInfo.php',
		'LoggerLoggingEvent' => '/LoggerLoggingEvent.php',
		'LoggerFilter' => '/LoggerFilter.php',
		'LoggerFilterDenyAll' => '/filters/LoggerFilterDenyAll.php',
		'LoggerFilterLevelMatch' => '/filters/LoggerFilterLevelMatch.php',
		'LoggerFilterLevelRange' => '/filters/LoggerFilterLevelRange.php',
		'LoggerFilterStringMatch' => '/filters/LoggerFilterStringMatch.php',
	);

	/**
	 * Class autoloader
	 * This method is provided to be invoked within an __autoload() magic method.
	 * @param string class name
	 */
	public static function autoload($className) {
		if(isset(self::$_classes[$className])) {
			include LOG4PHP_DIR.self::$_classes[$className];
		}
	}

	/**
	 * Additivity is set to true by default, that is children inherit the 
	 * appenders of their ancestors by default.
	 * @var boolean
	 */
	private $additive = true;
	
	/** @var string fully qualified class name */
	private $fqcn = 'Logger';

	/** @var LoggerLevel The assigned level of this category. */
	private $level = null;
	
	/** @var string name of this category. */
	private $name = '';
	
	/** @var Logger The parent of this category. Null if this is the root logger*/
	private $parent = null;
	
	/**
	 * @var array collection of appenders
	 * @see LoggerAppender
	 */
	private $aai = array();

	/** the hierarchy used by log4php */
	private static $hierarchy;
	
	/** the configurator class name */
	private static $configurationClass = 'LoggerConfiguratorBasic';
	
	/** the path to the configuration file */
	private static $configurationFile = null;
	
	/** inidicates if log4php has already been initialized */
	private static $initialized = false;
	
	/**
	 * Constructor.
	 * @param  string  $name  Category name	  
	 */
	public function __construct($name) {
		$this->name = $name;
	}
	
	/**
	 * Return the category name.
	 * @return string
	 */
	public function getName() {
		return $this->name;
	} 

	/**
	 * Returns the parent of this category.
	 * @return Logger
	 */
	public function getParent() {
		return $this->parent;
	}	  
	
	/**
	 * Returns the hierarchy used by this Logger.
	 * Caution: do not use this hierarchy unless you have called initialize().
	 * To get Loggers, use the Logger::getLogger and Logger::getRootLogger methods
	 * instead of operating on on the hierarchy directly.
	 * 
	 * @deprecated - will be moved to private
	 */
	public static function getHierarchy() {
		if(!isset(self::$hierarchy)) {
			self::$hierarchy = new LoggerHierarchy(new LoggerRoot());
		}
		return self::$hierarchy;
	}
	
	/* Logging methods */
	/**
	 * Log a message object with the DEBUG level including the caller.
	 *
	 * @param mixed $message message
	 * @param mixed $caller caller object or caller string id
	 */
	public function debug($message, $caller = null) {
		$this->logLevel($message, LoggerLevel::getLevelDebug(), $caller);
	} 


	/**
	 * Log a message object with the INFO Level.
	 *
	 * @param mixed $message message
	 * @param mixed $caller caller object or caller string id
	 */
	public function info($message, $caller = null) {
		$this->logLevel($message, LoggerLevel::getLevelInfo(), $caller);
	}

	/**
	 * Log a message with the WARN level.
	 *
	 * @param mixed $message message
	 * @param mixed $caller caller object or caller string id
	 */
	public function warn($message, $caller = null) {
		$this->logLevel($message, LoggerLevel::getLevelWarn(), $caller);
	}
	
	/**
	 * Log a message object with the ERROR level including the caller.
	 *
	 * @param mixed $message message
	 * @param mixed $caller caller object or caller string id
	 */
	public function error($message, $caller = null) {
		$this->logLevel($message, LoggerLevel::getLevelError(), $caller);
	}
	
	/**
	 * Log a message object with the FATAL level including the caller.
	 *
	 * @param mixed $message message
	 * @param mixed $caller caller object or caller string id
	 */
	public function fatal($message, $caller = null) {
		$this->logLevel($message, LoggerLevel::getLevelFatal(), $caller);
	}
	
	/**
	 * This method creates a new logging event and logs the event without further checks.
	 *
	 * It should not be called directly. Use {@link info()}, {@link debug()}, {@link warn()},
	 * {@link error()} and {@link fatal()} wrappers.
	 *
	 * @param string $fqcn Fully Qualified Class Name of the Logger
	 * @param mixed $caller caller object or caller string id
	 * @param LoggerLevel $level log level	   
	 * @param mixed $message message
	 * @see LoggerLoggingEvent			
	 */
	public function forcedLog($fqcn, $caller, $level, $message) {
		$this->callAppenders(new LoggerLoggingEvent($fqcn, $this, $level, $message));
	} 
	
	
		/**
	 * Check whether this category is enabled for the DEBUG Level.
	 * @return boolean
	 */
	public function isDebugEnabled() {
		return $this->isEnabledFor(LoggerLevel::getLevelDebug());
	}		

	/**
	 * Check whether this category is enabled for a given Level passed as parameter.
	 *
	 * @param LoggerLevel level
	 * @return boolean
	 */
	public function isEnabledFor($level) {
		return (bool)($level->isGreaterOrEqual($this->getEffectiveLevel()));
	} 

	/**
	 * Check whether this category is enabled for the info Level.
	 * @return boolean
	 * @see LoggerLevel
	 */
	public function isInfoEnabled() {
		return $this->isEnabledFor(LoggerLevel::getLevelInfo());
	} 

	/**
	 * This generic form is intended to be used by wrappers.
	 *
	 * @param LoggerLevel $priority a valid level
	 * @param mixed $message message
	 * @param mixed $caller caller object or caller string id
	 */
	public function log($priority, $message, $caller = null) {
		if($this->isEnabledFor($priority)) {
			$this->forcedLog($this->fqcn, $caller, $priority, $message);
		}
	}
	
	/**
	 * If assertion parameter is false, then logs msg as an error statement.
	 *
	 * @param bool $assertion
	 * @param string $msg message to log
	 */
	public function assertLog($assertion = true, $msg = '') {
		if($assertion == false) {
			$this->error($msg);
		}
	}
	 
	private function logLevel($message, $level, $caller = null) {
		if($level->isGreaterOrEqual($this->getEffectiveLevel())) {
			$this->forcedLog($this->fqcn, $caller, $level, $message);
		}
	} 
	
	/* Factory methods */ 
	
	/**
	 * Get a Logger by name (Delegate to {@link Logger})
	 * 
	 * @param string $name logger name
	 * @param LoggerFactory $factory a {@link LoggerFactory} instance or null
	 * @return Logger
	 * @static 
	 */
	public static function getLogger($name) {
		if(!self::isInitialized()) {
			self::initialize();
		}
		return self::getHierarchy()->getLogger($name);
	}
	
	/**
	 * get the Root Logger (Delegate to {@link Logger})
	 * @return LoggerRoot
	 * @static 
	 */	   
	public static function getRootLogger() {
		if(!self::isInitialized()) {
			self::initialize();
		}
		return self::getHierarchy()->getRootLogger();	  
	}
	
	/* Configuration methods */
	
	/**
	 * Add a new Appender to the list of appenders of this Category instance.
	 *
	 * @param LoggerAppender $newAppender
	 */
	public function addAppender($newAppender) {
		$appenderName = $newAppender->getName();
		$this->aai[$appenderName] = $newAppender;
	}
	
	/**
	 * Remove all previously added appenders from this Category instance.
	 */
	public function removeAllAppenders() {
		$appenderNames = array_keys($this->aai);
		$enumAppenders = count($appenderNames);
		for($i = 0; $i < $enumAppenders; $i++) {
			$this->removeAppender($appenderNames[$i]); 
		}
	} 
			
	/**
	 * Remove the appender passed as parameter form the list of appenders.
	 *
	 * @param mixed $appender can be an appender name or a {@link LoggerAppender} object
	 */
	public function removeAppender($appender) {
		if($appender instanceof LoggerAppender) {
			$appender->close();
			unset($this->aai[$appender->getName()]);
		} else if (is_string($appender) and isset($this->aai[$appender])) {
			$this->aai[$appender]->close();
			unset($this->aai[$appender]);
		}
	} 
			
	/**
	 * Call the appenders in the hierarchy starting at this.
	 *
	 * @param LoggerLoggingEvent $event 
	 */
	public function callAppenders($event) {
		if(count($this->aai) > 0) {
			foreach(array_keys($this->aai) as $appenderName) {
				$this->aai[$appenderName]->doAppend($event);
			}
		}
		if($this->parent != null and $this->getAdditivity()) {
			$this->parent->callAppenders($event);
		}
	}
	
	/**
	 * Get the appenders contained in this category as an array.
	 * @return array collection of appenders
	 */
	public function getAllAppenders() {
		return array_values($this->aai);
	}
	
	/**
	 * Look for the appender named as name.
	 * @return LoggerAppender
	 */
	public function getAppender($name) {
		return $this->aai[$name];
	}
	
	/**
	 * Get the additivity flag for this Category instance.
	 * @return boolean
	 */
	public function getAdditivity() {
		return $this->additive;
	}
 
	/**
	 * Starting from this category, search the category hierarchy for a non-null level and return it.
	 * @see LoggerLevel
	 * @return LoggerLevel or null
	 */
	public function getEffectiveLevel() {
		for($c = $this; $c != null; $c = $c->parent) {
			if($c->getLevel() !== null) {
				return $c->getLevel();
			}
		}
		return null;
	}
  
	/**
	 * Returns the assigned Level, if any, for this Category.
	 * @return LoggerLevel or null 
	 */
	public function getLevel() {
		return $this->level;
	}
	
	/**
	 * Set the level of this Category.
	 *
	 * @param LoggerLevel $level a level string or a level constant 
	 */
	public function setLevel($level) {
		$this->level = $level;
	}
	
	/**
	 * Clears all logger definitions
	 * 
	 * @static
	 * @return boolean 
	 */
	public static function clear() {
		return self::getHierarchy()->clear();	 
	}
	
	/**
	 * Destroy configurations for logger definitions
	 * 
	 * @static
	 * @return boolean 
	 */
	public static function resetConfiguration() {
		$result = self::getHierarchy()->resetConfiguration();
		self::$initialized = false;
		self::$configurationClass = 'LoggerConfiguratorBasic';
		self::$configurationFile = null;
		return $result;	 
	}

	/**
	 * Safely close all appenders.
	 * This is not longer necessary due the appenders shutdown via
	 * destructors. 
	 * @deprecated
	 * @static
	 */
	public static function shutdown() {
		return self::getHierarchy()->shutdown();	   
	}
	
	/**
	 * check if a given logger exists.
	 * 
	 * @param string $name logger name 
	 * @static
	 * @return boolean
	 */
	public static function exists($name) {
		return self::getHierarchy()->exists($name);
	}
	
	/**
	 * Returns an array this whole Logger instances.
	 * 
	 * @static
	 * @see Logger
	 * @return array
	 */
	public static function getCurrentLoggers() {
		return self::getHierarchy()->getCurrentLoggers();
	}
	
	/**
	 * Is the appender passed as parameter attached to this category?
	 *
	 * @param LoggerAppender $appender
	 */
	public function isAttached($appender) {
		return isset($this->aai[$appender->getName()]);
	} 
		   
	/**
	 * Set the additivity flag for this Category instance.
	 *
	 * @param boolean $additive
	 */
	public function setAdditivity($additive) {
		$this->additive = (bool)$additive;
	}

	/**
	 * Sets the parent logger of this logger
	 */
	public function setParent(Logger $logger) {
		$this->parent = $logger;
	} 
	
	/**
	 * Configures Log4PHP.
	 * This method needs to be called before the first logging event
	 * has occured. If this methode is never called, the standard configuration
	 * takes place (@see LoggerConfiguratorBasic).
	 * If only the configuration file is given, the configurator class will
	 * be the XML Configurator or the INI Configurator, if no .xml ending
	 * could be determined.
	 * 
	 * If a custom configurator should be used, the configuration file
	 * is either null or the path to file the custom configurator uses.
	 * Make sure the configurator is already or can be loaded by PHP when necessary.
	 * 
	 * @param String $configurationFile the configuration file
	 * @param String $configurationClass the configurator class
	 */
	public static function configure($configurationFile = null, 
									 $configurationClass = null ) {
		if($configurationClass === null && $configurationFile === null) {
			self::$configurationClass = 'LoggerConfiguratorBasic';
			return;
		}
									 	
		if($configurationClass !== null) {
			self::$configurationFile = $configurationFile;
			self::$configurationClass = $configurationClass;
			return;
		}
		
		if (strtolower(substr( $configurationFile, -4 )) == '.xml') {
			self::$configurationFile = $configurationFile;
			self::$configurationClass = 'LoggerConfiguratorXml';
		} else {
			self::$configurationFile = $configurationFile;
			self::$configurationClass = 'LoggerConfiguratorIni';
		}
	}
	
	/**
	 * Returns the current configurator
	 * @return the configurator
	 */
	public static function getConfigurationClass() {
		return self::$configurationClass;
	}
	
	/**
	 * Returns the current configuration file
	 * @return the configuration file
	 */
	public static function getConfigurationFile() {
		return self::$configurationFile;
	}
	
	/**
	 * Returns, true, if the log4php framework is already initialized
	 */
	private static function isInitialized() {
		return self::$initialized;
	}
	
	/**
	 * Initializes the log4php framework.
	 * @return boolean
	 */
	public static function initialize() {
		self::$initialized = true;
		$instance = LoggerReflectionUtils::createObject(self::$configurationClass);
		$result = $instance->configure(self::getHierarchy(), self::$configurationFile);
		return $result;
	}
}
