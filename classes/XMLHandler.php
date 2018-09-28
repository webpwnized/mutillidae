<?php
class XMLHandler {

	/* private properties */
	private $mSecurityLevel = 0;
	private $mXMLDataSourcePath = "";
	private $mSimpleXMLElement = null;
	
	/* private methods */
	private function doSetSecurityLevel($pSecurityLevel){
		$this->mSecurityLevel = $pSecurityLevel;
	
		switch ($this->mSecurityLevel){
			case "0": // This code is insecure, we are not encoding output
			case "1": // This code is insecure, we are not encoding output
				break;
	
			case "2":
			case "3":
			case "4":
			case "5": // This code is fairly secure
				break;
		};// end switch
	}// end function

	// Thanks: Tim Tomes (Twitter: @LanMaster53)
	private function doWarpAttributes($attributes) {
		$ret = '';
		foreach($attributes as $a => $b) {
			$ret .= ' '.$a.'="'.$b.'"';
		};
		return $ret;
	}// end function WarpAttributes
	
	// Thanks: Tim Tomes (Twitter: @LanMaster53)
	private function doPrettyPrintXML( SimpleXMLElement $han, $prefix = "") {
		if( count( $han->children() ) < 1 ) {
			return $prefix . "&lt;" . $han->getName() . $this->doWarpAttributes($han->attributes()) . "&gt;" . $han . "&lt;/" . $han->getName() . "&gt;<br />";
		};
		$ret = $prefix . "&lt;" . $han->getName() . $this->doWarpAttributes($han->attributes()) . "&gt;<br />";
		foreach( $han->children() as $key => $child ) {
			$ret .= $this->doPrettyPrintXML($child, $prefix . "    " );
		};
		$ret .= $prefix . "&lt;/" . $han->getName() . "&gt;<br />";
		return $ret;
	}// end function PrettyPrintXML

	private function doParseXMLErrors($lXMLErrors, $lFormat){
		
		$lLineTerminator = "";
		switch ($lFormat) {
			case "TEXT":$lLineTerminator = "\n";break;
			case "HTML":$lLineTerminator = "<br/>";break;
		}//end switch
		
		foreach ($lXMLErrors as $lXMLError) {
			
			$lErrorString = $lLineTerminator . $lLineTerminator;
			
			switch ($lXMLError->level) {
				case LIBXML_ERR_WARNING:$lErrorString .= "Warning ". $lXMLError->code . ": ";break;
				case LIBXML_ERR_ERROR:$lErrorString .= "Error " . $lXMLError->code . ": ";break;
				case LIBXML_ERR_FATAL:$lErrorString .= "Fatal Error " . $lXMLError->code . ": ";break;
			}//end switch
			
			$lErrorString .= trim($lXMLError->message) .
			$lLineTerminator . "  Line: $lXMLError->line" .
			$lLineTerminator . "  Column: $lXMLError->column";
			
			if ($lXMLError->file) {
				$lErrorString .= $lLineTerminator . "  File: " . $lXMLError->file;
			}
			
			return $lErrorString;
			
		}//end foreach
		
	}//end function doParseXMLErrors
	
	/* public methods */
	/* constructor */
	public function __construct($pPathToESAPI, $pSecurityLevel){
		libxml_use_internal_errors(TRUE);
		$this->doSetSecurityLevel($pSecurityLevel);
	}// end function __construct

	public function GetDataSourcePath(){
		return $this->mXMLDataSourcePath;
	}// end function GetDataSourcePath
	
	public function SetDataSource($pDataSourcePath){
		$this->mXMLDataSourcePath = $pDataSourcePath;
		libxml_clear_errors();
		$this->mSimpleXMLElement = simplexml_load_file($this->mXMLDataSourcePath);
		
		$lXMLErrors = libxml_get_errors();
		if(count($lXMLErrors)){
			$lErrorString = $this->doParseXMLErrors($lXMLErrors, "HTML");
			throw new Exception('XML datasource not loaded. This may be caused by failing to set XML datasource with call to SetDataSourcePath(), reserved XML characters in XML data or malformed XML input. '.$lErrorString);
		}//end if
	}// end function SetDataSourcePath

	public function ExecuteXPATHQuery($pXPathQueryString){
	
		$lPrettyXML = "";
		$lXMLQueryResults = null;
		if($this->mSimpleXMLElement){
			$lXMLQueryResults = $this->mSimpleXMLElement->xpath($pXPathQueryString);
		}else{
			throw new Exception('XML datasource not parsed using XPath query string '.$pXPathQueryString.'. This may be caused by failing to set XML datasource with call to SetDataSourcePath().');
		}// end if
		
		foreach ($lXMLQueryResults as $lXMLQueryResult) {
			$lPrettyXML .= $this->doPrettyPrintXML($lXMLQueryResult);
		}// end foreach

		return $lPrettyXML;

	}// end function ExecuteXPATHQuery

}// end class
?>
