<?php

/**
 * OWASP Enterprise Security API (ESAPI)
 * 
 * This file is part of the Open Web Application Security Project (OWASP)
 * Enterprise Security API (ESAPI) project. For details, please see
 * <a href="http://www.owasp.org/index.php/ESAPI">http://www.owasp.org/index.php/ESAPI</a>.
 *
 * Copyright (c) 2009 The OWASP Foundation
 * 
 * The ESAPI is published by OWASP under the BSD license. You should read and accept the
 * LICENSE before you use, modify, and/or redistribute this software.
 * 
 * @author Andrew van der Stock
 * @created 2009
 * @since 1.6
 * @package ESAPI_Reference
 */

require_once dirname(__FILE__) . '/../Randomizer.php';

class DefaultRandomizer implements Randomizer
{
	private $maxRand;
	
	function __construct()
	{
		$this->maxRand = mt_getrandmax();	
	}
	/**
	 * Gets a random string of a desired length and character set.  The use of java.security.SecureRandom
	 * is recommended because it provides a cryptographically strong pseudo-random number generator. 
	 * If SecureRandom is not used, the pseudo-random number gernerator used should comply with the 
	 * statistical random number generator tests specified in <a href="http://csrc.nist.gov/cryptval/140-2.htm">
	 * FIPS 140-2, Security Requirements for Cryptographic Modules</a>, section 4.9.1.
	 * 
	 * @param length 
	 * 		the length of the string
	 * @param characterSet 
	 * 		the set of characters to include in the created random string
	 * 
	 * @return 
	 * 		the random string of the desired length and character set
	 */
	function getRandomString($numChars, $charset)
	{
		
		if ( $numChars < 1 || strlen($charset) < 2 ) {
			throw new InvalidArgumentException();
		}
		
		$l = strlen($charset) - 1;
		
		$rs = '';
		for ($i = 0; $i < $numChars; $i++)
		{
			$rs .= $charset[mt_rand(0, $l)];
		}
	
	 	return $rs;
	}

	/**
	 * Returns a random boolean.  The use of java.security.SecureRandom
	 * is recommended because it provides a cryptographically strong pseudo-random number generator. 
	 * If SecureRandom is not used, the pseudo-random number gernerator used should comply with the 
	 * statistical random number generator tests specified in <a href="http://csrc.nist.gov/cryptval/140-2.htm">
	 * FIPS 140-2, Security Requirements for Cryptographic Modules</a>, section 4.9.1.
	 * 
	 * @return 
	 * 		true or false, randomly
	 */
	function getRandomBoolean()
	{
		return (( mt_rand(0, 100) % 2) ? true : false);
	}

	/**
	 * Gets the random integer. The use of java.security.SecureRandom
	 * is recommended because it provides a cryptographically strong pseudo-random number generator. 
	 * If SecureRandom is not used, the pseudo-random number gernerator used should comply with the 
	 * statistical random number generator tests specified in <a href="http://csrc.nist.gov/cryptval/140-2.htm">
	 * FIPS 140-2, Security Requirements for Cryptographic Modules</a>, section 4.9.1.
	 * 
	 * @param min 
	 * 		the minimum integer that will be returned
	 * @param max 
	 * 		the maximum integer that will be returned
	 * 
	 * @return 
	 * 		the random integer
	 */
	function getRandomInteger($min, $max)
	{
		return mt_rand($min, $max);
	}

	/**
	 * Gets the random long. The use of java.security.SecureRandom
	 * is recommended because it provides a cryptographically strong pseudo-random number generator. 
	 * If SecureRandom is not used, the pseudo-random number gernerator used should comply with the 
	 * statistical random number generator tests specified in <a href="http://csrc.nist.gov/cryptval/140-2.htm">
	 * FIPS 140-2, Security Requirements for Cryptographic Modules</a>, section 4.9.1.
	 * 
	 * mt_rand() without arguments will return between 0 and mt_getrandmax(). That's about as good as PHP gets
	 * 
	 * @return 
	 * 		the random long
	 */
	function getRandomLong()
	{
		return mt_rand();
	}

	/**
	 * Returns an unguessable random filename with the specified extension.  This method could call
	 * getRandomString(length, charset) from this Class with the desired length and alphanumerics as the charset 
	 * then merely append "." + extension.
	 * 
	 * @param extension 
	 * 		extension to add to the random filename
	 * 
	 * @return 
	 * 		a random unguessable filename ending with the specified extension
	 */
	function getRandomFilename($extension = '')
	{
		// Because PHP runs on case insensitive OS as well as case sensitive OS, only use lowercase
		
		$rs = $this->getRandomString(16, 'abcdefghijklmnopqrstuvxyz0123456789');
		$rs .= $extension;
		return  $rs;
	}

	/**
	 * Gets the random real.  The use of java.security.SecureRandom
	 * is recommended because it provides a cryptographically strong pseudo-random number generator. 
	 * If SecureRandom is not used, the pseudo-random number gernerator used should comply with the 
	 * statistical random number generator tests specified in <a href="http://csrc.nist.gov/cryptval/140-2.htm">
	 * FIPS 140-2, Security Requirements for Cryptographic Modules</a>, section 4.9.1.
	 * 
	 * @param min 
	 * 		the minimum real number that will be returned
	 * @param max 
	 * 		the maximum real number that will be returned
	 * 
	 * @return 
	 * 		the random real
	 */
	function getRandomReal($min, $max)
	{
		$rf = (float) (mt_rand() / $this->maxRand);		// Maximizes the random bit counts from the PHP PRNG
		
		$factor = $max - $min;
		return (float) ($rf * $factor + $min);
	}

	/**
	 * Generates a random GUID.  This method could use a hash of random Strings, the current time,
	 * and any other random data available.  The format is a well-defined sequence of 32 hex digits 
	 * grouped into chunks of 8-4-4-4-12.  
	 * 
	 * Function from comments found on http://php.net/uniqid
	 * 
	 * @return 
	 * 		the GUID
	 * 
	 * @throws 
	 * 		EncryptionException if hashing or encryption fails 
	 */
	function getRandomGUID()
	{
		return sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
       						 mt_rand(0, 65535), mt_rand(0, 65535), // 32 bits for "time_low"
      						 mt_rand(0, 65535), // 16 bits for "time_mid"
        					 mt_rand(0, 4095),  // 12 bits before the 0100 of (version) 4 for "time_hi_and_version"
        					 bindec(substr_replace(sprintf('%016b', mt_rand(0, 65535)), '01', 6, 2)),
				            // 8 bits, the last two of which (positions 6 and 7) are 01, for "clk_seq_hi_res"
				            // (hence, the 2nd hex digit after the 3rd hyphen can only be 1, 5, 9 or d)
				            // 8 bits for "clk_seq_low"
					        mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535) // 48 bits for "node"
					   );  
	}

}
?>