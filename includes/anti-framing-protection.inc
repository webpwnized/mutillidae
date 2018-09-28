<?php
   	/* ------------------------------------------
    * ANTI-CLICK-JACKING (Older Browsers)
    * ------------------------------------------ */
  	/* To prevent click-jacking and IE6 key-log-via-framing issue
  	 * we can instruct the browser that it should not frame our site. 
 	 * Unfortuneately only the latest browsers support this option.
	 * There are javascript frame-buster options that work reasonably well
	 * although the arms race continues. Use the x-frame-options as the
	 * primary defense and include the javascript frame-buster to help
	 * with older browsers.
	 */
	echo "<script type=\"text/javascript\">if(top != self) top.location.replace(location);</script>";
?>