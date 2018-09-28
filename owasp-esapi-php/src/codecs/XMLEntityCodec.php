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
 * @author    Linden Darling <Linden.Darling@jds.net.au>
 * @author    jah <jah@jahboite.co.uk>
 * @author    Mike Boberski <boberski_michael@bah.com>
 * @copyright 2009-2010 The OWASP Foundation
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD license
 * @version   SVN: $Id$
 * @link      http://www.owasp.org/index.php/ESAPI
 */


/**
 * XMLEntityCodec extends Codec.
 */
require_once dirname(__FILE__) . '/Codec.php';


/**
 * Extension of the abstract class Codec for XML entity encoding.
 *
 * @category  OWASP
 * @package   ESAPI_Reference_Validation
 * @author    Johannes B. Ullrich <jullrich@sans.edu>
 * @author    Linden Darling <Linden.Darling@jds.net.au>
 * @author    jah <jah@jahboite.co.uk>
 * @author    Mike Boberski <boberski_michael@bah.com>
 * @copyright 2009-2010 The OWASP Foundation
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD license
 * @version   Release: @package_version@
 * @link      http://www.owasp.org/index.php/ESAPI
 */
class XMLEntityCodec extends Codec
{
    private static $_characterToEntityMap = Array();
    private static $_entityToCharacterMap = Array();
    private static $_longestEntity = 0;
    private static $_mapIsInitialized = false;

    
    /**
     * Public Constructor calls the parent construcor and initialises the character
     * to entity and entity to character maps.
     * 
     * @return null
     */
    function __construct()
    {
        parent::__construct();
        
        if (self::$_mapIsInitialized == false) {
            $this->_initializeMaps();
            self::$_mapIsInitialized = true;
        }
    }

    
    /**
     * {@inheritdoc}
     */
    public function encodeCharacter($immune,$c)
    {
        // detect encoding, special-handling for chr(172) and chr(128) to chr(159)
        // which fail to be detected by mb_detect_encoding()
        $initialEncoding = $this->detectEncoding($c);

        // Normalize encoding to UTF-32
        $_4ByteUnencodedOutput = $this->normalizeEncoding($c);

        // Start with an empty string
        $encodedOutput = '';

        // Grab the 4 byte character.
        $_4ByteCharacter = $this->forceToSingleCharacter($_4ByteUnencodedOutput);

        // Get the ordinal value of the character.
        list(, $ordinalValue) = unpack("N", $_4ByteCharacter);

        // Check for immune characters.
        if ( $this->containsCharacter($_4ByteCharacter, $immune)) {
            return $encodedOutput . chr($ordinalValue);
        }

        // Check for alphanumeric characters
        $hex = $this->getHexForNonAlphanumeric($_4ByteCharacter);
        if ($hex === null) {
            return $encodedOutput . chr($ordinalValue);
        }

        // Check for illegal characters
        if ((  $ordinalValue <= 31
            && $ordinalValue != 9
            && chr($ordinalValue) != "\n"
            && chr($ordinalValue) != "\r" )
            || ($ordinalValue >= 0x7f && $ordinalValue <= 0x9f )
        ) {
            return $encodedOutput . ' ';
        }

        // Check if there's a defined entity
        if (array_key_exists($_4ByteCharacter, self::$_characterToEntityMap)) {
        
            $entityName = self::$_characterToEntityMap[$_4ByteCharacter];
            if ($entityName!=null) {
                return $encodedOutput . '&' . $entityName . ';';
            }
        
        }

        $encodedOutput .= '&#x' . $hex . ';';

        // Encoded!
        return $encodedOutput;
    }

 
    /**
     * {@inheritdoc}
     */
    public function decodeCharacter($input)
    {
        $decodeResult = null;
        if (mb_substr($input, 0, 1, 'UTF-32') == null) {
            // first character is null, so eat the 1st character off the string and
            // return null
            return array('decodedCharacter' => null, 'encodedString' => null);
        }
    
        // if this is not an encoded character, return null
        if (mb_substr($input, 0, 1, 'UTF-32') != $this->normalizeEncoding('&')) {
        
            // 1st character is not part of encoding pattern, so return null
            return array('decodedCharacter'=>null,'encodedString'=>null);
        }
    
        // 1st character is part of encoding pattern...

        // test for numeric encodings
        if (mb_substr($input, 1, 1, 'UTF-32') == null) {
        
            // 2nd character is null, so return decodedCharacter=null and
            // encodedString=(1st character, malformed encoding)
            return array(
                'decodedCharacter' => null,
                'encodedString' => mb_substr($input, 0, 1, 'UTF-32')
            );
        }
    
        if (mb_substr($input, 1, 1, 'UTF-32') == $this->normalizeEncoding('#')) {
        
            // 2nd character is hash, so handle numbers...
            $decodeResult = $this->_getNumericEntity($input);
            $decodedCharacter = $decodeResult['decodedCharacter'];
            if ($decodedCharacter != null) {
                return $decodeResult;
            }
        
        } else {
        
            // Get the ordinal value of the 2nd character.
            list(, $ordinalValue) = unpack("N", mb_substr($input, 1, 1, 'UTF-32'));
        
            if (preg_match("/^[a-zA-Z]/", chr($ordinalValue))) {
            
                // 2nd character is an alphabetical char, so handle entities...
                $decodeResult = $this->_getNamedEntity($input);
                $decodedCharacter = $decodeResult['decodedCharacter'];
                if ($decodedCharacter != null) {
                    return $decodeResult;
                }
            
            } else {
            
                // 2nd character does not form a known entity, so return null
                return array('decodedCharacter' => null, 'encodedString' => null);
            
            }
        }
    
        // at this stage: decodedCharacter could only be null, encodedString could
        // only be anything between 1st character (i.e. '&') and all remaining
        // characters
        return $decodeResult;
    }

   
    /**
     * getNumericEntry checks the input to see if it is either a decimal or
     * hexadecimal numeric entity.
     * 
     * @param string $input The input to test for being a numeric entity.
     * 
     * @return array Returns an array containing two objects:
     *               'decodedCharacter' => the decoded character.
     *               'encodedString' => the string that was decoded or found to be
     *               malformed.
     */
    private function _getNumericEntity($input)
    {
        // decodeCharacter should've already established that the first two
        // characters are '&#', but check again in case this method is being called
        // from elsewhere.
        if (   mb_substr($input, 0, 1, 'UTF-32') != $this->normalizeEncoding('&')
            || mb_substr($input, 1, 1, 'UTF-32') != $this->normalizeEncoding('#')
        ) {
            // input did not satisfy initial pattern requirements for
            // _getNumericEntity, so return null
            return array('decodedCharacter'=>null,'encodedString'=>null);
        }
    
        if (   mb_substr($input, 2, 1, 'UTF-32') == $this->normalizeEncoding('x')
            || mb_substr($input, 2, 1, 'UTF-32') == $this->normalizeEncoding('X')
        ) {
            return $this->_parseHex($input);
        } else {
            return $this->_parseNumber($input);
        }
        // Unreached
    }


