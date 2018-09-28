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
 * @package   ESAPI_Codecs
 * @author    jah <jah@jahboite.co.uk>
 * @copyright 2009-2010 The OWASP Foundation
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD license
 * @version   SVN: $Id$
 * @link      http://www.owasp.org/index.php/ESAPI
 */


/**
 * 
 * @var string Define the name of the Auditor instance for CodecDebug.
 */
define('CD_LOG', 'CodecDebug');


/**
 * CodecDebug is a singleton class to aid Codec debugging.  It buffers debug
 * info comprising the input to a Codec encode/decode method, as single UTF-32
 * encoded characters, as well as the final output from the Codec method.  The
 * debug info is logged immediately before the Codec method returns its value
 * and the buffer is cleared at that time.
 * To enable CodecDebug add the following to the ESAPI.xml file if not already
 * present:
 * <SpecialDebugging><Enabled>true</Enabled></SpecialDebugging>
 *
 * PHP version 5.2
 *
 * @category  OWASP
 * @package   ESAPI_Codecs
 * @author    jah <jah@jahboite.co.uk>
 * @copyright 2009-2010 The OWASP Foundation
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD license
 * @version   Release: @package_version@
 * @link      http://www.owasp.org/index.php/ESAPI
 */
class CodecDebug
{
    private $_verb;
    private $_buf = null;
    private $_allowRecurse = true;
    private $_enabled = false;

    private static $_instance;
    
    
    /**
     * Prevents public cloning of this singleton class.
     * 
     * @return null
     */
    private function __clone()
    {
    }


    /**
     * Private constructor ensures CodecDebug can only be instantiated privately.
     * Stores boolean true in $_enabled if SepcialDebugging is enabled.  This object
     * will only produce output if $_enabled is true.
     * 
     * @return null
     */
    private function __construct()
    {
        $this->_enabled
            = ESAPI::getSecurityConfiguration()->getSpecialDebugging();
    }


    /**
     * Retrieves the singleton instance of CodecDebug.
     * 
     * @return CodecDebug Singleton Instance of CodecDebug.
     */
    public static function getInstance()
    {
        if (!self::$_instance) {
            self::$_instance = new CodecDebug();
        }
        return self::$_instance;
    }


    /**
     * Adds a string of one or more encoded characters to the debug output.
     * Should be called, for example, from Codec->decode().
     *
     * @param string $stringNormalizedEncoding is a UTF-32 encoded string.
     * 
     * @return null
     */
    public function addEncodedString($stringNormalizedEncoding)
    {
        if (   $this->_enabled == false
            || ! ESAPI::getAuditor(CD_LOG)->isDebugEnabled()
            || ! $this->_allowRecurse
        ) {
            return;
        }
        $this->_verb = "Decod";
        $this->_addString($stringNormalizedEncoding);
    }


    /**
     * Adds a string of one or more unencoded characters to the debug output.
     * Should be called, for example, from Codec->encode().
     *
     * @param string $stringNormalizedEncoding is a UTF-32 encoded string.
     * 
     * @return null
     */
    public function addUnencodedString($stringNormalizedEncoding)
    {
        if (   $this->_enabled == false
            || ! ESAPI::getAuditor(CD_LOG)->isDebugEnabled()
            || ! $this->_allowRecurse
        ) {
            return;
        }
        $this->_verb = "Encod";
        $this->_addString($stringNormalizedEncoding);
    }


