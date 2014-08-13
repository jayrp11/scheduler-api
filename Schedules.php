<?php
use Valitron\Validator;
use Luracast\Restler\RestException;

/**
 *
 * @access protected
 */
class Schedules {

	private $dp;
	
    private $rules = [
        'required' => [ 
            ['theme'],
            ['s_date']
        ]
    ];

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
        if($_SESSION['user']['authlevel'] > 50) {
            throw new RestException(401, 'Not authorized');
        }

        $v = new Validator($request_data);
        $v->rules($this->rules);

        if($v->validate()) {
        	return $this->dp->insert($request_data);
        }

        return $v->errors();
	}

	function put($id, $request_data = NULL) {
        if($_SESSION['user']['authlevel'] > 50) {
            throw new RestException(401, 'Not authorized');
        }

		$v = new Validator($request_data);
        $v->rules($this->rules);

        if($v->validate()) {
        	return $this->dp->update($id, $request_data);
        }

        return $v->errors();
    }

    function delete($id) {
        if($_SESSION['user']['authlevel'] > 50) {
            throw new RestException(401, 'Not authorized');
        }

        return $this->dp->delete($id);
    }

    function getUpcoming() {
        return $this->dp->getAll('UPCOMING');
    }

    function getPast() {
        return $this->dp->getAll('PAST');
    }

    function postLock($id, $request_data = null) {
        if($_SESSION['user']['authlevel'] > 50) {
            throw new RestException(401, 'Not authorized');
        }

        return $this->dp->lock($id, $request_data);
    }
}
