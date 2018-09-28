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
 * @package   ESAPI_Codecs
 * @author    Arnaud Labenne <arnaud.labenne@dotsafe.fr>
 * @author    Mike Boberski <boberski_michael@bah.com>
 * @copyright 2009-2010 The OWASP Foundation
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD license
 * @version   SVN: $Id$
 * @link      http://www.owasp.org/index.php/ESAPI
 */

require_once 'Codec.php';

/**
 * Reference implementation of the CSS codec.
 *
 * @category  OWASP
 * @package   ESAPI_Codecs
 * @author    Arnaud Labenne <arnaud.labenne@dotsafe.fr>
 * @author    Mike Boberski <boberski_michael@bah.com>
 * @copyright 2009-2010 The OWASP Foundation
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD license
 * @version   Release: @package_version@
 * @link      http://www.owasp.org/index.php/ESAPI
 */
class MySQLCodec extends Codec
{
    const MYSQL_ANSI = 0;
    const MYSQL_STD = 1;
    
    //To avoid performance loss in decodeCharacterMySQL
    const ORD_VALUE_0 = 48;
    const ORD_VALUE_B = 98;
    const ORD_VALUE_T = 116;
    const ORD_VALUE_N = 110;
    const ORD_VALUE_R = 114;
    const ORD_VALUE_Z = 90;
    const ORD_VALUE_DQUOTE = 34;
    const ORD_VALUE_PERCENT = 37;
    const ORD_VALUE_QUOTE = 39;
    const ORD_VALUE_BSLASH = 92;
    const ORD_VALUE_UNDERSCORE = 95;
    
    private $_mode;
    
    /**
     * Public Constructor 
     * 
     * @param int $mode Mode has to be one of {MYSQL_MODE|ANSI_MODE} to allow 
     *                  correct encoding
     * 
     * @return does not return a value.
     */
    function __construct($mode = self::MYSQL_STD)
    {
        parent::__construct();
        $this->_mode = $mode;
    }
    
    /**
     * {@inheritdoc}
     */
    public function encodeCharacter($immune, $c)
    {
        //detect encoding, special-handling for chr(172) and chr(128) to chr(159) 
        //which fail to be detected by mb_detect_encoding()
        $initialEncoding = $this->detectEncoding($c);
        
        // Normalize encoding to UTF-32
        $_4ByteUnencodedOutput = $this->normalizeEncoding($c);
        
        // Start with nothing; format it to match the encoding of the string passed 
        //as an argument.
        $encodedOutput = mb_convert_encoding("", $initialEncoding);
        
        // Grab the 4 byte character.
        $_4ByteCharacter = $this->forceToSingleCharacter($_4ByteUnencodedOutput);
        
        // Get the ordinal value of the character.
        list(, $ordinalValue) = unpack("N", $_4ByteCharacter);
        
        // check for immune characters
        foreach ($immune as $immuneCharacter) {
            // Convert to UTF-32 (4 byte characters, regardless of actual number of 
            //bytes in the character).
            $_4ByteImmuneCharacter = $this->normalizeEncoding($immuneCharacter);
            
            // Ensure it's a single 4 byte character (since $immune is an array of 
            //strings) by grabbing only the 1st multi-byte character.
            $_4ByteImmuneCharacter = $this->forceToSingleCharacter(
                $_4ByteImmuneCharacter
            );
            
            // If the character is immune then return it.
            if ($_4ByteCharacter === $_4ByteImmuneCharacter) {
                return $encodedOutput . chr($ordinalValue);
            }
        }
        
        // Check for alphanumeric characters
        $hex = $this->getHexForNonAlphanumeric($_4ByteCharacter);
        if ($hex === null) {
            return $encodedOutput . chr($ordinalValue);
        }
        
        switch ($this->_mode) {
        case self::MYSQL_ANSI:
            return $encodedOutput . $this->_encodeCharacterANSI($c);
        case self::MYSQL_STD:
            return $encodedOutput . $this->_encodeCharacterMySQL($c);
        }
        
        //Mode has an incorrect value
        return $encodedOutput . chr($ordinalValue);
    }
    
    /**
     * encodeCharacterANSI encodes for ANSI SQL. 
     * 
     * Only the apostrophe is encoded
     * 
     * @param string $c Character to encode
     * 
     * @return string '' if ', otherwise return c directly
     */
    private function _encodeCharacterANSI($c)
    {
        // Normalize encoding to UTF-32
        $_4ByteUnencodedOutput = $this->normalizeEncoding($c);
        
        // Grab the 4 byte character
        $_4ByteCharacter = $this->forceToSingleCharacter($_4ByteUnencodedOutput);
        
        // Get the ordinal value of the character.
        list(, $ordinalValue) = unpack("N", $_4ByteCharacter);
        
        //If the character is a quote
        if ($ordinalValue == 0x27) {
            return $c . $c;
        } else {
            return $c;
        }
    }
    
