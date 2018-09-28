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

class LoggerHierarchyTest extends PHPUnit_Framework_TestCase {
        
	private $hierarchy;
        
	protected function setUp() {
		$this->hierarchy = new LoggerHierarchy(new LoggerRoot());
	}
	
	public function testIfLevelIsInitiallyLevelAllg() {
		self::assertEquals('ALL', $this->hierarchy->getRootLogger()->getLevel()->toString());
	}

	public function testIfNameIsRoot() {
		self::assertEquals('root', $this->hierarchy->getRootLogger()->getName());
	}

	public function testIfParentIsNull() {
		self::assertSame(null, $this->hierarchy->getRootLogger()->getParent());
	}

	public function testSetParent() {
		$l = $this->hierarchy->getLogger('dummy');
		$this->hierarchy->getRootLogger()->setParent($l);
		$this->testIfParentIsNull();
	}
        
	public function testResetConfiguration() {
		$root = $this->hierarchy->getRootLogger();
		$appender = new LoggerAppenderConsole('A1');
		$root->addAppender($appender);
		$logger = $this->hierarchy->getLogger('test');
		self::assertEquals(count($this->hierarchy->getCurrentLoggers()), 1);
		$this->hierarchy->resetConfiguration();
		self::assertEquals($this->hierarchy->getRootLogger()->getLevel()->toString(), 'DEBUG');
		self::assertEquals($this->hierarchy->getThreshold()->toString(), 'ALL');
		self::assertEquals(count($this->hierarchy->getCurrentLoggers()), 1);
		foreach($this->hierarchy->getCurrentLoggers() as $l) {
			self::assertEquals($l->getLevel(), null);
			self::assertTrue($l->getAdditivity());
			self::assertEquals(count($l->getAllAppenders()), 0);
		}
	}
	
	public function testSettingParents() {
		$hierarchy = $this->hierarchy;
		$loggerDE = $hierarchy->getLogger("de");
		$root = $loggerDE->getParent();
		self::assertEquals('root', $root->getName());
		
		$loggerDEBLUB = $hierarchy->getLogger("de.blub");
		self::assertEquals('de.blub', $loggerDEBLUB->getName());
		$p = $loggerDEBLUB->getParent();
		self::assertEquals('de', $p->getName());
		
		$loggerDEBLA = $hierarchy->getLogger("de.bla");
		$p = $loggerDEBLA->getParent();
		self::assertEquals('de', $p->getName());
		
		$logger3 = $hierarchy->getLogger("de.bla.third");
		$p = $logger3->getParent();
		self::assertEquals('de.bla', $p->getName());
		
		$p = $p->getParent();
		self::assertEquals('de', $p->getName());
	}
	
	public function testExists() {
		$hierarchy = $this->hierarchy;
		$logger = $hierarchy->getLogger("de");
		
		self::assertTrue($hierarchy->exists("de"));
		
		$logger = $hierarchy->getLogger("de.blub");
		self::assertTrue($hierarchy->exists("de.blub"));
		self::assertTrue($hierarchy->exists("de"));
		
		$logger = $hierarchy->getLogger("de.de");
		self::assertTrue($hierarchy->exists("de.de"));
	}
	
	public function testClear() {
		$hierarchy = $this->hierarchy;
		$logger = $hierarchy->getLogger("de");
		self::assertTrue($hierarchy->exists("de"));
		$hierarchy->clear();
		self::assertFalse($hierarchy->exists("de"));
	}
}
