<?php
/**
 * Licensed to the Apache Software Foundation (ASF) under one or more
 * contributor license agreements. See the NOTICE file distributed with
 * this work for additional information regarding copyright ownership.
 * The ASF licenses this file to You under the Apache License, Version 2.0
 * (the "License"); you may not use this file except in compliance with
 * the License. You may obtain a copy of the License at
 *
 *	   http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 *
 */

/**
 * Allows the configuration of log4php from an external file.
 * 
 * See {@link doConfigure()} for the expected format.
 * 
 * <p>It is sometimes useful to see how log4php is reading configuration
 * files. You can enable log4php internal logging by defining the
 * <b>log4php.debug</b> variable.</p>
 *
 * <p>The <i>LoggerConfiguratorIni</i> does not handle the
 * advanced configuration features supported by the {@link LoggerConfiguratorXml} 
 * such as support for {@link LoggerFilter}, 
   custom {@link LoggerErrorHandlers}, nested appenders such as the 
   {@link Logger AsyncAppender}, 
 * etc.
 * 
 * <p>All option <i>values</i> admit variable substitution. The
 * syntax of variable substitution is similar to that of Unix
 * shells. The string between an opening <b>&quot;${&quot;</b> and
 * closing <b>&quot;}&quot;</b> is interpreted as a key. The value of
 * the substituted variable can be defined as a system property or in
 * the configuration file itself. The value of the key is first
 * searched in the defined constants, in the enviroments variables
 * and if not found there, it is
 * then searched in the configuration file being parsed.  The
 * corresponding value replaces the ${variableName} sequence.</p>
 * <p>For example, if <b>$_ENV['home']</b> env var is set to
 * <b>/home/xyz</b>, then every occurrence of the sequence
 * <b>${home}</b> will be interpreted as
 * <b>/home/xyz</b>. See {@link LoggerOptionConverter::getSystemProperty()}
 * for details.</p>
 *
 * <p>Please note that boolean values should be quoted otherwise the default 
 * value will be chosen. E.g.:
 * <code>
 * // Does *not* work. Will always result in default value
 * // (which is currently 'true' for this attribute).
 * log4php.appender.A2.append=false
 * // Does work.
 * log4php.appender.A2.append="false"
 * </code>
 * </p>
 *
 * @version $Revision: 805681 $
 * @package log4php
 * @subpackage configurators
 * @since 0.5 
 */
class LoggerConfiguratorIni implements LoggerConfigurator {
 	const CATEGORY_PREFIX = "log4php.category.";
 	const LOGGER_PREFIX = "log4php.logger.";
	const FACTORY_PREFIX = "log4php.factory";
	const ADDITIVITY_PREFIX = "log4php.additivity.";
	const ROOT_CATEGORY_PREFIX = "log4php.rootCategory";
	const ROOT_LOGGER_PREFIX = "log4php.rootLogger";
	const APPENDER_PREFIX = "log4php.appender.";
	const RENDERER_PREFIX = "log4php.renderer.";
	const THRESHOLD_PREFIX = "log4php.threshold";
	
	/** 
	 * Key for specifying the {@link LoggerFactory}.  
	 */
	const LOGGER_FACTORY_KEY = "log4php.loggerFactory";
	const LOGGER_DEBUG_KEY = "log4php.debug";
	const INTERNAL_ROOT_NAME = "root";
	
	/**
	 * Constructor
	 */
	public function __construct() {
	}

