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
 */
class LoggerLocationPatternConverter extends LoggerPatternConverter {
	
	/**
	 * @var integer
	 */
	private $type;

	/**
	 * Constructor
	 *
	 * @param string $formattingInfo
	 * @param integer $type
	 */
	public function __construct($formattingInfo, $type) {
	  parent::__construct($formattingInfo);
	  $this->type = $type;
	}

	/**
	 * @param LoggerLoggingEvent $event
	 * @return string
	 */
	public function convert($event) {
		$locationInfo = $event->getLocationInformation();
		switch($this->type) {
			case LoggerPatternParser::LOG4PHP_LOGGER_PATTERN_PARSER_FULL_LOCATION_CONVERTER:
				return $locationInfo->getFullInfo();
			case LoggerPatternParser::LOG4PHP_LOGGER_PATTERN_PARSER_METHOD_LOCATION_CONVERTER:
				return $locationInfo->getMethodName();
			case LoggerPatternParser::LOG4PHP_LOGGER_PATTERN_PARSER_LINE_LOCATION_CONVERTER:
				return $locationInfo->getLineNumber();
			case LoggerPatternParser::LOG4PHP_LOGGER_PATTERN_PARSER_FILE_LOCATION_CONVERTER:
				return $locationInfo->getFileName();
			default: 
				return '';
		}
	}
}

