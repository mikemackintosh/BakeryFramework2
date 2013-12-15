<?php

/*
* This file is part of the Bakery framework.
*
* (c) Mike Mackintosh <mike@bakeryframework.com>
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace Bakery\Pantry\Provider;

/**
* @author Mike Mackintosh <mike@bakeryframework.com>
*
*/
class ArrayAccessProvider {
        
        /* (non-PHPdoc)
         * @see ArrayAccess::offsetExists()
         */
        public function offsetSet($offset, $value) {
                if (is_null($offset)) {
                        $this->param[] = $value;
                } else {
                        $this->param[$offset] = $value;
                }
        }
        
        /* (non-PHPdoc)
         * @see ArrayAccess::offsetExists()
         */
        public function offsetExists($offset) {
                return isset($this->param[$offset]);
        }
        
        /* (non-PHPdoc)
         * @see ArrayAccess::offsetExists()
         */
        public function offsetUnset($offset) {
                unset($this->param[$offset]);
        }
        
        /* (non-PHPdoc)
         * @see ArrayAccess::offsetExists()
         */
        public function offsetGet($offset) {
                if( isset($this->param[$offset]) ){
                        return $this->param[$offset];
                }
                else if( isset($this->param['shared']->$offset)){
                        return $this->param['shared']->$offset;
                }
                else{
                        return null;
                }
        }
        
        /**
         * @param unknown_type $values
         * @param unknown_type $keys
         * @param unknown_type $many_to_one
         * @param unknown_type $merge_classifier
         * @param unknown_type $array
         * @param unknown_type $return_array
         * @return unknown
         */
        public function rekey_array($array, $keys, $many_to_one = false, $merge_classifier = NULL, $array = NULL, $return_array = array())
        {
                
                $array = (array) $array;
                
                if(is_string($keys)){
                        $tmp = $keys;
                        $keys = array();
                        $keys[] = $tmp;
                }
        
                $key_size = sizeof(array_intersect_key($array[0], array_flip($keys)));
                $rows = sizeof($array);
        
                foreach($array as $result) {
                        $object = &$return_array;
        
                        $i = 0;
                        foreach($keys as $index){
                                $i++;
                                if(!array_key_exists($result[$index], $object)) {
                                        $object[$result[$index]] = array();
                                }
        
                                $object = &$object[$result[$index]];
                        }
        
                        if(!is_null($merge_classifier)){
                                $object = array_merge($result, array($merge_classifier => ($result[$merge_classifier] + $object[$merge_classifier])));
                        }
                        else{
                                if( $i == $key_size && !$many_to_one){
                                        $object = $result;
                                }
                                else{
                                        $object[] = $result;
                                }
                        }
                }
        
                return $return_array;
        }
                
}

