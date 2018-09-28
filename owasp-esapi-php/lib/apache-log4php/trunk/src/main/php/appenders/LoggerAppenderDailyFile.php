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
 * LoggerAppenderDailyFile appends log events to a file ne.
 *
 * A formatted version of the date pattern is used as to create the file name
 * using the {@link PHP_MANUAL#sprintf} function.
 * <p>Parameters are {@link $datePattern}, {@link $file}. Note that file 
 * parameter should include a '%s' identifier and should always be set 
 * before {@link $file} param.</p>
 *
 * @version $Revision: 806678 $
 * @package log4php
 * @subpackage appenders
 */
class LoggerAppenderDailyFile extends LoggerAppenderFile {

	/**
	 * Format date. 
	 * It follows the {@link PHP_MANUAL#date()} formatting rules and <b>should always be set before {@link $file} param</b>.
	 * @var string
	 */
	public $datePattern = "Ymd";
	
	public function __destruct() {
       parent::__destruct();
   	}
   	
	/**
	* Sets date format for the file name.
	* @param string $format a regular date() string format
	*/
	public function setDatePattern($format) {
		$this->datePattern = $format;
	}
	
	/**
	* @return string returns date format for the filename
	*/
	public function getDatePattern() {
		return $this->datePattern;
	}
	
	/**
	* The File property takes a string value which should be the name of the file to append to.
	* Sets and opens the file where the log output will go.
	*
	* @see LoggerAppenderFile::setFile()
	*/
	public function setFile() {
		$numargs = func_num_args();
		$args = func_get_args();
		
		if($numargs == 1 and is_string($args[0])) {
			parent::setFile( sprintf((string)$args[0], date($this->getDatePattern())) );
		} else if ($numargs == 2 and is_string($args[0]) and is_bool($args[1])) {
			parent::setFile( sprintf((string)$args[0], date($this->getDatePattern())), $args[1] );
		}
	} 

}
