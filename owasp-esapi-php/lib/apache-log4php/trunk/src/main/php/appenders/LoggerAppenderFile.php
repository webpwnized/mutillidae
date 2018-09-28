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
 * FileAppender appends log events to a file.
 *
 * Parameters are ({@link $fileName} but option name is <b>file</b>), 
 * {@link $append}.
 *
 * @version $Revision: 806678 $
 * @package log4php
 * @subpackage appenders
 */
class LoggerAppenderFile extends LoggerAppender {

	/**
	 * @var boolean if {@link $file} exists, appends events.
	 */
	private $append = true;
	/**
	 * @var string the file name used to append events
	 */
	protected $fileName;
	/**
	 * @var mixed file resource
	 */
	protected $fp = false;
	
	public function __construct($name = '') {
		parent::__construct($name);
		$this->requiresLayout = true;
	}

	public function __destruct() {
       $this->close();
   	}
   	
   	/* Added by JD to fix Mutillidae issue */
   	static public function mkdir_error_handler($errno, $errstr) {
   		// bummer: we cant make the directory
   	}
	/* Added by JD to fix Mutillidae issue */
	static public function fopen_error_handler($errno, $errstr) {
		// bummer: we cant open the file
	}
   	
	public function activateOptions() {
		$fileName = $this->getFile();

		if(!is_file($fileName)) {
			$dir = dirname($fileName);
			if(!is_dir($dir)) {
			   	/* Added by JD to fix Mutillidae issue */
				set_error_handler(array($this, 'mkdir_error_handler'));
				mkdir($dir, 0777, true);
		   		restore_error_handler();
			}
		}
		
		/* Added by JD to fix Mutillidae issue */
		set_error_handler(array($this, 'fopen_error_handler'));
		$this->fp = fopen($fileName, ($this->getAppend()? 'a':'w'));
		restore_error_handler();
		if($this->fp) {
			if(flock($this->fp, LOCK_EX)) {
				if($this->getAppend()) {
					fseek($this->fp, 0, SEEK_END);
				}
				fwrite($this->fp, $this->layout->getHeader());
				flock($this->fp, LOCK_UN);
				$this->closed = false;
			} else {
				// TODO: should we take some action in this case?
				$this->closed = true;
			}		 
		} else {
			$this->closed = true;
		}
	}
	
	public function close() {
		if($this->closed != true) {
			if($this->fp and $this->layout !== null) {
				if(flock($this->fp, LOCK_EX)) {
					fwrite($this->fp, $this->layout->getFooter());
					flock($this->fp, LOCK_UN);
				}
				fclose($this->fp);
			}
			$this->closed = true;
		}
	}

	public function append(LoggerLoggingEvent $event) {
		if($this->fp and $this->layout !== null) {
			if(flock($this->fp, LOCK_EX)) {
				fwrite($this->fp, $this->layout->format($event));
				flock($this->fp, LOCK_UN);
			} else {
				$this->closed = true;
			}
		} 
	}
	
	/**
	 * Sets and opens the file where the log output will go.
	 *
	 * This is an overloaded method. It can be called with:
	 * - setFile(string $fileName) to set filename.
	 * - setFile(string $fileName, boolean $append) to set filename and append.
	 */
	public function setFile() {
		$numargs = func_num_args();
		$args	 = func_get_args();

		if($numargs == 1 and is_string($args[0])) {
			$this->setFileName($args[0]);
		} else if ($numargs >=2 and is_string($args[0]) and is_bool($args[1])) {
			$this->setFile($args[0]);
			$this->setAppend($args[1]);
		}
	}
	
	/**
	 * @return string
	 */
	public function getFile() {
		return $this->getFileName();
	}
	
	/**
	 * @return boolean
	 */
	public function getAppend() {
		return $this->append;
	}

	public function setAppend($flag) {
		$this->append = LoggerOptionConverter::toBoolean($flag, true);		  
	}

	public function setFileName($fileName) {
		$this->fileName = $fileName;
	}
	
	/**
	 * @return string
	 */
	public function getFileName() {
		return $this->fileName;
	}
	
	 
}
