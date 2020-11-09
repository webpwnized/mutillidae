<?php 
   	switch ($_SESSION["security-level"]){
   		case "0": // This code is insecure
   		case "1": // This code is insecure
   			/* This code is insecure. Direct object references in the form of the "forwardurl"
   			 parameter give the user complete control of the input. Contrary to popular belief, 
   			 input validation, blacklisting, etc is not the best defense. The best defenses are 
   			 probably secure 100% of the time. For direct object references, there are two defenses.
   			 Authorization via ACL or Entitlements is used when transaction requires authentication.
   			 This transaction (forwarding URL) does not require authentication so the other method is used;
   			 mapping. Mapping substitutes a harmless token for the direct object. The direct object in 
   			 this case is the page the user is being forwarded to. We will use mapping to secure this code.
   			
   			 Note: For static links, the best defense is to simply hardcode the links in an anchor tag.
   			 This exercise will use mapping to show how it works, but it should be recognized that 
   			 for giving the user links to click, hardcoding is the best defense.
   			*/ 
			$lOWASPURLReference = "http://www.owasp.org";
			$lKYISSAURLReference = "http://www.issa-kentuckiana.org";
			$lOWASPLouisvilleURLReference = "http://www.owasp.org/index.php/Louisville";
			$lMutillidaeFirefoxAddOnsURLReference = "https://addons.mozilla.org/en-US/firefox/collections/jdruin/pro-web-developer-qa-pack/";
   		break;
    		
   		case "2":
   		case "3":
   		case "4":
   		case "5": // This code is fairly secure
			$lOWASPURLReference = "2";
			$lKYISSAURLReference = "3";
			$lOWASPLouisvilleURLReference = "4";
			$lMutillidaeFirefoxAddOnsURLReference = "10";
  		break;
   	}// end switch
?>

<div class="page-title">Credits</div>

<?php include_once (__ROOT__.'/includes/back-button.inc');?>
<?php include_once (__ROOT__.'/includes/hints/hints-menu-wrapper.inc'); ?>

<div class="label">Developed by <a href="https://twitter.com/webpwnized" target="_blank">Jeremy "webpwnized" Druin</a>. Based on Mutillidae 1.0 from Adrian &quot;<a href="http://www.irongeek.com" target="_blank">Irongeek</a>&quot; Crenshaw.</div>
<div>&nbsp;</div>
<div class="label"><a href="index.php?page=redirectandlog.php&forwardurl=<?php echo $lOWASPURLReference; ?>">OWASP</a></div>
<div class="label"><a href="index.php?page=redirectandlog.php&forwardurl=<?php echo $lKYISSAURLReference; ?>">ISSA Kentuckiana</a></div>
<div class="label"><a href="index.php?page=redirectandlog.php&forwardurl=<?php echo $lOWASPLouisvilleURLReference; ?>">OWASP Louisville</a></div>
<div class="label"><a href="index.php?page=redirectandlog.php&forwardurl=<?php echo $lMutillidaeFirefoxAddOnsURLReference; ?>">Helpful Firefox Add-Ons</a></div>
