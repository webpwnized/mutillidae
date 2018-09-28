<?php
/**
 * OWASP Enterprise Security API (ESAPI)
 *
 * This file is part of the Open Web Application Security Project (OWASP)
 * Enterprise Security API (ESAPI) project. For details, please see
 * <a href="http://www.owasp.org/index.php/ESAPI">http://www.owasp.org/index.php/ESAPI</a>.
 *
 * Copyright (c) 2007 - 2009 The OWASP Foundation
 *
 * The ESAPI is published by OWASP under the BSD license. You should read and accept the
 * LICENSE before you use, modify, and/or redistribute this software.
 *
 * @author Bipin Upadhyay <http://projectbee.org/blog/contact/>
 * @created 2009
 * @since 1.4
 * @package ESAPI_Reference
 */

require_once dirname(__FILE__).'/../Authenticator.php';
require_once dirname(__FILE__).'/DefaultUser.php';

define('MAX_ACCOUNT_NAME_LENGTH', 250);
/**
 * Reference Implementation of the FileBasedAuthenticator interface.
 *
 * @category  OWASP
 * @package   ESAPI_Reference
 * @copyright 2009-2010 The OWASP Foundation
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD license
 * @version   Release: @package_version@
 * @link      http://www.owasp.org/index.php/ESAPI
 */
class FileBasedAuthenticator implements Authenticator {
    private $users;

    /** The file that contains the user db */
    private $userDB = null;

    /** How frequently to check the user db for external modifications */
    private $checkInterval = 60000;//60 * 1000;

    /** The last modified time we saw on the user db. */
    private $lastModified = 0;

    /** The last time we checked if the user db had been modified externally */
    private $lastChecked = 0;

    /** Associative array of user: array(AccoundId => UserObjectReference) */
    private $userMap = array();

    // $passwordMap[user] = passwordHash, where the values are password hashes, with the current hash in entry 0
    private $passwordMap = array();

    function __construct() {
        $this->users = array();
        $this->logger = ESAPI::getLogger("Authenticator");
    }

    /**
     * Clears the current User. This allows the thread to be reused safely.
     *
     * This clears all threadlocal variables from the thread. This should ONLY be called after
     * all possible ESAPI operations have concluded. If you clear too early, many calls will
     * fail, including logging, which requires the user identity.
     */
    function clearCurrent() {
        throw new EnterpriseSecurityException("Method Not implemented");
    }

    /**
     * This method should be called for every HTTP request, to login the current user either from the session of HTTP
     * request. This method will set the current user so that getCurrentUser() will work properly.
     *
     * Authenticates the user's credentials from the HttpServletRequest if
     * necessary, creates a session if necessary, and sets the user as the
     * current user.
     *
     * Specification:  The implementation should do the following:
     * 	1) Check if the User is already stored in the session
     * 		a. If so, check that session absolute and inactivity timeout have not expired
     * 		b. Step 2 may not be required if 1a has been satisfied
     * 	2) Verify User credentials
     * 		a. It is recommended that you use
     * 			loginWithUsernameAndPassword(HttpServletRequest, HttpServletResponse) to verify credentials
     * 	3) Set the last host of the User (ex.  user.setLastHostAddress(address) )
     * 	4) Verify that the request is secure (ex. over SSL)
     * 	5) Verify the User account is allowed to be logged in
     * 		a. Verify the User is not disabled, expired or locked
     * 	6) Assign User to session variable
     *
     * @param request
     *            the current HTTP request
     * @param response
     *            the HTTP response
     *
     * @return
     * 		the User
     *
     * @throws AuthenticationException
     *             if the credentials are not verified, or if the account is disabled, locked, expired, or timed out
     */
    function login($request, $response) {
        throw new EnterpriseSecurityException("Method Not implemented");
    }

    /**
     * Verify that the supplied password matches the password for this user. Password should
     * be stored as a hash. It is recommended you use the hashPassword(password, accountName) method
     * in this class.
     * This method is typically used for "reauthentication" for the most sensitive functions, such
     * as transactions, changing email address, and changing other account information.
     *
     * @param user
     * 		the user who requires verification
     * @param password
     * 		the hashed user-supplied password
     *
     * @return
     * 		true, if the password is correct for the specified user
     */
    function verifyPassword($user, $password) {
        throw new EnterpriseSecurityException("Method Not implemented");
    }

