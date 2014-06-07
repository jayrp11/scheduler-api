<?php

use Luracast\Restler\RestException;

class DB_PDO_Assets extends DB_PDO_MySqlCRUD
{
    function get($id)
    {
        $this->getDb()->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        try {
            $sql = $this->getDb()->prepare('SELECT * FROM resources WHERE id = :id');
            $sql->execute(array(':id' => $id));
            return $this->id2int($sql->fetch());
        } catch (PDOException $e) {
            throw new RestException(501, 'MySQL: ' . $e->getMessage());
        }
    }

    function getAll()
    {
        $this->getDb()->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        try {
            $stmt = $this->getDb()->query('SELECT * FROM resources');
            return $this->id2int($stmt->fetchAll());
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

