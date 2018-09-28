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
 * Extend this abstract class to create your own log layout format.
 *	
 * @version $Revision: 795643 $
 * @package log4php
 * @abstract
 */
abstract class LoggerLayout {
	/**
	 * Activates options for this layout.
	 * Override this method if you have options to be activated.
	 */
	public function activateOptions() {
		return true;
	}

	/**
	 * Override this method to create your own layout format.
	 *
	 * @param LoggerLoggingEvent
	 * @return string
	 */
	public function format(LoggerLoggingEvent $event) {
		return $event->getRenderedMessage();
	} 
	
	/**
	 * Returns the content type output by this layout.
	 * @return string
	 */
	public function getContentType() {
		return "text/plain";
	} 
			
	/**
	 * Returns the footer for the layout format.
	 * @return string
	 */
	public function getFooter() {
		return null;
	} 

	/**
	 * Returns the header for the layout format.
	 * @return string
	 */
	public function getHeader() {
		return null;
	}
}
