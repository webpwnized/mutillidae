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

	    /*int*/ $lStrLen = strlen($pString);
	    /*string*/ $lEncodedString = "";
	    for ($i = 0; $i < $lStrLen; $i++){
	        if (ctype_alnum($pString[$i])){
	            $lEncodedString.=$pString[$i];
	        }else{
	            switch ($pType) {
	                case self::cHTML || self::cXML:
	                    $lEncodedString.=self::cXMLHEXENCPREFIX.strval(bin2hex($pString[$i])).self::cSEMICOLON;
	                    break;
	                case self::cURL:
	                    $lEncodedString.=self::cPERCENT.strval(bin2hex($pString[$i]));
	                    break;
	                case self::cCSS || self::cLDAP:
	                    $lEncodedString.=self::cBACKSLASH.strval(bin2hex($pString[$i]));
	                    break;
	                case self::cJS:
	                    $lEncodedString.=self::cJAVASCRIPTHEXENCPREFIX.strval(bin2hex($pString[$i]));
	                    break;
	            }//end switch
	        }//end if
	    }//end for
	    return $lEncodedString;
	}//end private function

	public function encodeForLDAP(/*string*/ $pString) {
	    return $this->encode($pString, self::cLDAP);
	}//end private function

	public function encodeForCSS(/*string*/ $pString) {
	    return $this->encode($pString, self::cCSS);
	}//end private function

	public function encodeForHTML(/*string*/ $pString) {
	    return $this->encode($pString, self::cHTML);
	}//end private function

	public function encodeForXML(/*string*/ $pString) {
	    return $this->encode($pString, self::cXML);
	}//end private function

	public function encodeForXPath(/*string*/ $pString) {
	    return $this->encode($pString, self::cXML);
	}//end private function

	public function encodeForJavaScript(/*string*/ $pString) {
	    return $this->encode($pString, self::cJS);
	}//end private function

	public function encodeForURL(/*string*/ $pString) {
	    return $this->encode($pString, self::cURL);
	}//end private function

}// end class
