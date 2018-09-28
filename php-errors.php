<div class="page-title">Usage Instructions</div>

<?php include_once (__ROOT__.'/includes/back-button.inc');?>

<span>
	Get rid of PHP "strict" errors. They are not compatible with the OWASP ESAPI classes in use in Mutillidae 2.0. The
	error modifies headers disrupting functionality so this is not simply an annoyance issue. 
	To do this, go to the PHP.INI file and change the line that reads "error_reporting = E_ALL | E_STRICT" to 
	"error_reporting = E_ALL &amp; ~E_NOTICE &amp; ~E_WARNING &amp; ~E_DEPRECIATED". Once the modification is complete,
	restart the Apache service. If you are not sure how to restart the service, reboot.
	<br/><br/>
	Important note: If you use XAMPP Lite or various version of XAMPP on various operating systems, the path for your 
	php.ini file may vary. You may even have multiple php.ini files in which case try to modify the one in the Apache
	directory first, then the one in the PHP file if that doesnt do the trick.
	<br/><br/>
	Windows possible default location C:\xampp\php\php.ini, C:\XamppLite\PHP\php.ini, others
	Linux possible default locations: /XamppLite/PHP/php.ini, /XamppLite/apache/bin/php.ini, others 
</span>
