<?php
use Luracast\Restler\RestException;
use Valitron\Validator;

class SubSchedules {

	private $dp;

	static $FIELDS = array('title', 'start_time', 'end_time', 'presenter', 'lead', 'resources');

    private $rules = [
        'required' => [ 
            ['title'],
            ['start_time'],
            ['end_time'],
            ['presenter'],
            ['lead']
        ]
    ];

	function __construct() {
		$this->dp = new DB_PDO_SubSchedules();
	}

	/**
	*
	* @url GET schedules/{schedule_id}/sub_schedules
	*/
	function index($schedule_id) {
		return $this->dp->getAll($schedule_id);
	}

	/**
	*
	* @url GET schedules/{schedule_id}/sub_schedules/{id}
	*/
	function get($id) {
        return $this->dp->get($id);
    }

    /**
    *
    * @url POST schedules/{schedule_id}/sub_schedules
    *
    */
    function post($schedule_id, $request_data = NULL) {
        if($_SESSION['user']['authlevel'] > 50) {
            throw new RestException(401, 'Not authorized');
        }

        $v = new Validator($request_data);
        $v->rules($this->rules);

        if($v->validate()) {
            return $this->dp->insert($schedule_id, $request_data);
        }

        return $v->errors();
    }

    /**
    *
    * @url PUT schedules/{schedule_id}/sub_schedules/{id}
    *
    */
    function put($schedule_id, $id, $request_data = NULL) {
        if($_SESSION['user']['authlevel'] > 50) {
            throw new RestException(401, 'Not authorized');
        }

        $v = new Validator($request_data);
        $v->rules($this->rules);

        if($v->validate()) {
            return $this->dp->update($schedule_id, $id, $request_data);
        }

        return $v->errors();
    }

    /**
    *
    * @url DELETE schedules/{schedule_id}/sub_schedules/{id}
    *
    */
    function delete($schedule_id, $id) {
        if($_SESSION['user']['authlevel'] > 50) {
            throw new RestException(401, 'Not authorized');
        }
        
        return $this->dp->delete($schedule_id, $id);
    }
}