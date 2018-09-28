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
 * Defines the minimum set of levels recognized by the system, that is
 * <i>OFF</i>, <i>FATAL</i>, <i>ERROR</i>,
 * <i>WARN</i>, <i>INFO</i, <i>DEBUG</i> and
 * <i>ALL</i>.
 *
 * <p>The <i>LoggerLevel</i> class may be subclassed to define a larger
 * level set.</p>
 *
 * @version $Revision: 795643 $
 * @package log4php
 * @since 0.5
 */
class LoggerLevel {
	
	const OFF = 2147483647;
	const FATAL = 50000;
	const ERROR = 40000;
	const WARN = 30000;
	const INFO = 20000;
	const DEBUG = 10000;
	const ALL = -2147483647;

	/**
	 * TODO: check if still necessary or to be refactored 
	 * @var integer
	 */
	private $level;
  
  	/**
   	 * Contains a list of instantiated levels 
   	 */
  	private static $levelMap;
  	
	/**
	 * @var string
	 */
	private $levelStr;
  
	/**
	 * @var integer
	 */
	private $syslogEquivalent;

	/**
	 * Constructor
	 *
	 * @param integer $level
	 * @param string $levelStr
	 * @param integer $syslogEquivalent
	 */
	private function __construct($level, $levelStr, $syslogEquivalent) {
		$this->level = $level;
		$this->levelStr = $levelStr;
		$this->syslogEquivalent = $syslogEquivalent;
	}

	/**
	 * Two priorities are equal if their level fields are equal.
	 *
	 * @param object $o
	 * @return boolean 
	 */
	public function equals($o) {
		if($o instanceof LoggerLevel) {
			if($this->level == $o->level) {
			    return true;
			}
		} else {
			return false;
		}
	}
	
	/**
	 * Returns an Off Level
	 * @static
	 * @return LoggerLevel
	 */
	public static function getLevelOff() {
		if(!isset(self::$levelMap[LoggerLevel::OFF])) {
			self::$levelMap[LoggerLevel::OFF] = new LoggerLevel(LoggerLevel::OFF, 'OFF', 0);
		}
		return self::$levelMap[LoggerLevel::OFF];
	}

	/**
	 * Returns a Fatal Level
	 * @static
	 * @return LoggerLevel
	 */
	public static function getLevelFatal() {
		if(!isset(self::$levelMap[LoggerLevel::FATAL])) {
			self::$levelMap[LoggerLevel::FATAL] = new LoggerLevel(LoggerLevel::FATAL, 'FATAL', 0);
		}
		return self::$levelMap[LoggerLevel::FATAL];
	}
	
	/**
	 * Returns an Error Level
	 * @static
	 * @return LoggerLevel
	 */
	public static function getLevelError() {
		if(!isset(self::$levelMap[LoggerLevel::ERROR])) {
			self::$levelMap[LoggerLevel::ERROR] = new LoggerLevel(LoggerLevel::ERROR, 'ERROR', 3);
		}
		return self::$levelMap[LoggerLevel::ERROR];
	}
	
	/**
	 * Returns a Warn Level
	 * @static
	 * @return LoggerLevel
	 */
	public static function getLevelWarn() {
		if(!isset(self::$levelMap[LoggerLevel::WARN])) {
			self::$levelMap[LoggerLevel::WARN] = new LoggerLevel(LoggerLevel::WARN, 'WARN', 4);
		}
		return self::$levelMap[LoggerLevel::WARN];
	}

	/**
	 * Returns an Info Level
	 * @static
	 * @return LoggerLevel
	 */
	public static function getLevelInfo() {
		if(!isset(self::$levelMap[LoggerLevel::INFO])) {
			self::$levelMap[LoggerLevel::INFO] = new LoggerLevel(LoggerLevel::INFO, 'INFO', 6);
		}
		return self::$levelMap[LoggerLevel::INFO];
	}

	/**
	 * Returns a Debug Level
	 * @static
	 * @return LoggerLevel
	 */
	public static function getLevelDebug() {
		if(!isset(self::$levelMap[LoggerLevel::DEBUG])) {
			self::$levelMap[LoggerLevel::DEBUG] = new LoggerLevel(LoggerLevel::DEBUG, 'DEBUG', 7);
		}
		return self::$levelMap[LoggerLevel::DEBUG];
	}

	/**
	 * Returns an All Level
	 * @static
	 * @return LoggerLevel
	 */
	public static function getLevelAll() {
		if(!isset(self::$levelMap[LoggerLevel::ALL])) {
			self::$levelMap[LoggerLevel::ALL] = new LoggerLevel(LoggerLevel::ALL, 'ALL', 7);
		}
		return self::$levelMap[LoggerLevel::ALL];
	}
	
	/**
	 * Return the syslog equivalent of this priority as an integer.
	 * @final
	 * @return integer
	 */
	public function getSyslogEquivalent() {
		return $this->syslogEquivalent;
	}

	/**
	 * Returns <i>true</i> if this level has a higher or equal
	 * level than the level passed as argument, <i>false</i>
	 * otherwise.  
	 * 
	 * <p>You should think twice before overriding the default
	 * implementation of <i>isGreaterOrEqual</i> method.
	 *
	 * @param LoggerLevel $r
	 * @return boolean
	 */
	public function isGreaterOrEqual($r) {
		return $this->level >= $r->level;
	}

	/**
	 * Returns the string representation of this priority.
	 * @return string
	 * @final
	 */
	public function toString() {
		return $this->levelStr;
	}

	/**
	 * Returns the integer representation of this level.
	 * @return integer
	 */
	public function toInt() {
		return $this->level;
	}

	/**
	 * Convert the string passed as argument to a level. If the
	 * conversion fails, then this method returns a DEBUG Level.
	 *
	 * @param mixed $arg
	 * @param LoggerLevel $default
	 * @static 
	 */
	public static function toLevel($arg, $defaultLevel = null) {
		if($defaultLevel === null) {
			return self::toLevel($arg, self::getLevelDebug());
		} else {
			if(is_int($arg)) {
				switch($arg) {
					case self::ALL:	return self::getLevelAll();
					case self::DEBUG: return self::getLevelDebug();
					case self::INFO: return self::getLevelInfo();
					case self::WARN: return self::getLevelWarn();
					case self::ERROR: return self::getLevelError();
					case self::FATAL: return self::getLevelFatal();
					case self::OFF:	return self::getLevelOff();
					default: return $defaultLevel;
				}
			} else {
				switch(strtoupper($arg)) {
					case 'ALL':	return self::getLevelAll();
					case 'DEBUG': return self::getLevelDebug();
					case 'INFO': return self::getLevelInfo();
					case 'WARN': return self::getLevelWarn();
					case 'ERROR': return self::getLevelError();
					case 'FATAL': return self::getLevelFatal();
					case 'OFF':	return self::getLevelOff();
					default: return $defaultLevel;
				}
			}
		}
	}
}
