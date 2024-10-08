<?php
// Pull in the NuSOAP code
require_once('./lib/nusoap.php');
// Create the server instance
$server = new soap_server();
// Initialize WSDL support
$server->configureWSDL('hellowsdl', 'urn:hellowsdl');
// Register the method to expose
$server->register('hello',                // method name
    array('name' => 'xsd:string'),        // input parameters
    array('return' => 'xsd:string'),      // output parameters
    'urn:hellowsdl',                      // namespace
    'urn:hellowsdl#hello',                // soapaction
    'rpc',                                // style
    'encoded',                            // use
    'Says hello to the caller
	<br/><br/>
	Sample Request (Copy and paste into Burp Repeater)<br/>
		<br/>POST /mutillidae/webservices/soap/ws-hello-world.php HTTP/1.1
		<br/>Accept-Encoding: gzip,deflate
		<br/>Content-Type: text/xml;charset=UTF-8
		<br/>SOAPAction: &quot;urn:hellowsdl#hello&quot;
		<br/>Content-Length: 438
		<br/>Host: localhost
		<br/>Connection: Keep-Alive
		<br/>User-Agent: Apache-HttpClient/4.1.1 (java 1.5)
		<br/>
		<br/>&lt;soapenv:Envelope xmlns:xsi=&quot;http://www.w3.org/2001/XMLSchema-instance&quot; xmlns:xsd=&quot;http://www.w3.org/2001/XMLSchema&quot; xmlns:soapenv=&quot;http://schemas.xmlsoap.org/soap/envelope/&quot; xmlns:urn=&quot;urn:hellowsdl&quot;&gt;
		<br/>   &lt;soapenv:Header/&gt;
		<br/>   &lt;soapenv:Body&gt;
		<br/>      &lt;urn:hello soapenv:encodingStyle=&quot;http://schemas.xmlsoap.org/soap/encoding/&quot;&gt;
		<br/>         &lt;name xsi:type=&quot;xsd:string&quot;&gt;Fred&lt;/name&gt;
		<br/>      &lt;/urn:hello&gt;
		<br/>   &lt;/soapenv:Body&gt;
		<br/>&lt;/soapenv:Envelope&gt;'            // end documentation
);
// Define the method as a PHP function
function hello($name) {
        return 'Hello, ' . $name;
}
// Use the request to (try to) invoke the service
$HTTP_RAW_POST_DATA = isset($HTTP_RAW_POST_DATA) ? $HTTP_RAW_POST_DATA : '';
$php_version = phpversion();
$php_major_version = (int)substr($php_version, 0, 1);
if ($php_major_version >= 7) { 
    $server->service(file_get_contents("php://input"));
} else {
    $server->service($HTTP_RAW_POST_DATA);
}


?>

