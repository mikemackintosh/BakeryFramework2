<?php

/*
* This file is part of the Bakery framework.
*
* (c) Mike Mackintosh <mike@bakeryframework.com>
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

// Start Session
session_start();

// set current working directory to below web root
chdir(__DIR__);

// Define some vars
define("EXT", ".php");

// Define APP_PATH

define('APP_PATH', getcwd().'/');
define('WEB_PATH', getcwd().'/web/');


/**
 * class Bakery Def
 */
class Bakery {
    public static $cfg;
    public static $error;
    public static $render;
    public static $pdo;
    public static $cookbook = [];
    public static $clicookbook = [];
    public static $request;
    public static $response;
    public static $oven;
    public static $module;
}

// Include composer autloader
include_once 'vendor/autoload.php';
include_once 'src/Bakery/Kitchen.php';