    /**
     * output appends the final output from a codec (either an encoded or
     * decoded string) to the contents of $this->_buf and then logs this
     * debugging output before resetting the CodecDebug instance ready for
     * reuse.
     *
     * @param string $codecOutput is the final output being returned from Codec.
     * 
     * @return null
     */
    public function output($codecOutput)
    {
        if (   $this->_enabled == false
            || ! ESAPI::getAuditor(CD_LOG)->isDebugEnabled()
            || ! $this->_allowRecurse
        ) {
            return;
        }
        if ($this->_buf === null) {
            return; // the codec being tested has not added any normalised inputs.
        }
        $output = '';
    
        $this->_allowRecurse = false;
        $htmlCodecOutput = ESAPI::getEncoder()->encodeForHTML($codecOutput);
        if ($htmlCodecOutput == '') {
            $output = $this->_buf . $this->_verb . 'ed string was an empty string.';
        } else {
            $output = $this->_buf . $this->_verb . 'ed: [' . $htmlCodecOutput . ']';
        }

        ESAPI::getAuditor(CD_LOG)->debug(Auditor::SECURITY, true, $output);
        $this->_allowRecurse = true;

        $this->_buf  = null;
        $this->_verb = null;
    }


    /**
     * _addString is called by addEncodedString or addUnencodedString and adds
     * Codec input to the buffer character by character.  It also adds some
     * backtrace information to the buffer before adding any characters.
     *
     * @param string $string is a UTF-32 encoded string.
     * 
     * @return null
     */
    private function _addString($string)
    {
        if (   $this->_enabled == false
            || ! ESAPI::getAuditor(CD_LOG)->isDebugEnabled()
            || ! $this->_allowRecurse
        ) {
            return;
        }
        // start with some details about the caller
        if ($this->_buf === null) {
            $caller = null;
            try {
                $caller = $this->_shortTrace();
            } catch (Exception $e) {
                $caller = $this->_verb . 'ing';
            }
            $this->_buf = $caller . ":\n";
        }
        // add the string, char by char
        $len = mb_strlen($string, 'UTF-32');
        if ($len == 0) {
            $this->_addNormalized('');
            return;
        }
        for ($i=0; $i<$len; $i++) {
            $char = mb_substr($string, $i, 1, 'UTF-32');
            $this->_addNormalized($char);
        }
    }


    /**
     * _addNormalized is called by _addString and adds a character (with
     * accompanying debug info) to the buffer.
     *
     * @param string $charNormalizedEncoding a single character.
     * 
     * @return null
     */
    private function _addNormalized($charNormalizedEncoding)
    {
        ob_start();
        var_dump($charNormalizedEncoding);
        $dumpedVar = ob_get_clean();
        $matches=array();
        if (! preg_match('/\(length=([0-9]+)\)/', $dumpedVar, $matches)) {
            $matches[1] = strtok(stristr($dumpedVar, '('), '"');
        }
        $this->_buf .= 'Normalized codec input: ' .
        $matches[1] .
                      ' bytes [' .
        substr(var_export($charNormalizedEncoding, true), 0) .
                      "]\n";
    }


    /**
     * Convenience method which returns a shortened backtrace.  it's not very
     * robust and assumes that one of the add*String methods was called from
     * either Codec or a method in one of the codecs.
     * 
     * @return string shortened backtrace.
     */
    private function _shortTrace()
    {
        $dt = debug_backtrace();
        $i = 0;
        $pos = 0;
        $trace = '';
        $objName = '';
        for ($i=2; $i<8; $i++) {
            if (   array_key_exists($i, $dt)
                && array_key_exists('class', $dt[$i])
                && $dt[$i]['class'] == 'Codec'
            ) {
                if ($i == 4) { // this is a bit tenuous, but it should suffice...
                    $pos = 6;
                    $trace .= $dt[$pos]['class'] . '-&gt;' .  
                        $dt[$pos--]['function'] . ', ';
                } else {
                    $pos = ($dt[5]['class'] == 'SimpleInvoker') ? 4 : 5;
                    $objName = ', ' . get_class($dt[$i]['object']);
                }
                break;
            }
        }
        if ($pos == 0) {
            throw new Exception('backtrace is odd!'); // abort!
        }
        $trace .= $dt[$pos]['class'] . '.' .  $dt[$pos--]['function'] . ', ';
        $trace .= $dt[$pos]['class'] . '.' .  $dt[$pos--]['function'] . ', ';
        $trace .= $dt[$pos]['class'] . '.' .  $dt[$pos]['function']   . $objName;
    
        return $trace;
    }
}
?>