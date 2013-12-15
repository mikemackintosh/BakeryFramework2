<?php

/**
 * 
 */
namespace Bakery\Pantry\Patterns;

/**
 * 
 */
class Singleton {

    /**
     * @var $instance Rerefence to new class instance
     */
    protected static $instance = [];

    protected function __construct() {
        return true;
    }
        
    /**
     * [instance description]
     * 
     * @return object Class Reference
     */
    public static function instance( $options = NULL ) {
        
        $class = get_called_class();
        
        if ( !array_key_exists($class, self::$instance) || self::$instance[$class] === null) {
            self::$instance[$class] = new $class( $options );
        }
        
        return self::$instance[$class];
    }

}