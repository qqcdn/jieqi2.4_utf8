<?php

class JieqiFTP extends JieqiObject
{
    public $_host;
    public $_port = 21;
    public $_user;
    public $_pass;
    public $_path = '.';
    public $_ssl = 0;
    public $_timeout = 0;
    public $_pasv = 1;
    public $connid;
    public static $instance = array();
    public function wipespecial($str)
    {
        return str_replace(array("\n", "\r"), '', $str);
    }
    public function __construct($ftphost = '', $ftpuser = '', $ftppass = '', $ftppath = '.', $ftpport = 21, $timeout = 0, $ftpssl = 0, $ftppasv = 1)
    {
        if (JIEQI_VERSION_TYPE == '' || JIEQI_VERSION_TYPE == 'Free') {
            exit('Your version type is ' . JIEQI_VERSION_TYPE . ', ftp function is is not supported!');
        }
        $this->_host = $this->wipespecial($ftphost);
        $this->_user = $ftpuser;
        $this->_pass = $ftppass;
        $this->_port = intval($ftpport);
        $this->_timeout = intval($timeout);
        $this->_ssl = intval($ftpssl);
        $this->_pasv = intval($ftppasv);
        $this->_path = $ftppath;
    }
    public static function retInstance()
    {
        return self::$instance;
    }
    public static function close($ftp = NULL)
    {
        if (is_object($ftp)) {
            $ftp->ftp_close();
        } else {
            if (!empty(self::$instance)) {
                foreach (self::$instance as $ftp) {
                    $ftp->ftp_close();
                }
            }
        }
    }
    public static function getInstance($ftphost = '', $ftpuser = '', $ftppass = '', $ftppath = '.', $ftpport = 21, $timeout = 0, $ftpssl = 0, $ftppasv = 1)
    {
        $inskey = md5($ftphost . ',' . $ftpuser . ',' . $ftppass . ',' . $ftppath . ',' . $ftpport . ',' . $timeout . ',' . $ftpssl . ',' . $ftppasv);
        if (!isset(self::$instance[$inskey])) {
            self::$instance[$inskey] = new JieqiFTP($ftphost, $ftpuser, $ftppass, $ftppath, $ftpport, $timeout, $ftpssl, $ftppasv);
            $fid = self::$instance[$inskey]->ftp_connect();
            if (!$fid) {
                return false;
            }
        }
        return self::$instance[$inskey];
    }
    public function ftp_connect()
    {
        $func = $this->_ssl && function_exists('ftp_ssl_connect') ? 'ftp_ssl_connect' : 'ftp_connect';
        if ($func == 'ftp_connect' && !function_exists('ftp_connect')) {
            $this->raiseError('FTP not supported', JIEQI_ERROR_RETURN);
            return -4;
        }
        if ($this->connid = @func($this->_host, $this->_port, 20)) {
            if ($this->_timeout && function_exists('ftp_set_option')) {
                @ftp_set_option($this->connid, FTP_TIMEOUT_SEC, $this->_timeout);
            }
            if ($this->ftp_login($this->_user, $this->_pass)) {
                if ($this->_pasv) {
                    $this->ftp_pasv(true);
                }
                if ($this->ftp_chdir($this->_path)) {
                    if (!defined('JIEQI_FTP_CONNECTED')) {
                        @define('JIEQI_FTP_CONNECTED', true);
                    }
                    return 1;
                } else {
                    $this->ftp_close();
                    $this->raiseError('Chdir ' . $this->_path, ' error', JIEQI_ERROR_RETURN);
                    return -3;
                }
            } else {
                $this->ftp_close();
                $this->raiseError('FTP login failure', JIEQI_ERROR_RETURN);
                return -2;
            }
        } else {
            $this->raiseError('Couldn\'t connect to ' . $this->_host . ':' . $this->_port, JIEQI_ERROR_RETURN);
            return -2;
        }
    }
    public function ftp_mkdir($directory)
    {
        $directory = $this->wipespecial($directory);
        return @ftp_mkdir($this->connid, $directory);
    }
    public function ftp_rmdir($directory)
    {
        $directory = $this->wipespecial($directory);
        return @ftp_rmdir($this->connid, $directory);
    }
    public function ftp_put($remote_file, $local_file, $mode = FTP_BINARY, $startpos = 0)
    {
        $remote_file = $this->wipespecial($remote_file);
        $local_file = $this->wipespecial($local_file);
        $mode = intval($mode);
        $startpos = intval($startpos);
        return @ftp_put($this->connid, $remote_file, $local_file, $mode, $startpos);
    }
    public function ftp_size($remote_file)
    {
        $remote_file = $this->wipespecial($remote_file);
        return @ftp_size($this->connid, $remote_file);
    }
    public function ftp_close()
    {
        return @ftp_close($this->connid);
    }
    public function ftp_delete($path)
    {
        $path = $this->wipespecial($path);
        return @ftp_delete($this->connid, $path);
    }
    public function ftp_get($local_file, $remote_file, $mode = FTP_BINARY, $resumepos = 0)
    {
        $remote_file = $this->wipespecial($remote_file);
        $local_file = $this->wipespecial($local_file);
        $mode = intval($mode);
        $resumepos = intval($resumepos);
        return @ftp_get($this->connid, $local_file, $remote_file, $mode, $resumepos);
    }
    public function ftp_login($username, $password)
    {
        $username = $this->wipespecial($username);
        $password = str_replace(array("\n", "\r"), array('', ''), $password);
        return @ftp_login($this->connid, $username, $password);
    }
    public function ftp_pasv($pasv)
    {
        $pasv = intval($pasv);
        return @ftp_pasv($this->connid, $pasv);
    }
    public function ftp_chdir($directory)
    {
        $directory = $this->wipespecial($directory);
        return @ftp_chdir($this->connid, $directory);
    }
    public function ftp_site($cmd)
    {
        $cmd = $this->wipespecial($cmd);
        return @ftp_site($this->connid, $cmd);
    }
    public function ftp_chmod($mode, $filename)
    {
        $mode = intval($mode);
        $filename = $this->wipespecial($filename);
        if (function_exists('ftp_chmod')) {
            return @ftp_chmod($this->connid, $mode, $filename);
        } else {
            return $this->ftp_site($this->connid, 'CHMOD ' . $mode . ' ' . $filename);
        }
    }
    public function ftp_rename($oldfile, $newfile)
    {
        return @ftp_rename($this->connid, $oldfile, $newfile);
    }
    public function ftp_pwd()
    {
        return @ftp_pwd($this->connid);
    }
    public function ftp_nlist($path)
    {
        $path = $this->wipespecial($path);
        return @ftp_nlist($this->connid, $path);
    }
    public function ftp_delfolder($path, $flag = true)
    {
        $path = $this->wipespecial($path);
        if ($flag) {
            $ret = $this->ftp_rmdir($path) || $this->ftp_delete($path);
        } else {
            $ret = false;
        }
        if (!$ret) {
            $files = $this->ftp_nlist($path);
            foreach ($files as $values) {
                $values = basename($values);
                if (!$this->ftp_delete($path . '/' . $values)) {
                    $this->ftp_delfolder($path . '/' . $values, true);
                }
            }
            if ($flag) {
                return $this->ftp_rmdir($path);
            } else {
                return true;
            }
        } else {
            return $ret;
        }
    }
    public function ftp_mkdirs($path)
    {
        $path = $this->wipespecial($path);
        $path_arr = explode('/', $path);
        $path_div = count($path_arr);
        foreach ($path_arr as $val) {
            if ($this->ftp_chdir($val) == false) {
                $tmp = $this->ftp_mkdir($val);
                if ($tmp == false) {
                    $this->raiseError('FTP mkdir failure', JIEQI_ERROR_RETURN);
                    exit;
                }
                $this->ftp_chdir($val);
            }
        }
        for ($i = 1; $i <= $path_div; $i++) {
            @ftp_cdup($this->connid);
        }
    }
    public function ftp_xcopy($srcfolder, $dstfolder)
    {
        $srcfolder = $this->wipespecial($srcfolder);
        $dstfolder = $this->wipespecial($dstfolder);
        $srcfiles = $this->ftp_nlist($srcfolder);
        $this->ftp_mkdirs($dstfolder);
        foreach ($srcfiles as $srcfile) {
            $srcfile = basename($srcfile);
            if (!$this->ftp_rename($srcfolder . '/' . $srcfile, $dstfolder . '/' . $srcfile)) {
                $this->ftp_mkdir($dstfolder . '/' . $srcfile);
                $this->ftp_xcopy($srcfolder . '/' . $srcfile, $dstfolder . '/' . $srcfile);
            }
        }
    }
}