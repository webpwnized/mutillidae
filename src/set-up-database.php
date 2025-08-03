<?php

    if (session_status() == PHP_SESSION_NONE){
        session_start();
    }// end if

    if(isset($_SESSION["security-level"])){
        $lSecurityLevel = $_SESSION["security-level"];
    }else{
        $lSecurityLevel = 0;
    }

    //initialize custom error handler
    require_once 'classes/CustomErrorHandler.php';
    if (!isset($CustomErrorHandler)){
        $CustomErrorHandler = new CustomErrorHandler($lSecurityLevel);
    }// end if

    require_once 'classes/MySQLHandler.php';
    $MySQLHandler = new MySQLHandler($lSecurityLevel);
    
	function format($pMessage, $pLevel) {
		$styles = [
			"I" => "database-informative-message",
			"S" => "database-success-message",
			"F" => "database-failure-message",
			"W" => "database-warning-message"
		];
	
		// Use the level-specific style if it exists, otherwise default to "database-informative-message"
		$lStyle = $styles[$pLevel] ?? "database-informative-message";
	
		return "<div class=\"".$lStyle."\">" . htmlspecialchars($pMessage) . "</div>";
	}

	$lErrorDetected = false;
?>

    <!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/1999/REC-html401-19991224/loose.dtd">
	<html lang="en" xml:lang="en">
		<head>
			<title>Set Up Database</title>
			<link rel="shortcut icon" href="./images/favicon.ico" type="image/x-icon" />
    		<link rel="stylesheet" type="text/css" href="./styles/global-styles.css" />
    	</head>
    	<body>
    		<div>&nbsp;</div>
    		<div class="page-title">Setting up the database...</div><br /><br />
    		<div class="label" style="text-align: center;">If you see no error messages, it should be done.</div>
    		<div>&nbsp;</div>
    		<div class="label" style="text-align: center;"><a href="index.php">Continue back to the frontpage.</a></div>
    		<br />
    		<script>
    			try{
    				window.sessionStorage.clear();
    				window.localStorage.clear();
    			}catch(e){
    				alert("Error clearing HTML 5 Local and Session Storage" + e.toString());
    			};
    		</script>
    		<div class="database-success-message">HTML 5 Local and Session Storage cleared unless error popped-up already.</div>
