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
 * This class is specialized in retrieving loggers by name and also maintaining 
 * the logger hierarchy. The logger hierarchy is dealing with the several Log-Levels
 * Logger can have. From log4j website:
 * 
 * "A logger is said to be an ancestor of another logger if its name followed 
 * by a dot is a prefix of the descendant logger name. A logger is said to be
 * a parent of a child logger if there are no ancestors between itself and the 
 * descendant logger."
 * 
 * Child Loggers do inherit their Log-Levels from their Ancestors. They can
 * increase their Log-Level compared to their Ancestors, but they cannot decrease it.
 * 
 * <p>The casual user does not have to deal with this class directly.</p>
 *
 * <p>The structure of the logger hierarchy is maintained by the
 * getLogger method. The hierarchy is such that children link
 * to their parent but parents do not have any pointers to their
 * children. Moreover, loggers can be instantiated in any order, in
 * particular descendant before ancestor.</p>
 *
 * <p>In case a descendant is created before a particular ancestor,
 * then it creates a provision node for the ancestor and adds itself
 * to the provision node. Other descendants of the same ancestor add
 * themselves to the previously created provision node.</p>
 *
 * @version $Revision: 804669 $
 * @package log4php
 */
 /*
  * TODO:
  * - addHierarchyEventListener($listener) not supported
  * - emitNoAppenderWarning($cat) not supported
  * - fireAddAppenderEvent not supported
  */
class LoggerHierarchy {
	/** Default Factory  */
	protected $defaultFactory;
	
	/** array hierarchy tree. saves here all loggers */
	protected $ht = array();
	
	/** The root Logger */
	protected $root = null;
	
	/** LoggerRendererMap */
	protected $rendererMap;

	/** LoggerLevel main level threshold */
	protected $threshold;
	
	/**
	 * Create a new logger hierarchy.
	 * @param object $root the root logger
	 */
	public function __construct(LoggerRoot $root) {
		$this->root = $root;
		// TODO: isn't necessary, access via singleton?'
//		$this->root->setHierarchy($this);
		$this->setThreshold(LoggerLevel::getLevelAll());
		$this->rendererMap = new LoggerRendererMap();
	}
	 
	/**
	 * Add an object renderer for a specific class.
	 * @param string $classToRender
	 * @param LoggerObjectRenderer $or
	 */
	public function addRenderer($classToRender, $or) {
		$this->rendererMap->put($classToRender, $or);
	} 
	
	/**
	 * This call will clear all logger definitions from the internal hashtable.
	 */
	public function clear() {
		$this->ht = array();
	}
	  
	/**
	 * Check if the named logger exists in the hierarchy.
	 * @param string $name
	 * @return boolean
	 */
	public function exists($name) {
		return isset($this->ht[$name]);
	}

	/**
	 * Returns all the currently defined categories in this hierarchy as an array.
	 * @return array
	 */	 
	public function getCurrentLoggers() {
		return array_values($this->ht);
	}
	
	/**
	 * Return a new logger instance named as the first parameter using the default factory.
	 * 
	 * @param string $name logger name
	 * @param LoggerFactory $factory a {@link LoggerFactory} instance or null	  
	 * @return Logger
	 */
	public function getLogger($name) {
		if(!isset($this->ht[$name])) {
			$this->ht[$name] = new Logger($name);;
			// TODO: isn't necessary, access via singleton?
			// $this->ht[$name]->setHierarchy($this);
			$nodes = explode('.', $name);
			$firstNode = array_shift($nodes);
			
			// if name is not a first node but another first node is their
			if($firstNode != $name and isset($this->ht[$firstNode])) {
				$this->ht[$name]->setParent($this->ht[$firstNode]);
			} else {
				// if there is no father, set root logger as father
				$this->ht[$name]->setParent($this->root);
			} 
		
			// if there are more nodes than one
			if(count($nodes) > 0) {
				// find parent node
				foreach($nodes as $node) {
					$parentNode = "$firstNode.$node";
					if(isset($this->ht[$parentNode]) and $parentNode != $name) {
		
						$this->ht[$name]->setParent($this->ht[$parentNode]);
					}
					$firstNode .= ".$node";
				}
			}
		}		
		
		return $this->ht[$name];
	} 
	
	/**
	 * @return LoggerRendererMap Get the renderer map for this hierarchy.
	 */
	public function getRendererMap() {
		return $this->rendererMap;
	}
	
	/**
	 * @return LoggerRoot Get the root of this hierarchy.
	 */ 
	public function getRootLogger() {
		if(!isset($this->root) or $this->root == null) {
			$this->root = new LoggerRoot();
		}
		return $this->root;
	}
	 
	/**
	 * @return LoggerLevel Returns the threshold Level.
	 */
	public function getThreshold() {
		return $this->threshold;
	} 

	/**
	 * This method will return true if this repository is disabled 
	 * for level object passed as parameter and false otherwise.
	 * @return boolean
	 */
	public function isDisabled(LoggerLevel $level) {
		return ($this->threshold->toInt() > $level->toInt());
	}
	
	/**
	 * Reset all values contained in this hierarchy instance to their
	 * default. 
	 *
	 * This removes all appenders from all categories, sets
	 * the level of all non-root categories to <i>null</i>,
	 * sets their additivity flag to <i>true</i> and sets the level
	 * of the root logger to {@link LOGGER_LEVEL_DEBUG}.  Moreover,
	 * message disabling is set its default "off" value.
	 * 
	 * <p>Existing categories are not removed. They are just reset.
	 *
	 * <p>This method should be used sparingly and with care as it will
	 * block all logging until it is completed.</p>
	 */
	public function resetConfiguration() {
		$root = $this->getRootLogger();
		
		$root->setLevel(LoggerLevel::getLevelDebug());
		$this->setThreshold(LoggerLevel::getLevelAll());
		$this->shutDown();
		$loggers = $this->getCurrentLoggers();
		$enumLoggers = count($loggers);
		for($i = 0; $i < $enumLoggers; $i++) {
			$loggers[$i]->setLevel(null);
			$loggers[$i]->setAdditivity(true);
			$loggers[$i]->removeAllAppenders();
		}
		$this->rendererMap->clear();
	}
	
	/**
	 * Used by subclasses to add a renderer to the hierarchy passed as parameter.
	 * @param string $renderedClass a LoggerRenderer class name
	 * @param LoggerRenderer $renderer
	 *
	 */
	public function setRenderer($renderedClass, $renderer) {
		$this->rendererMap->put($renderedClass, $renderer);
	}
	
	/**
	 * set a new threshold level
	 *
	 * @param LoggerLevel $l
	 */
	public function setThreshold(LoggerLevel $l) {
		if($l !== null) {
			$this->threshold = $l;
		}
	}
	
	/**
	 * Shutting down a hierarchy will <i>safely</i> close and remove
	 * all appenders in all categories including the root logger.
	 * 
	 * <p>Some appenders such as {@link LoggerSocketAppender}
	 * need to be closed before the
	 * application exists. Otherwise, pending logging events might be
	 * lost.
	 * 
	 * <p>The shutdown method is careful to close nested
	 * appenders before closing regular appenders. This is allows
	 * configurations where a regular appender is attached to a logger
	 * and again to a nested appender.
	 */
	public function shutdown() {
		$this->root->removeAllAppenders();
		$cats = $this->getCurrentLoggers();
		$enumCats = count($cats);		  
		if($enumCats > 0) {
			for($i = 0; $i < $enumCats; $i++) {
				$cats[$i]->removeAllAppenders();
			}
		}
	}
} 
