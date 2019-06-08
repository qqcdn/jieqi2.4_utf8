<?php

class VisitorInfo
{
    public static function getIp()
    {
        if (isset($_SERVER['HTTP_CLIENT_IP'])) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } else {
            if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
                $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
            } else {
                $ip = $_SERVER['REMOTE_ADDR'];
            }
        }
        $ip = trim($ip);
        if (!is_numeric(str_replace('.', '', $ip))) {
            $ip = '0.0.0.0';
        }
        return $ip;
    }
    public static function getIpLocation($ip = '')
    {
        include_once JIEQI_ROOT_PATH . '/include/ip2location.php';
        if (empty($ip)) {
            $ip = VisitorInfo::getIp();
        }
        if ($ip == '0.0.0.0') {
            return 'unknow';
        }
        $wry = new Ip2Location();
        $wry->qqwry($ip);
        $returnVal = $wry->Country . $wry->Local;
        return $returnVal;
    }
    public static function getUrl()
    {
        $server = substr(getenv('SERVER_SOFTWARE'), 0, 3);
        if ($server == 'Apa') {
            $wookie = $server;
            $url = getenv('REQUEST_URI');
        } else {
            if ($server == 'Mic' || $server == 'Aby') {
                $protocol = getenv('HTTPS') == 'off' ? 'http://' : 'https://';
                $query = getenv('QUERY_STRING') ? '?' . getenv('QUERY_STRING') : '';
                $url = $protocol . getenv('SERVER_NAME') . getenv('SCRIPT_NAME') . $query;
            } else {
                if ($server == 'Aby') {
                    $protocol = getenv('HTTPS') == 'on' ? 'https://' : 'http://';
                    $query = getenv('QUERY_STRING') ? '?' . getenv('QUERY_STRING') : '';
                    $url = $protocol . getenv('SERVER_NAME') . getenv('SCRIPT_NAME') . $query;
                } else {
                    $url = getenv('REQUEST_URI');
                }
            }
        }
        return $url;
    }
    public static function getBrowser()
    {
        $_SERVER =& $_SERVER;
        $Agent = $_SERVER['HTTP_USER_AGENT'];
        $browser = $browserver = '';
        $Browsers = array('Lynx', 'MOSAIC', 'AOL', 'Opera', 'JAVA', 'MacWeb', 'WebExplorer', 'OmniWeb');
        for ($i = 0; $i <= 7; $i++) {
            if (strpos($Agent, $Browsers[$i])) {
                $browser = $Browsers[$i];
            }
        }
        if (preg_match('/Mozilla/', $Agent)) {
            if (preg_match('/MSIE/', $Agent)) {
                preg_match('/MSIE (.*);/U', $Agent, $args);
                $browserver = $args[1];
                $browser = 'Internet Explorer';
            } else {
                if (preg_match('/Opera/', $Agent)) {
                    $temp = explode(')', $Agent);
                    $browserver = $temp[1];
                    $temp = explode(' ', $browserver);
                    $browserver = $temp[2];
                    $browser = 'Opera';
                } else {
                    $temp = explode('/', $Agent);
                    $browserver = $temp[1];
                    $temp = explode(' ', $browserver);
                    $browserver = $temp[0];
                    $browser = 'Netscape Navigator';
                }
            }
        }
        if ($browser != '') {
            $browseinfo = $browser . ' ' . $browserver;
        } else {
            $browseinfo = false;
        }
        return $browseinfo;
    }
    public static function getOS()
    {
        $_SERVER =& $_SERVER;
        $agent = $_SERVER['HTTP_USER_AGENT'];
        $os = false;
        if (preg_match('/win/i', $agent) && strpos($agent, '95')) {
            $os = 'Windows 95';
        } else {
            if (preg_match('/win 9x/i', $agent) && strpos($agent, '4.90')) {
                $os = 'Windows ME';
            } else {
                if (preg_match('/win/i', $agent) && preg_match('/98/i', $agent)) {
                    $os = 'Windows 98';
                } else {
                    if (preg_match('/win/i', $agent) && preg_match('/nt 5.1/i', $agent)) {
                        $os = 'Windows XP';
                    } else {
                        if (preg_match('/win/', $agent) && preg_match('/nt 5/i', $agent)) {
                            $os = 'Windows 2000';
                        } else {
                            if (preg_match('/win/i', $agent) && preg_match('/nt/i', $agent)) {
                                $os = 'Windows NT';
                            } else {
                                if (preg_match('/win/i', $agent) && preg_match('/32/i', $agent)) {
                                    $os = 'Windows 32';
                                } else {
                                    if (preg_match('/linux/i', $agent)) {
                                        $os = 'Linux';
                                    } else {
                                        if (preg_match('/unix/i', $agent)) {
                                            $os = 'Unix';
                                        } else {
                                            if (preg_match('/sun/i', $agent) && preg_match('/os/i', $agent)) {
                                                $os = 'SunOS';
                                            } else {
                                                if (preg_match('/ibm/i', $agent) && preg_match('/os/i', $agent)) {
                                                    $os = 'IBM OS/2';
                                                } else {
                                                    if (preg_match('/Mac/i', $agent) && preg_match('/PC/i', $agent)) {
                                                        $os = 'Macintosh';
                                                    } else {
                                                        if (preg_match('/PowerPC/i', $agent)) {
                                                            $os = 'PowerPC';
                                                        } else {
                                                            if (preg_match('/AIX/i', $agent)) {
                                                                $os = 'AIX';
                                                            } else {
                                                                if (preg_match('/HPUX/i', $agent)) {
                                                                    $os = 'HPUX';
                                                                } else {
                                                                    if (preg_match('/NetBSD/i', $agent)) {
                                                                        $os = 'NetBSD';
                                                                    } else {
                                                                        if (preg_match('/BSD/i', $agent)) {
                                                                            $os = 'BSD';
                                                                        } else {
                                                                            if (preg_match('/OSF1/i', $agent)) {
                                                                                $os = 'OSF1';
                                                                            } else {
                                                                                if (preg_match('/IRIX/i', $agent)) {
                                                                                    $os = 'IRIX';
                                                                                } else {
                                                                                    if (preg_match('/FreeBSD/i', $agent)) {
                                                                                        $os = 'FreeBSD';
                                                                                    } else {
                                                                                        if (preg_match('/teleport/i', $agent)) {
                                                                                            $os = 'teleport';
                                                                                        } else {
                                                                                            if (preg_match('/flashget/i', $agent)) {
                                                                                                $os = 'flashget';
                                                                                            } else {
                                                                                                if (preg_match('/webzip/i', $agent)) {
                                                                                                    $os = 'webzip';
                                                                                                } else {
                                                                                                    if (preg_match('/offline/i', $agent)) {
                                                                                                        $os = 'offline';
                                                                                                    } else {
                                                                                                        $os = 'Unknown';
                                                                                                    }
                                                                                                }
                                                                                            }
                                                                                        }
                                                                                    }
                                                                                }
                                                                            }
                                                                        }
                                                                    }
                                                                }
                                                            }
                                                        }
                                                    }
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
        return $os;
    }
    public static function getFromUrl()
    {
        if (!empty($_REQUEST['fromurl'])) {
            $fromUrl = $_REQUEST['fromurl'];
        } else {
            if ($_SERVER['HTTP_REFERER'] != '') {
                $fromUrl = $_SERVER['HTTP_REFERER'];
            } else {
                $fromUrl = $_SERVER['REQUEST_URI'];
            }
        }
        return $fromUrl;
    }
}