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
 * Implemented by classes capable of configuring log4php using a URL.
 *	
 * @version $Revision: 804867 $
 * @package log4php
 */
interface LoggerConfigurator {
	
	/**
	 * Special level value signifying inherited behaviour. The current
	 * value of this string constant is <b>inherited</b>. 
	 * {@link CONFIGURATOR_NULL} is a synonym.  
	 */
	const CONFIGURATOR_INHERITED = 'inherited';
	
	/**
	 * Special level signifying inherited behaviour, same as 
	 * {@link CONFIGURATOR_INHERITED}. 
	 * The current value of this string constant is <b>null</b>. 
	 */
	const CONFIGURATOR_NULL = 'null';
		
	/**
	 * Interpret a resource pointed by a <var>url</var> and configure accordingly.
	 *
	 * The configuration is done relative to the <var>repository</var>
	 * parameter.
	 *
	 * @param string $url The URL to parse
	 */
	public function configure(LoggerHierarchy $hierarchy, $url = null);
	
}
