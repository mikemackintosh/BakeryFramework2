<?php

namespace Bakery\Exceptions;

/**
 * 
 */
class AutoloaderFileNotFound extends \Exception{
	
	public function __construct($message){
		
		parent::__construct( $message );

	}

}

/**
 * 
 */
class AutoloaderClassNotFound extends \Exception{
	
	public function __construct($message){
		
		parent::__construct( $message );

	}

}