<?php

namespace Bakery;

/**
 * 
 */
class CookBook {
	
	private $_type = "GET";
	
	private $_routeName = null;
	private $_https = false;
	private $_http = true;
	private $_perm = 0;
	public $_uriPattern = [];

	
	
	public $func = null;

	public $controller = "DefaultController";

	public function __construct( $route = NULL, $controller = NULL, $func = NULL ) {

		// echo "<br />Creating route from `$controller`<br />\n";
		$this->controller = $controller ? $controller : $this->controller;

		if(!is_null($func)){
			$this->func = $func;
		}

		$this->_routeName = $route;

		\Bakery::$cookbook[$route] = &$this;

		return $this;

	}

	public function method( $method ){
		$this->method = $method;
		return $this;
	}

	public function type( $method = "POST" ){
		$this->_type = $method;
		return $this;
	}

	public function https( $required ){
		$this->_https = $required;
		return $this;
	}

	public function http(){
		$this->_http = array('http');

		return $this;
	}

	public function permissions( $perm ){
		$this->_perm = $perm;
		return $this;
	}

	public function regex( $variable, $pattern){
		$this->_uriPattern[$variable] = $pattern;

		return $this;
	}

	public function domain( $variable ){
		$this->_domain = $variable;

		return $this;
	}

	public function subdomain( $variable){
		$this->_subdomain = $variable;

		return $this;
	}

	public function bind( $name ){
		$this->_routeName = $name;
	}



}