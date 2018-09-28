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
 * @author    Jeff Williams <jeff.williams@aspectsecurity.com>
 * @author    Johannes B. Ullrich <jullrich@sans.edu>
 * @author    jah <jah@jahboite.co.uk>
 * @copyright 2009-2010 The OWASP Foundation
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD license
 * @version   SVN: $Id$
 * @link      http://www.owasp.org/index.php/ESAPI
 */


/**
 * DateValidationRule requires the BaseValidationRule.
 */
require_once dirname(__FILE__) . '/BaseValidationRule.php';


/**
 * DateValidationRule implementation of the ValidationRule interface.
 *
 * PHP version 5.2.9
 *
 * @category  OWASP
 * @package   ESAPI_Reference_Validation
 * @author    Jeff Williams <jeff.williams@aspectsecurity.com>
 * @author    Johannes B. Ullrich <jullrich@sans.edu>
 * @author    jah <jah@jahboite.co.uk>
 * @copyright 2009-2010 The OWASP Foundation
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD license
 * @version   Release: @package_version@
 * @link      http://www.owasp.org/index.php/ESAPI
 */
class DateValidationRule extends BaseValidationRule
{
    private $_format;


    /**
     * Constructor sets-up the validation rule with a descriptive name for this
     * validator, an optional Encoder instance (for canonicalization) and an
     * optional date format string. The date format string should be of the type
     * accepted by PHP's date() function.
     *
     * @param string  $typeName  A descriptive name for this validator.
     * @param Encoder $encoder   Encoder object providing canonicalize method.
     * @param string  $newFormat Date format string {@see date()}.
     *
     * @return null
     */
    public function __construct($typeName, $encoder = null, $newFormat = null)
    {
        parent::__construct($typeName, $encoder);

        if (! is_string($newFormat) || $newFormat == '') {
            $newFormat = 'Y-m-d';
        }
        $this->setDateFormat($newFormat);
    }


    /**
     * Sets the date format string which valid inputs must adhere to. The format
     * should be of the type accepted by PHP's date() function e.g. 'Y-m-d'.
     *
     * @param string $newFormat Date format string {@see date()}.
     *
     * @return null
     */
    public function setDateFormat($newFormat)
    {
        if (! is_string($newFormat) || $newFormat == '') {
            throw new RuntimeException(
                'setDateFormat requires a non-empty string DateFormat as '.
                'accepted by date().'
            );
        }
        $this->_format = $newFormat;
    }


    /**
     * Returns the canonicalized, valid input.
     * Throws ValidationException if the input is not valid or
     * IntrusionException if the input is an obvious attack.
     *
     * @param string $context A descriptive name of the parameter that you are
     *                        validating (e.g., LoginPage_UsernameField). This value
     *                        is used by any logging or error handling that is done
     *                        with respect to the value passed in.
     * @param string $input   The actual string user input data to validate.
     *
     * @return DateTime DateTime object parsed from canonicalized, valid input.
     *
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
                "{$context}: Input required",
                "Input required: context={$context}",
                $context
            );
        }

        // strict canonicalization
        $canonical = null;
        try
        {
            $canonical = $this->encoder->canonicalize($input, true);
        }
        catch (EncodingException $e)
        {
            throw new ValidationException(
                "{$context} -  Invalid input. Encoding problem detected.",
                'An EncodingException was thrown during canonicalization of '.
                'the input.',
                $context
            );
        }

        // try to create a DateTime object from the canonical input
        $date = false;
        if ((@strtotime($canonical)) !== false) {
            $date = date_create($canonical);
        }
        if ($date === false) {
            throw new ValidationException(
                "{$context} - Invalid date must follow the {$this->_format} format",
                "Invalid date - format={$this->_format}, input={$input}",
                $context
            );
        }

        // the DateTime object, formatted with $format, must equal the canonical
        // input
        $formatted = $date->format($this->_format);
        if ($formatted !== $canonical) {
            throw new ValidationException(
                "{$context} - Invalid date must follow the {$this->_format} format",
                "Invalid date - format={$this->_format}, input={$input}",
                $context
            );
        }

        // validation passed
        return $date;
    }


    /**
     * Returns a default DateTime object created by calling date_create without
     * supplying any parameters.
     *
     * @param string $context A descriptive name of the parameter that you are
     *                        validating (e.g., UserPage_DoB). This value is used by
     *                        any logging or error handling that is done with
     *                        respect to the value passed in.
     * @param string $input   The actual user input data to validate.
     *
     * @return DateTime DateTime object for the current date and time and default
     *                  Timezone.
     */
    public function sanitize($context, $input)
    {
        return date_create();
    }


}
