<?php

namespace Bakery\Oven;

/**
 * 
 */
class CliCookBook {
	
	/**
	 * Private Properties
	 */	
	private $_routeName = null,
			$_title = false,
			$_version = false,
			$_help = false,
			$_description = false,
			$_perm = 0,
			$_args = [];

	/**
	 * Public Properties
	 */
	public  $_uriPattern = [],
			$func = null,
			$controller = "DefaultController";

	/**
	 * [__construct description]
	 * @param [type] $route      [description]
	 * @param [type] $controller [description]
	 * @param [type] $func       [description]
	 */
	public function __construct( $route = NULL, $controller = NULL, $func = NULL ) {

		// echo "<br />Creating route from `$controller`<br />\n";
		$this->controller = $controller ? $controller : $this->controller;

		if(!is_null($func)){
			$this->func = $func;
		}

		$this->_routeName = $route;

		\Bakery::$clicookbook[$route] = &$this;

		return $this;

	}

	/**
	 * [__call description]
	 * @param  [type] $k [description]
	 * @param  [type] $v [description]
	 * @return [type]    [description]
	 */
	public function __call($k, $v){		
		if(substr($k, 0, 3) == "get"){
			return $this->{"_".substr( strtolower($k), 3)};
		}
	}

	/**
	 * [title description]
	 * @param  [type] $title [description]
	 * @return class
	 */
	public function title( $title ){
		$this->_title = $title;
		return $this;
	}

	/**
	 * [version description]
	 * @param  [type] $version [description]
	 * @return class
	 */
	public function version( $version ){
		$this->_version = $version;
		return $this;
	}

	/**
	 * [description description]
	 * @param  [type] $_description [description]
	 * @return class
	 */
	public function description( $_description ){
		$this->_description = $_description;
		return $this;
	}

	/**
	 * [help description]
	 * @param  [type] $help [description]
	 * @return class
	 */
	public function help( $help ){
		$this->_help = $help;
		return $this;
	}

	/**
	 * [permissions description]
	 * @param  [type] $perm [description]
	 * @return class
	 */
	public function permissions( $perm ){
		$this->_perm = $perm;
		return $this;
	}

	/**
	 * [bind description]
	 * @param  [type] $name [description]
	 * @return class
	 */
	public function bind( $name ){
		$this->_routeName = $name;
		return $this;
	}

	/**
	 * [arg description]
	 * @param  [type]  $arg         [description]
	 * @param  [type]  $usage       [description]
	 * @param  boolean $required    [description]
	 * @param  string  $input_regex [description]
	 * @return class;
	 */
	public function arg( $arg, $usage, $required = FALSE, $input_regex = ".*" ){
		$this->_args[$arg] = [ "usage" => $usage, "required" => $required, "regex" => $input_regex ];
		return $this;
	}
}