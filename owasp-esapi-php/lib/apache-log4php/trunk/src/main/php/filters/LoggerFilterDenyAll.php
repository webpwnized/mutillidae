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
 * This filter drops all logging events. 
 * 
 * <p>You can add this filter to the end of a filter chain to
 * switch from the default "accept all unless instructed otherwise"
 * filtering behaviour to a "deny all unless instructed otherwise"
 * behaviour.</p>
 *
 * @version $Revision: 795643 $
 * @package log4php
 * @subpackage filters
 * @since 0.3
 */
class LoggerFilterDenyAll extends LoggerFilter {

	/**
	 * Always returns the integer constant {@link LoggerFilter::DENY}
	 * regardless of the {@link LoggerLoggingEvent} parameter.
	 * 
	 * @param LoggerLoggingEvent $event The {@link LoggerLoggingEvent} to filter.
	 * @return LoggerFilter::DENY Always returns {@link LoggerFilter::DENY}
	 */
	public function decide(LoggerLoggingEvent $event) {
		return LoggerFilter::DENY;
	}
}
