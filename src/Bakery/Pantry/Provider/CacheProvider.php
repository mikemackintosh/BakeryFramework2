<?php

namespace Bakery\Pantry\Provider;

/**
 * Provider for Cache Storage Engine access
 *
 * @author Mike Mackintosh <mike@bakeryphp.com>
 */
class CacheProvider {
	

	public function __construct( $file, $path=TOPHAT_CACHE_PATH, $encrypted=false){

		//$password = $this->decrypt_pass($password, CRYPTO_KEY);
		$this->file = md5($file);
		$this->path = $path;

		$this->_file = $this->path.'/'.$this->file;

		return $this;

	}

	public function valid( $time = 3600 ){
		
		if(!file_exists($this->_file)){

			touch($this->_file);
			chmod($this->_file, 0664);

			return false;
		}
		
		if(filemtime($this->_file) < (time()-$time)){
			return false;
		}

		return true;
	}

	
	public function write( $contents ){

		file_put_contents($this->_file, serialize($contents));

	}

	
	public function fetch( ){

		return unserialize(file_get_contents($this->_file));
		
	}


	public function encrypt_pass( $string, $key, $alg=MCRYPT_CAST_256 ){
		$size = mcrypt_get_iv_size( $alg, MCRYPT_MODE_CFB );
    	$iv = mcrypt_create_iv( $size, MCRYPT_RAND );

    	$payload = mcrypt_encrypt($alg, $key,
                                 $string, MCRYPT_MODE_CBC, $iv);

    	return base64_encode(serialize([ bin2hex($payload), bin2hex($iv), $alg ]));
	}


	public function decrypt_pass( $hash, $key){
		list($payload, $iv, $alg) = unserialize(base64_decode( $hash ));

		return mcrypt_decrypt($alg, $key,
                                 hex2bin($payload), MCRYPT_MODE_CBC, hex2bin($iv));
	}


}