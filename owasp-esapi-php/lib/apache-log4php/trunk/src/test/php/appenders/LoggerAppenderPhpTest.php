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
 
function errorHandler($errno, $errstr, $errfile, $errline) {
	switch ($errno) {
    	case E_USER_ERROR: 
    			PHPUnit_Framework_TestCase::assertEquals($errstr, "ERROR - testmessage".PHP_EOL); 
    			break;
    	case E_USER_WARNING:
    			PHPUnit_Framework_TestCase::assertEquals($errstr, "WARN - testmessage".PHP_EOL); 
        		break;
	    case E_USER_NOTICE:
    			PHPUnit_Framework_TestCase::assertEquals($errstr, "DEBUG - testmessage".PHP_EOL); 
        		break;
	    default: 
	    		PHPUnit_Framework_TestCase::assertTrue(false);
	}
}

class LoggerAppenderPhpTest extends PHPUnit_Framework_TestCase {
    protected function setUp() {
		set_error_handler("errorHandler");
	}
    
	public function testPhp() {
		$appender = new LoggerAppenderPhp("TEST");
		
		$layout = new LoggerLayoutSimple();
		$appender->setLayout($layout);
		$appender->activateOptions();
		$event = new LoggerLoggingEvent("LoggerAppenderPhpTest", new Logger("TEST"), LoggerLevel::getLevelError(), "testmessage");
		$appender->append($event);
		
		$event = new LoggerLoggingEvent("LoggerAppenderPhpTest", new Logger("TEST"), LoggerLevel::getLevelWarn(), "testmessage");
		$appender->append($event);
		
		$event = new LoggerLoggingEvent("LoggerAppenderPhpTest", new Logger("TEST"), LoggerLevel::getLevelDebug(), "testmessage");
		$appender->append($event);
    }
    
    protected function tearDown() {
		restore_error_handler();
	}
}
