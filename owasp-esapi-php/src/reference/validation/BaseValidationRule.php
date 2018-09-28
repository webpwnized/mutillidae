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
 * @package   ESAPI_Reference_Validation
 * @author    Johannes B. Ullrich <jullrich@sans.edu>
 * @author    jah <jah@jahboite.co.uk>
 * @author    Mike Boberski <boberski_michael@bah.com>
 * @copyright 2009-2010 The OWASP Foundation
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD license
 * @version   SVN: $Id$
 * @link      http://www.owasp.org/index.php/ESAPI
 */

/**
 * BaseValidationRule requires the ValidationRule Interface and DefaultEncoder
 * for canonicalization.
 */
require_once dirname(__FILE__) . '/../../ValidationRule.php';
require_once dirname(__FILE__) . '/../DefaultEncoder.php';

/**
 * Reference Implementation of the ValidationRule interface.
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
abstract class BaseValidationRule implements ValidationRule
{
    protected $typeName  = null;
    protected $encoder   = null;
    protected $allowNull = false;


    /**
     * Stores an instance of an Encoder implementation (e.g. DefaultEncoder) to
     * be used for canonicalization and a name for the type of Input to be
     * validated (e.g. 'Date' or 'CreditCardNumber').
     *
     * @param string  $typeName type name of the input to be validated.
     * @param Encoder $encoder  instance of an Encoder implementation.
     * 
     * @return does not return a value.
     */
    protected function __construct($typeName, $encoder)
    {
        global $ESAPI;
        if ($encoder instanceof Encoder) {
            $this->encoder = $encoder;
        } else {
            $this->encoder = new DefaultEncoder();
        }
        $this->typeName = $typeName;
    }


    /**
     * Sets the boolean allowNull property which, if set true, will allow empty
     * inputs to validate as true.
     *
     * @param bool $flag set to true if empty inputs should validate as true.
     * 
     * @return does not return a value.
     */
    public function setAllowNull($flag)
    {
        if ($flag === true) {
            $this->allowNull = true;
        } else {
            $this->allowNull = false;
        }
    }


    /**
     * Gets the boolean allowNull property which, if set true, will allow empty
     * inputs to validate as true.
     *
     * @return bool returns true if empty inputs should validate as true, false
     *              otherwise.
     */
    public function getAllowNull()
    {
        return $this->allowNull;
    }


    /**
     * Sets a descriptive name for the validator e.g. CreditCardNumber.
     * If $typeName is empty or not a string then a default value will be set.
     *
     * @param string $typeName name describing the validator.
     * 
     * @return does not return a value.
     */
    public function setTypeName($typeName)
    {
        if (! is_string($typeName) || $typeName == '') {
            $typeName = 'GenericValidator';
        }
        $this->typeName = $typeName;
    }


    /**
     * Gets the descriptive name for the validator.
     *
     * @return string name describing the validator.
     */
    public function getTypeName()
    {
        return $this->typeName;
    }


    /**
     * Sets an instance of an encoder class which should provide a
     * canonicalize method.
     * TODO should ensure that a canonicalize method is available or should
     * only allow instances of Encoder implementations...
     *
     * @param Encoder $encoder object which provides a canonicalize method.
     * 
     * @return does not return a value.
     * @throws InvalidArgumentException if encoder is missing a canonicalize method
     */
    final public function setEncoder($encoder)
    {
        if (   ! is_object($encoder)
            || ! method_exists($encoder, 'canonicalize')
        ) {
            throw new InvalidArgumentException(
                'expected $encoder to be an object providing a canonicalize method'
            );
        }
        $this->encoder = $encoder;
    }


    /**
     * Asserts that the supplied $input is valid after canonicalization. Invalid
     * Inputs will cause a descriptive ValidationException to be thrown. Inputs
     * that are obviously an attack will cause an IntrusionException.
     *
     * @param string $context A descriptive name of the parameter that you are
     *                        validating (e.g., LoginPage_UsernameField). This 
     *                        value is used by any logging or error handling that 
     *                        is done with respect to the value passed in.
     * @param string $input   The actual user input data to validate.
     * 
     * @return does not return a value.
     */
    public function assertValid($context, $input)
    {
        $this->getValid($context, $input);
    }


    /**
     * Attempts to return valid canonicalized input.  If a ValidationException
     * is thrown, this method will return sanitized input which may or may not
     * have any similarity to the original input.
     *
     * @param string $context A descriptive name of the parameter that you are
     *                        validating (e.g., LoginPage_UsernameField). This 
     *                        value is used by any logging or error handling that 
     *                        is done with respect to the value passed in.
     * @param string $input   The actual user input data to validate.
     *
     * @return string valid, canonicalized input or sanitized input or a default 
     *                value.
     */
    public function getSafe($context, $input)
    {
        $safe = null;
        try
        {
            $safe = $this->getValid($context, $input);
        }
        catch (ValidationException$e )
        {
            $safe = $this->sanitize($context, $input);
        }
        return $safe;
    }


    /**
     * Returns boolean true if the input is valid, false otherwise.
     *
     * @param string $context A descriptive name of the parameter that you are
     *                        validating (e.g., LoginPage_UsernameField). This 
     *                        value is used by any logging or error handling that 
     *                        is done with respect to the value passed in.
     * @param string $input   The actual user input data to validate.
     *
     * @return bool true if the input is valid, false otherwise.
     */
    public function isValid($context, $input)
    {
        try
        {
            $this->getValid($context, $input);
            return true;
        }
        catch (Exception $e)
        {
            return false;
        }
    }


    /**
     * Returns the supplied input string after removing any characters not
     * present in the supplied whitelist.
     *
     * @param string $input     string input to be filtered.
     * @param string $whitelist array or string of whitelist characters.
     *
     * @return string returns characters from $input that are present in $whitelist.
     */
    public function whitelist($input, $whitelist)
    {
        // Sanity check
        if (! is_string($input) || $input == '') {
            $input = '';
        }
        if (is_string($whitelist)) {
            $charEnc = Codec::detectEncoding($whitelist);
            $limit = mb_strlen($whitelist, $charEnc);
            $ary = array();
            for ($i = 0; $i < $limit; $i++) {
                $ary[] = mb_substr($whitelist, $i, 1, $charEnc);
            }
            $whitelist = $ary;
        }

        $filtered = '';
        $initialCharEnc = Codec::detectEncoding($input);
        $_4ByteCharacterString = Codec::normalizeEncoding($input);
        $limit = mb_strlen($_4ByteCharacterString, 'UTF-32');
        for ($i = 0; $i < $limit; $i++) {
            $c = mb_substr($_4ByteCharacterString, $i, 1, 'UTF-32');
            if (Codec::containsCharacter($c, $whitelist)) {
                $filtered .= $c;
            }
        }
        if ($filtered != '') {
            $filtered = mb_convert_encoding($filtered, $initialCharEnc, 'UTF-32');
        }
        if (! is_string($filtered)) {
            $filtered = '';
        }

        return $filtered;
    }

}
