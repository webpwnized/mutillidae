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
 * This class encapsulates the information obtained when parsing
 * formatting modifiers in conversion modifiers.
 * 
 * @package log4php
 * @subpackage helpers
 * @since 0.3
 */
class LoggerFormattingInfo {

	public $min = -1;
	public $max = 0x7FFFFFFF;
	public $leftAlign = false;

	/**
	 * Constructor
	 */
	public function __construct() {}
	
	public function reset() {
		$this->min = -1;
		$this->max = 0x7FFFFFFF;
		$this->leftAlign = false;	  
	}

	public function dump() {
		// TODO: other option to dump?
		// LoggerLog::debug("LoggerFormattingInfo::dump() min={$this->min}, max={$this->max}, leftAlign={$this->leftAlign}");
	}
}
