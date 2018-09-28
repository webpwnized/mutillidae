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
 */

/**
 * This is a very simple filter based on level matching, which can be
 * used to reject messages with priorities outside a certain range.
 *	
 * <p>The filter admits three options <b><var>LevelMin</var></b>, <b><var>LevelMax</var></b>
 * and <b><var>AcceptOnMatch</var></b>.</p>
 *
 * <p>If the level of the {@link LoggerLoggingEvent} is not between Min and Max
 * (inclusive), then {@link LoggerFilter::DENY} is returned.</p>
 *	
 * <p>If the Logging event level is within the specified range, then if
 * <b><var>AcceptOnMatch</var></b> is <i>true</i>, 
 * {@link LoggerFilter::ACCEPT} is returned, and if
 * <b><var>AcceptOnMatch</var></b> is <i>false</i>, 
 * {@link LoggerFilter::NEUTRAL} is returned.</p>
 *	
 * <p>If <b><var>LevelMin</var></b> is not defined, then there is no
 * minimum acceptable level (i.e. a level is never rejected for
 * being too "low"/unimportant).  If <b><var>LevelMax</var></b> is not
 * defined, then there is no maximum acceptable level (ie a
 * level is never rejected for being too "high"/important).</p>
 *
 * <p>Refer to the {@link LoggerAppender::setThreshold()} method
 * available to <b>all</b> appenders extending {@link LoggerAppender} 
 * for a more convenient way to filter out events by level.</p>
 *
 * @log4j-class org.apache.log4j.varia.LevelRangeFilter
 * @log4j-author Simon Kitching
 * @log4j-author based on code by Ceki G&uuml;lc&uuml; 
 *
 * @version $Revision: 795643 $
 * @package log4php
 * @subpackage filters
 * @since 0.6
 */
class LoggerFilterLevelRange extends LoggerFilter {

	/**
	 * @var boolean
	 */
	private $acceptOnMatch = true;

	/**
	 * @var LoggerLevel
	 */
	private $levelMin;
  
	/**
	 * @var LoggerLevel
	 */
	private $levelMax;

	/**
	 * @param boolean $acceptOnMatch
	 */
	public function setAcceptOnMatch($acceptOnMatch) {
		$this->acceptOnMatch = LoggerOptionConverter::toBoolean($acceptOnMatch, true); 
	}
	
	/**
	 * @param string $l the level min to match
	 */
	public function setLevelMin($l) {
		if($l instanceof LoggerLevel) {
		    $this->levelMin = $l;
		} else {
			$this->levelMin = LoggerOptionConverter::toLevel($l, null);
		}
	}

	/**
	 * @param string $l the level max to match
	 */
	public function setLevelMax($l) {
		if($l instanceof LoggerLevel) {
		    $this->levelMax = $l;
		} else {
			$this->levelMax = LoggerOptionConverter::toLevel($l, null);
		}
	}

	/**
	 * Return the decision of this filter.
	 *
	 * @param LoggerLoggingEvent $event
	 * @return integer
	 */
	public function decide(LoggerLoggingEvent $event) {
		$level = $event->getLevel();
		
		if($this->levelMin !== null) {
			if($level->isGreaterOrEqual($this->levelMin) == false) {
				// level of event is less than minimum
				return LoggerFilter::DENY;
			}
		}

		if($this->levelMax !== null) {
			if($level->toInt() > $this->levelMax->toInt()) {
				// level of event is greater than maximum
				// Alas, there is no Level.isGreater method. and using
				// a combo of isGreaterOrEqual && !Equal seems worse than
				// checking the int values of the level objects..
				return LoggerFilter::DENY;
			}
		}

		if($this->acceptOnMatch) {
			// this filter set up to bypass later filters and always return
			// accept if level in range
			return LoggerFilter::ACCEPT;
		} else {
			// event is ok for this filter; allow later filters to have a look..
			return LoggerFilter::NEUTRAL;
		}
	}
}
