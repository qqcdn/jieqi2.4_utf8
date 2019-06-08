<?php

class JieqiZip
{
    public $zip_fd;
    public $max_block_file = 1048576;
    public $read_block_size = 2048;
    public $filelist = array();
    public $filedata = array();
    public $headerlist = array();
    public $comment = '';
    public function addFile($fname)
    {
        if (is_dir($fname)) {
            if ($fname != '.' && $fname != '..') {
                $this->filelist[] = $fname;
            }
            if ($fname != '.') {
                $dir = $fname . '/';
            } else {
                $dir = '';
            }
            $handle = opendir($fname);
            while ($handle !== false && ($file = readdir($handle)) !== false) {
                if ($file != '.' && $file != '..') {
                    if (is_dir($dir . $file)) {
                        $this->addfile($dir . $file);
                    } else {
                        $this->filelist[] = $dir . $file;
                    }
                }
            }
            closedir($handle);
        } else {
            if (is_file($fname)) {
                $this->filelist[] = $fname;
            }
        }
    }
    public function addData($fname, $fdata)
    {
        $this->filedata[$fname] = $fdata;
    }
    public function setComment($comment = '')
    {
        $this->comment = $comment;
    }
    public function makezip($zipfile)
    {
        if (!function_exists('gzopen')) {
            return false;
        }
        if (empty($this->filelist) && empty($this->filedata)) {
            return false;
        }
        $this->zip_fd = @fopen($zipfile, 'wb');
        if (!$this->zip_fd) {
            return false;
        }
        @flock($this->zip_fd, LOCK_EX);
        $v_header_list = array();
        $p_result_list = array();
        $v_header = array();
        $v_nb = count($v_header_list);
        $filenum = count($this->filelist);
        $stored_files = array();
        $root_path = '';
        $prevary = array();
        $curary = array();
        $prenum = 0;
        $curnum = 0;
        for ($i = 0; $i < $filenum; $i++) {
            $this->filelist[$i] = $this->translatewinpath($this->filelist[$i], false);
            $stored_files[$i] = $this->pathreduction($this->filelist[$i]);
            if ($i == 0) {
                if (is_file($this->filelist[$i])) {
                    $root_path = dirname($stored_files[$i]);
                } else {
                    if (is_dir($this->filelist[$i])) {
                        $root_path = $stored_files[$i];
                    }
                }
            } else {
                if (0 < $i && $root_path != '') {
                    $prevary = explode('/', $root_path);
                    $prenum = count($prevary);
                    $curary = explode('/', $stored_files[$i]);
                    $curnum = count($curary);
                    $j = 0;
                    $root_path = '';
                    while ($j < $curnum && $j < $prenum && $curary[$j] == $prevary[$j]) {
                        if ($root_path != '') {
                            $root_path .= '/';
                        }
                        $root_path .= $curary[$j];
                        $j++;
                    }
                }
            }
        }
        $rootlen = strlen($root_path);
        for ($i = 0; $i < $filenum; $i++) {
            if ($this->filelist[$i] == '') {
                continue;
            }
            if (!file_exists($this->filelist[$i])) {
                continue;
            }
            if (0 < $rootlen && substr($stored_files[$i], 0, $rootlen) == $root_path) {
                $stored_files[$i] = substr($stored_files[$i], $rootlen);
                if (substr($stored_files[$i], 0, 1) == '/') {
                    $stored_files[$i] = substr($stored_files[$i], 1);
                }
            }
            if ($stored_files[$i] == '') {
                continue;
            }
            $this->addfile2zip($this->filelist[$i], $stored_files[$i], $v_header);
            $v_header_list[$v_nb++] = $v_header;
        }
        foreach ($this->filedata as $fname => $fdata) {
            $fname = $this->translatewinpath($fname, false);
            $fname = $this->pathreduction($fname);
            if ($fname == '') {
                continue;
            }
            $this->adddata2zip($fname, $fdata, $v_header);
            $v_header_list[$v_nb++] = $v_header;
        }
        $v_offset = @ftell($this->zip_fd);
        $filenum = count($v_header_list);
        $v_count = 0;
        for ($i = 0; $i < $filenum; $i++) {
            if ($v_header_list[$i]['status'] == 'ok') {
                $this->writecentralfileheader($v_header_list[$i]);
                $v_count++;
            }
            $this->header2fileinfo($v_header_list[$i], $p_result_list[$i]);
        }
        $v_comment = $this->comment;
        $v_size = @ftell($this->zip_fd) - $v_offset;
        $this->writecentralheader($v_count, $v_size, $v_offset, $v_comment);
        @flock($this->zip_fd, LOCK_UN);
        @fclose($this->zip_fd);
        $this->zip_fd = 0;
        return true;
    }
    public function zipstart($zipfile)
    {
        if (!function_exists('gzopen')) {
            return false;
        }
        $this->zip_fd = @fopen($zipfile, 'wb');
        if (!$this->zip_fd) {
            return false;
        }
        @flock($this->zip_fd, LOCK_EX);
        $this->headerlist = array();
        return true;
    }
    public function zipadd($fname, $fdata)
    {
        $fname = $this->translatewinpath($fname, false);
        $fname = $this->pathreduction($fname);
        if ($fname == '') {
            return false;
        }
        $this->adddata2zip($fname, $fdata, $v_header);
        $this->headerlist[] = $v_header;
        return true;
    }
    public function zipend()
    {
        $v_offset = @ftell($this->zip_fd);
        $filenum = count($this->headerlist);
        $v_count = 0;
        for ($i = 0; $i < $filenum; $i++) {
            if ($this->headerlist[$i]['status'] == 'ok') {
                $this->writecentralfileheader($this->headerlist[$i]);
                $v_count++;
            }
            $this->header2fileinfo($this->headerlist[$i], $p_result_list[$i]);
        }
        $v_comment = $this->comment;
        $v_size = @ftell($this->zip_fd) - $v_offset;
        $this->writecentralheader($v_count, $v_size, $v_offset, $v_comment);
        @flock($this->zip_fd, LOCK_UN);
        @fclose($this->zip_fd);
        $this->zip_fd = 0;
        $this->headerlist = array();
        return true;
    }
    public function writefileheader(&$p_header)
    {
        $p_header['offset'] = ftell($this->zip_fd);
        $v_date = getdate($p_header['mtime']);
        $v_mtime = ($v_date['hours'] << 11) + ($v_date['minutes'] << 5) + $v_date['seconds'] / 2;
        $v_mdate = ($v_date['year'] - 1980 << 9) + ($v_date['mon'] << 5) + $v_date['mday'];
        $v_binary_data = pack('VvvvvvVVVvv', 67324752, $p_header['version_extracted'], $p_header['flag'], $p_header['compression'], $v_mtime, $v_mdate, $p_header['crc'], $p_header['compressed_size'], $p_header['size'], strlen($p_header['stored_filename']), $p_header['extra_len']);
        fputs($this->zip_fd, $v_binary_data, 30);
        if (strlen($p_header['stored_filename']) != 0) {
            fputs($this->zip_fd, $p_header['stored_filename'], strlen($p_header['stored_filename']));
        }
        if ($p_header['extra_len'] != 0) {
            fputs($this->zip_fd, $p_header['extra'], $p_header['extra_len']);
        }
    }
    public function header2fileinfo($p_header, &$p_info)
    {
        $p_info['filename'] = $p_header['filename'];
        $p_info['stored_filename'] = $p_header['stored_filename'];
        $p_info['size'] = $p_header['size'];
        $p_info['compressed_size'] = $p_header['compressed_size'];
        $p_info['mtime'] = $p_header['mtime'];
        $p_info['comment'] = $p_header['comment'];
        $p_info['folder'] = ($p_header['external'] & 16) == 16;
        $p_info['index'] = $p_header['index'];
        $p_info['status'] = $p_header['status'];
    }
    public function writecentralheader($p_nb_entries, $p_size, $p_offset, $p_comment)
    {
        $v_binary_data = pack('VvvvvVVv', 101010256, 0, 0, $p_nb_entries, $p_nb_entries, $p_size, $p_offset, strlen($p_comment));
        fputs($this->zip_fd, $v_binary_data, 22);
        if (strlen($p_comment) != 0) {
            fputs($this->zip_fd, $p_comment, strlen($p_comment));
        }
    }
    public function writecentralfileheader(&$p_header)
    {
        $v_date = getdate($p_header['mtime']);
        $v_mtime = ($v_date['hours'] << 11) + ($v_date['minutes'] << 5) + $v_date['seconds'] / 2;
        $v_mdate = ($v_date['year'] - 1980 << 9) + ($v_date['mon'] << 5) + $v_date['mday'];
        $v_binary_data = pack('VvvvvvvVVVvvvvvVV', 33639248, $p_header['version'], $p_header['version_extracted'], $p_header['flag'], $p_header['compression'], $v_mtime, $v_mdate, $p_header['crc'], $p_header['compressed_size'], $p_header['size'], strlen($p_header['stored_filename']), $p_header['extra_len'], $p_header['comment_len'], $p_header['disk'], $p_header['internal'], $p_header['external'], $p_header['offset']);
        fputs($this->zip_fd, $v_binary_data, 46);
        if (strlen($p_header['stored_filename']) != 0) {
            fputs($this->zip_fd, $p_header['stored_filename'], strlen($p_header['stored_filename']));
        }
        if ($p_header['extra_len'] != 0) {
            fputs($this->zip_fd, $p_header['extra'], $p_header['extra_len']);
        }
        if ($p_header['comment_len'] != 0) {
            fputs($this->zip_fd, $p_header['comment'], $p_header['comment_len']);
        }
    }
    public function translatewinpath($p_path, $p_remove_disk_letter = true)
    {
        if (stristr(php_uname(), 'win')) {
            if ($p_remove_disk_letter && ($v_position = strpos($p_path, ':')) != false) {
                $p_path = substr($p_path, $v_position + 1);
            }
            if (0 < strpos($p_path, '\\') || substr($p_path, 0, 1) == '\\') {
                $p_path = strtr($p_path, '\\', '/');
            }
        }
        return $p_path;
    }
    public function addfile2zip($p_filename, $stored_filename, &$p_header)
    {
        clearstatcache();
        $p_header['version'] = 20;
        $p_header['version_extracted'] = 10;
        $p_header['flag'] = 0;
        $p_header['compression'] = 0;
        $p_header['mtime'] = filemtime($p_filename);
        $p_header['crc'] = 0;
        $p_header['compressed_size'] = 0;
        $p_header['size'] = filesize($p_filename);
        $p_header['filename_len'] = strlen($p_filename);
        $p_header['extra_len'] = 0;
        $p_header['comment_len'] = 0;
        $p_header['disk'] = 0;
        $p_header['internal'] = 0;
        $p_header['external'] = is_file($p_filename) ? 0 : 16;
        $p_header['offset'] = 0;
        $p_header['filename'] = $p_filename;
        $p_header['stored_filename'] = $stored_filename;
        $p_header['extra'] = '';
        $p_header['comment'] = '';
        $p_header['status'] = 'ok';
        $p_header['index'] = -1;
        if ($p_header['stored_filename'] == '') {
            $p_header['status'] = 'filtered';
        }
        if (255 < strlen($p_header['stored_filename'])) {
            $p_header['status'] = 'filename_too_long';
        }
        if ($p_header['status'] == 'ok') {
            if (is_file($p_filename)) {
                $v_size = filesize($p_filename);
                if ($v_size < $this->max_block_file) {
                    if (($v_file = @fopen($p_filename, 'rb')) == 0) {
                        return false;
                    }
                    $v_content = @fread($v_file, $p_header['size']);
                    $p_header['crc'] = @crc32($v_content);
                    $v_content_compressed = @gzdeflate($v_content);
                    $p_header['compressed_size'] = strlen($v_content_compressed);
                    $p_header['compression'] = 8;
                    $this->writefileheader($p_header);
                    @fwrite($this->zip_fd, $v_content_compressed, $p_header['compressed_size']);
                    @fclose($v_file);
                } else {
                    $tmp_gzfile = tempnam('', '');
                    if (($v_file_compressed = @gzopen($tmp_gzfile, 'wb')) == 0) {
                        return false;
                    }
                    if (($v_file = @fopen($p_filename, 'rb')) == 0) {
                        return false;
                    }
                    while ($v_size != 0) {
                        $v_read_size = $v_size < $this->read_block_size ? $v_size : $this->read_block_size;
                        $v_buffer = fread($v_file, $v_read_size);
                        $v_binary_data = pack('a' . $v_read_size, $v_buffer);
                        @gzputs($v_file_compressed, $v_binary_data, $v_read_size);
                        $v_size -= $v_read_size;
                    }
                    @fclose($v_file);
                    @gzclose($v_file_compressed);
                    if (filesize($tmp_gzfile) < 18) {
                        @unlink($tmp_gzfile);
                        return false;
                    }
                    if (($v_file_compressed = @fopen($tmp_gzfile, 'rb')) == 0) {
                        @unlink($tmp_gzfile);
                        return false;
                    }
                    $v_binary_data = @fread($v_file_compressed, 10);
                    $v_data_header = unpack('a1id1/a1id2/a1cm/a1flag/Vmtime/a1xfl/a1os', $v_binary_data);
                    $v_data_header['os'] = bin2hex($v_data_header['os']);
                    @fseek($v_file_compressed, filesize($tmp_gzfile) - 8);
                    $v_binary_data = @fread($v_file_compressed, 8);
                    $v_data_footer = unpack('Vcrc/Vcompressed_size', $v_binary_data);
                    $p_header['compression'] = ord($v_data_header['cm']);
                    $p_header['crc'] = $v_data_footer['crc'];
                    $p_header['compressed_size'] = filesize($tmp_gzfile) - 18;
                    $this->writefileheader($p_header);
                    @fwrite($this->zip_fd, $v_content_compressed, $p_header['compressed_size']);
                    @rewind($v_file_compressed);
                    fseek($v_file_compressed, 10);
                    $v_size = $p_header['compressed_size'];
                    while ($v_size != 0) {
                        $v_read_size = $v_size < $this->read_block_size ? $v_size : $this->read_block_size;
                        $v_buffer = fread($v_file_compressed, $v_read_size);
                        $v_binary_data = pack('a' . $v_read_size, $v_buffer);
                        @fwrite($this->zip_fd, $v_binary_data, $v_read_size);
                        $v_size -= $v_read_size;
                    }
                    @fclose($v_file_compressed);
                    @unlink($tmp_gzfile);
                }
            } else {
                if (@substr($p_header['stored_filename'], -1) != '/') {
                    $p_header['stored_filename'] .= '/';
                }
                $p_header['size'] = 0;
                $p_header['external'] = 16;
                $this->writefileheader($p_header);
            }
        }
    }
    public function adddata2zip($fname, $fdata, &$p_header)
    {
        clearstatcache();
        $p_header['version'] = 20;
        $p_header['version_extracted'] = 10;
        $p_header['flag'] = 0;
        $p_header['compression'] = 0;
        $p_header['mtime'] = time();
        $p_header['crc'] = 0;
        $p_header['compressed_size'] = 0;
        $p_header['size'] = strlen($fdata);
        $p_header['filename_len'] = strlen($fname);
        $p_header['extra_len'] = 0;
        $p_header['comment_len'] = 0;
        $p_header['disk'] = 0;
        $p_header['internal'] = 0;
        $p_header['external'] = $fdata === false ? 16 : 0;
        $p_header['offset'] = 0;
        $p_header['filename'] = $fname;
        $p_header['stored_filename'] = $fname;
        $p_header['extra'] = '';
        $p_header['comment'] = '';
        $p_header['status'] = 'ok';
        $p_header['index'] = -1;
        if ($p_header['stored_filename'] == '') {
            $p_header['status'] = 'filtered';
        }
        if (255 < strlen($p_header['stored_filename'])) {
            $p_header['status'] = 'filename_too_long';
        }
        if ($p_header['status'] == 'ok') {
            if ($fdata !== false) {
                $p_header['crc'] = @crc32($fdata);
                $v_content_compressed = @gzdeflate($fdata);
                $p_header['compressed_size'] = strlen($v_content_compressed);
                $p_header['compression'] = 8;
                $this->writefileheader($p_header);
                @fwrite($this->zip_fd, $v_content_compressed, $p_header['compressed_size']);
            } else {
                if (@substr($p_header['stored_filename'], -1) != '/') {
                    $p_header['stored_filename'] .= '/';
                }
                $p_header['size'] = 0;
                $p_header['external'] = 16;
                $this->writefileheader($p_header);
            }
        }
    }
    public function pathreduction($p_dir)
    {
        $v_result = '';
        if ($p_dir != '') {
            $v_list = explode('/', $p_dir);
            $v_skip = 0;
            $v_listnum = count($v_list);
            for ($i = $v_listnum - 1; 0 <= $i; $i--) {
                if ($v_list[$i] == '.') {
                } else {
                    if ($v_list[$i] == '..') {
                        $v_skip++;
                    } else {
                        if ($v_list[$i] == '') {
                            if ($i == 0) {
                                $v_result = '/' . $v_result;
                                if (0 < $v_skip) {
                                    $v_result = $p_dir;
                                    $v_skip = 0;
                                }
                            } else {
                                if ($i == $v_listnum - 1) {
                                    $v_result = $v_list[$i];
                                }
                            }
                        } else {
                            if (0 < $v_skip) {
                                $v_skip--;
                            } else {
                                $v_result = $v_list[$i] . ($i != $v_listnum - 1 ? '/' . $v_result : '');
                            }
                        }
                    }
                }
            }
            if (0 < $v_skip) {
                while (0 < $v_skip) {
                    $v_result = '../' . $v_result;
                    $v_skip--;
                }
            }
        }
        return $v_result;
    }
}