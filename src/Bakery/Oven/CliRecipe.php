<?php

namespace Bakery\Oven;

/**
 * 
 */
interface CliRecipeInterface{
	
	public function help();

	public function init();

}

/**
 * 
 */
abstract class CliRecipe implements CliRecipeInterface{

	
}