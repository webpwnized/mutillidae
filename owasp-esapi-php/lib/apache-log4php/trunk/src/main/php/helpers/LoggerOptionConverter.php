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
 * A convenience class to convert property values to specific types.
 *
 * @version $Revision: 795643 $ 
 * @package log4php
 * @subpackage helpers
 * @static
 * @since 0.5
 */
class LoggerOptionConverter {

	const DELIM_START = '${';
	const DELIM_STOP = '}';
	const DELIM_START_LEN = 2;
	const DELIM_STOP_LEN = 1;

   /**
	* Read a predefined var.
	*
	* It returns a value referenced by <var>$key</var> using this search criteria:
	* - if <var>$key</var> is a constant then return it. Else
	* - if <var>$key</var> is set in <var>$_ENV</var> then return it. Else
	* - return <var>$def</var>. 
	*
	* @param string $key The key to search for.
	* @param string $def The default value to return.
	* @return string	the string value of the system property, or the default
	*					value if there is no property with that key.
	*
	* @static
	*/
	public static function getSystemProperty($key, $def) {
		if(defined($key)) {
			return (string)constant($key);
		} else if(isset($_SERVER[$key])) {
			return (string)$_SERVER[$key];
		} else if(isset($_ENV[$key])) {
			return (string)$_ENV[$key];
		} else {
			return $def;
		}
	}

	/**
	 * If <var>$value</var> is <i>true</i>, then <i>true</i> is
	 * returned. If <var>$value</var> is <i>false</i>, then
	 * <i>true</i> is returned. Otherwise, <var>$default</var> is
	 * returned.
	 *
	 * <p>Case of value is unimportant.</p>
	 *
	 * @param string $value
	 * @param boolean $default
	 * @return boolean
	 *
	 * @static
	 */
	public static function toBoolean($value, $default) {
		if($value === null) {
			return $default;
		}
		if($value == 1) {
			return true;
		}
		$trimmedVal = strtolower(trim($value));
		if("true" == $trimmedVal or "yes" == $trimmedVal) {
			return true;
		}
		if("false" == $trimmedVal) {
			return false;
		}
		return $default;
	}

	/**
	 * @param string $value
	 * @param integer $default
	 * @return integer
	 * @static
	 */
	public static function toInt($value, $default) {
		$value = trim($value);
		if(is_numeric($value)) {
			return (int)$value;
		} else {
			return $default;
		}
	}

	/**
	 * Converts a standard or custom priority level to a Level
	 * object.
	 *
	 * <p> If <var>$value</var> is of form "<b>level#full_file_classname</b>",
	 * where <i>full_file_classname</i> means the class filename with path
	 * but without php extension, then the specified class' <i>toLevel()</i> method
	 * is called to process the specified level string; if no '#'
	 * character is present, then the default {@link LoggerLevel}
	 * class is used to process the level value.</p>
	 *
	 * <p>As a special case, if the <var>$value</var> parameter is
	 * equal to the string "NULL", then the value <i>null</i> will
	 * be returned.</p>
	 *
	 * <p>If any error occurs while converting the value to a level,
	 * the <var>$defaultValue</var> parameter, which may be
	 * <i>null</i>, is returned.</p>
	 *
	 * <p>Case of <var>$value</var> is insignificant for the level level, but is
	 * significant for the class name part, if present.</p>
	 *
	 * @param string $value
	 * @param LoggerLevel $defaultValue
	 * @return LoggerLevel a {@link LoggerLevel} or null
	 * @static
	 */
	public static function toLevel($value, $defaultValue) {
		if($value === null) {
			return $defaultValue;
		}
		$hashIndex = strpos($value, '#');
		if($hashIndex === false) {
			if("NULL" == strtoupper($value)) {
				return null;
			} else {
				// no class name specified : use standard Level class
				return LoggerLevel::toLevel($value, $defaultValue);
			}
		}

		$result = $defaultValue;

		$clazz = substr($value, ($hashIndex + 1));
		$levelName = substr($value, 0, $hashIndex);

		// This is degenerate case but you never know.
		if("NULL" == strtoupper($levelName)) {
			return null;
		}

		$clazz = basename($clazz);

		if(class_exists($clazz)) {
			$result = @call_user_func(array($clazz, 'toLevel'), $levelName, $defaultValue);
			if(!$result instanceof LoggerLevel) {
				$result = $defaultValue;
			}
		} 
		return $result;
	}

