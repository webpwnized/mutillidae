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
 * StringValidationRule requires the BaseValidationRule.
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
class StringValidationRule extends BaseValidationRule
{
    protected $whitelistPatterns;
    protected $blacklistPatterns;
    protected $minLength = 0;
    protected $maxLength = PHP_INT_MAX;


    /**
     * Constructor sets-up the validation rule with a descriptive name for this
     * validator, an optional Encoder instance (for canonicalization) and an
     * optional whitelist regex pattern.
     *
     * @param string $typeName         descriptive name for this validator.
     * @param object $encoder          providing canonicalize method.
     * @param string $whiteListPattern whitelist regex.
     * 
     * @return does not return a value
     */
    public function __construct($typeName, $encoder = null, 
        $whiteListPattern = null
    ) {
        parent::__construct($typeName, $encoder);

        $this->whitelistPatterns = array();
        $this->blacklistPatterns = array();

        if (is_string($whiteListPattern)) {
            $this->addWhitelistPattern($whiteListPattern);
        } else if ($whiteListPattern !== null) {
            throw new InvalidArgumentException(
                'Validation misconfiguration - constructor expected a string'.
                ' $whiteListPattern'
            );
        }
    }


    /**
     * Adds a whitelist regex pattern to the array of whitelist patterns.
     * Inputs will be validated against each pattern.
     *
     * @param string $pattern non-empty string whitelist regex pattern.
     * 
     * @return does not return a value
     */
    public function addWhitelistPattern($pattern)
    {
        if (! is_string($pattern)) {
            throw new InvalidArgumentException(
                'Validation misconfiguration - addWhitelistPattern expected a '.
                'string $pattern'
            );
        }
        if ($pattern == '') {
            ESAPI::getLogger()->warning(
                ESAPILogger::SECURITY, false,
                'addWhitelistPattern received $pattern as an empty string.'
            );
        }
        array_push($this->whitelistPatterns, $pattern);
    }


    /**
     * Adds a blacklist regex pattern to the array of blacklist patterns.
     * Inputs will be validated against each pattern.
     *
     * @param string $pattern non-empty string blacklist regex pattern.
     * 
     * @return does not return a value
     */
    public function addBlacklistPattern($pattern)
    {
        if (! is_string($pattern)) {
            throw new InvalidArgumentException(
                'Validation misconfiguration - addBlacklistPattern expected '.
                'string $pattern'
            );
        }
        
        if ($pattern == '') {
            ESAPI::getLogger()->warning(
                ESAPILogger::SECURITY, false,
                'addBlacklistPattern received $pattern as an empty string.'
            );
        }
        
        array_push($this->blacklistPatterns, $pattern);
        
    }


    /**
     * Sets the minimum length of the input after canonicalization, below which
     * the input is deemed invalid.
     *
     * @param int $length minimum length of the canonicalized Input below which
     *                    it is deemed invalid.
     *                    
     * @return does not return a value
     */
    public function setMinimumLength($length)
    {
        if (! is_numeric($length)) {
            throw new InvalidArgumentException(
                'Validation misconfiguration - setMinimumLength expected '.
                'numeric $length'
            );
        }
        $this->minLength = (int) $length;
    }


    /**
     * Sets the maximum length of the input after canonicalization, above which
     * the input is deemed invalid.
     *
     * @param int $length maximum length of the canonicalized Input above which
     *                    it is deemed invalid.
     *                    
     * @return does not return a value
     */
    public function setMaximumLength($length)
    {
        if (! is_numeric($length)) {
            throw new InvalidArgumentException(
                'Validation misconfiguration - setMaximumLength expected '.
                'numeric $length'
            );
        }
        $this->maxLength = (int) $length;
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
        if ($this->minLength > $this->maxLength) {
            throw new RuntimeException(
                'Validation misconfiguration - $minLength should not be greater '.
                'than $maxLength!'
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
        } catch (EncodingException $e)
        {
            throw new ValidationException(
                $context . ': Invalid input. Encoding problem detected.',
                'An EncodingException was thrown during canonicalization '.
                'of the input.',
                $context
            );
        }

        // check length
        $charEnc = mb_detect_encoding($canonical);
        $length = mb_strlen($canonical, $charEnc);
        if ($length < $this->minLength) {
            throw new ValidationException(
                $context . ': Invalid input. Input was shorter than the '.
                'Minimum length of ' . $this->minLength . ' characters.',
                'Length of Input was less than the minimum length of ' . 
                $this->minLength,
                $context
            );
        }
        if ($length > $this->maxLength) {
            throw new ValidationException(
                $context . ': Invalid input. Input was longer than the '.
                'Maximum length of ' . $this->maxLength . ' characters.',
                'Length of Input was more than the maximum length of ' . 
                $this->maxLength,
                $context
            );
        }

        // check whitelist
        foreach ($this->whitelistPatterns as $pattern) {
            if (! preg_match("/{$pattern}/", $canonical)) {
                throw new ValidationException(
                    $context . ': Invalid input. Please conform to the regex ' . 
                    $pattern,
                    $context . ': Invalid input. Input does not conform to the'.
                    ' whitelist regex ' . $pattern,
                    $context
                );
            }
        }

        // check blacklist
        foreach ($this->blacklistPatterns as $pattern) {
            if (preg_match("/{$pattern}/", $canonical)) {
                throw new ValidationException(
                    $context . ': Invalid input. Dangerous input matching ' . 
                    $pattern,
                    $context . ': Invalid input. Input matches the blacklist '.
                    'regex ' . $pattern,
                    $context
                );
            }
        }

        return $canonical;
    }


    /**
     * Returns the supplied input string after removing any non-alphanumeric
     * characters.
     *
     * @param string $context A descriptive name of the parameter that you are
     *                        validating (e.g., LoginPage_UsernameField). This 
     *                        value is used by any logging or error handling that 
     *                        is done with respect to the value passed in.
     * @param string $input   The actual user input data to validate.
     *
     * @return string of zero or more alphanumeric characters from $input.
     */
    public function sanitize($context, $input)
    {
        return $this->whitelist($input, Encoder::CHAR_ALPHANUMERICS);
    }
}
