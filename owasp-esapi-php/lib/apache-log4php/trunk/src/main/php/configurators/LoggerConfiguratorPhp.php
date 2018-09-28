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
 * LoggerConfiguratorPhp class
 *
 * This class allows configuration of log4php through an external file that 
 * deliver a PHP array in return.
 *
 * Example:
 * <code>
 * <?php
 * return array(
 *     'threshold' => 'ALL',
 *     'rootLogger' => array(
 *        'level' => 'INFO',
 *        'appenders' => array('default'),
 *     ),
 *     'loggers' => array(
 *         'dev' => array(
 *             'level' => 'DEBUG',
 *             'appenders' => array('default'),
 *         ),
 *     ),
 *     'appenders' => array(
 *         'default' => array(
 *             'class' => 'LoggerAppenderEcho',
 *             'layout' => array(
 *                 'class' => 'LoggerPatternLayout',
 *                 'conversionPattern' => "%d{Y-m-d H:i:s} %-5p %c %X{username}: %m in %F at %L%n",
 *             ),
 *         ),
 *     ),
 * );
 * </code>
 *
 * @package log4php
 * @subpackage configurators
 * @since 2.0
 */
class LoggerConfiguratorPhp implements LoggerConfigurator {
	
	public function configure(LoggerHierarchy $hierarchy, $url = '') {
		return $this->doConfigure($url, $hierarchy);
	}
	
	private function doConfigure($url, LoggerHierarchy $hierarchy) {
		
		$config = require $url;
		
		// set threshold
		if(isset($config['threshold'])) {
			$hierarchy->setThreshold(LoggerOptionConverter::toLevel($config['threshold'], LoggerLevel::getLevelAll()));
		}
		
		// parse and create appenders
		if(isset($config['appenders'])) {
			
			foreach($config['appenders'] as $appenderName => $appenderProperties) {
				
				$appender = LoggerAppenderPool::getAppenderFromPool($appenderName, $appenderProperties['class']);
				
				if($appender->requiresLayout()) {
					
					if(isset($appenderProperties['layout'])) {
						
						if(isset($appenderProperties['layout']['class']) and !empty($appenderProperties['layout']['class'])) {
							$layoutClass = $appenderProperties['layout']['class'];							
						} else {
							$layoutClass = 'LoggerLayoutSimple';
						}
						
						$layout = LoggerReflectionUtils::createObject($layoutClass);
						if($layout === null) {
							$layout = LoggerReflectionUtils::createObject('LoggerLayoutSimple');
						}
						
						if($layout instanceof LoggerLayoutPattern) {
							$layout->setConversionPattern($appenderProperties['layout']['conversionPattern']);
						}
						
						$appender->setLayout($layout);
						
					} else {
						// TODO: throw exception?
					}
					
				}
				
			}
			
		}
		
		// parse and create root logger
		if(isset($config['rootLogger'])) {
			$rootLogger = $hierarchy->getRootLogger();
			if(isset($config['rootLogger']['level'])) {
				$rootLogger->setLevel(LoggerOptionConverter::toLevel($config['rootLogger']['level'], LoggerLevel::getLevelDebug()));
				if(isset($config['rootLogger']['appenders'])) {
					foreach($config['rootLogger']['appenders'] as $appenderName) {
						$appender = LoggerAppenderPool::getAppenderFromPool($appenderName);
						if($appender !== null) {
							$rootLogger->addAppender($appender);
						}
					}
				}	
			}
		}
		
		// parse and create loggers
		if(isset($config['loggers'])) {
			foreach($config['loggers'] as $loggerName => $loggerProperties) {
				if(is_string($loggerName)) {
					$logger = $hierarchy->getLogger($loggerName);
					
					if(isset($loggerProperties['level'])) {
						$logger->setLevel(LoggerOptionConverter::toLevel($loggerProperties['level'], LoggerLevel::getLevelDebug()));
						if(isset($loggerProperties['appenders'])) {
							foreach($loggerProperties['appenders'] as $appenderName) {
								$appender = LoggerAppenderPool::getAppenderFromPool($appenderName);
								if($appender !== null) {
									$logger->addAppender($appender);
								}
							}
						}	
					}
				} else {
					// TODO: throw exception
				}
			}
		}
		
		return true;
	}
	
}
