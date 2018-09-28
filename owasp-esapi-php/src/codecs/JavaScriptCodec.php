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
 * @author    Mike Boberski <boberski_michael@bah.com>
 * @copyright 2009-2010 The OWASP Foundation
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD license
 * @version   SVN: $Id$
 * @link      http://www.owasp.org/index.php/ESAPI
 */

require_once 'Codec.php';

/**
 * Reference implementation of the JavaScriptCodec codec.
 *
 * @category  OWASP
 * @package   ESAPI_Codecs
 * @author    Mike Boberski <boberski_michael@bah.com>
 * @copyright 2009-2010 The OWASP Foundation
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD license
 * @version   Release: @package_version@
 * @link      http://www.owasp.org/index.php/ESAPI
 */
class JavaScriptCodec extends Codec
{
    /**
     * Public Constructor 
     */
    function __construct()
    {
        parent::__construct();
    }
    
    /**
     * Returns backslash encoded numeric format. Does not use backslash 
     * character escapes such as, \" or \' as these may cause parsing problems. 
     * For example, if a javascript attribute, such as onmouseover, contains 
     * a \" that will close the entire attribute and allow an attacker to inject 
     * another script attribute.
     *
     * {@inheritdoc}
     */
    public function encodeCharacter($immune, $c)
    {
        //detect encoding, special-handling for chr(172) and chr(128) to 
        //chr(159) which fail to be detected by mb_detect_encoding()
        $initialEncoding = $this->detectEncoding($c);
        
        // Normalize encoding to UTF-32
        $_4ByteUnencodedOutput = $this->normalizeEncoding($c);
        
        // Start with nothing; format it to match the encoding of the string 
        //passed as an argument.
        $encodedOutput = mb_convert_encoding("", $initialEncoding);
        
        // Grab the 4 byte character.
        $_4ByteCharacter = $this->forceToSingleCharacter($_4ByteUnencodedOutput);
        
        // Get the ordinal value of the character.
        list(, $ordinalValue) = unpack("N", $_4ByteCharacter);
        
        // check for immune characters
        if ($this->containsCharacter($_4ByteCharacter, $immune)) {
            return $encodedOutput . chr($ordinalValue);
        }
        
        // Check for alphanumeric characters
        $hex = $this->getHexForNonAlphanumeric($_4ByteCharacter);
        if ($hex === null) {
            return $encodedOutput . chr($ordinalValue);
        }
        
        // Do not use these shortcuts as they can be used to break out of a context
        // if ( ch == 0x00 ) return "\\0";
        // if ( ch == 0x08 ) return "\\b";
        // if ( ch == 0x09 ) return "\\t";
        // if ( ch == 0x0a ) return "\\n";
        // if ( ch == 0x0b ) return "\\v";
        // if ( ch == 0x0c ) return "\\f";
        // if ( ch == 0x0d ) return "\\r";
        // if ( ch == 0x22 ) return "\\\"";
        // if ( ch == 0x27 ) return "\\'";
        // if ( ch == 0x5c ) return "\\\\";
        
        // encode up to 256 with \\xHH
        $pad = mb_substr("00", mb_strlen($hex));
        if ($ordinalValue < 256) {
            return "\\x" . $pad . strtoupper($hex);
        }
        
        // otherwise encode with \\uHHHH
        $pad = mb_substr("0000", mb_strlen($hex));
        return "\\u" . $pad . strtoupper($hex);
        
    }
    
