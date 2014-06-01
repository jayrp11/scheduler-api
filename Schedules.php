<?php
use Luracast\Restler\RestException;

class Schedules {

	public $dp;

	static $FIELDS = array('theme', 'occurs_on');

	function __construct() {
		$this->dp = new DB_PDO_Schedules();
	}

	function index() {
		return $this->dp->getAll();
	}

	function get($id) {
		return $this->dp->get($id);
	}

	function post($request_data = NULL) {
		return $this->dp->insert($this->_validate($request_data));
	}

	function put($id, $request_data = NULL) {
        return $this->dp->update($id, $this->_validate($request_data));
    }

    function delete($id) {
        return $this->dp->delete($id);
    }

	private function _validate($data) {
		$reso = array();
		foreach (Schedules::$FIELDS as $field) {
//you may also validate the data here
			if (!isset($data[$field]))
				throw new RestException(400, "$field field missing");
			$reso[$field] = $data[$field];
		}
		return $reso;
	}
}
