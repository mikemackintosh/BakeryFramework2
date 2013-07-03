<?php

// Create UrlGenerator
\Bakery::$module['urlgenerator'] = new \Bakery\Pantry\Libraries\URLGenerator\URLGenerator();


// Create recipe for homepage
(new \Bakery\CookBook("/", "\Bakery\Recipes\BakeryFramework2\Welcome", "homepage"))
	->type("GET")
	->bind('homepage');


// Create recipe for changelog
(new \Bakery\CookBook("/changelog{ver}", "\Bakery\Recipes\BakeryFramework2\Welcome", "changelog"))
	->type("GET")
	->regex('ver', '.*')
	->bind('changelog');


/********************************
 *
 *    
 * 
 *******************************/
/*
(new \Bakery\CliCookBook("changelog", "\Bakery\Recipes\CLI\Testing"))
	->title("Example Application")
	->version("20130621")
	->description("This is an example function")
	->arg("f", "Filename", true, ".*\.php")
	->arg("c", "Clear CLI")
	->bind("Name");

//*/