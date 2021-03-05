			<br/><br/>
			<span class="report-header">Overview</span>
			<br/><br/>
			Some web applications rely on client-side controls such as disabling form fields,
			limiting field size, JavaScript validation or other controls to prevent the
			user from sending malicious input. In all cases, any client-side control can be bypassed
			or ignored entirely.
			<br/><br/>
			Not all applications that use client-side controls are vulnerable.
			Application which pair all client-side controls with an equivalent server-side version of
			the control may be secure. Quality applications often mirror all server-side
			controls with the client-side counterpart to reduce network traffic, prevent honest users
			from sending known-bad values and provide guidance.
			<br/><br/>
			However applications depending solely on client-side controls are
			often vulnerable to injection and/or attacks on application logic.
<br/><br/>
<a href="#videos" class="label"><img alt="YouTube" src="/images/youtube-play-icon-40-40.png" style="margin-right: 10px;" />Video Tutorials</a>
<br/><br/>
<span class="report-header">Discovery Methodology (Static source-code analysis)</span>
<br/><br/>
Applications using client-side controls will write the controls using client-side
languages such as HTML, JavaScript, Cascading Style Sheets and other technologies. By
examining the source code for keywords, it is possible to locate client-side controls. While
not an exhaustive list, the following keywords may be helpful to locate client-side
controls.
<br/><br/>
<span class="label">HTML</span>
<ul>
<li>Button, input, select, textarea, optgroup, option or fieldset elements that have a "disabled" attribute</li>
<li>Input elements with the "required","maxlength","readonly","pattern" or "hidden" attribute</li>
</ul>
This gives to us the following list of keywords
<ul>
	<li>disabled</li>
	<li>required</li>
	<li>maxlength</li>
	<li>readonly</li>
	<li>pattern</li>
	<li>hidden</li>
</ul>
Text boxes allow any characters so tend to be paired with the controls mentioned above.
But some HTML input elements such as select elements (drop-down boxes) are restrictive
by their nature. Developers may employ these "implicit controls" believing users
cannot choose options other than those offered.
<br/><br/>
These are some "implicit controls" that restrict input by their nature.
<ul>
	<li>select</li>
	<li>radio</li>
	<li>checkbox</li>
	<li>file</li>
	<li>number</li>
	<li>range</li>
	<li>button</li>
	<li>submit</li>
</ul>
<span class="label">CSS</span>
<br/><br/>
Styles can prevent browsers from drawing certain inputs, move inputs off screen and
layer inputs under other elements. Some sites may use these tecniques in an attempt
to prevent users from entering data under some conditions.
<br/><br/>
One of the common CSS properties that hides content is "display".
<ul>
	<li>display</li>
</ul>
<span class="label">JavaScript</span>
<br/><br/>
JavaScript tightly integrates with the Document Object Model. JS may hide any element,
alter the contents, interupt form submission or rewrite all or part of a page.
Since any JS, inline or imported, may place restrictions on elements it is neccesary
to examine all JavaScript functions if they are to be bypassed.
<br/><br/>
JavaScript that is associated with onblur, onfocus, onsubmit and onclick events may
be more likely to be implementing client-side controls on average.
<ul>
	<li>onblur</li>
	<li>onfocus</li>
	<li>onsubmit</li>
	<li>onclick</li>
</ul>
Because JavaScript can be complex and be comprised of many lines of code, a
better approach may be to disable or ignore the JavaScript validation entirely.
<br/><br/>
Using the keywords above combined with a tool like grep can locate the most likely
locations of client-side controls in HTML, CSS and JavaScript source code.
<br/><br/>
<span class="report-header">Discovery Methodology (Dynamic source-code analysis)</span>
<br/><br/>
Detection of client-side controls using dynamic testing is relatively easy. Fill in
all fields available, noting any restrictions experienced and submit the request. Fields
that disallow some or all choices are likely client-side controls. Observe if the request
is sent to the web server or blocked inline. If the application refuses to send the
request, usually a JavaScript or HTML5 control (i.e. pattern) is stopping the
request.
<br/><br/>
<span class="report-header">Exploitation</span>
<br/><br/>
It is worth noting bypassing client-side controls is not standard practice. Usually the
controls are ignored entirely by capturing a "valid" request/response pair using an
interception proxy such as Burp-Suite. The baseline request is fuzzed, manipulated or attacked one
parameter at a time to generate reponses. Each response can be compared against the
original baseline reponse.
<br/><br/>
Asssuming bypass of individual client-side controls is desired the following tecniques
are one possible solution.
<br/><br/>
<span class="label">Tools</span>
<br/><br/>
There are many plug-ins and add-ons that are useful. For Firefox some can be found in
<a href="https://addons.mozilla.org/en-US/firefox/collections/jdruin/pro-web-developer-qa-pack/">this
Mozilla Add-on Collection</a>. However the developer tools built-into modern browsers may be
the best choice for most testing.
<ul>
	<li>Firefox Web Developer Tools</li>
	<li>Safari Web Inspector</li>
	<li>Internet Explorer Developer Tools</li>
</ul>
<br/>
<span class="label">HTML</span>
<br/><br/>
The following attribute values can be changed to remove restrictions
<ul>
	<li>maxlength</li>
	<li>pattern</li>
	<li>hidden (change to "text")</li>
	<li>value</li>
</ul>
The following attributes can be deleted to remove restrictions
<ul>
	<li>disabled</li>
	<li>required</li>
	<li>readonly</li>
	<li>pattern</li>
</ul>
HTML input elements which are implicit controls can be bypassed using the same techniques
<br/><br/>
Select elements contain child elements known as "options". The value sent for the
select element is the value of the option chosen. To send a different value than
the options offered, either add a new option with the desired value or change the
value of an existing option. When done, pick the option that has the value wanted.
<br/><br/>
Radio buttons and checkboxes are similar except the value sent is the value of the
checkbox or radio button itself. Again either add a new checkbox/radio button or
change the value of an existing checkbox/radio button.
<br/><br/>
The input elements of type file, number, range, button and submit have an attribute
called "value" that is sent to the server when the form is submitted. Altering these
values is trivial and is the easiest way to bypass these controls.
<br/><br/>
<span class="label">CSS</span>
<br/><br/>
Settings the CSS property "display" to block, inline or and empty string will allow the
browser to display elements in the hidden scope
<br/><br/>
<span class="label">JavaScript</span>
<br/><br/>
JavaScript can generally be dealth with in one of three ways:
<br/><br/>
<span class="label">Rewrite the JavaScript</span>
<br/><br/>
Since JavaScript is under the control of the user, it is possible to create, alter or delete
JavaScript code.
<br/><br/>
<span class="label">Delete or negate the event that triggers the JavaScript</span>
<br/><br/>
JavaScript that performs validation is usually called by the browser in response to an event
since it is neccesary for the user to enter the value before the value can be validated.
Deleting this event or setting the event to TRUE may be an easy way to circumvent validation
<br/><br/>
<span class="label">Submit requests outside the browser using proxy</span>
<br/><br/>
The easiest and most reliable method to bypass any client-side control is to capture
 a "valid" request/response pair using an interception proxy such as Burp-Suite. By this time
 any JavaScript has already run and can be ignored entirely.
<br/><br/>
<span id="videos" class="report-header">Videos</span>
<br/><br/>
<?php echo $YouTubeVideoHandler->getYouTubeVideo($YouTubeVideoHandler->HowtoInstalldirbonLinux);?>
<?php echo $YouTubeVideoHandler->getYouTubeVideo($YouTubeVideoHandler->HowtoInstallOWASPDirBusteronLinux);?>
<br/><br/>