    /**
     * Parse a decimal numeric string.
     * 
     * @param string $input The input to test for being a decimal numeric entity.
     * 
     * @return array Returns an array containing two objects:
     *               'decodedCharacter' => the decoded character.
     *               'encodedString'    => the string that was decoded or found to
     *               be malformed
     */
    private function _parseNumber($input)
    {
        // decodeCharacter and _getNumericEntity should've already established that
        // the first two characters are '&#', but check again in case this method is
        // being called from elsewhere
        if (   mb_substr($input, 0, 1, 'UTF-32') != $this->normalizeEncoding('&')
            || mb_substr($input, 1, 1, 'UTF-32') != $this->normalizeEncoding('#')
        ) {
        
            // input did not satisfy initial pattern requirements for _parseNumber,
            // so return null
            return array('decodedCharacter' => null, 'encodedString' => null);
        }
    
        // get numeric characters up until first occurance of ';', return null if
        // format doesn't conform
        $integerStringAscii = '';
        $integerString = mb_substr($input, 0, 2, 'UTF-32');
        $inputLength = mb_strlen($input, 'UTF-32');
    
        for ($i = 2; $i < $inputLength; $i++) {
        
            // Get the ordinal value of the character.
            list(, $ordinalValue) = unpack("N", mb_substr($input, $i, 1, 'UTF-32'));
        
            // if character is a digit, add it and keep on going
            if (preg_match('/^[0-9]/', chr($ordinalValue))) {
            
                $integerString .= mb_substr($input, $i, 1, 'UTF-32');
                $integerStringAscii .= chr($ordinalValue);
            
            } else if (mb_substr($input, $i, 1, 'UTF-32') == $this->normalizeEncoding(';')) {

                // if character is a semicolon, then eat it and quit
                $integerString .= mb_substr($input, $i, 1, 'UTF-32');
                break;
            
            } else {
                // otherwise just quit
                break;
            }
        }
    
        try {
        
            $parsedInteger = (int) $integerStringAscii;
            $parsedCharacter = $this->normalizeEncoding(chr($parsedInteger));
            return array(
                'decodedCharacter' => $parsedCharacter,
                'encodedString'    => $integerString
            );
        
        } catch(Exception $e) {
        
            //TODO: throw an exception for malformed entity?
            return array(
                'decodedCharacter' => null,
                'encodedString'    => mb_substr($input, 0, $i+1, 'UTF-32')
            );
        
        }
    }


