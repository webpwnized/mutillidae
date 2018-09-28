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
 * @subpackage renderers
 * @license    http://www.apache.org/licenses/LICENSE-2.0 Apache License, Version 2.0
 * @version    SVN: $Id$
 * @link       http://logging.apache.org/log4php
 */

class LoggerConfiguratorBasicTest extends PHPUnit_Framework_TestCase {
        
	protected function setUp() {
	}
        
	protected function tearDown() {
		Logger::resetConfiguration();
	}
        
	public function testConfigure() {
		$root = Logger::getRootLogger();
		$appender = $root->getAppender('A1');
		self::assertType('LoggerAppenderConsole', $appender);
		$layout = $appender->getLayout();
		self::assertType('LoggerLayoutTTCC', $layout);
		
		$event = new LoggerLoggingEvent('LoggerAppenderConsoleTest', 
    									new Logger('mycategory'), 
    									LoggerLevel::getLevelWarn(),
    									"my message");
		$appender->setTarget('STDOUT');
		$appender->activateOptions();
		
		ob_start();
		$appender->append($event);
		$v = ob_get_contents();
		ob_end_clean();
		$appender->close();
	}

	public function testResetConfiguration() {
		$root = Logger::getRootLogger();
		$appender = $root->getAppender('A1');
		self::assertType('LoggerAppenderConsole', $appender);
		$layout = $appender->getLayout();
		self::assertType('LoggerLayoutTTCC', $layout);
		
		// As PHPUnit runs all tests in one run, there might be some loggers left over
		// from previous runs. ResetConfiguration() only clears the appenders, it does
		// not remove the categories!
		Logger::resetConfiguration();
        foreach (Logger::getCurrentLoggers() as $logger) {
            self::assertEquals(0, count($logger->getAllAppenders()));
        }		

        // This on the other hand really removes the categories:
        Logger::clear(); 
		self::assertEquals(0, count(Logger::getCurrentLoggers()));
	}
}
