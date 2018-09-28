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
Logger::configure(dirname(__FILE__).'/../resources/server.properties');

require_once 'Net/Server.php';
require_once 'Net/Server/Handler.php';

class Net_Server_Handler_Log extends Net_Server_Handler {
  
        var $hierarchy;

        function onStart() {
                $this->hierarchy = Logger::getLoggerRepository();
        }
  
        function onReceiveData($clientId = 0, $data = "") {
                $events = $this->getEvents($data);
                foreach($events as $event) {
                        $root = $this->hierarchy->getRootLogger();
                        if($event->getLoggerName() === 'root') {
                                $root->callAppenders($event);      
                        } else {
                                $loggers = $this->hierarchy->getCurrentLoggers();
                                foreach($loggers as $logger) {
                                        $root->callAppenders($event);
                                        $appenders = $logger->getAllAppenders();
                                        foreach($appenders as $appender) {
                                                $appender->doAppend($event);
                                        }
                                }
                        }
                }
        }
  
        function getEvents($data) {
                preg_match('/^(O:\d+)/', $data, $parts);
                $events = split($parts[1], $data);
                array_shift($events);
                $size = count($events);
                for($i=0; $i<$size; $i++) {
                        $events[$i] = unserialize($parts[1].$events[$i]);
                }
                return $events;
        }
}

$host = '127.0.0.1';
$port = 9090;
//$server =& Net_Server::create('fork', $host, $port);
$server =& Net_Server::create('sequential', $host, $port);
$handler =& new Net_Server_Handler_Log();
$server->setCallbackObject($handler);
$server->start();
?>
