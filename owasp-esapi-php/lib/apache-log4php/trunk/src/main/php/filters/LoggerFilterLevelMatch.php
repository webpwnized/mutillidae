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
 * This is a very simple filter based on level matching.
 *
 * <p>The filter admits two options <b><var>LevelToMatch</var></b> and
 * <b><var>AcceptOnMatch</var></b>. If there is an exact match between the value
 * of the <b><var>LevelToMatch</var></b> option and the level of the 
 * {@link LoggerLoggingEvent}, then the {@link decide()} method returns 
 * {@link LoggerFilter::ACCEPT} in case the <b><var>AcceptOnMatch</var></b> 
 * option value is set to <i>true</i>, if it is <i>false</i> then 
 * {@link LoggerFilter::DENY} is returned. If there is no match, 
 * {@link LoggerFilter::NEUTRAL} is returned.</p>
 *
 * @version $Revision: 795643 $
 * @package log4php
 * @subpackage filters
 * @since 0.6
 */
class LoggerFilterLevelMatch extends LoggerFilter {
  
	/** 
	 * Indicates if this event should be accepted or denied on match
	 * @var boolean
	 */
	private $acceptOnMatch = true;

	/**
	 * The level, when to match
	 * @var LoggerLevel
	 */
	private $levelToMatch;
  
	/**
	 * @param boolean $acceptOnMatch
	 */
	public function setAcceptOnMatch($acceptOnMatch) {
		$this->acceptOnMatch = LoggerOptionConverter::toBoolean($acceptOnMatch, true); 
	}
	
	/**
	 * @param string $l the level to match
	 */
	public function setLevelToMatch($l) {
		if($l instanceof LoggerLevel) {
		    $this->levelToMatch = $l;
		} else {
			$this->levelToMatch = LoggerOptionConverter::toLevel($l, null);
		}
	}

	/**
	 * Return the decision of this filter.
	 * 
	 * Returns {@link LoggerFilter::NEUTRAL} if the <b><var>LevelToMatch</var></b>
	 * option is not set or if there is not match.	Otherwise, if there is a
	 * match, then the returned decision is {@link LoggerFilter::ACCEPT} if the
	 * <b><var>AcceptOnMatch</var></b> property is set to <i>true</i>. The
	 * returned decision is {@link LoggerFilter::DENY} if the
	 * <b><var>AcceptOnMatch</var></b> property is set to <i>false</i>.
	 *
	 * @param LoggerLoggingEvent $event
	 * @return integer
	 */
	public function decide(LoggerLoggingEvent $event) {
		if($this->levelToMatch === null) {
			return LoggerFilter::NEUTRAL;
		}
		
		if($this->levelToMatch->equals($event->getLevel())) {	
			return $this->acceptOnMatch ? LoggerFilter::ACCEPT : LoggerFilter::DENY;
		} else {
			return LoggerFilter::NEUTRAL;
		}
	}
}
