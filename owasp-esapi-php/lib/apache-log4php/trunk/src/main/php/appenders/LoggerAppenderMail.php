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
 *
 * @package log4php
 * @subpackage appenders
 */

/**
 * Appends log events to mail using php function {@link PHP_MANUAL#mail}.
 *
 * <p>Parameters are {@link $from}, {@link $to}, {@link $subject}.</p>
 * <p>This appender requires a layout.</p>
 *
 * @version $Revision: 806678 $
 * @package log4php
 * @subpackage appenders
 */
class LoggerAppenderMail extends LoggerAppender {

	/**
	 * @var string 'from' field
	 */
	private $from = null;

	/**
	 * @var string 'subject' field
	 */
	private $subject = 'Log4php Report';
	
	/**
	 * @var string 'to' field
	 */
	private $to = null;

	/**
	 * @var string used to create mail body
	 * @access private
	 */
	private $body = '';
	
	/**
	 * Constructor.
	 *
	 * @param string $name appender name
	 */
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
		if($this->closed != true) {
			$from = $this->from;
			$to = $this->to;
	
			if(!empty($this->body) and $from !== null and $to !== null and $this->layout !== null) {
							$subject = $this->subject;
				mail(
					$to, $subject, 
					$this->layout->getHeader() . $this->body . $this->layout->getFooter(),
					"From: {$from}\r\n"
				);
			}
			$this->closed = true;
		}
	}
	
	public function setSubject($subject) {
		$this->subject = $subject;
	}
	
	public function setTo($to) {
		$this->to = $to;
	}

	public function setFrom($from) {
		$this->from = $from;
	}  

	public function append($event) {
		if($this->layout !== null) {
			$this->body .= $this->layout->format($event);
		}
	}
}
