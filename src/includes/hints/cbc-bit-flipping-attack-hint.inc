			<br/><br/>
			<span class="report-header">Overview</span>
			<br/><br/>
			Given an encrypted value, CBC Bit Flipping
			can be used to alter the decrypted plain text. If the plain
			text is used by the system to make decisions (such as the
			user privilege level), the system can be influenced by
			CBC bit flipping.
<br/><br/>
<a href="#videos" class="label"><img alt="YouTube" src="/images/youtube-play-icon-40-40.png" style="margin-right: 10px;" />Video Tutorials</a>
			<br/><br/>
			<span class="report-header">Discovery Methodology</span>
			<br/><br/>
			Toggle bits and bytes of initialization vectors and observe changes to the
			plain text.
			<br/><br/>
			<span class="report-header">Exploitation</span>
			<br/><br/>
			Alter the initialization vector only in the bits or bytes that affect
			the target plain text. Small amounts of plain text can be brute forced.
			For better results, calculate the value of initialization vector
			needed to produce the plain text desired.
			<br/><br/>
			<span class="report-header">Example</span>
			<br/><br/>
		  	The view user privilege level page is vulnerable to cipher block chaining (CBC) bit flipping.
		  	The initialization vector is passed in URL parameter "IV". Three fields are encrypted then
		  	XOR'ed with the IV. While the encryption key used cannot be determined easily the
		  	IV is under the control of the user.
		  	<br/><br/>
		  	The goal is to modify the
		  	initialization vector (IV) in order to cause the user ID and group ID
		  	to both be "000". When this occurs a message will appear indicating the
		  	user has root privileges.
			<br/><br/>
	  		First, determine which of the bytes affects the user ID and group ID respectively.
	  		Changing the first byte of the IV changes the first character of the Application ID.
			Changing the first byte from 6B to 00 results in the letter "A" changing to an asterisk ("*").
			However the goal is to change the user ID and group ID.
	  		<br/><br/>
	  		Modify each byte until the user ID and group ID are affected. Note the position
	  		of the bytes carefully. One byte in the IV will affect the "1" in the user
	  		ID and another byte will affect the "1" in the group ID.
	  		After methodically testing each byte of the IV, it will be found that bytes 5-7
	  		affect the 3 bytes of the UID
			By extension bytes 8-10 affect the 3 bytes of the GID
	  		<br/><br/>
			Note that in security level 0 the user ID and group ID are already "100".
			Only the first
			character ("1") needs to be modified. Try to leave the "00" alone.
			<br/><br/>
			Recall byte 5 of the IV (0xAB) maps to the first character of the user ID ("1")
			Bytes 6 and 7 map to characters 2 and 3 of the UID so those should be left alone since those characters are already 0
			Byte 8 of the IV (0x25) maps to the first character of the GID ("1")
			<br/><br/>
	  		A byte can only have 255 distinct values. One way to solve this problem
	  		is to brute force the answer by trying all 255 bytes until a "0" appears
	  		where the "1" is currently shown.
			<br/><br/>
	  		A much better way is to XOR the value you input with the value that appears
	  		in the User ID or Group ID. This is the respective byte of the cipher text.
	  		Next, XOR this byte of cipher text with the byte you want to appear; "0" which
	  		is 0X30.
	  		<br/><br/>
			Although the encryption key is not known, it can be seen that the
			encrypted value XOR with byte 5 of the IV (0xAB) equals "1" (0X31).
			Therefore 0xAB XOR 0x31 = encrypted value.
			We cannot decrypt the encrypted value but we can determine the encrypted value.
			<br/>
<code>
	? XOR 0XAB = 0x31 therefore 0XAB XOR 0x31 = ?
	0xAB: 1010 1011 Current IV
	0x31: 0011 0001 Current value
	--------------- (xor)
	0x9A: 1001 1010 Encrypted value
</code>
			<br/>
			XOR is communicative (A xor B implies B xor A)
			The IV needed to make the first character "0" (0x30) can be calculated
			by XOR'ing the encrypted value with the desire character
			<br />
<code>
	0x9A XOR 0x31 = ?
	0x9A: 1001 1010 Encrypted value
	0x30: 0011 0000 Desired value
	--------------- (xor)
	0xAA: 1010 1010 Needed IV
</code>
			<br/>
			Injecting 0xAA as byte 5 changes the user ID to "0" (0x30).
			<br/><br/>
			By extension, Byte 8 of the IV (0x25) causes the first character of the GID to be "1" (0X31).
			The needed IV value can be calculated using same technique used for UID.
			<br/>
<code>
	0x25: 0010 0101 Current IV
	0x31: 0011 0001 Current value
	--------------- (xor)
	0x14: 0001 0100 Encrypted value
	0x30: 0011 0000 Desired value
	--------------- (xor)
	0x24: 0010 0100 Needed IV (byte 8)
</code>
			<br/>
			Injecting 0x24 as byte 8 changes the group ID to "0" (0x30)
			<br/><br/>
		  	The final answer is "6bc24fc1aa650b24b4114e93a98f1eba" for security level 0 but
		  	not for security level 1. Note the initial user ID in security level
		  	1 is more challenging to change correctly.
			<br/><br/>
			<span id="videos" class="report-header">Videos</span>
			<br/><br/>
			<?php echo $YouTubeVideoHandler->getYouTubeVideo($YouTubeVideoHandler->IntroductiontoBurpSuiteIntrudersCharacterFrobberPayload);?>
			<?php echo $YouTubeVideoHandler->getYouTubeVideo($YouTubeVideoHandler->IntroductiontoBurpSuiteIntrudersGrepExtractFeature);?>
			<?php echo $YouTubeVideoHandler->getYouTubeVideo($YouTubeVideoHandler->HowtoFuzzWebApplicationswithOWASPZAPPart2);?>
		  	<br/><br/>