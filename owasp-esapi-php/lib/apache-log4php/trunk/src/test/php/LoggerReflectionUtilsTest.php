<?php
/*
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


class Simple {
    private $name;
    private $male;
   
    public function getName() {
        return $this->name;
    }
    
    public function isMale() {
        return $this->male;
    }
    
    public function setName($name) {
        $this->name = $name;
    }
    
    public function setMale($male) {
        $this->male = $male;
    }
}
/**
 * Tests the LoggerReflectionUtils class
 */
class LoggerReflectionUtilsTest extends PHPUnit_Framework_TestCase {

	public function testSimpleSet() {
		$s = new Simple();
		$ps = new LoggerReflectionUtils($s);
 		$ps->setProperty("name", "Joe");
 		$ps->setProperty("male", true);
 		
 		$this->assertEquals($s->isMale(), true);
 		$this->assertEquals($s->getName(), 'Joe');
	}
	
	public function testSimpleArraySet() {
		$arr['xxxname'] = 'Joe';
		$arr['xxxmale'] = true;
		
		$s = new Simple();
		$ps = new LoggerReflectionUtils($s);
 		$ps->setProperties($arr, "xxx");
 		
 		$this->assertEquals($s->getName(), 'Joe');
 		$this->assertEquals($s->isMale(), true);
	}
	
	public function testStaticArraySet() {
		$arr['xxxname'] = 'Joe';
		$arr['xxxmale'] = true;
		
		$s = new Simple();
		LoggerReflectionUtils::setPropertiesByObject($s,$arr,"xxx");
		
 		$this->assertEquals($s->getName(), 'Joe');
 		$this->assertEquals($s->isMale(), true);
	}
	public function testCreateObject() {
		$object = LoggerReflectionUtils::createObject('LoggerLayoutSimple');
		$name = get_class($object);
		self::assertEquals($name, 'LoggerLayoutSimple');
	}
}
