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
 * @author    Johannes B. Ullrich <jullrich@sans.edu>
 * @author    Mike Boberski <boberski_michael@bah.com>
 * @author    jah <jah@jahboite.co.uk>
 * @copyright 2009-2010 The OWASP Foundation
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD license
 * @version   SVN: $Id$
 * @link      http://www.owasp.org/index.php/ESAPI
 */

/**
 * Implementations require ValidationException and IntrusionException.
 */
require_once dirname(__FILE__).'/errors/IntrusionException.php';
require_once dirname(__FILE__).'/errors/ValidationException.php';

/**
 * Use this ESAPI security control to wrap data validation functions. 
 * 
 * The idea behind this interface is to define a set of functions that
 * perform a more complete set of checks than frameworks for example
 * otherwise typically do, or make available for developers to use, such
 * as checking for multiple encodings before validating.

 *
 * @category  OWASP
 * @package   ESAPI
 * @author    Andrew van der Stock <vanderaj@owasp.org>
 * @author    Johannes B. Ullrich <jullrich@sans.edu>
 * @author    Mike Boberski <boberski_michael@bah.com>
 * @author    jah <jah@jahboite.co.uk>
 * @copyright 2009-2010 The OWASP Foundation
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD license
 * @version   Release: @package_version@
 * @link      http://www.owasp.org/index.php/ESAPI
 */
interface Validator
{
    /**
     * Returns true if input is valid according to the specified type after
     * canonicalization. The type parameter must be the name of a defined type
     * in the ESAPI configuration or a valid regular expression pattern.
     *
     * @param string $context   A descriptive name of the parameter that you are
     *                          validating (e.g. LoginPage_UsernameField). This 
     *                          value is used by any logging or error handling 
     *                          that is done with respect to the value passed in.
     * @param string $input     The actual user input data to validate.
     * @param string $type      The regular expression name that maps to the actual 
     *                          regular expression from "ESAPI.xml" or an actual 
     *                          regular expression.
     * @param int    $maxLength The maximum post-canonicalized String length allowed.
     * @param bool   $allowNull If allowNull is true then an input that is NULL or an
     *                          empty string will be legal. If allowNull is false 
     *                          then NULL or an empty String will throw a 
     *                          ValidationException.
     *
     * @return bool TRUE if the input is valid, FALSE otherwise.
     */
    public function isValidInput($context, $input, $type, $maxLength, $allowNull);

    /**
     * Returns true if the canonicalized input is a valid date according to the
     * specified date format string, or false otherwise.
     *
     * @param string $context   A descriptive name of the parameter that you are
     *                          validating (e.g. ProfilePage_DoB). This value is used
     *                          by any logging or error handling that is done with 
     *                          respect to the value passed in.
     * @param string $input     The actual user input data to validate.
     * @param string $format    Required formatting of date inputted {@see date}.
     * @param bool   $allowNull If allowNull is true then an input that is NULL or 
     *                          an empty string will be legal. If allowNull is 
     *                          false then NULL or an empty String will throw a 
     *                          ValidationException.
     *
     * @return bool TRUE if the input is valid, FALSE otherwise.
     */
    public function isValidDate($context, $input, $format, $allowNull);

    /**
     * Returns true if the canonicalized input is valid, "safe" HTML.
     * 
     * Implementors should reference the OWASP AntiSamy project for ideas on how
     * to do HTML validation in a whitelist way, as this is an extremely
     * difficult problem. It is recommended that PHP implementations make use of
     * HTMLPurifier {@link http://htmlpurifier.org}.
     * 
     * @param string $context   A descriptive name of the parameter that you are
     *                          validating (e.g. ProfilePage_Sig). This value is 
     *                          used by any logging or error handling that is done 
     *                          with respect to the value passed in.
     * @param string $input     The actual user input data to validate.
     * @param int    $maxLength The maximum post-canonicalized String length 
     *                          allowed.
     * @param bool   $allowNull If allowNull is true then an input that is NULL or 
     *                          an empty string will be legal. If allowNull is false
     *                          then NULL or an empty String will throw a 
     *                          ValidationException.
     * 
     * @return bool TRUE if the input is valid, FALSE otherwise.
     */
    public function isValidHTML($context, $input, $maxLength, $allowNull);
    
     
    /**
     * Returns true if the canonicalized input is a valid Credit Card Number.
     * 
     * @param string $context   A descriptive name of the parameter that you are
     *                          validating (e.g. PurchasePage_CCNum). This value 
     *                          is used by any logging or error handling that is 
     *                          done with respect to the value passed in.
     * @param string $input     The actual user input data to validate.
     * @param bool   $allowNull If allowNull is true then an input that is NULL or 
     *                          an empty string will be legal. If allowNull is 
     *                          false then NULL or an empty String will throw a 
     *                          ValidationException.
     *
     * @return bool TRUE if the input is valid, FALSE otherwise.
     */
    public function isValidCreditCard($context, $input, $allowNull);
    
