<?php

    $lUserAgentString = "";
    if(isset($_SERVER['HTTP_USER_AGENT'])){
        $lUserAgentString = $_SERVER['HTTP_USER_AGENT'];
    }// end if

	switch ($_SESSION["security-level"]){
		default: // Default case: This code is insecure
		case "0": // This code is insecure
		case "1": // This code is insecure
			// DO NOTHING: This is equivalent to using client side security
			$lPHPVersion = "PHP Version: " . phpversion();
   		break;

   		case "2":
   		case "3":
   		case "4":
   		case "5": // This code is fairly secure
   			// encode the entire message following OWASP standards
   			// this is HTML encoding because we are outputting data into HTML
   		    $lUserAgentString = $Encoder->encodeForHTML($lUserAgentString);
			$lPHPVersion = "PHP Version: Not Available (Secure mode doesn't reveal the server version)";
		break;
   	}// end switch
?>
				<!-- End Content -->
    			</td>
    		</tr>
    		<tr class="main-table-frame-dark">
    			<td colspan="2">
    				Browser: <?php echo $lUserAgentString; ?>
    				<br/>
    				<?php echo $lPHPVersion; ?>
    			</td>
    		</tr>
    	</table>
    </body>
</html>