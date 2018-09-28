<?php
/**
 * Licensed to the Apache Software Foundation (ASF) under one or more
 * contributor license agreements.  See the NOTICE file distributed with
 * this work for additional information regarding copyright ownership.
 * The ASF licenses this file to You under the Apache License, Version 2.0
 * (the "License"); you may not use this file except in compliance with
 * the License.  You may obtain a copy of the License at
 * 
 *      http://www.apache.org/licenses/LICENSE-2.0
 * 
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 * 
 * @category   tests   
 * @package    log4php
 * @subpackage appenders
 * @license    http://www.apache.org/licenses/LICENSE-2.0 Apache License, Version 2.0
 * @version    SVN: $Id$
 * @link       http://logging.apache.org/log4php
 */

// TODO: Should also test complex patterns like: "%d{Y-m-d H:i:s} %-5p %c %X{username}: %m in %F at %L%n"
class LoggerLayoutPatternTest extends PHPUnit_Framework_TestCase {
        
	public function testErrorLayout() {
		$event = new LoggerLoggingEvent("LoggerLayoutXml", new Logger("TEST"), LoggerLevel::getLevelError(), "testmessage");

		$layout = new LoggerLayoutPattern();
		$layout->setConversionPattern("%-5p %c %X{username}: %m in %F at %L%n");
		$v = $layout->format($event);
		$e = 'ERROR TEST : testmessage in NA at NA'.PHP_EOL;

		self::assertEquals($v, $e);
    }
    
    public function XtestWarnLayout() {
    	/*
Comment in when this has been fixed:

1) testWarnLayout(LoggerLayoutPatternTest)
Undefined index:  log4php.LoggerPatternConverter.spaces
/Users/cgrobmeier/Documents/Development/workspace/log4php-trunk/src/main/php/helpers/LoggerPatternConverter.php:131
/Users/cgrobmeier/Documents/Development/workspace/log4php-trunk/src/main/php/helpers/LoggerPatternConverter.php:104
/Users/cgrobmeier/Documents/Development/workspace/log4php-trunk/src/main/php/layouts/LoggerPatternLayout.php:216
/Users/cgrobmeier/Documents/Development/workspace/log4php-trunk/src/test/php/layouts/LoggerLayoutPatternTest.php:45

    	 * 
    	 */
		$event = new LoggerLoggingEvent("LoggerLayoutXml", new Logger("TEST"), LoggerLevel::getLevelWarn(), "testmessage");

		$layout = new LoggerLayoutPattern();
		$layout->setConversionPattern("%-5p %c %X{username}: %m in %F at %L%n");
		$v = $layout->format($event);
		$e = 'WARN TEST : testmessage in NA at NA'.PHP_EOL;
		
		self::assertEquals($v, $e);
    }
}
