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
 * Log events to an email address. It will be created an email for each event. 
 *
 * <p>Parameters are 
 * {@link $smtpHost} (optional), 
 * {@link $port} (optional), 
 * {@link $from} (optional), 
 * {@link $to}, 
 * {@link $subject} (optional).</p>
 * <p>A layout is required.</p>
 *
 * @version $Revision: 806678 $
 * @package log4php
 * @subpackage appenders
 */
class LoggerAppenderMailEvent extends LoggerAppender {

	/**
	 * @var string 'from' field
	 */
	private $from = null;

	/**
	 * @var integer 'from' field
	 */
	private $port = 25;

	/**
	 * @var string hostname. 
	 */
	private $smtpHost = null;

	/**
	 * @var string 'subject' field
	 */
	private $subject = '';

	/**
	 * @var string 'to' field
	 */
	private $to = null;
	
	/**
	 * @access private
	 */
	protected $requiresLayout = true;

	/**
	 * Constructor.
	 *
	 * @param string $name appender name
	 */
	public function __construct($name = '') {
		parent::__construct($name);
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

	public function setFrom($from) {
		$this->from = $from;
	}
	
	public function setPort($port) {
		$this->port = (int)$port;
	}
	
	public function setSmtpHost($smtpHost) {
		$this->smtpHost = $smtpHost;
	}
	
	public function setSubject($subject) {
		$this->subject = $subject;
	}
	
	public function setTo($to) {
		$this->to = $to;
	}

	public function append($event) {
		$from = $this->getFrom();
		$to = $this->getTo();
		if(empty($from) or empty($to)) {
			return;
		}
	
		$smtpHost = $this->getSmtpHost();
		$prevSmtpHost = ini_get('SMTP');
		if(!empty($smtpHost)) {
			ini_set('SMTP', $smtpHost);
		} else {
			$smtpHost = $prevSmtpHost;
		} 

		$smtpPort = $this->getPort();
		$prevSmtpPort= ini_get('smtp_port');		
		if($smtpPort > 0 and $smtpPort < 65535) {
			ini_set('smtp_port', $smtpPort);
		} else {
			$smtpPort = $prevSmtpPort;
		} 
		
		@mail($to, $this->getSubject(), 
			$this->layout->getHeader() . $this->layout->format($event) . $this->layout->getFooter($event), 
			"From: {$from}\r\n");
			
		ini_set('SMTP', $prevSmtpHost);
		ini_set('smtp_port', $prevSmtpPort);
	}
}

