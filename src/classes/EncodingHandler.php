<?php

class EncodingHandler {

    /* ------------------------------------------
     * PROTECTED PROPERTIES
     * ------------------------------------------ */
    protected const cHTML = "HTML";
    protected const cXML = "XML";
    protected const cLDAP = "LDAP";
    protected const cCSS = "CSS";
    protected const cJS = "JS";
    protected const cURL = "URL";

    protected const cBACKSLASH = "\\";
    protected const cPERCENT = "%";
    protected const cJAVASCRIPTHEXENCPREFIX = "\\x";
    protected const cXMLHEXENCPREFIX = "&#x";
    protected const cSEMICOLON = ";";

    /* ------------------------------------------
     * CONSTRUCTOR METHOD
     * ------------------------------------------ */
    public function __construct(){

    }// end function __construct()

    /* ------------------------------------------
     * PRIVATE METHODS
     * ------------------------------------------ */

    /* ------------------------------------------
     * PUBLIC METHODS
     * ------------------------------------------ */
    private function encode(/*string*/ $pString, $pType) {
        // Ensure $pString is a valid string to avoid warnings.
        if (!is_string($pString) || empty($pString)) {
            return ""; // Return an empty string if input is not valid.
        }

        /*int*/ $lStrLen = strlen($pString);
        /*string*/ $lEncodedString = "";
        
        for ($i = 0; $i < $lStrLen; $i++){
            if (ctype_alnum($pString[$i])) {
                $lEncodedString .= $pString[$i];
            } else {
                // Corrected switch case structure for better readability.
                switch ($pType) {
                    case self::cHTML:
                    case self::cXML:
                        $lEncodedString .= self::cXMLHEXENCPREFIX . strval(bin2hex($pString[$i])) . self::cSEMICOLON;
                        break;
                    case self::cURL:
                        $lEncodedString .= self::cPERCENT . strval(bin2hex($pString[$i]));
                        break;
                    case self::cCSS:
                    case self::cLDAP:
                        $lEncodedString .= self::cBACKSLASH . strval(bin2hex($pString[$i]));
                        break;
                    case self::cJS:
                        $lEncodedString .= self::cJAVASCRIPTHEXENCPREFIX . strval(bin2hex($pString[$i]));
                        break;
                    default:
                        // Handle unsupported encoding types gracefully.
                        throw new Exception("Unsupported encoding type: $pType");
                }//end switch
            }//end if
        }//end for

        return $lEncodedString;
    }//end private function

    public function encodeForLDAP(/*string*/ $pString) {
        return $this->encode($pString, self::cLDAP);
    }//end public function

    public function encodeForCSS(/*string*/ $pString) {
        return $this->encode($pString, self::cCSS);
    }//end public function

    public function encodeForHTML(/*string*/ $pString) {
        return $this->encode($pString, self::cHTML);
    }//end public function

    public function encodeForXML(/*string*/ $pString) {
        return $this->encode($pString, self::cXML);
    }//end public function

    public function encodeForXPath(/*string*/ $pString) {
        return $this->encode($pString, self::cXML);
    }//end public function

    public function encodeForJavaScript(/*string*/ $pString) {
        return $this->encode($pString, self::cJS);
    }//end public function

    public function encodeForURL(/*string*/ $pString) {
        return $this->encode($pString, self::cURL);
    }//end public function

}// end class
