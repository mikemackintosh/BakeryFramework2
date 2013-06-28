<?php
/*
 * This file is part of the Bakery Framework 2.
*
* (c) Mike Mackintosh <mike@bakeryframework.com>
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

/**
 * Configure PRISM
 */
require_once(PATH."/Pantry/Logging/Prism.php");
$prism['options'] = [ 'level' => \Bakery\Pantry\Logging\Prism::ALL, 'destination' => PATH."/Logs" ];
\Bakery::$logging = \Bakery\Pantry\Logging\Prism::instance( $prism['options'] );


/** Listen for Maintenace Mode */
if(MAINTENANCE_MODE && $_SERVER['REMOTE_ADDR'] != MAINTENANCE_ADMIN){
	ErrorHandler::maintenance($e);
}

/**
 * class Bakery{
 */
class Bakery {
	public static $logging;
	public static $render;
	public static $pdo;
	public static $cookbook = [];
	public static $clicookbook = [];
	public static $request;
	public static $response;
	public static $oven;
}


/**
 * Custom Framework Autoloader - nothing too special
 * 
 * @param  [type] $autoload [description]
 * @return [type]           [description]
 */
function __autoload($autoload){	
	
	// I feel insecure about the use of global, but I feel 
	// this is the only place where it seems to fit
	global $autoloader;

	/*
	echo "Trying to load: $autoload in ". __FILE__.":".__LINE__."<br />".PHP_EOL;
	//*/
	
	try{
		///* Check For Namespace
		if( strstr($autoload, '\\') ) {

			// Namespace string and class name
			$namespace = str_replace("Bakery/", "", str_replace("\\", "/", substr($autoload, 0, strrpos($autoload, '\\'))));
			$class = substr($autoload, strrpos($autoload, '\\')+1);

			// Do we have a static match for the namespace
			// 
			if( array_key_exists( $autoload, $autoloader ) ) {

				foreach( $autoloader[$autoload] as $classfile ) {

					if( file_exists( PATH ."/". $classfile . EXT ) ) {
						
						////echo $classfile."<br />\n";
						require_once(PATH ."/". $classfile . EXT);

						if(class_exists($autoload)){
						//	//echo "Exists<br />\n";
							return;
						}
					}
				}
			}			
			else if( array_key_exists( $namespace, $autoloader ) ) {

				foreach( $autoloader[$namespace] as $classfile ) {
					if( file_exists( PATH ."/". $classfile . EXT ) ) {
						
						////echo $classfile."<br />\n";
						require_once(PATH ."/". $classfile . EXT);

						if(class_exists($autoload)){
						//	//echo "Exists<br />\n";
							return;
						}
					}
				}
			}

			//echo "<br /><b>$autoload<br /><br /></b>";

			///*
			// If this is the Exceptions Namespace, Treat Specially
			if(strstr($namespace, "Exceptions")){
				
				if(file_exists(PATH ."/Pantry/Exceptions/". splituc($class)[0] . EXT))
					require_once(PATH ."/Pantry/Exceptions/". splituc($class)[0] . EXT);
				else if(file_exists(PATH ."/Pantry/Exceptions/". splituc($class)[0].splituc($class)[1]  . EXT))
					require_once(PATH ."/Pantry/Exceptions/". splituc($class)[0].splituc($class)[1]  . EXT);
				else if(file_exists(PATH ."/Pantry/Exceptions/". splituc($class)[0] .splituc($class)[1] .splituc($class)[2] . EXT))
					require_once(PATH ."/Pantry/Exceptions/". splituc($class)[0].splituc($class)[1].splituc($class)[2] . EXT);

				return;
			}
			
			//*/		
			// Found a class tree under the namespace
			if( file_exists( PATH ."/". $namespace ."/". $class ."/". $class . EXT ) ) {
				
				require_once(PATH ."/". $namespace ."/". $class ."/". $class . EXT);
				if(!class_exists($autoload))
					throw new \Bakery\Exceptions\AutoloaderFileNotFound("Error:");
				else
					return;
			}
			// Found the file directly under the namespace
			else if( file_exists( PATH ."/". $namespace ."/". $class . EXT ) ) {
				
				require_once(PATH ."/". $namespace ."/". $class . EXT);
				if(!class_exists($autoload)) 
					throw new \Bakery\Exceptions\AutoloaderFileNotFound("Error:");
				else
					return;
			}
			// Could not find the file
			else{
				
				throw new \Bakery\Exceptions\AutoloaderFileNotFound("AutoLoad Error: Unable to autoload `{$autoload}`");

			}

		}
		else{

			if( 0 === strpos($autoload, "Twig_")){
				if( is_file($file = PATH.'Pantry/Frosting/'.str_replace(array('_', "\0"), array('/', ''), $autoload).'.php') ){
					require_once $file;
				}

			}
			// Not Namespaced - Should not need
			//echo "Trying to load $autoload - Set Method Here: ". __FILE__ .":" . __LINE__."\n";

		}
		//*/
	}
	catch(\Exception $e){
		//ErrorHandler
		echo $e->getMessage();
	}
	finally{
		// Just because it's included in PHP5.5.0
	}

}

/**
 * [redirect description]
 * @param  [type] $location [description]
 * @return [type]           [description]
 */
function redirect($location){
	header('Location: '.$location);	
}

/**
 * [splituc description]
 * @param  [type] $s [description]
 * @return [type]    [description]
 */
function splituc($s) {
 
	return preg_split('/(?=[A-Z])/', $s, -1, PREG_SPLIT_NO_EMPTY);
}

/**
 * b_filter is a helper invoker for custom defined functions
 * 
 * @param unknown_type $type
 * @param unknown_type $this
 * @return mixed
 */
function b_filter( $type , $this ){
	$filter = new \ReflectionFunction( "bfilter_$type");
	return $filter->invoke($this);

}

/**
 * filter_numeric_keys removes non-string array keys
 * 
 * @param array $array
 * @return array
 */
function bfilter_numeric_keys( array $array ){
	foreach($array as $key=>$var){
		if(is_numeric($key)){
			unset($array[$key]);
		}
	}

	return $array;	
}

/**
 * bfilter_create_slug - converts string into slug
 * 
 * @param string $slug
 * @param boolean $hyphenate
 * @return string
 */
function bfilter_create_slug($slug, $hyphenate = true){
	$slug = str_replace(array("'", '"'), "", strtolower($slug));

	if($hyphenate){
		$slug = preg_replace("/[-\s\W]/","-",$slug);
	}

	return preg_replace("/[^a-z0-9-_]/", "", $slug);
}

/**
 * [is_closure description]
 * @param  [type]  $object [description]
 * @return boolean         [description]
 */
function is_closure( $object ){
    return is_object($object) && ($object instanceof Closure);
}