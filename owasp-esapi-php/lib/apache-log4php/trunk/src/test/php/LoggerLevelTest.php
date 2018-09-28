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

/**
 * Tests the LoggerLevel
 */
class LoggerLevelTest extends PHPUnit_Framework_TestCase {
        
	protected function doTestLevel($o, $code, $str, $syslog) {
		self::assertTrue( $o instanceof LoggerLevel );
		self::assertEquals( $o->toInt(), $code);
		self::assertEquals( $o->toString(), $str);
		self::assertEquals( $o->getSyslogEquivalent(), $syslog);
	}

	public function testLevelOff() {
		$this->doTestLevel( LoggerLevel::getLevelOff(), LoggerLevel::OFF, 'OFF', 0 );
		$this->doTestLevel( LoggerLevel::toLevel(LoggerLevel::OFF), LoggerLevel::OFF, 'OFF', 0 );
		$this->doTestLevel( LoggerLevel::toLevel('OFF'), LoggerLevel::OFF, 'OFF', 0 );
    }

	public function testLevelFatal() {
		$this->doTestLevel( LoggerLevel::getLevelFatal(), LoggerLevel::FATAL, 'FATAL', 0 );
		$this->doTestLevel( LoggerLevel::toLevel(LoggerLevel::FATAL), LoggerLevel::FATAL, 'FATAL', 0 );
		$this->doTestLevel( LoggerLevel::toLevel('FATAL'), LoggerLevel::FATAL, 'FATAL', 0 );
    }

	public function testLevelError() {
		$this->doTestLevel( LoggerLevel::getLevelError(), LoggerLevel::ERROR, 'ERROR', 3 );
		$this->doTestLevel( LoggerLevel::toLevel(LoggerLevel::ERROR), LoggerLevel::ERROR, 'ERROR', 3 );
		$this->doTestLevel( LoggerLevel::toLevel('ERROR'), LoggerLevel::ERROR, 'ERROR', 3 );
    }
	
	public function testLevelWarn() {
		$this->doTestLevel( LoggerLevel::getLevelWarn(), LoggerLevel::WARN, 'WARN', 4 );
		$this->doTestLevel( LoggerLevel::toLevel(LoggerLevel::WARN), LoggerLevel::WARN, 'WARN', 4 );
		$this->doTestLevel( LoggerLevel::toLevel('WARN'), LoggerLevel::WARN, 'WARN', 4 );
    }

	public function testLevelInfo() {
		$this->doTestLevel( LoggerLevel::getLevelInfo(), LoggerLevel::INFO, 'INFO', 6 );
		$this->doTestLevel( LoggerLevel::toLevel(LoggerLevel::INFO), LoggerLevel::INFO, 'INFO', 6 );
		$this->doTestLevel( LoggerLevel::toLevel('INFO'), LoggerLevel::INFO, 'INFO', 6 );
    }

	public function testLevelDebug() {
		$this->doTestLevel( LoggerLevel::getLevelDebug(), LoggerLevel::DEBUG, 'DEBUG', 7 );
		$this->doTestLevel( LoggerLevel::toLevel(LoggerLevel::DEBUG), LoggerLevel::DEBUG, 'DEBUG', 7 );
		$this->doTestLevel( LoggerLevel::toLevel('DEBUG'), LoggerLevel::DEBUG, 'DEBUG', 7 );
    }

	public function testLevelAll() {
		$this->doTestLevel( LoggerLevel::getLevelAll(), LoggerLevel::ALL, 'ALL', 7 );
		$this->doTestLevel( LoggerLevel::toLevel(LoggerLevel::ALL), LoggerLevel::ALL, 'ALL', 7 );
		$this->doTestLevel( LoggerLevel::toLevel('ALL'), LoggerLevel::ALL, 'ALL', 7 );
    }
}
