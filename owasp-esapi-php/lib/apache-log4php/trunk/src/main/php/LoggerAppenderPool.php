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
 * Pool implmentation for LoggerAppender instances
 *
 * @version $Revision: 795727 $
 * @package log4php
 */
class LoggerAppenderPool {
	/* Appender Pool */
	public static $appenderPool =  null;
	
	/**
	 * 
	 *
	 * @param string $name 
	 * @param string $class 
	 * @return LoggerAppender
	 */
	public static function getAppenderFromPool($name, $class = '') {
		if(isset(self::$appenderPool[$name])) {
			return self::$appenderPool[$name];
		}
		
		if(empty($class)) {
			return null;
		}
		
		$appender = LoggerReflectionUtils::createObject($class);
		$appender->setName($name);
		if($appender !== null) { 
			self::$appenderPool[$name] = $appender;
			return self::$appenderPool[$name];
		}
		return null;		
	}
}
