<?php

class appcontrol extends base
{
    public function __construct()
    {
        $this->appcontrol();
    }
    public function appcontrol()
    {
        parent::__construct();
        $this->load('app');
    }
    public function onls()
    {
        $this->init_input();
        $applist = $_ENV['app']->get_apps('appid, type, name, url, tagtemplates, viewprourl, synlogin');
        $applist2 = array();
        foreach ($applist as $key => $app) {
            $app['tagtemplates'] = $this->unserialize($app['tagtemplates']);
            $applist2[$app['appid']] = $app;
        }
        return $applist2;
    }
    public function onadd()
    {
    }
    public function onucinfo()
    {
    }
    public function _random($length, $numeric = 0)
    {
    }
    public function _generate_key()
    {
    }
    public function _format_notedata($notedata)
    {
    }
}
!defined('IN_UC') && exit('Access Denied');