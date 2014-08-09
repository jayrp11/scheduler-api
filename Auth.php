<?php
use Luracast\Restler\iAuthenticate;
use Valitron\Validator;

class Auth implements iAuthenticate
{
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

    public function __getWWWAuthenticateString() {
        return 'Query name="session_id"';
    }

    function __isAllowed()
    {
        session_start();
        $user = $_SESSION['user'];
        return isset($user);
    }

    public function postLogin($request_data = NULL) {
        $v = new Validator($request_data);
        $v->rules($this->rules);

        if($v->validate()) {
            $user = $this->dp->validate($request_data);

            session_start();
            $_SESSION = array();
            $_SESSION['user'] = $user;

            return $user;
        }

        return $v->errors();
    }

    public function getLogout() {
        session_start();
        session_destroy();
    }

    public function getCurrentUser() {
        session_start();
        return isset($_SESSION['user']) ?  $_SESSION['user'] : NULL;
    }
}