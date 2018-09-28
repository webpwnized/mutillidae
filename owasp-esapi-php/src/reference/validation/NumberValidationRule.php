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
 * NumberValidationRule requires the BaseValidationRule.
 */
require_once dirname(__FILE__) . '/BaseValidationRule.php';


/**
 * Reference extension of the BaseValidationRule class.
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
class NumberValidationRule extends BaseValidationRule
{
    private $_minValue;
    private $_maxValue;

    
    /**
     * Constructor sets-up the validation rule with a descriptive name for this
     * validator, an optional Encoder instance (for canonicalization) and
     * optional minimum and maximum bounds for valid numbers.
     * 
     * @param string $typeName descriptive name for this validator.
     * @param object $encoder  providing canonicalize method.
     * @param int    $minValue or float minimum valid number.
     * @param int    $maxValue or float maximum valid number.
     * 
     * @return does not return a value.
     */
    public function __construct($typeName, $encoder, $minValue = null, 
        $maxValue = null
    ) {
        parent::__construct($typeName, $encoder);
        
        if ($minValue === null || ! is_numeric($minValue)) {
            $this->_minValue = null;
        } else {
            $this->_minValue = (double) $minValue;
        }
        
        if ($maxValue === null || ! is_numeric($maxValue)) {
            $this->_maxValue = null;
        } else {
            $this->_maxValue = (double) $maxValue;
        }
    }
    
    
    /**
     * Returns the canonicalized, valid input.
     * Throws ValidationException if the input is not valid or
     * IntrusionException if the input is an obvious attack.
     *
     * @param string $context A descriptive name of the parameter that you are
     *                        validating (e.g., LoginPage_UsernameField). This 
     *                        value is used by any logging or error handling that
     *                        is done with respect to the value passed in.
     * @param string $input   The actual string user input data to validate.
     *
     * @return float float parsed from canonicalized, valid input.
     * @throws ValidationException, IntrusionException
     */
    public function getValid($context, $input)
    {
        // Some sanity checks first
        if (! is_string($context)) {
            $context = 'NoContextSupplied'; // TODO Invalid Arg Exception?
        }
        
        if (! is_string($input) && $input !== null) {
            throw new ValidationException(
                "{$context}: Input required",
                "Input was not a string or NULL: context={$context}",
                $context
            );
        }
        
        if (   $this->_minValue !== null
            && $this->_maxValue !== null
            && $this->_minValue > $this->_maxValue
        ) {
            throw new RuntimeException(
                'Validation misconfiguration - $minValue should not be greater'.
                ' than $maxValue!'
            );
        }
        
        if ($input === null || $input == '') {
            if ($this->allowNull) {
                return null;
            }
            
            throw new ValidationException(
                "{$context}: Input required",
                "Input required: context={$context}",
                $context
            );
        }

        // strict canonicalization
        $canonical = null;
        try {
            $canonical = $this->encoder->canonicalize($input, true);
        } catch (EncodingException $e) {
            throw new ValidationException(
                $context . ': Invalid input. Encoding problem detected.',
                'An EncodingException was thrown during canonicalization of '.
                'the input.',
                $context
            );
        }

        // validate min and max
        try {
            $d = $canonical;
            if (! is_numeric($d)) {
                throw new ValidationException(
                    'Invalid number input: context=' . $context,
                    'Invalid number input: Input is not numeric: ' . $input,
                    $context
                );
            }
            
            $d = (double) $d;
            if (is_infinite($d)) {
                throw new ValidationException(
                    'Invalid number input: context=' . $context,
                    'Invalid double input is infinite: context=' . $context . 
                    ', input=' . $input,
                    $context
                );
            }
            
            if (is_nan($d)) {
                throw new ValidationException(
                    'Invalid number input: context=' . $context,
                    'Invalid double input is not a number: context=' . $context . 
                    ', input=' . $input,
                    $context
                );
            }
            
            if ($this->_minValue !== null && $d < $this->_minValue) {
                throw new ValidationException(
                    'Invalid number input must not be less than ' . $this->_minValue,
                    'Invalid number input must not be less than ' . 
                    $this->_minValue . ': context=' . $context . ', input=' . $input,
                    $context
                );
            }
            
            if ($this->_maxValue !== null && $d > $this->_maxValue) {
                throw new ValidationException(
                    'Invalid number input must not be greater than ' . 
                    $this->_maxValue,
                    'Invalid number input must not be greater than ' . 
                    $this->_maxValue . ': context=' . $context . ', input=' . $input,
                    $context
                );
            }
            
            return $d;
            
        } catch (NumberFormatException $e) {
            throw new ValidationException(
                $context . ': Invalid number input',
                'Invalid number input format: Caught NumberFormatException: ' . 
                $e->getMessage() . 'context=' . $context . ', input=' . $input,
                $context
            );
        }
    }


    /**
     * Returns a default safe number - in this case (double) zero.
     * TODO filter non-numeric chars 0123456789+-e., ?
     *
     * @param string $context A descriptive name of the parameter that you are
     *                        validating (e.g., LoginPage_UsernameField). This 
     *                        value is used by any logging or error handling that 
     *                        is done with respect to the value passed in.
     * @param int    $input   The actual user input data to validate.
     *
     * @return double (double) zero - a dafault safe number.
     */
    public function sanitize($context, $input)
    {
        return (double) 0;
    }


}
