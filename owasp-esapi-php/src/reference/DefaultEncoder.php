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
 * @package   ESAPI_Reference
 * @author    Jeff Williams <jeff.williams@aspectsecurity.com>
 * @author    Linden Darling <Linden.Darling@jds.net.au>
 * @author    jah <jah@jahboite.co.uk>
 * @author    Mike Boberski <boberski_michael@bah.com>
 * @copyright 2009-2010 The OWASP Foundation
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD license
 * @version   SVN: $Id$
 * @link      http://www.owasp.org/index.php/ESAPI
 */

/**
 * DefaultEncoder requires the interface it implements and any Codecs it uses.
 */
require_once dirname(__FILE__).'/../Encoder.php';
require_once dirname(__FILE__).'/../codecs/Base64Codec.php';
require_once dirname(__FILE__).'/../codecs/CSSCodec.php';
require_once dirname(__FILE__).'/../codecs/HTMLEntityCodec.php';
require_once dirname(__FILE__).'/../codecs/JavaScriptCodec.php';
require_once dirname(__FILE__).'/../codecs/PercentCodec.php';
require_once dirname(__FILE__).'/../codecs/VBScriptCodec.php';
require_once dirname(__FILE__).'/../codecs/XMLEntityCodec.php';

/**
 * Reference implementation of the Encoder interface.
 *
 * @category  OWASP
 * @package   ESAPI_Reference
 * @author    Linden Darling <Linden.Darling@jds.net.au>
 * @author    jah <jah@jahboite.co.uk>
 * @author    Mike Boberski <boberski_michael@bah.com>
 * @copyright 2009-2010 The OWASP Foundation
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD license
 * @version   Release: @package_version@
 * @link      http://www.owasp.org/index.php/ESAPI
 */
class DefaultEncoder implements Encoder
{

    private $_base64Codec     = null;
    private $_cssCodec        = null;
    private $_htmlCodec       = null;
    private $_javascriptCodec = null;
    private $_percentCodec    = null;
    private $_vbscriptCodec   = null;
    private $_xmlCodec        = null;

    /*
     * Character sets that define characters (in addition to alphanumerics) that are
     * immune from encoding in various formats
     */
    private $_immune_css        = array( ' ' );
    private $_immune_html       = array( ',', '.', '-', '_', ' ' );
    private $_immune_htmlattr   = array( ',', '.', '-', '_' );
    private $_immune_javascript = array( ',', '.', '_' );
    private $_immune_os         = array( '-' );
    private $_immune_sql        = array( ' ' );
    private $_immune_vbscript   = array( ' ' );
    private $_immune_xml        = array( ',', '.', '-', '_', ' ' );
    private $_immune_xmlattr    = array( ',', '.', '-', '_' );
    private $_immune_xpath      = array( ',', '.', '-', '_', ' ' );
    private $_immune_url        = array( '.', '-', '*', '_');

    private $_codecs = array();
    private $_auditor = null;

    /**
     * Encoder constructor.
     * 
     * @param array $_codecs An array of Codec instances which will be used for
     *                       canonicalization.
     *                    
     * @return does not return a value.
     */
    function __construct($_codecs = null)
    {
        $this->logger = ESAPI::getAuditor("Encoder");

        // initialise codecs
        $this->_base64Codec     = new Base64Codec();
        $this->_cssCodec        = new CSSCodec();
        $this->_htmlCodec       = new HTMLEntityCodec();
        $this->_javascriptCodec = new JavaScriptCodec();
        $this->_percentCodec    = new PercentCodec();
        $this->_vbscriptCodec   = new VBScriptCodec();
        $this->_xmlCodec        = new XMLEntityCodec();

        // initialise array of codecs for use by canonicalize
        if ($_codecs === null) {
            array_push($this->_codecs, $this->_htmlCodec);
            array_push($this->_codecs, $this->_javascriptCodec);
            array_push($this->_codecs, $this->_percentCodec);
            // leaving css and vbs codecs out - they eat / and " chars respectively
            // array_push($this->_codecs,$this->_cssCodec);
            // array_push($this->_codecs,$this->_vbscriptCodec);
        } else if (! is_array($_codecs)) {
            throw new Exception(
                'Invalid Argument. Codec list must be of type '.
                'Array.'
            );
        } else {
            // check array contains only codec instances
            foreach ($_codecs as $codec) {
                if ($codec instanceof Codec == false) {
                    throw new Exception(
                        'Invalid Argument. Codec list must '.
                        'contain only Codec instances.'
                    );
                }
            }
            $this->_codecs = array_merge($this->_codecs, $_codecs);
        }

    }