    /**
     * Parse a hexadecimal numeric entity.
     * 
     * @param string $input Hexadecimal numeric string - possibly with a trailing
     *                      semi-colon e.g. '437ae;'.
     * 
     * @return array Returns an array containing two objects:
     *               'decodedCharacter' => null if input is null, the character of
     *               input after decoding
     *               'encodedString' => the string that was decoded or found to be
     *               malformed.
     */
    private function _parseHex($input)
    {
        // decodeCharacter and _getNumericEntity should've already established that
        // the first three characters are '&#x' or '&#X', but check again in case
        // this method is being called from elsewhere.
        if (   mb_substr($input, 0, 1, 'UTF-32') != $this->normalizeEncoding('&')
            || mb_substr($input, 1, 1, 'UTF-32') != $this->normalizeEncoding('#')
            || (mb_substr($input, 2, 1, 'UTF-32') != $this->normalizeEncoding('x')
            &&  mb_substr($input, 2, 1, 'UTF-32') != $this->normalizeEncoding('X'))
        ) {
            // input did not satisfy initial pattern requirements for _parseHex, so
            // return null
            return array('decodedCharacter'=>null, 'encodedString'=>null);
        }
    
        $hexString = '';
        $trailingSemicolon = '';
        $inputLength = mb_strlen($input, 'UTF-32');
    
        for ($i = 3; $i < $inputLength; $i++) {
        
            // Get the ordinal value of the character.
            list(, $ordinalValue) = unpack("N", mb_substr($input, $i, 1, 'UTF-32'));
        
            if (preg_match('/^[0-9a-fA-F]/', chr($ordinalValue))) {
            
                // hex digit found, add it and continue...
                $hexString .= mb_substr($input, $i, 1, 'UTF-32');
            
            } else if (mb_substr($input, $i, 1, 'UTF-32') == $this->normalizeEncoding(';')) {
            
                // if character is a semicolon, then eat it and quit
                $trailingSemicolon = $this->normalizeEncoding(';');
                break;
            
            } else {
            
                // otherwise just quit
                break;
            }
        
        }
    
        // try to convert hexString to integer...
        try {
        
            $parsedInteger = (int) hexdec($hexString);
            if ($parsedInteger <= 0xFF) {
                $parsedCharacter = chr($parsedInteger);
            } else {
                $parsedCharacter = mb_convert_encoding(
                    '&#' . $parsedInteger . ';', 'UTF-8', 'HTML-ENTITIES'
                );
            }
            $parsedCharacter = $this->normalizeEncoding($parsedCharacter);
            return array(
                'decodedCharacter' => $parsedCharacter,
                'encodedString' => mb_substr($input, 0, 3, 'UTF-32')
                    . $hexString . $trailingSemicolon
            );
        
        } catch(Exception $e) {
            //TODO: throw an exception for malformed entity?
            return array(
                'decodedCharacter'=>null,
                'encodedString'=>mb_substr($input, 0, $i+1, 'UTF-32')
            );
        }
    }


