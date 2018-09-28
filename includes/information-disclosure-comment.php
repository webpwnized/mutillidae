<?php
/* ------------------------------------------
 * HTML\JAVASCRIPT COMMENTS
* ------------------------------------------ */
/* In general, HTML and JavaScript comments are to
 * be avoided. Commenting is an excellent practice,
* but the comments should be kept on the server
* where they belong. To accomplish this, simply
* use the frameworks comment tags rather than
* the HTML and JavaScript style comments.
*/
switch ($_SESSION["security-level"]){
case "0": // This code is insecure
	case "1":
		echo '
		<!-- I think the database password is set to blank or perhaps samurai.
			It depends on whether you installed this web app from irongeeks site or
			are using it inside Kevin Johnsons Samurai web testing framework.
			It is ok to put the password in HTML comments because no user will ever see
			this comment. I remember that security instructor saying we should use the
			framework comment symbols (ASP.NET, JAVA, PHP, Etc.)
			rather than HTML comments, but we all know those
			security instructors are just making all this up. -->';
   		break;
	  
   		case "2":
   		case "3":
		case "4":
		case "5": // This code is fairly secure
		/*
		* Note: Notice these are PHP comments rather than client side comments.
		* I think the database password is set to blank or perhaps samurai.
		*/
		break;
}// end switch
?>