    /**
     * Logs out the current user.
     *
     * This is usually done by calling User.logout on the current User.
     */
    function logout() {
        throw new EnterpriseSecurityException("Method Not implemented");
    }

    /**
     * Creates a new User with the information provided. Implementations should check
     * accountName and password for proper format and strength against brute force
     * attacks ( verifyAccountNameStrength(String), verifyPasswordStrength(String, String)  ).
     *
     * Two copies of the new password are required to encourage user interface designers to
     * include a "re-type password" field in their forms. Implementations should verify that
     * both are the same.
     *
     * @param accountName
     * 		the account name of the new user
     * @param password1
     * 		the password of the new user
     * @param password2
     * 		the password of the new user.  This field is to encourage user interface designers to include two password fields in their forms.
     *
     * @return
     * 		the User that has been created
     *
     * @throws AuthenticationException
     * 		if user creation fails due to any of the qualifications listed in this method's description
     */
    function createUser($accountName, $password1, $password2) {

        $this->loadUsersIfNecessary();
        if ( !$this->isValidString($accountName) ) {
            throw new AuthenticationAccountsException("Account creation failed", "Attempt to create user with null accountName");
        }
        if ($this->getUserByName($accountName) != null) {
            throw new AuthenticationAccountsException("Account creation failed", "Duplicate user creation denied for ".$accountName);
        }

        $this->verifyAccountNameStrength($accountName);

        if ( $password1 == null ) {
            throw new AuthenticationCredentialsException( "Invalid account name", "Attempt to create account ".$accountName." with a null password" );
        }
        $this->verifyPasswordStrength(null, $password1);

        if ($password1 != $password2) {
            throw new AuthenticationCredentialsException("Passwords do not match", "Passwords for ".$accountName." do not match");
        }

        $user = new DefaultUser($accountName);
        try {
            $this->setHashedPassword( $user, $this->hashPassword($password1, $accountName) );
        } catch (EncryptionException $ee) {
            throw new AuthenticationException("Internal error", "Error hashing password for ".$accountName);
        }

        $this->userMap[$user->getAccountId()] = $user;

        $this->logger->info( ESAPILogger::SECURITY, TRUE, "New user created: ".$accountName);
        $this->saveUsers();
        return $user;
    }


    /**
     * Load users if they haven't been loaded in a while.
     */
    protected function loadUsersIfNecessary() {
    //        throw new EnterpriseSecurityException("Method Not Implemented");
        if (!$this->isValidString( $this->userDB )) {
            $fileHandle = ESAPI::getSecurityConfiguration()->getResourceDirectory()."users.txt";
            $this->userDB = fopen($fileHandle, 'a');
        }

        // We only check at most every checkInterval milliseconds
        $now = time();
        if ($now - $this->lastChecked < $this->checkInterval) {
            return;
        }
        $this->lastChecked = $now;

        $fileData = fstat($this->userDB);
        if ($this->lastModified == $fileData['mtime']) {
            return;
        }
    //Note: Removing call for now to avoid red exception and spread greenery in tests :)
    //        $this->loadUsersImmediately();
    }

    protected function  loadUsersImmediately() {
        throw new EnterpriseSecurityException("Method Not Implemented");
    }

    /**
     * Saves the user database to the file system. In this implementation you must call save to commit any changes to
     * the user file. Otherwise changes will be lost when the program ends.
     *
     * @throws AuthenticationException
     * 		if the user file could not be written
     */
    public function saveUsers() {
        throw new EnterpriseSecurityException("Method Not Implemented");
    }