    /**
     * Returns the decoded version of the character starting at index, or
     * null if no decoding is possible.
     *
     * Formats all are legal both with and without semi-colon, upper/lower case:
     * &aa;
     * &aaa;
     * &aaaa;
     * &aaaaa;
     * &aaaaaa;
     * &aaaaaaa;
     * &aaaaaaaa;
     *
     * note: the case of the first letter is important and should be preserved
     * so as to differentiate between, say, &Oacute; and &oacute; .
     *
     * @param string $input A string containing a named entity like &quot; and which
     *               may contain trailing characters like &quot;quotlala or
     *               &quotquotlala.
     *
     * @return array Returns an array containing two objects:
     *               'decodedCharacter' => the decoded version of the character
     *               starting at index, or null if no decoding is possible.
     *               'encodedString' => the string that was decoded or found to be
     *               malformed.
     */
    private function _getNamedEntity($input)
    {
        // decodeCharacter should've already established that the 1st character
        // is '&', but check again in case this method is being called from elsewhere
        if (mb_substr($input, 0, 1, 'UTF-32') != $this->normalizeEncoding('&')) {
            // input did not satisfy initial pattern requirements for _getNamedEntity,
            // so return null
            return array('decodedCharacter'=>null, 'encodedString'=>null);
        }
    
        // Get the first alpanum input character
        $inputCaseUnchanged = mb_substr($input, 1, 1, 'UTF-32');
        if ($inputCaseUnchanged === '') {
            return array('decodedCharacter'=>null, 'encodedString'=>null);
        }
        list(, $ordinalValue) = unpack('N', $inputCaseUnchanged);
        $asciiCaseUnchanged = chr($ordinalValue);
    
        // Is it alphanumeric
        $alphanums = str_split(Encoder::CHAR_ALPHANUMERICS, 1);
        if ($this->containsCharacter($inputCaseUnchanged, $alphanums) !== true) {
            return array('decodedCharacter'=>null, 'encodedString'=>null);
        }
    
        // Preserving the case of the first character
        $inputCaseLowerPreserveFirst = $inputCaseUnchanged;
        $asciiCaseLowerPreserveFirst = $asciiCaseUnchanged;
    
        // The first character as lower case.
        $inputCaseLower = strtolower($inputCaseUnchanged);
        $ordinalValue = null;
        list(, $ordinalValue) = unpack('N', $inputCaseLower);
        $asciiCaseLower = chr($ordinalValue);
    
        $entityValue   = null; // the most recently found entity name
        $originalInput = null; // the corresponding original input
    
        // If first char is lowercase CaseLowerPreserveFirst can be discarded.
        if ($asciiCaseUnchanged === $asciiCaseLower) {
            $inputCaseLowerPreserveFirst = null;
            $asciiCaseLowerPreserveFirst = null;
        }
    
        // Test for a valid entity
        if ($asciiCaseLowerPreserveFirst !== null
            && array_key_exists($asciiCaseLower, self::$_entityToCharacterMap)
        ) {
            $entityValue = self::$_entityToCharacterMap[$asciiCaseLower];
            $originalInput = $inputCaseLower;
        }
    
        if (array_key_exists($asciiCaseUnchanged, self::$_entityToCharacterMap)) {
            $entityValue = self::$_entityToCharacterMap[$asciiCaseUnchanged];
            $originalInput = $inputCaseUnchanged;
        }
    
        // Loop through remaining characters or as far as the longest known entity.
        $limit = min(mb_strlen($input, 'UTF-32'), self::$_longestEntity);
        for ($i = 2; $i < $limit; $i++) {
            $c = mb_substr($input, $i, 1, 'UTF-32');
            if ($c === '') {
                break;
            }
            list(, $ordVal) = unpack('N', $c);
            $a = chr($ordVal);
            if ($a == ';' && $entityValue !== null) {
                $originalInput .= $c;
                break;
            }
            if ($this->containsCharacter($c, $alphanums) !== true) {
                break;
            }
            // we have an alphanum!
            $inputCaseUnchanged .= $c;
            $asciiCaseUnchanged .= $a;

            $cLower = strtolower($c);
            if ($inputCaseLowerPreserveFirst !== null) {
                $inputCaseLowerPreserveFirst .= $cLower;
            }
            $inputCaseLower .= $cLower;
            list(, $ordValL) = unpack('N', $cLower);
            if ($asciiCaseLowerPreserveFirst !== null) {
                $asciiCaseLowerPreserveFirst .= chr($ordValL);
            }
            $asciiCaseLower .= chr($ordValL);
        
            if (   $asciiCaseLower !== $asciiCaseUnchanged
                && array_key_exists($asciiCaseLower, self::$_entityToCharacterMap)
            ) {
                $entityValue = self::$_entityToCharacterMap[$asciiCaseLower];
                $originalInput = $inputCaseLower;
            }
            if (   $asciiCaseLowerPreserveFirst !== null
                && $asciiCaseLowerPreserveFirst !== $asciiCaseLower
                && array_key_exists($asciiCaseLowerPreserveFirst, self::$_entityToCharacterMap)
            ) {
                $entityValue
                    = self::$_entityToCharacterMap[$asciiCaseLowerPreserveFirst];
                $originalInput = $inputCaseLowerPreserveFirst;
            }
            if (array_key_exists($asciiCaseUnchanged, self::$_entityToCharacterMap)
            ) {
                $entityValue = self::$_entityToCharacterMap[$asciiCaseUnchanged];
                $originalInput = $inputCaseUnchanged;
            }
        }
        if ($originalInput !== null) {
            $originalInput = $this->normalizeEncoding('&') . $originalInput;
        }
    
        return array (
            'decodedCharacter' => $entityValue,
            'encodedString'    => $originalInput,
        );
    }


