<?php

class appmodel
{
    public $db;
    public $base;
    public function __construct(&$base)
    {
        $this->appmodel($base);
    }
    public function appmodel(&$base)
    {
        $this->base = $base;
        $this->db = $base->db;
    }
    public function get_apps($col = '*', $where = '')
    {
        $arr = $this->db->fetch_all('SELECT ' . $col . ' FROM ' . UC_DBTABLEPRE . 'applications' . ($where ? ' WHERE ' . $where : ''), 'appid');
        foreach ($arr as $k => $v) {
            isset($v['extra']) && !empty($v['extra']) && ($v['extra'] = unserialize($v['extra']));
            unset($v['authkey']);
            $arr[$k] = $v;
        }
        return $arr;
    }
}
!defined('IN_UC') && exit('Access Denied');