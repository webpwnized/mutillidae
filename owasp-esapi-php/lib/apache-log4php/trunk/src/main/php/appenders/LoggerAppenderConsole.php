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
 * ConsoleAppender appends log events to STDOUT or STDERR using a layout specified by the user.
 *
 * <p>Optional parameter is {@link $target}. The default target is Stdout.</p>
 * <p><b>Note</b>: Use this Appender with command-line php scripts.
 * On web scripts this appender has no effects.</p>
 * <p>This appender requires a layout.</p>
 *
 * @version $Revision: 806678 $
 * @package log4php
 * @subpackage appenders
 */
class LoggerAppenderConsole extends LoggerAppender {

	const STDOUT = 'php://stdout';
	const STDERR = 'php://stderr';

	/**
	 * Can be 'php://stdout' or 'php://stderr'. But it's better to use keywords <b>STDOUT</b> and <b>STDERR</b> (case insensitive).
	 * Default is STDOUT
	 * @var string
	 */
	private $target = self::STDOUT;

	/**
	 * @var boolean
	 * @access private
	 */
	protected $requiresLayout = true;

	/**
	 * @var mixed the resource used to open stdout/stderr
	 * @access private
	 */
	protected $fp = null;

	public function __destruct() {
       $this->close();
   	}

	/**
	 * Set console target.
	 * @param mixed $value a constant or a string
	 */
	public function setTarget($value) {
		$v = trim($value);
		if ($v == self::STDOUT || strtoupper($v) == 'STDOUT') {
			$this->target = self::STDOUT;
		} elseif ($v == self::STDERR || strtoupper($v) == 'STDERR') {
			$this->target = self::STDERR;
		}
	}

	public function activateOptions() {
		$this->fp = fopen($this->target, 'w');
		if(!is_null($this->fp) && is_resource($this->fp) && !is_null($this->layout)) {
		    $lHeader = $this->layout->getHeader();
		    if(!is_null($lHeader)){
		        fwrite($this->fp, $lHeader);
		    }
		}
		$this->closed = (bool)is_resource($this->fp) === false;
	}

	/**
	 * @see LoggerAppender::close()
	 */
	public function close() {
		if($this->closed != true) {
		    if (!is_null($this->fp) && is_resource($this->fp) && !is_null($this->layout)) {
		        $lFooter = $this->layout->getFooter();
		        if(!is_null($lFooter)){
		            fwrite($this->fp, $lFooter);
		        }
				fclose($this->fp);
			}
			$this->closed = true;
		}
	}

	public function append(LoggerLoggingEvent $event) {
		if (is_resource($this->fp) && $this->layout !== null) {
			fwrite($this->fp, $this->layout->format($event));
		}
	}
}

