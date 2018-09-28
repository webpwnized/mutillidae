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
 * This is the global repository of NDC stack
 */
$GLOBALS['log4php.LoggerNDC.ht'] = array();

/**
 * The NDC class implements <i>nested diagnostic contexts</i> as
 * defined by Neil Harrison in the article "Patterns for Logging
 * Diagnostic Messages" part of the book "<i>Pattern Languages of
 * Program Design 3</i>" edited by Martin et al.
 *
 * <p>A Nested Diagnostic Context, or NDC in short, is an instrument
 * to distinguish interleaved log output from different sources. Log
 * output is typically interleaved when a server handles multiple
 * clients near-simultaneously.
 *
 * <p>Interleaved log output can still be meaningful if each log entry
 * from different contexts had a distinctive stamp. This is where NDCs
 * come into play.
 *
 * <p><i><b>Note that NDCs are managed on a per thread
 * basis</b></i>. NDC operations such as {@link push()}, {@link pop()}, 
 * {@link clear()}, {@link getDepth()} and {@link setMaxDepth()}
 * affect the NDC of the <i>current</i> thread only. NDCs of other
 * threads remain unaffected.
 *
 * <p>For example, a servlet can build a per client request NDC
 * consisting the clients host name and other information contained in
 * the the request. <i>Cookies</i> are another source of distinctive
 * information. To build an NDC one uses the {@link push()}
 * operation.</p>
 * 
 * Simply put,
 *
 * - Contexts can be nested.
 * - When entering a context, call 
 *	 <code>LoggerNDC::push()</code>
 *	 As a side effect, if there is no nested diagnostic context for the
 *	 current thread, this method will create it.
 * - When leaving a context, call 
 *	 <code>LoggerNDC::pop()</code>
 * - <b>When exiting a thread make sure to call {@link remove()}</b>
 *	 
 * <p>There is no penalty for forgetting to match each
 * <code>push</code> operation with a corresponding <code>pop</code>,
 * except the obvious mismatch between the real application context
 * and the context set in the NDC.</p>
 *
 * <p>If configured to do so, {@link LoggerPatternLayout} and {@link LoggerLayoutTTCC} 
 * instances automatically retrieve the nested diagnostic
 * context for the current thread without any user intervention.
 * Hence, even if a servlet is serving multiple clients
 * simultaneously, the logs emanating from the same code (belonging to
 * the same category) can still be distinguished because each client
 * request will have a different NDC tag.</p>
 *
 *	
 * @version $Revision: 795643 $
 * @package log4php 
 * @since 0.3
 */
class LoggerNDC {
	const HT_SIZE = 7;
	/**
	 * Clear any nested diagnostic information if any. This method is
	 * useful in cases where the same thread can be potentially used
	 * over and over in different unrelated contexts.
	 *
	 * <p>This method is equivalent to calling the {@link setMaxDepth()}
	 * method with a zero <var>maxDepth</var> argument.
	 *
	 * @static	
	 */
	public static function clear() {
		$GLOBALS['log4php.LoggerNDC.ht'] = array();
	}

	/**
	 * Never use this method directly, use the {@link LoggerLoggingEvent::getNDC()} method instead.
	 * @static
	 * @return array
	 */
	public static function get() {
		if(!array_key_exists('log4php.LoggerNDC.ht', $GLOBALS)) {
			LoggerNDC::clear();
		}
		return $GLOBALS['log4php.LoggerNDC.ht'];
	}
  
	/**
	 * Get the current nesting depth of this diagnostic context.
	 *
	 * @see setMaxDepth()
	 * @return integer
	 * @static
	 */
	public static function getDepth() {
		return count($GLOBALS['log4php.LoggerNDC.ht']);	  
	}

	/**
	 * Clients should call this method before leaving a diagnostic
	 * context.
	 *
	 * <p>The returned value is the value that was pushed last. If no
	 * context is available, then the empty string "" is returned.</p>
	 *
	 * @return string The innermost diagnostic context.
	 * @static
	 */
	public static function pop() {
		if(count($GLOBALS['log4php.LoggerNDC.ht']) > 0) {
			return array_pop($GLOBALS['log4php.LoggerNDC.ht']);
		} else {
			return '';
		}
	}

	/**
	 * Looks at the last diagnostic context at the top of this NDC
	 * without removing it.
	 *
	 * <p>The returned value is the value that was pushed last. If no
	 * context is available, then the empty string "" is returned.</p>
	 * @return string The innermost diagnostic context.
	 * @static
	 */
	public static function peek(){
		if(count($GLOBALS['log4php.LoggerNDC.ht']) > 0) {
			return end($GLOBALS['log4php.LoggerNDC.ht']);
		} else {
			return '';
		}
	}
  
	/**
	 * Push new diagnostic context information for the current thread.
	 *
	 * <p>The contents of the <var>message</var> parameter is
	 * determined solely by the client.
	 *	
	 * @param string $message The new diagnostic context information.
	 * @static	
	 */
	public static function push($message) {
		array_push($GLOBALS['log4php.LoggerNDC.ht'], (string)$message);
	}

	/**
	 * Remove the diagnostic context for this thread.
	 * @static
	 */
	public static function remove() {
		LoggerNDC::clear();
	}

	/**
	 * Set maximum depth of this diagnostic context. If the current
	 * depth is smaller or equal to <var>maxDepth</var>, then no
	 * action is taken.
	 *
	 * <p>This method is a convenient alternative to multiple 
	 * {@link pop()} calls. Moreover, it is often the case that at 
	 * the end of complex call sequences, the depth of the NDC is
	 * unpredictable. The {@link setMaxDepth()} method circumvents
	 * this problem.
	 *
	 * @param integer $maxDepth
	 * @see getDepth()
	 * @static
	 */
	public static function setMaxDepth($maxDepth) {
		$maxDepth = (int)$maxDepth;
		if($maxDepth <= self::HT_SIZE) {
			if(LoggerNDC::getDepth() > $maxDepth) {
				$GLOBALS['log4php.LoggerNDC.ht'] = array_slice($GLOBALS['log4php.LoggerNDC.ht'], $maxDepth);
			}
		}
	}

}
