<?php
/**
 * Licensed to the Apache Software Foundation (ASF) under one or more
 * contributor license agreements.  See the NOTICE file distributed with
 * this work for additional information regarding copyright ownership.
 * The ASF licenses this file to You under the Apache License, Version 2.0
 * (the "License"); you may not use this file except in compliance with
 * the License.  You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 *
 */

/**
 * Use this class to initialize the log4php environment using expat parser.
 *
 * <p>Read the log4php.dtd included in the documentation directory. Note that
 * php parser does not validate the document.</p>
 *
 * <p>Sometimes it is useful to see how log4php is reading configuration
 * files. You can enable log4php internal logging by setting the <var>debug</var> 
 * attribute in the <var>log4php:configuration</var> element. As in
 * <pre>
 * &lt;log4php:configuration <b>debug="true"</b> xmlns:log4php="http://logging.apache.org/log4php/">
 * ...
 * &lt;/log4php:configuration>
 * </pre>
 *
 * <p>There are sample XML files included in the package under <b>tests/</b> 
 * subdirectories.</p>
 *
 * @version $Revision: 805681 $
 * @package log4php
 * @subpackage configurators
 * @since 0.4 
 */
class LoggerConfiguratorXml implements LoggerConfigurator {
	const APPENDER_STATE = 1000;
	const LAYOUT_STATE = 1010;
	const ROOT_STATE = 1020;
	const LOGGER_STATE = 1030;
	const FILTER_STATE = 1040;
	
	const DEFAULT_FILENAME = './log4php.xml';
	
	/**
	 * @var string the default configuration document
	 */
	const DEFAULT_CONFIGURATION = 
	'<?xml version="1.0" ?>
	<log4php:configuration threshold="all">
	    <appender name="A1" class="LoggerAppenderEcho">
	        <layout class="LoggerLayoutSimple" />
	    </appender>
	    <root>
	        <level value="debug" />
	        <appender_ref ref="A1" />
	    </root>
	</log4php:configuration>';
	
	/**
	 * @var string the elements namespace
	 */
	const XMLNS = 'HTTP://LOGGING.APACHE.ORG/LOG4PHP/'; 

    /**
     * @var LoggerHierarchy
     */
    private $repository;
    
    /**
     * @var array state stack 
     */
    private $state;

    /**
     * @var Logger parsed Logger  
     */
    private $logger;
    
    /**
     * @var LoggerAppender parsed LoggerAppender 
     */
    private $appender;
    
    /**
     * @var LoggerFilter parsed LoggerFilter 
     */
    private $filter;
    
    /**
     * @var LoggerLayout parsed LoggerLayout 
     */
    private $layout;
    
    /**
     * Constructor
     */
    public function __construct() {
        $this->state    = array();
        $this->logger   = null;
        $this->appender = null;
        $this->filter   = null;
        $this->layout   = null;
    }
    
    /**
     * Configure the default repository using the resource pointed by <b>url</b>.
     * <b>Url</b> is any valid resource as defined in {@link PHP_MANUAL#file} function.
     * Note that the resource will be search with <i>use_include_path</i> parameter 
     * set to "1".
     *
     * @param string $url
     * @static
     */
    public function configure(LoggerHierarchy $hierarchy, $url = '') {
        return $this->doConfigure($url, $hierarchy);
    }
    
    /**
     * Configure the given <b>repository</b> using the resource pointed by <b>url</b>.
     * <b>Url</b> is any valid resurce as defined in {@link PHP_MANUAL#file} function.
     * Note that the resource will be search with <i>use_include_path</i> parameter 
     * set to "1".
     *
     * @param string $url
     * @param LoggerHierarchy $repository
     */
    private function doConfigure($url = '', LoggerHierarchy $repository)
    {
        $xmlData = '';
        if (!empty($url))
            $xmlData = implode('', file($url, 1));
        return $this->doConfigureByString($xmlData, $repository);
    }
    
    /**
     * Configure the given <b>repository</b> using the configuration written in <b>xmlData</b>.
     * Do not call this method directly. Use {@link doConfigure()} instead.
     * @param string $xmlData
     * @param LoggerHierarchy $repository
     */
    private function doConfigureByString($xmlData, LoggerHierarchy $repository)
    {
        return $this->parse($xmlData, $repository);
    }
    
    /**
     * @param LoggerHierarchy $repository
     */
    private function doConfigureDefault(LoggerHierarchy $repository)
    {
        return $this->doConfigureByString(self::DEFAULT_CONFIGURATION, $repository);
    }
    
