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
 * @package log4php
 * @subpackage helpers
 * @abstract
 */
class LoggerNamedPatternConverter extends LoggerPatternConverter {

	/**
	 * @var integer
	 */
	private $precision;

	/**
	 * Constructor
	 *
	 * @param string $formattingInfo
	 * @param integer $precision
	 */
	public function __construct($formattingInfo, $precision) {
	  parent::__construct($formattingInfo);
	  $this->precision =  $precision;
	}

	/**
	 * @param LoggerLoggingEvent $event
	 * @return string
	 * @abstract
	 */
	public function getFullyQualifiedName($event) {
		// abstract
		return;
	}

	/**
	 * @param LoggerLoggingEvent $event
	 * @return string
	 */
	function convert($event) {
		$n = $this->getFullyQualifiedName($event);
		if($this->precision <= 0) {
			return $n;
		} else {
			$len = strlen($n);
			// We substract 1 from 'len' when assigning to 'end' to avoid out of
			// bounds exception in return r.substring(end+1, len). This can happen if
			// precision is 1 and the category name ends with a dot.
			$end = $len -1 ;
			for($i = $this->precision; $i > 0; $i--) {
				$end = strrpos(substr($n, 0, ($end - 1)), '.');
				if($end == false) {
					return $n;
				}
			}
			return substr($n, ($end + 1), $len);
		}
	}
}
