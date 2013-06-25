<?php

namespace Bakery\Oven;

class CliRequestManager {
	
	private $r = [];

	public function __construct( $uri ) {

		$this->r['uri'] = $uri;
		$this->r['uri_parts'] = explode("/", $uri);

		array_shift($this->r['uri_parts']);

		$this->r['method'] = $_SERVER['REQUEST_METHOD'];
		$this->r['host'] = $_SERVER['HTTP_HOST'];
		$this->r['content_length'] = $_SERVER['CONTENT_LENGTH'];
		$this->r['content_type'] = $_SERVER['CONTENT_TYPE'];

		$this->r['https'] = ($_SERVER['HTTPS'] ? true : false);

		$this->r['server_addr'] = $_SERVER['SERVER_ADDR'];
		$this->r['server_port'] = $_SERVER['SERVER_PORT'];

		$this->r['requestor_addr'] = $_SERVER['REMOTE_ADDR'];
		$this->r['requestor_port'] = $_SERVER['REMOTE_PORT'];

	}

	public function __class( $k, $v ) {
		print_r($k);
		print_r($v);
	}

}