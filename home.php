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

<div class="row">
	
	<div class="col-md-4 offset-md-8">
		<div class="alert alert-info alert-dismissible fade show" role="alert">
		<?php include_once (__ROOT__.'/includes/hints/hints-menu-wrapper.inc'); ?>
		<span class="material-icons">
		arrow_upward
		</span>
		TIP: Click <strong><em>Hint and Videos</em></strong> on each page
			<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
		</div>

	</div>
</div>

<!-- Section - Row 1 -->

<div class="row">
	<div class="col">
		<div class="card">
			<div class="card-body">
			<a title="Usage Instructions" href="./index.php?page=documentation/usage-instructions.php">
				<img alt="What Should I Do?" align="middle" src="./images/question-mark-40-61.png" />
				What Should I Do?
			</a>
			</div>
		</div>
	</div>
	<div class="col">
		<div class="card">
			<div class="card-body">
			<?php include_once './includes/help-button.inc';?>
			</div>
		</div>
	</div>
</div>

<!-- Section - Row 2 -->
<div class="row py-3">
	<div class="col">
		<div class="card">
			<div class="card-body">
			<a href="./index.php?page=./documentation/vulnerabilities.php">
				<img alt="Help" align="middle" src="./images/siren-48-48.png" />
				Listing of vulnerabilities
			</a>
			</div>
		</div>
	</div>
	<div class="col">
		<div class="card">
			<div class="card-body">
			<a href="http://www.youtube.com/user/webpwnized" target="_blank">
			<img align="middle" alt="Webpwnized YouTube Channel" src="./images/youtube-play-icon-40-40.png" />
				Video Tutorials
			</a>
			</div>
		</div>
	</div>
</div>

<!-- Section - Row 3 -->

<div class="row py-3">
	<div class="col">
		<div class="card">
			<div class="card-body">
			<a href="https://twitter.com/webpwnized" target="_blank">
				<img align="middle" alt="Webpwnized Twitter Channel" src="./images/twitter-bird-48-48.png" />
				Release Announcements
			</a>
			</div>
		</div>
	</div>
	<div class="col">
		<div class="card">
			<div class="card-body">
			<img alt="Latest Version" align="middle" src="./images/installation-icon-48-48.png" />
			<a title="Latest Version" href="https://github.com/webpwnized/mutillidae" target="_blank">Latest Version</a>
			</div>
		</div>
	</div>
</div>

<!-- Section - Row 4 -->
<div class="row py-3">
	<div class="col">
		<div class="card">
			<div class="card-body">
			<a href="documentation/mutillidae-test-scripts.txt" target="_blank">
				<img alt="Helpful hints and scripts" align="middle" src="./images/help-icon-48-48.png" />
				Helpful hints and scripts
			</a>
			</div>
		</div>
	</div>
	<div class="col">
		<div class="card">
			<div class="card-body">
			<a href="configuration/openldap/mutillidae.ldif" target="_blank">
				<img align="middle" alt="Mutillidae LDIF File" src="./images/ldap-server-48-59.png" />
				Mutillidae LDIF File
			</a>
			</div>
		</div>
	</div>
</div>