    /**
     * @inheritdoc
     */
    function canonicalize($input, $strict = true)
    {
        if ($input === null) {
            return null;
        }
        $working = $input;
        $codecFound = null;
        $mixedCount = 1;
        $foundCount = 0;
        $clean = false;
        while (! $clean) {
            $clean = true;

            foreach ($this->_codecs as $codec) {
                $old = $working;
                $working = $codec->decode($working);
                if ($old != $working) {
                    if ($codecFound != null && $codecFound != $codec) {
                        $mixedCount++;
                    }
                    $codecFound = $codec;
                    if ($clean) {
                        $foundCount++;
                    }
                    $clean = false;
                }
            }
        }
        if ( $foundCount >= 2 && $mixedCount > 1 ) {
            if ( $strict == true ) {
                throw new IntrusionException('Input validation failure',
                    'Multiple (' . $foundCount . 'x) and mixed ('
                    . $mixedCount . 'x) encoding detected in ' . $input
                );
            } else {
                $this->logger->warning(
                    Auditor::SECURITY, false,
                    'Multiple (' . $foundCount . 'x) and mixed ('
                    . $mixedCount . 'x) encoding detected in '.$input
                );
            }
        } else if ( $foundCount >= 2 ) {
            if ( $strict == true ) {
                throw new IntrusionException(
                    'Input validation failure',
                    "Multiple encoding ({$foundCount}x) detected in {$input}"
                );
            } else {
                $this->logger->warning(
                    Auditor::SECURITY, false,
                    "Multiple encoding ({$foundCount}x) detected in {$input}"
                );
            }
        } else if ( $mixedCount > 1 ) {
            if ( $strict == true ) {
                throw new IntrusionException(
                    'Input validation failure',
                    "Mixed encoding ({$mixedCount}x) detected in {$input}"
                );
            } else {
                $this->logger->warning(
                    Auditor::SECURITY, false,
                    "Mixed encoding ({$mixedCount}x) detected in {$input}"
                );
            }
        }
        return $working;
    }


    /**
     * @inheritdoc
     */
    function encodeForCSS($input)
    {
        if ($input === null) {
            return null;
        }
        return $this->_cssCodec->encode($this->_immune_css, $input);
    }


    /**
     * @inheritdoc
     */
    function encodeForHTML($input)
    {
        if ($input === null) {
            return null;
        }
        return $this->_htmlCodec->encode($this->_immune_html, $input);
    }


    /**
     * @inheritdoc
     */
    function encodeForHTMLAttribute($input)
    {
        if ($input === null) {
            return null;
        }
        return $this->_htmlCodec->encode($this->_immune_htmlattr, $input);
    }


    /**
     * @inheritdoc
     */
    function encodeForJavaScript($input)
    {
        if ($input === null) {
            return null;
        }
        return $this->_javascriptCodec->encode($this->_immune_javascript, $input);
    }


    /**
     * @inheritdoc
     */
    function encodeForVBScript($input)
    {
        if ($input === null) {
            return null;
        }
        return $this->_vbscriptCodec->encode($this->_immune_vbscript, $input);
    }


    /**
     * @inheritdoc
     */
    function encodeForSQL($codec, $input)
    {
        if ($input === null) {
            return null;
        }
        return $codec->encode($this->_immune_sql, $input);
    }


