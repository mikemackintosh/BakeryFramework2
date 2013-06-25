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

	}

	public function render( $file, $args ){
		header('HTTP/1.1 200 OK');
		header('X-Powered-By: '.$_ENV['BAKERY_LCR']);

		echo \Bakery::$render->render( $file, $args );
	}

	public function image( $type = 'png' ){

	}

	public function pdf(){

	}

	public function error( $response ){

	}

}