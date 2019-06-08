<?php

class tagcontrol extends base
{
    public function __construct()
    {
        $this->tagcontrol();
    }
    public function tagcontrol()
    {
        parent::__construct();
        $this->init_input();
        $this->load('tag');
        $this->load('misc');
    }
    public function ongettag()
    {
        $appid = $this->input('appid');
        $tagname = $this->input('tagname');
        $nums = $this->input('nums');
        if (empty($tagname)) {
            return NULL;
        }
        $return = $apparray = $appadd = array();
        if ($nums && is_array($nums)) {
            foreach ($nums as $k => $num) {
                $apparray[$k] = $k;
            }
        }
        $data = $_ENV['tag']->get_tag_by_name($tagname);
        if ($data) {
            $apparraynew = array();
            foreach ($data as $tagdata) {
                $row = $r = array();
                $tmp = explode('	', $tagdata['data']);
                $type = $tmp[0];
                array_shift($tmp);
                foreach ($tmp as $tmp1) {
                    $tmp1 != '' && ($r[] = $_ENV['misc']->string2array($tmp1));
                }
                if (in_array($tagdata['appid'], $apparray)) {
                    if (0 < $tagdata['expiration'] && 3600 < $this->time - $tagdata['expiration']) {
                        $appadd[] = $tagdata['appid'];
                        $_ENV['tag']->formatcache($tagdata['appid'], $tagname);
                    } else {
                        $apparraynew[] = $tagdata['appid'];
                    }
                    $datakey = array();
                    $count = 0;
                    foreach ($r as $data) {
                        $return[$tagdata['appid']]['data'][] = $data;
                        $return[$tagdata['appid']]['type'] = $type;
                        $count++;
                        if ($nums[$tagdata['appid']] <= $count) {
                            break;
                        }
                    }
                }
            }
            $apparray = array_diff($apparray, $apparraynew);
        } else {
            foreach ($apparray as $appid) {
                $_ENV['tag']->formatcache($appid, $tagname);
            }
        }
        if ($apparray) {
            $this->load('note');
            $_ENV['note']->add('gettag', 'id=' . $tagname, '', $appadd, -1);
        }
        return $return;
    }
}
!defined('IN_UC') && exit('Access Denied');