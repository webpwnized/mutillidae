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

class LoggerRendererMapTest extends PHPUnit_Framework_TestCase {

	public function testAddRenderer() {
		$hierarchy = Logger::getHierarchy();
		//print_r($hierarchy);
		LoggerRendererMap::addRenderer($hierarchy, 'string', 'LoggerRendererDefault');
		//print_r($hierarchy);
		self::markTestIncomplete();
	}
        
	public function testFindAndRender() {
		self::markTestIncomplete();
	}
        
	public function testGetByObject() {
		self::markTestIncomplete();
	}
        
	public function testGetByClassName() {
		self::markTestIncomplete();
	}
        
	public function testGetDefaultRenderer() {
		self::markTestIncomplete();
	}
        
	public function testClear() {
		self::markTestIncomplete();
	}
        
	public function testPut() {
		self::markTestIncomplete();
	}
        
	public function testRendererExists() {
		self::markTestIncomplete();
	}

}
