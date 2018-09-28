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

class LoggerTest extends PHPUnit_Framework_TestCase {
	
	protected function setUp() {
		Logger::clear();
		Logger::resetConfiguration();
	}
	
	protected function tearDown() {
		Logger::clear();
		Logger::resetConfiguration();
	}
	
	public function testLoggerExist() {
		$l = Logger::getLogger('test');
		self::assertEquals($l->getName(), 'test');
		$l->debug('test');
		self::assertTrue(Logger::exists('test'));
	}
	
	public function testCanGetRootLogger() {
		$l = Logger::getRootLogger();
		self::assertEquals($l->getName(), 'root');
	}
	
	public function testCanGetASpecificLogger() {
		$l = Logger::getLogger('test');
		self::assertEquals($l->getName(), 'test');
	}
	
	public function testCanLogToAllLevels() {
		Logger::configure('LoggerTest.properties');
		
		$logger = Logger::getLogger('mylogger');
		ob_start();
		$logger->info('this is an info');
		$logger->warn('this is a warning');
		$logger->error('this is an error');
		$logger->debug('this is a debug message');
		$logger->fatal('this is a fatal message');
		
		$v = ob_get_contents();
		ob_end_clean();
		
		$e = 'INFO - this is an info'.PHP_EOL;
		$e .= 'WARN - this is a warning'.PHP_EOL;
		$e .= 'ERROR - this is an error'.PHP_EOL;
		$e .= 'DEBUG - this is a debug message'.PHP_EOL;
		$e .= 'FATAL - this is a fatal message'.PHP_EOL;
		
		self::assertEquals($v, $e);
	}
	
	public function testIsEnabledFor() {
		Logger::configure('LoggerTest.properties');
		
		$logger = Logger::getLogger('mylogger');
		
		self::assertTrue($logger->isDebugEnabled());
		self::assertTrue($logger->isInfoEnabled());
		
		$logger = Logger::getRootLogger();
		
		self::assertFalse($logger->isDebugEnabled());
		self::assertFalse($logger->isInfoEnabled());
	}
	
	public function testGetCurrentLoggers() {
		Logger::clear();
		Logger::resetConfiguration();
		
		self::assertEquals(0, count(Logger::getCurrentLoggers()));
		
		Logger::configure('LoggerTest.properties');
		Logger::initialize();
		self::assertEquals(1, count(Logger::getCurrentLoggers()));
		$list = Logger::getCurrentLoggers();
		self::assertEquals('mylogger', $list[0]->getName());
	}
	
	public function testConfigure() {
		Logger::resetConfiguration();
		Logger::configure();
		self::assertEquals('LoggerConfiguratorBasic', Logger::getConfigurationClass());
		self::assertEquals(null, Logger::getConfigurationFile());
		
		Logger::configure(null, 'MyLoggerClass');
		self::assertEquals('MyLoggerClass', Logger::getConfigurationClass());
		self::assertEquals(null, Logger::getConfigurationFile());
		
		Logger::configure('log4php.xml');
		self::assertEquals('LoggerConfiguratorXml', Logger::getConfigurationClass());
		self::assertEquals('log4php.xml', Logger::getConfigurationFile());
		
		Logger::configure('log4php.xml');
		self::assertEquals('LoggerConfiguratorXml', Logger::getConfigurationClass());
		self::assertEquals('log4php.xml', Logger::getConfigurationFile());
		
		Logger::configure('log4php.properties');
		self::assertEquals('LoggerConfiguratorIni', Logger::getConfigurationClass());
		self::assertEquals('log4php.properties', Logger::getConfigurationFile());
		
	}
}
