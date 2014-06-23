<?php
use Luracast\Restler\iAuthenticate;

class SimpleAuth implements iAuthenticate
{
    public $dp;

    static $FIELDS = array('username', 'password');

    function __construct() {
        $this->dp = new DB_PDO_Users();
    }

    function __isAllowed()
    {
        session_start();
    	error_log(session_id()."  Session id \r\n ", 3, "/tmp/php_error.log");
    	error_log(PHP_SESSION_ACTIVE."  PHP_SESSION_ACTIVE \r\n ", 3, "/tmp/php_error.log");
    	error_log((session_status() == PHP_SESSION_ACTIVE) ? "true \r\n" : "false \r\n", 3, "/tmp/php_error.log");
        $user = $_SESSION['user'];
        error_log($user . " $_SESSION[user] \r\n", 3, "/tmp/php_error.log");
    	
        return !(!isset($user) || trim($user)==='');
    }

    public function postAuth($request_data = NULL) {
        error_log(" postAuth \r\n ", 3, "/tmp/php_error.log");
        return $this->dp->validate($request_data);
    }

    public function postLogout() {
        session_start();
        session_destroy();
    }

    public function __getWWWAuthenticateString()
    {
        return 'Query name="session_id"';
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