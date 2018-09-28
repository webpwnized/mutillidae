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
 * @author    Andrew van der Stock <vanderaj@owasp.org>
 * @author    Mike Boberski <boberski_michael@bah.com>
 * @copyright 2009-2010 The OWASP Foundation
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD license
 * @version   SVN: $Id$
 * @link      http://www.owasp.org/index.php/ESAPI
 */

/**
 * Use this ESAPI security control to assist with manipulating strings
 * in other ESAPI security controls.
 * 
 * The idea behind this interface is to define a set of helper
 * functions related to manipulating strings.
 *
 * @category  OWASP
 * @package   ESAPI
 * @author    Andrew van der Stock <vanderaj@owasp.org>
 * @author    Mike Boberski <boberski_michael@bah.com>
 * @copyright 2009-2010 The OWASP Foundation
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD license
 * @version   Release: @package_version@
 * @link      http://www.owasp.org/index.php/ESAPI
 */
class StringUtilities
{

    /**
     * Removes all unprintable characters from a string 
     * and replaces with a space for use in an HTTP header
     * 
     * @param string $input a string that may have unprintable characters
     * 
     * @return string the stripped header
     */
    public static function stripControls( $input ) 
    {
        if (empty($input)) {
            return '';
        }

        $i = str_split($input);

        $sb = '';
        foreach ( $i as $c ) {
            if ( $c > chr(32) && $c < chr(127) ) {
                $sb .= $c;
            } else {
                $sb .= ' ';
            }
        }

        return $sb;
    }


    /**
     * Union two character arrays.
     * 
     * @param string $c1 the first character array
     * @param string $c2 the second character array
     * 
     * @return array the union of the two character arrays
     */
    public static function union($c1, $c2) 
    {
        if (empty($c1) && empty($c2)) {
            return null;
        }

        return sort(array_unique(array_merge($c1, $c2)));
    }


    /**
     * Returns true if the character is contained in the provided StringBuffer.
     * 
     * @param string $haystack the string to search
     * @param string $c        the character to search for in the string
     * 
     * @return bool TRUE, if the character is found, false otherwise
     */
    public static function contains($haystack, $c) 
    {
        if ( empty($haystack) || empty($c) ) {
            return false;
        }

        return ( strpos($haystack, $c) !== false ) ? true : false;
    }
}