	/**
	 * Read configuration from a file.
	 *
	 * <p>The function {@link PHP_MANUAL#parse_ini_file} is used to read the
	 * file.</p>
	 *
	 * <b>The existing configuration is not cleared nor reset.</b> 
	 * If you require a different behavior, then call 
	 * {@link  Logger::resetConfiguration()} 
	 * method before calling {@link doConfigure()}.
	 * 
	 * <p>The configuration file consists of statements in the format
	 * <b>key=value</b>. The syntax of different configuration
	 * elements are discussed below.
	 * 
	 * <p><b>Repository-wide threshold</b></p>
	 * 
	 * <p>The repository-wide threshold filters logging requests by level
	 * regardless of logger. The syntax is:
	 * 
	 * <pre>
	 * log4php.threshold=[level]
	 * </pre>
	 * 
	 * <p>The level value can consist of the string values OFF, FATAL,
	 * ERROR, WARN, INFO, DEBUG, ALL or a <i>custom level</i> value. A
	 * custom level value can be specified in the form
	 * <samp>level#classname</samp>. By default the repository-wide threshold is set
	 * to the lowest possible value, namely the level <b>ALL</b>.
	 * </p>
	 * 
	 * 
	 * <p><b>Appender configuration</b></p>
	 * 
	 * <p>Appender configuration syntax is:</p>
	 * <pre>
	 * ; For appender named <i>appenderName</i>, set its class.
	 * ; Note: The appender name can contain dots.
	 * log4php.appender.appenderName=name_of_appender_class
	 * 
	 * ; Set appender specific options.
	 * 
	 * log4php.appender.appenderName.option1=value1
	 * log4php.appender.appenderName.optionN=valueN
	 * </pre>
	 * 
	 * For each named appender you can configure its {@link LoggerLayout}. The
	 * syntax for configuring an appender's layout is:
	 * <pre>
	 * log4php.appender.appenderName.layout=name_of_layout_class
	 * log4php.appender.appenderName.layout.option1=value1
	 *	....
	 * log4php.appender.appenderName.layout.optionN=valueN
	 * </pre>
	 * 
	 * <p><b>Configuring loggers</b></p>
	 * 
	 * <p>The syntax for configuring the root logger is:
	 * <pre>
	 * log4php.rootLogger=[level], appenderName, appenderName, ...
	 * </pre>
	 * 
	 * <p>This syntax means that an optional <i>level</i> can be
	 * supplied followed by appender names separated by commas.
	 * 
	 * <p>The level value can consist of the string values OFF, FATAL,
	 * ERROR, WARN, INFO, DEBUG, ALL or a <i>custom level</i> value. A
	 * custom level value can be specified in the form</p>
	 *
	 * <pre>level#classname</pre>
	 * 
	 * <p>If a level value is specified, then the root level is set
	 * to the corresponding level.	If no level value is specified,
	 * then the root level remains untouched.
	 * 
	 * <p>The root logger can be assigned multiple appenders.
	 * 
	 * <p>Each <i>appenderName</i> (separated by commas) will be added to
	 * the root logger. The named appender is defined using the
	 * appender syntax defined above.
	 * 
	 * <p>For non-root categories the syntax is almost the same:
	 * <pre>
	 * log4php.logger.logger_name=[level|INHERITED|NULL], appenderName, appenderName, ...
	 * </pre>
	 * 
	 * <p>The meaning of the optional level value is discussed above
	 * in relation to the root logger. In addition however, the value
	 * INHERITED can be specified meaning that the named logger should
	 * inherit its level from the logger hierarchy.</p>
	 * 
	 * <p>If no level value is supplied, then the level of the
	 * named logger remains untouched.</p>
	 * 
	 * <p>By default categories inherit their level from the
	 * hierarchy. However, if you set the level of a logger and later
	 * decide that that logger should inherit its level, then you should
	 * specify INHERITED as the value for the level value. NULL is a
	 * synonym for INHERITED.</p>
	 * 
	 * <p>Similar to the root logger syntax, each <i>appenderName</i>
	 * (separated by commas) will be attached to the named logger.</p>
	 * 
	 * <p>See the <i>appender additivity rule</i> in the user manual for 
	 * the meaning of the <b>additivity</b> flag.
	 * 
	 * <p><b>ObjectRenderers</b></p>
	 * 
	 * <p>You can customize the way message objects of a given type are
	 * converted to String before being logged. This is done by
	 * specifying a {@link LoggerRendererObject}
	 * for the object type would like to customize.</p>
	 * 
	 * <p>The syntax is:
	 * 
	 * <pre>
	 * log4php.renderer.name_of_rendered_class=name_of_rendering.class
	 * </pre>
	 * 
	 * As in,
	 * <pre>
	 * log4php.renderer.myFruit=myFruitRenderer
	 * </pre>
	 * 
	 * <p><b>Logger Factories</b></p>
	 * 
	 * The usage of custom logger factories is discouraged and no longer
	 * documented.
	 * 
	 * <p><b>Example</b></p>
	 * 
	 * <p>An example configuration is given below. Other configuration
	 * file examples are given in the <b>tests</b> folder.
	 * 
	 * <pre>
	 * ; Set options for appender named "A1".
	 * ; Appender "A1" will be a LoggerAppenderSyslog
	 * log4php.appender.A1=LoggerAppenderSyslog
	 * 
	 * ; The syslog daemon resides on www.abc.net
	 * log4php.appender.A1.ident=log4php-test
	 * 
	 * ; A1's layout is a LoggerPatternLayout, using the conversion pattern
	 * ; <b>%r %-5p %c{2} %M.%L %x - %m%n</b>. Thus, the log output will
	 * ; include the relative time since the start of the application in
	 * ; milliseconds, followed by the level of the log request,
	 * ; followed by the two rightmost components of the logger name,
	 * ; followed by the callers method name, followed by the line number,
	 * ; the nested disgnostic context and finally the message itself.
	 * ; Refer to the documentation of LoggerPatternLayout} for further information
	 * ; on the syntax of the ConversionPattern key.
	 * log4php.appender.A1.layout=LoggerPatternLayout
	 * log4php.appender.A1.layout.ConversionPattern="%-4r %-5p %c{2} %M.%L %x - %m%n"
	 * 
	 * ; Set options for appender named "A2"
	 * ; A2 should be a LoggerAppenderRollingFile, with maximum file size of 10 MB
	 * ; using at most one backup file. A2's layout is TTCC, using the
	 * ; ISO8061 date format with context printing enabled.
	 * log4php.appender.A2=LoggerAppenderRollingFile
	 * log4php.appender.A2.MaxFileSize=10MB
	 * log4php.appender.A2.MaxBackupIndex=1
	 * log4php.appender.A2.layout=LoggerLayoutTTCC
	 * log4php.appender.A2.layout.ContextPrinting="true"
	 * log4php.appender.A2.layout.DateFormat="%c"
	 * 
	 * ; Root logger set to DEBUG using the A2 appender defined above.
	 * log4php.rootLogger=DEBUG, A2
	 * 
	 * ; Logger definitions:
	 * ; The SECURITY logger inherits is level from root. However, it's output
	 * ; will go to A1 appender defined above. It's additivity is non-cumulative.
	 * log4php.logger.SECURITY=INHERIT, A1
	 * log4php.additivity.SECURITY=false
	 * 
	 * ; Only warnings or above will be logged for the logger "SECURITY.access".
	 * ; Output will go to A1.
	 * log4php.logger.SECURITY.access=WARN
	 * 
	 * 
	 * ; The logger "class.of.the.day" inherits its level from the
	 * ; logger hierarchy.	Output will go to the appender's of the root
	 * ; logger, A2 in this case.
	 * log4php.logger.class.of.the.day=INHERIT
	 * </pre>
	 * 
	 * <p>Refer to the <b>setOption</b> method in each Appender and
	 * Layout for class specific options.</p>
	 * 
	 * <p>Use the <b>&quot;;&quot;</b> character at the
	 * beginning of a line for comments.</p>
	 * 
	 * @param string $url The name of the configuration file where the
	 *					  configuration information is stored.
	 * @param LoggerHierarchy $repository the repository to apply the configuration
	 */
	public function configure(LoggerHierarchy $hierarchy, $url = '') {
		$properties = @parse_ini_file($url);
		if ($properties === false || count($properties) == 0) {
			$error = error_get_last();
		    throw new LoggerException("LoggerConfiguratorIni: ".$error['message']);
		}
		return $this->doConfigureProperties($properties, $hierarchy);
	}

