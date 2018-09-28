<div class="page-title">Discussion of Back Button</div>

<?php include_once (__ROOT__.'/includes/back-button.inc');?>
<?php include_once (__ROOT__.'/includes/hints/hints-menu-wrapper.inc'); ?>

<table style="margin-left:auto; margin-right:auto; width: 600px;">
	<tr>
		<td class="form-header">Discussion of Back Button</td>
	</tr>
	<tr><td></td></tr>
	<tr>
		<td>The large back button image appears automatically on most pages in the site. If the image is clicked
		the user is redirected to the previous page. The button works by executing a javascript statement which 
		sets document.location.href equal to the HTTP header referrer. The HTTP referrer is automatically
		set and sent by the browser. Some browsers allow the referrer to be set. In all cases, the user
		can alter the referrer using an interception proxy. A mallicious agent can override the referrer using
		a machine in the middle attack.</td>
	</tr>
	<tr><td></td></tr>
	<tr>
		<td style="text-align:center;">
			Alter the HTTP referrer to a page other than the one intended such as www.google.com in order 
			to redirect a user to an arbitrary page.
			<br><br>
			Alter the HTTP referrer to be a valid JavaScript statement in order to execute a XSS attack.
			<br><br>
			Alter the referrer to break out of the JavaScript context then write HTML to the page to execute
			and HTML injection attack.
		</td>
	</tr>
	<tr><td>&nbsp;</td></tr>
</table>