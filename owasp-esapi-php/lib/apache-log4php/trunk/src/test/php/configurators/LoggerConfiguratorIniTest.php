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

class LoggerConfiguratorIniTest extends PHPUnit_Framework_TestCase {
        
	protected function setUp() {
		
	}
	
	protected function tearDown() {
		Logger::resetConfiguration();
	}
        
	public function testConfigure() {
		Logger::configure('configurators/test1.properties');
		$root = Logger::getRootLogger();
		self::assertEquals(LoggerLevel::getLevelWarn(), $root->getLevel());
		$appender = $root->getAppender("default");
		self::assertTrue($appender instanceof LoggerAppenderEcho);
		$layout = $appender->getLayout();
		self::assertTrue($layout instanceof LoggerLayoutSimple);
		
		$logger = Logger::getLogger('mylogger');
		self::assertEquals(LoggerLevel::getLevelInfo(), $logger->getLevel());
		self::assertFalse($logger->getAdditivity());
		
		$logger2 = Logger::getLogger('mylogger');
		$logger2->setAdditivity(true);
		self::assertTrue($logger2->getAdditivity());
		self::assertTrue($logger->getAdditivity());
	}
	
	public function testConfigureWithRootCategory() {
		Logger::configure('configurators/test3.properties');
		$root = Logger::getRootLogger();
		self::assertEquals(LoggerLevel::getLevelWarn(), $root->getLevel());
		$appender = $root->getAppender("default");
		self::assertTrue($appender instanceof LoggerAppenderEcho);
		$layout = $appender->getLayout();
		self::assertTrue($layout instanceof LoggerLayoutSimple);
	}
	
	public function testConfigureWithoutIniFile() {
	    $catchedException = null;
	    try {
	       Logger::configure(null,'LoggerConfiguratorIni');
	       Logger::initialize();
	       self::assertTrue(false);
	    } catch (LoggerException $e) {
	    	$catchedException = $e;
	    }
		self::assertNotNull($catchedException);
	}
	
	public function testConfigureWithEmptyIniFile() {
		$catchedException = null;
	    try {
	       Logger::configure('configurators/test2.properties');
	       Logger::initialize();
	       self::assertTrue(false);
	    } catch (LoggerException $e) {
	    	$catchedException = $e;
	    }
		self::assertNotNull($catchedException);
	}
}
