<?php

use Luracast\Restler\RestException;

class DB_PDO_SubSchedules
{
    private $db;

    private $time_pattern = '/^(0?[1-9]|1[012])[:\\-\s]([0-5]*\d)([:\\-\s]([APap][mM]))?$/';

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
            $sql = $this->db->prepare("SELECT id, schedule_id, date_format(start_time, '%h:%i %p') as start_time, date_format(end_time, '%h:%i %p') as end_time, title, presenter, lead FROM sub_schedules WHERE id = :id");
            $sql->execute(array(':id' => $id));
            $s = $this->id2int($sql->fetch());
            $r = $this->getResources($id);
            $s['resources'] = $r;
            return $s;
        } catch (PDOException $e) {
            throw new RestException(501, 'MySQL: ' . $e->getMessage());
        }
    }

    private function loadResources($s)
    {
        if (is_array($s)) {
            foreach ($s as &$s0) {
                $r = $this->getResources($s0['id']);
                $s0['resources'] = $r;
            }
        }
        return $s;
    }

    function getAll($schedule_id)
    {
        $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        try {
            $sql = $this->db->prepare("SELECT id, schedule_id, date_format(start_time, '%h:%i %p') as start_time, date_format(end_time, '%h:%i %p') as end_time, title, presenter, lead FROM sub_schedules WHERE schedule_id = :schedule_id order by start_time asc");
            $sql->execute(array(':schedule_id' => $schedule_id));
            $s = $this->id2int($sql->fetchAll());
            $s = $this->loadResources($s);
            return $s;
        } catch (PDOException $e) {
            throw new RestException(501, 'MySQL: ' . $e->getMessage());
        }
    }

    private function validate($rec) {
        if(strtotime($rec['end_time']) <= strtotime($rec['start_time'])) {
            throw new RestException(400, 'End time is greater then or equal to Start time.');
        }

        $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        try {
            $sql = $this->db->prepare("select count(*) as count from sub_schedules where"
                . " (str_to_date(:start_time, '%h:%i %p') > start_time and str_to_date(:end_time, '%h:%i %p') < end_time)"
                . " or "
                . " (str_to_date(:start_time, '%h:%i %p') < start_time and str_to_date(:end_time, '%h:%i %p') < end_time and str_to_date(:end_time, '%h:%i %p') > start_time)"
                . " or "
                . " (str_to_date(:start_time, '%h:%i %p') > start_time and str_to_date(:end_time, '%h:%i %p') > end_time and str_to_date(:start_time, '%h:%i %p') < end_time)"
                . " or "
                . "(str_to_date(:start_time, '%h:%i %p') < start_time and str_to_date(:end_time, '%h:%i %p') > end_time)");
            $sql->execute(array(':start_time' => $rec['start_time'],
                ':end_time' => $rec['end_time']));
            $row = $sql->fetch();
            error_log($row['count'] ."  count \r\n ", 3, "/tmp/php_error.log");    
            if($row['count'] > 0) {
                throw new RestException(400, 'Start time/End time overlap.');
            }
        } catch (PDOException $e) {
            throw new RestException(501, 'MySQL: ' . $e->getMessage());
        }   
    }

    function format_time($time) {
        error_log($time ."  time \r\n ", 3, "/tmp/php_error.log");
        if(preg_match($this->time_pattern, trim($time), $matches)) {
            return $matches[1].':'.$matches[2].' '.(isset($matches[4])?$matches[4]:'PM');
        } else {
            throw new RestException(400, 'Format does not match for : ' . $time);
        }
    }

    function insert($schedule_id, $rec)
    {
        $rec['start_time'] = $this->format_time($rec['start_time']);
        $rec['end_time'] = $this->format_time($rec['end_time']);
        error_log($rec['start_time'] ."  start time \r\n ", 3, "/tmp/php_error.log");

        $this->validate($rec);

        $sql = $this->db->prepare("INSERT INTO sub_schedules (schedule_id, title, start_time, end_time, presenter, lead) VALUES (:schedule_id, :title, str_to_date(:start_time, '%h:%i %p'), str_to_date(:end_time, '%h:%i %p'), :presenter, :lead)");
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
        $rec['start_time'] = $this->format_time($rec['start_time']);
        $rec['end_time'] = $this->format_time($rec['end_time']);
        error_log($rec['start_time'] ."  start time \r\n ", 3, "/tmp/php_error.log");
        error_log($rec['end_time'] ."  end time \r\n ", 3, "/tmp/php_error.log");

        $this->validate($rec);

        $sql = $this->db->prepare("UPDATE sub_schedules set schedule_id = :schedule_id, title = :title, start_time = str_to_date(:start_time, '%h:%i %p'), end_time = str_to_date(:end_time, '%h:%i %p'), presenter = :presenter, lead = :lead where id = :id");
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
            $sql = $this->db->prepare('SELECT resource_id as id, name FROM sub_schedules_resources sr join resources r on sr.resource_id=r.id WHERE sub_schedule_id = :sub_schedule_id');
            $sql->execute(array(':sub_schedule_id' => $sub_schedule_id));

            $r = $this->id2int($sql->fetchAll());
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

    