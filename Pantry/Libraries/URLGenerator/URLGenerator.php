<?php

namespace Bakery\Pantry\Libraries\URLGenerator;

/**
 * 
 */
class URLGenerator{
	
	/**
	 * [__construct description]
	 */
	public function __construct(){
		//echo __METHOD__;
	}

	/**
	 * generate a url
	 * 
	 * @param  [string] $route [description]
	 * @param  [array] $args  [description]
	 * @return [string]        [description]
	 */
	public function generate( $route, $args = [] ){


		foreach( \Bakery::$cookbook as $cbroute => $recipe ){
			if( $recipe->getRouteName() == $route){

				if( !empty( $recipe->_uriPattern )){
					
					$i = 0;
					foreach( $recipe->_uriPattern as $k => $v ){

						$cbroute = str_replace("{{$k}}", $args[$i++], $cbroute);

					}

				}
				
				return $cbroute;

			}

		}

	}

}