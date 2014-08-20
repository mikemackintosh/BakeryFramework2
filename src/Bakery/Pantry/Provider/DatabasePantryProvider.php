<?php

namespace Bakery\Pantry\Provider;

/**
 * Provider for Database Storage Engine access
 *
 * @author Mike Mackintosh <mike@bakeryphp.com>
 */
class DatabasePantryProvider extends \PDO{
	
	public function __construct($driver, $host, $database, $uname, $password, $encrypted=false){

		if($driver == 'vertica'){
			$dsn = "odbc:Driver=vertica;Server={$host};Database={$database};ReadOnly=true;";
		}
		else{
			$dsn = "{$driver}:host={$host};dbname={$database};";
		}

		if( $encrypted !== false ){
			$password = $this->decrypt_pass($password, CRYPTO_KEY);
		}

		parent::__construct($dsn, $uname, $password);
		parent::setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

		return $this;
	}

	/**
	 * @param String $query
	 * @param Array $values
	 */
	public function fetchAll( $query, $values = array() ){
		
		$stmt = $this->prepare($query);
		$stmt->execute($values);
		
		return $stmt->fetchAll(\PDO::FETCH_BOTH);
		
	}

	/**
	 * @param String $query
	 * @param Arrau $values
	 */
	public function fetchArray( $query, $values = array()  ){
	
		$stmt = $this->prepare($query);
		$stmt->execute($values);
	
		return $stmt->fetch(\PDO::FETCH_BOTH);
	
	}

	/**
	 * @param String $query
	 * @param Array $values
	 */
	public function fetchAssoc( $query, $values = array()  ){
	
		$stmt = $this->prepare($query);
		$stmt->execute($values);
	
		return $stmt->fetch(\PDO::FETCH_ASSOC);
	
	}
	
	/**
	 * @param String $table
	 * @param Array $fields
	 * @param Array $values
	 */
	public function insert( $table, $fields, $values){

		$query = "INSERT INTO $table SET ". implode(",", $fields);
		
		$stmt = $this->prepare($query);
		$this->_last = $stmt->execute($values);
				
		return $this->lastInsertId();
		
	}
	
	/**
	 * @param String $table
	 * @param Array $fields
	 * @param Array $values
	 */
	public function insertDuplicate( $table, $fields, $values, $dup_constraint){
	
		$query = "INSERT INTO $table SET ". implode(",", $fields) ." ON DUPLICATE KEY UPDATE ". $dup_constraint;
	
		$stmt = $this->prepare($query);
		$this->_last = $stmt->execute($values);
	
		return $this->lastInsertId();
	
	}
	
	/**
	 * @param String $table
	 * @param Array $fields
	 * @param Array $values
	 * @param String $constraint
	 */
	public function update( $table, $fields, $values, $constraint){

		$query = "UPDATE $table SET ". implode(",", $fields)." WHERE $constraint";
		
		$stmt = $this->prepare($query);
		$this->_last = $stmt->execute($values);
		
		return;
		
	}

	/**
	 * @param String $query
	 * @param Array $values
	 */
	public function executeQuery( $query, $values = array() ){
	
		$stmt = $this->prepare($query);
		$this->_last = $stmt->execute($values);
	
		return;
	}

	/**
	 * @param String $query
	 * @param Array $values
	 */
	public function numrows( ){
	
		return $this->_last->rowCount();
		
	}
	
	/**
	 *
	 */
	public function beginTransaction(){
	
		return $this->beginTransaction();
	
	}


	/**
	 *
	 */
	public function commit(){
	
		return $this->commit();
	
	}	

	/**
	 *
	 */
	public function rollback(){
	
		return $this->rollback();
	
	}
	
	/**
	 * @param unknown_type $query
	 * @param unknown_type $values
	 * @param unknown_type $keys
	 * @param unknown_type $many_to_one
	 * @param unknown_type $merge_classifier
	 * @param unknown_type $array
	 * @param unknown_type $return_array
	 * @return unknown
	 */
	public function format($query, $values, $keys, $many_to_one = false, $merge_classifier = NULL, $array = NULL, $return_array = array())
	{
		
		$array = $this->fetchAll( $query, $values);
	
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