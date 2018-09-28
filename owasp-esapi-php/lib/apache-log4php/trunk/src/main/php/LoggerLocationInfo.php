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
 * The internal representation of caller location information.
 *
 * @version $Revision: 795643 $
 * @package log4php
 * @since 0.3
 */
class LoggerLocationInfo {
	/**
	 * When location information is not available the constant
	 * <i>NA</i> is returned. Current value of this string
	 * constant is <b>?</b>.  
	 */
	const LOCATION_INFO_NA = 'NA';
	
	/**
	* @var string Caller's line number.
	*/
	protected $lineNumber = null;
	
	/**
	* @var string Caller's file name.
	*/
	protected $fileName = null;
	
	/**
	* @var string Caller's fully qualified class name.
	*/
	protected $className = null;
	
	/**
	* @var string Caller's method name.
	*/
	protected $methodName = null;
	
	/**
	* @var string 
	*/
	protected $fullInfo = null;

	/**
	 * Instantiate location information based on a {@link PHP_MANUAL#debug_backtrace}.
	 *
	 * @param array $trace
	 * @param mixed $caller
	 */
	public function __construct($trace, $fqcn = null) {
		$this->lineNumber = isset($trace['line']) ? $trace['line'] : null;
		$this->fileName = isset($trace['file']) ? $trace['file'] : null;
		$this->className = isset($trace['class']) ? $trace['class'] : null;
		$this->methodName = isset($trace['function']) ? $trace['function'] : null;
		$this->fullInfo = $this->getClassName() . '.' . $this->getMethodName() . 
			'(' . $this->getFileName() . ':' . $this->getLineNumber() . ')';
	}

	public function getClassName() {
		return ($this->className === null) ? self::LOCATION_INFO_NA : $this->className; 
	}

	/**
	 *	Return the file name of the caller.
	 *	<p>This information is not always available.
	 */
	public function getFileName() {
		return ($this->fileName === null) ? self::LOCATION_INFO_NA : $this->fileName; 
	}

	/**
	 *	Returns the line number of the caller.
	 *	<p>This information is not always available.
	 */
	public function getLineNumber() {
		return ($this->lineNumber === null) ? self::LOCATION_INFO_NA : $this->lineNumber; 
	}

	/**
	 *	Returns the method name of the caller.
	 */
	public function getMethodName() {
		return ($this->methodName === null) ? self::LOCATION_INFO_NA : $this->methodName; 
	}

	/**
	 *	Returns the full information of the caller.
	 */
	public function getFullInfo() {
		return ($this->fullInfo === null) ? self::LOCATION_INFO_NA : $this->fullInfo;
	}

}
