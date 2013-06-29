<?php

namespace Bakery\Oven;

/**
 * 
 */
class HttpResponseManager {
	
	/**
	 * [__construct description]
	 */
	public function __construct() {
		
		// Create the Twig_Loader
		$loader = new \Twig_Loader_Filesystem(PATH.'Glaze/'.THEME);
		\Bakery::$render = new \Twig_Environment($loader, array(
		//	'cache' => PATH.'Cache',
		));

		\Bakery::$render->addGlobal("site", [ "name" => SITE_NAME, "description" => "", 'bf2_ver' => $_ENV['BAKERY_VER'], 'bf2_sn' => $_ENV['BAKERY_SN'] ]);
		\Bakery::$render->addGlobal('vanilla', [ 'stylesheet' => '/assets/style.css'] );

		//\Bakery::$render->addGlobal("vanilla", [ "stylesheet"  ]);

	}

	/**
	 * [render description]
	 * 
	 * @param  [type] $file [description]
	 * @param  [type] $args [description]
	 * @return [type]       [description]
	 */
	public function render( $file, $args ){
		header('HTTP/1.1 200 OK');
		header('X-Powered-By: '.$_ENV['BAKERY_LCR']);
		
		try{

			// Is JSON
			if(\Bakery::$request->json()){
				die( json_encode( $args ) );
			}

			die( \Bakery::$render->render( $file, $args ) );

		}
		catch(\Exception $e){
			$this->error( $e->getMessage() );
		}
	}

	/**
	 * [image description]
	 * 
	 * @param  string $type [description]
	 * @return [type]       [description]
	 */
	public function image( $type = 'png' ){

	}

	/**
	 * [pdf description]
	 * 
	 * @return [type] [description]
	 */
	public function pdf(){

	}

	/**
	 * [error description]
	 * 
	 * @param  [type]  $response [description]
	 * @param  integer $error    [description]
	 * @return [type]            [description]
	 */
	public function error( $response , $error = 500){
		echo \Bakery::$render->render( "errors/{$error}.twig", [ "error" => $response ] );
	}

}