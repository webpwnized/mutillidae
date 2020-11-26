
<script src="./javascript/follow-mouse.js"></script>
<div class="page-title">Page Viewer</div>

<?php include_once (__ROOT__.'/includes/back-button.inc');?>
<?php include_once (__ROOT__.'/includes/hints/hints-menu-wrapper.inc'); ?>

<table>
	<tr>
		<td colspan="2" class="form-header">Click on portion of picture to enlarge</td>
	</tr>
	<tr><td></td></tr>
	<tr><td>Starting with the mouse off of the picture, move the mouse over the picture, then click to enlarge a portion of the picture.</td></tr>
</table>

<iframe
	id="id-iframe" 
	width="500px" 
	height="600px" 
	src="rene-magritte.php" 
	style="margin-left:auto; margin-right:auto; border:none; overflow:hidden;"
	>
</iframe>

<div id="id-hover-div" class="click-jacking-button"
onclick="window.alert('This page has been hijacked by the Mutillidae development team.');document.location.href='https://github.com/webpwnized/mutillidae';"
>
Giant Invisible Click-Jacking Button 
</div>

<script>
	objHoverDiv = document.getElementById('id-hover-div');
</script>
	