<?php
/**
 * LDAP Configuration for OWASP Mutillidae II
 * 
 * For Docker: uses 'ldap' hostname (Docker service name)
 * For local development: change LDAP_HOST to '127.0.0.1' or 'localhost'
 */

// Use environment variable if set, otherwise use Docker default
define('LDAP_HOST', getenv('MUTILLIDAE_LDAP_HOST') ?: 'ldap');
define('LDAP_PORT', getenv('MUTILLIDAE_LDAP_PORT') ?: 389);
define('LDAP_BASE_DN', getenv('MUTILLIDAE_LDAP_BASE_DN') ?: 'dc=mutillidae,dc=localhost');
define('LDAP_BIND_DN', getenv('MUTILLIDAE_LDAP_BIND_DN') ?: 'cn=admin,dc=mutillidae,dc=localhost');
define('LDAP_BIND_PASSWORD', getenv('MUTILLIDAE_LDAP_BIND_PASSWORD') ?: 'mutillidae');
?>