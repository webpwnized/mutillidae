<div class="page-title">SQLMap Practice Targets</div>

<?php include_once (__ROOT__.'/includes/back-button.inc');?>
<?php include_once (__ROOT__.'/includes/hints/hints-menu-wrapper.inc'); ?>

<table style="margin-left:auto; margin-right:auto; width: 600px;">
	<tr>
		<td class="form-header">SQLMap Practice Targets</td>
	</tr>
	<tr><td></td></tr>
	<tr>
		<td>
			Several pages in this training environment have SQL injection flaws added. A few listed below
			are the easiest on which to practice using <a href="http://sqlmap.org/" target="_blank">sqlmap</a>;
			an advanced, automated sql injection audit tool.
			<br/>
			<ul style="font-weight:bold;">
				<li><a href="./index.php?page=login.php">Login</a><br/></li>
				<li><a href="./index.php?page=view-someones-blog.php">View Someones Blog</a><br/></li>
				<li><a href="./index.php?page=user-info.php">User Info</a></li>
			</ul>
		</td>
	</tr>
	<tr>
		<td>
			A <a href="http://www.youtube.com/watch?v=vTB3Ze901pM" target="_blank">video using SQLMap</a> against the 
			OWASP Mutillidae II login page is available on the webpwnized YouTube
			channel. Additional help is available on the bottom of this page 
			when the "Hints" are enabled.
		</td>
	</tr>
</table>