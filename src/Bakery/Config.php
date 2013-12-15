<?php
#########################################
#
#		     Config Loader
#                      
#########################################

namespace Bakery;

/**
* Config
*
* @author Mike Mackintosh <mike@bakeryframework.com>
*
*/
class Config{

    /**
     * @param unknown_type $filename
     * @param unknown_type $data
     * @return \Bakery\Config
     */
    public static function load( $filename, $data = [] )
    {
        if(file_exists( $filename )){
            $data = parse_ini_file( $filename, true);
        }
    
        // return class instance
        return new static($data);
    }

    /**
     * @param unknown_type $data
     */
    public function __construct($data)
    {
        $this->data = $data;
    
        $this->validateOptions();
    }
    
    /**
     * @return array
     */
    public function all(){
            
        return (array) $this->data;
    }
    
    /**
     * @param unknown_type $section
     * @param unknown_type $option
     * @return boolean
     */
    public function get($section, $option)
    {
        if (!array_key_exists($section, $this->data)) {
            return false;
        }

        if (!array_key_exists($option, $this->data[$section])) {
            return false;
        }

        return $this->data[$section][$option];
    }
    
    /**
     * @param unknown_type $section
     * @return boolean|\Bakery\unknown_type
     */
    public function getSection($section)
    {
        if (!array_key_exists($section, $this->data)) {
            return false;
         }
    
        return $this->data[$section];
    }
    
    /**
     * @param unknown_type $section
     * @param unknown_type $option
     * @param unknown_type $value
     */
    public function set($section, $option, $value)
    {
        $this->data[$section][$option] = $value;
    }
    
    /**
     *
     */
    protected function validateOptions()
    {
            // Validate that debug is defined in config.ini
            if ( !$this->getSection('app') && !$this->getSection('global') ) {
                    //die("Please, edit the config file and ensure your debug status is set.");
            }
    }
}

