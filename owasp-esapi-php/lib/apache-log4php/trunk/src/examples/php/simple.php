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
 */
require_once dirname(__FILE__).'/../../main/php/Logger.php';

class Log4phpTest {

        private $_logger;
    
    public function Log4phpTest() {
        $this->_logger = Logger::getLogger('Log4phpTest');
        $this->_logger->debug('Hello!');
    }

}

function Log4phpTestFunction() {
    $logger = Logger::getLogger('Log4phpTestFunction');
    $logger->debug('Hello again!');    
}

$test = new Log4phpTest();
Log4phpTestFunction();

// Safely close all appenders with...
Logger::shutdown();
?>
