<?php

	/* ------------------------------------------
	 * @VERSION
	 * ------------------------------------------*/
	$C_VERSION = "2.8.78";
	$C_VERSION_STRING = "Version: " . $C_VERSION;
	$C_MAX_HINT_LEVEL = 1;

    /* ------------------------------------------
     * Determine the root of the entire project.
     * Recall this file is in the "includes" folder
     * so its "2 levels deep".
	 * ------------------------------------------*/
	define('__ROOT__', dirname(dirname(__FILE__)));

	/* ------------------------------------------
	 * @DANGEROUS TAG TOKENIZATION CONSTANTS
	 * ------------------------------------------*/
	/* Defined our constants to use to tokenize allowed HTML characters.
	 * Why use GUIDs? GUIDs are unique, they are very unlikely to be typed in
	 * by users, and they are alpha numeric. It is important that there be
	 * a mathematically slim chance that the user would input the token
	 * as part of normal input. For example, the character "X" would work
	 * fine as a token for the bold tag, but it is likely the user would
	 * want to use the letter "X" as the letter "X" and not to
	 * represent a bold tag. GUIDs solve this issue. Equally important
	 * the GUID is alphanumeric so when we encode our output, the GUID
	 * will not be modified. When alpha-numeric characters are encoded,
	 * they come out the same as before encoding. */
	define('BOLD_STARTING_TAG','9880e8bc4fcb4794a875e8ca8d493e65');
	define('BOLD_ENDING_TAG','9880e8bc4fcb4794a875e8ca8d493e66');
	define('ITALIC_STARTING_TAG','7dc0116a0d514496adbb456fd60b00ac');
	define('ITALIC_ENDING_TAG','7dc0116a0d514496adbb456fd60b00ad');
	define('UNDERLINE_STARTING_TAG','7dc0116a0d514496adbb456fd60b001d');
	define('UNDERLINE_ENDING_TAG','7dc0116a0d514496adbb456fd60b002d');

	// IPV6 - Credit: http://www.vankouteren.eu/blog/2009/05/working-ipv6-regular-expression/#more-84
	define('IPV6_REGEX_PATTERN', "/^\s*((([0-9A-Fa-f]{1,4}:){7}(([0-9A-Fa-f]{1,4})|:))|(([0-9A-Fa-f]{1,4}:){6}(:|((25[0-5]|2[0-4]\d|[01]?\d{1,2})(\.(25[0-5]|2[0-4]\d|[01]?\d{1,2})){3})|(:[0-9A-Fa-f]{1,4})))|(([0-9A-Fa-f]{1,4}:){5}((:((25[0-5]|2[0-4]\d|[01]?\d{1,2})(\.(25[0-5]|2[0-4]\d|[01]?\d{1,2})){3})?)|((:[0-9A-Fa-f]{1,4}){1,2})))|(([0-9A-Fa-f]{1,4}:){4}(:[0-9A-Fa-f]{1,4}){0,1}((:((25[0-5]|2[0-4]\d|[01]?\d{1,2})(\.(25[0-5]|2[0-4]\d|[01]?\d{1,2})){3})?)|((:[0-9A-Fa-f]{1,4}){1,2})))|(([0-9A-Fa-f]{1,4}:){3}(:[0-9A-Fa-f]{1,4}){0,2}((:((25[0-5]|2[0-4]\d|[01]?\d{1,2})(\.(25[0-5]|2[0-4]\d|[01]?\d{1,2})){3})?)|((:[0-9A-Fa-f]{1,4}){1,2})))|(([0-9A-Fa-f]{1,4}:){2}(:[0-9A-Fa-f]{1,4}){0,3}((:((25[0-5]|2[0-4]\d|[01]?\d{1,2})(\.(25[0-5]|2[0-4]\d|[01]?\d{1,2})){3})?)|((:[0-9A-Fa-f]{1,4}){1,2})))|(([0-9A-Fa-f]{1,4}:)(:[0-9A-Fa-f]{1,4}){0,4}((:((25[0-5]|2[0-4]\d|[01]?\d{1,2})(\.(25[0-5]|2[0-4]\d|[01]?\d{1,2})){3})?)|((:[0-9A-Fa-f]{1,4}){1,2})))|(:(:[0-9A-Fa-f]{1,4}){0,5}((:((25[0-5]|2[0-4]\d|[01]?\d{1,2})(\.(25[0-5]|2[0-4]\d|[01]?\d{1,2})){3})?)|((:[0-9A-Fa-f]{1,4}){1,2})))|(((25[0-5]|2[0-4]\d|[01]?\d{1,2})(\.(25[0-5]|2[0-4]\d|[01]?\d{1,2})){3})))(%.+)?\s*$/");

	// Credits: IP - Jeremy Druin
	define('IPV4_REGEX_PATTERN', "/^\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}$/");

	// Credits : IANA - http://www.shauninman.com/archive/2006/05/08/validating_domain_names
	define('DOMAIN_NAME_REGEX_PATTERN',"/^([a-z0-9]([-a-z0-9]*[a-z0-9])?\\.)+((a[cdefgilmnoqrstuwxz]|aero|arpa)|(b[abdefghijmnorstvwyz]|biz)|(c[acdfghiklmnorsuvxyz]|cat|com|coop)|d[ejkmoz]|(e[ceghrstu]|edu)|f[ijkmor]|(g[abdefghilmnpqrstuwy]|gov)|h[kmnrtu]|(i[delmnoqrst]|info|int)|(j[emop]|jobs)|k[eghimnprwyz]|l[abcikrstuvy]|(m[acdghklmnopqrstuvwxyz]|mil|mobi|museum)|(n[acefgilopruz]|name|net)|(om|org)|(p[aefghklmnrstwy]|pro)|qa|r[eouw]|s[abcdeghijklmnortvyz]|(t[cdfghjklmnoprtvwz]|travel)|u[agkmsyz]|v[aceginu]|w[fs]|y[etu]|z[amw])$/i");

	define('XML_EXTERNAL_ENTITY_REGEX_PATTERNS',"/<![CDATA|<!ELEMENT|<!ENTITY|<!DOCTYPE]/i");
	define("VALID_XML_CHARACTERS","/[\x{0009}\x{000a}\x{000d}\x{0020}-\x{D7FF}\x{E000}-\x{FFFD}]/u");

?>
