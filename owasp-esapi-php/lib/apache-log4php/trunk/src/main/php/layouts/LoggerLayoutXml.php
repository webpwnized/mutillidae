<?php
/**
 * Licensed to the Apache Software Foundation (ASF) under one or more
 * contributor license agreements.  See the NOTICE file distributed with
 * this work for additional information regarding copyright ownership.
 * The ASF licenses this file to You under the Apache License, Version 2.0
 * (the "License"); you may not use this file except in compliance with
 * the License.  You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 *
 */

/**
 * The output of the LoggerXmlLayout consists of a series of log4php:event elements. 
 * 
 * <p>Parameters: {@link $locationInfo}.</p>
 *
 * <p>It does not output a complete well-formed XML file. 
 * The output is designed to be included as an external entity in a separate file to form
 * a correct XML file.</p>
 *
 * @version $Revision: 795734 $
 * @package log4php
 * @subpackage layouts
 */
class LoggerLayoutXml extends LoggerLayout {
	const LOG4J_NS_PREFIX ='log4j';
	const LOG4J_NS = 'http://jakarta.apache.org/log4j/';
	
	const LOG4PHP_NS_PREFIX = 'log4php';
	const LOG4PHP_NS = 'http://logging.apache.org/log4php/';
	
	const CDATA_START = '<![CDATA[';
	const CDATA_END = ']]>';
	const CDATA_PSEUDO_END = ']]&gt;';

	const CDATA_EMBEDDED_END = ']]>]]&gt;<![CDATA[';

    /**
     * The <b>LocationInfo</b> option takes a boolean value. By default,
     * it is set to false which means there will be no location
     * information output by this layout. If the the option is set to
     * true, then the file name and line number of the statement at the
     * origin of the log statement will be output.
     * @var boolean
     */
    private $locationInfo = true;
  
    /**
     * @var boolean set the elements namespace
     */
    private $log4jNamespace = false;
    
    
    /**
     * @var string namespace
     * @private
     */
    private $_namespace = self::LOG4PHP_NS;
    
    /**
     * @var string namespace prefix
     * @private
     */
    private $_namespacePrefix = self::LOG4PHP_NS_PREFIX;
     
    /** 
     * No options to activate. 
     */
    public function activateOptions() {
        if ($this->getLog4jNamespace()) {
            $this->_namespace        = self::LOG4J_NS;
            $this->_namespacePrefix  = self::LOG4J_NS_PREFIX;
        } else {
            $this->_namespace        = self::LOG4PHP_NS;
            $this->_namespacePrefix  = self::LOG4PHP_NS_PREFIX;
        }     
    }
    
    /**
     * @return string
     */
    public function getHeader() {
        return "<{$this->_namespacePrefix}:eventSet ".
                    "xmlns:{$this->_namespacePrefix}=\"{$this->_namespace}\" ".
                    "version=\"0.3\" ".
                    "includesLocationInfo=\"".($this->getLocationInfo() ? "true" : "false")."\"".
               ">\r\n";
    }

    /**
     * Formats a {@link LoggerLoggingEvent} in conformance with the log4php.dtd.
     *
     * @param LoggerLoggingEvent $event
     * @return string
     */
    public function format(LoggerLoggingEvent $event) {
        $loggerName = $event->getLoggerName();
        $timeStamp  = number_format((float)($event->getTimeStamp() * 1000), 0, '', '');
        $thread     = $event->getThreadName();
        $level      = $event->getLevel();
        $levelStr   = $level->toString();

        $buf = "<{$this->_namespacePrefix}:event logger=\"{$loggerName}\" level=\"{$levelStr}\" thread=\"{$thread}\" timestamp=\"{$timeStamp}\">".PHP_EOL;
        $buf .= "<{$this->_namespacePrefix}:message><![CDATA["; 
        $this->appendEscapingCDATA($buf, $event->getRenderedMessage()); 
        $buf .= "]]></{$this->_namespacePrefix}:message>".PHP_EOL;        

        $ndc = $event->getNDC();
        if($ndc != null) {
            $buf .= "<{$this->_namespacePrefix}:NDC><![CDATA[";
            $this->appendEscapingCDATA($buf, $ndc);
            $buf .= "]]></{$this->_namespacePrefix}:NDC>".PHP_EOL;       
        }

        if ($this->getLocationInfo()) {
            $locationInfo = $event->getLocationInformation();
            $buf .= "<{$this->_namespacePrefix}:locationInfo ". 
                    "class=\"" . $locationInfo->getClassName() . "\" ".
                    "file=\"" .  htmlentities($locationInfo->getFileName(), ENT_QUOTES) . "\" ".
                    "line=\"" .  $locationInfo->getLineNumber() . "\" ".
                    "method=\"" . $locationInfo->getMethodName() . "\" ";
            $buf .= "/>".PHP_EOL;

        }

        $buf .= "</{$this->_namespacePrefix}:event>".PHP_EOL.PHP_EOL;
        
        return $buf;

    }
    
    /**
     * @return string
     */
    public function getFooter() {
        return "</{$this->_namespacePrefix}:eventSet>\r\n";
    }
    
    /**
     * @return boolean
     */
    public function getLocationInfo() {
        return $this->locationInfo;
    }
  
    /**
     * The {@link $locationInfo} option takes a boolean value. By default,
     * it is set to false which means there will be no location
     * information output by this layout. If the the option is set to
     * true, then the file name and line number of the statement at the
     * origin of the log statement will be output.
     */
    public function setLocationInfo($flag) {
        $this->locationInfo = LoggerOptionConverter::toBoolean($flag, true);
    }
  
    /**
     * @param boolean
     */
    public function setLog4jNamespace($flag) {
        $this->log4jNamespace = LoggerOptionConverter::toBoolean($flag, true);
    }
    
    /**
	 * Ensures that embeded CDEnd strings (]]&gt;) are handled properly
	 * within message, NDC and throwable tag text.
	 *
	 * @param string $buf	String holding the XML data to this point.	The
	 *						initial CDStart (<![CDATA[) and final CDEnd (]]>) 
	 *						of the CDATA section are the responsibility of 
	 *						the calling method.
	 * @param string str	The String that is inserted into an existing 
	 *						CDATA Section within buf.
	 * @static  
	 */
	private function appendEscapingCDATA(&$buf, $str) {
		if(empty($str)) {
			return;
		}
	
		$rStr = str_replace(
			self::CDATA_END,
			self::CDATA_EMBEDDED_END,
			$str
		);
		$buf .= $rStr;
	}
}

