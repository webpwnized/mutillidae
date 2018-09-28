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
 * Array for fast space padding
 * Used by {@link LoggerPatternConverter::spacePad()}.	
 * 
 * @package log4php
 * @subpackage helpers
 */
$GLOBALS['log4php.LoggerPatternConverter.spaces'] = array(
	" ", // 1 space
	"  ", // 2 spaces
	"    ", // 4 spaces
	"        ", // 8 spaces
	"                ", // 16 spaces
	"                                " ); // 32 spaces


/**
 * LoggerPatternConverter is an abstract class that provides the formatting 
 * functionality that derived classes need.
 * 
 * <p>Conversion specifiers in a conversion patterns are parsed to
 * individual PatternConverters. Each of which is responsible for
 * converting a logging event in a converter specific manner.</p>
 * 
 * @version $Revision: 798408 $
 * @package log4php
 * @subpackage helpers
 * @abstract
 * @since 0.3
 */
class LoggerPatternConverter {

	/**
	 * @var LoggerPatternConverter next converter in converter chain
	 */
	public $next = null;
	
	public $min = -1;
	public $max = 0x7FFFFFFF;
	public $leftAlign = false;

	/**
	 * Constructor 
	 *
	 * @param LoggerFormattingInfo $fi
	 */
	public function __construct($fi = null) {  
		if($fi !== null) {
			$this->min = $fi->min;
			$this->max = $fi->max;
			$this->leftAlign = $fi->leftAlign;
		}
	}
  
	/**
	 * Derived pattern converters must override this method in order to
	 * convert conversion specifiers in the correct way.
	 *
	 * @param LoggerLoggingEvent $event
	 */
	public function convert($event) {}

	/**
	 * A template method for formatting in a converter specific way.
	 *
	 * @param string $sbuf string buffer
	 * @param LoggerLoggingEvent $e
	 */
	public function format(&$sbuf, $e) {
		$s = $this->convert($e);
		
		if($s == null or empty($s)) {
			if(0 < $this->min) {
				$this->spacePad($sbuf, $this->min);
			}
			return;
		}
		
		$len = strlen($s);
	
		if($len > $this->max) {
			$sbuf .= substr($s , 0, ($len - $this->max));
		} else if($len < $this->min) {
			if($this->leftAlign) {		
				$sbuf .= $s;
				$this->spacePad($sbuf, ($this->min - $len));
			} else {
				$this->spacePad($sbuf, ($this->min - $len));
				$sbuf .= $s;
			}
		} else {
			$sbuf .= $s;
		}
	}	

	/**
	 * Fast space padding method.
	 *
	 * @param string	&$sbuf	   string buffer
	 * @param integer	$length	   pad length
	 *
	 * @todo reimplement using PHP string functions
	 */
	public function spacePad(&$sbuf, $length) {
		while($length >= 32) {
		  $sbuf .= $GLOBALS['log4php.LoggerPatternConverter.spaces'][5];
		  $length -= 32;
		}
		
		for($i = 4; $i >= 0; $i--) {	
			if(($length & (1<<$i)) != 0) {
				$sbuf .= $GLOBALS['log4php.LoggerPatternConverter.spaces'][$i];
			}
		}

		// $sbuf = str_pad($sbuf, $length);
	}
}
