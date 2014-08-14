<?php

use Luracast\Restler\RestException;

class DB_PDO_Schedules extends DB_PDO_MySqlCRUD
{
    private $sub_schedules;

    function __construct()
    {
        try {
            parent::__construct();
            $this->sub_schedules = new DB_PDO_SubSchedules();
        } catch (PDOException $e) {
            throw new RestException(501, 'MySQL: ' . $e->getMessage());
        }
    }

    function get($id)
    {
        $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        try {
            $sql = $this->db->prepare('SELECT * FROM schedules WHERE id = :id');
            $sql->execute(array(':id' => $id));
            return $this->loadSubSchedules($this->id2int($sql->fetch()));
        } catch (PDOException $e) {
            throw new RestException(501, 'MySQL: ' . $e->getMessage());
        }
    }

    function getAll($condition)
    {
        $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        try {
            $stmtStr = 'SELECT * FROM schedules';
            switch($condition) {
                case 'UPCOMING':
                    $stmtStr = $stmtStr . ' where s_date > curdate()';
                    break;
                case 'PAST':
                    $stmtStr = $stmtStr . ' where s_date < curdate()';
                    break;
            }
            $stmt = $this->db->query($stmtStr);
            return $this->id2int($stmt->fetchAll());
        } catch (PDOException $e) {
            throw new RestException(501, 'MySQL: ' . $e->getMessage());
        }
    }

    function lock($id, $rec) {
        $sql = $this->db->prepare("UPDATE schedules SET locked = 1 WHERE id = :id");
        if (!$sql->execute(array(':id' => $id)))
            return FALSE;
        return $this->get($id);
    }

    function unlock($id, $rec) {
        $sql = $this->db->prepare("UPDATE schedules SET locked = 0 WHERE id = :id");
        if (!$sql->execute(array(':id' => $id)))
            return FALSE;
        return $this->get($id);
    }

    function insert($rec)
    {
        $sql = $this->db->prepare("INSERT INTO schedules (theme, s_date) VALUES (:theme, :s_date)");
        if (!$sql->execute(array(':theme' => $rec['theme'], ':s_date' => $rec['s_date'])))
            return FALSE;
        return $this->get($this->db->lastInsertId());
    }

    function update($id, $rec)
    {
        $sql = $this->db->prepare("select locked from schedules where id = :id");
        $sql->execute(array(':id' => $id));
        $retval = $sql->fetch()['locked'];
        if($retval)
            throw new RestException(401, 'Not authorized');

        $sql = $this->db->prepare("UPDATE schedules SET theme = :theme, s_date = :s_date WHERE id = :id");
        if (!$sql->execute(array(':id' => $id, ':theme' => $rec['theme'], ':s_date' => $rec['s_date'])))
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

    private function loadSubSchedules($r)
    {
        if (is_array($r)) {
            if (isset($r['id'])) {
                $r['sub_schedules'] = $this->sub_schedules->getAll($r['id']);
            } else {
                foreach ($r as &$r0) {
                    $r0['sub_schedules'] = $this->sub_schedules->getAll($r0['id']);
                }
            }
        }
        return $r;
    }
}

