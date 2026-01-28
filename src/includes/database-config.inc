<?php
/**
 * Database Configuration for OWASP Mutillidae II
 * 
 * For Docker: uses 'database' hostname (Docker service name)
 * For local development: change DB_HOST to '127.0.0.1' or 'localhost'
 */

// Use environment variable if set, otherwise use Docker default
define('DB_HOST', getenv('MUTILLIDAE_DB_HOST') ?: 'database');
define('DB_USERNAME', getenv('MUTILLIDAE_DB_USERNAME') ?: 'root');
define('DB_PASSWORD', getenv('MUTILLIDAE_DB_PASSWORD') ?: 'mutillidae');
define('DB_NAME', getenv('MUTILLIDAE_DB_NAME') ?: 'mutillidae');
define('DB_PORT', getenv('MUTILLIDAE_DB_PORT') ?: 3306);
?>
