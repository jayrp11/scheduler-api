<?php
use Luracast\Restler\iAuthenticate;

class SimpleAuth implements iAuthenticate
{
    function __isAllowed()
    {

    	session_start();
    	error_log(session_status().'  Session Status < ', 3, "/tmp/php_error.log");
    	error_log(PHP_SESSION_NONE.'  PHP Session < ', 3, "/tmp/php_error.log");
    	error_log($_SESSION['user'].'  $session < ', 3, "/tmp/php_error.log");
    	
        return session_status() === PHP_SESSION_ACTIVE;
    }

    public function __getWWWAuthenticateString()
    {
        return 'Query name="session_id"';
    }
}