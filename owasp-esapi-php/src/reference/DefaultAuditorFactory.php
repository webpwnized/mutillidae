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
 * @author    Jeff Williams <jeff.williams@aspectsecurity.com>
 * @author    Andrew van der Stock <vanderaj@owasp.org>
 * @author    Laura Bell <laura.d.bell@gmail.com>
 * @author    jah <jah@jahboite.co.uk>
 * @copyright 2009-2010 The OWASP Foundation
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD license
 * @version   SVN: $Id$
 * @link      http://www.owasp.org/index.php/ESAPI
 */


/**
 * 
 */
require_once dirname(__FILE__).'/../AuditorFactory.php';
require_once dirname(__FILE__).'/DefaultAuditor.php';


/**
 * Reference Implementation of the DefaultAuditorFactory interface.
 *
 * @category  OWASP
 * @package   ESAPI_Reference
 * @author    Andrew van der Stock <vanderaj@owasp.org>
 * @author    Laura Bell <laura.d.bell@gmail.com>
 * @author    jah <jah@jahboite.co.uk>
 * @author    Mike Boberski <boberski_michael@bah.com>
 * @copyright 2009-2010 The OWASP Foundation
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD license
 * @version   Release: @package_version@
 * @link      http://www.owasp.org/index.php/ESAPI
 */
class DefaultAuditorFactory implements AuditorFactory
{

    private $_loggerMap = array();


    /**
     * DefaultAuditorFactory constructor.
     * 
     * @return does not return a value.
     */
    function __construct()
    {
        // NoOp
    }


    /**
     * {@inheritdoc}
     */
    public function getLogger($moduleName) 
    {

        // If a logger for this module already exists, we return the same one,
        // otherwise we create a new one.
        if (   array_key_exists($moduleName, $this->_loggerMap)
            && $this->_loggerMap[$moduleName] instanceof DefaultAuditor
        ) {
            return $this->_loggerMap[$moduleName];
        } else {
            $moduleLogger = new DefaultAuditor($moduleName);
            $this->_loggerMap[$moduleName] = $moduleLogger;
            return $moduleLogger;
        }
    }
}
