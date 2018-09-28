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
 * @package   ESAPI
 * @author    Laura Bell <laura.d.bell@gmail.com>
 * @author    Mike Boberski <boberski_michael@bah.com>
 * @copyright 2009-2010 The OWASP Foundation
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD license
 * @version   SVN: $Id$
 * @link      http://www.owasp.org/index.php/ESAPI
 */

/**
 * Use this ESAPI security control to swap audit libraries in and out.
 * 
 * The idea behind this interface is to allow substitution of various 
 * logging packages, while providing a common interface to access them.
 *
 * @category  OWASP
 * @package   ESAPI
 * @author    Laura Bell <laura.d.bell@gmail.com>
 * @author    Mike Boberski <boberski_michael@bah.com>
 * @copyright 2009-2010 The OWASP Foundation
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD license
 * @version   Release: @package_version@
 * @link      http://www.owasp.org/index.php/ESAPI
 */
interface AuditorFactory
{

    /**
     * Gets the logger associated with the specified module name. The module
     * name is used by the logger to log which module is generating the log
     * events. The implementation of this method should return any
     * preexisting Logger associated with this module name, rather than
     * creating a new Logger.
     *
     * @param string $moduleName The name of the module requesting the logger.
     *
     * @return ESAPILogger The DefaultLogger associated with this module.
     */
    function getLogger($moduleName);

}
