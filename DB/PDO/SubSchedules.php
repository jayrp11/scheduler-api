<?php

use Luracast\Restler\RestException;

class DB_PDO_SubSchedules
{
    private $db;

    function __construct()
    {
        try {
            //Make sure you are using UTF-8
            $options = array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8');

            //Update the dbname username and password to suit your server
            $this->db = new PDO(
                'mysql:host=localhost;dbname=scheduler',
                'root',
                'root',
                $options
            );
            $this->db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE,
                PDO::FETCH_ASSOC);

            //If you are using older version of PHP and having issues with Unicode
            //uncomment the following line
            //$this->db->exec("SET NAMES utf8");

        } catch (PDOException $e) {
            throw new RestException(501, 'MySQL: ' . $e->getMessage());
        }
    }

    function get($id)
    {
        $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        try {
            $sql = $this->db->prepare('SELECT * FROM sub_schedules WHERE id = :id');
            $sql->execute(array(':id' => $id));
            return $this->id2int($sql->fetch());
        } catch (PDOException $e) {
            throw new RestException(501, 'MySQL: ' . $e->getMessage());
        }
    }

    function getAll($schedule_id)
    {
        $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        try {
            $sql = $this->db->prepare('SELECT * FROM sub_schedules WHERE schedule_id = :schedule_id');
            $sql->execute(array(':schedule_id' => $schedule_id));
            return $this->id2int($sql->fetchAll());
        } catch (PDOException $e) {
            throw new RestException(501, 'MySQL: ' . $e->getMessage());
        }
    }

    function insert($schedule_id, $rec)
    {
        $sql = $this->db->prepare("INSERT INTO sub_schedules (schedule_id, title, start_time, end_time, presenter, lead) VALUES (:schedule_id, :title, :start_time, :end_time, :presenter, :lead)");
        if (!$sql->execute(array(
                ':schedule_id'        => $schedule_id, 
                ':title'            => $rec['title'],
                ':start_time'    => $rec['start_time'],
                ':end_time'    => $rec['end_time'],
                ':presenter'    => $rec['presenter'],
                ':lead'    => $rec['lead'],
                )))
            return FALSE;
        return $this->get($this->db->lastInsertId());
    }

    function update($id, $rec)
    {
        $sql = $this->db->prepare("UPDATE schedules SET theme = :theme, s_date = :occurs_on WHERE id = :id");
        if (!$sql->execute(array(':id' => $id, ':theme' => $rec['theme'], ':occurs_on' => $rec['occurs_on'])))
            return FALSE;
        return $this->get($id);
    }

    function delete($id)
    {
        $r = $this->get($id);
        if (!$r || !$this->db->prepare('DELETE FROM schedules WHERE id = ?')->execute(array($id)))
            return FALSE;
        return $r;
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

