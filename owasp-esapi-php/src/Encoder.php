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
 * @author    Linden Darling <Linden.Darling@jds.net.au>
 * @author    jah <jah@jahboite.co.uk>
 * @author    Mike Boberski <boberski_michael@bah.com>
 * @copyright 2009-2010 The OWASP Foundation
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD license
 * @version   SVN: $Id$
 * @link      http://www.owasp.org/index.php/ESAPI
 */

/**
 * Implementations will require Codec.
 */
require_once dirname(__FILE__) . '/codecs/Codec.php';

/**
 * Use this ESAPI security control to wrap your codecs.
 * 
 * The idea behind this interface is to make output safe so that it 
 * will be safe for the intended interpreter.
 *
 * @category  OWASP
 * @package   ESAPI
 * @author    Andrew van der Stock <vanderaj@owasp.org>
 * @author    Linden Darling <Linden.Darling@jds.net.au>
 * @author    jah <jah@jahboite.co.uk>
 * @author    Mike Boberski <boberski_michael@bah.com>
 * @copyright 2009-2010 The OWASP Foundation
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD license
 * @version   Release: @package_version@
 * @link      http://www.owasp.org/index.php/ESAPI
 */
interface Encoder
{
    /*
     * Standard character sets.
     */
    const CHAR_LOWERS        = 'abcdefghijklmnopqrstuvwxyz';
    const CHAR_UPPERS        = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    const CHAR_DIGITS        = '0123456789';
    const CHAR_SPECIALS      = '.-_!@$^*=~|+?';
    const CHAR_LETTERS       
        = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    const CHAR_ALPHANUMERICS 
        = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';

    /*
     * Password character sets.
     */
    /**
     * Lower case alphabet, for passwords, which excludes 'l', 'i' and 'o'.
     */
    const CHAR_PASSWORD_LOWERS = 'abcdefghjkmnpqrstuvwxyz';

    /**
     * Upper case alphabet, for passwords, which excludes 'I' and 'O'.
     */
    const CHAR_PASSWORD_UPPERS = 'ABCDEFGHJKLMNPQRSTUVWXYZ';

    /**
     * Numerical digits, for passwords, which excludes '0'.
     */
    const CHAR_PASSWORD_DIGITS = '123456789';

    /**
     * Special characters, for passwords, excluding '|' which resembles
     * alphanumeric characters 'i' and '1' and excluding '+' used in URL
     * encoding.
     */
    const CHAR_PASSWORD_SPECIALS = '.-_!@$*=?';

    /**
     * Union of Encoder::CHAR_PASSWORD_LOWERS and Encoder::CHAR_PASSWORD_UPPERS.
     */
    const CHAR_PASSWORD_LETTERS = 'abcdefghjkmnpqrstuvwxyzABCDEFGHJKLMNPQRSTUVWXYZ';


