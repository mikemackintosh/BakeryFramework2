<?php
############################################################################
#
#			          	Bakery PHP Core Oven Utility
#
#			  	Applies Runtime Logic and Baking For Your App
#
############################################################################

/**
 * @author <name> <[email]>
 */

namespace Bakery\Oven;

use \Bakery\Oven\CliRequestManager as CliRequestManager;
use \Bakery\Oven\HttpRequestManager as HttpRequestManager;
/**
 * extends Singleton
 */
class Bake extends \Bakery\Pantry\Patterns\Singleton {

	public function __construct(){

		//print_r( $_SERVER['REQUEST_URI'] );
		
		if(php_sapi_name() == "cli"){
			\Bakery::$request = new CliRequestManager( );
		}
		else{
			\Bakery::$request = new HttpRequestManager( );			
		}

		\Bakery::$request->bake();

	}

}

