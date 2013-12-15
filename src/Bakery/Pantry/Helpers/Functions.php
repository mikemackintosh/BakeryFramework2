<?php


/**
 * [$isError description]
 * 
 * @var boolean
 */
//register_shutdown_function('shutdownHandler');
//set_error_handler("shutdownHandler");

function shutdownHandler() {
    if ($error = \error_get_last()){
        switch($error['type']){
            case E_ERROR:
            case E_CORE_ERROR:
            case E_COMPILE_ERROR:
            case E_USER_ERROR:
            case E_RECOVERABLE_ERROR:
            case E_CORE_WARNING:
            case E_COMPILE_WARNING:
            case E_PARSE:            
                var_dump ($error);//do whatever you need with it
                break;
        }

       // \Bakery::$logging->debug($error);

    }

};


/**
 * [redirect description]
 * @param  [type] $location [description]
 * @return [type]           [description]
 */
function redirect($location){
    header('Location: '.$location); 
}

/**
 * [splituc description]
 * @param  [type] $s [description]
 * @return [type]    [description]
 */
function splituc($s) {
 
    return preg_split('/(?=[A-Z])/', $s, -1, PREG_SPLIT_NO_EMPTY);
}

/**
 * b_filter is a helper invoker for custom defined functions
 * 
 * @param unknown_type $type
 * @param unknown_type $this
 * @return mixed
 */
function b_filter( $type , $this ){
    $filter = new \ReflectionFunction( "bfilter_$type");
    return $filter->invoke($this);

}

/**
 * filter_numeric_keys removes non-string array keys
 * 
 * @param array $array
 * @return array
 */
function bfilter_numeric_keys( array $array ){
    foreach($array as $key=>$var){
        if(is_numeric($key)){
            unset($array[$key]);
        }
    }

    return $array;  
}

/**
 * bfilter_create_slug - converts string into slug
 * 
 * @param string $slug
 * @param boolean $hyphenate
 * @return string
 */
function bfilter_create_slug($slug, $hyphenate = true){
    $slug = str_replace(array("'", '"'), "", strtolower($slug));

    if($hyphenate){
        $slug = preg_replace("/[-\s\W]/","-",$slug);
    }

    return preg_replace("/[^a-z0-9-_]/", "", $slug);
}

/**
 * [is_closure description]
 * @param  [type]  $object [description]
 * @return boolean         [description]
 */
function is_closure( $object ){
    return is_object($object) && ($object instanceof Closure);
}
