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
 * @author    jah <jah@jahboite.co.uk>
 * @author    Mike Boberski <boberski_michael@bah.com>
 * @copyright 2009-2010 The OWASP Foundation
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD license
 * @version   SVN: $Id$
 * @link      http://www.owasp.org/index.php/ESAPI
 */

/**
 * DefaultSanitizer requires the Sanitizer Interface and the various
 * ValidationRule implementations.
 */
require_once dirname(__FILE__) . '/CodecDebug.php';
require_once dirname(__FILE__) . '/../Encoder.php';

/**
 * The Codec interface defines a set of methods for encoding and decoding
 * application level encoding schemes, such as HTML entity encoding and percent
 * encoding (aka URL encoding). Codecs are used in output encoding and
 * canonicalization.  The design of these codecs allows for
 * character-by-character decoding, which is necessary to detect double-encoding
 * and the use of multiple encoding schemes, both of which are techniques used
 * by attackers to bypass validation and bury encoded attacks in data.
 *
 * @category  OWASP
 * @package   ESAPI_Codecs
 * @author    Linden Darling <Linden.Darling@jds.net.au>
 * @author    jah <jah@jahboite.co.uk>
 * @author    Mike Boberski <boberski_michael@bah.com>
 * @copyright 2009-2010 The OWASP Foundation
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD license
 * @version   Release: @package_version@
 * @link      http://www.owasp.org/index.php/ESAPI
 */
abstract class Codec
{
    /**
     * An map where the keys are ordinal values of non-alphanumeric single-byte
     * characters and the values are hexadecimal equivalents as strings.
     */
    private static $_hex = Array();
    
    
    /**
     * Populates the $hex map of non-alphanumeric single-byte characters.
     */
    public function __construct()
    {
        for ($i = 0; $i < 256; $i++) {
            if (($i >= 48 && $i <= 57) 
                || ($i >= 65 && $i <= 90) 
                || ($i >= 97 && $i <= 122)
            ) {
                self::$_hex[$i] = null;
            } else {
                self::$_hex[$i] = self::toHex($i);
            }
        }
    }
    
    /**
     * Encode a String with a Codec.
     *
     * @param string $immune immune characters
     * @param string $input  the String to encode.
     *
     * @return string the encoded string.
     */
    public function encode($immune, $input)
    {
        // debug
        CodecDebug::getInstance()->addUnencodedString(
            self::normalizeEncoding($input)
        );
        
        $encoding      = self::detectEncoding($input);
        $mbstrlen      = mb_strlen($input, $encoding);
        $encodedString = mb_convert_encoding("", $encoding);
        for ($i = 0; $i < $mbstrlen; $i++) {
            $c = mb_substr($input, $i, 1, $encoding);
            $encodedString .= $this->encodeCharacter($immune, $c);
        }
        
        // debug
        CodecDebug::getInstance()->output($encodedString);
        
        return $encodedString;
    }
    
    
    /**
     * Encode a Character with a Codec.
     *
     * @param string $immune immune characters
     * @param string $c      the Character to encode.
     *
     * @return string the encoded Character.
     */
    public function encodeCharacter($immune, $c)
    {
        // Normalize string to UTF-32
        $_4ByteString = self::normalizeEncoding($c);
        
        $initialEncoding = self::detectEncoding($c);
        
        // Start with nothing; format it to match the encoding of the string passed
        // as an argument.
        $encodedOutput = mb_convert_encoding("", $initialEncoding);
        
        // Grab the 4 byte character.
        $_4ByteCharacter = self::forceToSingleCharacter($_4ByteString);
        
        // Get the ordinal value of the character.
        list(, $ordinalValue) = unpack("N", $_4ByteCharacter);
        
        return $encodedOutput . chr($ordinalValue);
    }
    
