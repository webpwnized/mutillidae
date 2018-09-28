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
 * Serialize events and send them to a network socket.
 *
 * Parameters are {@link $remoteHost}, {@link $port}, {@link $timeout}, 
 * {@link $locationInfo}, {@link $useXml} and {@link $log4jNamespace}.
 *
 * @version $Revision: 806678 $
 * @package log4php
 * @subpackage appenders
 */ 
class LoggerAppenderSocket extends LoggerAppender {

	/**
	 * @var mixed socket connection resource
	 * @access private
	 */
	private $sp = false;
	
	/**
	 * Target host. On how to define remote hostaname see 
	 * {@link PHP_MANUAL#fsockopen}
	 * @var string 
	 */
	private $remoteHost = '';
	
	/**
	 * @var integer the network port.
	 */
	private $port = 4446;
	
	/**
	 * @var boolean get event's location info.
	 */
	private $locationInfo = false;
	
	/**
	 * @var integer connection timeout
	 */
	private $timeout = 30;
	
	/**
	 * @var boolean output events via {@link LoggerXmlLayout}
	 */
	private $useXml = false;
	
	/**
	 * @var boolean forward this option to {@link LoggerXmlLayout}. 
	 *				Ignored if {@link $useXml} is <i>false</i>.
	 */
	private $log4jNamespace = false;

	/**
	 * @var LoggerXmlLayout
	 * @access private
	 */
	private $xmlLayout = null;
	
	public function __destruct() {
       $this->close();
   	}
   	
	/**
	 * Create a socket connection using defined parameters
	 */
	public function activateOptions() {
		$errno = 0;
		$errstr = '';
		$this->sp = @fsockopen($this->getRemoteHost(), $this->getPort(), $errno, $errstr, $this->getTimeout());
		if($errno) {
			$this->closed = true;
		} else {
			if($this->getUseXml()) {
				$this->xmlLayout = LoggerReflectionUtils::createObject('LoggerXmlLayout');
				if($this->xmlLayout === null) {
					$this->setUseXml(false);
				} else {
					$this->xmlLayout->setLocationInfo($this->getLocationInfo());
					$this->xmlLayout->setLog4jNamespace($this->getLog4jNamespace());
					$this->xmlLayout->activateOptions();
				}			 
			}
			$this->closed = false;
		}
	}
	
	public function close() {
		if($this->closed != true) {
			fclose($this->sp);
			$this->closed = true;
		}
	}

	/**
	 * @return string
	 */
	public function getHostname() {
		return $this->getRemoteHost();
	}
	
	/**
	 * @return boolean
	 */
	public function getLocationInfo() {
		return $this->locationInfo;
	} 
	 
	/**
	 * @return boolean
	 */
	public function getLog4jNamespace() {
		return $this->log4jNamespace;
	}

	/**
	 * @return integer
	 */
	public function getPort() {
		return $this->port;
	}
	
	public function getRemoteHost() {
		return $this->remoteHost;
	}
	
	/**
	 * @return integer
	 */
	public function getTimeout() {
		return $this->timeout;
	}
	
	/**
	 * @var boolean
	 */
	public function getUseXml() {
		return $this->useXml;
	} 
	 
	public function reset() {
		$this->close();
		parent::reset();
	}

	/**
	 * @param mixed
	 */
	public function setLocationInfo($flag) {
		$this->locationInfo = LoggerOptionConverter::toBoolean($flag, $this->getLocationInfo());
	} 

	/**
	 * @param mixed
	 */
	public function setLog4jNamespace($flag) {
		$this->log4jNamespace = LoggerOptionConverter::toBoolean($flag, $this->getLog4jNamespace());
	} 
			
	/**
	 * @param integer
	 */
	public function setPort($port) {
		$port = LoggerOptionConverter::toInt($port, 0);
		if($port > 0 and $port < 65535) {
			$this->port = $port;	
		}
	}
	
	/**
	 * @param string
	 */
	public function setRemoteHost($hostname) {
		$this->remoteHost = $hostname;
	}
	
	/**
	 * @param integer
	 */
	public function setTimeout($timeout) {
		$this->timeout = LoggerOptionConverter::toInt($timeout, $this->getTimeout());
	}
	
	/**
	 * @param mixed
	 */
	public function setUseXml($flag) {
		$this->useXml = LoggerOptionConverter::toBoolean($flag, $this->getUseXml());
	} 
 
	/**
	 * @param LoggerLoggingEvent
	 */
	public function append($event) {
		if($this->sp) {
			if($this->getLocationInfo()) {
				$event->getLocationInformation();
			}
		
			if(!$this->getUseXml()) {
				$sEvent = serialize($event);
				fwrite($this->sp, $sEvent, strlen($sEvent));
			} else {
				fwrite($this->sp, $this->xmlLayout->format($event));
			}			 

			// not sure about it...
			fflush($this->sp);
		} 
	}
}