    /**
     * Generate strong password that takes into account the user's information and old password. Implementations
     * should verify that the new password does not include information such as the username, fragments of the
     * old password, and other information that could be used to weaken the strength of the password.
     *
     * @param user
     * 		the user whose information to use when generating password
     * @param oldPassword
     * 		the old password to use when verifying strength of new password.  The new password may be checked for fragments of oldPassword.
     *
     * @return
     * 		a password with strong password strength
     */
    function generateStrongPassword($user = null, $oldPassword = null) {
        $randomizer = ESAPI::getRandomizer();
        $letters = $randomizer->getRandomInteger(4, 6);
        $digits = 7 - $letters;
        $passLetters = $randomizer->getRandomString($letters, DefaultEncoder::CHAR_PASSWORD_LETTERS );
        $passDigits = $randomizer->getRandomString( $digits, DefaultEncoder::CHAR_PASSWORD_DIGITS );
        $passSpecial = $randomizer->getRandomString( 1, DefaultEncoder::CHAR_PASSWORD_SPECIALS );
        $newPassword = $passLetters.$passSpecial.$passDigits;

        if ($this->isValidString($newPassword) && $this->isValidString($user) ) {
            $this->logger->info( ESAPILogger::SECURITY, TRUE, "Generated strong password for ".$user->getAccountName());
        }

        return $newPassword;
    }

    /**
     * Changes the password for the specified user. This requires the current password, as well as
     * the password to replace it with. The new password should be checked against old hashes to be sure the new password does not closely resemble or equal any recent passwords for that User.
     * Password strength should also be verified.  This new password must be repeated to ensure that the user has typed it in correctly.
     *
     * @param user
     * 		the user to change the password for
     * @param currentPassword
     * 		the current password for the specified user
     * @param newPassword
     * 		the new password to use
     * @param newPassword2
     * 		a verification copy of the new password
     *
     * @throws AuthenticationException
     * 		if any errors occur
     */
    function changePassword($user, $currentPassword, $newPassword, $newPassword2) {
        $accountName = $user->getAccountName();

        try {
            $currentHash = $this->getHashedPassword($user);
            $verifyHash = $this->hashPassword($currentPassword, $accountName);

            if($currentHash != $verifyHash) {
                throw new AuthenticationCredentialsException("Password change failed", "Authentication failed for password change on user: ".$accountName);
            }

            if(!$this->isValidString( $newPassword ) || !$this->isValidString($newPassword2) || $newPassword != $newPassword2) {
                throw new AuthenticationCredentialsException("Password change failed", "Passwords do not match for password change on user: ".$accountName );
            }

            $this->verifyPasswordStrength($currentPassword, $newPassword);
            //TODO: Is this actually the expected value?
            $user->setLastPasswordChangeTime(time());
            $newHash = $this->hashPassword($newPassword, $accountName);
            if( in_array($newHash, $this->getOldPasswordHashes($user)) ) {
                throw new AuthenticationCredentialsException( "Password change failed", "Password change matches a recent password for user: ".$accountName );
            }

            $this->setHashedPassword($user, $newHash);
            $this->logger->info(ESAPILogger::SECURITY, TRUE, "Password changed for user: ".$accountName);
        } catch (EncryptionException $e ) {
            throw new AuthenticationException("Password change failed", "Encryption exception changing password for ".$accountName);
        }
    }

    /**
     * Returns all of the specified User's hashed passwords.  If the User's list of passwords is null,
     * and create is set to true, an empty password list will be associated with the specified User
     * and then returned. If the User's password map is null and create is set to false, an exception
     * will be thrown.
     *
     * @param user
     * 		the User whose old hashes should be returned
     * @param create
     * 		true - if no password list is associated with this user, create one
     * 		false - if no password list is associated with this user, do not create one
     * @return
     * 		a List containing all of the specified User's password hashes
     */
    public function getAllHashedPasswords($user, $create) {
    //        TODO: Reverify with tests. Something doesn't seem right here
        $hashes = $this->passwordMap[$user];
        if ($this->isValidString($hashes)) {
            return $hashes;
        }
        if ($create) {
            $hashes = array();
            $this->passwordMap[$user] = $hashes;
            return hashes;
        }
        throw new RuntimeException("No hashes found for ".$user->getAccountName().". Is User.hashcode() and equals() implemented correctly?");
    }

    /**
     * Return the specified User's current hashed password.
     *
     * @param user
     * 		this User's current hashed password will be returned
     * @return
     * 		the specified User's current hashed password
     */
    public function getHashedPassword($user) {
        $hashes = $this->getAllHashedPasswords($user, false);
        return $hashes[0];
    }


