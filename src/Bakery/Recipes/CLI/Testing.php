<?php
namespace Bakery\Recipes\CLI;

class Testing extends \Bakery\Oven\CliRecipe {

	public function __construct(){

		if(\Bakery::$request->option("f")){
			echo \Bakery::$request->option("f");
		}
		
	}

	public function init(){
		echo __METHOD__.PHP_EOL;
	}

	public function help(){
		//print_r($this->args);
	}
}