<?php
use Luracast\Restler\iAuthenticate;

class Auth implements iAuthenticate
{
    public $dp;

    static $FIELDS = array('username', 'password');

    function __construct() {
        $this->dp = new DB_PDO_Users();
    }

    public function __getWWWAuthenticateString() {
        return 'Query name="session_id"';
    }

    function __isAllowed()
    {
        session_start();
        $user = $_SESSION['user'];
        return !(!isset($user) || trim($user)==='');
    }

    public function postLogin($request_data = NULL) {
        $user = $this->dp->validate($request_data);

        session_start();
        $_SESSION = array();
        $_SESSION['user'] = $user['username'];

        return $user;
    }

    public function postLogout() {
        session_start();
        session_destroy();
    }

    private function _validate($data) {
        $reso = array();
        foreach (SubSchedules::$FIELDS as $field) {
            if (!isset($data[$field]))
                throw new RestException(400, "$field field missing");
            $reso[$field] = $data[$field];
        }
        return $reso;
    }
}