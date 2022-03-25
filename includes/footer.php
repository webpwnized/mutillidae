		</div>
    </div>
</div>

<!-- End Content -->

<!-- Section - Footer -->
<?php

    $lUserAgentString = "";
    if(isset($_SERVER['HTTP_USER_AGENT'])){
        $lUserAgentString = $_SERVER['HTTP_USER_AGENT'];
    }// end if

	switch ($_SESSION["security-level"]){
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

	   		<footer class="container-fluid bg-dark text-white">
				   <div class="row p-3 ">
					   
					   <div class="col text-center">
						   <span class="span">Browser: <?php echo $lUserAgentString; ?></span>
						   <br>
						   <span class="span"><?php echo $lPHPVersion; ?></span>
					   </div>
					   
				   </div>
			</footer>

<!-- End Footer -->
		
		<!-- Section - Javascript -->
		<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
    </body>
</html>