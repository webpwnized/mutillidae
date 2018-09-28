<?php
	echo '<script type="text/javascript" src="javascript/jQuery/jquery.js"></script>';
	echo '<script type="text/javascript" src="./javascript/jQuery/jquery.balloon.js"></script>';
	echo '<script src="javascript/jQuery/colorbox/jquery.colorbox-min.js"></script>';
	echo '<link rel="stylesheet" href="javascript/jQuery/colorbox/colorbox.css" />';
	echo "
	<script>
		$(document).ready(function () {
			$('a.colorbox').colorbox({width:'700px',opacity:'0.60'});
		});
	</script>
	";
?>