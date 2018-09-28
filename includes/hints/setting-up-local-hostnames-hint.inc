<?php 
        $lHostname = $_SERVER["SERVER_NAME"];
        $lHomepage = str_replace($_SERVER["CONTEXT_DOCUMENT_ROOT"], "", $_SERVER["SCRIPT_FILENAME"]) . "?page=home.php";
        $lHomepageURL = "https://" . $lHostname . $lHomepage
?>

<br/><br/>
<span class="report-header">Overview</span>
<br/><br/>
Local hostnames allow a site to have a DNS name without the need to set up DNS
<br/><br/>
<span class="report-header">Creating Local Hostname</span>
<br/><br/>
You may want to give mutillidae a hostname so it can be accessed more easily.
For example, the following command would create a hostname that can stand in for
the IP address 127.0.0.1
<pre>
echo -e "\n127.0.0.1\tmutillidae.local" &gt;&gt; /etc/hosts
</pre>
Then the site could be access at <span class="label">http://127.0.0.1<?php echo $lHomepage; ?></span> or 
<span class="label">http://mutillidae.local<?php echo $lHomepage; ?></span>
<br/><br/>