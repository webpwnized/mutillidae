<style>
	a{
		font-weight: bold;
	}
</style>

<?php
	/* Check if required software is installed. Issue warning if not. */
 
	if (!$RequiredSoftwareHandler->isPHPCurlIsInstalled()){
		echo $RequiredSoftwareHandler->getNoCurlAdviceBasedOnOperatingSystem();
	}// end if

	if (!$RequiredSoftwareHandler->isPHPJSONIsInstalled()){
		echo $RequiredSoftwareHandler->getNoJSONAdviceBasedOnOperatingSystem();
	}// end if
?>

<div style=" width: 750px; overflow: hidden;">
<?php include_once (__ROOT__.'/includes/hints/hints-menu-wrapper.inc'); ?>
<span style="float: right">
	<img src="images/arrow-45-degree-left-up.png" style="margin-right: 5px" />
	<span class="label" style="float: right;">TIP:&nbsp;
	<span style="float: right; text-align: center;">Click 
	<span style="color: blue;font-style: italic;">Hint and Videos</span><br/>on each page</span></span>
</span>
</div>

<table style="margin:0px;margin-top:5px;">
 	<tr>
	    <td>
			<a title="Usage Instructions" href="./index.php?page=documentation/usage-instructions.php">
				<img alt="What Should I Do?" align="middle" src="./images/question-mark-40-61.png" />
				What Should I Do?
			</a>
		</td>
		<td>
			<a href="documentation/change-log.txt" target="_blank">
				<img alt="See the latest vulnerabilities in Mutillidae" align="middle" src="./images/new-icon-48-48.png" />
				What's New? Click Here
			</a>
		</td>
	</tr>
	<tr><td>&nbsp;</td></tr>
	<tr>
		<td>
			<?php include_once './includes/help-button.inc';?>
		</td>
		<td>
			<a href="./index.php?page=./documentation/vulnerabilities.php">
				<img alt="Help" align="middle" src="./images/siren-48-48.png" />
				Listing of vulnerabilities
			</a>
		</td>
	</tr>
	<tr><td>&nbsp;</td></tr>
	<tr>
		<td>
			<a href="http://www.youtube.com/user/webpwnized" target="_blank">
			<img align="middle" alt="Webpwnized YouTube Channel" src="./images/youtube-play-icon-40-40.png" />
				Video Tutorials
			</a>
		</td>
		<td>
			<a href="https://twitter.com/webpwnized" target="_blank">
				<img align="middle" alt="Webpwnized Twitter Channel" src="./images/twitter-bird-48-48.png" />
				Release Announcements
			</a>
		</td>
	</tr>
	<tr><td>&nbsp;</td></tr>
	<tr>
		<td class="label" title="Latest Version">
			<img alt="Latest Version" align="middle" src="./images/installation-icon-48-48.png" />
			<a title="Latest Version" href="https://github.com/webpwnized/mutillidae" target="_blank">Latest Version</a>
		</td>
		<td>
			<a href="documentation/mutillidae-test-scripts.txt" target="_blank">
				<img alt="Helpful hints and scripts" align="middle" src="./images/help-icon-48-48.png" />
				Helpful hints and scripts
			</a>
		</td>
	</tr>
	<tr><td>&nbsp;</td></tr>
	<tr>
		<td>
			<a href="https://github.com/webpwnized/mutillidae" target="_blank">
				<img align="middle" alt="Feature Requests" src="./images/tools-icon-48-48.png" />
				<a href="https://addons.mozilla.org/en-US/firefox/collections/jdruin/pro-web-developer-qa-pack/" target="_blank">Some Useful Firefox Add-ons</a>
			</a>
		</td>
		<td>
			<a href="" onclick="alert('The email account is webpwnized. Its a gmail account.');">
				<img alt="Help" align="middle" src="./images/mail-icon-48-48.png" />
				Bug Report Email Address 
			</a>
		</td>
	</tr>
</table>
