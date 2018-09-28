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

class LoggerLayoutXmlTest extends PHPUnit_Framework_TestCase {
        
	public function testErrorLayout() {
		$event = new LoggerLoggingEvent("LoggerLayoutXml", new Logger("TEST"), LoggerLevel::getLevelError(), "testmessage");

		$layout = new LoggerLayoutXml();
		$v = $layout->format($event);

		$e = "<log4php:event logger=\"TEST\" level=\"ERROR\" thread=\"".$event->getThreadName().
			"\" timestamp=\"".number_format((float)($event->getTimeStamp() * 1000), 0, '', '')."\">".PHP_EOL.
			"<log4php:message><![CDATA[testmessage]]></log4php:message>".PHP_EOL.
			"<log4php:locationInfo class=\"LoggerLoggingEvent\" file=\"NA\" line=\"NA\" " .
			"method=\"getLocationInformation\" />".PHP_EOL.
			"</log4php:event>\n".PHP_EOL;

		self::assertEquals($v, $e);
    }
    
    public function testWarnLayout() {
		$event = new LoggerLoggingEvent("LoggerLayoutXml", new Logger("TEST"), LoggerLevel::getLevelWarn(), "testmessage");

		$layout = new LoggerLayoutXml();
		$v = $layout->format($event);

		$e = "<log4php:event logger=\"TEST\" level=\"WARN\" thread=\"".$event->getThreadName().
			"\" timestamp=\"".number_format((float)($event->getTimeStamp() * 1000), 0, '', '')."\">".PHP_EOL.
			"<log4php:message><![CDATA[testmessage]]></log4php:message>".PHP_EOL.
			"<log4php:locationInfo class=\"LoggerLoggingEvent\" file=\"NA\" line=\"NA\" " .
			"method=\"getLocationInformation\" />".PHP_EOL.
			"</log4php:event>\n".PHP_EOL;
		
		self::assertEquals($v, $e);
    }
}
