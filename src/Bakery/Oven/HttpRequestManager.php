<?php

namespace Bakery\Oven;

/**
 * 
 */
class HttpRequestManager {
	
	private $r = [];

	/**
	 * [__construct description]
	 */
	public function __construct( ) {

		//
		@list($this->r['uri'], $args) = explode("?", urldecode($_SERVER['REQUEST_URI']));
		$this->r['uri'] = explode("?", urldecode($_SERVER['REQUEST_URI']))[0];
		$this->r['uri_parts'] = explode("/", $this->r['uri']);
		
		@parse_str($args, $this->r['args']);

		// 
		$this->r['method'] = $_SERVER['REQUEST_METHOD'];
		$this->r['host'] = $_SERVER['HTTP_HOST'];

		if(array_key_exists("CONTENT_LENGTH", $_SERVER)){
			$this->r['content_length'] = $_SERVER['CONTENT_LENGTH'];
		}

		if(array_key_exists("CONTENT_TYPE", $_SERVER)){
			$this->r['content_type'] = $_SERVER['CONTENT_TYPE'];
		}

		if(array_key_exists("https", $_SERVER)){
			$this->r['https'] = ($_SERVER['HTTPS'] ? true : false);
		}

		// Detect JSON
		$this->r['json'] = (strstr($_SERVER['REQUEST_URI'], ".json") ? true : false);

		// Detect YAML
		$this->r['json'] = ((strstr($_SERVER['REQUEST_URI'], ".yaml") 
							|| strstr($_SERVER['REQUEST_URI'], ".yml")) ? true : false);

		// Detect Stylesheet
		$this->r['stylesheet'] = (strstr($_SERVER['REQUEST_URI'], ".css") ? true : false);

		// Detect LESS
		$this->r['less'] = (strstr($_SERVER['REQUEST_URI'], ".less") ? true : false);

		// Detect Image
		$this->r['image'] = ((stristr($_SERVER['REQUEST_URI'], ".png") 
							|| stristr($_SERVER['REQUEST_URI'], ".jpg")
							|| stristr($_SERVER['REQUEST_URI'], ".jpeg") 
							|| stristr($_SERVER['REQUEST_URI'], ".svg") 
							|| stristr($_SERVER['REQUEST_URI'], ".gif")
							) ? true : false);
		// Detect Javascript
		$this->r['javascript'] = ((stristr($_SERVER['REQUEST_URI'], ".js") 
							) ? true : false);

		// Server Details
		$this->r['server_addr'] = (isset($_SERVER['SERVER_ADDR']) ? $_SERVER['SERVER_ADDR'] : $_SERVER['SERVER_NAME']) ;		
		$this->r['server_port'] = $_SERVER['SERVER_PORT'];

		// Remote details
		$this->r['requestor_addr'] = $_SERVER['REMOTE_ADDR'];
		$this->r['requestor_port'] = $_SERVER['REMOTE_PORT'];

		// 
		\Bakery::$response = new HttpResponseManager();

	}

	/**
	 * [bake description]
	 * 
	 * @return [type] [description]
	 */
	private function bake(){

		try{

			// Handle Stylesheets and Images
			if($this->r['stylesheet'] || $this->r['image'] || $this->r['javascript']){
				
				$asset = PATH."Glaze/".THEME.$this->r['uri'];

				if( file_exists( $asset )) {
					
					if($this->r['stylesheet']){
						
						header("Content-type: text/css", true);

					}
					elseif( $this->r['javascript'] ){
						header("Content-type: application/javascript");
					}
					else{
						header("Content-type: ".image_type_to_mime_type( exif_imagetype( $asset ) ), true);
					}

					echo file_get_contents( $asset );
					
					die();

				}

			}
			// Autoconvert LESS
			else if($this->r['less']){

				header("Content-type: text/css", true);

				$less = new \lessc();

				die( $less->compileFile( str_replace("//", "/", PATH."Glaze/".THEME.$this->r['uri'] )) );
				
			}

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
					//
					// Extract args out of the uri if regex matching has been done
					if(is_array($matches)){
						foreach($matches as $arg => $val){
							$this->r['args'][$arg] = $val;
						}
					}

					if(sizeof($matches) > 1 ){
						array_shift($matches);
						$matches = \b_filter("numeric_keys", $matches);
					}

					if(!preg_match("`({$recipe->_method})`i", $this->r['method'])){
						// Does not matches method constraint
						\Bakery::$response->error( "Invalid Request", 405 );

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
				throw new \Exception( $this->r['uri'] );
			}

		}
		catch(\Exception $e){
			\Bakery::$response->error( $e->getMessage(), 404 );
		}
		

	}

	/**
	 * [__call description]
	 * 
	 * @param  [type] $k [description]
	 * @param  [type] $v [description]
	 * @return [type]    [description]
	 */
	public function __call($k, $v){

		if( empty( $v ) ){

			if(method_exists($this, $k)){
				return $this->{$k}($v);
			}

			return $this->r[$k];
		}

	}

	/**
	 * [__toString description]
	 * 
	 * @return string [description]
	 */
	public function __toString(){
		return $this->r['uri'];
	}

	/**
	 * [mixVars description]
	 * 
	 * @param  [type] $rc      [description]
	 * @param  [type] $recipe  [description]
	 * @param  [type] $args    [description]
	 * @param  [type] $matches [description]
	 * 
	 * @return [type]          [description]
	 */
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

	/**
	 * [get description]
	 * 
	 * @param  [type] $var [description]
	 * 
	 * @return [type]      [description]
	 */
	public function get($var){
		return (array_key_exists($var, $this->r['args']) ? $this->r['args'][$var] : $_GET[$var]);
	}

	/**
	 * [post description]
	 * 
	 * @param  [type] $var [description]
	 * 
	 * @return [type]      [description]
	 */
	public function post($var){
		return (array_key_exists($var, $this->r['args']) ? $this->r['args'][$var] : $_POST[$var]);
	}

	/**
	 * Uses HTTP method to get args
	 * 
	 * @param  [type] $var [description]
	 * 
	 * @return [type]      [description]
	 * 
	 */
	public function arg($var){
		return (isset($this->r['args'][$var]) ? $this->r['args'][$var] : $this->{$recipe->_method}($var));
	}

}