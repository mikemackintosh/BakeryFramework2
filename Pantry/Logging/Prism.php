<?php

namespace Bakery\Pantry\Logging;

class Prism extends \Bakery\Pantry\Patterns\Singleton{

	const ALL = 9;
	const ERROR = 7;
	const WARNING = 5;
	const DEBUG = 3;
	const INFO = 2;
	const OK = 1;

	private $opts = ["level" => 0, "destination" => PATH, "logname" => "bakery", "split_by_day" => true, "db" => false];
	private $log;

	/**
	 * [__construct description]
	 * 
	 * @param [type] $options [description]
	 */
	public function __construct( $options ) {
		
		if( !LOGGING_MODE ){
			return;
		}
		
		// 
		$this->options = array_merge( $this->opts, $options );

		try{

			/**
			 * If split_by_day is enabled, we will rollover logs once a day.
			 * The date will be in the filename
			 */
			if( $this->options['split_by_day'] ) {
				$this->options['logname'] = $this->options['logname']."_".date("Ymd");
			}

			// Create log file name
			$this->log = "{$this->options['destination']}/{$this->options['logname']}.log";

			/**
			 * Check if the log file exists
			 */
			if( !file_exists( $this->log ) ){

				if(@!touch($this->log)){
					throw new PrismNotCreatedException("`{$this->log}`");
				}

				if(@!chmod($this->log, 0766)){
					throw new PrismNotCreatedException("`{$this->log}`");
				}

				
			}

		}
		catch( \Exception $e ){
			echo $e->getMessage();
		}

	}

	/**
	 * [__call description]
	 * 
	 * @param  [type] $k [description]
	 * @param  [type] $v [description]
	 * 
	 * @return [type]    [description]
	 */
	public function __call( $k, $v ){

		if( !LOGGING_MODE ){
			return;
		}
		
		switch( $k ){
			
			case $k == "warn":
				$level = self::WARNING;
				break;
			
			case $k == "error":
				$level = self::ERROR;
				break;
			
			case $k == "debug":
				$level = self::DEBUG;
				break;

			case $k == "info":
				$level = self::INFO;
				break;

			case $k == "ok":
				$level = self::OK;
				break;

			case $k == "all":
				$level = self::ALL;
				break;

			default:
				return;
				break;
		}

		// If the allowed value is greater than the passed value
		// continue. Less than verbosity
		if($this->options['level'] >= $level) {

			// print_r( debug_backtrace());

			// Where is our storage engine?
			if($this->options['db']){


			}
			else{

				// Create date string
				$mesg = (!$this->options['split_by_day'] ? date("M d, y H:i:s")." [". date("YmdHis") ."] ## " : date("H:i:s")." [". date("His") ."] ## ");
				
				// break out verbosity
				file_put_contents( $this->log , $mesg . strtoupper($k) ." ## inst: [ ".$_ENV['BAKERY_SESSION_INSTNC']." ] ## ". implode(" ", $v) . PHP_EOL, FILE_APPEND);
			
			}

		}

	}

}

class PrismNotCreatedException extends \Exception {

	public function __construct( $log ){
		parent::__construct( "Prism Log: {$log} could not be created.");
	}

}

class PrismNotWriteableException extends \Exception {

	public function __construct( $log ){
		parent::__construct( "Prism Log: {$log} is not writeable.");
	}

}