    /**
     * Initialize two maps:
     * _characterToEntityMap maps characters to the five XML 1.0 predefined
     * entity names for encoding characters as named entities.
     * _entityToCharacterMap maps 252 entity names defined in HTML 4 and one
     * additional entitiy name defined in XML 1.0 to their corresponding caharcters
     * for decoding of named entities.
     * During the initialization, the length of the longest named entity (including
     * the leading ampersand and trailing semi-colon) is noted in _longestEntity.
     * 
     * @return null
     */
    private function _initializeMaps()
    {
        $entityNamesForEncoding = array(
            'quot' => 34, /* quotation mark */
            'amp'  => 38, /* ampersand */
            'apos' => 39, /* apostrophe */
            'lt'   => 60, /* less-than sign */
            'gt'   => 62  /* greater-than sign */
        );
        $entityNamesForDecoding = array(
            'quot'     => 34,   /* quotation mark */
            'amp'      => 38,   /* ampersand */
            'apos'     => 39,   /* apostrophe */
            'lt'       => 60,   /* less-than sign */
            'gt'       => 62,   /* greater-than sign */
            'nbsp'     => 160,  /* no-break space */
            'iexcl'    => 161,  /* inverted exclamation mark */
            'cent'     => 162,  /* cent sign */
            'pound'    => 163,  /* pound sign */
            'curren'   => 164,  /* currency sign */
            'yen'      => 165,  /* yen sign */
            'brvbar'   => 166,  /* broken bar */
            'sect'     => 167,  /* section sign */
            'uml'      => 168,  /* diaeresis */
            'copy'     => 169,  /* copyright sign */
            'ordf'     => 170,  /* feminine ordinal indicator */
            'laquo'    => 171,  /* left-pointing double angle quotation mark */
            'not'      => 172,  /* not sign */
            'shy'      => 173,  /* soft hyphen */
            'reg'      => 174,  /* registered sign */
            'macr'     => 175,  /* macron */
            'deg'      => 176,  /* degree sign */
            'plusmn'   => 177,  /* plus-minus sign */
            'sup2'     => 178,  /* superscript two */
            'sup3'     => 179,  /* superscript three */
            'acute'    => 180,  /* acute accent */
            'micro'    => 181,  /* micro sign */
            'para'     => 182,  /* pilcrow sign */
            'middot'   => 183,  /* middle dot */
            'cedil'    => 184,  /* cedilla */
            'sup1'     => 185,  /* superscript one */
            'ordm'     => 186,  /* masculine ordinal indicator */
            'raquo'    => 187,  /* right-pointing double angle quotation mark */
            'frac14'   => 188,  /* vulgar fraction one quarter */
            'frac12'   => 189,  /* vulgar fraction one half */
            'frac34'   => 190,  /* vulgar fraction three quarters */
            'iquest'   => 191,  /* inverted question mark */
            'Agrave'   => 192,  /* Latin capital letter a with grave */
            'Aacute'   => 193,  /* Latin capital letter a with acute */
            'Acirc'    => 194,  /* Latin capital letter a with circumflex */
            'Atilde'   => 195,  /* Latin capital letter a with tilde */
            'Auml'     => 196,  /* Latin capital letter a with diaeresis */
            'Aring'    => 197,  /* Latin capital letter a with ring above */
            'AElig'    => 198,  /* Latin capital letter ae */
            'Ccedil'   => 199,  /* Latin capital letter c with cedilla */
            'Egrave'   => 200,  /* Latin capital letter e with grave */
            'Eacute'   => 201,  /* Latin capital letter e with acute */
            'Ecirc'    => 202,  /* Latin capital letter e with circumflex */
            'Euml'     => 203,  /* Latin capital letter e with diaeresis */
            'Igrave'   => 204,  /* Latin capital letter i with grave */
            'Iacute'   => 205,  /* Latin capital letter i with acute */
            'Icirc'    => 206,  /* Latin capital letter i with circumflex */
            'Iuml'     => 207,  /* Latin capital letter i with diaeresis */
            'ETH'      => 208,  /* Latin capital letter eth */
            'Ntilde'   => 209,  /* Latin capital letter n with tilde */
            'Ograve'   => 210,  /* Latin capital letter o with grave */
            'Oacute'   => 211,  /* Latin capital letter o with acute */
            'Ocirc'    => 212,  /* Latin capital letter o with circumflex */
            'Otilde'   => 213,  /* Latin capital letter o with tilde */
            'Ouml'     => 214,  /* Latin capital letter o with diaeresis */
            'times'    => 215,  /* multiplication sign */
            'Oslash'   => 216,  /* Latin capital letter o with stroke */
            'Ugrave'   => 217,  /* Latin capital letter u with grave */
            'Uacute'   => 218,  /* Latin capital letter u with acute */
            'Ucirc'    => 219,  /* Latin capital letter u with circumflex */
            'Uuml'     => 220,  /* Latin capital letter u with diaeresis */
            'Yacute'   => 221,  /* Latin capital letter y with acute */
            'THORN'    => 222,  /* Latin capital letter thorn */
            'szlig'    => 223,  /* Latin small letter sharp s - German Eszett */
            'agrave'   => 224,  /* Latin small letter a with grave */
            'aacute'   => 225,  /* Latin small letter a with acute */
            'acirc'    => 226,  /* Latin small letter a with circumflex */
            'atilde'   => 227,  /* Latin small letter a with tilde */
            'auml'     => 228,  /* Latin small letter a with diaeresis */
            'aring'    => 229,  /* Latin small letter a with ring above */
            'aelig'    => 230,  /* Latin lowercase ligature ae */
            'ccedil'   => 231,  /* Latin small letter c with cedilla */
            'egrave'   => 232,  /* Latin small letter e with grave */
            'eacute'   => 233,  /* Latin small letter e with acute */
            'ecirc'    => 234,  /* Latin small letter e with circumflex */
            'euml'     => 235,  /* Latin small letter e with diaeresis */
            'igrave'   => 236,  /* Latin small letter i with grave */
            'iacute'   => 237,  /* Latin small letter i with acute */
            'icirc'    => 238,  /* Latin small letter i with circumflex */
            'iuml'     => 239,  /* Latin small letter i with diaeresis */
            'eth'      => 240,  /* Latin small letter eth */
            'ntilde'   => 241,  /* Latin small letter n with tilde */
            'ograve'   => 242,  /* Latin small letter o with grave */
            'oacute'   => 243,  /* Latin small letter o with acute */
            'ocirc'    => 244,  /* Latin small letter o with circumflex */
            'otilde'   => 245,  /* Latin small letter o with tilde */
            'ouml'     => 246,  /* Latin small letter o with diaeresis */
            'divide'   => 247,  /* division sign */
            'oslash'   => 248,  /* Latin small letter o with stroke */
            'ugrave'   => 249,  /* Latin small letter u with grave */
            'uacute'   => 250,  /* Latin small letter u with acute */
            'ucirc'    => 251,  /* Latin small letter u with circumflex */
            'uuml'     => 252,  /* Latin small letter u with diaeresis */
            'yacute'   => 253,  /* Latin small letter y with acute */
            'thorn'    => 254,  /* Latin small letter thorn */
            'yuml'     => 255,  /* Latin small letter y with diaeresis */
            'OElig'    => 338,  /* Latin capital ligature oe */
            'oelig'    => 339,  /* Latin small ligature oe */
            'Scaron'   => 352,  /* Latin capital letter s with caron */
            'scaron'   => 353,  /* Latin small letter s with caron */
            'Yuml'     => 376,  /* Latin capital letter y with diaeresis */
            'fnof'     => 402,  /* Latin small letter f with hook */
            'circ'     => 710,  /* modifier letter circumflex accent */
            'tilde'    => 732,  /* small tilde */
            'Alpha'    => 913,  /* Greek capital letter alpha */
            'Beta'     => 914,  /* Greek capital letter beta */
            'Gamma'    => 915,  /* Greek capital letter gamma */
            'Delta'    => 916,  /* Greek capital letter delta */
            'Epsilon'  => 917,  /* Greek capital letter epsilon */
            'Zeta'     => 918,  /* Greek capital letter zeta */
            'Eta'      => 919,  /* Greek capital letter eta */
            'Theta'    => 920,  /* Greek capital letter theta */
            'Iota'     => 921,  /* Greek capital letter iota */
            'Kappa'    => 922,  /* Greek capital letter kappa */
            'Lambda'   => 923,  /* Greek capital letter lambda */
            'Mu'       => 924,  /* Greek capital letter mu */
            'Nu'       => 925,  /* Greek capital letter nu */
            'Xi'       => 926,  /* Greek capital letter xi */
            'Omicron'  => 927,  /* Greek capital letter omicron */
            'Pi'       => 928,  /* Greek capital letter pi */
            'Rho'      => 929,  /* Greek capital letter rho */
            'Sigma'    => 931,  /* Greek capital letter sigma */
            'Tau'      => 932,  /* Greek capital letter tau */
            'Upsilon'  => 933,  /* Greek capital letter upsilon */
            'Phi'      => 934,  /* Greek capital letter phi */
            'Chi'      => 935,  /* Greek capital letter chi */
            'Psi'      => 936,  /* Greek capital letter psi */
            'Omega'    => 937,  /* Greek capital letter omega */
            'alpha'    => 945,  /* Greek small letter alpha */
            'beta'     => 946,  /* Greek small letter beta */
            'gamma'    => 947,  /* Greek small letter gamma */
            'delta'    => 948,  /* Greek small letter delta */
            'epsilon'  => 949,  /* Greek small letter epsilon */
            'zeta'     => 950,  /* Greek small letter zeta */
            'eta'      => 951,  /* Greek small letter eta */
            'theta'    => 952,  /* Greek small letter theta */
            'iota'     => 953,  /* Greek small letter iota */
            'kappa'    => 954,  /* Greek small letter kappa */
            'lambda'   => 955,  /* Greek small letter lambda */
            'mu'       => 956,  /* Greek small letter mu */
            'nu'       => 957,  /* Greek small letter nu */
            'xi'       => 958,  /* Greek small letter xi */
            'omicron'  => 959,  /* Greek small letter omicron */
            'pi'       => 960,  /* Greek small letter pi */
            'rho'      => 961,  /* Greek small letter rho */
            'sigmaf'   => 962,  /* Greek small letter final sigma */
            'sigma'    => 963,  /* Greek small letter sigma */
            'tau'      => 964,  /* Greek small letter tau */
            'upsilon'  => 965,  /* Greek small letter upsilon */
            'phi'      => 966,  /* Greek small letter phi */
            'chi'      => 967,  /* Greek small letter chi */
            'psi'      => 968,  /* Greek small letter psi */
            'omega'    => 969,  /* Greek small letter omega */
            'thetasym' => 977,  /* Greek theta symbol */
            'upsih'    => 978,  /* Greek upsilon with hook symbol */
            'piv'      => 982,  /* Greek pi symbol */
            'ensp'     => 8194, /* en space */
            'emsp'     => 8195, /* em space */
            'thinsp'   => 8201, /* thin space */
            'zwnj'     => 8204, /* zero width non-joiner */
            'zwj'      => 8205, /* zero width joiner */
            'lrm'      => 8206, /* left-to-right mark */
            'rlm'      => 8207, /* right-to-left mark */
            'ndash'    => 8211, /* en dash */
            'mdash'    => 8212, /* em dash */
            'lsquo'    => 8216, /* left single quotation mark */
            'rsquo'    => 8217, /* right single quotation mark */
            'sbquo'    => 8218, /* single low-9 quotation mark */
            'ldquo'    => 8220, /* left double quotation mark */
            'rdquo'    => 8221, /* right double quotation mark */
            'bdquo'    => 8222, /* double low-9 quotation mark */
            'dagger'   => 8224, /* dagger */
            'Dagger'   => 8225, /* double dagger */
            'bull'     => 8226, /* bullet */
            'hellip'   => 8230, /* horizontal ellipsis */
            'permil'   => 8240, /* per mille sign */
            'prime'    => 8242, /* prime */
            'Prime'    => 8243, /* double prime */
            'lsaquo'   => 8249, /* single left-pointing angle quotation mark */
            'rsaquo'   => 8250, /* single right-pointing angle quotation mark */
            'oline'    => 8254, /* overline */
            'frasl'    => 8260, /* fraction slash */
            'euro'     => 8364, /* euro sign */
            'image'    => 8465, /* black-letter capital i */
            'weierp'   => 8472, /* script capital p - Weierstrass p */
            'real'     => 8476, /* black-letter capital r */
            'trade'    => 8482, /* trademark sign */
            'alefsym'  => 8501, /* alef symbol */
            'larr'     => 8592, /* leftwards arrow */
            'uarr'     => 8593, /* upwards arrow */
            'rarr'     => 8594, /* rightwards arrow */
            'darr'     => 8595, /* downwards arrow */
            'harr'     => 8596, /* left right arrow */
            'crarr'    => 8629, /* downwards arrow with corner leftwards */
            'lArr'     => 8656, /* leftwards double arrow */
            'uArr'     => 8657, /* upwards double arrow */
            'rArr'     => 8658, /* rightwards double arrow */
            'dArr'     => 8659, /* downwards double arrow */
            'hArr'     => 8660, /* left right double arrow */
            'forall'   => 8704, /* for all */
            'part'     => 8706, /* partial differential */
            'exist'    => 8707, /* there exists */
            'empty'    => 8709, /* empty set */
            'nabla'    => 8711, /* nabla */
            'isin'     => 8712, /* element of */
            'notin'    => 8713, /* not an element of */
            'ni'       => 8715, /* contains as member */
            'prod'     => 8719, /* n-ary product */
            'sum'      => 8721, /* n-ary summation */
            'minus'    => 8722, /* minus sign */
            'lowast'   => 8727, /* asterisk operator */
            'radic'    => 8730, /* square root */
            'prop'     => 8733, /* proportional to */
            'infin'    => 8734, /* infinity */
            'ang'      => 8736, /* angle */
            'and'      => 8743, /* logical and */
            'or'       => 8744, /* logical or */
            'cap'      => 8745, /* intersection */
            'cup'      => 8746, /* union */
            'int'      => 8747, /* integral */
            'there4'   => 8756, /* therefore */
            'sim'      => 8764, /* tilde operator */
            'cong'     => 8773, /* congruent to */
            'asymp'    => 8776, /* almost equal to */
            'ne'       => 8800, /* not equal to */
            'equiv'    => 8801, /* identical to - equivalent to */
            'le'       => 8804, /* less-than or equal to */
            'ge'       => 8805, /* greater-than or equal to */
            'sub'      => 8834, /* subset of */
            'sup'      => 8835, /* superset of */
            'nsub'     => 8836, /* not a subset of */
            'sube'     => 8838, /* subset of or equal to */
            'supe'     => 8839, /* superset of or equal to */
            'oplus'    => 8853, /* circled plus */
            'otimes'   => 8855, /* circled times */
            'perp'     => 8869, /* up tack */
            'sdot'     => 8901, /* dot operator */
            'lceil'    => 8968, /* left ceiling */
            'rceil'    => 8969, /* right ceiling */
            'lfloor'   => 8970, /* left floor */
            'rfloor'   => 8971, /* right floor */
            'lang'     => 9001, /* left-pointing angle bracket */
            'rang'     => 9002, /* right-pointing angle bracket */
            'loz'      => 9674, /* lozenge */
            'spades'   => 9824, /* black spade suit */
            'clubs'    => 9827, /* black club suit */
            'hearts'   => 9829, /* black heart suit */
            'diams'    => 9830, /* black diamond suit */
        );
        $longestEntity = 0;
        // populate $_characterToEntityMap
        foreach ($entityNamesForEncoding as $name => $ord) {
            $character = pack('N', $ord);
            self::$_characterToEntityMap[$character] = $name;
            
            // get the length of the longest entity name
            $len = mb_strlen($name, 'ASCII');
            if ($len > $longestEntity) {
                $longestEntity = $len;
            }
        }
        // populate $_characterToEntityMap
        $limit = count($entityNamesForDecoding);
        foreach ($entityNamesForDecoding as $name => $ord) {
            $character = pack('N', $ord);
            self::$_entityToCharacterMap[$name] = $character;
            
            // get the length of the longest entity name
            $len = mb_strlen($name, 'ASCII');
            if ($len > $longestEntity) {
                $longestEntity = $len;
            }
        }
        self::$_longestEntity = $longestEntity + 2;
    }
}