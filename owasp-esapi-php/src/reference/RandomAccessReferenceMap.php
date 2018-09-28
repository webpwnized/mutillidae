<?php
/**
 * OWASP Enterprise Security API (ESAPI)
 * 
 * This file is part of the Open Web Application Security Project (OWASP)
 * Enterprise Security API (ESAPI) project. For details, please see
 * <a href="http://www.owasp.org/index.php/ESAPI">http://www.owasp.org/index.php/ESAPI</a>.
 *
 * Copyright (c) 2007 - 2009 The OWASP Foundation
 * 
 * The ESAPI is published by OWASP under the BSD license. You should read and accept the
 * LICENSE before you use, modify, and/or redistribute this software.
 * 
 * @author Andrew van der Stock
 * @created 2009
 * @since 1.6
 * @package ESAPI_Reference
 */

require_once dirname(__FILE__).'/../AccessReferenceMap.php';
require_once dirname(__FILE__).'/../StringUtilities.php';

/**
 * Reference Implementation of the RandomAccessReferenceMap interface.
 *
 * @category  OWASP
 * @package   ESAPI_Reference
 * @copyright 2009-2010 The OWASP Foundation
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD license
 * @version   Release: @package_version@
 * @link      http://www.owasp.org/index.php/ESAPI
 */
class RandomAccessReferenceMap implements AccessReferenceMap {
	private $dtoi = null;
	private $itod = null;
	private $random = 0;
	
	function __construct($directReferences = null)
	{
		$this->random = mt_rand();
		
		$this->dtoi = new ArrayObject();
		$this->itod = new ArrayObject();
		
		if ( !empty($directReferences) ) 
		{
			$this->update($directReferences);
		}
	}

	/**
	 * Get an iterator through the direct object references. No guarantee is made as 
	 * to the order of items returned.
	 * 
	 * @return the iterator
	 */
	function iterator()
	{
		return $this->dtoi->getIterator();
	}

	/**
	 * Get a safe indirect reference to use in place of a potentially sensitive
	 * direct object reference. Developers should use this call when building
	 * URL's, form fields, hidden fields, etc... to help protect their private
	 * implementation information.
	 * 
	 * @param directReference
	 * 		the direct reference
	 * 
	 * @return 
	 * 		the indirect reference
	 */
	function getIndirectReference($direct)
	{
		if ( empty($direct) )
		{
			return null;
		}
		
		$hash = $this->getHash($direct);
		
		if ( !($this->dtoi->offsetExists($hash)) )
		{
			return null;
		}
		
		return $this->dtoi->offsetGet($hash);
	}

	/**
	 * Get the original direct object reference from an indirect reference.
	 * Developers should use this when they get an indirect reference from a
	 * request to translate it back into the real direct reference. If an
	 * invalid indirect reference is requested, then an AccessControlException is
	 * thrown.
	 * 
	 * @param indirectReference
	 * 		the indirect reference
	 * 
	 * @return 
	 * 		the direct reference
	 * 
	 * @throws AccessControlException 
	 * 		if no direct reference exists for the specified indirect reference
	 */
	function getDirectReference($indirectReference)
	{
		if (!empty($indirectReference) && $this->itod->offsetExists($indirectReference) )
		{
			return $this->itod->offsetGet($indirectReference);
		}
		
		throw new AccessControlException("Access denied", "Request for invalid indirect reference: " + $indirectReference);
		return null;
	}

	/**
	 * Adds a direct reference to the AccessReferenceMap, then generates and returns 
	 * an associated indirect reference.
	 *  
	 * @param direct 
	 * 		the direct reference
	 * 
	 * @return 
	 * 		the corresponding indirect reference
	 */
	function addDirectReference($direct)
	{
		if ( empty($direct) )
		{
			return null;
		}
		
		$hash = $this->getHash($direct);
		
		if ( $this->dtoi->offsetExists($hash) )
		{
			return $this->dtoi->offsetGet($hash);
		}
		
		$indirect = $this->getUniqueRandomReference();
		
		$this->itod->offsetSet($indirect, $direct);
		$this->dtoi->offsetSet($hash, $indirect);
		
		return $indirect;
	}
	
	/**
	 * Create a new random reference that is guaranteed to be unique.
	 * 
	 *  @return 
	 *  	a random reference that is guaranteed to be unique
	 */
	function getUniqueRandomReference() {
		$candidate = null;
		
		do {
			$candidate = ESAPI::getRandomizer()->getRandomString(6	, "123456789");
		} while ($this->itod->offsetExists($candidate));
		
		return $candidate;
	}
	
	function getHash($direct) 
	{
		if ( empty($direct) )
		{
			return null;
		}
		
		$hash = hexdec(substr(md5(serialize($direct)), -7));
		return $hash;
	}
	
	/**
	 * Removes a direct reference and its associated indirect reference from the AccessReferenceMap.
	 * 
	 * @param direct 
	 * 		the direct reference to remove
	 * 
	 * @return 
	 * 		the corresponding indirect reference
	 * 
	 * @throws AccessControlException
	 */
	function removeDirectReference($direct)
	{
		if ( empty($direct) ) {
			return null;
		}
		
		$hash = $this->getHash($direct);
		
		if ( $this->dtoi->offsetExists($hash) ) {
			$indirect = $this->dtoi->offsetGet($hash);
			$this->itod->offsetUnset($indirect);
			$this->dtoi->offsetUnset($hash);
			return $indirect;
		} 
		
		return null;
	}



	/**
	 * Updates the access reference map with a new set of direct references, maintaining
	 * any existing indirect references associated with items that are in the new list.
	 * New indirect references could be generated every time, but that
	 * might mess up anything that previously used an indirect reference, such
	 * as a URL parameter. 
	 * 
	 * @param directReferences
	 * 		a Set of direct references to add
	 */
	function update($directReferences)
	{
		$dtoi_old = clone $this->dtoi;
		
		unset($this->dtoi);
		unset($this->itod);
				
		$this->dtoi = new ArrayObject();
		$this->itod = new ArrayObject();

		$dir = new ArrayObject($directReferences);
		$directIterator = $dir->getIterator();				

		while ($directIterator->valid())
		{
			$indirect = null;
			$direct = $directIterator->current();
			$hash = $this->getHash($direct);
			
			// Try to get the old direct object reference (if it exists)
			// otherwise, create a new entry
			if (!empty($direct) && $dtoi_old->offsetExists($hash) )
			{
				$indirect = $dtoi_old->offsetGet($hash);
			}
			
			if ( empty($indirect) )
			{
				$indirect = $this->getUniqueRandomReference();
			}
			$this->itod->offsetSet($indirect, $direct);
			$this->dtoi->offsetSet($hash, $indirect);
			$directIterator->next();
		}
	}
}
?>