    /**
     * @param string $xmlData
     */
    private function parse($xmlData, LoggerHierarchy $repository)
    {
        // Logger::resetConfiguration();
        $this->repository = $repository;

        $parser = xml_parser_create_ns();
    
        xml_set_object($parser, $this);
        xml_set_element_handler($parser, "tagOpen", "tagClose");
        
        $result = xml_parse($parser, $xmlData, true);
        if (!$result) {
            $errorCode = xml_get_error_code($parser);
            $errorStr = xml_error_string($errorCode);
            $errorLine = xml_get_current_line_number($parser);
            $this->repository->resetConfiguration();
        } else {
            xml_parser_free($parser);
        }
        return $result;
    }
    
    /**
     * @param mixed $parser
     * @param string $tag
     * @param array $attribs
     *
     * @todo In 'LOGGER' case find a better way to detect 'getLogger()' method
     */
    private function tagOpen($parser, $tag, $attribs)
    {
        switch ($tag) {
        
            case 'CONFIGURATION' :
            case self::XMLNS.':CONFIGURATION':
            
                if (isset($attribs['THRESHOLD'])) {
                
                    $this->repository->setThreshold(
                        LoggerOptionConverter::toLevel(
                            $this->subst($attribs['THRESHOLD']), 
                            $this->repository->getThreshold()
                        )
                    );
                }
                break;
                
            case 'APPENDER' :
            case self::XMLNS.':APPENDER':
            
                unset($this->appender);
                $this->appender = null;
                
                $name  = $this->subst(@$attribs['NAME']);
                $class = $this->subst(@$attribs['CLASS']);
                
                $this->appender = LoggerAppenderPool::getAppenderFromPool($name, $class);
                $this->state[] = self::APPENDER_STATE;
                break;
                
            case 'APPENDER_REF' :
            case 'APPENDER-REF' :
            case self::XMLNS.':APPENDER_REF':
            case self::XMLNS.':APPENDER-REF':
                if (isset($attribs['REF']) and !empty($attribs['REF'])) {
                    $appenderName = $this->subst($attribs['REF']);
                    
                    $appender = LoggerAppenderPool::getAppenderFromPool($appenderName);
                    if ($appender !== null) {
                        switch (end($this->state)) {
                            case self::LOGGER_STATE:
                            case self::ROOT_STATE:                
                                $this->logger->addAppender($appender);
                                break;
                        }
                    }
                } 
                break;
                
            case 'FILTER' :
            case self::XMLNS.':FILTER':
                unset($this->filter);
                $this->filter = null;

                $filterName = basename($this->subst(@$attribs['CLASS']));
                if (!empty($filterName)) {
                    $this->filter = new $filterName();
                    $this->state[] = self::FILTER_STATE;
                } 
                break;
                
            case 'LAYOUT':
            case self::XMLNS.':LAYOUT':
                $class = @$attribs['CLASS'];
                $this->layout = LoggerReflectionUtils::createObject($this->subst($class));
                $this->state[] = self::LAYOUT_STATE;
                break;
            
            case 'LOGGER':
            case self::XMLNS.':LOGGER':
                // $this->logger is assigned by reference.
                // Only '$this->logger=null;' destroys referenced object
                unset($this->logger);
                $this->logger = null;
                
                $loggerName = $this->subst(@$attribs['NAME']);
                if (!empty($loggerName)) {
                    $this->logger = $this->repository->getLogger($loggerName);
                    if ($this->logger !== null and isset($attribs['ADDITIVITY'])) {
                        $additivity = LoggerOptionConverter::toBoolean($this->subst($attribs['ADDITIVITY']), true);     
                        $this->logger->setAdditivity($additivity);
                    }
                } 
                $this->state[] = self::LOGGER_STATE;;
                break;
            
            case 'LEVEL':
            case self::XMLNS.':LEVEL':
            case 'PRIORITY':
            case self::XMLNS.':PRIORITY':
            
                if (!isset($attribs['VALUE'])) {
                    // LoggerDOMConfigurator::tagOpen() LEVEL value not set
                    break;
                }
                    
                if ($this->logger === null) { 
                    // LoggerDOMConfigurator::tagOpen() LEVEL. parent logger is null
                    break;
                }
        
                switch (end($this->state)) {
                    case self::ROOT_STATE:
                        $this->logger->setLevel(
                            LoggerOptionConverter::toLevel(
                                $this->subst($attribs['VALUE']), 
                                $this->logger->getLevel()
                            )
                        );
                        break;
                    case self::LOGGER_STATE:
                        $this->logger->setLevel(
                            LoggerOptionConverter::toLevel(
                                $this->subst($attribs['VALUE']), 
                                $this->logger->getLevel()
                            )
                        );
                        break;
                    default:
                        //LoggerLog::warn("LoggerDOMConfigurator::tagOpen() LEVEL state '{$this->state}' not allowed here");
                }
                break;
            
            case 'PARAM':
            case self::XMLNS.':PARAM':
                if (!isset($attribs['NAME'])) {
                    // LoggerDOMConfigurator::tagOpen() PARAM attribute 'name' not defined.
                    break;
                }
                if (!isset($attribs['VALUE'])) {
                    // LoggerDOMConfigurator::tagOpen() PARAM. attribute 'value' not defined.
                    break;
                }
                    
                switch (end($this->state)) {
                    case self::APPENDER_STATE:
                        if ($this->appender !== null) {
                            LoggerReflectionUtils::setter($this->appender, $this->subst($attribs['NAME']), $this->subst($attribs['VALUE']));
                        }
                        break;
                    case self::LAYOUT_STATE:
                        if ($this->layout !== null) {
                            LoggerReflectionUtils::setter($this->layout, $this->subst($attribs['NAME']), $this->subst($attribs['VALUE']));                
                        }
                        break;
                    case self::FILTER_STATE:
                        if ($this->filter !== null) {
                            LoggerReflectionUtils::setter($this->filter, $this->subst($attribs['NAME']), $this->subst($attribs['VALUE']));
                        }
                        break;
                    default:
                        //LoggerLog::warn("LoggerDOMConfigurator::tagOpen() PARAM state '{$this->state}' not allowed here");
                }
                break;
            
            case 'RENDERER':
            case self::XMLNS.':RENDERER':

                $renderedClass   = $this->subst(@$attribs['RENDEREDCLASS']);
                $renderingClass  = $this->subst(@$attribs['RENDERINGCLASS']);
        
                if (!empty($renderedClass) and !empty($renderingClass)) {
                    $renderer = LoggerReflectionUtils::createObject($renderingClass);
                    if ($renderer === null) {
                        // LoggerDOMConfigurator::tagOpen() RENDERER cannot instantiate '$renderingClass'
                    } else { 
                        $this->repository->setRenderer($renderedClass, $renderer);
                    }
                }
                break;
            
            case 'ROOT':
            case self::XMLNS.':ROOT':
                $this->logger = Logger::getRootLogger();
                $this->state[] = self::ROOT_STATE;
                break;
        }
    }


