<?php

define('APP_NAME', 'ThunderPHP');
define('APP_DESCRIPTION', 'An Plugin Based PHP Framework, Everything here is an Plugin.');

if((empty($_SERVER['SERVER_NAME']) && strpos(PHP_SAPI, 'cgi') !== 0) || (!empty($_SERVER['SERVER_NAME']) && $_SERVER['SERVER_NAME'] == 'localhost'))
{
	/** The name of your database */
    define( 'DB_NAME', 'your_db_name');

    /** Database username */
    define( 'DB_USER', 'your_db_user');

    /** Database password */
    define( 'DB_PASSWORD', 'your_db_password');

    /** Database hostname */
    define( 'DB_HOST', 'localhost');

    /** Database driver */
    define( 'DB_DRIVER', 'mysql');

	define('ROOT', 'http://localhost');

}else
{
	/** The name of your database */
    define( 'DB_NAME', 'your_db_name');

    /** Database username */
    define( 'DB_USER', 'your_db_user');

    /** Database password */
    define( 'DB_PASSWORD', 'your_db_password');

    /** Database hostname */
    define( 'DB_HOST', 'localhost');

    /** Database driver */
    define( 'DB_DRIVER', 'mysql');

	define('ROOT', 'https://your-domain.example.com');
}
