<?php
use Luracast\Restler\RestException;

/**
 *
 * @access protected
 */
class Users {

	public $dp;

	static $FIELDS = array('username', 'password');

	function __construct() {
		$this->dp = new DB_PDO_Users();
	}

	function post($request_data = NULL) {
		return $this->dp->insert($this->_validate($request_data));
	}

	private function _validate($data) {
		$reso = array();
		foreach (Users::$FIELDS as $field) {
//you may also validate the data here
			if (!isset($data[$field]))
				throw new RestException(400, "$field field missing");
			$reso[$field] = $data[$field];
		}
		return $reso;
	}
}