    /**
     * Data canonicalization.
     *
     * This method performs canonicalization on data received to ensure that it
     * has been reduced to its most basic form before validation, for example
     * URL-encoded data received from ordinary "application/x-www-url-encoded"
     * forms, so that it may be validated properly.
     *
     * Canonicalization is simply the operation of reducing a possibly encoded
     * string down to its simplest form. This is important, because attackers
     * frequently use encoding to change their input in a way that will bypass
     * validation filters, but still be interpreted properly by the target of
     * the attack. Note that data encoded more than once is not something that a
     * normal user would generate and should be regarded as an attack.
     *
     * For input that comes from an HTTP request, there are generally two types
     * of encoding to be concerned with. The first is
     * "applicaton/x-www-url-encoded" which is what is typically used in most
     * forms and URI's where characters are encoded in a %xy format. The other
     * type of common character encoding is HTML entity encoding, which uses
     * several formats:
     *
     * <pre>&amp;lt;</pre>,
     * <pre>&amp;#117;</pre>, and
     * <pre>&amp;#x3a;</pre>.
     *
     * Note that all of these formats may possibly render properly in a browser
     * without the trailing semicolon.
     *
     * Double-encoding is a particularly thorny problem, as applying ordinary
     * decoders may introduce encoded characters, even characters encoded with a
     * different encoding scheme. For example %26lt; is a < character which has
     * been entity encoded and then the first character has been url-encoded.
     * Implementations should throw an IntrusionException when double-encoded
     * characters are detected.
     *
     * Note that there is also "multipart/form" encoding, which allows files and
     * other binary data to be transmitted. Each part of a multipart form can
     * itself be encoded according to a "Content-Transfer-Encoding" header. See
     * the HTTPUtilties.getSafeFileUploads() method.
     *
     * For more information on form encoding, please refer to the
     * <a href="http://www.w3.org/TR/html4/interact/forms.html#h-17.13.4">
     * W3C specifications</a>.
     *
     * @param string $input  string to canonicalize.
     * @param bool   $strict true if checking for multiple and/or mixed encoding is
     *                       desired, false otherwise.
     *
     * @return the canonicalized input string.
     * @throws IntrusionException if, in strict mode, canonicalization detects
     *         multiple or mixed encoding.
     *         
     * @see <a href="http://www.w3.org/TR/html4/interact/forms.html#h-17.13.4">W3C specifications</a>
     */
    function canonicalize($input, $strict = true);


    /**
     * Encode data for use in Cascading Style Sheets (CSS) content.
     *
     * @param string $input string to encode for CSS.
     *
     * @return the input string encoded for CSS.
     * 
     * @see <a href="http://www.w3.org/TR/CSS21/syndata.html#escaped-characters">CSS Syntax [w3.org]</a>
     */
    function encodeForCSS($input);


    /**
     * Encode data for use in HTML using HTML entity encoding
     *
     * Note that the following characters: 00-08, 0B-0C, 0E-1F and 7F-9F cannot
     * be used in HTML.
     *
     * @param string $input string to encode for HTML.
     *
     * @return the input string encoded for HTML.
     * 
     * @see <a href="http://en.wikipedia.org/wiki/Character_encodings_in_HTML">HTML Encodings [wikipedia.org]</a>
     * @see <a href="http://www.w3.org/TR/html4/sgml/sgmldecl.html">SGML Specification [w3.org]</a>
     * @see <a href="http://www.w3.org/TR/REC-xml/#charsets">XML Specification [w3.org]</a>
     */
    function encodeForHTML($input);


    /**
     * Encode data for use in HTML attributes.
     *
     * @param string $input string to encode for an HTML attribute.
     *
     * @return the input string encoded for use as an HTML attribute.
     */
    function encodeForHTMLAttribute($input);


    /**
     * Encode data for insertion inside a data value in JavaScript. Putting user
     * data directly inside a script is quite dangerous. Great care must be
     * taken to prevent putting user data directly into script code itself, as
     * no amount of encoding will prevent attacks there.
     *
     * @param string $input string to encode for use in JavaScript.
     *
     * @return the input string encoded for use in JavaScript.
     */
    function encodeForJavaScript($input);


    /**
     * Encode data for insertion inside a data value in a Visual Basic script.
     * Putting user data directly inside a script is quite dangerous. Great care
     * must be taken to prevent putting user data directly into script code
     * itself, as no amount of encoding will prevent attacks there.
     *
     * This method is not recommended as VBScript is only supported by Internet
     * Explorer.
     *
     * @param string $input string to encode for use in VBScript.
     *
     * @return the input string encoded for use in VBScript.
     */
    function encodeForVBScript($input);


