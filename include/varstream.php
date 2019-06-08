<?php

class JieqiVarStream
{
    public $position;
    public $varname;
    public function stream_open($path, $mode, $options, &$opened_path)
    {
        $url = parse_url($path);
        $this->varname = $url['host'];
        $this->position = 0;
        return true;
    }
    public function stream_close()
    {
    }
    public function stream_read($count)
    {
        $ret = substr($GLOBALS[$this->varname], $this->position, $count);
        $this->position += strlen($ret);
        return $ret;
    }
    public function stream_write($data)
    {
        $left = substr($GLOBALS[$this->varname], 0, $this->position);
        $right = substr($GLOBALS[$this->varname], $this->position + strlen($data));
        $GLOBALS[$this->varname] = $left . $data . $right;
        $this->position += strlen($data);
        return strlen($data);
    }
    public function stream_tell()
    {
        return $this->position;
    }
    public function stream_eof()
    {
        return strlen($GLOBALS[$this->varname]) <= $this->position;
    }
    public function stream_stat()
    {
        return array('size' => strlen($GLOBALS[$this->varname]));
    }
    public function stream_flush()
    {
        return true;
    }
    public function stream_seek($offset, $whence)
    {
        switch ($whence) {
            case SEEK_SET:
                if ($offset < strlen($GLOBALS[$this->varname]) && 0 <= $offset) {
                    $this->position = $offset;
                    return true;
                } else {
                    return false;
                }
                break;
            case SEEK_CUR:
                if (0 <= $offset) {
                    $this->position += $offset;
                    return true;
                } else {
                    return false;
                }
                break;
            case SEEK_END:
                if (0 <= strlen($GLOBALS[$this->varname]) + $offset) {
                    $this->position = strlen($GLOBALS[$this->varname]) + $offset;
                    return true;
                } else {
                    return false;
                }
                break;
            default:
                return false;
        }
    }
}