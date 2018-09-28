<?php
/**
 * OWASP Enterprise Security API (ESAPI)
 *
 * This file is part of the Open Web Application Security Project (OWASP)
 * Enterprise Security API (ESAPI) project.
 * 
 * PHP version 5.2
 *
 * LICENSE: This source file is subject to the New BSD license.  You should read
 * and accept the LICENSE before you use, modify, and/or redistribute this
 * software.
 *
 * @category  OWASP
 * @package   ESAPI_Reference
 * @author    Mike Boberski <boberski_michael@bah.com> 
 * @author    Linden Darling <linden.darling@jds.net.au>
 * @copyright 2009-2010 The OWASP Foundation
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD license
 * @version   SVN: $Id$
 * @link      http://www.owasp.org/index.php/ESAPI
 */

require_once  dirname(__FILE__).'/../Executor.php';

/**
 * Reference Implementation of the Executor interface.
 *
 * @category  OWASP
 * @package   ESAPI_Reference
 * @author    Mike Boberski <boberski_michael@bah.com> 
 * @author    Linden Darling <linden.darling@jds.net.au>
 * @copyright 2009-2010 The OWASP Foundation
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD license
 * @version   Release: @package_version@
 * @link      http://www.owasp.org/index.php/ESAPI
 */
class DefaultExecutor implements Executor
{
        
    // Logger
    private $_auditor = null;
    private $_ApplicationName = null;
    private $_LogEncodingRequired = null;
    private $_LogLevel = null;
    private $_LogFileName = null;
    private $_MaxLogFileSize = null;
    
    //SecurityConfiguration
    private $_config = null;
    
    /**
     * Executor constructor.
     * 
     * @return does not return a value.
     */
    function __construct()
    {
        $this->_auditor = ESAPI::getAuditor('Executor');
        $this->_config = ESAPI::getSecurityConfiguration();
    }

    /**
     * @inheritdoc
     */
    function executeSystemCommand($executable, $params)
    {
        $workdir = $this->_config->getWorkingDirectory();
        $logParams = false;
        return $this->executeSystemCommandLonghand(
            $executable, $params, $workdir, $logParams
        );
    }
     
     /**
     * @inheritdoc
     */
    function executeSystemCommandLonghand($executable, $params, $workdir, 
        $logParams
    ) {
        try {
            
            // executable must exist
            $resolved = $executable;
            
            if (substr(PHP_OS, 0, 3) == 'WIN') {
                $exploded = explode("%", $executable);
                $systemroot = getenv($exploded[1]);
                $resolved = $systemroot . $exploded[2];
            }
            
            if (!file_exists($resolved)) {
                throw new ExecutorException(
                    "Execution failure, No such ".
                    "executable: $executable"
                );
            }
            
            // executable must use canonical path
            if (strcmp($resolved, realpath($resolved)) != 0) {
                throw new ExecutorException(
                    "Execution failure, Attempt ".
                    "to invoke an executable using a non-absolute path: [".realpath($resolved)."] != [$executable]"
                );
            }            
                             
            // exact, absolute, canonical path to executable must be listed 
            //in ESAPI configuration 
            $approved = $this->_config->getAllowedExecutables();
            if (!in_array($executable, $approved)) {
                throw new ExecutorException(
                    "Execution failure, Attempt to invoke executable that ".
                    "is not listed as an approved executable in ESAPI ".
                    "configuration: ".$executable . " not listed in " . $approved
                );
            }            

            // escape any special characters in the parameters
            for ($i = 0; $i < count($params); $i++) {
                $params[$i] = escapeshellcmd($params[$i]);  
            }
           
            // working directory must exist
            $resolved_workdir = $workdir;
            if (substr(PHP_OS, 0, 3) == 'WIN') {
                if (substr_count($workdir, '%')>=2) {
                    //only explode on % if at least 2x % chars exist in string
                    $exploded = explode("%", $workdir);
                    $systemroot = getenv($exploded[1]);
                    $resolved_workdir = $systemroot . $exploded[2];
                }
            }
            if (!file_exists($resolved_workdir)) {
                throw new ExecutorException(
                    "Execution failure, No such".
                    " working directory for running executable: $workdir"
                );
            }
 
            // run the command
            $paramstr = "";
            foreach ($params as $param) {
                //note: will yield a paramstr with a leading whitespace
                $paramstr .= " ".$param;    
            }
            //note: no whitespace between $executable and $paramstr since 
            //$paramstr already has a leading whitespace
            $output = shell_exec($executable . $paramstr);    
            return $output;
        }
        catch ( ExecutorException $e ) {
            $this->_auditor->warning(Auditor::SECURITY, true, $e->getMessage());
            throw new ExecutorException($e->getMessage());
        }
    
    }    
    
}
?>