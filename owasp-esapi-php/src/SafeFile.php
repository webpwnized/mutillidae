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
 * @package   ESAPI
 * @author    Andrew van der Stock <vanderaj@owasp.org>
 * @author    Martin Reiche <martin.reiche.ka@googlemail.com>
 * @author    Arnaud Labenne <arnaud.labenne@dotsafe.fr>
 * @author    Mike Boberski <boberski_michael@bah.com>
 * @copyright 2009-2010 The OWASP Foundation
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD license
 * @version   SVN: $Id$
 * @link      http://www.owasp.org/index.php/ESAPI
 */


/**
 * SafeFile requires ValidationException and EnterpriseSecurityException.
 */
require_once dirname(__FILE__).'/errors/ValidationException.php';


/**
 * Use this ESAPI security control to read files from the operating 
 * system.
 * 
 * The idea behind this interface is to extend the PHP SplFileObject 
 * to prevent against null byte injections and other unforeseen problems 
 * resulting from unprintable characters causing problems in path 
 * lookups. This does NOT prevent against directory traversal attacks.
 *
 * @category  OWASP
 * @package   ESAPI
 * @author    Andrew van der Stock <vanderaj@owasp.org>
 * @author    Martin Reiche <martin.reiche.ka@googlemail.com>
 * @author    Arnaud Labenne <arnaud.labenne@dotsafe.fr>
 * @author    Mike Boberski <boberski_michael@bah.com>
 * @copyright 2009-2010 The OWASP Foundation
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD license
 * @version   Release: @package_version@
 * @link      http://www.owasp.org/index.php/ESAPI
 */
class SafeFile extends SplFileObject
{

    private $_PERCENTS_PAT = "/(%)([0-9a-f])([0-9a-f])/i";
    private $_FILE_BLACKLIST_PAT = "/([\\/:*?<>|])/";
    private $_DIR_BLACKLIST_PAT = "/([*?<>|])/";

    /**
     * Creates an extended SplFileObject from the given filename, which
     * prevents against null byte injections and unprintable characters.
     *
     * @param string $path the path to the file (path && file name)
     *
     * @return does not return a value.
     */
    function __construct($path)
    {
        try {
            @parent::__construct($path);
        } catch (Exception $e) {
                throw new EnterpriseSecurityException(
                    'Failed to open stream',
                    'Failed to open stream ' . $e->getMessage()
                );
        }

        $this->_doDirCheck($path);
        $this->_doFileCheck($path);
        $this->_doExtraCheck($path);
    }

    /**
     * Checks the directory against null bytes and unprintable characters.
     *
     * @param string $path the path to the file (path && file name)
     *
     * @return does not return a value
     * @exception ValidationException thrown if check fails
     */
    private function _doDirCheck($path)
    {
        $dir = $this->getPath();
        
        if ( preg_match($this->_DIR_BLACKLIST_PAT, $dir) ) {
            throw new ValidationException(
                'Invalid directory',
                "Directory path ({$dir}) contains illegal character. "
            );
        }

        if ( preg_match($this->_PERCENTS_PAT, $dir) ) {
            throw new ValidationException(
                'Invalid directory',
                "Directory path ({$dir}) contains encoded characters. "
            );
        }

        $ch = $this->_containsUnprintableCharacters($path);
        if ($ch != -1) {
            throw new ValidationException(
                'Invalid directory',
                "Directory path ({$dir}) contains unprintable character. "
            );
        }
    }

    /**
     * Checks the file name against null bytes and unprintable characters.
     *
     * @param string $path the file name
     *
     * @return does not return a value
     * @exception ValidationException thrown if check fails
     */
    private function _doFileCheck($path)
    {
        $filename = $this->getFilename();

        // Workaround for PHP == 5.2.0 getFilename returns entire path.
        if (preg_match('/[\/\\\]/', $filename)) {
            // this _might_ be a filename with a slash in it or it might be
            // a full path.
            $charEncP = mb_detect_encoding($path);
            $pathLen = mb_strlen($path, $charEncP);

            $charEncF = mb_detect_encoding($filename);
            $fileLen = mb_strlen($filename, $charEncF);

            if ($pathLen === $fileLen) {
                // filename is the entire path!
                $dir = $this->getPath();
                $charEncD = mb_detect_encoding($dir);
                $dirLen = mb_strlen($dir, $charEncD);

                // sanity check that the entire path returned by getFilename is
                // longer than the path returned by getPath
                if ($fileLen <= $dirLen) {
                    throw new ValidationException(
                        'Invalid file',
                        'The path returned from SplFileObject::getFilename should have been shorter than the path returned by SplFileObject::getPath.'
                    );
                }

                // Assume file name is $filename with $dir+slash knocked off it.
                $dirLen += 1;
                $filename
                    = mb_substr($filename, $dirLen, $fileLen-$dirLen, $charEncF);
            }

        }

        if ( preg_match($this->_FILE_BLACKLIST_PAT, $filename) ) {
            throw new ValidationException(
                'Invalid file',
                "File path ({$filename}) contains illegal character.");
        }

        if ( preg_match($this->_PERCENTS_PAT, $filename) ) {
            throw new ValidationException(
                'Invalid file',
                "File path ({$filename}) contains encoded characters."
            );
        }

        $ch = $this->_containsUnprintableCharacters($filename);
        if ($ch != -1) {
            throw new ValidationException(
                'Invalid file',
                "File path ({$filename}) contains unprintable character."
            );
        }
    }

    /**
     * Checks the specified string for unprintable characters (ASCII range
     * from 0 to 31 and from 127 to 255).
     *
     * @param string $s the string to check for unprintable characters
     *
     * @return int the value of the first unprintable character found, or -1
     */
    private function _containsUnprintableCharacters($s)
    {
        for ($i = 0; $i < strlen($s); $i++) {
            $ch = $s[$i];
            if (ord($ch) < 32 || ord($ch) > 126) {
                return $ch;
            }
        }
        return -1;
    }

    /**
     * Checks if the last character is a slash
     *
     * @param string $path the string to check
     *
     * @return does not return a value
     * @exception ValidationException thrown if check fails
     */
    private function _doExtraCheck($path)
    {
        $last = substr($path, -1);
        if ($last === '/') {
            throw new ValidationException(
                'Invalid file',
                "File path ({$path}) contains an extra slash."
            );
        }
    }
}