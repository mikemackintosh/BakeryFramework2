<?php
namespace Bakery\Pantry\Provider;

/**
 * Provider for Database Storage Engine access
 *
 * @author Mike Mackintosh <mike@bakeryphp.com>
 */
class CryptoAccessProvider{
    
    public static function encrypt( $string, $key, $alg=MCRYPT_CAST_256 ){
        $size = mcrypt_get_iv_size( $alg, MCRYPT_MODE_CFB );
        $iv = mcrypt_create_iv( $size, MCRYPT_RAND );

        $payload = mcrypt_encrypt($alg, $key,
                                 $string, MCRYPT_MODE_CBC, $iv);

        return base64_encode(serialize([ bin2hex($payload), bin2hex($iv), $alg ]));
    }

    public static function decrypt( $hash, $key){

        list($payload, $iv, $alg) = unserialize(base64_decode( $hash ));
         
        return mcrypt_decrypt($alg, $key,
                                 hex2bin($payload), MCRYPT_MODE_CBC, hex2bin($iv));
    }
}