<div class="page-title">Cache Control</div>

<?php include_once (__ROOT__.'/includes/back-button.inc');?>
<?php include_once (__ROOT__.'/includes/hints/hints-menu-wrapper.inc'); ?>

<table style="margin-left:auto; margin-right:auto; width: 600px;">
	<tr>
		<td class="form-header">Cache Control</td>
	</tr>
	<tr><td></td></tr>
	<tr>
		<td>
			<span class="report-header">Reconnaissance</span>
			<br/><br/>
			Web applications may cache information locally to increase performance. 
			Caching a copy on client avoids retransmission and caching is useful for 
			images and static content.
			<br/><br/>
			Dynamic (i.e. interactive) pages such as forms tend to collect or display sensitive information.
			Some information is sensitive in any context such as SSN, CC, user profile, etc.
			<br/><br/>
			Some information may not be sensitive to the custodian (i.e. corporation, government) 
			but is sensitive to the owner such as pharmacy invoice, travel arrangements, etc.
			<br/><br/>
			Regardless of sensitivity, information leakage may raise privacy concerns. 
			Any content from a domain reveals the user visited the page.
			Even when content itself not sensitive, disclosing browsing history may be privacy violation.
			<br/><br/>
			Cache controls must be used when the content-type indicates the content may 
			contain user data. Of particular concern are media types that carry user data like
			HTML, JSON, XML, etc. Browsers also cache documents. Document caching leaves 
			document on the browser. This may result in information disclosure of sensitive information.
			<br/><br/>
			<span class="report-header">Exploitation</span>
			<br/><br/>
			<span class="label">How to view cached items in Firefox</span>
			<br/><br/>
			Type <span class="label">about:cache</span> in the address bar
			<br/><br/>
			<span class="label">How to view cached items in Internet Explorer</span>
			<br/>
			<ul>
			<li>In the Tools menu, choose <span class="label">Internet Options</span></li>
			<li>On the General tab under <span class="label">Temporary Internet Files</span>, click the <span class="label">Settings</span> button</li>
			<li>From the Settings dialog, click the <span class="label">View Files</span> button</li>
			</ul>
			Nirsoft <span class="label">IE CacheView</span> also useful
			<br/><br/>
			<span class="report-header">Reporting</span>
			<br/><br/>	
			RFC-7234 from the Internet Engineering Task Force (IETF) specifies caching controls.
			HTTP headers are used to specify caching directives (Section 5.2.1).
			"Cache-Control" is standard for HTTP/1.1.
			"Pragma": provides backwards compatibility with HTTP/1.0 clients.
			<br/><br/>
			Strategy for Implementing Cache Control
			<br/><br/>
			The correct cache-control to use depends on the type of document.
			Browsers can natively parse HTML, JSON, XML, CSS, JavaScript and other formats.
			Document formats such as PDF, DOCX, XLSX and PPTX must be handed off to other applications.
			Native content cache-control (aka "forms cache control") is used when the document 
			is a type the browser parses natively:
			<br/><br/>
			<span class="label">Cache-Control: no-store, no-cache.</span>
			<br/><br/>
			Static document cache-control is used when the static document is handled by an external application:
			<br/><br/>
			<span class="label">Cache-Control: no-store, no-cache, max-age=0, must-revalidate.</span>
			<br/><br/>
			URI tagging or streaming document cache-control is used for streamed content.
			<br/>
		</td>
	</tr>
</table>