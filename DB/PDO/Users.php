<?php

use Luracast\Restler\RestException;

class DB_PDO_Users extends DB_PDO_MySqlCRUD
{
    function validate($rec) {
        $this->getDb()->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        try {
            $sql = $this->getDb()->prepare('SELECT count(*) as count FROM users WHERE username = :username');
            $sql->execute(array(':username' => $rec['username']));
            $row = $sql->fetch();
            if($row['count'] != 1) {
                throw new RestException(401, 'Username or Password does not match');
            }

            $sql = $this->getDb()->prepare('SELECT count(*) as count FROM users WHERE username = :username and password = :password');
            $sql->execute(array(':username' => $rec['username'], ':password' => sha1($rec['password'])));
            $row = $sql->fetch();
            if($row['count'] != 1) {
                throw new RestException(401, 'Username or Password does not match');
            }

            session_destroy();
            session_start();
            $_SESSION['user'] = 'test';
            session_write_close();

            error_log(session_status().'  Users Session Status < ', 3, "/tmp/php_error.log");

            $sql = $this->getDb()->prepare('SELECT username FROM users WHERE username = :username');
            $row = $sql->fetch(PDO::FETCH_ASSOC);
            $sql->execute(array(':username' => $rec['username']));

            return $sql->fetch();
        } catch (PDOException $e) {
            throw new RestException(501, 'MySQL: ' . $e->getMessage());
        }
    }
}

