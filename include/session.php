<?php
jieqi_includedb();
class JieqiSessionHandler extends JieqiObjectHandler
{
    public function open($save_path, $session_name)
    {
        return true;
    }
    public function close()
    {
        return true;
    }
    public function read($sess_id)
    {
        $sql = 'SELECT sess_data FROM ' . jieqi_dbprefix('system_session') . ' WHERE sess_id = \'' . $sess_id . '\'';
        if (false != ($result = $this->db->query($sql))) {
            if (list($sess_data) = $this->db->fetchRow($result)) {
                return $sess_data;
            }
        }
        return '';
    }
    public function write($sess_id, $sess_data)
    {
        list($count) = $this->db->fetchRow($this->db->query('SELECT COUNT(*) FROM ' . jieqi_dbprefix('system_session') . ' WHERE sess_id=\'' . $sess_id . '\''));
        if (0 < $count) {
            $sql = sprintf('UPDATE %s SET sess_updated = %u, sess_data = \'%s\' WHERE sess_id = \'%s\'', jieqi_dbprefix('system_session'), JIEQI_NOW_TIME, jieqi_dbslashes($sess_data), $sess_id);
        } else {
            $sql = sprintf('INSERT INTO %s (sess_id, sess_updated, sess_data) VALUES (\'%s\', %u, \'%s\')', jieqi_dbprefix('system_session'), $sess_id, JIEQI_NOW_TIME, jieqi_dbslashes($sess_data));
        }
        if (!$this->db->query($sql)) {
            return false;
        }
        return true;
    }
    public function destroy($sess_id)
    {
        $sql = sprintf('DELETE FROM %s WHERE sess_id = \'%s\'', jieqi_dbprefix('system_session'), $sess_id);
        if (!($result = $this->db->query($sql))) {
            return false;
        }
        return true;
    }
    public function gc($expire)
    {
        $mintime = JIEQI_NOW_TIME - intval($expire);
        $sql = sprintf('DELETE FROM %s WHERE sess_updated < %u', jieqi_dbprefix('system_session'), $mintime);
        $this->db->query($sql);
    }
}
