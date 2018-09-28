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
 * Reference implementation of the Windows codec.
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
class WindowsCodec extends Codec
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
        
        return $encodedOutput . "^" . $c;
    }
    
    
    
    /**
     * {@inheritdoc}
     */
    public function decodeCharacter($input)
    {
        // Assumption/prerequisite: $c is a UTF-32 encoded string
        $_4ByteEncodedInput = $input;
        
        if (mb_substr($_4ByteEncodedInput, 0, 1, "UTF-32") === null) {
            // 1st character is null, so return null
            // eat the 1st character off the string and return null
            //todo: no point in doing this
            $_4ByteEncodedInput = mb_substr(
                $input, 1, mb_strlen($_4ByteEncodedInput, "UTF-32"), "UTF-32"
            );
            return array(
                'decodedCharacter' => null,
                'encodedString' => null
            );
        }
        
        // if this is not an encoded character, return null
        if (mb_substr($_4ByteEncodedInput, 0, 1, "UTF-32") != $this->normalizeEncoding('^')) {
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
}