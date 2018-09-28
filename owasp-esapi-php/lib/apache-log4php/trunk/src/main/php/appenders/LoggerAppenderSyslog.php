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
 * Log events using php {@link PHP_MANUAL#syslog} function.
 *
 * Levels are mapped as follows:
 * - <b>level &gt;= FATAL</b> to LOG_ALERT
 * - <b>FATAL &gt; level &gt;= ERROR</b> to LOG_ERR 
 * - <b>ERROR &gt; level &gt;= WARN</b> to LOG_WARNING
 * - <b>WARN  &gt; level &gt;= INFO</b> to LOG_INFO
 * - <b>INFO  &gt; level &gt;= DEBUG</b> to LOG_DEBUG
 *
 * @version $Revision: 806678 $
 * @package log4php
 * @subpackage appenders
 */ 
class LoggerAppenderSyslog extends LoggerAppender {
	
	/**
	 * The ident string is added to each message. Typically the name of your application.
	 *
	 * @var string Ident for your application
	 */
	private $_ident = "Log4PHP Syslog-Event";

	/**
	 * The priority parameter value indicates the level of importance of the message.
	 * It is passed on to the Syslog daemon.
	 * 
	 * @var int Syslog priority
	 */
	private $_priority;
	
	/**
	 * The option used when generating a log message.
	 * It is passed on to the Syslog daemon.
	 * 
	 * @var int Syslog priority
	 */
	private $_option;
	
	/**
	 * The facility value indicates the source of the message.
	 * It is passed on to the Syslog daemon.
	 *
	 * @var const int Syslog facility
	 */
	private $_facility;
	
	/**
	 * If it is necessary to define logging priority in the .properties-file,
	 * set this variable to "true".
	 *
	 * @var const int  value indicating whether the priority of the message is defined in the .properties-file
	 *				   (or properties-array)
	 */
	private $_overridePriority;

	public function __construct($name = '') {
		parent::__construct($name);
		$this->requiresLayout = true;
	}

	public function __destruct() {
       $this->close();
   	}
   	
	/**
	 * Set the ident of the syslog message.
	 *
	 * @param string Ident
	 */
	public function setIdent($ident) {
		$this->_ident = $ident; 
	}

	/**
	 * Set the priority value for the syslog message.
	 *
	 * @param const int Priority
	 */
	public function setPriority($priority) {
		$this->_priority = $priority;
	}
	
	
	/**
	 * Set the facility value for the syslog message.
	 *
	 * @param const int Facility
	 */
	public function setFacility($facility) {
		$this->_facility = $facility;
	} 
	
	/**
	 * If the priority of the message to be sent can be defined by a value in the properties-file, 
	 * set parameter value to "true".
	 *
	 * @param bool Override priority
	 */
	public function setOverridePriority($overridePriority) {
		$this->_overridePriority = $overridePriority;							
	} 
	
	/**
	 * Set the option value for the syslog message.
	 * This value is used as a parameter for php openlog()	
	 * and passed on to the syslog daemon.
	 *
	 * @param string	$option
	 */
	public function setOption($option) {	  
		$this->_option = $option;		
	}
	
	public function activateOptions() {
		define_syslog_variables();
		$this->closed = false;
	}

	public function close() {
		if($this->closed != true) {
			closelog();
			$this->closed = true;
		}
	}

	public function append($event) {
		if($this->_option == NULL){
			$this->_option = LOG_PID | LOG_CONS;
		}
		
		// Attach the process ID to the message, use the facility defined in the .properties-file
		openlog($this->_ident, $this->_option, $this->_facility);
		
		$level	 = $event->getLevel();
		if($this->layout === null) {
			$message = $event->getRenderedMessage();
		} else {
			$message = $this->layout->format($event); 
		}

		// If the priority of a syslog message can be overridden by a value defined in the properties-file,
		// use that value, else use the one that is defined in the code.
		if($this->_overridePriority){
						syslog($this->_priority, $message);			   
		} else {
			if($level->isGreaterOrEqual(LoggerLevel::getLevelFatal())) {
				syslog(LOG_ALERT, $message);
			} else if ($level->isGreaterOrEqual(LoggerLevel::getLevelError())) {
				syslog(LOG_ERR, $message);		  
			} else if ($level->isGreaterOrEqual(LoggerLevel::getLevelWarn())) {
				syslog(LOG_WARNING, $message);
			} else if ($level->isGreaterOrEqual(LoggerLevel::getLevelInfo())) {
				syslog(LOG_INFO, $message);
			} else if ($level->isGreaterOrEqual(LoggerLevel::getLevelDebug())) {
				syslog(LOG_DEBUG, $message);
			}
		}
		closelog();
	}
}