    /**
     * Encode a character suitable for MySQL
     * 
     * @param string $c Character to encode
     * 
     * @return string Encoded Character
     */
    private function _encodeCharacterMySQL($c)
    {
        // Normalize encoding to UTF-32
        $_4ByteUnencodedOutput = $this->normalizeEncoding($c);
        
        // Grab the 4 byte character
        $_4ByteCharacter = $this->forceToSingleCharacter($_4ByteUnencodedOutput);
        
        // Get the ordinal value of the character.
        list(, $ordinalValue) = unpack("N", $_4ByteCharacter);
        
        switch ($ordinalValue) {
        case 0x00:
            return "\\0";
        case 0x08:
            return "\\b";
        case 0x09:
            return "\\t";
        case 0x0a:
            return "\\n";
        case 0x0d:
            return "\\r";
        case 0x1a:
            return "\\Z";
        case 0x22:
            return "\\\"";
        case 0x25:
            return "\\%";
        case 0x27:
            return "\\'";
        case 0x5c:
            return "\\\\";
        case 0x5f:
            return "\\_";
        }
        
        return '\\' . $c;
    }
    
    /**
     * Returns the decoded version of the character starting at index, or
     * null if no decoding is possible.
     * 
     *   In ANSI_MODE '' decodes to '
     *   In MYSQL_MODE \x decodes to x (or a small list of specials)
     *   
     *   {@inheritdoc}
     */
    public function decodeCharacter($input)
    {
        // Assumption/prerequisite: $c is a UTF-32 encoded string
        $_4ByteEncodedInput = $input;
        
        if (mb_substr($_4ByteEncodedInput, 0, 1, "UTF-32") === null) {
            // 1st character is null, so return null
            // eat the 1st character off the string and return null
            $_4ByteEncodedInput = mb_substr(
                $input, 1, mb_strlen($_4ByteEncodedInput, "UTF-32"), "UTF-32"
            ); //todo: no point in doing this
            return array(
                'decodedCharacter' => null,
                'encodedString' => null
            );
        }
        
        switch ($this->_mode) {
        case self::MYSQL_ANSI:
            return $this->_decodeCharacterANSI($_4ByteEncodedInput);
        case self::MYSQL_STD:
            return $this->_decodeCharacterMySQL($_4ByteEncodedInput);
        }
        
        //Mode has an incorrect value 
        return array(
            'decodedCharacter' => null,
            'encodedString' => null
        );
    }
    
    /**
     * decodeCharacterANSI decodes the next character from ANSI SQL escaping
     *  
     * @param string $input A string containing characters you'd like decoded
     * 
     * @return string A single character, decoded
     */
    private function _decodeCharacterANSI($input)
    {
        $_4ByteEncodedInput = $input;
        
        // if this is not an encoded character, return null
        if (mb_substr($_4ByteEncodedInput, 0, 1, "UTF-32") != $this->normalizeEncoding("'")) {
            // 1st character is not part of encoding pattern, so return null
            return array(
                'decodedCharacter' => null,
                'encodedString' => null
            );
        }
        
        // 1st character is part of encoding pattern...
        $second = mb_substr($_4ByteEncodedInput, 1, 1, "UTF-32");
        
        return array(
            'decodedCharacter' => $second,
            'encodedString' => mb_substr($input, 0, 2, "UTF-32")
        );
    }
    
    /**
     * decodeCharacterMySQL decodes all the potential escaped characters that 
     * MySQL is prepared to escape
     * 
     * @param string $input A string you'd like to be decoded
     * 
     * @return string A single character from that string, decoded.
     */
    private function _decodeCharacterMySQL($input)
    {
        $_4ByteEncodedInput = $input;
        
        // if this is not an encoded character, return null
        if (mb_substr($_4ByteEncodedInput, 0, 1, "UTF-32") != $this->normalizeEncoding('\\')) {
            // 1st character is not part of encoding pattern, so return null
            return array(
                'decodedCharacter' => null,
                'encodedString' => null
            );
        }
        
        // 1st character is part of encoding pattern...
        $second = mb_substr($_4ByteEncodedInput, 1, 1, "UTF-32");
        list(, $ordinalValue) = unpack("N", $second);
        
        //if second character is special, so return the original value
        switch ($ordinalValue) {
        case self::ORD_VALUE_0:
            $second = $this->normalizeEncoding(chr(0x00));
            break;
        case self::ORD_VALUE_B:
            $second = $this->normalizeEncoding(chr(0x08));
            break;
        case self::ORD_VALUE_T:
            $second = $this->normalizeEncoding(chr(0x09));
            break;
        case self::ORD_VALUE_N:
            $second = $this->normalizeEncoding(chr(0x0a));
            break;
        case self::ORD_VALUE_R:
            $second = $this->normalizeEncoding(chr(0x0d));
            break;
        case self::ORD_VALUE_Z:
            $second = $this->normalizeEncoding(chr(0x1a));
            break;
        case self::ORD_VALUE_DQUOTE:
            $second = $this->normalizeEncoding(chr(0x22));
            break;
        case self::ORD_VALUE_PERCENT:
            $second = $this->normalizeEncoding(chr(0x25));
            break;
        case self::ORD_VALUE_QUOTE:
            $second = $this->normalizeEncoding(chr(0x27));
            break;
        case self::ORD_VALUE_BSLASH:
            $second = $this->normalizeEncoding(chr(0x5c));
            break;
        case self::ORD_VALUE_UNDERSCORE:
            $second = $this->normalizeEncoding(chr(0x5f));
            break;
        }
        
        return array(
            'decodedCharacter' => $second,
            'encodedString' => mb_substr($input, 0, 2, "UTF-32")
        );
    }
}