    /**
     * Encode input for use in a SQL query, according to the selected codec
     * (appropriate codecs include the MySQLCodec and OracleCodec).
     *
     * This method is not recommended. The use of the PreparedStatement
     * interface is the preferred approach. However, if for some reason this is
     * impossible, then this method is provided as a weaker alternative.
     *
     * @param Codec  $codec an instance of a Codec which will encode the input 
     *                      string for the desired SQL database (e.g. MySQL, Oracle,
     *                      etc.).
     * @param string $input string to encode for use in a SQL query.
     *
     * @return the input string encoded for use in a SQL query.
     */
    function encodeForSQL($codec, $input);


    /**
     * Encode for an operating system command shell according to the selected
     * codec (appropriate codecs include the WindowsCodec and UnixCodec).
     *
     * @param Codec  $codec an instance of a Codec which will encode the input 
     *                      string for the desired operating system (e.g. Windows,
     *                      Unix, etc.).
     * @param string $input string to encode for use in a command shell.
     *
     * @return the input string encoded for use in a command shell.
     */
    function encodeForOS($codec, $input);


    /**
     * Encode data for use in an XPath query.
     *
     * The difficulty with XPath encoding is that XPath has no built in
     * mechanism for escaping characters. It is possible to use XQuery in a
     * parameterized way to prevent injection.
     *
     * For more information, refer to <a
     * href="http://www.ibm.com/developerworks/xml/library/x-xpathinjection.html">
     * this article</a> which specifies the following list of characters as the
     * most dangerous: ^&"*';<>(). <a href=
     * "http://www.packetstormsecurity.org/papers/bypass/Blind_XPath_Injection_20040518.pdf">
     * This paper</a> suggests disallowing ' and " in queries.
     *
     * @param string $input string to be encoded for use in an XPath query.
     *
     * @return the input string encoded for use in an XPath query.
     */
    function encodeForXPath($input);


    /**
     * Encode data for use in an XML element. The implementation should follow
     * the <a href="http://www.w3schools.com/xml/xml_encoding.asp">XML Encoding
     * Standard</a> from the W3C.
     *
     * The use of a real XML parser is strongly encouraged. However, in the
     * hopefully rare case that you need to make sure that data is safe for
     * inclusion in an XML document and cannot use a parse, this method provides
     * a safe mechanism to do so.
     *
     * @param string $input string to be encoded for use in an XML element.
     *
     * @return the input string encoded for use in an XML element.
     */
    function encodeForXML($input);


    /**
     * Encode data for use in an XML attribute. The implementation should follow
     * the <a href="http://www.w3schools.com/xml/xml_encoding.asp">XML Encoding
     * Standard</a> from the W3C.
     *
     * The use of a real XML parser is highly encouraged. However, in the
     * hopefully rare case that you need to make sure that data is safe for
     * inclusion in an XML document and cannot use a parse, this method provides
     * a safe mechanism to do so.
     *
     * @param string $input string to be encoded for use as an XML attribute.
     *
     * @return the input string encoded for use in an XML attribute.
     */
    function encodeForXMLAttribute($input);


    /**
     * Encode for use in a URL. This method performs <a
     * href="http://en.wikipedia.org/wiki/Percent-encoding">URL encoding</a>
     * on the entire string.
     *
     * @param string $input string to be encoded for use in a URL.
     *
     * @return the input string encoded for use in a URL.
     */
    function encodeForURL($input);


    /**
     * Decode from URL. Implementations should first canonicalize and detect any
     * double-encoding. If this check passes, then the data is decoded using URL
     * decoding.
     *
     * @param string $input string to be decoded.
     *
     * @return the input string decoded from URL.
     */
    function decodeFromURL($input);


    /**
     * Encode data with Base64 encoding.
     *
     * @param string $input string to encode for Base64.
     * @param bool   $wrap  boolean the encoder will wrap lines every 64 characters 
     *                      of output if true.
     *
     * @return the input string encoded for Base64.
     */
    function encodeForBase64($input, $wrap = false);


    /**
     * Decode data encoded with Base64 encoding.
     *
     * @param string $input string to be decoded.
     *
     * @return the input string decoded from Base64.
     */
    function decodeFromBase64($input);

}
