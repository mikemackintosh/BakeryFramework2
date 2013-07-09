<?php
############################################################################
#
#			          Set Environment Variables
#
#			  Used for module and framework consistency
#
############################################################################

session_start();
date_default_timezone_set("UTC");

/* Grab Basic FW Environment Vars */
$_ENV['BAKERY_PREMEMORY'] = memory_get_usage();
$_ENV['BAKERY_STARTTIME'] = microtime(true);
$_ENV['BAKERY_SESSION_INSTNC'] = md5($_ENV['BAKERY_STARTTIME']);
$_ENV['BAKERY_VISITTIME'] = $_SERVER['REQUEST_TIME'];

/* Create Environment */
$_ENV['BAKERY_VER'] = '2.0.1'; 						// Build Version 
$_ENV['BAKERY_BUILDDATE'] = "20130709";			 	// Build Date
$_ENV['BAKERY_SN'] = "Bagel"; 						// Logical Name - Bagel, Brownie, Cheesecake, Cookie, Cupcake, Danish, Donut, Eclair, Fruitcake, Zeppoli
$_ENV['BAKERY_CR'] = "Powered By BakeryPHP - v{$_ENV['BAKERY_VER']}";
$_ENV['BAKERY_LCR'] = "Powered By BakeryPHP - v{$_ENV['BAKERY_VER']} [{$_ENV['BAKERY_SN']}]";

/* Define Site Variables */
define('SITE_NAME', 'Bakery Framework 2');
define('SITE_URL', '//');
define('THEME', 'vanilla');

/* Define Environment Variables */
define('PATH', dirname(__FILE__).'/');
define('BAKERY_PATH', dirname(__FILE__)."/Bakery/");

/* Set Logging Mode */
define('LOGGING_MODE', true);

/* Set Debug Mode */
define('DEBUG_MODE', true);

/* Set Maintainer Mode and Owner */
define('MAINTENANCE_MODE', false);
${'MAINTENANCE_ADMIN'} = [ '' ];

/* Set Default Application */
//define('DEFAULT_APP', 'Welcome');

/* Set Default Extension */
define('EXT', '.php');

/**
 * Debug Mode Settings
 */

if(DEBUG_MODE){
	
	ini_set("display_errors", true);
	error_reporting( -1 );

}
else{
	ini_set("display_errors", false);
	error_reporting( 0 );
}

/**
 * Basic autoloader
 */

$autoloader = [
				"Bakery\Oven\Bake" => [ "Oven/Bake" ],
				"Bakery\CookBook" => [ "Oven/CookBook" ],
				"Bakery\CliCookBook" => [ "Oven/CliCookBook" ],
				"Bakery\Exceptions" => [ "Pantry/Exceptions" ],
			  ]; 


/**
 * Load Helpers
 */

include(PATH."/Oven/Helpers".EXT);

/**
 *  Include Recipe.php
 */

include(PATH."Recipe".EXT);

///*
// Let's get baking

\Bakery::$logging->debug("Initial Script Access", "--", "Version:", $_ENV['BAKERY_VER'], "--", "Build Date:", $_ENV['BAKERY_BUILDDATE']);
\Bakery::$oven = \Bakery\Oven\Bake::instance();

// Enjoy