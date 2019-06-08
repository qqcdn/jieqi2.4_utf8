<?php


class JieqiUnzip
{
    public $zipname = '';
    public $zip_fd = 0;
    public $error_code = 1;
    public $error_string = '';
    public $magic_quotes_status;
    public $block_size = 2048;
    public function __construct($zipname)
    {
        if (!function_exists('gzopen')) {
            exit('Abort ' . basename(__FILE__) . ' : Missing zlib extensions');
        }
        if (!is_file($zipname)) {
            return false;
        }
        $this->zipname = $zipname;
        $this->zip_fd = 0;
        $this->magic_quotes_status = -1;
        return true;
    }
    public function ExtractZip()
    {
        $v_result = 1;
        if (!$this->CheckFormat()) {
            return 0;
        }
        $v_options = array();
        $v_path = '';
        $v_remove_path = '';
        $v_remove_all_path = false;
        $v_size = func_num_args();
        $v_options[77006] = false;
        if (0 < $v_size) {
            $v_arg_list = func_get_args();
            if (is_integer($v_arg_list[0]) && 77000 < $v_arg_list[0]) {
                $v_result = $this->ParseOptions($v_arg_list, $v_size, $v_options, array(77001 => 'optional', 77003 => 'optional', 77004 => 'optional', 77002 => 'optional', 78001 => 'optional', 78002 => 'optional', 77005 => 'optional', 77008 => 'optional', 77010 => 'optional', 77011 => 'optional', 77009 => 'optional', 77006 => 'optional', 77015 => 'optional', 77016 => 'optional', 77017 => 'optional', 77019 => 'optional'));
                if ($v_result != 1) {
                    return 0;
                }
                if (isset($v_options[77001])) {
                    $v_path = $v_options[77001];
                }
                if (isset($v_options[77003])) {
                    $v_remove_path = $v_options[77003];
                }
                if (isset($v_options[77004])) {
                    $v_remove_all_path = $v_options[77004];
                }
                if (isset($v_options[77002])) {
                    if (0 < strlen($v_path) && substr($v_path, -1) != '/') {
                        $v_path .= '/';
                    }
                    $v_path .= $v_options[77002];
                }
            } else {
                $v_path = $v_arg_list[0];
                if ($v_size == 2) {
                    $v_remove_path = $v_arg_list[1];
                }
            }
        }
        $p_list = array();
        $v_result = $this->ExtractByRule($p_list, $v_path, $v_remove_path, $v_remove_all_path, $v_options);
        if ($v_result < 1) {
            unset($p_list);
            return 0;
        }
        return $p_list;
    }
    public function ParseOptions(&$p_options_list, $p_size, &$v_result_list, $v_requested_options = false)
    {
        $v_result = 1;
        $i = 0;
        while ($i < $p_size) {
            if (!isset($v_requested_options[$p_options_list[$i]])) {
                return NULL;
            }
            switch ($p_options_list[$i]) {
                case 77001:
                case 77003:
                case 77002:
                    if ($p_size <= $i + 1) {
                        return NULL;
                    }
                    $v_result_list[$p_options_list[$i]] = $this->SwitchWinPath($p_options_list[$i + 1], false);
                    $i++;
                    break;
                case 77019:
                    if ($p_size <= $i + 1) {
                        return NULL;
                    }
                    if (is_string($p_options_list[$i + 1]) && $p_options_list[$i + 1] != '') {
                        $v_result_list[$p_options_list[$i]] = $this->SwitchWinPath($p_options_list[$i + 1], false);
                        $i++;
                    }
                    break;
                case 77008:
                    if ($p_size <= $i + 1) {
                        return NULL;
                    }
                    if (is_string($p_options_list[$i + 1])) {
                        $v_result_list[$p_options_list[$i]][0] = $p_options_list[$i + 1];
                    } else {
                        if (is_array($p_options_list[$i + 1])) {
                            $v_result_list[$p_options_list[$i]] = $p_options_list[$i + 1];
                        } else {
                            return NULL;
                        }
                    }
                    $i++;
                    break;
                case 77010:
                case 77011:
                    if ($p_size <= $i + 1) {
                        return NULL;
                    }
                    if (is_string($p_options_list[$i + 1])) {
                        $v_result_list[$p_options_list[$i]] = $p_options_list[$i + 1];
                    } else {
                        return NULL;
                    }
                    $i++;
                    break;
                case 77012:
                case 77013:
                case 77014:
                    if ($p_size <= $i + 1) {
                        return NULL;
                    }
                    if (is_string($p_options_list[$i + 1])) {
                        $v_result_list[$p_options_list[$i]] = $p_options_list[$i + 1];
                    } else {
                        return NULL;
                    }
                    $i++;
                    break;
                case 77009:
                    if ($p_size <= $i + 1) {
                        return NULL;
                    }
                    $v_work_list = array();
                    if (is_string($p_options_list[$i + 1])) {
                        $p_options_list[$i + 1] = strtr($p_options_list[$i + 1], ' ', '');
                        $v_work_list = explode(',', $p_options_list[$i + 1]);
                    } else {
                        if (is_integer($p_options_list[$i + 1])) {
                            $v_work_list[0] = $p_options_list[$i + 1] . '-' . $p_options_list[$i + 1];
                        } else {
                            if (is_array($p_options_list[$i + 1])) {
                                $v_work_list = $p_options_list[$i + 1];
                            } else {
                                return NULL;
                            }
                        }
                    }
                    $v_sort_flag = false;
                    $v_sort_value = 0;
                    for ($j = 0; $j < sizeof($v_work_list); $j++) {
                        $v_item_list = explode('-', $v_work_list[$j]);
                        $v_size_item_list = sizeof($v_item_list);
                        if ($v_size_item_list == 1) {
                            $v_result_list[$p_options_list[$i]][$j]['start'] = $v_item_list[0];
                            $v_result_list[$p_options_list[$i]][$j]['end'] = $v_item_list[0];
                        } else {
                            if ($v_size_item_list == 2) {
                                $v_result_list[$p_options_list[$i]][$j]['start'] = $v_item_list[0];
                                $v_result_list[$p_options_list[$i]][$j]['end'] = $v_item_list[1];
                            } else {
                                return NULL;
                            }
                        }
                        if ($v_result_list[$p_options_list[$i]][$j]['start'] < $v_sort_value) {
                            $v_sort_flag = true;
                            return NULL;
                        }
                        $v_sort_value = $v_result_list[$p_options_list[$i]][$j]['start'];
                    }
                    if ($v_sort_flag) {
                    }
                    $i++;
                    break;
                case 77004:
                case 77006:
                case 77007:
                case 77015:
                case 77016:
                case 77017:
                    $v_result_list[$p_options_list[$i]] = true;
                    break;
                case 77005:
                    if ($p_size <= $i + 1) {
                        return NULL;
                    }
                    $v_result_list[$p_options_list[$i]] = $p_options_list[$i + 1];
                    $i++;
                    break;
                case 78001:
                case 78002:
                case 78003:
                case 78004:
                    if ($p_size <= $i + 1) {
                        return NULL;
                    }
                    $v_function_name = $p_options_list[$i + 1];
                    if (!function_exists($v_function_name)) {
                        return NULL;
                    }
                    $v_result_list[$p_options_list[$i]] = $v_function_name;
                    $i++;
                    break;
                default:
                    return NULL;
            }
            $i++;
        }
        if ($v_requested_options !== false) {
            for ($key = reset($v_requested_options); $key = key($v_requested_options); $key = next($v_requested_options)) {
                if ($v_requested_options[$key] == 'mandatory') {
                    if (!isset($v_result_list[$key])) {
                        return NULL;
                    }
                }
            }
        }
        return $v_result;
    }
    public function CheckFormat($p_level = 0)
    {
        $v_result = true;
        clearstatcache();
        if (!is_file($this->zipname)) {
            return false;
        }
        if (!is_readable($this->zipname)) {
            return false;
        }
        return $v_result;
    }
    public function ExtractByRule(&$p_file_list, $p_path, $p_remove_path, $p_remove_all_path, &$p_options)
    {
        $v_result = 1;
        $this->DisableMagicQuotes();
        if ($p_path == '' || substr($p_path, 0, 1) != '/' && substr($p_path, 0, 3) != '../' && substr($p_path, 1, 2) != ':/') {
            if ($p_path != './' && $p_path != '/') {
                while (substr($p_path, -1) == '/') {
                    $p_path = substr($p_path, 0, strlen($p_path) - 1);
                }
            }
        }
        if ($p_remove_path != '' && substr($p_remove_path, -1) != '/') {
            $p_remove_path .= '/';
        }
        $p_remove_path_size = strlen($p_remove_path);
        if (($v_result = $this->OpenFd('rb')) != 1) {
            $this->SwapBackMagicQuotes();
            return $v_result;
        }
        $v_central_dir = array();
        if (($v_result = $this->ReadEndCentralDir($v_central_dir)) != 1) {
            $this->CloseFd();
            $this->SwapBackMagicQuotes();
            return $v_result;
        }
        $v_pos_entry = $v_central_dir['offset'];
        $j_start = 0;
        $i = 0;
        for ($v_nb_extracted = 0; $i < $v_central_dir['entries']; $i++) {
            @rewind($this->zip_fd);
            if (@fseek($this->zip_fd, $v_pos_entry)) {
                $this->CloseFd();
                $this->SwapBackMagicQuotes();
            }
            $v_header = array();
            if (($v_result = $this->ReadCentralFileHeader($v_header)) != 1) {
                $this->CloseFd();
                $this->SwapBackMagicQuotes();
                return $v_result;
            }
            $v_header['index'] = $i;
            $v_pos_entry = ftell($this->zip_fd);
            $v_extract = false;
            if (isset($p_options[77008]) && $p_options[77008] != 0) {
                for ($j = 0; !$v_extract; $j++) {
                    if (substr($p_options[77008][$j], -1) == '/') {
                        if (strlen($p_options[77008][$j]) < strlen($v_header['stored_filename']) && substr($v_header['stored_filename'], 0, strlen($p_options[77008][$j])) == $p_options[77008][$j]) {
                            $v_extract = true;
                        }
                    } else {
                        if ($v_header['stored_filename'] == $p_options[77008][$j]) {
                            $v_extract = true;
                        }
                    }
                }
            } else {
                if (isset($p_options[77010]) && $p_options[77010] != '') {
                    if (preg_match('/' . $p_options[77010] . '/', $v_header['stored_filename'])) {
                        $v_extract = true;
                    }
                } else {
                    if (isset($p_options[77011]) && $p_options[77011] != '') {
                        if (preg_match($p_options[77011], $v_header['stored_filename'])) {
                            $v_extract = true;
                        }
                    } else {
                        if (isset($p_options[77009]) && $p_options[77009] != 0) {
                            for ($j = $j_start; !$v_extract; $j++) {
                                if ($p_options[77009][$j]['start'] <= $i && $i <= $p_options[77009][$j]['end']) {
                                    $v_extract = true;
                                }
                                if ($p_options[77009][$j]['end'] <= $i) {
                                    $j_start = $j + 1;
                                }
                                if ($i < $p_options[77009][$j]['start']) {
                                    break;
                                }
                            }
                        } else {
                            $v_extract = true;
                        }
                    }
                }
            }
            if ($v_extract && $v_header['compression'] != 8 && $v_header['compression'] != 0) {
                $v_header['status'] = 'unsupported_compression';
                if (isset($p_options[77017]) && $p_options[77017] === true) {
                    $this->SwapBackMagicQuotes();
                }
            }
            if ($v_extract && ($v_header['flag'] & 1) == 1) {
                $v_header['status'] = 'unsupported_encryption';
                if (isset($p_options[77017]) && $p_options[77017] === true) {
                    $this->SwapBackMagicQuotes();
                }
            }
            if ($v_extract && $v_header['status'] != 'ok') {
                $v_result = $this->ConvertHeader2FileInfo($v_header, $p_file_list[$v_nb_extracted++]);
                if ($v_result != 1) {
                    $this->CloseFd();
                    $this->SwapBackMagicQuotes();
                    return $v_result;
                }
                $v_extract = false;
            }
            if ($v_extract) {
                @rewind($this->zip_fd);
                if (@fseek($this->zip_fd, $v_header['offset'])) {
                    $this->CloseFd();
                    $this->SwapBackMagicQuotes();
                }
                if ($p_options[77006]) {
                    $v_result1 = $this->ExtractFileAsString($v_header, $v_string);
                    if ($v_result1 < 1) {
                        $this->CloseFd();
                        $this->SwapBackMagicQuotes();
                        return $v_result1;
                    }
                    if (($v_result = $this->ConvertHeader2FileInfo($v_header, $p_file_list[$v_nb_extracted])) != 1) {
                        $this->CloseFd();
                        $this->SwapBackMagicQuotes();
                        return $v_result;
                    }
                    $p_file_list[$v_nb_extracted]['content'] = $v_string;
                    $v_nb_extracted++;
                    if ($v_result1 == 2) {
                        break;
                    }
                } else {
                    if (isset($p_options[77015]) && $p_options[77015]) {
                        $v_result1 = $this->ExtractFileInOutput($v_header, $p_options);
                        if ($v_result1 < 1) {
                            $this->CloseFd();
                            $this->SwapBackMagicQuotes();
                            return $v_result1;
                        }
                        if (($v_result = $this->ConvertHeader2FileInfo($v_header, $p_file_list[$v_nb_extracted++])) != 1) {
                            $this->CloseFd();
                            $this->SwapBackMagicQuotes();
                            return $v_result;
                        }
                        if ($v_result1 == 2) {
                            break;
                        }
                    } else {
                        $v_result1 = $this->ExtractFile($v_header, $p_path, $p_remove_path, $p_remove_all_path, $p_options);
                        if ($v_result1 < 1) {
                            $this->CloseFd();
                            $this->SwapBackMagicQuotes();
                            return $v_result1;
                        }
                        if (($v_result = $this->ConvertHeader2FileInfo($v_header, $p_file_list[$v_nb_extracted++])) != 1) {
                            $this->CloseFd();
                            $this->SwapBackMagicQuotes();
                            return $v_result;
                        }
                        if ($v_result1 == 2) {
                            break;
                        }
                    }
                }
            }
        }
        $this->CloseFd();
        $this->SwapBackMagicQuotes();
        return $v_result;
    }
    public function DisableMagicQuotes()
    {
        $v_result = 1;
        if (!function_exists('get_magic_quotes_runtime') || !function_exists('set_magic_quotes_runtime')) {
            return $v_result;
        }
        if ($this->magic_quotes_status != -1) {
            return $v_result;
        }
        $this->magic_quotes_status = @get_magic_quotes_runtime();
        if ($this->magic_quotes_status == 1) {
            @set_magic_quotes_runtime(0);
        }
        return $v_result;
    }
    public function SwapBackMagicQuotes()
    {
        $v_result = 1;
        if (!function_exists('get_magic_quotes_runtime') || !function_exists('set_magic_quotes_runtime')) {
            return $v_result;
        }
        if ($this->magic_quotes_status != -1) {
            return $v_result;
        }
        if ($this->magic_quotes_status == 1) {
            @set_magic_quotes_runtime($this->magic_quotes_status);
        }
        return $v_result;
    }
    public function OpenFd($p_mode)
    {
        $v_result = 1;
        if ($this->zip_fd != 0) {
            return NULL;
        }
        if (($this->zip_fd = @fopen($this->zipname, $p_mode)) == 0) {
            return NULL;
        }
        return $v_result;
    }
    public function CloseFd()
    {
        $v_result = 1;
        if ($this->zip_fd != 0) {
            @fclose($this->zip_fd);
        }
        $this->zip_fd = 0;
        return $v_result;
    }
    public function ConvertHeader2FileInfo($p_header, &$p_info)
    {
        $v_result = 1;
        $p_info['filename'] = $p_header['filename'];
        $p_info['stored_filename'] = $p_header['stored_filename'];
        $p_info['size'] = $p_header['size'];
        $p_info['compressed_size'] = $p_header['compressed_size'];
        $p_info['mtime'] = $p_header['mtime'];
        $p_info['comment'] = $p_header['comment'];
        $p_info['folder'] = ($p_header['external'] & 16) == 16;
        $p_info['index'] = $p_header['index'];
        $p_info['status'] = $p_header['status'];
        return $v_result;
    }
    public function ExtractFile(&$p_entry, $p_path, $p_remove_path, $p_remove_all_path, &$p_options)
    {
        $v_result = 1;
        if (($v_result = $this->ReadFileHeader($v_header)) != 1) {
            return $v_result;
        }
        if ($this->CheckFileHeaders($v_header, $p_entry) != 1) {
        }
        if ($p_remove_all_path == true) {
            if (($p_entry['external'] & 16) == 16) {
                $p_entry['status'] = 'filtered';
                return $v_result;
            }
            $p_entry['filename'] = basename($p_entry['filename']);
        } else {
            if ($p_remove_path != '') {
                if ($this->PathInclusion($p_remove_path, $p_entry['filename']) == 2) {
                    $p_entry['status'] = 'filtered';
                    return $v_result;
                }
                $p_remove_path_size = strlen($p_remove_path);
                if (substr($p_entry['filename'], 0, $p_remove_path_size) == $p_remove_path) {
                    $p_entry['filename'] = substr($p_entry['filename'], $p_remove_path_size);
                }
            }
        }
        if ($p_path != '') {
            $p_entry['filename'] = $p_path . '/' . $p_entry['filename'];
        }
        if (isset($p_options[77019])) {
            $v_inclusion = $this->PathInclusion($p_options[77019], $p_entry['filename']);
            if ($v_inclusion == 0) {
                return NULL;
            }
        }
        if (isset($p_options[78001])) {
            $v_local_header = array();
            $this->ConvertHeader2FileInfo($p_entry, $v_local_header);
            eval('$v_result = ' . $p_options[78001] . '(78001, $v_local_header);');
            if ($v_result == 0) {
                $p_entry['status'] = 'skipped';
                $v_result = 1;
            }
            if ($v_result == 2) {
                $p_entry['status'] = 'aborted';
                $v_result = 2;
            }
            $p_entry['filename'] = $v_local_header['filename'];
        }
        if ($p_entry['status'] == 'ok') {
            if (file_exists($p_entry['filename'])) {
                if (is_dir($p_entry['filename'])) {
                    $p_entry['status'] = 'already_a_directory';
                    if (isset($p_options[77017]) && $p_options[77017] === true) {
                        return NULL;
                    }
                } else {
                    if (!is_writeable($p_entry['filename'])) {
                        $p_entry['status'] = 'write_protected';
                        if (isset($p_options[77017]) && $p_options[77017] === true) {
                            return NULL;
                        }
                    } else {
                        if ($p_entry['mtime'] < filemtime($p_entry['filename'])) {
                            if (isset($p_options[77016]) && $p_options[77016] === true) {
                            } else {
                                $p_entry['status'] = 'newer_exist';
                                if (isset($p_options[77017]) && $p_options[77017] === true) {
                                    return NULL;
                                }
                            }
                        }
                    }
                }
            } else {
                if (($p_entry['external'] & 16) == 16 || substr($p_entry['filename'], -1) == '/') {
                    $v_dir_to_check = $p_entry['filename'];
                } else {
                    if (!strstr($p_entry['filename'], '/')) {
                        $v_dir_to_check = '';
                    } else {
                        $v_dir_to_check = dirname($p_entry['filename']);
                    }
                }
                if (($v_result = $this->DirCheck($v_dir_to_check, ($p_entry['external'] & 16) == 16)) != 1) {
                    $p_entry['status'] = 'path_creation_fail';
                    $v_result = 1;
                }
            }
        }
        if ($p_entry['status'] == 'ok') {
            if (!($p_entry['external'] & 16) == 16) {
                if ($p_entry['compression'] == 0) {
                    if (($v_dest_file = @fopen($p_entry['filename'], 'wb')) == 0) {
                        $p_entry['status'] = 'write_error';
                        return $v_result;
                    }
                    $v_size = $p_entry['compressed_size'];
                    while ($v_size != 0) {
                        $v_read_size = $v_size < $this->block_size ? $v_size : $this->block_size;
                        $v_buffer = @fread($this->zip_fd, $v_read_size);
                        @fwrite($v_dest_file, $v_buffer, $v_read_size);
                        $v_size -= $v_read_size;
                    }
                    fclose($v_dest_file);
                    touch($p_entry['filename'], $p_entry['mtime']);
                } else {
                    if (($p_entry['flag'] & 1) == 1) {
                    } else {
                        $v_buffer = @fread($this->zip_fd, $p_entry['compressed_size']);
                    }
                    $v_file_content = @gzinflate($v_buffer);
                    unset($v_buffer);
                    if ($v_file_content === false) {
                        $p_entry['status'] = 'error';
                        return $v_result;
                    }
                    if (($v_dest_file = @fopen($p_entry['filename'], 'wb')) == 0) {
                        $p_entry['status'] = 'write_error';
                        return $v_result;
                    }
                    @fwrite($v_dest_file, $v_file_content, $p_entry['size']);
                    unset($v_file_content);
                    @fclose($v_dest_file);
                    @touch($p_entry['filename'], $p_entry['mtime']);
                }
                if (isset($p_options[77005])) {
                    @chmod($p_entry['filename'], $p_options[77005]);
                }
            }
        }
        if ($p_entry['status'] == 'aborted') {
            $p_entry['status'] = 'skipped';
        } else {
            if (isset($p_options[78002])) {
                $v_local_header = array();
                $this->ConvertHeader2FileInfo($p_entry, $v_local_header);
                eval('$v_result = ' . $p_options[78002] . '(78002, $v_local_header);');
                if ($v_result == 2) {
                    $v_result = 2;
                }
            }
        }
        return $v_result;
    }
    public function ExtractFileInOutput(&$p_entry, &$p_options)
    {
        $v_result = 1;
        if (($v_result = $this->ReadFileHeader($v_header)) != 1) {
            return $v_result;
        }
        if ($this->CheckFileHeaders($v_header, $p_entry) != 1) {
        }
        if (isset($p_options[78001])) {
            $v_local_header = array();
            $this->ConvertHeader2FileInfo($p_entry, $v_local_header);
            eval('$v_result = ' . $p_options[78001] . '(78001, $v_local_header);');
            if ($v_result == 0) {
                $p_entry['status'] = 'skipped';
                $v_result = 1;
            }
            if ($v_result == 2) {
                $p_entry['status'] = 'aborted';
                $v_result = 2;
            }
            $p_entry['filename'] = $v_local_header['filename'];
        }
        if ($p_entry['status'] == 'ok') {
            if (!($p_entry['external'] & 16) == 16) {
                if ($p_entry['compressed_size'] == $p_entry['size']) {
                    $v_buffer = @fread($this->zip_fd, $p_entry['compressed_size']);
                    echo $v_buffer;
                    unset($v_buffer);
                } else {
                    $v_buffer = @fread($this->zip_fd, $p_entry['compressed_size']);
                    $v_file_content = gzinflate($v_buffer);
                    unset($v_buffer);
                    echo $v_file_content;
                    unset($v_file_content);
                }
            }
        }
        if ($p_entry['status'] == 'aborted') {
            $p_entry['status'] = 'skipped';
        } else {
            if (isset($p_options[78002])) {
                $v_local_header = array();
                $this->ConvertHeader2FileInfo($p_entry, $v_local_header);
                eval('$v_result = ' . $p_options[78002] . '(78002, $v_local_header);');
                if ($v_result == 2) {
                    $v_result = 2;
                }
            }
        }
        return $v_result;
    }
    public function DirCheck($p_dir, $p_is_dir = false)
    {
        $v_result = 1;
        if ($p_is_dir && substr($p_dir, -1) == '/') {
            $p_dir = substr($p_dir, 0, strlen($p_dir) - 1);
        }
        if (is_dir($p_dir) || $p_dir == '') {
            return 1;
        }
        $p_parent_dir = dirname($p_dir);
        if ($p_parent_dir != $p_dir) {
            if ($p_parent_dir != '') {
                if (($v_result = $this->DirCheck($p_parent_dir)) != 1) {
                    return $v_result;
                }
            }
        }
        if (!@mkdir($p_dir, 511)) {
            return NULL;
        }
        return $v_result;
    }
    public function ExtractFileAsString(&$p_entry, &$p_string)
    {
        $v_result = 1;
        $v_header = array();
        if (($v_result = $this->ReadFileHeader($v_header)) != 1) {
            return $v_result;
        }
        if ($this->CheckFileHeaders($v_header, $p_entry) != 1) {
        }
        if (!($p_entry['external'] & 16) == 16) {
            if ($p_entry['compression'] == 0) {
                $p_string = @fread($this->zip_fd, $p_entry['compressed_size']);
            } else {
                $v_data = @fread($this->zip_fd, $p_entry['compressed_size']);
                if (($p_string = @gzinflate($v_data)) === false) {
                }
            }
        }
        return $v_result;
    }
    public function ReadFileHeader(&$p_header)
    {
        $v_result = 1;
        $v_binary_data = @fread($this->zip_fd, 4);
        $v_data = unpack('Vid', $v_binary_data);
        if ($v_data['id'] != 67324752) {
            return NULL;
        }
        $v_binary_data = fread($this->zip_fd, 26);
        if (strlen($v_binary_data) != 26) {
            $p_header['filename'] = '';
            $p_header['status'] = 'invalid_header';
            return NULL;
        }
        $v_data = unpack('vversion/vflag/vcompression/vmtime/vmdate/Vcrc/Vcompressed_size/Vsize/vfilename_len/vextra_len', $v_binary_data);
        $p_header['filename'] = fread($this->zip_fd, $v_data['filename_len']);
        if ($v_data['extra_len'] != 0) {
            $p_header['extra'] = fread($this->zip_fd, $v_data['extra_len']);
        } else {
            $p_header['extra'] = '';
        }
        $p_header['version_extracted'] = $v_data['version'];
        $p_header['compression'] = $v_data['compression'];
        $p_header['size'] = $v_data['size'];
        $p_header['compressed_size'] = $v_data['compressed_size'];
        $p_header['crc'] = $v_data['crc'];
        $p_header['flag'] = $v_data['flag'];
        $p_header['filename_len'] = $v_data['filename_len'];
        $p_header['mdate'] = $v_data['mdate'];
        $p_header['mtime'] = $v_data['mtime'];
        if ($p_header['mdate'] && $p_header['mtime']) {
            $v_hour = ($p_header['mtime'] & 63488) >> 11;
            $v_minute = ($p_header['mtime'] & 2016) >> 5;
            $v_seconde = ($p_header['mtime'] & 31) * 2;
            $v_year = (($p_header['mdate'] & 65024) >> 9) + 1980;
            $v_month = ($p_header['mdate'] & 480) >> 5;
            $v_day = $p_header['mdate'] & 31;
            $p_header['mtime'] = mktime($v_hour, $v_minute, $v_seconde, $v_month, $v_day, $v_year);
        } else {
            $p_header['mtime'] = time();
        }
        $p_header['stored_filename'] = $p_header['filename'];
        $p_header['status'] = 'ok';
        return $v_result;
    }
    public function CheckFileHeaders(&$p_local_header, &$p_central_header)
    {
        $v_result = 1;
        if ($p_local_header['filename'] != $p_central_header['filename']) {
        }
        if ($p_local_header['version_extracted'] != $p_central_header['version_extracted']) {
        }
        if ($p_local_header['flag'] != $p_central_header['flag']) {
        }
        if ($p_local_header['compression'] != $p_central_header['compression']) {
        }
        if ($p_local_header['mtime'] != $p_central_header['mtime']) {
        }
        if ($p_local_header['filename_len'] != $p_central_header['filename_len']) {
        }
        if (($p_local_header['flag'] & 8) == 8) {
            $p_local_header['size'] = $p_central_header['size'];
            $p_local_header['compressed_size'] = $p_central_header['compressed_size'];
            $p_local_header['crc'] = $p_central_header['crc'];
        }
        return $v_result;
    }
    public function ReadCentralFileHeader(&$p_header)
    {
        $v_result = 1;
        $v_binary_data = @fread($this->zip_fd, 4);
        $v_data = unpack('Vid', $v_binary_data);
        if ($v_data['id'] != 33639248) {
            return NULL;
        }
        $v_binary_data = fread($this->zip_fd, 42);
        if (strlen($v_binary_data) != 42) {
            $p_header['filename'] = '';
            $p_header['status'] = 'invalid_header';
            return NULL;
        }
        $p_header = unpack('vversion/vversion_extracted/vflag/vcompression/vmtime/vmdate/Vcrc/Vcompressed_size/Vsize/vfilename_len/vextra_len/vcomment_len/vdisk/vinternal/Vexternal/Voffset', $v_binary_data);
        if ($p_header['filename_len'] != 0) {
            $p_header['filename'] = fread($this->zip_fd, $p_header['filename_len']);
        } else {
            $p_header['filename'] = '';
        }
        if ($p_header['extra_len'] != 0) {
            $p_header['extra'] = fread($this->zip_fd, $p_header['extra_len']);
        } else {
            $p_header['extra'] = '';
        }
        if ($p_header['comment_len'] != 0) {
            $p_header['comment'] = fread($this->zip_fd, $p_header['comment_len']);
        } else {
            $p_header['comment'] = '';
        }
        if ($p_header['mdate'] && $p_header['mtime']) {
            $v_hour = ($p_header['mtime'] & 63488) >> 11;
            $v_minute = ($p_header['mtime'] & 2016) >> 5;
            $v_seconde = ($p_header['mtime'] & 31) * 2;
            $v_year = (($p_header['mdate'] & 65024) >> 9) + 1980;
            $v_month = ($p_header['mdate'] & 480) >> 5;
            $v_day = $p_header['mdate'] & 31;
            $p_header['mtime'] = mktime($v_hour, $v_minute, $v_seconde, $v_month, $v_day, $v_year);
        } else {
            $p_header['mtime'] = time();
        }
        $p_header['stored_filename'] = $p_header['filename'];
        $p_header['status'] = 'ok';
        if (substr($p_header['filename'], -1) == '/') {
            $p_header['external'] = 16;
        }
        return $v_result;
    }
    public function ReadEndCentralDir(&$p_central_dir)
    {
        $v_result = 1;
        $v_size = filesize($this->zipname);
        @fseek($this->zip_fd, $v_size);
        if (@ftell($this->zip_fd) != $v_size) {
            return NULL;
        }
        $v_found = 0;
        if (26 < $v_size) {
            @fseek($this->zip_fd, $v_size - 22);
            if (($v_pos = @ftell($this->zip_fd)) != $v_size - 22) {
                return NULL;
            }
            $v_binary_data = @fread($this->zip_fd, 4);
            $v_data = @unpack('Vid', $v_binary_data);
            if ($v_data['id'] == 101010256) {
                $v_found = 1;
            }
            $v_pos = ftell($this->zip_fd);
        }
        if (!$v_found) {
            $v_maximum_size = 65557;
            if ($v_size < $v_maximum_size) {
                $v_maximum_size = $v_size;
            }
            @fseek($this->zip_fd, $v_size - $v_maximum_size);
            if (@ftell($this->zip_fd) != $v_size - $v_maximum_size) {
                return NULL;
            }
            $v_pos = ftell($this->zip_fd);
            $v_bytes = 0;
            while ($v_pos < $v_size) {
                $v_byte = @fread($this->zip_fd, 1);
                $v_bytes = $v_bytes << 8 | ord($v_byte);
                if ($v_bytes == 1347093766) {
                    $v_pos++;
                    break;
                }
                $v_pos++;
            }
            if ($v_pos == $v_size) {
                return NULL;
            }
        }
        $v_binary_data = fread($this->zip_fd, 18);
        if (strlen($v_binary_data) != 18) {
            return NULL;
        }
        $v_data = unpack('vdisk/vdisk_start/vdisk_entries/ventries/Vsize/Voffset/vcomment_size', $v_binary_data);
        if ($v_pos + $v_data['comment_size'] + 18 != $v_size) {
            if (0) {
                return NULL;
            }
        }
        if ($v_data['comment_size'] != 0) {
            $p_central_dir['comment'] = fread($this->zip_fd, $v_data['comment_size']);
        } else {
            $p_central_dir['comment'] = '';
        }
        $p_central_dir['entries'] = $v_data['entries'];
        $p_central_dir['disk_entries'] = $v_data['disk_entries'];
        $p_central_dir['offset'] = $v_data['offset'];
        $p_central_dir['size'] = $v_data['size'];
        $p_central_dir['disk'] = $v_data['disk'];
        $p_central_dir['disk_start'] = $v_data['disk_start'];
        return $v_result;
    }
    public function PathInclusion($p_dir, $p_path)
    {
        $v_result = 1;
        if ($p_dir == '.' || 2 <= strlen($p_dir) && substr($p_dir, 0, 2) == './') {
            $p_dir = $this->SwitchWinPath(getcwd(), false) . '/' . substr($p_dir, 1);
        }
        if ($p_path == '.' || 2 <= strlen($p_path) && substr($p_path, 0, 2) == './') {
            $p_path = $this->SwitchWinPath(getcwd(), false) . '/' . substr($p_path, 1);
        }
        $v_list_dir = explode('/', $p_dir);
        $v_list_dir_size = sizeof($v_list_dir);
        $v_list_path = explode('/', $p_path);
        $v_list_path_size = sizeof($v_list_path);
        $i = 0;
        $j = 0;
        while ($i < $v_list_dir_size && $j < $v_list_path_size && $v_result) {
            if ($v_list_dir[$i] == '') {
                $i++;
                continue;
            }
            if ($v_list_path[$j] == '') {
                $j++;
                continue;
            }
            if ($v_list_dir[$i] != $v_list_path[$j] && $v_list_dir[$i] != '' && $v_list_path[$j] != '') {
                $v_result = 0;
            }
            $i++;
            $j++;
        }
        if ($v_result) {
            while ($j < $v_list_path_size && $v_list_path[$j] == '') {
                $j++;
            }
            while ($i < $v_list_dir_size && $v_list_dir[$i] == '') {
                $i++;
            }
            if ($v_list_dir_size <= $i && $v_list_path_size <= $j) {
                $v_result = 2;
            } else {
                if ($i < $v_list_dir_size) {
                    $v_result = 0;
                }
            }
        }
        return $v_result;
    }
    public function SwitchWinPath($p_path, $p_remove_disk_letter = true)
    {
        if (stristr(php_uname(), 'windows')) {
            if ($p_remove_disk_letter && ($v_position = strpos($p_path, ':')) != false) {
                $p_path = substr($p_path, $v_position + 1);
            }
            if (0 < strpos($p_path, '\\') || substr($p_path, 0, 1) == '\\') {
                $p_path = strtr($p_path, '\\', '/');
            }
        }
        return $p_path;
    }
}