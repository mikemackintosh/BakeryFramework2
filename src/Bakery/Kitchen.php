<?php
############################################################################
#
#			          Set Environment Variables
#
#			  Used for module and framework consistency
#
############################################################################

namespace Bakery;

// Load app cfg
$cfg = Config::load(TOPHAT_PATH."/configs/app.cfg");
\Bakery::$cfg = &$cfg;

/* Set Envinronment */
$env = 'prod';
$_BENV['HOSTNAME'] = (isset($_SERVER['HTTP_X_FORWARDED_HOST']) ? $_SERVER['HTTP_X_FORWARDED_HOST'] : $_SERVER['HTTP_HOST']);
$_BENV['PROTOCOL'] = ($_SERVER['SERVER_PORT'] == 443 ? "https://" : "http://");

/* Get Environment */
if(is_array($cfg->getSection('environments'))){
    foreach( $cfg->getSection('environments') as $env => $hostname){
        if(preg_match("`$hostname`", $_BENV['HOSTNAME'] )){

            $_BENV['ENV'] = $env;
            break;
        }
    }
}


/* Grab Basic FW Environment Vars */
$_BENV['BAKERY_PREMEMORY'] = memory_get_usage();
$_BENV['BAKERY_STARTTIME'] = microtime(true);
$_BENV['BAKERY_SESS_INST'] = md5($_BENV['BAKERY_STARTTIME']);
$_BENV['BAKERY_VISITTIME'] = $_SERVER['REQUEST_TIME'];

/* Create Environment */
$_BENV['BAKERY_VER']       = '2.1.0'; 						// Build Version
$_BENV['BAKERY_BUILDDATE'] = "20131213";			 	        // Build Date
$_BENV['BAKERY_SN']        = "Bagel"; 						// Logical Name - Bagel, Brownie, Cheesecake, Cookie, Cupcake, Danish, Donut, Eclair, Fruitcake, Zeppoli
$_BENV['BAKERY_CR']        = "Powered By BakeryPHP - v{$_BENV['BAKERY_VER']}";
$_BENV['BAKERY_LCR']       = "Powered By BakeryPHP - v{$_BENV['BAKERY_VER']} [{$_BENV['BAKERY_SN']}]";

/* Define Site Variables */
$_BENV['SITE_NAME'] = 'Bakery Framework 2';
$_BENV['SITE_URL']  = '//';
$_BENV['THEME']     = 'vanilla';

/* Define Environment Variables */
$_BENV['PATH']        = dirname(__FILE__).'/';
$_BENV['BAKERY_PATH'] = dirname(__FILE__)."/";

/* Generate Bakery Env Constants and Magic _ENV */
foreach( $_BENV as $var => $val ){
    $_ENV[$var] = $val;
    define($var, $val);
}

/* Define crypto key */
define('CRYPTO_KEY', $cfg->get("crypto", "key"));

/* Set Logging Mode */
define('LOGGING_MODE', $cfg->get(ENV, 'logging'));

/* Set Debug Mode */
define('DEBUG_MODE', $cfg->get(ENV, 'debug'));

/* Set Maintainer Mode and Owner */
define('MAINTENANCE_MODE', false);
${'MAINTENANCE_ADMIN'} = [ '' ];

/* Include Helper **/
include_once(PATH."Pantry/Helpers/Functions".EXT);

/* Debug Mode Settings */
if(DEBUG_MODE){
	ini_set("display_errors", true);
	error_reporting( -1 );
}

/** Listen for Maintenace Mode */
if(MAINTENANCE_MODE && $_SERVER['REMOTE_ADDR'] != MAINTENANCE_ADMIN){
	ErrorHandler::maintenance($e);
}

/* Include Recipe.php */
include_once(PATH."Recipe".EXT);

// Let's get baking
\Bakery::$oven = Oven\Bake::instance();

// Enjoi
