<?php
use Luracast\Restler\RestException;

class SubSchedules {

	public $dp;

	static $FIELDS = array('title', 'start_time', 'end_time', 'presenter', 'lead', 'resources');

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
        return $this->dp->insert($schedule_id, $this->_validate($request_data));
    }

    /**
    *
    * @url PUT schedules/{schedule_id}/sub_schedules/{id}
    *
    */
    function put($schedule_id, $id, $request_data = NULL) {
        return $this->dp->update($schedule_id, $id, $this->_validate($request_data));
    }

    /**
    *
    * @url DELETE schedules/{schedule_id}/sub_schedules/{id}
    *
    */
    function delete($schedule_id, $id) {
        return $this->dp->delete($schedule_id, $id);
    }

    private function _validate($data) {
        $reso = array();
        foreach (SubSchedules::$FIELDS as $field) {
//you may also validate the data here
            if (!isset($data[$field]))
                throw new RestException(400, "$field field missing");
            $reso[$field] = $data[$field];
        }
        return $reso;
    }
}