	/**
	 * Read configuration options from <b>properties</b>.
	 *
	 * @see doConfigure().
	 * @param array $properties
	 * @param LoggerHierarchy $hierarchy
	 */
	private function doConfigureProperties($properties, LoggerHierarchy $hierarchy) {
		$thresholdStr = @$properties[self::THRESHOLD_PREFIX];
		$hierarchy->setThreshold(LoggerOptionConverter::toLevel($thresholdStr, LoggerLevel::getLevelAll()));
		$this->configureRootCategory($properties, $hierarchy);
		$this->parseCatsAndRenderers($properties, $hierarchy);
		return true;
	}

	/**
	 * @param array $props array of properties
	 * @param LoggerHierarchy $hierarchy
	 */
	private function configureRootCategory($props, LoggerHierarchy $hierarchy) {
		$effectivePrefix = self::ROOT_LOGGER_PREFIX;
		$value = @$props[self::ROOT_LOGGER_PREFIX];

		if(empty($value)) {
			$value = @$props[self::ROOT_CATEGORY_PREFIX];
			$effectivePrefix = self::ROOT_CATEGORY_PREFIX;
		}

		if(empty($value)) {
			// TODO "Could not find root logger information. Is this OK?"
		} else {
			$root = $hierarchy->getRootLogger();
			$this->parseCategory($props, $root, $effectivePrefix, self::INTERNAL_ROOT_NAME,	$value);
		}
	}

