<?php

namespace Bakery\Oven;

class HttpRequestManager {
	
	private $r = [];

	public function __construct( ) {

		$this->r['uri'] = urldecode($_SERVER['REQUEST_URI']);
		$this->r['uri_parts'] = explode("/", $this->r['uri']);

		array_shift($this->r['uri_parts']);

		$this->r['method'] = $_SERVER['REQUEST_METHOD'];
		$this->r['host'] = $_SERVER['HTTP_HOST'];
		$this->r['content_length'] = $_SERVER['CONTENT_LENGTH'];
		$this->r['content_type'] = $_SERVER['CONTENT_TYPE'];

		$this->r['https'] = ($_SERVER['HTTPS'] ? true : false);
		

		$this->r['json'] = (strstr($_SERVER['REQUEST_URI'], ".json") ? true : false);

		$this->r['server_addr'] = $_SERVER['SERVER_ADDR'];
		$this->r['server_port'] = $_SERVER['SERVER_PORT'];

		$this->r['requestor_addr'] = $_SERVER['REMOTE_ADDR'];
		$this->r['requestor_port'] = $_SERVER['REMOTE_PORT'];

		\Bakery::$response = new HttpResponseManager();

	}

	private function bake(){

		try{

			// Handles iterations for 404 count
			$_attemptedRoutes = 0;

			// loop through recorded routes
			foreach( \Bakery::$cookbook as $route => $recipe ){

				// Increment
				$_attemptedRoutes++;

				// If last character is no trailing slash, 
				// add it and make it optional
				if(substr($route, -1) != "/"){
					$route .= "/?";
				}

				if(!empty( $recipe->_uriPattern )){
					foreach($recipe->_uriPattern as $var => $pattern){
						$route = str_replace("{".$var."}", "(?P<{$var}>$pattern)", $route);
					}
				}

				/**
				 * 
				 */
				if( preg_match("`^{$route}$`", \Bakery::$request, $matches )){

					$args = [];
					//$args = [ "recipe" => $recipe, "request" => $this ];

					if(sizeof($matches) > 1 ){
						array_shift($matches);
						$matches = b_filter("numeric_keys", $matches);
					}
					
					if(!is_null($recipe->func)){
						$rc = (new \ReflectionMethod($recipe->controller, $recipe->func));

						foreach( $rc->getParameters() as $param ){
							////echo $param->getName();
							if( strtolower($param->getName()) == "recipe" ){
								$args = $args+["recipe" => $recipe];
							}
							else if( strtolower($param->getName()) == "request" ){
								$args = $args+["request" => $this];
							}
							elseif(array_key_exists($param->getName(), $matches)){
								$args = $args+[ $param->getName() => $matches[$param->getName()] ];
							}
							
						}

						$rc = (new \ReflectionMethod($recipe->controller, $recipe->func))->invokeArgs(new $recipe->controller, $args );

					}
					else{
						// If the route leads to a annoymous function, create a reflection
						if( is_closure($recipe->controller) ){
							$rc = (new \ReflectionFunction($recipe->controller));
							$rc->invokeArgs( $this->mixVars( $rc, $recipe, $args, $matches ));
						}
						// Else, 
						else{

							$rc = (new \ReflectionClass($recipe->controller));
							$rc->newInstanceArgs( [ "recipe" => $recipe, "request" => $this ]);
						}
					}

					return;
				}

			}

			if($_attemptedRoutes == sizeof(\Bakery::$cookbook)){
				throw new \Exception("Error404");
			}

		}
		catch(\Exception $e){
			//echo $e->getMessage();
		}
		

	}

	public function __call($k, $v){

		if( empty( $v ) ){

			if(method_exists($this, $k)){
				return $this->{$k}($v);
			}

			return $this->r[$k];
		}

	}

	public function __toString(){
		return $this->r['uri'];
	}

	public function mixVars( $rc, $recipe, $args, $matches ){

		foreach( $rc->getParameters() as $param ){
			//echo $param->getName();
			if( strtolower($param->getName()) == "recipe" ){
				$args = $args+["recipe" => $recipe];
			}
			else if( strtolower($param->getName()) == "request" ){
				$args = $args+["request" => $this];
			}
			elseif(array_key_exists($param->getName(), $matches)){
				$args = $args+[ $param->getName() => $matches[$param->getName()] ];
			}
			
		}

		return $args;
	}

	public function get($var){
		return $_GET[$var];
	}

	public function post($var){
		return $_POST[$var];
	}

}