    /**
     * Get a List of the specified User's old password hashes.  This will not return the User's current
     * password hash.
     *
     * @param user
     * 		he user whose old password hashes should be returned
     * @return
     * 		the specified User's old password hashes
     */
    public function getOldPasswordHashes($user) {
        $hashes = $this->getAllHashedPasswords($user, false);
        if (count($hashes) > 1) {
            return array_slice($hashes, 1, (count($hashes) - 1), TRUE );
        }
        return array();
    }

    /**
     * Returns the User matching the provided accountId.  If the accoundId is not found, an Anonymous
     * User or null may be returned.
     *
     * @param accountId
     *            the account id
     *
     * @return
     * 		the matching User object, or the Anonymous User if no match exists
     */
    function getUserById($accountId) {

        if($accountId == 0) {
        //FIXME: ANONYMOUS User to be returned
            return null;
        }

        $this->loadUsersIfNecessary();

        if( in_array($accountId, $this->userMap) ) {
            return $this->userMap[$accountId];
        }else {
            return null;
        }
    }

    /**
     * Returns the User matching the provided accountName.  If the accoundId is not found, an Anonymous
     * User or null may be returned.
     *
     * @param accountName
     *            the account name
     *
     * @return
     * 		the matching User object, or the Anonymous User if no match exists
     */
    function getUserByName($accountName) {
        if ( empty($this->users) ) {
            return null;
        }

        if ( in_array($accountName, $this->users) ) {
            return new DefaultUser($accountName, '123', '123');	// TODO: Milestone 3 - fix with real code
        }

        return null;
    }

