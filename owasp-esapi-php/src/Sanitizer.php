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
 * @author    jah <jah@jahboite.co.uk>
 * @author    Mike Boberski <boberski_michael@bah.com>
 * @copyright 2009-2010 The OWASP Foundation
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD license
 * @version   SVN: $Id$
 * @link      http://www.owasp.org/index.php/ESAPI
 */

/**
 * Use this ESAPI security control to wrap your sanitization functions.
 * 
 * The idea behind this interface is to define a set of functions that can
 * be used to attempt to sanitize data.
 *
 * @category  OWASP
 * @package   ESAPI
 * @author    jah <jah@jahboite.co.uk>
 * @author    Mike Boberski <boberski_michael@bah.com>
 * @copyright 2009-2010 The OWASP Foundation
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD license
 * @version   Release: @package_version@
 * @link      http://www.owasp.org/index.php/ESAPI
 */
interface Sanitizer
{
    /**
     * Returns valid, "safe" HTML.
     * 
     * This implementation uses HTMLPurifier {@link http://htmlpurifier.org}. 
     * 
     * @param string $context A descriptive name of the parameter that you are
     *                        validating (e.g. ProfilePage_Sig). This value is 
     *                        used by any logging or error handling that is done 
     *                        with respect to the value passed in.
     * @param string $input   The actual user input data to validate.
     *
     * @return string valid, "safe" HTML.
     */
    function getSanitizedHTML($context, $input);
    
    /**
     * Returns valid, "safe" email address.
     * 
     * This implementation uses a PHP filter 
     * {@link http://php.net/manual/en/filter.filters.sanitize.php}. 
     * 
     * @param string $context A descriptive name of the parameter that you are
     *                        validating (e.g. ProfilePage_Sig). This value is 
     *                        used by any logging or error handling that is done 
     *                        with respect to the value passed in.
     * @param string $input   The actual user input data to validate.
     *
     * @return string valid, "safe" email address.
     */
    function getSanitizedEmailAddress($context, $input);
    
    /**
     * Returns valid, "safe" URL.
     * 
     * This implementation uses a PHP filter 
     * {@link http://php.net/manual/en/filter.filters.sanitize.php}. 
     * 
     * @param string $context A descriptive name of the parameter that you are
     *                        validating (e.g. ProfilePage_Sig). This value is 
     *                        used by any logging or error handling that is done 
     *                        with respect to the value passed in.
     * @param string $input   The actual user input data to validate.
     *
     * @return string valid, "safe" URL.
     */
    function getSanitizedURL($context, $input);

    /**
     * Generically attempts to sanitize English language words based on the
     * provided guess by calculating and comparing metaphone values. 
     * 
     * @param string $context A descriptive name of the parameter that you are
     *                        validating (e.g. ProfilePage_Sig). This value is 
     *                        used by any logging or error handling that is done 
     *                        with respect to the value passed in.
     * @param string $input   The actual user input data to validate.
     *
     * @return string valid, "safe" word.
     */
    function getSanitizedWord($context, $input);    
}
