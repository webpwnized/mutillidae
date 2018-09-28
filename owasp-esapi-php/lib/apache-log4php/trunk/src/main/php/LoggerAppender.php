<?php
/**
 * Licensed to the Apache Software Foundation (ASF) under one or more
 * contributor license agreements. See the NOTICE file distributed with
 * this work for additional information regarding copyright ownership.
 * The ASF licenses this file to You under the Apache License, Version 2.0
 * (the "License"); you may not use this file except in compliance with
 * the License. You may obtain a copy of the License at
 *
 *	   http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 *
 * @package log4php
 */

/**
 * Abstract class that defines output logs strategies.
 *
 * @version $Revision: 822392 $
 * @package log4php
 */
abstract class LoggerAppender {
	
	/**
	 * @var boolean closed appender flag
	 */
	protected $closed = false;
	
	/**
	 * The first filter in the filter chain
	 * @var LoggerFilter
	 */
	protected $filter = null;
			
	/**
	 * LoggerLayout for this appender. It can be null if appender has its own layout
	 * @var LoggerLayout
	 */
	protected $layout = null; 
		   
	/**
	 * @var string Appender name
	 */
	protected $name;
		   
	/**
	 * @var LoggerLevel There is no level threshold filtering by default.
	 */
	protected $threshold = null;
	
	/**
	 * @var boolean needs a layout formatting ?
	 */
	protected $requiresLayout = false;
	
	/**
	 * Constructor
	 *
	 * @param string $name appender name
	 */
	public function __construct($name = '') {
		$this->name = $name;
	}

	/**
	 * Add a filter to the end of the filter list.
	 *
	 * @param LoggerFilter $newFilter add a new LoggerFilter
	 */
	public function addFilter($newFilter) {
		if($this->filter === null) {
			$this->filter = $newFilter;
		} else {
			$this->filter->addNext($newFilter);
		}
	}
	
	/**
	 * Clear the list of filters by removing all the filters in it.
	 * @abstract
	 */
	public function clearFilters() {
		unset($this->filter);
		$this->filter = null;
	}

	/**
	 * Return the first filter in the filter chain for this Appender. 
	 * The return value may be <i>null</i> if no is filter is set.
	 * @return LoggerFilter
	 */
	public function getFilter() {
		return $this->filter;
	} 
	
	/** 
	 * Return the first filter in the filter chain for this Appender. 
	 * The return value may be <i>null</i> if no is filter is set.
	 * @return LoggerFilter
	 */
	public function getFirstFilter() {
		return $this->filter;
	}
	
	
	/**
	 * This method performs threshold checks and invokes filters before
	 * delegating actual logging to the subclasses specific <i>append()</i> method.
	 * @see LoggerAppender::doAppend()
	 * @param LoggerLoggingEvent $event
	 */
	public function doAppend(LoggerLoggingEvent $event) {
		if($this->closed) {
			return;
		}
		
		if(!$this->isAsSevereAsThreshold($event->getLevel())) {
			return;
		}

		$f = $this->getFirstFilter();
		while($f !== null) {
			switch ($f->decide($event)) {
				case LoggerFilter::DENY: return;
				case LoggerFilter::ACCEPT: return $this->append($event);
				case LoggerFilter::NEUTRAL: $f = $f->getNext();
			}
		}
		$this->append($event);	  
	}	 

	/**
	 * Set the Layout for this appender.
	 * @see LoggerAppender::setLayout()
	 * @param LoggerLayout $layout
	 */
	public function setLayout($layout) {
		if($this->requiresLayout()) {
			$this->layout = $layout;
		}
	} 
	
	/**
	 * Returns this appender layout.
	 * @see LoggerAppender::getLayout()
	 * @return LoggerLayout
	 */
	public function getLayout() {
		return $this->layout;
	}
	
	/**
	 * Configurators call this method to determine if the appender
	 * requires a layout. 
	 *
	 * <p>If this method returns <i>true</i>, meaning that layout is required, 
	 * then the configurator will configure a layout using the configuration 
	 * information at its disposal.	 If this method returns <i>false</i>, 
	 * meaning that a layout is not required, then layout configuration will be
	 * skipped even if there is available layout configuration
	 * information at the disposal of the configurator.</p>
	 *
	 * <p>In the rather exceptional case, where the appender
	 * implementation admits a layout but can also work without it, then
	 * the appender should return <i>true</i>.</p>
	 * 
	 * @see LoggerAppender::requiresLayout()
	 * @return boolean
	 */
	public function requiresLayout() {
		return $this->requiresLayout;
	}
	
	/**
	 * Get the name of this appender.
	 * @see LoggerAppender::getName()
	 * @return string
	 */
	public function getName() {
		return $this->name;
	}
	
/**
	 * Set the name of this appender.
	 *
	 * The name is used by other components to identify this appender.
	 *
	 * 
	 * @param string $name
	 */
	public function setName($name) {
		$this->name = $name;	
	}
	
	/**
	 * Returns this appenders threshold level. 
	 * See the {@link setThreshold()} method for the meaning of this option.
	 * @return LoggerLevel
	 */
	public function getThreshold() { 
		return $this->threshold;
	}
	
	/**
	 * Set the threshold level of this appender.
	 *
	 * @param mixed $threshold can be a {@link LoggerLevel} object or a string.
	 * @see LoggerOptionConverter::toLevel()
	 */
	public function setThreshold($threshold) {
		if(is_string($threshold)) {
		   $this->threshold = LoggerOptionConverter::toLevel($threshold, null);
		} else if($threshold instanceof LoggerLevel) {
		   $this->threshold = $threshold;
		}
	}
	
	/**
	 * Check whether the message level is below the appender's threshold. 
	 *
	 *
	 * If there is no threshold set, then the return value is always <i>true</i>.
	 * @param LoggerLevel $level
	 * @return boolean true if priority is greater or equal than threshold	
	 */
	public function isAsSevereAsThreshold($level) {
		if($this->threshold === null) {
			return true;
		}
		return $level->isGreaterOrEqual($this->getThreshold());
	}

	/**
	 * Derived appenders should override this method if option structure
	 * requires it.
	 */
	abstract public function activateOptions();	   
	
	/**
	 * Subclasses of {@link LoggerAppender} should implement 
	 * this method to perform actual logging.
	 *
	 * @param LoggerLoggingEvent $event
	 * @see doAppend()
	 * @abstract
	 */
	abstract protected function append(LoggerLoggingEvent $event); 

	/**
	 * Release any resources allocated.
	 * Subclasses of {@link LoggerAppender} should implement 
	 * this method to perform proper closing procedures.
	 * @abstract
	 */
	abstract public function close();
}
