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

class LoggerAppenderPDOTest extends PHPUnit_Framework_TestCase {
        
	public function testSimpleLogging() {
		
		if(!extension_loaded('pdo_sqlite')) {
			self::markTestSkipped("Please install 'pdo_sqlite' in order to run this test");
		}
		
		$event = new LoggerLoggingEvent("LoggerAppenderPDOTest", new Logger("TEST"), LoggerLevel::getLevelError(), "testmessage");

		$dbname = '../../../target/pdotest.sqlite';
		$dsn = 'sqlite:'.$dbname;

		$database = new PDO($dsn);
		$database = null;
		
		$appender = new LoggerAppenderPDO("myname");
		$appender->setDSN($dsn);
		$appender->setCreateTable(true);
		$appender->activateOptions();
		$appender->append($event);
		
		
		$db = $appender->getDatabaseHandle();
		$q = "select * from log4php_log";	
		$error = "";
		if($result = $db->query($q)) {
			while($row = $result->fetch()) {
    			self::assertEquals($row['1'], 'TEST');
    			self::assertEquals($row['2'], 'ERROR');
    			self::assertEquals($row['3'], 'testmessage');
  			}
		} else {
			// todo propagate exception to phpunit
		   self::assertTrue(false);
		}
		$appender->close();
		
    }
    
    public function testException() {
        $dsn = 'doenotexist';
        $appender = new LoggerAppenderPDO("myname");
        $appender->setDSN($dsn);
        $appender->setCreateTable(true);
        
        $catchedException = null;
        try {
            $appender->activateOptions();
        } catch (LoggerException $e) {
            $catchedException = $e;
        }
        self::assertNotNull($catchedException);
    }
    
    public function tearDown() {
    	@unlink('../../../target/pdotest.sqlite');
    }
    
}
