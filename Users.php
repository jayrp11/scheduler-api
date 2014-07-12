<?php
use Luracast\Restler\RestException;
use Valitron\Validator;

/**
 *
 * @access protected
 */
class Users {

	private $dp;

	private $rules = [
		'required' => [ 
			['username'],
			['password']
		]
	];

	function __construct() {
		$this->dp = new DB_PDO_Users();
	}

	function post($request_data = NULL) {
		$v = new Validator($request_data);
		$v->rules($this->rules);

		if($v->validate()) {
			$this->dp->insert($request_data);
		} else {
			return $v->errors();
		}
	}
}
