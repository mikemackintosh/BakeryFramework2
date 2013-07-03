<?php

namespace Bakery\Oven;

/**
 * 
 */
class CliRequestManager {
	
	private $r = [];
	private $options = [];

	/**
	 * [__construct description]
	 */
	public function __construct(  ) {
		
		try{

			$this->r['filename'] = $_SERVER['SCRIPT_FILENAME'];

			if( array_key_exists(1, $_SERVER['argv']) ){
				$this->r['cmd'] = @$_SERVER['argv'][1];
			}
			else{
				$this->r['cmd'] = "";
				throw new CliRequestManagerMissingTarget();
			}
			
			$this->r['arguments'] = $_SERVER['argv'];

			$this->r['shell'] = $_SERVER['SHELL'];
			$this->r['user'] = $_SERVER['USER'];
			$this->r['pwd'] = $_SERVER['PWD'];

			if($_SERVER['argc'] > 1){

				// remove filename
				array_shift($this->r['arguments']);

				// Parse arguments
				self::parse_arguments();
			}

			\Bakery::$response = new CliResponseManager();

		}
		catch( \Exception $e ){
			echo $e->getMessage();
		}

	}

	public function __destruct(){
		echo "\n\n".$_ENV['BAKERY_LCR']."\n";
	}

	/**
	 * [bake description]
	 * @return [type] [description]
	 */
	public function bake(){
		try{
			foreach( \Bakery::$clicookbook as $cmd => $opts ){

				if( $cmd == $this->r['cmd'] ){

					if($opts->getTitle() !== false){
						echo "[ {$opts->getTitle()} ]".PHP_EOL;
					}

					if($opts->getVersion() !== false){
						echo "[ Version: {$opts->getVersion()} ]".PHP_EOL;
					}

					if( ( $this->option("?") && $opts->getDescription() ) || $this->option('h') ){
					
						if( $opts->getDescription() ){
							echo "- Description: ".$opts->getDescription() . PHP_EOL.PHP_EOL;
						}

						$opts->func = "help";

					}

					foreach($opts->getArgs() as $a => $b ){
						if( $b['required'] && !array_key_exists($a, $this->options)){
							throw new CliRequestManagerMissingRequiredArgException( $a, $b );
						}
						else if($b['regex'] !== NULL && array_key_exists($a, $this->options) && !preg_match("`{$b['regex']}`i", $this->options[$a])){
							throw new CliRequestManagerInvalidArgException( $a, $b );
						}
					}

					$rc = new \ReflectionMethod($opts->controller."::". ($opts->func ? $opts->func : "init"));
					$rc->invokeArgs(new $opts->controller, array( $this->options ));
				}

			}
		}
		catch(\Exception $e){
			echo PHP_EOL."!! " .$e->getMessage();
		}

	}

	/**
	 * [parse_arguments description]
	 * @return [type] [description]
	 */
	private function parse_arguments(){

		$single_opt = false;

		foreach($this->r['arguments'] as $arg) {
			
			// Something mutable to work with
			$opts = null;

			/*
			echo "Evaluating $arg\n";
			//*/
			
			if( strpos($arg, "-") === false ){
				// echo "\tStandard Option: $arg\n";

				if($single_opt){
					$this->options[$single_opt] = $arg;
					$single_opt = false;
					continue;
				}

				if(strstr($arg, "=")){
					$opts = explode("=", $arg);
					$this->options[$opts[0]] = $opts[1];	
				}
				else{
					$this->options[$arg] = true;
				}

			}
			else if( strpos($arg, "--") === 0 ){
				// echo "\tDouble Dash Option: $arg\n";

				$arg = str_replace("--", "", $arg);
				
				if(strstr($arg, "=")){
					$opts = explode("=", $arg);
					$this->options[$opts[0]] = $opts[1];	
				}
				else{
					$this->options[str_replace("--", "", $arg)] = true;
				}

			}
			else{
				// echo "\tSingle Dash Option: $arg\n";

				$opts = str_split($arg);
				array_shift($opts);

				foreach( $opts as $opt ){

					$this->options[$opt] = true;

				}

				$single_opt = $opt;

			}

		}

	}

	public function option( $opt ){
		if(array_key_exists($opt, $this->options)){
			return $this->options[$opt];
		}
	}

}

class CliRequestManagerMissingTarget extends \Exception{

	public function __construct(){
		parent::__construct("A missing target has been detected.");
	}

}

class CliRequestManagerInvalidArgException extends \Exception{

	public function __construct( $arg, $details ){
		parent::__construct("An invalid argument ($arg) has been passed. This argument should match the format of `{$details['regex']}`.");
	}

}

class CliRequestManagerMissingRequiredArgException extends \Exception{

	public function __construct( $arg, $details ){
		parent::__construct("A required argument ($arg) is missing.");
	}

}