	/**
	 * @param string $value
	 * @param float $default
	 * @return float
	 *
	 * @static
	 */
	public static function toFileSize($value, $default) {
		if($value === null) {
			return $default;
		}

		$s = strtoupper(trim($value));
		$multiplier = (float)1;
		if(($index = strpos($s, 'KB')) !== false) {
			$multiplier = 1024;
			$s = substr($s, 0, $index);
		} else if(($index = strpos($s, 'MB')) !== false) {
			$multiplier = 1024 * 1024;
			$s = substr($s, 0, $index);
		} else if(($index = strpos($s, 'GB')) !== false) {
			$multiplier = 1024 * 1024 * 1024;
			$s = substr($s, 0, $index);
		}
		if(is_numeric($s)) {
			return (float)$s * $multiplier;
		} 
		return $default;
	}

	/**
	 * Find the value corresponding to <var>$key</var> in
	 * <var>$props</var>. Then perform variable substitution on the
	 * found value.
	 *
	 * @param string $key
	 * @param array $props
	 * @return string
	 *
	 * @static
	 */
	public static function findAndSubst($key, $props) {
		$value = @$props[$key];
		if(!empty($value)) {
			return LoggerOptionConverter::substVars($value, $props);
		}
	}

	/**
	 * Perform variable substitution in string <var>$val</var> from the
	 * values of keys found with the {@link getSystemProperty()} method.
	 * 
	 * <p>The variable substitution delimeters are <b>${</b> and <b>}</b>.
	 * 
	 * <p>For example, if the "MY_CONSTANT" contains "value", then
	 * the call
	 * <code>
	 * $s = LoggerOptionConverter::substituteVars("Value of key is ${MY_CONSTANT}.");
	 * </code>
	 * will set the variable <i>$s</i> to "Value of key is value.".</p>
	 * 
	 * <p>If no value could be found for the specified key, then the
	 * <var>$props</var> parameter is searched, if the value could not
	 * be found there, then substitution defaults to the empty string.</p>
	 * 
	 * <p>For example, if {@link getSystemProperty()} cannot find any value for the key
	 * "inexistentKey", then the call
	 * <code>
	 * $s = LoggerOptionConverter::substVars("Value of inexistentKey is [${inexistentKey}]");
	 * </code>
	 * will set <var>$s</var> to "Value of inexistentKey is []".</p>
	 * 
	 * <p>A warn is thrown if <var>$val</var> contains a start delimeter "${" 
	 * which is not balanced by a stop delimeter "}" and an empty string is returned.</p>
	 * 
	 * @log4j-author Avy Sharell
	 * 
	 * @param string $val The string on which variable substitution is performed.
	 * @param array $props
	 * @return string
	 *
	 * @static
	 */
	 // TODO: this method doesn't work correctly with key = true, it needs key = "true" which is odd
	public static function substVars($val, $props = null) {
		$sbuf = '';
		$i = 0;
		while(true) {
			$j = strpos($val, self::DELIM_START, $i);
			if($j === false) {
				// no more variables
				if($i == 0) { // this is a simple string
					return $val;
				} else { // add the tail string which contails no variables and return the result.
					$sbuf .= substr($val, $i);
					return $sbuf;
				}
			} else {
			
				$sbuf .= substr($val, $i, $j-$i);
				$k = strpos($val, self::DELIM_STOP, $j);
				if($k === false) {
					// LoggerOptionConverter::substVars() has no closing brace. Opening brace
					return '';
				} else {
					$j += self::START_LEN;
					$key = substr($val, $j, $k - $j);
					// first try in System properties
					$replacement = LoggerOptionConverter::getSystemProperty($key, null);
					// then try props parameter
					if($replacement == null and $props !== null) {
						$replacement = @$props[$key];
					}

					if(!empty($replacement)) {
						// Do variable substitution on the replacement string
						// such that we can solve "Hello ${x2}" as "Hello p1" 
						// the where the properties are
						// x1=p1
						// x2=${x1}
						$recursiveReplacement = LoggerOptionConverter::substVars($replacement, $props);
						$sbuf .= $recursiveReplacement;
					}
					$i = $k + self::DELIM_STOP_LEN;
				}
			}
		}
	}

}