    /**
     * Returns the decoded version of the character starting at index, or
     * null if no decoding is possible.
     * See http://www.planetpdf.com/codecuts/pdfs/tutorial/jsspec.pdf 
     * Formats all are legal both upper/lower case:
     *   \\a - special characters
     *   \\xHH
     *   \\uHHHH
     *   \\OOO (1, 2, or 3 digits)
     *   
     * {@inheritdoc}
    */
    public function decodeCharacter($input)
    {
        // Assumption/prerequisite: $c is a UTF-32 encoded string
        $_4ByteEncodedInput = $input;
        
        if (mb_substr($_4ByteEncodedInput, 0, 1, "UTF-32") === null) {
            // 1st character is null, so return null
            // eat the 1st character off the string and return null
            return array(
                'decodedCharacter' => null,
                'encodedString' => null
            );
        }
        
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
        
        // There is no second character, return null.
        if ($second == '') {
            return array(
                'decodedCharacter' => null,
                'encodedString' => null
            );
        }
        
        // \0 collides with the octal decoder and is non-standard
        // if ( second.charValue() == '0' ) {
        //    return Character.valueOf( (char)0x00 );
        if ($second == $this->normalizeEncoding('b')) {
            return array(
                'decodedCharacter' => $this->normalizeEncoding(chr(hexdec('8'))),
                'encodedString' => mb_substr($_4ByteEncodedInput, 0, 2, "UTF-32")
            );
        } else if ($second == $this->normalizeEncoding('t')) {
            return array(
                'decodedCharacter' => $this->normalizeEncoding(chr(hexdec('9'))),
                'encodedString' => mb_substr($_4ByteEncodedInput, 0, 2, "UTF-32")
            );
        } else if ($second == $this->normalizeEncoding('n')) {
            return array(
                'decodedCharacter' => $this->normalizeEncoding(chr(hexdec('a'))),
                'encodedString' => mb_substr($_4ByteEncodedInput, 0, 2, "UTF-32")
            );
        } else if ($second == $this->normalizeEncoding('v')) {
            return array(
                'decodedCharacter' => $this->normalizeEncoding(chr(hexdec('b'))),
                'encodedString' => mb_substr($_4ByteEncodedInput, 0, 2, "UTF-32")
            );
        } else if ($second == $this->normalizeEncoding('f')) {
            return array(
                'decodedCharacter' => $this->normalizeEncoding(chr(hexdec('c'))),
                'encodedString' => mb_substr($_4ByteEncodedInput, 0, 2, "UTF-32")
            );
        } else if ($second == $this->normalizeEncoding('r')) {
            return array(
                'decodedCharacter' => $this->normalizeEncoding(chr(hexdec('d'))),
                'encodedString' => mb_substr($_4ByteEncodedInput, 0, 2, "UTF-32")
            );
        } else if ($second == $this->normalizeEncoding('\"')) {
            return array(
                'decodedCharacter' => $this->normalizeEncoding(chr(hexdec('22'))),
                'encodedString' => mb_substr($_4ByteEncodedInput, 0, 2, "UTF-32")
            );
        } else if ($second == $this->normalizeEncoding('\'')) {
            return array(
                'decodedCharacter' => $this->normalizeEncoding(chr(hexdec('27'))),
                'encodedString' => mb_substr($_4ByteEncodedInput, 0, 2, "UTF-32")
            );
        } else if ($second == $this->normalizeEncoding('\\')) {
            return array(
                'decodedCharacter' => $this->normalizeEncoding(chr(hexdec('5c'))),
                'encodedString' => mb_substr($_4ByteEncodedInput, 0, 2, "UTF-32")
            );
        } else if (strtolower($second) == $this->normalizeEncoding('x')) {
            // look for \\xXX format
            // check for exactly two hex digits following
            $potentialHexString = $this->normalizeEncoding('');
            for ($i = 0; $i < 2; $i++) {
                $c = mb_substr($input, 2 + $i, 1, "UTF-32");
                if ($c != null)
                    $potentialHexString .= $c;
            }
            if (mb_strlen($potentialHexString, "UTF-32") == 2) {
                $charFromHex = $this->normalizeEncoding(
                    $this->_parseHex($potentialHexString)
                );
                return array(
                    'decodedCharacter' => $charFromHex,
                    'encodedString' => mb_substr($input, 0, 4, "UTF-32")
                );
            }
            return array(
                'decodedCharacter' => null,
                'encodedString' => null
            );
        } else if (strtolower($second) == $this->normalizeEncoding('u')) {
            // look for \\uXXXX format
            // Search for exactly 4 hex digits following
            $potentialHexString = $this->normalizeEncoding('');
            for ($i = 0; $i < 4; $i++) {
                $c = mb_substr($input, 2 + $i, 1, "UTF-32");
                if ($c != null)
                    $potentialHexString .= $c;
            }
            if (mb_strlen($potentialHexString, "UTF-32") == 4) {
                $charFromHex = $this->normalizeEncoding(
                    $this->_parseHex($potentialHexString)
                );
                return array(
                    'decodedCharacter' => $charFromHex,
                    'encodedString' => mb_substr($input, 0, 6, "UTF-32")
                );
            }
            return array(
                'decodedCharacter' => null,
                'encodedString' => null
            );
        } else if (preg_match('/[0-7]+/', $second) > 0) {
            // look for one, two, or three octal digits
            // get digit 1
            $digit1 = $second;
            $digits = $digit1;
            // get digit 2 if present
            $digit2 = mb_substr($_4ByteEncodedInput, 2, 1, "UTF-32");
            if (!preg_match('/[0-7]+/', $digit2)) {
                $digit2 = "";
            } else {
                $digits .= $digit2;
                // get digit 3 if present
                $digit3 = mb_substr($_4ByteEncodedInput, 3, 1, "UTF-32");
                if (!preg_match('/[0-7]+/', $digit3)) {
                    $digit3 = "";
                } else {
                    $digits = $digit1 . $digit2 . $digit3;
                }
            }
            return array(
                'decodedCharacter' => 
                    $this->normalizeEncoding(chr(octdec($digits))),
                'encodedString' 
                    => mb_substr($_4ByteEncodedInput, 0, 1, "UTF-32") . $digits
            );
        }
        
        // ignore the backslash and return the character
        return array(
            'decodedCharacter' => $second,
            'encodedString' => mb_substr($_4ByteEncodedInput, 0, 2, "UTF-32")
        );
        
    }
    
    /**
     * Utility function.
     * 
     * @param string $input string to parse
     * 
     * @return string hex value
     */
    private function _parseHex($input)
    {
        //todo: encoding should be UTF-32, so why detect it?
        $hexString   = mb_convert_encoding("", mb_detect_encoding($input)); 
        $inputLength = mb_strlen($input, "UTF-32");
        for ($i = 0; $i < $inputLength; $i++) {
            // Get the ordinal value of the character.
            list(, $ordinalValue) = unpack("N", mb_substr($input, $i, 1, "UTF-32"));
            
            // if character is a hex digit, add it and keep on going
            if (preg_match("/^[0-9a-fA-F]/", chr($ordinalValue))) {
                // hex digit found, add it and continue...
                $hexString .= mb_substr($input, $i, 1, "UTF-32");
            } else if (mb_substr($input, $i, 1, "UTF-32") == $this->normalizeEncoding(';')) {
                // if character is a semicolon, then eat it and quit
                //this parameter is not utilised by this method, consider removing
                $trailingSemicolon = $this->normalizeEncoding(';'); 
                break;
            } else {
                // otherwise just quit
                break;
            }
        }
        
        try {
            // trying to convert hexString to integer...
            
            $parsedInteger = (int) hexdec($hexString);
            if ($parsedInteger <= 0xFF) {
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
