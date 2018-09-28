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

class LoggerAppenderFileTest extends PHPUnit_Framework_TestCase {
     
    protected function setUp() {
        if(file_exists('../../../target/temp/phpunit/TEST.txt')) {
	        unlink('../../../target/temp/phpunit/TEST.txt');
        }
    }
    
    public function testSimpleLogging() {
    	$layout = new LoggerLayoutSimple();
    	
    	$event = new LoggerLoggingEvent('LoggerAppenderFileTest', 
    									new Logger('mycategory'), 
    									LoggerLevel::getLevelWarn(),
    									"my message");
    	
    	$appender = new LoggerAppenderFile("mylogger"); 
		$appender->setFileName('../../../target/temp/phpunit/TEST.txt');
		$appender->setLayout($layout);
		$appender->activateOptions();
		$appender->append($event);
		$appender->close();

		$v = file_get_contents('../../../target/temp/phpunit/TEST.txt');		
		$e = "WARN - my message".PHP_EOL;
		self::assertEquals($e, $v);
    }
     
    protected function tearDown() {
        unlink('../../../target/temp/phpunit/TEST.txt');
        rmdir('../../../target/temp/phpunit');
    }
}
