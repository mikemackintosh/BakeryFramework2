<?php

namespace Bakery\Oven;

class HttpResponseManager {
	
	public function __construct() {
		
		//require_once PATH.'Pantry/Frosting/Twig/Autoloader.php';
		//\Twig_Autoloader::register();

		$loader = new \Twig_Loader_Filesystem(PATH.'Glaze/'.THEME);
		\Bakery::$render = new \Twig_Environment($loader, array(
		//	'cache' => PATH.'Cache',
		));

		\Bakery::$render->addGlobal("site", [ "name" => SITE_NAME, "description" => "", 'bf2_ver' => $_ENV['BAKERY_VER'], 'bf2_sn' => $_ENV['BAKERY_SN'] ]);
		\Bakery::$render->addGlobal('vanilla', [ 'stylesheet' => '/assets/style.css'] );

		//\Bakery::$render->addGlobal("vanilla", [ "stylesheet"  ]);

	}

	public function render( $file, $args ){
		header('HTTP/1.1 200 OK');
		header('X-Powered-By: '.$_ENV['BAKERY_LCR']);
		try{
			echo \Bakery::$render->render( $file, $args );
		}
		catch(\Exception $e){
			$this->error( $e->getMessage() );
		}
	}

	public function image( $type = 'png' ){

	}

	public function pdf(){

	}

	public function error( $response , $error = 500){
		echo \Bakery::$render->render( "errors/{$error}.twig", [ "error" => $response ] );
	}

}