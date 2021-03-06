<?php

namespace Bakery\Recipes\BakeryFramework2;

/**
 * 
 */
class Welcome {
	
	/**
	 * [__construct description]
	 */
	public function __construct(  ) {

	}

	/**
	 * [homepage description]
	 * 
	 * @param  [type] $recipe  [description]
	 * @param  [type] $request [description]
	 * @return [type]          [description]
	 */
	public function homepage( $recipe, $request ){

		// Set Header message
		$glaze['header']  = [ "message" => "Congratulations! You've successfully installed <b>Bakery Framework</b> {$_ENV['BAKERY_VER']}." ];

		\Bakery::$response->render("home.twig", $glaze );

	}

	/**
	 * [changeLog description]
	 * 
	 * @param  integer $ver [description]
	 * @return [type]       [description]
	 */
	public function changelog( $ver = -1 ){

		preg_match('`([\d.]+)`', $ver, $match);

 		$glaze['changelog'] = $this->revisions( @$match[0] );
 		$glaze['header']  = [ 
 								"message" => "Read below to learn about the <b>feature enhancements</b> and <b>changes</b> in version {$_ENV['BAKERY_VER']}.",
 								"left" => '<a href="'.\Bakery::$module['urlgenerator']->generate("homepage").'">&laquo; Go Back</a>'
 							];

		\Bakery::$response->render("changelog.twig", $glaze );

	}

	/**
	 * [revisions description]
	 * 
	 * @param  [type] $revision [description]
	 * @return [type]           [description]
	 */
	public function revisions( $revision ){

		$changes = [
				"2.1.0" => [
					"date" => "20131213",
					"commits" => [
						
						"5f1dede" => [
										"Refactored code base in accordance with 2.1.0",
									],
						"5f1dede" => [
										"Updated changelog",
									],
					]
				],
				"2.0.8" => [
					"date" => "20131213",
					"commits" => [
						
						"292d206" => [
										"Added base accessors",
									],
						"a6c9baa" => [
										"removed legacy code"
									],
					]
				],
				"2.0.2" => [
					"date" => "20130709",
					"commits" => [
						
						"292d206" => [
										"Added Lessc library to HttpRequestManager",
									],
						"c6812ef" => [
										"Added style, less, image and asset twig functions"
									],
					]
				],
				"2.0.0" => [
					"date" => "20130625",
					"commits" => [
						
						"59de92c" => [
										"Off-loaded response and Twig rendering modules from core",
									],
						"9b77c33" => [ 
										"Removed specific SQL drivers, supports only PDO",
									],
						"cde4b9c" => [
										"Added PRISM logging module",
										"Optimized Oven engine for better performance and lower overhead",
									],
						"232665c" => [ 
										"Custom autoloader which supports PSR-1 and PSR-2 standards",
										"Added Singleton pattern for easy class extension",
									],
						"b7c88bf" => [
										"Added support for HTTP error handling and status codes",
									],
						"00a7a1c" => [
										"Added a URL Generator to easily create URL's from routes",
									],
						"7c9b75d" => [
										"Added LDAP Pantry Provider to core",
									],
						"57824e3" => [
										"Added automatic .json extension detection and a json object will be returned",
									],
					]
				]
			];

		if(array_key_exists($revision, $changes)){
			return [ $revision => $changes[$revision] ];
		}else{
			return $changes;
		}

	}

	public function git(){
		header("Location: http://bit.ly/gitgitbakeryphp");
	}

}

