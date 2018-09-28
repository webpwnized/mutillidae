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
 * @author    Linden Darling <Linden.Darling@jds.net.au>
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
 * @author    Linden Darling <Linden.Darling@jds.net.au>
 * @author    Mike Boberski <boberski_michael@bah.com>
 * @copyright 2009-2010 The OWASP Foundation
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD license
 * @version   Release: @package_version@
 * @link      http://www.owasp.org/index.php/ESAPI
 */
class CSSCodec extends Codec
{
    /**
     * Public Constructor 
     */
    function __construct()
    {
        parent::__construct();
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
        
        // CSS 2.1 section 4.1.3: "It is undefined in CSS 2.1 what happens if a
        // style sheet does contain a character with Unicode codepoint zero."
        if ($ordinalValue === 0) {
            throw new Exception(
              "IllegalArgumentException - Chracter value zero is not valid in CSS"
            );
        }
        
        // check for immune characters
        if ($this->containsCharacter($_4ByteCharacter, $immune)) {
            // character is immune, therefore return character...
            return $encodedOutput . chr($ordinalValue);
        }
        
        // check for alphanumeric characters
        $hex = $this->getHexForNonAlphanumeric($_4ByteCharacter);
        if ($hex === null) {
            //character is alphanumric, therefore return the character...
            return $encodedOutput . chr($ordinalValue);
        }
        
        return "\\" . $hex . " ";
    }
    
    /**
     * {@inheritdoc}
     * 
     * Returns the decoded version of the character starting at index, or null if 
     * no decoding is possible.  This implementation does not support \\### octal 
     * encoding nor special character encoding such as \\&, \\-, etc.
     */
    public function decodeCharacter($input)
    {
        if (mb_substr($input, 0, 1, "UTF-32") === null) {
            // 1st character is null, so return null
            // eat the 1st character off the string and return null
            //todo: is this mb_substr neccessary
            $input = mb_substr($input, 1, mb_strlen($input, "UTF-32"), "UTF-32"); 
            return array(
                'decodedCharacter' => null,
                'encodedString' => null
            );
        }
        
        // if this is not an encoded character, return null
        if (mb_substr($input, 0, 1, "UTF-32") != $this->normalizeEncoding("\\")) {
            // 1st character is not part of encoding pattern, so return null
            return array(
                'decodedCharacter' => null,
                'encodedString' => null
            );
        }
        
        // 1st character is part of encoding pattern...
        
        // look for \HHH format
        // Search for up to 6 hex digits following until a space
        $potentialHexString = $this->normalizeEncoding('');
        $hexDigitCount      = 0;
        $limit              = min(6, mb_strlen($input, 'UTF-32') - 1);
        for ($i = 0; $i < $limit; $i++) {
            $_4ByteCharacter = mb_substr($input, 1 + $i, 1, "UTF-32");
            if ($this->isHexDigit($_4ByteCharacter)) {
                $potentialHexString .= $_4ByteCharacter;
                $hexDigitCount++;
            } else {
                break;
            }
        }
        if ($hexDigitCount) {
            $charFromHex = $this->normalizeEncoding(
                $this->_parseHex($potentialHexString)
            );
            if ($hexDigitCount < 6 
                && mb_substr($input, 1 + $hexDigitCount, 1, "UTF-32") != $this->normalizeEncoding(' ') 
            ) {
                // no terminating space, yet less than 6 hex digits in 
                //encoding = malformed encoding
                //TODO: throw an exception for malformed entity?
                return array(
                    'decodedCharacter' => $charFromHex,
                    'encodedString' => mb_substr(
                        $input, 0, 1 + $hexDigitCount, 
                        "UTF-32"
                    )
                );
            } elseif ($hexDigitCount < 6) {
                return array(
                    'decodedCharacter' => $charFromHex,
                    'encodedString' => mb_substr(
                        $input, 0, 1 + $hexDigitCount + 1, "UTF-32"
                    )
                );
            } else {
                return array(
                    'decodedCharacter' => $charFromHex,
                    'encodedString' => mb_substr(
                        $input, 0, 1 + $hexDigitCount, "UTF-32"
                    )
                );
            }
        } elseif (mb_substr($input, 1, 1, "UTF-32") == $this->normalizeEncoding("\n")
        //FIXME: perhaps add the following logic to all ESAPI implementations so 
        //they handle escaped new lines correctly?
            ) {
            // in the case of escape character followed by a newline, the encoding 
            //should be ignored note: ESAPI4JAVA does not specifically handle this 
            //situation (it would be handled but throw a malformed entity exception)
            return array(
                'decodedCharacter' => '',
                'encodedString' => mb_substr($input, 0, 2, "UTF-32")
            );
        } else {
            // zero hex digits after start of encoding pattern...
            //TODO: throw an exception for malformed entity?
            return array(
                'decodedCharacter' => null,
                'encodedString' => mb_substr($input, 0, 1, "UTF-32")
            );
        }
        
        return array(
            'decodedCharacter' => null,
            'encodedString' => null
        );
    }
    
    /**
     * Parse a hex encoded entity (special purposes for CSSCodec).
     * 
     * @param string $input Hex encoded input (such as 437ae;)
     * 
     * @return array Returns an array containing two objects: 'decodedCharacter' => 
     *               null if input is null, the character of input after decoding 
     *               'encodedString' => the string that was decoded or found to be 
     *               malformed
     */
    private function _parseHex($input)
    {
        //todo: encoding should be UTF-32, so why detect it?
        $hexString   = mb_convert_encoding("", mb_detect_encoding($input)); 
        $inputLength = mb_strlen($input, "UTF-32");
        for ($i = 0; $i < $inputLength; $i++) {
            // Get the ordinal value of the character.
            $_4ByteCharacter = mb_substr($input, $i, 1, "UTF-32");
            
            // if character is a hex digit, add it and keep on going
            if ($this->isHexDigit($_4ByteCharacter)) {
                // hex digit found, add it and continue...
                $hexString .= $_4ByteCharacter;
            } else { // otherwise just quit
                break;
            }
        }
        try {
            // trying to convert hexString to integer...
            
            $parsedInteger = (int) hexdec($hexString);
            if ($parsedInteger == 0) {
                // codepoint of zero not recognised in CSS, therefore return null
                return null;
            } else if ($parsedInteger <= 0xFF) {
                $parsedCharacter = chr($parsedInteger);
            } else {
                $parsedCharacter = mb_convert_encoding(
                    '&#' . $parsedInteger . ';', 'UTF-8', 'HTML-ENTITIES'
                );
            }
            return $parsedCharacter;
        }
        catch (Exception $e) {
            //TODO: throw an exception for malformed entity?
            return null;
        }
    }
}