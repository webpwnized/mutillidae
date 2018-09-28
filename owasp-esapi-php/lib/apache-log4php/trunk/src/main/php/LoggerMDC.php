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
 * This is the global repository of user mappings
 */
$GLOBALS['log4php.LoggerMDC.ht'] = array();

/**
 * The LoggerMDC class is similar to the {@link LoggerNDC} class except that it is
 * based on a map instead of a stack. It provides <i>mapped diagnostic contexts</i>.
 * 
 * A <i>Mapped Diagnostic Context</i>, or
 * MDC in short, is an instrument for distinguishing interleaved log
 * output from different sources. Log output is typically interleaved
 * when a server handles multiple clients near-simultaneously.
 *
 * <p><b><i>The MDC is managed on a per thread basis</i></b>.
 * 
 * @version $Revision: 795658 $
 * @since 0.3
 * @package log4php
 */
class LoggerMDC {
	/**
	 * Put a context value as identified with the key parameter into the current thread's
	 *	context map.
	 *
	 * <p>If the current thread does not have a context map it is
	 *	created as a side effect.</p>
	 *
	 * <p>Note that you cannot put more than {@link self::HT_SIZE} keys.</p>
	 *
	 * @param string $key the key
	 * @param string $value the value
	 * @static
	 */
	public static function put($key, $value) {
		$GLOBALS['log4php.LoggerMDC.ht'][$key] = $value;
	}
  
	/**
	 * Get the context identified by the key parameter.
	 *
	 * <p>You can use special key identifiers to map values in 
	 * PHP $_SERVER and $_ENV vars. Just put a 'server.' or 'env.'
	 * followed by the var name you want to refer.</p>
	 *
	 * <p>This method has no side effects.</p>
	 *
	 * @param string $key
	 * @return string
	 * @static
	 */
	public static function get($key) {
		if(!empty($key)) {
			if(strpos($key, 'server.') === 0) {
				$varName = substr($key, 7);
				return @$_SERVER[$varName];
			} else if(strpos($key, 'env.') === 0) {
				$varName = substr($key, 4);
				return @$_ENV[$varName];
			} else if (isset($GLOBALS['log4php.LoggerMDC.ht'][$key])) {
				return $GLOBALS['log4php.LoggerMDC.ht'][$key];
			}
		}
		return '';
	}

	/**
	 * Remove the the context identified by the key parameter. 
	 *
	 * It only affects user mappings.
	 *
	 * @param string $key
	 * @return string
	 * @static
	 */
	public static function remove($key) {
		unset($GLOBALS['log4php.LoggerMDC.ht'][$key]);
	}

}