    /**
     * @inheritdoc
     */
    function encodeForOS($codec, $input)
    {
        if ($input === null) {
            return null;
        }
        
        if ($codec instanceof Codec == false) {
            ESAPI::getLogger('Encoder')->error(
                ESAPILogger::SECURITY, false,
                'Invalid Argument, expected an instance of an OS Codec.'
            );
            return null;
        }
        
        return $codec->encode($this->_immune_os, $input);
    }

    /**
     * @inheritdoc
     */
    function encodeForXPath($input)
    {
        if ($input === null) {
            return null;
        }
        return $this->_htmlCodec->encode($this->_immune_xpath, $input);
    }


    /**
     * @inheritdoc
     */
    function encodeForXML($input)
    {
        if ($input === null) {
            return null;
        }
        return $this->_xmlCodec->encode($this->_immune_xml, $input);
    }


    /**
     * @inheritdoc
     */
    function encodeForXMLAttribute($input)
    {
        if ($input === null) {
            return null;
        }
        return $this->_xmlCodec->encode($this->_immune_xmlattr, $input);
    }


    /**
     * @inheritdoc
     */
    function encodeForURL($input)
    {
        if ($input === null) {
            return null;
        }
        $encoded = $this->_percentCodec->encode($this->_immune_url, $input);

        $initialEncoding = $this->_percentCodec->detectEncoding($encoded);
        $decodedString = mb_convert_encoding('', $initialEncoding);

        $pcnt = $this->_percentCodec->normalizeEncoding('%');
        $two  = $this->_percentCodec->normalizeEncoding('2');
        $zero = $this->_percentCodec->normalizeEncoding('0');
        $char_plus = mb_convert_encoding('+', $initialEncoding);

        $index = 0;
        $limit = mb_strlen($encoded, $initialEncoding);
        for ($i = 0; $i < $limit; $i++) {
            if ($index > $i) {
                continue; // already dealt with this character
            }
            $c = mb_substr($encoded, $i, 1, $initialEncoding);
            $d = mb_substr($encoded, $i+1, 1, $initialEncoding);
            $e = mb_substr($encoded, $i+2, 1, $initialEncoding);
            if (   $this->_percentCodec->normalizeEncoding($c) == $pcnt
                && $this->_percentCodec->normalizeEncoding($d) == $two
                && $this->_percentCodec->normalizeEncoding($e) == $zero
            ) {
                $decodedString .= $char_plus;
                $index += 3;
            } else {
                $decodedString .= $c;
                $index++;
            }
        }

        return $decodedString;
    }


    /**
     * @inheritdoc
     */
    function decodeFromURL($input)
    {
        if ($input === null) {
            return null;
        }
        $canonical = $this->canonicalize($input, true);

        // Replace '+' with ' '
        $initialEncoding = $this->_percentCodec->detectEncoding($canonical);
        $decodedString = mb_convert_encoding('', $initialEncoding);

        $find = $this->_percentCodec->normalizeEncoding('+');
        $char_space = mb_convert_encoding(' ', $initialEncoding);

        $limit = mb_strlen($canonical, $initialEncoding);
        for ($i = 0; $i < $limit; $i++) {
            $c = mb_substr($canonical, $i, 1, $initialEncoding);
            if ($this->_percentCodec->normalizeEncoding($c) == $find) {
                $decodedString .= $char_space;
            } else {
                $decodedString .= $c;
            }
        }

        return $this->_percentCodec->decode($decodedString);
    }


    /**
     * @inheritdoc
     */
    function encodeForBase64($input, $wrap = true)
    {
        if ($input === null) {
            return null;
        }
        return $this->_base64Codec->encode($input, $wrap);
    }


    /**
     * @inheritdoc
     */
    function decodeFromBase64($input)
    {
        if ($input === null) {
            return null;
        }
        return $this->_base64Codec->decode($input);
    }
}