    /**
     * @param mixed $parser
     * @param string $tag
     */
    private function tagClose($parser, $tag)
    {
        switch ($tag) {
        
            case 'CONFIGURATION' : 
            case self::XMLNS.':CONFIGURATION':
                break;
                
            case 'APPENDER' :
            case self::XMLNS.':APPENDER':
                if ($this->appender !== null) {
                    if ($this->appender->requiresLayout() and $this->appender->getLayout() === null) {
                        $appenderName = $this->appender->getName();
                        $this->appender->setLayout(LoggerReflectionUtils::createObject('LoggerLayoutSimple'));
                    }                    
                    $this->appender->activateOptions();
                }        
                array_pop($this->state);        
                break;
                
            case 'FILTER' :
            case self::XMLNS.':FILTER':
                if ($this->filter !== null) {
                    $this->filter->activateOptions();
                    $this->appender->addFilter($this->filter);
                    $this->filter = null;
                }
                array_pop($this->state);        
                break;
                
            case 'LAYOUT':
            case self::XMLNS.':LAYOUT':
                if ($this->appender !== null and $this->layout !== null and $this->appender->requiresLayout()) {
                    $this->layout->activateOptions();
                    $this->appender->setLayout($this->layout);
                    $this->layout = null;
                }
                array_pop($this->state);
                break;
            
            case 'LOGGER':
            case self::XMLNS.':LOGGER':
                array_pop($this->state);
                break;
            
            case 'ROOT':
            case self::XMLNS.':ROOT':
                array_pop($this->state);
                break;
        }
    }
    
    private function subst($value)
    {
        return LoggerOptionConverter::substVars($value);
    }

}
