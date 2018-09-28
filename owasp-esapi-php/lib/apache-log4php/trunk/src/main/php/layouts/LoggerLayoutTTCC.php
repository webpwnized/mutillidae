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
 * TTCC layout format consists of time, thread, category and nested
 * diagnostic context information, hence the name.
 * 
 * <p>Each of the four fields can be individually enabled or
 * disabled. The time format depends on the <b>DateFormat</b> used.</p>
 *
 * <p>If no dateFormat is specified it defaults to '%c'. 
 * See php {@link PHP_MANUAL#date} function for details.</p>
 *
 * Params:
 * - {@link $threadPrinting} (true|false) enable/disable pid reporting.
 * - {@link $categoryPrefixing} (true|false) enable/disable logger category reporting.
 * - {@link $contextPrinting} (true|false) enable/disable NDC reporting.
 * - {@link $microSecondsPrinting} (true|false) enable/disable micro seconds reporting in timestamp.
 * - {@link $dateFormat} (string) set date format. See php {@link PHP_MANUAL#date} function for details.
 *
 * @version $Revision: 795643 $
 * @package log4php
 * @subpackage layouts
 */
class LoggerLayoutTTCC extends LoggerLayout {

	/**
	 * String constant designating no time information. Current value of
	 * this constant is <b>NULL</b>.
	 */
	 // TODO: not used?
	const LOG4PHP_LOGGER_LAYOUT_NULL_DATE_FORMAT = 'NULL';
	
	/**
	 * String constant designating relative time. Current value of
	 * this constant is <b>RELATIVE</b>.
	 */
	 // TODO: not used?
	const LOG4PHP_LOGGER_LAYOUT_RELATIVE_TIME_DATE_FORMAT = 'RELATIVE';
	
    // Internal representation of options
    protected $threadPrinting    = true;
    protected $categoryPrefixing = true;
    protected $contextPrinting   = true;
    protected $microSecondsPrinting = true;
    
    /**
     * @var string date format. See {@link PHP_MANUAL#strftime} for details
     */
    protected $dateFormat = '%c';

    /**
     * Constructor
     *
     * @param string date format
     * @see dateFormat
     */
    public function __construct($dateFormat = '') {
        if (!empty($dateFormat)) {
            $this->dateFormat = $dateFormat;
        }
        return;
    }

    /**
     * The <b>ThreadPrinting</b> option specifies whether the name of the
     * current thread is part of log output or not. This is true by default.
     */
    public function setThreadPrinting($threadPrinting) {
        $this->threadPrinting = is_bool($threadPrinting) ? 
            $threadPrinting : 
            (bool)(strtolower($threadPrinting) == 'true'); 
    }

    /**
     * @return boolean Returns value of the <b>ThreadPrinting</b> option.
     */
    public function getThreadPrinting() {
        return $this->threadPrinting;
    }

    /**
     * The <b>CategoryPrefixing</b> option specifies whether {@link Category}
     * name is part of log output or not. This is true by default.
     */
    public function setCategoryPrefixing($categoryPrefixing) {
        $this->categoryPrefixing = is_bool($categoryPrefixing) ?
            $categoryPrefixing :
            (bool)(strtolower($categoryPrefixing) == 'true');
    }

    /**
     * @return boolean Returns value of the <b>CategoryPrefixing</b> option.
     */
    public function getCategoryPrefixing() {
        return $this->categoryPrefixing;
    }

    /**
     * The <b>ContextPrinting</b> option specifies log output will include
     * the nested context information belonging to the current thread.
     * This is true by default.
     */
    public function setContextPrinting($contextPrinting) {
        $this->contextPrinting = is_bool($contextPrinting) ? 
            $contextPrinting : 
            (bool)(strtolower($contextPrinting) == 'true'); 
    }

    /**
     * @return boolean Returns value of the <b>ContextPrinting</b> option.
     */
    public function getContextPrinting() {
        return $this->contextPrinting;
    }
    
    /**
     * The <b>MicroSecondsPrinting</b> option specifies if microseconds infos
     * should be printed at the end of timestamp.
     * This is true by default.
     */
    public function setMicroSecondsPrinting($microSecondsPrinting) {
        $this->microSecondsPrinting = is_bool($microSecondsPrinting) ? 
            $microSecondsPrinting : 
            (bool)(strtolower($microSecondsPrinting) == 'true'); 
    }

    /**
     * @return boolean Returns value of the <b>MicroSecondsPrinting</b> option.
     */
    public function getMicroSecondsPrinting() {
        return $this->microSecondsPrinting;
    }
    
    
    public function setDateFormat($dateFormat) {
        $this->dateFormat = $dateFormat;
    }
    
    /**
     * @return string
     */
    public function getDateFormat() {
        return $this->dateFormat;
    }

    /**
     * In addition to the level of the statement and message, the
     * returned string includes time, thread, category.
     * <p>Time, thread, category are printed depending on options.
     *
     * @param LoggerLoggingEvent $event
     * @return string
     */
    public function format(LoggerLoggingEvent $event) {
        $timeStamp = (float)$event->getTimeStamp();
        $format = strftime($this->dateFormat, (int)$timeStamp);
        
        if ($this->microSecondsPrinting) {
            $usecs = floor(($timeStamp - (int)$timeStamp) * 1000);
            $format .= sprintf(',%03d', $usecs);
        }
            
        $format .= ' ';
        
        if ($this->threadPrinting) {
            $format .= '['.getmypid().'] ';
        }
        
        $level = $event->getLevel();
        $format .= $level->toString().' ';
        
        if($this->categoryPrefixing) {
            $format .= $event->getLoggerName().' ';
        }
       
        if($this->contextPrinting) {
            $ndc = $event->getNDC();
            if($ndc != null) {
                $format .= $ndc.' ';
            }
        }
        
        $format .= '- '.$event->getRenderedMessage();
        $format .= PHP_EOL;
        
        return $format;
    }

    public function ignoresThrowable() {
        return true;
    }
}
