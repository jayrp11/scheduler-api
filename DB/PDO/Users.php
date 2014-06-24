<?php

use Luracast\Restler\RestException;

class DB_PDO_Users extends DB_PDO_MySqlCRUD
{
    function get($id)
    {
        $this->getDb()->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        try {
            $sql = $this->getDb()->prepare('SELECT * FROM users WHERE id = :id');
            $sql->execute(array(':id' => $id));
            return $this->id2int($sql->fetch());
        } catch (PDOException $e) {
            throw new RestException(501, 'MySQL: ' . $e->getMessage());
        }
    }

    function insert($rec)
    {
        $sql = $this->getDb()->prepare("INSERT INTO users (username, password) VALUES (:username, :password)");
        if (!$sql->execute(array(':username' => $rec['username'], ':password' => sha1($rec['password']))))
            return FALSE;
        return $this->get($this->getDb()->lastInsertId());
    }

    function validate($rec) {
        $this->getDb()->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        try {
            $sql = $this->getDb()->prepare('SELECT count(*) as count FROM users WHERE username = :username');
            $sql->execute(array(':username' => $rec['username']));
            $row = $sql->fetch();
            if($row['count'] != 1) {
                throw new RestException(400, 'Username or Password does not match');
            }

            $sql = $this->getDb()->prepare('SELECT count(*) as count FROM users WHERE username = :username and password = :password');
            $sql->execute(array(':username' => $rec['username'], ':password' => sha1($rec['password'])));
            $row = $sql->fetch();
            if($row['count'] != 1) {
                throw new RestException(400, 'Username or Password does not match');
            }

            error_log(session_status()."  Users Session Status \r\n ", 3, "/tmp/php_error.log");

            $sql = $this->getDb()->prepare('SELECT username, type FROM users WHERE username = :username');
            $row = $sql->fetch(PDO::FETCH_ASSOC);
            $sql->execute(array(':username' => $rec['username']));

            return $sql->fetch();
        } catch (PDOException $e) {
            throw new RestException(501, 'MySQL: ' . $e->getMessage());
        }
    }

    private function id2int($r)
    {
        if (is_array($r)) {
            if (isset($r['id'])) {
                $r['id'] = intval($r['id']);
            } else {
                foreach ($r as &$r0) {
                    $r0['id'] = intval($r0['id']);
                }
            }
        }
        return $r;
    }
}