    /**
     * Returns true if the canonicalized input is a valid directory path.
     * 
     * @param string $context   A descriptive name of the parameter that you are
     *                          validating (e.g. IncludeFile). This value is used 
     *                          by any logging or error handling that is done with 
     *                          respect to the value passed in.
     * @param string $input     The actual user input data to validate.
     * @param bool   $allowNull If allowNull is true then an input that is NULL or 
     *                          an empty string will be legal. If allowNull is 
     *                          false then NULL or an empty String will throw a 
     *                          ValidationException.
     *
     * @return bool TRUE if the input is valid, FALSE otherwise.
     */
    public function isValidDirectoryPath($context, $input, $allowNull);
    
    /**
     * Returns true if the canonicalized input is a valid, real number within
     * the specified range minValue to maxValue.
     * 
     * @param string $context   A descriptive name of the parameter that you are
     *                          validating (e.g. PurchasePage_Quantity). This value 
     *                          is used by any logging or error handling that is done
     *                          with respect to the value passed in.
     * @param string $input     The actual user input data to validate.
     * @param int    $minValue  The numeric lowest legal value for input.
     * @param int    $maxValue  The numeric highest legal value for input.
     * @param bool   $allowNull If allowNull is true then an input that is NULL or 
     *                          an empty string will be legal. If allowNull is 
     *                          false then NULL or an empty String will throw a 
     *                          ValidationException.
     * 
     * @return bool TRUE if the input is valid, FALSE otherwise.
     */
    public function isValidNumber($context, $input, $minValue, $maxValue, 
        $allowNull
    );
   
    
    /**
     * Returns true if the canonicalized input is a valid integer within the
     * specified range minValue to maxValue.
     * 
     * @param string $context   A descriptive name of the parameter that you are
     *                          validating (e.g. PurchasePage_Quantity). This value 
     *                          is used by any logging or error handling that is 
     *                          done with respect to the value passed in.
     * @param string $input     The actual user input data to validate.
     * @param int    $minValue  The numeric lowest legal value for input.
     * @param int    $maxValue  The numeric highest legal value for input.
     * @param bool   $allowNull If allowNull is true then an input that is NULL or 
     *                          an empty string will be legal. If allowNull is 
     *                          false then NULL or an empty String will throw a 
     *                          ValidationException.
     * 
     * @return bool TRUE if the input is valid, FALSE otherwise.
     */
    public function isValidInteger($context, $input, $minValue, $maxValue, 
        $allowNull
    );
    
   
    /**
     * Returns true if the canonicalized input is a valid double within the
     * specified range minValue to maxValue.
     * 
     * @param string $context   A descriptive name of the parameter that you are
     *                          validating (e.g. PurchasePage_Quantity). This value 
     *                          is used by any logging or error handling that is 
     *                          done with respect to the value passed in.
     * @param string $input     The actual user input data to validate.
     * @param int    $minValue  The numeric lowest legal value for input.
     * @param int    $maxValue  The numeric highest legal value for input.
     * @param bool   $allowNull If allowNull is true then an input that is NULL or 
     *                          an empty string will be legal. If allowNull is false 
     *                          then NULL or an empty String will throw a 
     *                          ValidationException.
     * 
     * @return bool TRUE if the input is valid, FALSE otherwise.
     */
    public function isValidDouble($context, $input, $minValue, $maxValue, 
        $allowNull
    );
 
    /**
     * Returns true if the canonicalized input exactly matches a list item.
     * 
     * @param string $context A descriptive name of the parameter that you are
     *                        validating (e.g. Contact_Recipient). This value 
     *                        is used by any logging or error handling that 
     *                        is done with respect to the value passed in.
     * @param string $input   The value to search for in the supplied list.
     * @param array  $list    The list to search for the supplied input.
     * 
     * @return bool TRUE if the input is valid, FALSE otherwise.
     */
    public function isValidListItem($context, $input, $list);
    
    /**
     * Returns true if the canonicalized input contains no more than the number
     * of valid printable ASCII characters specified.
     * 
     * @param string $context   A descriptive name of the parameter that you are
     *                          validating (e.g. ASCIIArt_Submission). This value 
     *                          is used by any logging or error handling that is 
     *                          done with respect to the value passed in.
     * @param string $input     The actual user input data to validate.
     * @param int    $maxLength The maximum number of canonicalized ascii characters
     *                          allowed in a legal input.
     * @param bool   $allowNull If allowNull is true then an input that is NULL or an
     *                          empty string will be legal. If allowNull is false 
     *                          then NULL or an empty String will throw a 
     *                          ValidationException.
     * 
     * @return bool TRUE if the input is valid, FALSE otherwise.
     */
    public function isValidPrintable($context, $input, $maxLength, $allowNull);
    
    /**
     * Returns true if input is a valid redirect location.
     * 
     * @param string $context   A descriptive name of the parameter that you are
     *                          validating (e.g. ASCIIArt_Submission). This value 
     *                          is used by any logging or error handling that is 
     *                          done with respect to the value passed in.
     * @param string $input     The actual user input data to validate.
     * @param bool   $allowNull If allowNull is true then an input that is NULL or an
     *                          empty string will be legal. If allowNull is false 
     *                          then NULL or an empty String will throw a 
     *                          ValidationException.
     * 
     * @return bool TRUE if the input is valid, FALSE otherwise.
     */    
    public function isValidRedirectLocation($context, $input, $allowNull);
 
}
