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
 * The root logger.
 *
 * @version $Revision: 802170 $
 * @package log4php
 * @see Logger
 */
class LoggerRoot extends Logger {
	/**
	 * Constructor
	 *
	 * @param integer $level initial log level
	 */
	public function __construct($level = null) {
		parent::__construct('root');

		if($level == null) {
			$level = LoggerLevel::getLevelAll();
		}
		$this->setLevel($level);
	} 
	
	/**
	 * @return LoggerLevel the level
	 */
	public function getChainedLevel() {
		return parent::getLevel();
	} 
	
	/**
	 * Setting a null value to the level of the root category may have catastrophic results.
	 * @param LoggerLevel $level
	 */
	public function setLevel($level) {
		if($level != null) {
			parent::setLevel($level);
		}	 
	}
	
	/**
	 * Always returns false.
	 * Because LoggerRoot has no parents, it returns false.
	 * @param Logger $parent
	 * @return boolean
	 */
	public function setParent(Logger $parent) {
		return false;
	}

}
