<?php

class OpenidServer extends OpenidCommon
{
    public function __construct($assoc_handle = '', $type = NULL)
    {
        parent::__construct();
        if (!empty($assoc_handle)) {
            $this->loadAssociation($assoc_handle);
        }
        if (isset($type) && $type == 'SHA256') {
            $this->assoc_type = 'HMAC-SHA256';
            $this->session_type = 'DH-SHA256';
        }
    }
    public function server_url()
    {
        $prefix = 'http://';
        if (isset($_SERVER['HTTPS'])) {
            if (strtolower($_SERVER['HTTPS']) == 'on' || $_SERVER['HTTPS'] == 1) {
                $prefix = 'https://';
            }
        }
        return $prefix . $_SERVER['SERVER_NAME'] . '/';
    }
    public function Associate()
    {
        $data_to_send = array();
        if (!empty($_POST['openid_ns']) && $_POST['openid_ns'] == 'http://specs.openid.net/auth/2.0') {
            $this->openid_version = 2;
            $data_to_send['ns'] = 'http://specs.openid.net/auth/2.0';
        }
        if (empty($_POST['openid_session_type'])) {
            $data_to_send['session_type'] = 'no-encryption';
        } else {
            $data_to_send['session_type'] = $_POST['openid_session_type'];
        }
        if (empty($_POST['openid_assoc_type'])) {
            $this->assoc_type = $_POST['openid_assoc_type'];
            $data_to_send['assoc_type'] = $_POST['openid_assoc_type'];
        } else {
            $this->assoc_type = 'HMAC-SHA1';
            $data_to_send['assoc_type'] = 'HMAC-SHA1';
        }
        $data_to_send['expires_in'] = OPENID_EXPIRES_IN;
        $data_to_send['assoc_handle'] = $this->newAssociation();
        if ($data_to_send['session_type'] != 'no-encryption') {
            if (!empty($_POST['openid_dh_modulus'])) {
                $this->_openid_dh_modulus = $this->btwocDecode(base64_decode($_POST['openid_dh_modulus']));
            } else {
                $this->_openid_dh_modulus = OPENID_DH_MODULUS;
            }
            if (!empty($_POST['openid_dh_gen'])) {
                $this->_openid_dh_gen = $this->btwocDecode(base64_decode($_POST['openid_dh_gen']));
            } else {
                $this->_openid_dh_gen = OPENID_DH_GEN;
            }
            $openid_skey_server = $this->make_skey();
            $data_to_send['dh_server_public'] = base64_encode($this->btwocEncode($this->bcpowmod($this->_openid_dh_gen, $this->_secret_key, $this->_openid_dh_modulus)));
            $data_to_send['enc_mac_key'] = $this->enc_mac_key($_POST['openid_dh_consumer_public']);
        } else {
            $data_to_send['mac_key'] = base64_encode($this->_mac_key);
        }
        $log = 'Associate = ' . print_r($data_to_send, true);
        jieqi_writefile(JIEQI_ROOT_PATH . '/cache/log.txt', $log, 'ab');
        $this->directResponse($data_to_send);
        return true;
    }
    public function Checkid_Setup($positive = false, $adds = array())
    {
        $data_to_send = array();
        if (!empty($_REQUEST['openid_ns']) && $_REQUEST['openid_ns'] == 'http://specs.openid.net/auth/2.0') {
            $data_to_send['openid.ns'] = 'http://specs.openid.net/auth/2.0';
            $this->openid_version = 2;
        }
        if (!$positive) {
            $data_to_send['openid.mode'] = 'cancel';
        } else {
            $data_to_send['openid.mode'] = 'id_res';
            if (isset($this->openid_version) && $this->openid_version == 2) {
                $data_to_send['openid.op_endpoint'] = $this->server_url();
                $data_to_send['openid.claimed_id'] = $_REQUEST['openid_claimed_id'];
                $data_to_send['openid.response_nonce'] = gmdate('Y-m-d') . 'T' . gmdate('H:i:s') . 'Z' . $this->make_randstr(6);
            }
            $data_to_send['openid.identity'] = $_REQUEST['openid_identity'];
            $data_to_send['openid.return_to'] = $_REQUEST['openid_return_to'];
            if (empty($_REQUEST['openid_assoc_handle'])) {
                $data_to_send['openid.assoc_handle'] = $this->newAssociation();
            } else {
                if ($this->loadAssociation($_REQUEST['openid_assoc_handle'])) {
                    $data_to_send['openid.assoc_handle'] = $_REQUEST['openid_assoc_handle'];
                } else {
                    $data_to_send['openid.invalidate_handle'] = $_REQUEST['openid_assoc_handle'];
                    $data_to_send['openid.assoc_handle'] = $this->newAssociation();
                }
            }
            $signed = '';
            foreach ($data_to_send as $key => $v) {
                $signed .= substr($key, 7) . ',';
            }
            if (!empty($adds)) {
                foreach ($adds as $key => $v) {
                    $signed .= 'sreg.' . $key . ',';
                    $data_to_send['openid.sreg.' . $key] = $v;
                }
            }
            $data_to_send['openid.signed'] = $signed . 'signed';
            $tokens = '';
            foreach ($data_to_send as $key => $value) {
                $tokens .= substr($key, 7) . ':' . $value . "\n";
            }
            $data_to_send['openid.sig'] = base64_encode($this->hmac($this->_mac_key, $tokens));
        }
        $log = 'setup = ' . print_r($data_to_send, true);
        jieqi_writefile(JIEQI_ROOT_PATH . '/cache/log.txt', $log, 'ab');
        $this->redirect($_REQUEST['openid_return_to'], 'GET', $data_to_send);
        exit;
    }
    public function Check_Authentication()
    {
        $data_to_send = array();
        if (!empty($_POST['openid_ns']) && $_POST['openid_ns'] == 'http://specs.openid.net/auth/2.0') {
            $data_to_send['openid.ns'] = 'http://specs.openid.net/auth/2.0';
            $this->openid_version = 2;
        }
        $signfields = explode(',', $_POST['openid_signed']);
        $tokens = '';
        foreach ($signfields as $field) {
            if ($field == 'mode') {
                $_POST['openid_' . $field] = 'id_res';
            }
            $tokens .= $field . ':' . $_POST['openid_' . str_replace('.', '_', $field)] . "\n";
        }
        $assoc_handle = $_POST['openid_assoc_handle'];
        if ($this->loadAssociation($assoc_handle)) {
            $sig = base64_encode($this->hmac($this->_mac_key, $tokens));
            if (isset($_POST['openid_sig']) && $_POST['openid_sig'] == $sig) {
                $data_to_send['is_valid'] = 'true';
            } else {
                $data_to_send['is_valid'] = 'false';
            }
            $this->destoryAssociation($assoc_handle);
        } else {
            $data_to_send['is_valid'] = 'false';
        }
        $log = 'Authentication = ' . print_r($data_to_send, true);
        jieqi_writefile(JIEQI_ROOT_PATH . '/cache/log.txt', $log, 'ab');
        $this->directResponse($data_to_send);
    }
    public function Checkid_Immediate($positive = false, $adds = array())
    {
        $data_to_send = array();
        if (!empty($_POST['openid_ns']) && $_POST['openid_ns'] == 'http://specs.openid.net/auth/2.0') {
            $data_to_send['openid.ns'] = 'http://specs.openid.net/auth/2.0';
            $this->openid_version = 2;
        }
        if (!$positive) {
            $data_to_send['openid.mode'] = 'setup_needed';
        } else {
            $data_to_send['openid.mode'] = 'id_res';
            if (isset($this->openid_version) && $this->openid_version == 2) {
                $data_to_send['openid.op_endpoint'] = $this->server_url();
                $data_to_send['openid.claimed_id'] = $_POST['openid_claimed_id'];
                $data_to_send['openid.response_nonce'] = gmdate('Y-m-d') . 'T' . gmdate('H:i:s') . 'Z' . $this->make_randstr(6);
            }
            $data_to_send['openid.identity'] = $_POST['openid_identity'];
            $data_to_send['openid.return_to'] = $_POST['openid_return_to'];
            if (empty($_POST['openid_assoc_handle'])) {
                $data_to_send['openid.assoc_handle'] = $this->newAssociation();
            } else {
                if ($this->loadAssociation($_POST['openid_assoc_handle'])) {
                    $data_to_send['openid.assoc_handle'] = $_POST['openid_assoc_handle'];
                } else {
                    $data_to_send['openid.invalidate_handle'] = $_POST['openid_assoc_handle'];
                    $data_to_send['openid.assoc_handle'] = $this->newAssociation();
                }
            }
            $signed = '';
            foreach ($data_to_send as $key => $v) {
                $signed .= substr($key, 7) . ',';
            }
            if (!empty($adds)) {
                foreach ($adds as $key => $v) {
                    $signed .= 'sreg.' . $key . ',';
                    $data_to_send['openid.sreg.' . $key] = $v;
                }
            }
            $data_to_send['openid.signed'] = $signed . 'signed';
            $tokens = '';
            foreach ($data_to_send as $key => $value) {
                $tokens .= substr($key, 7) . ':' . $value . "\n";
            }
            $data_to_send['openid.sig'] = base64_encode($this->hmac($this->_mac_key, $tokens));
        }
        $log = 'Immediate = ' . print_r($data_to_send, true);
        jieqi_writefile(JIEQI_ROOT_PATH . '/cache/log.txt', $log, 'ab');
        $this->directResponse($data_to_send);
    }
    public function directResponse($data)
    {
        header('Content-Type: text/plain; charset=UTF-8');
        foreach ($data as $key => $value) {
            echo $key . ':' . $value . "\n";
        }
        exit;
    }
    public function assocHandle($usesid = true)
    {
        if ($usesid) {
            if (session_id() == '') {
                session_start();
            }
            $openid_assoc_handle = session_id();
        } else {
            switch ($this->assoc_type) {
                case 'HMAC-SHA256':
                    $limit = 32;
                    break;
                case 'HMAC-SHA1':
                    $limit = 20;
                    break;
                default:
                    $limit = 20;
            }
            $openid_assoc_handle = '';
            for ($i = 0; $i < $limit; $i++) {
                $openid_assoc_handle .= chr(rand(97, 122));
            }
        }
        return $openid_assoc_handle;
    }
    public function newAssociation()
    {
        if (session_id() == '') {
            session_start();
        }
        @session_regenerate_id();
        $_SESSION['openid_assoc_type'] = $this->assoc_type;
        $_SESSION['openid_mac_key'] = $this->make_mkey();
        $this->_assoc_handle = session_id();
        return $this->_assoc_handle;
    }
    public function loadAssociation($assoc_handle)
    {
        $this->_assoc_handle = $assoc_handle;
        $old_sessionid = session_id();
        if ($old_sessionid != $assoc_handle) {
            session_write_close();
            session_id($assoc_handle);
            session_start();
        }
        if (!empty($_SESSION['openid_mac_key'])) {
            $this->assoc_type = $_SESSION['openid_assoc_type'];
            $this->_mac_key = $_SESSION['openid_mac_key'];
            $ret = true;
        } else {
            $ret = false;
        }
        if (!empty($old_sessionid) && $old_sessionid != $assoc_handle) {
            session_write_close();
            session_id($old_sessionid);
            session_start();
        }
        return $ret;
    }
    public function destoryAssociation($assoc_handle)
    {
        $old_sessionid = session_id();
        if ($old_sessionid != $assoc_handle) {
            session_write_close();
            session_id($assoc_handle);
            session_start();
        }
        $_SESSION = array();
        @session_destroy();
        if (!empty($old_sessionid) && $old_sessionid != $assoc_handle) {
            session_write_close();
            session_id($old_sessionid);
            session_start();
        }
        return true;
    }
}
require_once dirname(__FILE__) . '/openidcommon.php';