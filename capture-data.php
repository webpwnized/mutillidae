<?php include_once (dirname(__FILE__).'/includes/capture-data.php');?>

<link rel="stylesheet" type="text/css" href="./styles/global-styles.css" />
<div class="page-title">Capture Data</div>

<?php include_once (__ROOT__.'/includes/back-button.inc');?>
<?php include_once (__ROOT__.'/includes/hints/hints-menu-wrapper.inc'); ?>

<!-- BEGIN HTML OUTPUT  -->

<div>
	<a href="./index.php?page=captured-data.php" style="text-decoration: none;">
	<img style="vertical-align: middle;" src="./images/cage-48-48.png" />
	<span style="font-weight:bold; cursor: pointer;">View Captured Data</span>
	</a>
</div>
<table style="margin-left:auto; margin-right:auto; width: 650px;">
	<tr>
		<td class="form-header">Data Capture Page</td>
	</tr>
	<tr><td>&nbsp;</td></tr>
	<tr>
		<td>
			This page is designed to capture any parameters sent and store them in a file and a database table. It loops through
			the POST and GET parameters and records them to a file named <span class="label"><?php print $lFilename; ?></span>. On this system, the
			file should be found at <span class="label"><?php print $lFilepath; ?></span>. The page
			also tries to store the captured data in a database table named captured_data and <a href="./index.php?page=show-log.php">logs</a> the captured data. There is another page named
			<a href="index.php?page=captured-data.php">captured-data.php</a> that attempts to list the contents of this table.
		</td>
	</tr>
	<tr><td>&nbsp;</td></tr>
	<tr>
		<th>
			The data captured on this request is: <?php print $lCapturedData; ?>
		</th>
	</tr>
	<tr><td>&nbsp;</td></tr>
	<tr>
		<td style="text-align:center;">
			Would it be possible to hack the hacker? Assume the hacker will view the captured requests with a web browser.
		</td>
	</tr>
	<tr><td>&nbsp;</td></tr>
</table>