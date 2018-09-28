<?php
/**
 * OWASP Enterprise Security API (ESAPI)
 *
 * This file is part of the Open Web Application Security Project (OWASP)
 * Enterprise Security API (ESAPI) project.
 *
 * LICENSE: This source file is subject to the New BSD license.  You should read
 * and accept the LICENSE before you use, modify, and/or redistribute this
 * software.
 * 
 * PHP version 5.2
 *
 * @category  OWASP
 * @package   ESAPI_Reference_Validation
 * @author    Johannes B. Ullrich <jullrich@sans.edu>
 * @author    Mike Boberski <boberski_michael@bah.com>
 * @author    jah <jah@jahboite.co.uk>
 * @copyright 2009-2010 The OWASP Foundation
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD license
 * @version   SVN: $Id$
 * @link      http://www.owasp.org/index.php/ESAPI
 */

/**
 * Helper class.
 *
 * @category  OWASP
 * @package   ESAPI_Reference_Validation
 * @author    Johannes B. Ullrich <jullrich@sans.edu>
 * @author    jah <jah@jahboite.co.uk>
 * @author    Mike Boberski <boberski_michael@bah.com>
 * @copyright 2009-2010 The OWASP Foundation
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD license
 * @version   Release: @package_version@
 * @link      http://www.owasp.org/index.php/ESAPI
 */
class DateFormat
{
    private $_format = array();
    const TYPES = array('SMALL','MEDIUM','LONG','FULL');
    
    /**
     * Constructor.
     * 
     * @param string $format date format
     * @param string $type   date type
     * 
     * @return does not return a value.
     */
    function __construct($format=null, $type='MEDIUM') 
    {
        $this->setformat($format, $type);
    }
    
    /**
     * Helper function.
     * 
     * @param string $format date format
     * @param string $type   date type
     * 
     * @return does not return a value.
     */
    function setformat($format, $type='MEDIUM') 
    {
        
        if ( is_array($format)) {
            foreach ( self::TYPES as $t ) {
                if ( key_exists($t, $format)) {
                    $this->_format[$t] = $format[$t];        
                }
            }
        } else {
            if ( in_array($type, self::TYPES) ) {
                $this->_format[$type] = $format;
            } else {
                throw ValidationException("invalid date type " . $type);
            }                        
        }
    }
}

?>