<?php

    try{
    	echo format("Attempting to connect to MySQL server on host " . MySQLHandler::$mMySQLDatabaseHost . " with user name " . MySQLHandler::$mMySQLDatabaseUsername,"I");
    	$MySQLHandler->openDatabaseConnection();
    	echo format("Connected to MySQL server at " . MySQLHandler::$mMySQLDatabaseHost . " as " . MySQLHandler::$mMySQLDatabaseUsername,"I");

    	try{
    		echo format("Preparing to drop database " . MySQLHandler::$mMySQLDatabaseName,"I");
    		$lQueryString = "DROP DATABASE IF EXISTS " . MySQLHandler::$mMySQLDatabaseName;
    		$lQueryResult = $MySQLHandler->executeQuery($lQueryString);
    		if (!$lQueryResult) {
    			$lErrorDetected = true;
    			echo format("Was not able to drop database " . MySQLHandler::$mMySQLDatabaseName,"F");
    		}else{
    			echo format("Executed query 'DROP DATABASE IF EXISTS' for database " . MySQLHandler::$mMySQLDatabaseName . " with result ".$lQueryResult,"S");
    		}// end if
    	}catch(Exception $e){
    		// We do not want error dropping database to derail entire database setup.
    		echo format("Error was reported while attempting to drop database " . MySQLHandler::$mMySQLDatabaseName,"F");
    		echo format("MySQL sometimes throws errors attempting to drop databases. Here is error in case the error is serious.","I");
    		echo $CustomErrorHandler->FormatError($e, $lQueryString);
    	}//end try

    	echo format("Preparing to create database " . MySQLHandler::$mMySQLDatabaseName,"I");
    	$lQueryString = "CREATE DATABASE " . MySQLHandler::$mMySQLDatabaseName;
    	$lQueryResult = $MySQLHandler->executeQuery($lQueryString);
    	if (!$lQueryResult) {
    		$lErrorDetected = true;
    		echo format("Was not able to create database " . MySQLHandler::$mMySQLDatabaseName,"F");
    	}else{
    		echo format("Executed query 'CREATE DATABASE' for database " . MySQLHandler::$mMySQLDatabaseName . " with result ".$lQueryResult,"S");
    	}// end if

    	echo format("Switching to use database " . MySQLHandler::$mMySQLDatabaseName,"I");
    	$lQueryString = "USE " . MySQLHandler::$mMySQLDatabaseName;
    	$lQueryResult = $MySQLHandler->executeQuery($lQueryString);
    	if (!$lQueryResult) {
    		$lErrorDetected = true;
    		echo format("Was not able to use database " . MySQLHandler::$mMySQLDatabaseName,"F");
    	}else{
    		echo format("Executed query 'USE DATABASE' " . MySQLHandler::$mMySQLDatabaseName . " with result ".$lQueryResult,"I");
    	}// end if

		$lQueryString = "
			CREATE TABLE IF NOT EXISTS security_level (
				id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
				level INT NOT NULL DEFAULT 0
			);
		";
		
		$lQueryResult = $MySQLHandler->executeQuery($lQueryString);
		
		if (!$lQueryResult) {
			$lErrorDetected = true;
			echo format("Failed to create 'security_level' table.", "F");
		} else {
			echo format("Successfully created 'security_level' table.", "S");
		
			// Ensure AUTO_INCREMENT starts at 1 (default behavior)
			$lAutoIncrementQuery = "ALTER TABLE security_level AUTO_INCREMENT = 1;";
			$lAutoIncrementResult = $MySQLHandler->executeQuery($lAutoIncrementQuery);
		
			if (!$lAutoIncrementResult) {
				$lErrorDetected = true;
				echo format("Failed to set AUTO_INCREMENT to 1.", "F");
			} else {
				echo format("AUTO_INCREMENT set to start at 1.", "S");
			}
		}
		
		// Optionally insert the initial value
		$lInsertQuery = "INSERT INTO security_level (level) VALUES (0)";
		$lInsertResult = $MySQLHandler->executeQuery($lInsertQuery);
		
		if (!$lInsertResult) {
			$lErrorDetected = true;
			echo format("Failed to insert initial security level.", "F");
		} else {
			echo format("Initial security level set to 0.", "S");
		}

    	$lQueryString = 'CREATE TABLE user_poll_results( '.
    			'cid INT NOT NULL AUTO_INCREMENT, '.
    			'tool_name TEXT, '.
    			'username TEXT, '.
    			'date DATETIME, '.
    			'PRIMARY KEY(cid))';
    	$lQueryResult = $MySQLHandler->executeQuery($lQueryString);
    	if (!$lQueryResult) {
    		$lErrorDetected = true;
			echo format("Failed to create 'user_poll_results' table.", "F");
    	}else{
			echo format("Successfully created 'user_poll_results' table.", "S");
    	}// end if

    	$lQueryString = 'CREATE TABLE blogs_table( '.
    			 'cid INT NOT NULL AUTO_INCREMENT, '.
    	         'blogger_name TEXT, '.
    	         'comment TEXT, '.
    			 'date DATETIME, '.
    			 'PRIMARY KEY(cid))';
    	$lQueryResult = $MySQLHandler->executeQuery($lQueryString);
    	if (!$lQueryResult) {
    		$lErrorDetected = true;
			echo format("Failed to create 'blogs_table' table.", "F");
    	}else{
			echo format("Successfully created 'blogs_table' table.", "S");
    	}// end if

		$lQueryString = 'CREATE TABLE accounts (
				cid INT NOT NULL AUTO_INCREMENT,
				username VARCHAR(255) NOT NULL UNIQUE,
				password VARCHAR(255) NOT NULL,
				mysignature TEXT,
				is_admin BOOLEAN DEFAULT FALSE,
				firstname VARCHAR(255),
				lastname VARCHAR(255),
				client_id CHAR(32) NOT NULL UNIQUE,
				client_secret VARCHAR(64) NOT NULL UNIQUE,
				created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
				PRIMARY KEY(cid)
			)';
		$lQueryResult = $MySQLHandler->executeQuery($lQueryString);
    	if (!$lQueryResult) {
    		$lErrorDetected = true;
			echo format("Failed to create 'accounts' table.", "F");
    	}else{
			echo format("Successfully created 'accounts' table.", "S");
    	}// end if

    	$lQueryString = 'CREATE TABLE hitlog( '.
    			 'cid INT NOT NULL AUTO_INCREMENT, '.
    	         'hostname TEXT, '.
    	         'ip TEXT, '.
    			 'browser TEXT, '.
    			 'referer TEXT, '.
    			 'date DATETIME, '.
    			 'PRIMARY KEY(cid))';
    	$lQueryResult = $MySQLHandler->executeQuery($lQueryString);
    	if (!$lQueryResult) {
    		$lErrorDetected = true;
			echo format("Failed to create 'hitlog' table.", "F");
    	}else{
			echo format("Successfully created 'hitlog' table.", "S");
    	}// end if

		$lQueryString = 'INSERT INTO accounts (username, password, mysignature, is_admin, firstname, lastname, client_id, client_secret) VALUES
			("admin", "adminpass", "g0t r00t?", true, "System", "Administrator", "' . bin2hex(random_bytes(16)) . '", "' . bin2hex(random_bytes(32)) . '"),
			("adrian", "somepassword", "Zombie Films Rock!", true, "Adrian", "Crenshaw", "' . bin2hex(random_bytes(16)) . '", "' . bin2hex(random_bytes(32)) . '"),
			("patches", "tortoise", "meow", false, "Patches", "Pester", "' . bin2hex(random_bytes(16)) . '", "' . bin2hex(random_bytes(32)) . '"),
			("jeremy", "password", "d1373 1337 speak", false, "Jeremy", "Druin", "' . bin2hex(random_bytes(16)) . '", "' . bin2hex(random_bytes(32)) . '"),
			("bryce", "password", "I Love SANS", false, "Bryce", "Galbraith", "' . bin2hex(random_bytes(16)) . '", "' . bin2hex(random_bytes(32)) . '"),
			("samurai", "samurai", "Carving fools", false, "Samurai", "WTF", "' . bin2hex(random_bytes(16)) . '", "' . bin2hex(random_bytes(32)) . '"),
			("dodgerjim", "password", "Rome is burning", false, "Jim", "Rome", "' . bin2hex(random_bytes(16)) . '", "' . bin2hex(random_bytes(32)) . '"),
			("bobby", "password", "Hank is my dad", false, "Bobby", "Hill", "' . bin2hex(random_bytes(16)) . '", "' . bin2hex(random_bytes(32)) . '"),
			("simba", "password", "I am a super-cat", false, "Simba", "Lion", "' . bin2hex(random_bytes(16)) . '", "' . bin2hex(random_bytes(32)) . '"),
			("dreveil", "password", "Preparation H", false, "Dr.", "Evil", "' . bin2hex(random_bytes(16)) . '", "' . bin2hex(random_bytes(32)) . '"),
			("scotty", "password", "Scotty do", false, "Scotty", "Evil", "' . bin2hex(random_bytes(16)) . '", "' . bin2hex(random_bytes(32)) . '"),
			("cal", "password", "C-A-T-S Cats Cats Cats", false, "John", "Calipari", "' . bin2hex(random_bytes(16)) . '", "' . bin2hex(random_bytes(32)) . '"),
			("john", "password", "Do the Duggie!", false, "John", "Wall", "' . bin2hex(random_bytes(16)) . '", "' . bin2hex(random_bytes(32)) . '"),
			("kevin", "42", "Doug Adams rocks", false, "Kevin", "Johnson", "' . bin2hex(random_bytes(16)) . '", "' . bin2hex(random_bytes(32)) . '"),
			("dave", "set", "Bet on S.E.T. FTW", false, "Dave", "Kennedy", "' . bin2hex(random_bytes(16)) . '", "' . bin2hex(random_bytes(32)) . '"),
			("rocky", "stripes", "treats?", false, "Rocky", "Paws", "' . bin2hex(random_bytes(16)) . '", "' . bin2hex(random_bytes(32)) . '"),
			("tim", "lanmaster53", "Because reconnaissance is hard to spell", false, "Tim", "Tomes", "' . bin2hex(random_bytes(16)) . '", "' . bin2hex(random_bytes(32)) . '"),
			("ABaker", "SoSecret", "Muffin tops only", true, "Aaron", "Baker", "' . bin2hex(random_bytes(16)) . '", "' . bin2hex(random_bytes(32)) . '"),
			("PPan", "NotTelling", "Where is Tinker?", false, "Peter", "Pan", "' . bin2hex(random_bytes(16)) . '", "' . bin2hex(random_bytes(32)) . '"),
			("CHook", "JollyRoger", "Gator-hater", false, "Captain", "Hook", "' . bin2hex(random_bytes(16)) . '", "' . bin2hex(random_bytes(32)) . '"),
			("james", "i<3devs", "Occupation: Researcher", false, "James", "Jardine", "' . bin2hex(random_bytes(16)) . '", "' . bin2hex(random_bytes(32)) . '"),
			("ed", "pentest", "Commandline KungFu anyone?", false, "Ed", "Skoudis", "' . bin2hex(random_bytes(16)) . '", "' . bin2hex(random_bytes(32)) . '"),
			("joe", "holly", "Off by one error", false, "Joe", "Holly", "' . bin2hex(random_bytes(16)) . '", "' . bin2hex(random_bytes(32)) . '"),
			("peter", "initech123", "I dont like my job", false, "Peter", "Gibbons", "' . bin2hex(random_bytes(16)) . '", "' . bin2hex(random_bytes(32)) . '"),
			("milton", "stapler", "Wheres my stapler?", false, "Milton", "Waddams", "' . bin2hex(random_bytes(16)) . '", "' . bin2hex(random_bytes(32)) . '"),
			("bill", "tpsreports", "Did you get the memo?", true, "Bill", "Lumbergh", "' . bin2hex(random_bytes(16)) . '", "' . bin2hex(random_bytes(32)) . '"),
			("samir", "nojoke123", "No one can pronounce my last name", false, "Samir", "Nagheenanajar", "' . bin2hex(random_bytes(16)) . '", "' . bin2hex(random_bytes(32)) . '"),
			("michael_b", "michael123", "We fixed the glitch", false, "Michael", "Bolton", "' . bin2hex(random_bytes(16)) . '", "' . bin2hex(random_bytes(32)) . '"),
			("michael_s", "worldsbestboss", "Worlds Best Boss", true, "Michael", "Scott", "' . bin2hex(random_bytes(16)) . '", "' . bin2hex(random_bytes(32)) . '"),
			("jim", "bearsbeatsbattlestar", "Pranking Dwight", false, "Jim", "Halpert", "' . bin2hex(random_bytes(16)) . '", "' . bin2hex(random_bytes(32)) . '"),
			("dwight", "assistant_to_the_regional_manager", "Bears. Beets. Battlestar Galactica.", false, "Dwight", "Schrute", "' . bin2hex(random_bytes(16)) . '", "' . bin2hex(random_bytes(32)) . '"),
			("pam", "pammy123", "Dunder Mifflin", false, "Pam", "Beesly", "' . bin2hex(random_bytes(16)) . '", "' . bin2hex(random_bytes(32)) . '"),
			("ryan", "temp123", "I am the temp", false, "Ryan", "Howard", "' . bin2hex(random_bytes(16)) . '", "' . bin2hex(random_bytes(32)) . '"),
			("derek", "bluestrike", "Im pretty sure theres more to life than being really, really ridiculously good looking", false, "Derek", "Zoolander", "' . bin2hex(random_bytes(16)) . '", "' . bin2hex(random_bytes(32)) . '"),
			("hansel", "hansel123", "Hansel. Hes so hot right now.", false, "Hansel", "", "' . bin2hex(random_bytes(16)) . '", "' . bin2hex(random_bytes(32)) . '"),
			("mugatu", "mugatu123", "I invented the piano key necktie!", true, "Jacobim", "Mugatu", "' . bin2hex(random_bytes(16)) . '", "' . bin2hex(random_bytes(32)) . '"),
			("matilda", "journalist123", "Investigative reporter", false, "Matilda", "Jeffries", "' . bin2hex(random_bytes(16)) . '", "' . bin2hex(random_bytes(32)) . '"),
			("stackhawk", "Kaakaww", "Swooping in for security", false, "Stack", "Hawk", "' . bin2hex(random_bytes(16)) . '", "' . bin2hex(random_bytes(32)) . '"),
			("zoolander", "zoolander123", "I am really, really, really, ridiculously good looking", false, "Derek", "Zoolander", "' . bin2hex(random_bytes(16)) . '", "' . bin2hex(random_bytes(32)) . '"),
			("maury", "maury123", "Youre the guy who cant turn left", false, "Maury", "Ballstein", "' . bin2hex(random_bytes(16)) . '", "' . bin2hex(random_bytes(32)) . '");';
		$lQueryResult = $MySQLHandler->executeQuery($lQueryString);
		if (!$lQueryResult) {
    		$lErrorDetected = true;
			echo format("Failed to insert data into 'accounts' table.", "F");
    	}else{
			echo format("Successfully inserted data into 'accounts' table.", "S");
    	}// end if

		$lQueryString = "INSERT INTO `blogs_table` (`cid`, `blogger_name`, `comment`, `date`) VALUES
			(1, 'adrian', 'Welcome to my crappy blog software. :)', '2009-03-01 22:26:12'),
			(2, 'adrian', 'Looks like I got a lot more work to do. Fun, Fun, Fun!!!', '2009-03-01 22:26:54'),
			(3, 'anonymous', 'An anonymous blog? Huh?', '2009-03-01 22:27:11'),
			(4, 'ed', 'I love me some Netcat!!!', '2009-03-01 22:27:48'),
			(5, 'john', 'Listen to Pauldotcom!', '2009-03-01 22:29:04'),
			(6, 'jeremy', 'Mutillidae is fun', '2009-03-01 22:29:49'),
			(7, 'john', 'Chocolate is GOOD!!!', '2009-03-01 22:30:06'),
			(8, 'admin', 'Fear me, for I am ROOT!', '2009-03-01 22:31:13'),
			(9, 'dave', 'Social Engineering is woot-tastic', '2009-03-01 22:31:13'),
			(10, 'kevin', 'Read more Douglas Adams', '2009-03-01 22:31:13'),
			(11, 'jim', 'Bears eat beets', '2009-03-01 22:31:13'),
			(12, 'michael_s', 'I declare BANKRUPTCY!', '2024-10-07 09:00:00'),
			(13, 'jim', 'Just pulled off the ultimate prank on Dwight.', '2024-10-07 09:05:00'),
			(14, 'pam', 'Art school has been really fulfilling.', '2024-10-07 09:10:00'),
			(15, 'dwight', 'Bears. Beets. Battlestar Galactica.', '2024-10-07 09:15:00'),
			(16, 'ryan', 'Starting my new tech venture.', '2024-10-07 09:20:00'),
			(17, 'peter', 'Today, I didnt really do much work. Feels great.', '2024-10-07 09:25:00'),
			(18, 'milton', 'They took my stapler again...', '2024-10-07 09:30:00'),
			(19, 'bill', 'Did you get the memo?', '2024-10-07 09:35:00'),
			(20, 'samir', 'No one can still pronounce my last name...', '2024-10-07 09:40:00'),
			(21, 'michael_b', 'Its not that Michael Bolton!', '2024-10-07 09:45:00'),
			(22, 'mugatu', 'The Derelicte campaign is going great!', '2024-10-07 09:50:00'),
			(23, 'derek', 'Being ridiculously good looking has its perks.', '2024-10-07 09:55:00')";
		$lQueryResult = $MySQLHandler->executeQuery($lQueryString);
    	if (!$lQueryResult) {
    		$lErrorDetected = true;
			echo format("Failed to insert data into 'blogs_table' table.", "F");
    	}else{
			echo format("Successfully inserted data into 'blogs_table' table.", "S");
    	}// end if

    	$lQueryString = 'CREATE TABLE credit_cards( '.
    			 'ccid INT NOT NULL AUTO_INCREMENT, '.
    	         'ccnumber TEXT, '.
    	         'ccv TEXT, '.
    			 'expiration DATE, '.
    			 'PRIMARY KEY(ccid))';
    	$lQueryResult = $MySQLHandler->executeQuery($lQueryString);
    	if (!$lQueryResult) {
    		$lErrorDetected = true;
			echo format("Failed to create 'credit_cards' table.", "F");
    	}else{
			echo format("Successfully created 'credit_cards' table.", "S");
    	}// end if

		$lQueryString = "INSERT INTO `credit_cards` (`ccid`, `ccnumber`, `ccv`, `expiration`) VALUES
			(1, '4111111111111111', '123', '2024-10-01 10:01:12'), -- Visa Test Card
			(2, '5555555555554444', '321', '2025-04-01 07:00:12'), -- Mastercard Test Card
			(3, '378282246310005', '231', '2026-03-01 11:55:12'), -- American Express Test Card
			(4, '6011111111111117', '456', '2027-06-01 04:33:12'), -- Discover Test Card
			(5, '4222222222222', '789', '2028-11-01 13:31:13'), -- Visa Short Test Card
			(6, '4000002760003184', '123', '2025-08-01 12:00:00'), -- Visa Debit Test Card
			(7, '2223000048400011', '234', '2026-09-01 09:30:45'), -- Mastercard Debit Test Card
			(8, '6011000990139424', '345', '2027-02-01 15:45:30'), -- Discover Debit Test Card
			(9, '4000000000000002', '456', '2025-05-01 08:15:00'), -- Visa Credit Test Card
			(10, '3566002020360505', '567', '2024-12-01 18:20:10'), -- JCB Test Card
			(11, '5038370200000000', '678', '2026-07-01 11:00:00') -- Maestro Test Card";
    	$lQueryResult = $MySQLHandler->executeQuery($lQueryString);
    	if (!$lQueryResult) {
    		$lErrorDetected = true;
			echo format("Failed to insert data into 'credit_cards' table.", "F");
    	}else{
			echo format("Successfully inserted data into 'credit_cards' table.", "S");
    	}// end if

    	$lQueryString =
    			'CREATE TABLE pen_test_tools('.
    			'tool_id INT NOT NULL AUTO_INCREMENT, '.
    	        'tool_name TEXT, '.
    	        'phase_to_use TEXT, '.
    			'tool_type TEXT, '.
    			'comment TEXT, '.
    			'PRIMARY KEY(tool_id))';
    	$lQueryResult = $MySQLHandler->executeQuery($lQueryString);
    	if (!$lQueryResult) {
    		$lErrorDetected = true;
			echo format("Failed to create 'pen_test_tools' table.", "F");
    	}else{
			echo format("Successfully created 'pen_test_tools' table.", "S");
    	}// end if

		$lQueryString = "
		INSERT INTO `pen_test_tools` (`tool_id`, `tool_name`, `phase_to_use`, `tool_type`, `comment`) VALUES
		(1, 'Burp Suite Professional', 'Discovery', 'Interception Proxy', 'Advanced manual testing with automated scanning capabilities.'),
		(2, 'OWASP ZAP', 'Discovery', 'Interception Proxy', 'Free, open-source alternative to Burp Suite with active and passive scanning.'),
		(3, 'Nmap', 'Reconnaissance', 'Network Scanner', 'Highly versatile tool for network discovery and security auditing.'),
		(4, 'Nuclei', 'Discovery', 'Scanner', 'Automated scanner powered by custom templates for detecting vulnerabilities.'),
		(5, 'SQLMap', 'Exploitation', 'SQL Injection Tool', 'Automates the process of detecting and exploiting SQL injection flaws.'),
		(6, 'Metasploit Framework', 'Exploitation', 'Exploitation Framework', 'Comprehensive platform for developing, testing, and using exploits.'),
		(7, 'Recon-ng', 'Reconnaissance', 'Framework', 'Web reconnaissance framework with modular structure.'),
		(8, 'Amass', 'Reconnaissance', 'DNS Enumeration Tool', 'Performs subdomain enumeration and DNS reconnaissance.'),
		(9, 'Shodan', 'Discovery', 'Search Engine', 'Search engine for Internet-connected devices with vulnerabilities.'),
		(10, 'Aquatone', 'Discovery', 'Visual Reconnaissance Tool', 'Captures screenshots of websites for quick inspection.'),
		(11, 'Sublist3r', 'Reconnaissance', 'DNS Enumeration Tool', 'Finds subdomains across multiple sources.'),
		(12, 'Dirsearch', 'Discovery', 'Directory Brute-forcing Tool', 'Brute-forces web directories to discover hidden content.'),
		(13, 'Hydra', 'Exploitation', 'Password Cracking Tool', 'Performs brute force attacks on various services like SSH, FTP, etc.'),
		(14, 'John the Ripper', 'Exploitation', 'Password Cracker', 'Advanced password cracking tool with modular capabilities.'),
		(15, 'Hashcat', 'Exploitation', 'Password Cracker', 'GPU-based password cracking tool for large hash sets.'),
		(16, 'Responder', 'Exploitation', 'Network Attack Tool', 'Intercepts and relays authentication traffic on the network.'),
		(17, 'Aircrack-ng', 'Exploitation', 'Wireless Attack Tool', 'Suite for auditing Wi-Fi networks by capturing and cracking keys.'),
		(18, 'Wireshark', 'Reconnaissance', 'Network Protocol Analyzer', 'Captures and analyzes network traffic.'),
		(19, 'PwnCat', 'Exploitation', 'Post-Exploitation Tool', 'Advanced backdoor framework for remote access.'),
		(20, 'Cobalt Strike', 'Exploitation', 'Red Teaming Tool', 'Commercial tool for advanced adversary simulations.'),
		(21, 'BloodHound', 'Discovery', 'Active Directory Tool', 'Maps out Active Directory relationships for attack path discovery.'),
		(22, 'PowerShell Empire', 'Exploitation', 'Post-Exploitation Tool', 'Framework for managing agents and post-exploitation operations.'),
		(23, 'Ffuf', 'Discovery', 'Fuzzer', 'Fast web fuzzer written in Go. Useful for directory and parameter brute-forcing.'),
		(24, 'Ghidra', 'Analysis', 'Reverse Engineering Tool', 'Open-source reverse engineering suite from NSA.'),
		(25, 'Radare2', 'Analysis', 'Reverse Engineering Tool', 'Powerful command-line framework for binary analysis and exploitation.'),
		(26, 'LinPEAS', 'Discovery', 'Privilege Escalation Tool', 'Searches for potential privilege escalation vectors on Linux systems.'),
		(27, 'Windows Exploit Suggester', 'Discovery', 'Privilege Escalation Tool', 'Suggests exploits based on missing patches.'),
		(28, 'Maltego', 'Reconnaissance', 'OSINT Tool', 'Graphs and correlates public data for OSINT investigations.'),
		(29, 'TheHarvester', 'Reconnaissance', 'OSINT Tool', 'Collects emails, subdomains, and names from public sources.'),
		(30, 'Censys', 'Discovery', 'Search Engine', 'Search engine for Internet-connected devices and vulnerabilities.');";
	
		$lQueryResult = $MySQLHandler->executeQuery($lQueryString);
		
		if (!$lQueryResult) {
			$lErrorDetected = true;
			echo format("Failed to populate 'pen_test_tools' table.", "F");
		} else {
			echo format("Successfully populated 'pen_test_tools' table.", "S");
		}
	
    	$lQueryString =
    			'CREATE TABLE captured_data('.
    				'data_id INT NOT NULL AUTO_INCREMENT, '.
    				'ip_address TEXT, '.
    				'hostname TEXT, '.
    				'port TEXT, '.
    				'user_agent_string TEXT, '.
    				'referrer TEXT, '.
    				'data TEXT, '.
    			 	'capture_date DATETIME, '.
    				'PRIMARY KEY(data_id)'.
    			')';
    	$lQueryResult = $MySQLHandler->executeQuery($lQueryString);
    	if (!$lQueryResult) {
    		$lErrorDetected = true;
			echo format("Failed to create 'captured_data' table.", "F");
    	}else{
			echo format("Successfully created 'captured_data' table.", "S");
    	}// end if

    	$lQueryString =
    			'CREATE TABLE page_hints('.
    				'page_name VARCHAR(64) NOT NULL, '.
    				'hint_key INT, '.
    				'hint TEXT, '.
    				'PRIMARY KEY(page_name, hint_key)'.
    			')';
    	$lQueryResult = $MySQLHandler->executeQuery($lQueryString);
    	if (!$lQueryResult) {
    		$lErrorDetected = true;
			echo format("Failed to create 'page_hints' table.", "F");
    	}else{
			echo format("Successfully created 'page_hints' table.", "S");
    	}// end if

    	$lQueryString =
    			'CREATE TABLE page_help('.
    				'page_name VARCHAR(64) NOT NULL, '.
    				'help_text_key INT, '.
    				'order_preference INT, '.
    				'PRIMARY KEY(page_name, help_text_key)'.
    			')';
    	$lQueryResult = $MySQLHandler->executeQuery($lQueryString);
    	if (!$lQueryResult) {
    		$lErrorDetected = true;
			echo format("Failed to create 'page_help' table.", "F");
    	}else{
			echo format("Successfully created 'page_help' table.", "S");
    	}// end if

    	$lQueryString ="INSERT INTO `page_help` (`page_name`, `help_text_key`, `order_preference`) VALUES
    			('home.php', 0, 5),
    			('home.php', 1, 5),
    			('home.php', 2, 5),
    			('home.php', 3, 5),
    			('home.php', 4, 5),
    			('home.php', 5, 5),
    			('home.php', 6, 5),
    			('home.php', 7, 0),
    			('home.php', 9, 5),
    			('home.php', 24, 5),
    			('home.php', 39, 5),
    			('home.php', 40, 5),
    			('home.php', 57, 5),
    			('home.php', 56, 5),
    			('home.php', 59, 5),
    			('home.php', 60, 0),
    			('home.php', 61, 3),
    			('home.php', 62, 1),
    			('home.php', 64, 4),
    			('add-to-your-blog.php', 8, 0),
    			('add-to-your-blog.php', 10, 2),
    			('add-to-your-blog.php', 53, 2),
    			('add-to-your-blog.php', 11, 3),
    			('add-to-your-blog.php', 55, 3),
    			('add-to-your-blog.php', 12, 1),
    			('add-to-your-blog.php', 13, 1),
    			('add-to-your-blog.php', 14, 1),
    			('add-to-your-blog.php', 30, 1),
    			('add-to-your-blog.php', 48, 1),
    			('add-to-your-blog.php', 54, 1),
    			('add-to-your-blog.php', 56, 1),
    			('add-to-your-blog.php', 59, 1),
    			('arbitrary-file-inclusion.php', 11, 3),
    			('arbitrary-file-inclusion.php', 55, 3),
    			('arbitrary-file-inclusion.php', 12, 1),
    			('arbitrary-file-inclusion.php', 15, 0),
    			('arbitrary-file-inclusion.php', 16, 1),
    			('arbitrary-file-inclusion.php', 17, 1),
    			('arbitrary-file-inclusion.php', 39, 1),
    			('arbitrary-file-inclusion.php', 40, 1),
    			('arbitrary-file-inclusion.php', 56, 1),
    			('arbitrary-file-inclusion.php', 59, 1),
    			('back-button-discussion.php', 11, 3),
    			('back-button-discussion.php', 55, 3),
    			('back-button-discussion.php', 12, 1),
    			('back-button-discussion.php', 18, 1),
    			('back-button-discussion.php', 19, 1),
    			('back-button-discussion.php', 56, 1),
    			('back-button-discussion.php', 59, 1),
    			('browser-info.php', 11, 3),
    			('browser-info.php', 55, 3),
    			('browser-info.php', 12, 1),
    			('browser-info.php', 18, 1),
    			('browser-info.php', 56, 1),
    			('browser-info.php', 59, 1),
    			('capture-data.php', 1, 1),
    			('capture-data.php', 10, 2),
    			('capture-data.php', 53, 2),
    			('capture-data.php', 11, 3),
    			('capture-data.php', 55, 3),
    			('capture-data.php', 12, 1),
    			('capture-data.php', 48, 1),
    			('captured-data.php', 11, 3),
    			('captured-data.php', 55, 3),
    			('captured-data.php', 12, 1),
    			('captured-data.php', 56, 1),
    			('captured-data.php', 59, 1),
    			('client-side-comments.php', 57, 1),
    			('client-side-comments.php', 56, 1),
    			('client-side-comments.php', 59, 1),
    			('client-side-control-challenge.php', 11, 3),
    			('client-side-control-challenge.php', 55, 3),
    			('client-side-control-challenge.php', 12, 1),
    			('client-side-control-challenge.php', 13, 1),
    			('client-side-control-challenge.php', 30, 1),
    			('client-side-control-challenge.php', 51, 1),
    			('client-side-control-challenge.php', 56, 1),
    			('client-side-control-challenge.php', 59, 1),
    			('conference-room-lookup.php', 29, 1),
    			('conference-room-lookup.php', 30, 1),
    			('conference-room-lookup.php', 56, 1),
    			('conference-room-lookup.php', 59, 1),
    			('conference-room-lookup.php', 63, 1),
    			('conference-room-lookup.php', 64, 1),
    			('content-security-policy.php', 11, 3),
    			('content-security-policy.php', 55, 3),
    			('content-security-policy.php', 12, 1),
    			('content-security-policy.php', 13, 1),
    			('content-security-policy.php', 20, 1),
    			('content-security-policy.php', 30, 1),
    			('content-security-policy.php', 48, 1),
    			('content-security-policy.php', 56, 1),
    			('content-security-policy.php', 59, 1),
    			('content-security-policy.php', 65, 1),
    			('cors.php', 11, 3),
    			('cors.php', 55, 3),
    			('cors.php', 12, 1),
    			('cors.php', 13, 1),
    			('cors.php', 20, 1),
    			('cors.php', 30, 1),
    			('cors.php', 48, 1),
    			('cors.php', 56, 1),
    			('cors.php', 59, 1),
    			('cors.php', 67, 1),
    			('credits.php', 19, 1),
    			('credits.php', 56, 1),
    			('credits.php', 59, 1),
    			('directory-browsing.php', 9, 1),
    			('directory-browsing.php', 56, 1),
    			('directory-browsing.php', 59, 1),
    			('dns-lookup.php', 11, 3),
    			('dns-lookup.php', 55, 3),
    			('dns-lookup.php', 12, 1),
    			('dns-lookup.php', 13, 1),
    			('dns-lookup.php', 20, 1),
    			('dns-lookup.php', 30, 1),
    			('dns-lookup.php', 48, 1),
    			('dns-lookup.php', 56, 1),
    			('dns-lookup.php', 59, 1),
    			('document-viewer.php', 11, 3),
    			('document-viewer.php', 55, 3),
    			('document-viewer.php', 12, 1),
    			('document-viewer.php', 21, 1),
    			('document-viewer.php', 30, 1),
    			('document-viewer.php', 41, 1),
    			('document-viewer.php', 48, 1),
    			('document-viewer.php', 56, 1),
    			('document-viewer.php', 59, 1),
    			('echo.php', 11, 3),
    			('echo.php', 55, 3),
    			('echo.php', 12, 1),
    			('echo.php', 13, 1),
    			('echo.php', 20, 1),
    			('echo.php', 30, 1),
    			('echo.php', 48, 1),
    			('echo.php', 56, 1),
    			('echo.php', 59, 1),
    			('edit-account-profile.php', 10, 2),
    			('edit-account-profile.php', 11, 3),
    			('edit-account-profile.php', 12, 1),
    			('edit-account-profile.php', 14, 1),
    			('edit-account-profile.php', 16, 3),
    			('edit-account-profile.php', 30, 1),
    			('edit-account-profile.php', 48, 1),
    			('edit-account-profile.php', 53, 2),
    			('edit-account-profile.php', 54, 1),
    			('edit-account-profile.php', 55, 3),
    			('edit-account-profile.php', 56, 1),
    			('edit-account-profile.php', 59, 1),
    			('framing.php', 22, 1),
    			('framing.php', 56, 1),
    			('framing.php', 59, 1),
    			('html5-storage.php', 12, 1),
    			('html5-storage.php', 23, 1),
    			('html5-storage.php', 42, 1),
    			('html5-storage.php', 56, 1),
    			('html5-storage.php', 59, 1),
    			('labs/lab-1.php', 68, 1),
    			('labs/lab-2.php', 69, 1),
    			('labs/lab-3.php', 70, 1),
    			('labs/lab-4.php', 71, 1),
    			('labs/lab-5.php', 72, 1),
    			('labs/lab-6.php', 10, 1),
    			('labs/lab-6.php', 53, 1),
    			('labs/lab-6.php', 73, 1),
    			('labs/lab-7.php', 10, 1),
    			('labs/lab-7.php', 53, 1),
    			('labs/lab-7.php', 74, 1),
    			('labs/lab-8.php', 10, 1),
    			('labs/lab-8.php', 53, 1),
    			('labs/lab-8.php', 75, 1),
    			('labs/lab-9.php', 10, 1),
    			('labs/lab-9.php', 53, 1),
    			('labs/lab-9.php', 76, 1),
    			('labs/lab-10.php', 10, 1),
    			('labs/lab-10.php', 53, 1),
    			('labs/lab-10.php', 77, 1),
    			('labs/lab-11.php', 10, 1),
    			('labs/lab-11.php', 53, 1),
    			('labs/lab-11.php', 78, 1),
    			('labs/lab-12.php', 79, 1),
    			('labs/lab-13.php', 80, 1),
    			('labs/lab-14.php', 81, 1),
    			('labs/lab-15.php', 82, 1),
    			('labs/lab-16.php', 83, 1),
    			('labs/lab-17.php', 84, 1),
    			('labs/lab-18.php', 85, 1),
    			('labs/lab-19.php', 86, 1),
    			('labs/lab-20.php', 87, 1),
    			('labs/lab-21.php', 88, 1),
    			('labs/lab-22.php', 89, 1),
    			('labs/lab-23.php', 90, 1),
    			('labs/lab-24.php', 91, 1),
    			('labs/lab-25.php', 92, 1),
    			('labs/lab-26.php', 93, 1),
    			('labs/lab-27.php', 94, 1),
    			('labs/lab-28.php', 95, 1),
    			('labs/lab-29.php', 96, 1),
    			('labs/lab-30.php', 97, 1),
    			('labs/lab-31.php', 98, 1),
    			('labs/lab-32.php', 99, 1),
    			('labs/lab-33.php', 100, 1),
    			('labs/lab-34.php', 101, 1),
    			('labs/lab-35.php', 102, 1),
    			('labs/lab-36.php', 103, 1),
    			('labs/lab-37.php', 104, 1),
    			('labs/lab-38.php', 105, 1),
    			('labs/lab-39.php', 106, 1),
    			('labs/lab-40.php', 107, 1),
    			('labs/lab-41.php', 108, 1),
    			('labs/lab-42.php', 109, 1),
    			('labs/lab-43.php', 110, 1),
    			('labs/lab-44.php', 111, 1),
    			('labs/lab-45.php', 112, 1),
    			('labs/lab-46.php', 113, 1),
    			('labs/lab-47.php', 114, 1),
    			('labs/lab-48.php', 115, 1),
    			('labs/lab-49.php', 116, 1),
    			('labs/lab-50.php', 117, 1),
    			('labs/lab-51.php', 118, 1),
    			('labs/lab-52.php', 119, 1),
    			('labs/lab-53.php', 120, 1),
    			('labs/lab-54.php', 121, 1),
    			('labs/lab-55.php', 122, 1),
    			('labs/lab-56.php', 123, 1),
    			('labs/lab-57.php', 124, 1),
    			('labs/lab-58.php', 125, 1),
    			('labs/lab-59.php', 126, 1),
    			('labs/lab-60.php', 127, 1),
    			('labs/lab-61.php', 128, 1),
    			('labs/lab-62.php', 129, 1),
    			('labs/lab-63.php', 130, 1),
    			('login.php', 1, 1),
    			('login.php', 10, 2),
    			('login.php', 53, 2),
    			('login.php', 11, 3),
    			('login.php', 55, 3),
    			('login.php', 12, 1),
    			('login.php', 13, 1),
    			('login.php', 25, 1),
    			('login.php', 47, 1),
    			('login.php', 48, 1),
    			('login.php', 54, 1),
    			('login.php', 56, 1),
    			('login.php', 59, 1),
    			('login.php', 60, 1),
    			('password-generator.php', 1, 1),
    			('password-generator.php', 11, 3),
    			('password-generator.php', 55, 3),
    			('password-generator.php', 12, 1),
    			('password-generator.php', 18, 1),
    			('password-generator.php', 56, 1),
    			('password-generator.php', 59, 1),
    			('pen-test-tool-lookup.php', 26, 1),
    			('pen-test-tool-lookup-ajax.php', 26, 1),
    			('pen-test-tool-lookup-ajax.php', 56, 1),
    			('pen-test-tool-lookup-ajax.php', 59, 1),
    			('phpinfo.php', 27, 1),
    			('phpinfo.php', 28, 1),
    			('phpinfo.php', 29, 1),
    			('phpinfo.php', 56, 1),
    			('phpinfo.php', 59, 1),
    			('register.php', 10, 2),
    			('register.php', 11, 3),
    			('register.php', 12, 1),
    			('register.php', 14, 1),
    			('register.php', 30, 1),
    			('register.php', 48, 1),
    			('register.php', 53, 2),
    			('register.php', 54, 1),
    			('register.php', 55, 3),
    			('register.php', 56, 1),
    			('register.php', 59, 1),
    			('rene-magritte.php', 22, 1),
    			('rene-magritte.php', 56, 1),
    			('rene-magritte.php', 59, 1),
    			('robots-txt.php', 9, 1),
    			('robots-txt.php', 29, 1),
    			('robots-txt.php', 43, 1),
    			('robots-txt.php', 56, 1),
    			('robots-txt.php', 59, 1),
    			('repeater.php', 11, 3),
    			('repeater.php', 55, 3),
    			('repeater.php', 12, 1),
    			('repeater.php', 13, 1),
    			('repeater.php', 31, 1),
    			('repeater.php', 32, 1),
    			('repeater.php', 56, 1),
    			('repeater.php', 59, 1),
    			('secret-administrative-pages.php', 6, 1),
    			('secret-administrative-pages.php', 27, 1),
    			('secret-administrative-pages.php', 28, 1),
    			('secret-administrative-pages.php', 29, 1),
    			('secret-administrative-pages.php', 44, 1),
    			('secret-administrative-pages.php', 56, 1),
    			('secret-administrative-pages.php', 59, 1),
    			('set-background-color.php', 11, 3),
    			('set-background-color.php', 55, 3),
    			('set-background-color.php', 12, 1),
    			('set-background-color.php', 33, 1),
    			('set-background-color.php', 56, 1),
    			('set-background-color.php', 59, 1),
    			('show-log.php', 11, 3),
    			('show-log.php', 55, 3),
    			('show-log.php', 12, 1),
    			('show-log.php', 34, 1),
    			('show-log.php', 56, 1),
    			('show-log.php', 59, 1),
    			('site-footer-xss-discussion.php', 11, 3),
    			('site-footer-xss-discussion.php', 55, 3),
    			('site-footer-xss-discussion.php', 12, 1),
    			('site-footer-xss-discussion.php', 56, 1),
    			('site-footer-xss-discussion.php', 59, 1),
    			('source-viewer.php', 11, 3),
    			('source-viewer.php', 55, 3),
    			('source-viewer.php', 12, 1),
    			('source-viewer.php', 15, 1),
    			('source-viewer.php', 16, 1),
    			('source-viewer.php', 39, 1),
    			('source-viewer.php', 40, 1),
    			('source-viewer.php', 48, 1),
    			('source-viewer.php', 56, 1),
    			('source-viewer.php', 59, 1),
    			('styling-frame.php', 11, 3),
    			('styling-frame.php', 55, 3),
    			('styling-frame.php', 12, 1),
    			('styling-frame.php', 16, 1),
    			('styling-frame.php', 39, 1),
    			('styling-frame.php', 40, 1),
    			('styling-frame.php', 41, 1),
    			('styling-frame.php', 48, 1),
    			('styling-frame.php', 50, 1),
    			('styling-frame.php', 56, 1),
    			('styling-frame.php', 59, 1),
    			('sqlmap-targets.php', 10, 2),
    			('sqlmap-targets.php', 53, 2),
    			('sqlmap-targets.php', 56, 1),
    			('sqlmap-targets.php', 59, 1),
    			('ssl-misconfiguration.php', 1, 1),
    			('ssl-misconfiguration.php', 56, 1),
    			('ssl-misconfiguration.php', 59, 1),
    			('ssl-misconfiguration.php', 60, 1),
    			('test-connectivity.php', 11, 3),
    			('test-connectivity.php', 55, 3),
    			('test-connectivity.php', 12, 1),
    			('test-connectivity.php', 13, 1),
    			('test-connectivity.php', 20, 1),
    			('test-connectivity.php', 30, 1),
    			('test-connectivity.php', 48, 1),
    			('test-connectivity.php', 56, 1),
    			('test-connectivity.php', 59, 1),
    			('test-connectivity.php', 131, 1),
    			('text-file-viewer.php', 11, 3),
    			('text-file-viewer.php', 55, 3),
    			('text-file-viewer.php', 12, 1),
    			('text-file-viewer.php', 15, 1),
    			('text-file-viewer.php', 16, 1),
    			('text-file-viewer.php', 30, 1),
    			('text-file-viewer.php', 35, 1),
    			('text-file-viewer.php', 39, 1),
    			('text-file-viewer.php', 40, 1),
    			('text-file-viewer.php', 56, 1),
    			('text-file-viewer.php', 59, 1),
    			('upload-file.php', 46, 1),
    			('upload-file.php', 11, 2),
    			('upload-file.php', 55, 2),
    			('upload-file.php', 12, 2),
    			('upload-file.php', 54, 2),
    			('upload-file.php', 56, 1),
    			('upload-file.php', 59, 1),
    			('user-agent-impersonation.php', 11, 3),
    			('user-agent-impersonation.php', 55, 3),
    			('user-agent-impersonation.php', 18, 1),
    			('user-agent-impersonation.php', 45, 1),
    			('user-agent-impersonation.php', 56, 1),
    			('user-agent-impersonation.php', 59, 1),
    			('user-info.php', 1, 1),
    			('user-info.php', 10, 2),
    			('user-info.php', 53, 2),
    			('user-info.php', 11, 3),
    			('user-info.php', 55, 3),
    			('user-info.php', 12, 1),
    			('user-info.php', 13, 1),
    			('user-info.php', 30, 1),
    			('user-info.php', 54, 1),
    			('user-info.php', 56, 1),
    			('user-info.php', 59, 1),
    			('user-info-xpath.php', 1, 1),
    			('user-info-xpath.php', 11, 3),
    			('user-info-xpath.php', 55, 3),
    			('user-info-xpath.php', 12, 1),
    			('user-info-xpath.php', 13, 1),
    			('user-info-xpath.php', 30, 1),
    			('user-info-xpath.php', 49, 1),
    			('user-info-xpath.php', 54, 1),
    			('user-info-xpath.php', 58, 1),
    			('user-info-xpath.php', 56, 1),
    			('user-info-xpath.php', 59, 1),
    			('user-poll.php', 10, 2),
    			('user-poll.php', 53, 2),
    			('user-poll.php', 11, 3),
    			('user-poll.php', 55, 3),
    			('user-poll.php', 12, 1),
    			('user-poll.php', 14, 1),
    			('user-poll.php', 21, 1),
    			('user-poll.php', 30, 1),
    			('user-poll.php', 54, 1),
    			('user-poll.php', 56, 1),
    			('user-poll.php', 59, 1),
    			('view-someones-blog.php', 11, 3),
    			('view-someones-blog.php', 55, 3),
    			('view-someones-blog.php', 12, 1),
    			('view-someones-blog.php', 14, 1),
    			('view-someones-blog.php', 30, 1),
    			('view-someones-blog.php', 54, 1),
    			('view-someones-blog.php', 56, 1),
    			('view-someones-blog.php', 59, 1),
    			('view-user-privilege-level.php', 11, 3),
    			('view-user-privilege-level.php', 55, 3),
    			('view-user-privilege-level.php', 12, 1),
    			('view-user-privilege-level.php', 25, 1),
    			('view-user-privilege-level.php', 31, 1),
    			('view-user-privilege-level.php', 38, 1),
    			('view-user-privilege-level.php', 56, 1),
    			('view-user-privilege-level.php', 59, 1),
    			('xml-validator.php', 11, 3),
    			('xml-validator.php', 55, 3),
    			('xml-validator.php', 12, 2),
    			('xml-validator.php', 15, 2),
    			('xml-validator.php', 36, 2),
    			('xml-validator.php', 58, 1),
    			('xml-validator.php', 56, 1),
    			('xml-validator.php', 59, 1),
    			('jwt.php', 66, 1)
    			;";

    	$lQueryResult = $MySQLHandler->executeQuery($lQueryString);
    	if (!$lQueryResult) {
    		$lErrorDetected = true;
			echo format("Failed to insert data into 'page_help' table.", "F");
    	}else{
			echo format("Successfully inserted data into 'page_help' table.", "S");
    	}// end if

    	$lQueryString =
    		'CREATE TABLE level_1_help_include_files('.
    			'level_1_help_include_file_key INT, '.
    			'level_1_help_include_file_description TEXT, '.
    			'level_1_help_include_file TEXT, '.
    			'PRIMARY KEY(level_1_help_include_file_key)'.
    		')';
    	$lQueryResult = $MySQLHandler->executeQuery($lQueryString);
    	if (!$lQueryResult) {
    		$lErrorDetected = true;
			echo format("Failed to create 'level_1_help_include_files' table.", "F");
    	}else{
			echo format("Successfully created 'level_1_help_include_files' table.", "S");
    	}// end if

    	/* NOTE: Be sure to keep indexes in the help_texts table
    	 * relatively the same as the level_1_help_include_files
    	 * table so we can reuse the keys in the page_help table.
    	 */
    	$lQueryString ="
    		INSERT INTO level_1_help_include_files (
    			level_1_help_include_file_key,
    			level_1_help_include_file_description,
    			level_1_help_include_file
    		) VALUES
    		(1, 'SSL Misconfiguration', 'ssl-misconfiguration-hint.inc'),
    		(9, 'Directory Browsing', 'directory-browsing-hint.inc'),
    		(10, 'SQL Injection (SQLi)', 'sql-injection-hint.inc'),
    		(11, 'Cross-site Scripting (XSS)', 'cross-site-scripting-hint.inc'),
    		(12, 'HTML Injection (HTMLi)', 'html-injection-hint.inc'),
    		(13, 'JavaScript Validation Bypass', 'javascript-validation-bypass-hint.inc'),
    		(14, 'Cross-site Request Forgery (CSRF)', 'cross-site-request-forgery-hint.inc'),
    		(16, 'Insecure Direct Object References (IDOR)', 'insecure-direct-object-reference-hint.inc'),
    		(18, 'JavaScript Injection', 'javascript-injection-hint.inc'),
    		(19, 'Unvalidated Redirects', 'unvalidated-redirects-and-forwards.inc'),
    		(20, 'Command Injection (CMDi)', 'command-injection-hint.inc'),
    		(21, 'Parameter Pollution', 'parameter-pollution-hint.inc'),
    		(22, 'Click-Jacking', 'click-jacking-hint.inc'),
    		(23, 'Document Object Model (DOM) Injection', 'dom-injection-hint.inc'),
    		(25, 'Authentication Bypass', 'authentication-bypass-hint.inc'),
    		(26, 'JavaScript Object Notation (JSON) Injection', 'json-injection-hint.inc'),
    		(27, 'Platform Path Disclosure', 'platform-path-disclosure-hint.inc'),
    		(28, 'Application Path Disclosure', 'application-path-disclosure-hint.inc'),
    		(29, 'Information Disclosure', 'information-disclosure-hint.inc'),
    		(30, 'Method Tampering', 'method-tampering-hint.inc'),
    		(31, 'Parameter Addition', 'parameter-addition-hint.inc'),
    		(32, 'Buffer Overflow', 'buffer-overflow-hint.inc'),
    		(33, 'Cascading Style Sheet (CSS) Injection', 'cascading-style-sheet-injection-hint.inc'),
    		(36, 'XML External Entity (XXE) Injection', 'xml-external-entity-attack-hint.inc'),
    		(38, 'CBC Bit-flipping Attack', 'cbc-bit-flipping-attack-hint.inc'),
    		(39, 'Local File Inclusion', 'local-file-inclusion-hint.inc'),
    		(40, 'Remote File Inclusion', 'remote-file-inclusion-hint.inc'),
    		(41, 'Frame Source Injection', 'frame-source-injection-hint.inc'),
    		(42, 'HTML-5 Web Storage Injection', 'html5-web-storage-hint.inc'),
    		(43, 'Robots.txt', 'robots-txt-hint.inc'),
    		(44, 'Secret Administrative Pages', 'secret-administrative-pages-hint.inc'),
    		(45, 'User-agent Impersonation', 'user-agent-impersonation-hint.inc'),
    		(46, 'Unrestricted File Upload', 'unrestricted-file-upload-hint.inc'),
    		(48, 'Application Log Injection', 'application-log-injection.inc'),
    		(49, 'XPath Injection', 'xpath-injection-hint.inc'),
    		(50, 'Path Relative Style-sheet Injection', 'path-relative-stylesheet-injection.inc'),
    		(51, 'Client-side Security Control Bypass', 'client-side-security-control-bypass.inc'),
    		(53, 'SQL Injection with SQLMap', 'sqlmap-hint.inc'),
    		(54, 'Insufficient Transport Layer Protection', 'insufficient-transport-layer-protection.inc'),
    		(55, 'Cross-site Scripting with BeEF Framework', 'beef-framework-hint.inc'),
    		(56, 'Using Burp-Suite', 'burp-suite-hint.inc'),
    		(57, 'Client-side Comments', 'client-side-comments.inc'),
    		(58, 'XML Entity Expansion', 'xml-entity-expansion-hint.inc'),
    		(59, 'Using OWASP Zed Attack Proxy (ZAP)', 'owasp-zap-hint.inc'),
    		(60, 'Set Up HTTPS Self-signed Certificate', 'setting-up-ssl-hint.inc'),
    		(61, 'Set Up Apache Virtual Hosts', 'setting-up-virtual-hosts-hint.inc'),
    		(62, 'Set Up Local Hostnames', 'setting-up-local-hostnames-hint.inc'),
    		(63, 'LDAP Injection', 'ldap-injection-hint.inc'),
    		(64, 'Setting up LDAP Server', 'ldap-setup-hint.inc'),
    		(65, 'Content Security Policy (CSP)', 'content-security-policy-hint.inc'),
    		(66, 'JSON Web Tokens (JWT)', 'jwt-hint.inc'),
    		(67, 'Cross-origin Resource Sharing (CORS)', 'cross-origin-resource-sharing-hint.inc'),
    		(68, 'Lab 1', 'lab-1-hint.inc'),
    		(69, 'Lab 2', 'lab-2-hint.inc'),
    		(70, 'Lab 3', 'lab-3-hint.inc'),
    		(71, 'Lab 4', 'lab-4-hint.inc'),
    		(72, 'Lab 5', 'lab-5-hint.inc'),
    		(73, 'Lab 6', 'lab-6-hint.inc'),
    		(74, 'Lab 7', 'lab-7-hint.inc'),
    		(75, 'Lab 8', 'lab-8-hint.inc'),
    		(76, 'Lab 9', 'lab-9-hint.inc'),
    		(77, 'Lab 10', 'lab-10-hint.inc'),
    		(78, 'Lab 11', 'lab-11-hint.inc'),
    		(79, 'Lab 12', 'lab-12-hint.inc'),
    		(80, 'Lab 13', 'lab-13-hint.inc'),
    		(81, 'Lab 14', 'lab-14-hint.inc'),
    		(82, 'Lab 15', 'lab-15-hint.inc'),
    		(83, 'Lab 16', 'lab-16-hint.inc'),
    		(84, 'Lab 17', 'lab-17-hint.inc'),
    		(85, 'Lab 18', 'lab-18-hint.inc'),
    		(86, 'Lab 19', 'lab-19-hint.inc'),
    		(87, 'Lab 20', 'lab-20-hint.inc'),
    		(88, 'Lab 21', 'lab-21-hint.inc'),
    		(89, 'Lab 22', 'lab-22-hint.inc'),
    		(90, 'Lab 23', 'lab-23-hint.inc'),
    		(91, 'Lab 24', 'lab-24-hint.inc'),
    		(92, 'Lab 25', 'lab-25-hint.inc'),
    		(93, 'Lab 26', 'lab-26-hint.inc'),
    		(94, 'Lab 27', 'lab-27-hint.inc'),
    		(95, 'Lab 28', 'lab-28-hint.inc'),
    		(96, 'Lab 29', 'lab-29-hint.inc'),
    		(97, 'Lab 30', 'lab-30-hint.inc'),
    		(98, 'Lab 31', 'lab-31-hint.inc'),
    		(99, 'Lab 32', 'lab-32-hint.inc'),
    		(100, 'Lab 33', 'lab-33-hint.inc'),
    		(101, 'Lab 34', 'lab-34-hint.inc'),
    		(102, 'Lab 35', 'lab-35-hint.inc'),
    		(103, 'Lab 36', 'lab-36-hint.inc'),
    		(104, 'Lab 37', 'lab-37-hint.inc'),
    		(105, 'Lab 38', 'lab-38-hint.inc'),
    		(106, 'Lab 39', 'lab-39-hint.inc'),
    		(107, 'Lab 40', 'lab-40-hint.inc'),
    		(108, 'Lab 41', 'lab-41-hint.inc'),
    		(109, 'Lab 42', 'lab-42-hint.inc'),
    		(110, 'Lab 43', 'lab-43-hint.inc'),
    		(111, 'Lab 44', 'lab-44-hint.inc'),
    		(112, 'Lab 45', 'lab-45-hint.inc'),
    		(113, 'Lab 46', 'lab-46-hint.inc'),
    		(114, 'Lab 47', 'lab-47-hint.inc'),
    		(115, 'Lab 48', 'lab-48-hint.inc'),
    		(116, 'Lab 49', 'lab-49-hint.inc'),
    		(117, 'Lab 50', 'lab-50-hint.inc'),
    		(118, 'Lab 51', 'lab-51-hint.inc'),
    		(119, 'Lab 52', 'lab-52-hint.inc'),
    		(120, 'Lab 53', 'lab-53-hint.inc'),
    		(121, 'Lab 54', 'lab-54-hint.inc'),
    		(122, 'Lab 55', 'lab-55-hint.inc'),
    		(123, 'Lab 56', 'lab-56-hint.inc'),
    		(124, 'Lab 57', 'lab-57-hint.inc'),
    		(125, 'Lab 58', 'lab-58-hint.inc'),
    		(126, 'Lab 59', 'lab-59-hint.inc'),
    		(127, 'Lab 60', 'lab-60-hint.inc'),
    		(128, 'Lab 61', 'lab-61-hint.inc'),
    		(129, 'Lab 62', 'lab-62-hint.inc'),
    		(130, 'Lab 63', 'lab-63-hint.inc'),
    		(131, 'Server-side Request Forgery (SSRF)', 'server-side-request-forgery-hint.inc'),
    		(999, 'Hints Not Found', 'hints-not-found.inc')";

    	$lQueryResult = $MySQLHandler->executeQuery($lQueryString);
    	if (!$lQueryResult) {
    		$lErrorDetected = true;
			echo format("Failed to insert data into 'level_1_help_include_files' table.", "F");
    	}else{
			echo format("Successfully inserted data into 'level_1_help_include_files' table.", "S");
    	}// end if

    	$lQueryString =
    			'CREATE TABLE help_texts('.
    				'help_text_key INT, '.
    				'help_text TEXT, '.
    				'PRIMARY KEY(help_text_key)'.
    			')';
    	$lQueryResult = $MySQLHandler->executeQuery($lQueryString);
    	if (!$lQueryResult) {
    		$lErrorDetected = true;
			echo format("Failed to create 'help_texts' table.", "F");
    	}else{
			echo format("Successfully created 'help_texts' table.", "S");
    	}// end if

    	/* NOTE: Be sure to keep indexes in the help_texts table
    	 * relatively the same as the level_1_help_include_files
    	 * table so we can reuse the keys in the page_help table.
    	 */
    	$lQueryString ="INSERT INTO help_texts (help_text_key, help_text) VALUES
    		(0, 'The index page has several global vulnerabilities.'),
    		(1, '<span class=\"label\">SSLStrip</span> can be used to downgrade the connection when the Enforce SSL button is selected.'),
    		(2, 'Output fields such as the logged-in username, signature, and the footer are vulnerable to cross-site scripting.'),
    		(3, 'The hints cookie and other cookies can be hacked to login as another user and gain admin access.'),
    		(4, 'Cookies are missing the HTTPOnly attribute and may be accessed via cross-site scripting.'),
    		(5, 'Check HTML comments for database credentials.'),
    		(6, 'The \"page\" input parameter is vulnerable to insecure direct object reference. Fuzzing the parameter with administrative page names or system file paths is likely to yield results.'),
    		(7, 'This is the home page. Its primary purpose is to provide a starting page for the user and provide instructions. There are no known vulnerabilties on the home.php page.'),
    		(8, '<span class=\"label\">Stored Cross-Site Scripting</span>: Attempt to inject cross-site scripts which will be stored in the backend database. When a user visits this page, the cross-site scripts will be fetched from the database, incorporated into the HTML generated, and sent to the user browser. The user browser will execute the Javascript. One option is to inject a cross-site script which sends the user to the capture-data.php page. You can view captured data on the captured-data.php page.'),
    		(9, '<span class=\"label\">Directory Browsing</span>: The entire site is vulnerable to directory browsing. Looking at the robots.txt file can provide hints of interesting directories.'),
    		(10, '<span class=\"label\">SQL Injection</span>: Attempt to inject special database characters or SQL timing attacks into page parameters. Database errors, page defacement, or noticable delays in response may indicate SQL injection flaws. This page is vulnerable.'),
    		(11, '<span class=\"label\">Reflected Cross-Site Scripting:</span> This page is vulnerable to reflected cross-site scripting because the input is not encoded prior to be used as output. Determine which input field contributes output here and inject scripts. Try to redirect the user to the capture-data.php page which records cookies and other parameters. Visit the captured-data.php page to view captured data.'),
    		(12, '<span class=\"label\">HTML Injection</span>: It is possible to inject your own HTML into this page because the input is not encoded prior to be used as output. Determine which input field contributes output here and inject HTML, CSS, and/or Javascripts in order to alter the client-side code of this page.'),
    		(13, '<span class=\"label\">Javascript Validation Bypass</span>: Set the page to at least security level 1 to activate the javascript validation. Javascript validation can always be bypassed. Use a client-proxy like Burp-Suite to capture the request after it has left the browser. You can alter the request at that time. Also, Javascript can be disabled.'),
    		(14, '<span class=\"label\">Cross Site Request Forgery</span>: This page is vulnerable to cross-site request forgery. There are a few steps to prepare a cross-site script to carry out the cross-site request forgery. Begin by filling out the form capturing the legitimate request. Inject a stored or reflected cross-site script anywhere on the site that will cause the browser to submit a copy of the legitimate request to the server. The server will process the request as if the user had filled out the form themselves.'),
    		(15, '<span class=\"label\">System File Compromise</span>: It is possible to access system files by injecting input parameters with the pathnames of system files. The web application will fetch the system files instead of application files. The system files may be displayed and/or included in page output. Remember web applications are usually served from a system directory like /var/www or C:XAMPP. You may need to move up directories.'),
    		(16, '<span class=\"label\">Insecure Direct Object Reference</span>: This page refers directly to resources by there real name or identifier making it possible to modify the name/ID to access other resources. Determine what resources are fetched. Provide the name or ID of a different resource. Resources can be filenames, record identifiers or others.'),
    		(17, '<span class=\"label\">Server Side Include</span>: It is possible to make the application include application files in this page that are not intended. These files may even come from other sites.'),
    		(18, '<span class=\"label\">Javascript Injection</span>: This page uses at least some of the input from the user to generate Javascript code. Usually in these cases the user input is used to create either a Javascript string or JSON object. Attempt to inject input which when incorporated with the page will form a syntactically correct Javascript statement. This will allow the injection to execute in the context of the browser.'),
    		(19, '<span class=\"label\">Unvalidated Redirects and Forwards</span>: This page refers directly to dynamic URLs. If the user clicks on one of the link, the URL embedded is passed to a page which performs redirection. Try to over-write one of the intended pages beind passed to redirect the user to an arbitrary page. Give the poisoned link you create to a freind and see if they are redirected to a site of your choosing.'),
    		(20, '<span class=\"label\">Operating System Command Injection</span>:  Command injection may occur when a web application passes user input in part or in whole to the operating system for execution. This page incorporates user input into a larger statement that is submitted to an operating system shell for execution. Try to determine the operating system in use. Enter characters that are reserved in shells; especially characters used to concatenate commands.'),
    		(21, '<span class=\"label\">HTTP Parameter Pollution</span>: If multiple parameters with the same name are sent in a request, different application servers will react differently. PHP takes only one of the parameters but not neccesarily the parameters intended by the developer. By duplicating parameters with a value of your choosing and placing that parameters before and-or after the pages native parameters, you can influence the pages behavior. Note that ASP and Java application servers act different.'),
    		(22, '<span class=\"label\">Click-jacking</span>: By placing an invisible overlay over top of a legitimate page, a malicious agent can hijack a users mouse clicks. To overlay the vulnerable page, the malicious agent will host the victim page inside a full page frame with no borders.'),
    		(23, '<span class=\"label\">Document Object Model (DOM) Injection</span>: User input is incorporated into the document object model (DOM) of the page itself. This allows a user to inject HTML which will be incorporated into the source code of the page. The browser will execute this new code immediately.'),
    		(24, 'The UID cookie is used in an SQL query allowing SQL injection via a cookie value.'),
    		(25, '<span class=\"label\">Authentication Bypass</span>: Authentication bypass can be achieved by either hacking the UID cookie or by SQL injecting the login.'),
    		(26, '<span class=\"label\">Javascript Object Notation (JSON) Injection</span>: This page uses JSON to pass data which is later parsed and incorporated into the page. Because the output is not properly encoded, it is possible to carefully craft an injection which will add extra data into the JSON without breaking the JSON syntax. This extra data will be executed by the browser once the data is incorporated into the page.'),
    		(27, '<span class=\"label\">Platform Path Disclosure</span>: Internal system paths are disclosed by this page under certain conditions.'),
    		(28, '<span class=\"label\">Application Path Disclosure</span>: Application file paths are disclosed by this page under certain conditions.'),
    		(29, '<span class=\"label\">Information Disclosure</span>: This page gives away internal system information, configuration information, paths, filenames, or other private information.'),
    		(30, '<span class=\"label\">Method Tampering</span>: Because the page does not specify that the input parameters must be posted, it is possible to submit input parameters via a post or a get. This is a second order vulnerability allowing other vulnerabilities to be exploited easier.'),
    		(31, '<span class=\"label\">Parameter Addition</span>: If extra parameters are submitted, the page will include them in output. A parameter can be added containing scripts which will be executed when loaded in the users browser.'),
    		(32, '<span class=\"label\">Buffer Overflow</span>: If very long input is submitted, it is possible to exhaust the available space alloted on the heap.'),
    		(33, '<span class=\"label\">Cascading Style Sheet Injection</span>: CSS styles can be used to interpret and execute Javascript. If styles can be injected, it is possible to inject a style with embedded Javascript which will be executed when loaded into the users browser.'),
    		(34, '<span class=\"label\">Denial of Service</span>: This page allows denial of service. DOS can be performed by exhausting system resource(s) such as filling up disk drives or consuming available network bandwidth.'),
    		(35, '<span class=\"label\">Phishing/Remote File Inclusion</span>: Due to defects allowing arbitrary web pages to be loaded into this pages frames, phishing and malware downloads are possible.'),
    		(36, '<span class=\"label\">XML External Entity Injection Attack</span>: This page parses XML which the user can influence. If external entities embedded in the XML contain system file directives, it is possible to cause the page to load system files and include the contents in the XML output.'),
    		(38, '<span class=\"label\">Cipher Block Chaining (CBC) Bit Flipping Attack</span>: This page is vulnerable to CBC bit flipping attack.'),
    		(39, '<span class=\"label\">Local File Inclusion</span>: This page is vulnerable to local file inclusion if the user account under which PHP is running has access to files besides the intended web site files.'),
    		(40, '<span class=\"label\">Remote File Inclusion</span>: This page is vulnerable to remote file inclusion if the PHP server configuration parameters \"allow_url_fopen\" and \"allow_url_include\" are set to \"On\" in php.ini.'),
    		(41, '<span class=\"label\">Frame Source Injection</span>: By controlling the parameter which determines the src attribute of a pages frame, a carefully injected value can load any arbitrary page into the frame.'),
    		(42, '<span class=\"label\">HTML 5 Web Storage Theft and Manipulation</span>: Using a cross site scripting attack, this page is vulnerable to having an attacker read, insert, update, or delete the values stored in the HTML5 web storage.'),
    		(43, '<span class=\"label\">Robots.txt</span>: This file gives away sensitive file paths.'),
    		(44, '<span class=\"label\">Secret Administrative Pages</span>: These pages are obscured by not being linked from other pages but they can be found using other vulnerabilities such as directory browsing, robots.txt, and local file inclusion.'),
    		(45, '<span class=\"label\">User Agent Impersonation</span>: Based on the information sent by the browser, this page decides if the user is authorized.'),
    		(46, '<span class=\"label\">Unrestricted File Upload</span>: This page allows dangerous files to be uploaded.'),
    		(47, '<span class=\"label\">Username Enumeration</span>: This page allows usernames to be enumerated.'),
    		(48, '<span class=\"label\">Application Log Injection</span>: Some inputs on this page are recorded into log records which can be read by visiting the Show Log page. Vulnerabilities on the Show Log page may allow injections in log records to execute.'),
    		(49, '<span class=\"label\">XPath Injection</span>: Some inputs on this page are vulnerable to XPath injection.'),
    		(50, '<span class=\"label\">Path Relative Stylesheet Injection</span>: Within this page is an iframe containing another page. The page being framed is vulnerable to path relative stylesheet injection.'),
    		(51, '<span class=\"label\">Client-side Security Control Bypass</span>: This page attempts to implement security using client-side security controls. Any page using such controls, including this page, is vulnerable to security control bypass.'),
    		(53, '<span class=\"label\">SQL Injection with SQLMap</span>: This page contains an sql injection vulnerability. The SQLMap tool may be able to automate testing and confirming this vulnerability.'),
    		(54, '<span class=\"label\">Insufficent Transport Layer Protection</span>: This page is vulnerable to interception with wireshark or tcpdump.'),
    		(55, '<span class=\"label\">Cross-site Scripting with BeEF Framework</span>: Some inputs on this page are vulnerable to Cross-site Scripting (XSS). The input may be hidden.'),
            (63, '<span class=\"label\">LDAP Injection</span>: This page is vulnerable to LDAP injection.'),
    	    (66, '<span class=\"label\">JSON Web Tokens (JWT)</span>: This page is vulnerable to JWT injection.'),
    	    (67, '<span class=\"label\">Cross-origin Resource Sharing (CORS)</span>: This page is vulnerable to CORS injection.'),
    	    (131, '<span class=\"label\">Server-side Request Forgery (SSRF)</span>: Some inputs on this page are vulnerable to Server-side Request Forgery (SSRF). The input may be hidden.')";

    	$lQueryResult = $MySQLHandler->executeQuery($lQueryString);
    	if (!$lQueryResult) {
    		$lErrorDetected = true;
			echo format("Failed to insert data into 'help_texts' table.", "F");
    	}else{
			echo format("Successfully inserted data into 'help_texts' table.", "S");
    	}// end if

    	$lQueryString = 'CREATE TABLE youTubeVideos( '.
    			'recordIndetifier INT NOT NULL, '.
    			'identificationToken VARCHAR(32), '.
    			'title VARCHAR(128),
    			PRIMARY KEY (recordIndetifier),
    			UNIQUE KEY (identificationToken))';
    	$lQueryResult = $MySQLHandler->executeQuery($lQueryString);
    	if (!$lQueryResult) {
    		$lErrorDetected = true;
			echo format("Failed to create 'youTubeVideos' table.", "F");
    	}else{
			echo format("Successfully created 'youTubeVideos' table.", "S");
    	}// end if

    	$lQueryString = "INSERT INTO youTubeVideos(recordIndetifier, identificationToken, title)
    		VALUES
    			(1, 'TQGblhhk-64', 'JWT Security: Part 1 - What is a JWT?'),
    			(2, 'iZPoBXcSYww', 'JWT Security: Part 2 - How to View JWT in Burp-Suite'),
    			(3, 'UAb0YZn6WjM', 'JWT Security: Part 3 - How Timeouts Work'),
    			(4, 'f-v70H9xgFI', 'JWT Security: Part 4 - How Signatures Protect Against Forgery'),
    			(5, 'plv4ilIJbYo', 'JWT Security: Part 5 - Why use Certificate-based Signatures?'),
    			(6, 'JST-9GmWA2s', 'JWT Security: Part 6 - Importance of Input Validation'),
    			(7, 'eGXCK3YieaI', 'How to Scan for Web Vulnerabilities with Nikto'),
    			(8, 'Mv7wpCIAdp4', 'What is SQL Injection?'),
                (9, 'DF22sTpcE6w', 'OWASP Dependency Check: Part 1 - How to Install'),
                (10, 'X47ZkdYnGZI', 'OWASP Dependency Check: Part 2 - How to Scan Your Project'),
                (11, '1a652-fvQLA', 'Linux Basics: How to Identify File Type'),
                (12, 'kSVv0AxlTVk', 'What is Server-side Request Forgery (SSRF)?'),
    			(13, 'la5hTlSDKWg', 'Using Burp Intruder Sniper to Fuzz Parameters'),
    			(14, 'YoNlia7B5F0', 'Cross-Site Scripting: Part 1- What is Reflected XSS?'),
    			(15, 'yN6hjtNvhMo', 'Cross-Site Scripting: Part 2 - What is DOM-based XSS?'),
    			(16, 'i0nOTA2NNDY', 'Cross-Site Scripting: Part 3 - What is Persistent XSS?'),
    			(17, 'V3xCPCaaH4s', 'What is Insecure Direct Object Reference (IDOR)?'),
    			(18, 'eQxIPjasZqA', 'What is an Open Redirect?'),
    			(19, 'UUbeaJiVBAw', 'What is Cross-Site Request Forgery (CSRF)?'),
    			(99, '7vWTEbOfa-8', 'Introduction to Burp-Suite Intruders Character Frobber Payload'),
    	        (100, 't0uMReqs8Ng', 'Introduction to Burp-Suite Intruders Grep-Extract Feature'),
                (137, 'Cazzls2sZVk', 'How to Create Ubuntu Virtual Machine (VirtualBox)'),
    	        (138, '8VCeFRwRmRU', 'How to Install VirtualBox Guest Additions (Linux)'),
    	        (139, '33CAgRKztqU', 'How to Create Ubuntu Virtual Machine (VMware)'),
    	        (140, 'cZtkVOHRYts', 'LAMP Stack: Part 1 - How to Install Apache Web Server'),
    	        (141, 'Z6IhGGxJidM', 'LAMP Stack: Part 2 - How to Install PHP'),
    	        (142, 'PsfuaRySts4', 'LAMP Stack: Part 3 - How to Install MySQL Server'),
    	        (143, 'yHCUd_5A8vo', 'How to Reset Root Password in MySQL/MariaDB'),
    	        (144, 'TcUOaeL5SJU', 'How to Install PHP Curl Library'),
    	        (145, 'e37RtQEnUhU', 'How to Install PHP XML Library'),
    	        (146, 'ZNAmmiEQuCM', 'How to Install PHP mbstring Library'),
    	        (147, '0wIUci7s3gM', 'How to Display Errors in PHP Pages'),
    	        (148, 'TcgeRab7ayM', 'How to Install Mutillidae on Linux'),
    	        (149, 'sJd0ir9-jSc', 'How to Create Self-Signed Certificate in Apache'),
    	        (150, '79mOiU3GfnQ', 'How to Create Virtual Hosts in Apache'),
                (163, 'MB_96xLh4As', 'Burp Suite 2: How to Install on Windows'),
                (164, 'ItUvwGu4Lo4', 'Burp Suite 2: How to Install on Linux'),
                (165, 'lUDrlP9x6hA', 'Burp Suite 2: Create Shortcut on Desktop (Linux)'),
                (166, '7ePmWhypzBI', 'Burp-Suite 2: Configure Firefox with Burp Suite'),
                (167, 'tsZdiuRNWRg', 'Burp Suite 2: Adding Burps Certificate to Firefox'),
                (168, 'l18nZpYiQZM', 'Burp Suite 2: Configuring Intercept Feature'),
                (169, 'K_92lb0k9FU', 'Burp Suite 2: Setting Scope'),
                (170, 'AsZqWHk7_bw', 'Burp Suite 2: Configuring Site Map and Targets'),
                (171, '9SL9968PgUk', 'Burp Suite 2: Site Map Filters'),
                (172, 'k0U47sS2o8g', 'Burp Suite 2: Proxy History'),
                (173, '5ZpM1gQ5PuQ', 'Burp Suite 2: Repeater Tool'),
                (174, 'QLDk5zI2cdM', 'Burp Suite 2: Intruder Tool - Sniper Mode'),
                (175, 'NPVddIPYn6M', 'Burp Suite 2: Intruder Tool - Battering Ram Mode'),
                (176, 'iG7003AC8ys', 'Burp Suite 2: Intruder Tool - Pitchfork Mode'),
                (177, 'ehGsDQbMXn8', 'Burp Suite 2: Intruder Tool - Cluster Mode'),
                (178, 'lT56Z54K-Jo', 'Burp Suite 2: Comparer Tool'),
                (179, 'KlWZ5pKg-PM', 'Burp Suite 2: Decoder Tool'),
                (180, 'C03EUbgRLNE', 'Burp Suite 2: Adding Extentions'),
                (181, 'ISb60TrNp8U', 'Burp Suite 2: Configuring Upstream Proxy'),
                (182, 'abqyFbeFIq4', 'How to Install Java on Windows'),
                (183, 'fJYyfZNNk5A', 'How to Install OWASP ZAP on Windows'),
                (184, 'bPr8yO_3kOg', 'How to install Java on Linux (Debian, Ubuntu, Kali)'),
                (185, 'a6_TprVx7LE', 'How to Install OWASP ZAP on Ubuntu'),
                (186, 'MpuFW_mkJ3M', 'How to Install OWASP ZAP on Linux'),
                (187, '1lblqC2Favk', 'How to Create Shortcut for OWASP ZAP (Linux)'),
                (188, 'jHGNLvSpaLs', 'How to Install and Configure Foxy Proxy with Firefox'),
                (189, 'ICPqz1Al9fk', 'How to Proxy Web Traffic through OWASP ZAP'),
                (190, 'fa5LAfXmwoo', 'How to Intercept HTTP Requests with OWASP ZAP'),
                (191, 'pGCBivHNRn8', 'How to Spider a Web Site with OWASP ZAP'),
                (192, 'b6IR2KgiOcw', 'OWASP ZAP Breakpoints: Part 1 - Trapping HTTP Requests'),
                (193, 'H2tKdwMcKnk', 'OWASP ZAP Breakpoints: Part 2 - Trapping Specific HTTP Requests'),
                (194, 'uSfGeyJKIVA', 'How to Fuzz Web Applications with OWASP ZAP (Part 1)'),
                (195, 'tBXX_GAK7BU', 'How to Fuzz Web Applications with OWASP ZAP (Part 2)'),
                (196, 'K6qwqMt_Ldc', 'OWASP ZAP: Web App Vulnerability Assessment (Single Page)'),
                (197, 'KeSUiCr-WGo', 'OWASP ZAP: Automated Web App Vulnerability Assessment (Entire Site)'),
                (198, 'RVzs8aCnpHw', 'OWASP ZAP: Web App Vulnerability Assessment (Partial Site)'),
                (199, 'ySzxNgQ6Qpk', 'How to Start OWASP ZAP from Command Line'),
                (200, 'dId4FS_Gyn4', 'Extending OWASP ZAP with Add-Ons'),
                (201, '1CJB8BtW0pQ', 'Using OWASP ZAP with Burp-Suite: Best of Both Worlds'),
    	        (202, 'ZSGE8EAHOdA', 'HTML Controls are not Security Controls'),
    	        (203, 'qkkFc_6Gr1k', 'Burp-Suite 2: Inspecting Web Sockets'),
    	        (204, 'Eqfe_HYX7MU', 'How to check HTTPS certificate from command line'),
                (205, 'Hz29n-Swx1w', 'How to check HTTPS Certificates for common issues'),
                (206, 'TpNa09xn34I', 'cURL Error: SSL Certificate Problem'),
                (207, 'inD4iQGGjb8', 'What is Content Security Policy? - Part 1'),
                (208, 'IgOLBR9bHhE', 'What is Content Security Policy? - Part 2'),
                (209, 'TO7pmkMh1Tg', 'What is Content Security Policy? - Part 3'),
                (210, 'HMm5GC6bcYs', 'What is Content Security Policy? - Part 4'),
                (211, 'leiGAuCgYY0', 'What is Content Security Policy? - Part 5'),
                (212, 'ot-dSAIEkK8', 'Content Security Policy: Script Source (script-src)'),
                (213, 'xV5o3G9wilU', 'Content Security Policy: Frame Ancestors'),
                (214, 'T86igOlSJBM', 'What are Web Server Banners?'),
                (215, 'ixy7Fr0s9Fk', 'How to Set HTTP Headers Using Apache Server'),
                (216, '7O391fuNZMk', 'Check HTTP Headers with cURL'),
                (217, 'Df8l_epP38k', 'How Cache Control Headers Work'),
                (218, 'fXu8KWIrnK4', 'What is HTTP Strict Transport Security (HSTS)?'),
                (219, '81A_cAxusTM', 'What is the HSTS Preload list?'),
                (220, 'd8liHExs5tc', 'Cookies: Part 1 - How HTTPOnly Works'),
                (221, 'ZSUGfIy1ds8', 'Cookies: Part 2 - How Secure Cookies Work'),
                (222, 'brNL_bvUCck', 'Cookies: Part 3 - How SameSite Works'),
                (223, 'JrSFc_KeNzc', 'What is the X-Frame-Options Header?'),
                (224, 'zaHznZL9SuA', 'What is the X-Content-Type-Options Header?'),
                (225, 'lCmiYKgq-o8', 'What is the Referrer Policy Header?'),
                (226, 'l7WFXv5cXzA', 'What is the XSS Protection Header?'),
                (227, 'lKX1MIIxFCg', 'SSLScan: Part 1 - How to test HTTPS, TLS, & SSL ciphers'),
                (228, 'DDbwrMrwOFc', 'SSLScan: Part 2 - How to Interpret the Results'),
                (229, 'zUwPhHigi_M', 'How to Install SSLScan on Windows'),
                (234, '2-U2s0akMqE', 'Cross-Site Scripting: Part 4 - How Output Encoding Stops XSS'),
                (235, 'JlLVhdlqe1Q', 'Cross-Site Scripting: Part 5 - How to Test Output Encoding'),
                (236, '2uWAZsCm-W8', 'How Cross-site Request Forgery (CSRF) Tokens Work'),
            	(237, 'QDIppL_j6Vo', 'What is CORS? - Part 1 - Explanation'),
            	(238, 'CxFuAcThKPA', 'What is CORS? - Part 2 - Demonstration'),
            	(239, 'qHhceEVLZvg', 'Check for Vulnerable Libraries in Your Web Application'),
            	(240, 'xczzcxL6WY0', 'How to Enable Apache Mod-Headers'),
            	(241, 'Ry884U5YTcs', 'What is Certificate Transparency - Part 1'),
            	(242, 'eCOkyuLxhl8', 'What is Certificate Transparency? - Part 2 - Expect-CT Header'),
            	(243, 'PcXZqUqBJeg', 'How to Check HTTP Headers (Command Line)'),
                (244, 'lvi_NM1n5vw', 'How to Check HTTP Headers from Browser'),
    	        (245, 'zneRNme9h3U', 'Mutillidae: Lab 1 Walkthrough'),
    	        (246, 'oy9Ya7NxDUQ', 'How to Install Wireshark in Windows 10'),
            	(247, 'akhB55S86kE', 'Introduction to Wireshark'),
            	(248, 'AIQVNlI_A20', 'Introduction to Packet Analysis - Capturing Network Traffic with TCPDump (Part 1)'),
            	(249, 'Gdmz3jtqjMM', 'Introduction to Packet Analysis - Capturing Network Traffic with TCPDump (Part 2)'),
            	(250, 'bSHfehhCxZI', 'Introduction to Packet Analysis - Packet Analysis with Wireshark (Part 1)'),
            	(251, '9bRoL-BOzr0', 'Introduction to Packet Analysis - Packet Analysis with Wireshark (Part 2)'),
    	        (252, 'ijsThXgSfHE', 'Mutillidae: Lab 2 Walkthrough'),
    	        (253, 'CUZgNTpJmJ4', 'Mutillidae: Lab 3 Walkthrough'),
    	        (254, '3yCX0MWV820', 'Mutillidae: Lab 4 Walkthrough'),
    	        (255, 'lU_fu-B5QtI', 'Mutillidae: Lab 5 Walkthrough'),
    	        (256, '6FXeO3Wx5wc', 'Mutillidae: Lab 6 Walkthrough'),
    	        (257, 'V_CsaO6RkvM', 'Mutillidae: Lab 7 Walkthrough'),
    	        (258, 'fVv39I0oXHE', 'Mutillidae: Lab 8 Walkthrough'),
    	        (259, 'KqoL60jtBWU', 'Mutillidae: Lab 9 Walkthrough'),
    	        (260, '8', 'Mutillidae: Lab 10 Walkthrough'),
    	        (261, '9', 'Mutillidae: Lab 11 Walkthrough'),
    	        (262, '10', 'Mutillidae: Lab 12 Walkthrough'),
    	        (263, '11', 'Mutillidae: Lab 13 Walkthrough'),
    	        (264, '12', 'Mutillidae: Lab 14 Walkthrough'),
    	        (265, '13', 'Mutillidae: Lab 15 Walkthrough'),
    	        (266, '14', 'Mutillidae: Lab 16 Walkthrough'),
    	        (267, 'UjEblUrWvb4', 'Mutillidae: Lab 17 Walkthrough'),
    	        (268, 'J2kq8EzhnCE', 'Mutillidae: Lab 18 Walkthrough'),
    	        (269, 'QSsLONLS5bk', 'Mutillidae: Lab 19 Walkthrough'),
    	        (270, 'PyHiBzfl0hI', 'Mutillidae: Lab 20 Walkthrough'),
    	        (271, 'NDITrGT9IqM', 'Mutillidae: Lab 21 Walkthrough'),
    	        (272, 'u8z7S1HlCHM', 'Mutillidae: Lab 22 Walkthrough'),
    	        (273, 'MfSKEtbMAjw', 'Mutillidae: Lab 23 Walkthrough'),
    	        (274, 'ydRZoXfZL9k', 'Mutillidae: Lab 24 Walkthrough'),
    	        (275, 'L0FvIGx0Co0', 'Mutillidae: Lab 25 Walkthrough'),
    	        (276, 'TzhKue6jmN0', 'Mutillidae: Lab 26 Walkthrough'),
    	        (277, 'lvVMnPQX8tE', 'Mutillidae: Lab 27 Walkthrough'),
    	        (278, 'UJunZ3Vadsc', 'Mutillidae: Lab 28 Walkthrough'),
    	        (279, 'BT6LrYLKE4A', 'Mutillidae: Lab 29 Walkthrough'),
    	        (280, 'rt-GoLEs6L4', 'Mutillidae: Lab 30 Walkthrough'),
    	        (281, 'OvDnbPbborM', 'Mutillidae: Lab 31 Walkthrough'),
                (282, 'y9vhp0llgNc', 'Mutillidae: Lab 32 Walkthrough'),
                (283, 'BlwFGkj79vk', 'Mutillidae: Lab 33 Walkthrough'),
                (284, 'ERhowYml8Ms', 'Mutillidae: Lab 34 Walkthrough'),
                (285, '6y5jl0y8Ukc', 'Mutillidae: Lab 35 Walkthrough'),
                (286, 'y1EDT6UTvqA', 'Mutillidae: Lab 36 Walkthrough'),
                (287, 'qIT-Hc_RJZI', 'Mutillidae: Lab 37 Walkthrough'),
                (288, 'NtkXw02MsQ4', 'Mutillidae: Lab 38 Walkthrough'),
                (289, '37', 'Mutillidae: Lab 39 Walkthrough'),
                (290, '38', 'Mutillidae: Lab 40 Walkthrough'),
                (291, '39', 'Mutillidae: Lab 41 Walkthrough'),
                (292, 'fyVmA7nlSVo', 'Mutillidae: Lab 42 Walkthrough'),
                (293, 'kr4KD9RJ-hA', 'Mutillidae: Lab 43 Walkthrough'),
                (294, 'ETK7l27eZTs', 'Mutillidae: Lab 44 Walkthrough'),
                (295, 'THnZOa93SOs', 'Mutillidae: Lab 45 Walkthrough'),
                (296, 'LkH_qRqcJzo', 'Mutillidae: Lab 46 Walkthrough'),
                (297, 'ICXErYUJ3sU', 'Mutillidae: Lab 47 Walkthrough'),
                (298, '46', 'Mutillidae: Lab 48 Walkthrough'),
                (299, 'Ddnid67vqq4', 'Mutillidae: Lab 49 Walkthrough'),
                (300, 'px40hbprIOM', 'Mutillidae: Lab 50 Walkthrough'),
                (301, 'DGYVXsrZrug', 'Mutillidae: Lab 51 Walkthrough'),
                (302, 'JJOqPu_oyi8', 'Mutillidae: Lab 52 Walkthrough'),
                (303, 'Gxm1_bkYcZ4', 'Mutillidae: Lab 53 Walkthrough'),
                (304, 'NF8dxs_CQA0', 'Mutillidae: Lab 54 Walkthrough'),
                (305, 'x7ibzMx4c3c', 'Mutillidae: Lab 55 Walkthrough'),
                (306, '54', 'Mutillidae: Lab 56 Walkthrough'),
                (307, '55', 'Mutillidae: Lab 57 Walkthrough'),
                (308, '56', 'Mutillidae: Lab 58 Walkthrough'),
                (309, '57', 'Mutillidae: Lab 59 Walkthrough'),
    	        (310, 'sVgXHH9GSyk', 'Mutillidae: Lab 60 Walkthrough'),
    	        (311, '6BIdjAYCyKc', 'Mutillidae: Lab 61 Walkthrough'),
    	        (312, 'z0USLZLCPPE', 'Mutillidae: Lab 62 Walkthrough'),
    	    	(313, '2fQfma45UMc', 'Mutillidae: Lab 63 Walkthrough'),
                (314, 'Y4TWdPZp2eA', 'Mutillidae: Lab 51 Walkthrough - Alternate Method'),
				(315, 'kNSAhKiXctA', 'How to Analyze Session Token Randomness using Burp-Suite Sequencer')";

    $lQueryResult = $MySQLHandler->executeQuery($lQueryString);
	if (!$lQueryResult) {
		$lErrorDetected = true;
		echo format("Failed to insert data into 'youTubeVideos' table.", "F");
	}else{
		echo format("Successfully inserted data into 'youTubeVideos' table.", "S");
	}// end if

	/* ***********************************************************************************
	 * Create accounts.xml password.txt file from MySQL accounts table
	 * ************************************************************************************/
	$lAccountXMLFilePath="data/accounts.xml";
	$lPasswordFilePath="passwords/accounts.txt";

	echo format("Trying to build XML version of accounts table to update accounts XML ".$lAccountXMLFilePath,"I");
	echo format("Do not worry. A default version of the file is included if this does not work.","I");

	echo format("Trying to build text version of accounts table to update password text file ".$lPasswordFilePath,"I");
	echo format("Do not worry. A default version of the file is included if this does not work.","I");

	$lAccountsXML = "";
	$lAccountsText = "";
	$lQueryString = "SELECT * FROM accounts;";
	$lQueryResult = $MySQLHandler->executeQuery($lQueryString);

	if (isset($lQueryResult->num_rows) && $lQueryResult->num_rows > 0) {
		$lResultsFound = true;
		$lRecordsFound = $lQueryResult->num_rows;
	}//end if

	if ($lResultsFound){

		echo format("Executed query 'SELECT * FROM accounts'. Found ".$lRecordsFound." records.","S");

		$lAccountsXML = '<?xml version="1.0" encoding="utf-8"?>'.PHP_EOL;
		$lAccountsXML .= "<Accounts>".PHP_EOL;
		$lCounter = 1;
		$cTAB = CHR(9);
		
		$lAccountsText = "CID,Username,Password,Signature,Type,FirstName,LastName,ClientID,ClientSecret".PHP_EOL; // Add CSV header
		
		while($row = $lQueryResult->fetch_object()){
			$lAccountType = $row->is_admin == 'TRUE' ? "Admin" : "User";
			
			// XML Generation
			$lAccountsXML .= $cTAB.'<Account ID="'.$lCounter.'">'.PHP_EOL;
			$lAccountsXML .= $cTAB.$cTAB.'<CID>'.htmlspecialchars($row->cid).'</CID>'.PHP_EOL;
			$lAccountsXML .= $cTAB.$cTAB.'<UserName>'.htmlspecialchars($row->username).'</UserName>'.PHP_EOL;
			$lAccountsXML .= $cTAB.$cTAB.'<Password>'.htmlspecialchars($row->password).'</Password>'.PHP_EOL;
			$lAccountsXML .= $cTAB.$cTAB.'<Signature>'.htmlspecialchars($row->mysignature).'</Signature>'.PHP_EOL;
			$lAccountsXML .= $cTAB.$cTAB.'<Type>'.htmlspecialchars($lAccountType).'</Type>'.PHP_EOL;
			$lAccountsXML .= $cTAB.$cTAB.'<FirstName>'.htmlspecialchars($row->firstname).'</FirstName>'.PHP_EOL;
			$lAccountsXML .= $cTAB.$cTAB.'<LastName>'.htmlspecialchars($row->lastname).'</LastName>'.PHP_EOL;
			$lAccountsXML .= $cTAB.$cTAB.'<ClientID>'.htmlspecialchars($row->client_id).'</ClientID>'.PHP_EOL;
			$lAccountsXML .= $cTAB.$cTAB.'<ClientSecret>'.htmlspecialchars($row->client_secret).'</ClientSecret>'.PHP_EOL;
			$lAccountsXML .= $cTAB.'</Account>'.PHP_EOL;
		
			// CSV Generation
			$lAccountsText .= $row->cid.","
						   . $row->username.","
						   . $row->password.","
						   . $row->mysignature.","
						   . $lAccountType.","
						   . $row->firstname.","
						   . $row->lastname.","
						   . $row->client_id.","
						   . $row->client_secret.PHP_EOL;
		
			$lCounter += 1;
		}
		
		$lAccountsXML .= "</Accounts>".PHP_EOL;
		
		try {
			// Ensure the directories exist and are writable
			if (!is_dir(pathinfo($lAccountXMLFilePath, PATHINFO_DIRNAME))) {
				echo format("Oh no. Trying to create an XML version of the accounts file did not work out. The directory " . $lAccountXMLFilePath . " does not exist.","F");
			}
			if (!is_dir(pathinfo($lPasswordFilePath, PATHINFO_DIRNAME))) {
				echo format("Oh no. Trying to create a text version of the accounts file did not work out. The directory " . $lPasswordFilePath . " does not exist.","F");
			}
			if (!is_writable(pathinfo($lAccountXMLFilePath, PATHINFO_DIRNAME))) {
				echo format("Oh no. Trying to create an XML version of the accounts file did not work out. The directory " . $lAccountXMLFilePath . " is not writable.","F");
			}
			if (!is_writable(pathinfo($lPasswordFilePath, PATHINFO_DIRNAME))) {
				echo format("Oh no. Trying to create a text version of the accounts file did not work out. The directory " . $lPasswordFilePath . " is not writable.","F");
			}

			// XML File Writing
			try {
				if (is_writable(pathinfo($lAccountXMLFilePath, PATHINFO_DIRNAME))) {
					file_put_contents($lAccountXMLFilePath, $lAccountsXML);
					echo format("Wrote accounts to " . $lAccountXMLFilePath, "S");
				}else{
					echo format("Could not write accounts XML to " . $lAccountXMLFilePath . " - Directory not writable", "W");
					echo format("Using default version of accounts.xml", "W");
				}
			} catch (Exception $e) {
				echo format("Could not write accounts XML to " . $lAccountXMLFilePath . " - " . $e->getMessage(), "W");
				echo format("Using default version of accounts.xml", "W");
			}
		
			// Text File Writing
			try {
				if (is_writable(pathinfo($lPasswordFilePath, PATHINFO_DIRNAME))) {
					file_put_contents($lPasswordFilePath, $lAccountsText);
					echo format("Wrote accounts to " . $lPasswordFilePath, "S");
				}else{
					echo format("Could not write accounts text to " . $lPasswordFilePath . " - Directory not writable", "W");
					echo format("Using default version of accounts.txt", "W");
				}
			} catch (Exception $e) {
				echo format("Could not write accounts text to " . $lPasswordFilePath . " - " . $e->getMessage(), "W");
				echo format("Using default version of accounts.txt", "W");
			}
		
		} catch (Exception $e) {
			$lErrorDetected = true;
			echo $CustomErrorHandler->FormatError($e, $lQueryString);
		}
		
	} else {
		$lErrorDetected = true;
		echo format("Warning: No records found when trying to build XML and text version of accounts table ".$lQueryResult,"W");
	}// end if ($lResultsFound)

	$MySQLHandler->closeDatabaseConnection();

} catch (Exception $e) {
	$lErrorDetected = true;
	echo $CustomErrorHandler->FormatError($e, $lQueryString);
}// end try

if (!$lErrorDetected) {
    // Check for HTTP referer (only if available)
    $lHTTPReferer = $_SERVER["HTTP_REFERER"] ?? "";

    // Determine if the referer matches certain conditions
    $lReferredFromOfflinePage = strpos($lHTTPReferer, "database-offline.php") !== false;

    // Redirect to home page if offline page triggered the reset
    if ($lReferredFromOfflinePage) {
        $lRedirectLocation = "index.php?page=home.php&popUpNotificationCode=SUD1";
    } else {
        $lRedirectLocation = "index.php?page=home.php";
    }

    // JavaScript-based redirect to ensure the session is cleared before the redirect
    echo "<script>
        if (confirm('Database reset successful. Click OK to continue to the home page. Click Cancel to stay on this page.')) {
            window.location.href = '$lRedirectLocation';
        }
    </script>";
}

$CustomErrorHandler = null;
?>
	</body>
</html>