    /**
     * Decode a String that was encoded using the encode method in this Class
     *
     * @param string $input the String to decode
     *
     * @return string returns the decoded string, otherwise null
     */
    function decode($input)
    {
        
        // Normalize string to UTF-32
        $_4ByteString = self::normalizeEncoding($input);
        
        // debug
        CodecDebug::getInstance()->addEncodedString($_4ByteString);
        
        // Start with an empty string.
        $decodedString           = '';
        $targetCharacterEncoding = 'ASCII';
        
        //logic to iterate through the string's characters, while(input has 
        //characters remaining){} feed whole sequence into decoder, which then 
        //determines the first decoded character from the input and "pushes back" 
        //the encodedPortion of seuquence and the resultant decodedCharacter to here
        while (mb_strlen($_4ByteString, "UTF-32") > 0) {
            // get the first decodedCharacter, allowing decodeCharacter to eat 
            //away at the string
            
            //decodeCharacter() returns an array containing 'decodedCharacter' and 
            //'encodedString' so as to provide PushbackString-(from-ESAPI-JAVA)-like
            //behaviour
            $decodeResult = $this->decodeCharacter($_4ByteString); 
            
            //note: decodedCharacter should be UTF-32 encoded already
            $decodedCharacter = $decodeResult['decodedCharacter']; 
            
            $encodedString = $decodeResult['encodedString'];
            
            if ($decodedCharacter !== null) {
                // Append the decoded character to the output string and remove
                // the sequence of characters that formed an entity or numeric
                // encoding of that character from the start of the input string.
                if ($decodedCharacter != '') {
                    $resultOfAppend = $this->_appendCharacterToOuput(
                        $decodedCharacter, 
                        $decodedString, 
                        $targetCharacterEncoding
                    );
                    
                    if ($resultOfAppend != true) {
                        // Decoded character has an Invalid codepoint so remove
                        // the first character from the encoded string
                        // $_4ByteString and append it to the decoded string.
                        $charToAppend   = mb_substr($_4ByteString, 0, 1, 'UTF-32');
                        $resultOfAppend = $this->_appendCharacterToOuput(
                            $charToAppend, 
                            $decodedString, 
                            $targetCharacterEncoding
                        );
                        if ($resultOfAppend != true) {
                            // We can do two things here, throw EncodingException
                            // or ignore the dodgy character.  This situation is
                            // an exceptional one and shouldn't happen often...
                            throw new EncodingException(
                                'Error encountered whilst decoding Input.', 
                                'A sequence of characters was recognised as using '.
                                'a valid encoding scheme, but the character it '.
                                'encodes is not a valid Unicode CodePoint. '.
                                'The first character in the sequence is also not'.
                                'a valid Unicode CodePoint so decoding was aborted'
                            );
                        }
                        // remove the first character from the input string.
                        $encStringLen = mb_strlen($_4ByteString, 'UTF-32');
                        $_4ByteString = mb_substr(
                            $_4ByteString, 1, $encStringLen, 'UTF-32'
                        );
                        continue;
                    }
                }
                
                // remove the encodedString portion off the start of the input 
                // string.
                $entityLen    = mb_strlen($encodedString, 'UTF-32');
                $encStringLen = mb_strlen($_4ByteString, 'UTF-32');
                $_4ByteString = mb_substr(
                    $_4ByteString, $entityLen, $encStringLen, 'UTF-32'
                );
            } else {
                // decodedCharacter is null, so add the single, unencoded
                // character to the decodedString and remove the 1st character
                // from the start of the input string.
                $charToAppend   = mb_substr($_4ByteString, 0, 1, 'UTF-32');
                $resultOfAppend = $this->_appendCharacterToOuput(
                    $charToAppend, 
                    $decodedString, 
                    $targetCharacterEncoding
                );
                if ($resultOfAppend !== true) {
                    // The first character in the remaining string of input
                    // characters is not a Valid Unicode CodePoint.  We could
                    // throw EncodingException here, but instead we'll forget
                    // about it and log a warning.
                    ESAPI::getLogger('Codec')->warn(
                        DefaultLogger::SECURITY, false, 
                        'Input contained a character with an invalid Unicode '.
                        'CodePoint. We destroyed it!'
                    );
                }
                
                // eat the single, unencoded character portion off the start of the 
                // UTF-32 converted input string
                $encStringLen = mb_strlen($_4ByteString, 'UTF-32');
                $_4ByteString = mb_substr($_4ByteString, 1, $encStringLen, 'UTF-32');
            }
        }
        
        // debug
        CodecDebug::getInstance()->output($decodedString);
        
        return $decodedString;
    }
    
    
    /**
     * Helper method which handles appending a UTF-32 character to the output
     * string of decode methods such that the output string does not contain
     * mixed character encodings. The method adjusts the character encoding of
     * the output string so that the character to append can exist in the set
     * of characters allowed in a given character encoding. Usually this means
     * converting the output string and character to UTF-8.
     *
     * @param string &$character_UTF32 String character to append (UTF-32).
     * @param string &$targetString    String target.
     * @param string &$targetCharEnc   String target character encoding name.
     *
     * @return bool returns true if the character was successfully appended to the 
     *              target false otherwise.
     */
    private function _appendCharacterToOuput(&$character_UTF32, &$targetString, 
        &$targetCharEnc
    ) {
        list(, $ordinalValue) = unpack('N', $character_UTF32);
        
        if ($ordinalValue > 0x110000) {
            return false; // Invalid code point.
        }
        
        if ($ordinalValue >= 0x00 && $ordinalValue <= 0x7F) {
            // An ASCII character can be appended to a string of any character
            // encoding
            $targetString .= mb_convert_encoding(
                $character_UTF32, 
                'ASCII', 
                "UTF-32"
            );
        } else if ($ordinalValue <= 0x10FFFF) {
            // convert the decoded character to UTF-8
            $character_UTF8 = mb_convert_encoding(
                $character_UTF32, 
                'UTF-8', 
                'UTF-32'
            );
            
            // convert decodedString to UTF-8 if necessary
            if ($targetString !== '' && $targetCharEnc != 'UTF-8') {
                $targetString = mb_convert_encoding(
                    $targetString, 
                    'UTF-8', 
                    $targetCharEnc
                );
            }
            
            // now append the character to the string
            $targetString .= $character_UTF8;
            
            // see if decodedString can exist in
            // targetCharacterEncoding and if so, convert back to
            // it. Otherwise the target character encoding is
            // changed to 'UTF-8'
            if ($targetCharEnc != 'UTF-8' 
                && $targetCharEnc 
                === mb_detect_encoding($targetString, $targetCharEnc, true)
            ) {
                // we can convert back to target encoding
                $targetString = mb_convert_encoding(
                    $targetString, 
                    $targetCharEnc, 
                    'UTF-8'
                );
            } else {
                // decoded String now contains characters that are
                // UTF-8
                $targetCharEnc = 'UTF-8';
            }
        }
        
        return true;
    }
    
