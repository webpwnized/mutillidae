<?php
	try {	    	
		switch ($_SESSION["security-level"]){
	   		case "0": // This code is insecure
	   		case "1": // This code is insecure 
	   			/* This code is insecure. Direct object references in the form of the "forwardurl"
	   			 parameter give the user complete control of the input. Contrary to popular belief, 
	   			 input validation, blacklisting, etc is not the best defense. The best defenses are 
	   			 provably secure 100% of the time. For direct object references, there are two defenses.
	   			 Authorization via ACL or Entitlements is used when transaction requires authentication.
	   			 This transaction (forwarding URL) does not require authentication so the other method is used;
	   			 mapping. Mapping substitutes a harmless token for the direct object. The direct object in 
	   			 this case is the page the user is being forwarded to. We will use mapping to secure this code.
	   			
	   			 Note: For static links, the best defense is to simply hardcode the links in an anchor tag.
	   			 This exercise will use mapping to show how it works, but it should be recognized that 
	   			 for giving the user links to click, hardcoding is the best defense.
	   			*/
	   			
	   			/* insecure: this would take input from GET or POST. This can result in an HTTP Parameter Polution
	   			 * attack. If a site uses POST, then grab input from _POST. Use _GET for gets. HPP can
	   			 * occur more easily when input is ambiguous.
	   			 * 
	   			 * Also, the web is weakly typed. All data is strings. It doesnt matter what the developers
	   			 * thinks the input is (int, string, char, etc.). The fact is that HTTP is text. if the 
	   			 * "forwardurl" is expected to be integer, it should be validated as such. If string, then 
	   			 * validate as string. 
	   			 */
	   			$forwardurl=$_REQUEST["forwardurl"];
				$LogHandler->writeToLog("Redirected user to: " . $forwardurl);				
				echo '<meta http-equiv="refresh" content="0;url='.$forwardurl.'">';
				//header("Location: " . $forwardurl); /* Redirect browser */
				exit; /* prevent other headers from runnning */				
	   		break;
	    		
	   		case "2":
	   		case "3":
	   		case "4":
	   		case "5": // This code is fairly secure
	   			/* The "forwardurl" is expected to be integer, so validate as such. Also,
	   			 * dont use _REQUEST as this would allow a POSTed "forwardurl" to be sent 
	   			 * along with a URL query parameter "forwardurl" as well. This type of sloppy
	   			 * variable fetching can result in HTTP Parameter Pollution. 
	   			 */
	   			$forwardurl=$_GET["forwardurl"];
	
	   			/* We expect small int. validate positive integer between 0-9.
	   			 * Regex pattern makes sure the user doesnt send in characters that
	   			 * are not actually digits but can be cast to digits.
	   			 */	
	   			$isDigits = (preg_match("/\d{1,2}/", $forwardurl) == 1);    			
	   			if ($isDigits && $forwardurl > 0 && $forwardurl < 11){
					$lURL = "";
					/* Insecure Direct Object References are patched
					 * by removing the direct object reference all together.
					 * Web applications are "fronts" for services. Some web
					 * sites offer web pages, some offer XML, SOAP, or other
					 * services. In any case, the web site should not "give away"
					 * information about internal objects such as database IDs,
					 * redirection URLs, system file names, or application
					 * paths/configuration.
					 * 
					 * Offer the user harmless tokens instead of actual 
					 * objects. In this case, we use integers to map to
					 * the direct object, which is the forwarding URL.
					 */ 
	   				switch($forwardurl){
	   					case 1: $lURL = "http://www.irongeek.com/";break;
	   					case 2: $lURL = "http://www.owasp.org";break;
	   					case 3: $lURL = "http://www.issa-kentuckiana.org/";break;
	   					case 4: $lURL = "http://www.owasp.org/index.php/Louisville";break;
	   					case 5: $lURL = "http://www.pocodoy.com/blog/";break;
	   					case 6: $lURL = "http://www.room362.com/";break;
	   					case 7: $lURL = "http://www.isd-podcast.com/";break;
	   					case 8: $lURL = "http://pauldotcom.com/";break;
	   					case 9: $lURL = "http://www.php.net/";break;
	   					case 10:$lURL = "https://addons.mozilla.org/en-US/firefox/collections/jdruin/pro-web-developer-qa-pack/";break;
	   				}// end switch($forwardurl)

					$LogHandler->writeToLog("Redirected user to: " . $lURL);				
	   				echo '<meta http-equiv="refresh" content="0;url='.$lURL.'">';/* Redirect browser */
					//header("Location: " . $lURL); /* Redirect browser */
					exit; /* prevent other headers from runnning */		
	   			}else{
	   				throw(new Exception("Expected integer input. Cannot process request. Support team alerted."));
	   			}// end if
	   		break;
	   	}// end switch ($_SESSION["security-level"])

	}catch(Exception $e){
		echo $CustomErrorHandler->FormatError($e, "Error in redirector. Cannot forward URL.");
	}// end try
?>