<?php

class JieqiSort
{
    public $query;
    public $table;
    public $savefile = 'sort';
    public $savevar = 'jieqiSort';
    public $module = 'system';
    public $autosave = true;
    public function __construct($table, $query = NULL)
    {
        $this->table = $table;
        if (is_object($query) && is_a($query, 'JieqiQueryHandler')) {
            $this->query =& $query;
        } else {
            jieqi_includedb();
            $this->query = JieqiQueryHandler::getInstance('JieqiQueryHandler');
        }
        if (defined('JIEQI_MODULE_NAME')) {
            $this->module = JIEQI_MODULE_NAME;
        }
    }
    public function getSort($sortid, $retid = true)
    {
        $sql = 'SELECT * FROM `' . $this->table . '` WHERE sortid = ' . intval($sortid) . ' LIMIT 1';
        $this->query->execute($sql);
        $row = $this->query->getRow();
        if (!is_array($row)) {
            return false;
        } else {
            if ($retid) {
                return $row['sortid'];
            } else {
                return $row;
            }
        }
    }
    public function getSubs($sortid, $retid = true)
    {
        $rows = array();
        $sql = 'SELECT * FROM `' . $this->table . '` WHERE parentid = ' . intval($sortid) . ' ORDER BY `sortorder` ASC, `sortid` ASC';
        $this->query->execute($sql);
        while ($row = $this->query->getRow()) {
            $rows[$row['sortid']] = $retid ? $row['sortid'] : $row;
        }
        return $rows;
    }
    public function getChilds($sortid, $retid = true, $check = false)
    {
        $rows = array();
        $sql = 'SELECT * FROM `' . $this->table . '` WHERE parentid = ' . intval($sortid) . ' ORDER BY `sortorder` ASC, `sortid` ASC';
        $res = $this->query->execute($sql);
        while ($row = $this->query->getRow($res)) {
            $row['sortid'] = intval($row['sortid']);
            $rows[$row['sortid']] = $retid ? $row['sortid'] : $row;
            if (!empty($row['childs']) || $check) {
                $rows += $this->getChilds($row['sortid'], $retid, $check);
            }
        }
        return $rows;
    }
    public function getSorts($sortid, $retid = true)
    {
        $row = $this->getSort($sortid, $retid);
        if ($row === false) {
            return false;
        } else {
            return array_merge(array($row), $this->getChilds($sortid, $retid));
        }
    }
    public function getRoutes($sortid, $retid = true)
    {
        $sortid = intval($sortid);
        if (empty($sortid)) {
            return false;
        }
        $rows = array();
        while ($row = $this->getSort($sortid, false)) {
            if ($retid) {
                array_unshift($rows, $row['sortid']);
            } else {
                array_unshift($rows, $row);
            }
            if (!empty($row['parentid'])) {
                $sortid = $row['parentid'];
            } else {
                break;
            }
        }
        return $rows;
    }
    public function addSort($params)
    {
        $params['parentid'] = intval($params['parentid']);
        $params['sortname'] = trim($params['sortname']);
        if (strlen($params['sortname']) == 0) {
            return false;
        }
        $sql = '';
        foreach ($params as $k => $v) {
            if ($k != 'sortid' && preg_match('/^\\w+$/', $k)) {
                if (!empty($sql)) {
                    $sql .= ', ';
                }
                $sql .= '`' . $k . '`=\'' . jieqi_dbslashes($v) . '\'';
            }
        }
        $sql = 'INSERT INTO `' . $this->table . '` SET ' . $sql;
        if ($this->query->execute($sql)) {
            $sortid = $this->query->db->getInsertId();
            $this->updateChilds($sortid, false, true);
            if (0 < $params['parentid']) {
                $rows = $this->getRoutes($sortid, true);
                if (is_array($rows)) {
                    foreach ($rows as $sid) {
                        if ($sid != $sortid) {
                            $this->updateChilds($sid, true, false);
                        }
                    }
                }
            }
            if ($this->autosave) {
                $this->setConfigs();
            }
            return true;
        } else {
            return false;
        }
    }
    public function deleteSort($sortid)
    {
        $routes = $this->getRoutes($sortid, true);
        $rows = $this->getSorts($sortid, true);
        if (!is_array($rows)) {
            return false;
        } else {
            if (count($rows) == 0) {
                return true;
            }
        }
        $sql = 'DELETE FROM `' . $this->table . '` WHERE sortid IN (' . implode(',', $rows) . ')';
        if ($this->query->execute($sql)) {
            if (is_array($routes)) {
                foreach ($routes as $sid) {
                    if ($sid != $sortid) {
                        $this->updateChilds($sid, true, false);
                    }
                }
            }
            if ($this->autosave) {
                $this->setConfigs();
            }
            return true;
        } else {
            return false;
        }
    }
    public function updateChilds($sortid, $childs = true, $routes = true)
    {
        $set = '';
        if ($childs) {
            $rows = $this->getChilds($sortid, true, true);
            if (is_array($rows) && 0 < count($rows)) {
                $set .= 'childs = \'' . jieqi_dbslashes(implode(',', $rows)) . '\'';
            } else {
                $set .= 'childs = \'\'';
            }
        }
        if ($routes) {
            $rows = $this->getRoutes($sortid, true);
            if (is_array($rows) && 0 < count($rows)) {
                if (!empty($set)) {
                    $set .= ', ';
                }
                $set .= 'layer = ' . (count($rows) - 1) . ', routes = \'' . jieqi_dbslashes(implode(',', $rows)) . '\'';
            } else {
                $set .= 'layer = 0, routes = \'' . jieqi_dbslashes($sortid) . '\'';
            }
        }
        if (!empty($set)) {
            $sql = 'UPDATE `' . $this->table . '` SET ' . $set . ' WHERE sortid = ' . intval($sortid);
            return $this->query->execute($sql);
        } else {
            return false;
        }
    }
    public function moveSort($sortid, $parentid)
    {
        $sortid = intval($sortid);
        $parentid = intval($parentid);
        $childs = $this->getSorts($sortid, true);
        $fparents = $this->getRoutes($sortid, true);
        $tparents = $this->getRoutes($parentid, true);
        $sql = 'UPDATE `' . $this->table . '` SET parentid = ' . $parentid . ' WHERE sortid = ' . $sortid;
        if ($this->query->execute($sql)) {
            if (is_array($childs)) {
                foreach ($childs as $sid) {
                    $this->updateChilds($sid, false, true);
                }
            }
            $sids = array();
            if (is_array($fparents)) {
                foreach ($fparents as $sid) {
                    if ($sid != $sortid && !in_array($sid, $sids)) {
                        $sids[] = $sid;
                    }
                }
            }
            if (is_array($tparents)) {
                foreach ($tparents as $sid) {
                    if (!in_array($sid, $sids)) {
                        $sids[] = $sid;
                    }
                }
            }
            foreach ($sids as $sid) {
                $this->updateChilds($sid, true, false);
            }
            if ($this->autosave) {
                $this->setConfigs();
            }
            return true;
        } else {
            return false;
        }
    }
    public function editSort($params)
    {
        $params['sortid'] = intval($params['sortid']);
        $params['parentid'] = intval($params['parentid']);
        $params['sortname'] = trim($params['sortname']);
        if (empty($params['sortid'])) {
            return false;
        }
        if (strlen($params['sortname']) == 0) {
            return false;
        }
        $sort = $this->getSort($params['sortid'], false);
        if (!is_array($sort)) {
            return false;
        }
        $sql = '';
        foreach ($params as $k => $v) {
            if ($k != 'sortid' && $k != 'parentid' && preg_match('/^\\w+$/', $k)) {
                if (!empty($sql)) {
                    $sql .= ', ';
                }
                $sql .= '`' . $k . '`=\'' . jieqi_dbslashes($v) . '\'';
            }
        }
        if (!empty($sql)) {
            $sql = 'UPDATE `' . $this->table . '` SET ' . $sql . ' WHERE sortid = ' . $params['sortid'];
            $this->query->execute($sql);
        }
        if ($params['parentid'] != $sort['parentid']) {
            if ($params['parentid'] == 0) {
                $this->moveSort($params['sortid'], $params['parentid']);
            } else {
                $parent = $this->getSort($params['parentid'], true);
                if (!empty($parent)) {
                    $this->moveSort($params['sortid'], $params['parentid']);
                }
            }
        }
        if ($this->autosave) {
            $this->setConfigs();
        }
        return true;
    }
    public function updateOrder($orders)
    {
        if (is_array($orders)) {
            foreach ($orders as $k => $v) {
                $k = intval($k);
                $v = intval($v);
                if (0 < $k) {
                    $this->query->execute('UPDATE `' . $this->table . '` SET sortorder = ' . $v . ' WHERE sortid = ' . $k);
                }
            }
        }
        if ($this->autosave) {
            $this->setConfigs();
        }
        return true;
    }
    public function setConfigs($sortid = 0)
    {
        if (!is_array($rows)) {
            $rows = array();
        }
        $rows[$this->module] = $this->getChilds($sortid, false);
        if (!is_array($rows[$this->module])) {
            $rows[$this->module] = array();
        }
        foreach ($rows[$this->module] as $k => $v) {
            foreach ($v as $field => $value) {
                if (!is_numeric($value)) {
                    $rows[$this->module][$k][$field] = jieqi_htmlstr($rows[$this->module][$k][$field]);
                }
            }
        }
        return jieqi_setconfigs($this->savefile, $this->savevar, $rows, $this->module);
    }
}