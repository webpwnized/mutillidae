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
 * @license    http://www.apache.org/licenses/LICENSE-2.0 Apache License, Version 2.0
 * @version    SVN: $Id$
 * @link       http://logging.apache.org/log4php
 */

class LoggerLoggingEventTestCaseAppender extends LoggerAppenderNull {
        
	protected $requiresLayout = true;

	public function append($event) {
		$this->layout->format($event);
	}

}

class LoggerLoggingEventTestCaseLayout extends LoggerLayout { 
        
	public function activateOptions() {
		return;
	}
        
	public function format(LoggerLoggingEvent $event) {
		LoggerLoggingEventTest::$locationInfo = $event->getLocationInformation();
	}
}

class LoggerLoggingEventTest extends PHPUnit_Framework_TestCase {
        
	public static $locationInfo;

	public function testConstructWithLoggerName() {
		$l = LoggerLevel :: getLevelDebug();
		$e = new LoggerLoggingEvent('fqcn', 'TestLogger', $l, 'test');
		self::assertEquals($e->getLoggerName(), 'TestLogger');
	}

	public function testConstructWithTimestamp() {
		$l = LoggerLevel :: getLevelDebug();
		$timestamp = microtime(true);
		$e = new LoggerLoggingEvent('fqcn', 'TestLogger', $l, 'test', $timestamp);
		self::assertEquals($e->getTimeStamp(), $timestamp);
 	}

	public function testGetStartTime() {
		$time = LoggerLoggingEvent :: getStartTime();
		self::assertType('float', $time);
		$time2 = LoggerLoggingEvent :: getStartTime();
		self::assertEquals($time, $time2);
	}

	public function testGetLocationInformation() {
		$hierarchy = Logger::getHierarchy();
		$root = $hierarchy->getRootLogger();

		$a = new LoggerLoggingEventTestCaseAppender('A1');
		$a->setLayout( new LoggerLoggingEventTestCaseLayout() );
		$root->addAppender($a);
                
		$logger = $hierarchy->getLogger('test');

		$line = __LINE__; $logger->debug('test');
		$hierarchy->shutdown();
                
		$li = self::$locationInfo;
                
		self::assertEquals($li->getClassName(), get_class($this));
		self::assertEquals($li->getFileName(), __FILE__);
		self::assertEquals($li->getLineNumber(), $line);
		self::assertEquals($li->getMethodName(), __FUNCTION__);

	}

}
