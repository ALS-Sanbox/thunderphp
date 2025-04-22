<?php

define('DEBUG', true);

define('APP_NAME', 'ThunderPHP');
define('APP_DESCRIPTION', 'An Plugin Based PHP Framework, Everything here is an Plugin.');

if((empty($_SERVER['SERVER_NAME']) && strpos(PHP_SAPI, 'cgi') !== 0) || (!empty($_SERVER['SERVER_NAME']) && $_SERVER['SERVER_NAME'] == 'localhost'))
{
	/** The name of your database */
    define( 'DB_NAME', 'thunder_thunderphp_db');

    /** Database username */
    define( 'DB_USER', 'thunder_tp');

    /** Database password */
    define( 'DB_PASSWORD', 'R@inbow2025');

    /** Database hostname */
    define( 'DB_HOST', 'localhost');

    /** Database driver */
    define( 'DB_DRIVER', 'mysql');
	
	define('ROOT', 'http://45-79-8-243.ip.linodeusercontent.com');

}else
{
	/** The name of your database */
    define( 'DB_NAME', 'thunder_thunderphp_db');

    /** Database username */
    define( 'DB_USER', 'thunder_tp');

    /** Database password */
    define( 'DB_PASSWORD', 'R@inbow2025');

    /** Database hostname */
    define( 'DB_HOST', 'localhost');

    /** Database driver */
    define( 'DB_DRIVER', 'mysql');
	
	define('ROOT', 'http://45-79-8-243.ip.linodeusercontent.com');
}