    /**
     * Returns the ordinal value as a hex string of any character that is not a
     * single-byte alphanumeric. The character should be supplied as a string in
     * the UTF-32 character encoding.
     * If the character is an alphanumeric character with ordinal value below
     * 255 then this method will return null.
     *
     * @param string $c 4 byte character character.
     * 
     * @return string hexadecimal ordinal value of non-alphanumeric characters
     *                or null otherwise.
     */
    public static function getHexForNonAlphanumeric($c)
    {
        // Assumption/prerequisite: $c is a UTF-32 encoded string
        $_4ByteString = $c;
        
        // Grab the 4 byte character.
        $_4ByteCharacter = self::forceToSingleCharacter($_4ByteString);
        
        // Get the ordinal value of the character.
        list(, $ordinalValue) = unpack("N", $_4ByteCharacter);
        
        if ($ordinalValue <= 255) {
            return self::$_hex[$ordinalValue];
        }
        return self::toHex($ordinalValue);
    }
    
    
    /**
     * Return the hex value of a character as a string without leading zeroes.
     *
     * @param string $c character to convert
     * 
     * @return int returns hex value
     */
    public static function toHex($c)
    {
        // Assumption/prerequisite: $c is the ordinal value of the character 
        // (i.e. an integer)
        return dechex($c);
    }
    
    
    /**
     * Utility to search a char[] for a specific char.
     *
     * @param string $c     character to search for
     * @param array  $array character array to search in
     * 
     * @return string returns specific character
     */
    public static function containsCharacter($c, $array)
    {
        // Assumption/prerequisite: $c is a UTF-32 encoded single character
        $_4ByteCharacter = $c;
        
        // Get the ordinal value of the character.
        list(, $ordinalValue) = unpack("N", $_4ByteCharacter);
        
        foreach ($array as $arrayCharacter) {
            // Convert to UTF-32 (4 byte characters, regardless of actual number 
            // of bytes in the character).
            $_4ByteArrayCharacter = self::normalizeEncoding($arrayCharacter);
            
            // Ensure it's a single 4 byte character (since $array is an array of 
            // strings) by grabbing only the 1st multi-byte character.
            $_4ByteArrayCharacter = self::forceToSingleCharacter(
                $_4ByteArrayCharacter
            );
            
            // If the character is contained in the array then return it.
            if ($_4ByteCharacter === $_4ByteArrayCharacter) {
                return true;
            }
        }
        
        return false;
    }
    
    
    /**
     * Utility to detect a (potentially multibyte) string's encoding with 
     * extra logic to deal with single characters that mb_detect_encoding() fails 
     * upon.
     *
     * @param string $string string to examine
     * 
     * @return string returns detected encoding
     */
    public static function detectEncoding($string)
    {
        // detect encoding, special-handling for chr(172) and chr(128) to 
        //chr(159) which fail to be detected by mb_detect_encoding()
        $is_single_byte = false;
        try {
            $bytes = unpack('C*', $string);
            if (is_array($bytes) && sizeof($bytes, 0) == 1) {
                $is_single_byte = true;
            }
        }
        catch (Exception $e) {
            // unreach?
            ESAPI::getLogger('Codec')->warning(
                DefaultLogger::SECURITY, false, 
                'Codec::detectEncoding threw an exception whilst attempting'.
                ' to unpack an input string', 
                $e
            );
        }
        
        if ($is_single_byte === false) {
            // NoOp
        } else if ((ord($string) == 172) 
            || (ord($string) >= 128 && ord($string) <= 159)
        ) {
             // although these chars are beyond ASCII range, if encoding is 
             // forced to ISO-8859-1 they will all encode to &#x31;
             return 'ASCII'; //
        } else if (ord($string) >= 160 && ord($string) <= 255) {
            return 'ISO-8859-1';
        }
        
        // Strict encoding detection with fallback to non-strict detection.
        if (mb_detect_encoding($string, 'UTF-32', true)) {
            return 'UTF-32';
        } else if (mb_detect_encoding($string, 'UTF-16', true)) {
            return 'UTF-16';
        } else if (mb_detect_encoding($string, 'UTF-8', true)) {
            return 'UTF-8';
        } else if (mb_detect_encoding($string, 'ISO-8859-1', true)) {
            // To try an catch strings containing mixed encoding, search
            // the string for chars of ordinal in the range 128 to 159 and
            // 172 and don't return ISO-8859-1 if present.
            $limit = mb_strlen($string, 'ISO-8859-1');
            for ($i = 0; $i < $limit; $i++) {
                $char = mb_substr($string, $i, 1, 'ISO-8859-1');
                if ( (ord($char) == 172) 
                    || (ord($char) >= 128 && ord($char) <= 159)
                ) {
                    return 'UTF-8';
                }
            }
            return 'ISO-8859-1';
        } else if (mb_detect_encoding($string, 'ASCII', true)) {
            return 'ASCII';
        } else {
            return mb_detect_encoding($string);
        }
    }
    
    
    /**
     * Utility to normalize a string's encoding to UTF-32.
     *
     * @param string $string string to normalize
     * 
     * @return string normalized string
     */
    public static function normalizeEncoding($string)
    {
        // Convert to UTF-32 (4 byte characters, regardless of actual number of 
        //bytes in the character).
        $initialEncoding = self::detectEncoding($string);
        
        $encoded = mb_convert_encoding($string, "UTF-32", $initialEncoding);
        
        return $encoded;
    }
    
    
    /**
     * Utility to get first (potentially multibyte) character from a (potentially 
     * multicharacter) multibyte string.
     *
     * @param string $string string to convert
     * 
     * @return string converted string
     */
    public static function forceToSingleCharacter($string)
    {
        // Grab first character from UTF-32 encoded string
        return mb_substr($string, 0, 1, "UTF-32");
    }
    
    
    /**
     * Utility method to determine if a single character string is a hex digit
     *
     * @param string $c Single character string that is potentially a hex digit
     *
     * @return bool True indicates that the single character string is a hex 
     *              digit
     */
    function isHexDigit($c)
    {
        // Assumption/prerequisite: $c is a UTF-32 encoded single character
        $_4ByteCharacter = $c;
        
        // Get the ordinal value of the character.
        list(, $ordinalValue) = unpack("N", $_4ByteCharacter);
        
        // if character is a hex digit, return true
        if (preg_match("/^[0-9a-fA-F]/", chr($ordinalValue))) {
            return true;
        }
        
        return false;
    }
}
?>