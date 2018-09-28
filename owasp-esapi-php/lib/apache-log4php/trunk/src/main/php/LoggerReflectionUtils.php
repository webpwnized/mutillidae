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
  * Provides methods for reflective use on php objects
  * @package log4php
  */
class LoggerReflectionUtils {
		/** the target object */
	private $obj;
  
	/**
	 * Create a new LoggerReflectionUtils for the specified Object. 
	 * This is done in prepartion for invoking {@link setProperty()} 
	 * one or more times.
	 * @param object &$obj the object for which to set properties
	 */
	public function __construct($obj) {
		$this->obj = $obj;
	}
  
	/**
	 * Set the properties of an object passed as a parameter in one
	 * go. The <code>properties</code> are parsed relative to a
	 * <code>prefix</code>.
	 *
	 * @param object $obj The object to configure.
	 * @param array $properties An array containing keys and values.
	 * @param string $prefix Only keys having the specified prefix will be set.
	 * @static
	 */
	 // TODO: check, if this is really useful
	public static function setPropertiesByObject($obj, $properties, $prefix) {
		$pSetter = new LoggerReflectionUtils($obj);
		return $pSetter->setProperties($properties, $prefix);
	}
  

	/**
	 * Set the properites for the object that match the
	 * <code>prefix</code> passed as parameter.
	 * 
	 * Example:
	 * 
	 * $arr['xxxname'] = 'Joe';
 	 * $arr['xxxmale'] = true;
	 * and prefix xxx causes setName and setMale.	
	 *
	 * @param array $properties An array containing keys and values.
	 * @param string $prefix Only keys having the specified prefix will be set.
	 */
	 // TODO: check, if this is really useful
	public function setProperties($properties, $prefix) {
		$len = strlen($prefix);
		while(list($key,) = each($properties)) {
			if(strpos($key, $prefix) === 0) {
				if(strpos($key, '.', ($len + 1)) > 0) {
					continue;
				}
				$value = LoggerOptionConverter::findAndSubst($key, $properties);
				$key = substr($key, $len);
				if($key == 'layout' and ($this->obj instanceof LoggerAppender)) {
					continue;
				}
				$this->setProperty($key, $value);
			}
		}
		$this->activate();
	}
	
	/**
	 * Set a property on this PropertySetter's Object. If successful, this
	 * method will invoke a setter method on the underlying Object. The
	 * setter is the one for the specified property name and the value is
	 * determined partly from the setter argument type and partly from the
	 * value specified in the call to this method.
	 *
	 * <p>If the setter expects a String no conversion is necessary.
	 * If it expects an int, then an attempt is made to convert 'value'
	 * to an int using new Integer(value). If the setter expects a boolean,
	 * the conversion is by new Boolean(value).
	 *
	 * @param string $name	  name of the property
	 * @param string $value	  String value of the property
	 */
	public function setProperty($name, $value) {
		if($value === null) {
			return;
		}
		
		$method = "set" . ucfirst($name);
		
		if(!method_exists($this->obj, $method)) {
			// no such setter method
			return;
		} else {
			return call_user_func(array($this->obj, $method), $value);
		} 
	}
  
	public function activate() {
		if(method_exists($this->obj, 'activateoptions')) {
			return call_user_func(array($this->obj, 'activateoptions'));
		} 
	}
	
	/**
	 * Creates an instances from the given class name.
	 *
	 * @param string $classname
	 * @return an object from the class with the given classname
	 */
	public static function createObject($class) {
		if(!empty($class)) {
			$class = basename($class);
			return new $class();
		}
		return null;
	}
	
	/**
     * @param object $object
     * @param string $name
     * @param mixed $value
     */
    public function setter($object, $name, $value) {
        if (empty($name)) {
            return false;
        }
        $methodName = 'set'.ucfirst($name);
        if (method_exists($object, $methodName)) {
            return call_user_func(array($object, $methodName), $value);
        } else {
            return false;
        }
    }
	
}
