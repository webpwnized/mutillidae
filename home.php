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
			<span class="material-icons align-middle">
				help
			</span>
			<strong>TIP</strong> <br>
			<hr>
			Click <strong><em>Hint and Videos</em></strong> on each page
			<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
			<?php include_once (__ROOT__.'/includes/hints/hints-menu-wrapper.inc'); ?>
		</div>

	</div>
</div>

<!-- Section - Row 1 -->

<div class="row">
	<div class="col">
		<div class="card">
			<div class="card-body">
			<a title="Usage Instructions" href="./index.php?page=documentation/usage-instructions.php">
				
				<span class="material-icons md-48 align-middle text-primary">
					help
				</span>
				
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
				
				<span class="material-icons md-48 align-middle text-danger">
					security
				</span>
				Listing of vulnerabilities
			</a>
			</div>
		</div>
	</div>
	<div class="col">
		<div class="card">
			<div class="card-body">
			<a href="http://www.youtube.com/user/webpwnized" target="_blank">
			
				<span class="material-icons md-48 align-middle text-danger">
					ondemand_video
				</span>
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
				<span class="material-icons md-48 align-middle text-warning">
				new_releases
				</span>
				Release Announcements
			</a>
			</div>
		</div>
	</div>
	<div class="col">
		<div class="card">
			<div class="card-body">
			<span class="material-icons md-48 align-middle">
				settings
			</span>
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
			<span class="material-icons md-48 align-middle">
				assignment
			</span>
				Helpful hints and scripts
			</a>
			</div>
		</div>
	</div>
	<div class="col">
		<div class="card">
			<div class="card-body">
			<a href="configuration/openldap/mutillidae.ldif" target="_blank">
			<span class="material-icons md-48 align-middle text-primary">
			playlist_add_check_circle
				</span>
				Mutillidae LDIF File
			</a>
			</div>
		</div>
	</div>
</div>


