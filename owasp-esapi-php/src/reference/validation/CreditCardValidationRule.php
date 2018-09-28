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
 * CreditCardValidationRule requires the BaseValidationRule and
 * StringValidationRule.
 */
require_once dirname(__FILE__) . '/BaseValidationRule.php';
require_once dirname(__FILE__) . '/StringValidationRule.php';


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
class CreditCardValidationRule extends BaseValidationRule
{
    private $_ccrule = null ;
    const CREDIT_CARD_VALIDATOR_KEY = 'CreditCard';


    /**
     * Constructor sets-up the validation rule with a descriptive name for this
     * validator, an optional Encoder instance (for canonicalization) and an
     * optional instance of a ValidationRule implementation for validating
     * Credit Card Numbers.
     *
     * @param string $typeName       descriptive name for this validator.
     * @param object $encoder        Encoder providing canonicalize method.
     * @param object $validationRule instance of a ValidationRule
     *                               implementation for validating Credit 
     *                               Card Numbers.
     *                                       
     * @return does not return a value.
     */
    public function __construct($typeName, $encoder = null, $validationRule = null)
    {
        parent::__construct($typeName, $encoder);

        if ($validationRule instanceof ValidationRule) {
            $this->ccrule = $validationRule;
        } else {
            $this->ccrule = $this->_getCCRule();
        }
    }


    /**
     * Returns an instance of StringValidationRule constructed with a regex
     * pattern for validating Credit Card Numbers obtained from the ESAPI
     * SecurityConfiguration.
     *
     * @return object object of type StringValidationRule.
     */
    private function _getCCRule()
    {
        global $ESAPI;
        $config = ESAPI::getSecurityConfiguration();
        $pattern = $config->getValidationPattern(self::CREDIT_CARD_VALIDATOR_KEY);
        $ccr = new StringValidationRule(
            'CreditCardValidator', 
            $this->encoder, 
            $pattern
        );
        $ccr->setMaximumLength(19);
        $ccr->setAllowNull(false);
        return $ccr;
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
     * @return string canonicalized, valid input.
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
        if ($input === null || $input == '') {
            if ($this->allowNull) {
                return null;
            }
            throw new ValidationException(
                "{$context}: Input Credit Card Number required",
                "Input Credit Card Number required: context={$context}",
                $context
            );
        }

        // Validate the input.
        $canonical = $this->ccrule->getValid($context, $input);

        // Detect errors in validated, canonicalized credit card number.
        $digitsOnly = preg_replace('/[^0-9]/', '', $canonical);
        $charEnc = mb_detect_encoding($digitsOnly);
        $len = mb_strlen($digitsOnly, $charEnc);
        $odd = ! $len % 2;
        $sum = 0;
        for ($i = $len - 1; $i >= 0; $i--) {
            $n = mb_substr($digitsOnly, $i, 1, $charEnc);
            $odd = ! $odd;
            if ($odd) {
                $sum += $n;
            } else {
                $x = $n * 2;
                $sum += $x > 9 ? $x - 9 : $x;
            }
        }
        if (($sum % 10) != 0) {
            throw new ValidationException(
                "{$context}: Invalid Credit Card Number",
                "Input Credit Card Number contains errors - check digit failure:".
                " context={$context}",
                $context
            );
        }

        return $canonical;
    }


    /**
     * Returns the supplied input string after removing any non-numeric
     * characters.
     *
     * @param string $context A descriptive name of the parameter that you are
     *                        validating (e.g., LoginPage_UsernameField). This 
     *                        value is used by any logging or error handling that 
     *                        is done with respect to the value passed in.
     * @param string $input   The actual user input data to validate.
     *
     * @return string string of zero or more numeric characters from $input.
     */
    public function sanitize($context, $input)
    {
        return $this->whitelist($input, Encoder::CHAR_DIGITS);
    }


}