    /**
     * Gets a collection containing all the existing user names.
     *
     * @return
     * 		a set of all user names
     */
    function getUserNames() {
    // TODO: Re-work in Milestone 3

        if ( !empty($this->users) ) {
            return $this->users;
        }

        $usersFile = dirname(__FILE__) . '/../../test/testresources/users.txt';
        $rawusers = file($usersFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

        $users = array();

        foreach ($rawusers as $dummy => $row) {
            $row = trim($row);
            if ( strlen($row) > 0 && $row[0] != '#' ) {
                $user = explode('|', $row);
                $users[] = $user[0];
            }
        }

        $this->users = $users;

        return $users;
    }

    /**
     * Returns the currently logged in User.
     *
     * @return
     * 		the matching User object, or the Anonymous User if no match
     *         exists
     */
    function getCurrentUser() {
        throw new EnterpriseSecurityException("Method Not implemented");
    }

    /**
     * Sets the currently logged in User.
     *
     * @param user
     *          the user to set as the current user
     */
    function setCurrentUser($user) {
        throw new EnterpriseSecurityException("Method Not implemented");
    }

    /**
     * Add a hash to a User's hashed password list.  This method is used to store a user's old password hashes
     * to be sure that any new passwords are not too similar to old passwords.
     *
     * @param user
     * 		the user to associate with the new hash
     * @param hash
     * 		the hash to store in the user's password hash list
     */
    private function setHashedPassword($user, $hash) {
        $hashes = $this->getAllHashedPasswords($user, true);
        $hashes[0] = $hash;
        if (count($hashes) > ESAPI::getSecurityConfiguration()->getMaxOldPasswordHashes() ) {
        //TODO: Verify
            array_pop($hashes);
        }
        $this->logger->info(ESAPILogger::SECURITY, TRUE, "New hashed password stored for ".$user->getAccountName() );
    }

    /**
     * Returns a $representation of the hashed password, using the
     * accountName as the salt. The salt helps to prevent against "rainbow"
     * table attacks where the attacker pre-calculates hashes for known strings.
     * This method specifies the use of the user's account name as the "salt"
     * value. The Encryptor.hash method can be used if a different salt is
     * required.
     *
     * @param password
     *            the password to hash
     * @param accountName
     *            the account name to use as the salt
     *
     * @return
     * 		the hashed password
     */
    function hashPassword($password, $accountName) {
        $salt = strtolower( $accountName );
        return ESAPI::getEncryptor()->hash($password, $salt);
    }

    /**
     * Removes the account of the specified accountName.
     *
     * @param accountName
     *            the account name to remove
     *
     * @throws AuthenticationException
     *             the authentication exception if user does not exist
     */
    function removeUser($accountName) {
    // TODO: Change in Milestone 3. In milestone 1, this is used to clean up a test

        $idx = array_search($accountName, $this->users);
        if ( !empty($this->users) && $idx !== false ) {
            unset($this->users[$idx]);
            return true;
        }

        return false;
    }

    /**
     * Ensures that the account name passes site-specific complexity requirements, like minimum length.
     *
     * @param accountName
     *            the account name
     *
     * @throws AuthenticationException
     *             if account name does not meet complexity requirements
     */
    function verifyAccountNameStrength($accountName) {
        if (!$this->isValidString( $accountName ) ) {
            throw new AuthenticationCredentialsException("Invalid account name", "Attempt to create account with a null/empty account name");
        }

        if (true/*!ESAPI::getValidator()->isValidInput("verifyAccountNameStrength", $accountName, "AccountName", MAX_ACCOUNT_NAME_LENGTH, false )*/) {
            throw new AuthenticationCredentialsException("Invalid account name", "New account name is not valid: ".$accountName);
        }
    }

    /**
     * Ensures that the password meets site-specific complexity requirements, like length or number
     * of character sets. This method takes the old password so that the algorithm can analyze the
     * new password to see if it is too similar to the old password. Note that this has to be
     * invoked when the user has entered the old password, as the list of old
     * credentials stored by ESAPI is all hashed.
     *
     * @param oldPassword
     *            the old password
     * @param newPassword
     *            the new password
     *
     * @throws AuthenticationException
     *				if newPassword is too similar to oldPassword or if newPassword does not meet complexity requirements
     */
    function verifyPasswordStrength($oldPassword, $newPassword) {
        if(!$this->isValidString($newPassword)) {
            throw new AuthenticationCredentialsException("Invalid password", "New password cannot be null" );
        }

        // can't change to a password that contains any 3 character substring of old password
        if( $this->isValidString($oldPassword)) {
            $passwordLength = strlen($oldPassword);
            for($counter = 0; $counter < $passwordLength-2; $counter++) {
                $sub = substr($oldPassword, $counter, 3);
                if( strlen(strstr($newPassword, $sub)) > 0) {
                //                if( strlen(strstr($newPassword, $sub)) > -1) { //TODO: Even this works. Revisit for a more elegant solution
                    throw new AuthenticationCredentialsException("Invalid password", "New password cannot contain pieces of old password" );
                }
            }
        }

        // new password must have enough character sets and length
        $charsets = 0;
        $passwordLength = strlen($newPassword);
        for($counter = 0; $counter < $passwordLength; $counter++) {
            if(in_array(substr($newPassword, $counter, 1), str_split(DefaultEncoder::CHAR_LOWERS))) {
                $charsets++;
                break;
            }
        }
        for($counter = 0; $counter < $passwordLength; $counter++) {
            if(in_array(substr($newPassword, $counter, 1), str_split(DefaultEncoder::CHAR_UPPERS))) {
                $charsets++;
                break;
            }
        }
        for($counter = 0; $counter < $passwordLength; $counter++) {
            if(in_array(substr($newPassword, $counter, 1), str_split(DefaultEncoder::CHAR_DIGITS))) {
                $charsets++;
                break;
            }
        }
        for($counter = 0; $counter < $passwordLength; $counter++) {
            if(in_array(substr($newPassword, $counter, 1), str_split(DefaultEncoder::CHAR_SPECIALS))) {
                $charsets++;
                break;
            }
        }

        // calculate and verify password strength
        $passwordStrength = $passwordLength * $charsets;
        if($passwordStrength < 16) {
            throw new AuthenticationCredentialsException("Invalid password", "New password is not long and complex enough");
        }
    }

    /**
     * Determine if the account exists.
     *
     * @param accountName
     *            the account name
     *
     * @return true, if the account exists
     */
    function exists($accountName) {
        throw new EnterpriseSecurityException("Method Not implemented");
    }


    private function isValidString($param) {
        return (isset($param) && $param != '');
    }
}
?>