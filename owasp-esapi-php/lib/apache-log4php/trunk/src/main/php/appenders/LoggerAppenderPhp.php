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
 * Log events using php {@link PHP_MANUAL#trigger_error} function and a {@link LoggerLayoutTTCC} default layout.
 *
 * <p>Levels are mapped as follows:</p>
 * - <b>level &lt; WARN</b> mapped to E_USER_NOTICE
 * - <b>WARN &lt;= level &lt; ERROR</b> mapped to E_USER_WARNING
 * - <b>level &gt;= ERROR</b> mapped to E_USER_ERROR  
 *
 * @version $Revision: 806678 $
 * @package log4php
 * @subpackage appenders
 */ 
class LoggerAppenderPhp extends LoggerAppender {

	public function __construct($name = '') {
		parent::__construct($name);
		$this->requiresLayout = true;
	}
	
	public function __destruct() {
       $this->close();
   	}
	
	public function activateOptions() {
		$this->closed = false;
	}

	public function close() {
		$this->closed = true;
	}

	public function append($event) {
		if($this->layout !== null) {
			$level = $event->getLevel();
			if($level->isGreaterOrEqual(LoggerLevel::getLevelError())) {
				trigger_error($this->layout->format($event), E_USER_ERROR);
			} else if ($level->isGreaterOrEqual(LoggerLevel::getLevelWarn())) {
				trigger_error($this->layout->format($event), E_USER_WARNING);
			} else {
				trigger_error($this->layout->format($event), E_USER_NOTICE);
			}
		}
	}
}