	/**
	 * Parse non-root elements, such non-root categories and renderers.
	 *
	 * @param array $props array of properties
	 * @param LoggerHierarchy $hierarchy
	 */
	private function parseCatsAndRenderers($props, LoggerHierarchy $hierarchy) {
		while(list($key,$value) = each($props)) {
			if(strpos($key, self::CATEGORY_PREFIX) === 0 or 
				strpos($key, self::LOGGER_PREFIX) === 0) {
				if(strpos($key, self::CATEGORY_PREFIX) === 0) {
					$loggerName = substr($key, strlen(self::CATEGORY_PREFIX));
				} else if(strpos($key, self::LOGGER_PREFIX) === 0) {
					$loggerName = substr($key, strlen(self::LOGGER_PREFIX));
				}
				
				$logger = $hierarchy->getLogger($loggerName);
				$this->parseCategory($props, $logger, $key, $loggerName, $value);
				$this->parseAdditivityForLogger($props, $logger, $loggerName);
			} else if(strpos($key, self::RENDERER_PREFIX) === 0) {
				$renderedClass = substr($key, strlen(self::RENDERER_PREFIX));
				$renderingClass = $value;
				if(method_exists($hierarchy, 'addrenderer')) { // ?
					LoggerRendererMap::addRenderer($hierarchy, $renderedClass, $renderingClass);
				}
			}
		}
	}

	/**
	 * Parse the additivity option for a non-root category.
	 *
	 * @param array $props array of properties
	 * @param Logger $cat
	 * @param string $loggerName
	 */
	private function parseAdditivityForLogger($props, Logger $cat, $loggerName) {
		$value = LoggerOptionConverter::findAndSubst(self::ADDITIVITY_PREFIX . $loggerName, $props);
		
		// touch additivity only if necessary
		if(!empty($value)) {
			$additivity = LoggerOptionConverter::toBoolean($value, true);
			$cat->setAdditivity($additivity);
		}
	}

	/**
	 * This method must work for the root category as well.
	 *
	 * @param array $props array of properties
	 * @param Logger $logger
	 * @param string $optionKey
	 * @param string $loggerName
	 * @param string $value
	 * @return Logger
	 */
	private function parseCategory($props, Logger $logger, $optionKey, $loggerName, $value) {
		// We must skip over ',' but not white space
		$st = explode(',', $value);

		// If value is not in the form ", appender.." or "", then we should set
		// the level of the loggeregory.

		if(!(empty($value) || @$value[0] == ',')) {
			// just to be on the safe side...
			if(count($st) == 0) {
				return;
			}
			$levelStr = current($st);
			
			// If the level value is inherited, set category level value to
			// null. We also check that the user has not specified inherited for the
			// root category.
			if('INHERITED' == strtoupper($levelStr) || 'NULL' == strtoupper($levelStr)) {
				if($loggerName == self::INTERNAL_ROOT_NAME) {
					// TODO: throw exception?	"The root logger cannot be set to null."
				} else {
					$logger->setLevel(null);
				}
			} else {
				$logger->setLevel(LoggerOptionConverter::toLevel($levelStr, LoggerLevel::getLevelDebug()));
			}
		}

		// TODO: removing should be done by the logger, if necessary and wanted 
		// $logger->removeAllAppenders();
		while($appenderName = next($st)) {
			$appenderName = trim($appenderName);
			if(empty($appenderName)) {
				continue;
			}
			
			$appender = $this->parseAppender($props, $appenderName);
			if($appender !== null) {
					$logger->addAppender($appender);
			}
		}
	}

	/**
	 * @param array $props array of properties
	 * @param string $appenderName
	 * @return LoggerAppender
	 */
	private function parseAppender($props, $appenderName) {
		$appender = LoggerAppenderPool::getAppenderFromPool($appenderName);
		$prefix = self::APPENDER_PREFIX . $appenderName;
		if($appender === null) {
			// Appender was not previously initialized.
			$appenderClass = @$props[$prefix];
			$appender = LoggerAppenderPool::getAppenderFromPool($appenderName, $appenderClass);
			if($appender === null) {
				return null;
			}
		}
		
		if($appender->requiresLayout() ) {
			$layoutPrefix = $prefix . ".layout";
			$layoutClass = @$props[$layoutPrefix];
			$layoutClass = LoggerOptionConverter::substVars($layoutClass, $props);
			if(empty($layoutClass)) {
				$layout = LoggerReflectionUtils::createObject('LoggerLayoutSimple');
			} else {
				$layout = LoggerReflectionUtils::createObject($layoutClass);
				if($layout === null) {
					$layout = LoggerReflectionUtils::createObject('LoggerLayoutSimple');
				}
			}
			
			LoggerReflectionUtils::setPropertiesByObject($layout, $props, $layoutPrefix . ".");				  
			$appender->setLayout($layout);
			
		}
		LoggerReflectionUtils::setPropertiesByObject($appender, $props, $prefix . ".");
		return $appender;		 
	}
}
