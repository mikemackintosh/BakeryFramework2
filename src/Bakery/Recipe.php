<?php

namespace Bakery;

#use Bakery\Pantry\Libraries\Auth\Auth as Auth;
use Bakery\Oven\CookBook as CookBook;

// Create UrlGenerator
\Bakery::$module['urlgenerator'] = new \Bakery\Pantry\Libraries\URLGenerator\URLGenerator();

// Create reference to auth module
#\Bakery::$module['auth'] = new Auth();

// Create recipe for homepage
(new CookBook("/", '\Bakery\Recipes\BakeryFramework2\Welcome', "homepage"))
	->method("GET")
	->bind('home');

(new CookBook("/changelog", '\Bakery\Recipes\BakeryFramework2\Welcome', "changelog"))
    ->method("GET")
    ->bind('changelog');


(new CookBook("/git", '\Bakery\Recipes\BakeryFramework2\Welcome', "git"))
    ->method("GET")
    ->bind('git');

