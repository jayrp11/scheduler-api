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
            $s = $this->id2int($sql->fetch());
            $r = $this->getResources($id);
            $s['resources'] = $r;
            return $s;
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
        $id = $this->db->lastInsertId();
        $this->saveOrUpdateResources($id, $rec['resources']);
        return $this->get($id);
    }

    function update($schedule_id, $id, $rec)
    {
        $sql = $this->db->prepare("UPDATE sub_schedules set schedule_id = :schedule_id, title = :title, start_time = :start_time, end_time = :end_time, presenter = :presenter, lead = :lead where id = :id");
        if (!$sql->execute(array(
                ':id'               => $id,
                ':schedule_id'        => $schedule_id, 
                ':title'            => $rec['title'],
                ':start_time'    => $rec['start_time'],
                ':end_time'    => $rec['end_time'],
                ':presenter'    => $rec['presenter'],
                ':lead'    => $rec['lead'],
                )))
            return FALSE;
        $this->saveOrUpdateResources($id, $rec['resources']);
        return $this->get($id);
    }

    function delete($schedule_id, $id)
    {
        $r = $this->get($id);
        if (!$r || !$this->db->prepare('DELETE FROM sub_schedules WHERE id = ?')->execute(array($id)))
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

    private function getResources($sub_schedule_id) {
        $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        try {
            $sql = $this->db->prepare('SELECT resource_id as id FROM sub_schedules_resources WHERE sub_schedule_id = :sub_schedule_id');
            $sql->execute(array(':sub_schedule_id' => $sub_schedule_id));

            $r = array();
            foreach ($this->id2int($sql->fetchAll()) as &$r0) {
                array_push($r, $r0['id']);
            }

            return $r;
        } catch (PDOException $e) {
            throw new RestException(501, 'MySQL: ' . $e->getMessage());
        }        
    }

    private function saveOrUpdateResources($sub_schedule_id, $resources) {
        $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        try {
            // TODO: below must be corrected
            $this->db->prepare('DELETE FROM sub_schedules_resources WHERE sub_schedule_id = ?')->execute(array($sub_schedule_id));

            if (is_array($resources)) {
                foreach ($resources as &$r0) {
                    error_log($r0, 3, "/tmp/php_error.log");
                    $sql = $this->db->prepare("INSERT INTO sub_schedules_resources (sub_schedule_id, resource_id) VALUES (:sub_schedule_id, :resource_id)");
                    if (!$sql->execute(array(
                        ':sub_schedule_id'        => $sub_schedule_id, 
                        ':resource_id'            => $r0
                        )))
                        error_log('FALSE', 3, "/tmp/php_error.log");
                      //return FALSE;        
                }
            }

        } catch (PDOException $e) {
            throw new RestException(501, 'MySQL: ' . $e->getMessage());
        }
    }
}

    