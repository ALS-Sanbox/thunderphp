
<?php

define('DEBUG', true);

define('APP_NAME', 'ThunderPHP');
define('APP_DESCRIPTION', 'An Plugin Based PHP Framework, Everything here is an Plugin.');

if((empty($_SERVER['SERVER_NAME']) && strpos(PHP_SAPI, 'cgi') !== 0) || (!empty($_SERVER['SERVER_NAME']) && $_SERVER['SERVER_NAME'] == 'localhost'))
{
	/** The name of your database */
    define( 'DB_NAME', 'thunderphp_db');

    /** Database username */
    define( 'DB_USER', 'root');

    /** Database password */
    define( 'DB_PASSWORD', '');

    /** Database hostname */
    define( 'DB_HOST', 'localhost');

    /** Database driver */
    define( 'DB_DRIVER', 'mysql');

    define('ROOT', 'http://localhost/pluginphp');

}else
{
	/** The name of your database */
    define( 'DB_NAME', 'ALS_Admin_thunderphp_db');

    /** Database username */
    define( 'DB_USER', 'ALS_Admin_tp');

    /** Database password */
    define( 'DB_PASSWORD', '1:Kt=@u;r?y&w}r+');

    /** Database hostname */
    define( 'DB_HOST', 'localhost');

    /** Database driver */
    define( 'DB_DRIVER', 'mysql');
	
	define('ROOT', 'https://thunder.alssandbox.com');
}
