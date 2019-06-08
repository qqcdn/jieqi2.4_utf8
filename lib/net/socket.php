<?php

class JieqiSocket extends JieqiObject
{
    public $fp;
    public $blocking = true;
    public $persistent = false;
    public $addr = '';
    public $port = 0;
    public $timeout = false;
    public $lineLength = 2048;
    public function __construct()
    {
        parent::__construct();
    }
    public function connect($addr, $port, $persistent = NULL, $timeout = NULL, $options = NULL)
    {
        if (is_resource($this->fp)) {
            @fclose($this->fp);
            $this->fp = NULL;
        }
        if (strspn($addr, '.0123456789') == strlen($addr)) {
            $this->addr = $addr;
        } else {
            $this->addr = gethostbyname($addr);
        }
        $this->port = $port % 65536;
        if ($persistent !== NULL) {
            $this->persistent = $persistent;
        }
        if ($timeout !== NULL) {
            $this->timeout = $timeout;
        }
        $openfunc = $this->persistent ? 'pfsockopen' : 'fsockopen';
        $errno = 0;
        $errstr = '';
        if ($options && function_exists('stream_context_create')) {
            if ($this->timeout) {
                $timeout = $this->timeout;
            } else {
                $timeout = 0;
            }
            $context = stream_context_create($options);
            $fp = $openfunc($this->addr, $this->port, $errno, $errstr, $timeout, $context);
        } else {
            if ($this->timeout) {
                $fp = @openfunc($this->addr, $this->port, $errno, $errstr, $this->timeout);
            } else {
                $fp = @openfunc($this->addr, $this->port, $errno, $errstr);
            }
        }
        if (!$fp) {
            $this->raiseError($errno . ':' . $errstr, JIEQI_ERROR_RETURN);
            return false;
        }
        $this->fp = $fp;
        return $this->setBlocking($this->blocking);
    }
    public function disconnect()
    {
        if (is_resource($this->fp)) {
            fclose($this->fp);
            $this->fp = NULL;
        }
        return true;
    }
    public function isBlocking()
    {
        return $this->blocking;
    }
    public function setBlocking($mode)
    {
        if (is_resource($this->fp)) {
            $this->blocking = $mode;
            socket_set_blocking($this->fp, $this->blocking);
            return true;
        }
        $this->raiseError('socket is not connected', JIEQI_ERROR_RETURN);
        return false;
    }
    public function setTimeout($seconds, $microseconds)
    {
        if (is_resource($this->fp)) {
            socket_set_timeout($this->fp, $seconds, $microseconds);
            return true;
        }
        $this->raiseError('socket is not connected', JIEQI_ERROR_RETURN);
        return false;
    }
    public function getStatus()
    {
        if (is_resource($this->fp)) {
            return socket_get_status($this->fp);
        }
        $this->raiseError('socket is not connected', JIEQI_ERROR_RETURN);
        return false;
    }
    public function gets($size)
    {
        if (is_resource($this->fp)) {
            return fgets($this->fp, $size);
        }
        $this->raiseError('socket is not connected', JIEQI_ERROR_RETURN);
        return false;
    }
    public function read($size)
    {
        if (is_resource($this->fp)) {
            return fread($this->fp, $size);
        }
        $this->raiseError('socket is not connected', JIEQI_ERROR_RETURN);
        return false;
    }
    public function write($data)
    {
        if (is_resource($this->fp)) {
            return fwrite($this->fp, $data);
        }
        $this->raiseError('socket is not connected', JIEQI_ERROR_RETURN);
        return false;
    }
    public function writeLine($data)
    {
        if (is_resource($this->fp)) {
            return $this->write($data . "\r\n");
        }
        $this->raiseError('socket is not connected', JIEQI_ERROR_RETURN);
        return false;
    }
    public function eof()
    {
        return is_resource($this->fp) && feof($this->fp);
    }
    public function readByte()
    {
        if (is_resource($this->fp)) {
            return ord($this->read(1));
        }
        $this->raiseError('socket is not connected', JIEQI_ERROR_RETURN);
        return false;
    }
    public function readWord()
    {
        if (is_resource($this->fp)) {
            $buf = $this->read(2);
            return ord($buf[0]) + (ord($buf[1]) << 8);
        }
        $this->raiseError('socket is not connected', JIEQI_ERROR_RETURN);
        return false;
    }
    public function readInt()
    {
        if (is_resource($this->fp)) {
            $buf = $this->read(4);
            return ord($buf[0]) + (ord($buf[1]) << 8) + (ord($buf[2]) << 16) + (ord($buf[3]) << 24);
        }
        $this->raiseError('socket is not connected', JIEQI_ERROR_RETURN);
        return false;
    }
    public function readString()
    {
        if (is_resource($this->fp)) {
            $string = '';
            while (($char = $this->read(1)) != '' . "\0" . '') {
                $string .= $char;
            }
            return $string;
        }
        $this->raiseError('socket is not connected', JIEQI_ERROR_RETURN);
        return false;
    }
    public function readIPAddress()
    {
        if (is_resource($this->fp)) {
            $buf = $this->read(4);
            return sprintf('%s.%s.%s.%s', ord($buf[0]), ord($buf[1]), ord($buf[2]), ord($buf[3]));
        }
        $this->raiseError('socket is not connected', JIEQI_ERROR_RETURN);
        return false;
    }
    public function readLine()
    {
        if (is_resource($this->fp)) {
            $line = '';
            $timeout = JIEQI_NOW_TIME + $this->timeout;
            while (!$this->eof() && (!$this->timeout || JIEQI_NOW_TIME < $timeout)) {
                $line .= $this->gets($this->lineLength);
                if (substr($line, -2) == "\r\n" || substr($line, -1) == "\n") {
                    return rtrim($line, "\r\n");
                }
            }
            return $line;
        }
        $this->raiseError('socket is not connected', JIEQI_ERROR_RETURN);
        return false;
    }
    public function readAll()
    {
        if (is_resource($this->fp)) {
            $data = '';
            while (!$this->eof()) {
                $data .= $this->read($this->lineLength);
            }
            return $data;
        }
        $this->raiseError('socket is not connected', JIEQI_ERROR_RETURN);
        return false;
    }
}