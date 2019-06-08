<?php

class JieqiUmd
{
    public $bookinfo = array('id' => 0, 'title' => 'umd book', 'author' => 'author', 'year' => '0', 'month' => '0', 'date' => '0', 'sort' => 'default', 'publisher' => '', 'seller' => 'DIY_GENERATED', 'corver' => '');
    public $chapters = array();
    public $charset = 'GBK';
    public $umd_fd;
    public $chaptercount = 0;
    public $articlelen = 0;
    public $chaptitlelen = 0;
    public function __construct()
    {
        $this->bookinfo['year'] = date('Y');
        $this->bookinfo['month'] = date('n');
        $this->bookinfo['date'] = date('j');
    }
    public function setcharset($charset)
    {
        $this->charset = $charset;
    }
    public function setinfo($infoary = array())
    {
        foreach ($this->bookinfo as $k => $v) {
            if (isset($infoary[$k])) {
                $this->bookinfo[$k] = $infoary[$k];
            }
            if ($k != 'id' && $this->charset != 'UCS') {
                $this->bookinfo[$k] = iconv($this->charset, 'UCS-2LE//IGNORE', $this->bookinfo[$k]);
            }
        }
    }
    public function addchapter($title, $content)
    {
        if ($this->charset != 'UCS') {
            $title = iconv($this->charset, 'UCS-2LE//IGNORE', $title);
            $content = iconv($this->charset, 'UCS-2LE//IGNORE', str_replace("\r", '', $content));
        }
        $this->chapters[$this->chaptercount] = array('title' => $title, 'content' => $content);
        $this->chaptercount++;
        $this->chaptitlelen += strlen($title);
        $this->articlelen += strlen($content);
    }
    public function makeumd($umdfile = '')
    {
        $this->umd_fd = @fopen($umdfile, 'wb');
        if (!$this->umd_fd) {
            return false;
        }
        @flock($this->umd_fd, LOCK_EX);
        $data = '';
        $data .= chr(137) . chr(155) . chr(154) . chr(222);
        $data .= chr(35) . chr(1) . chr(0);
        $data .= chr(0) . chr(8);
        $data .= chr(1);
        $pgkeed = rand(1025, 32767);
        $data .= $this->umddechex($pgkeed, 2);
        $data .= $this->umdmakeinfo($this->bookinfo['title'], 2);
        $data .= $this->umdmakeinfo($this->bookinfo['author'], 3);
        $data .= $this->umdmakeinfo($this->bookinfo['year'], 4);
        $data .= $this->umdmakeinfo($this->bookinfo['month'], 5);
        $data .= $this->umdmakeinfo($this->bookinfo['date'], 6);
        $data .= $this->umdmakeinfo($this->bookinfo['sort'], 7);
        $data .= $this->umdmakeinfo($this->bookinfo['publisher'], 8);
        $data .= $this->umdmakeinfo($this->bookinfo['seller'], 9);
        fputs($this->umd_fd, $data, strlen($data));
        $data = '';
        $data .= chr(35) . chr(11) . chr(0) . chr(0) . chr(9);
        $data .= $this->umddechex($this->articlelen + $this->chaptercount * 2, 4);
        $data .= chr(35) . chr(131) . chr(0) . chr(1) . chr(9);
        $tmpnum = rand(12288, 16383);
        $data .= $this->umddechex($tmpnum, 4);
        $data .= chr(36);
        $data .= $this->umddechex($tmpnum, 4);
        $tmpnum = $this->chaptercount * 4 + 9;
        $data .= $this->umddechex($tmpnum, 4);
        $spoint = 0;
        foreach ($this->chapters as $i => $chapter) {
            $data .= $this->umddechex($spoint, 4);
            $spoint += strlen($chapter['content']) + 2;
        }
        $data .= chr(35) . chr(132) . chr(0) . chr(1) . chr(9);
        $tmpnum = rand(16384, 20479);
        $data .= $this->umddechex($tmpnum, 4);
        $data .= chr(36);
        $data .= $this->umddechex($tmpnum, 4);
        $tmpnum = 9 + $this->chaptitlelen + $this->chaptercount;
        $data .= $this->umddechex($tmpnum, 4);
        foreach ($this->chapters as $i => $chapter) {
            $tmpnum = strlen($chapter['title']);
            $data .= $this->umddechex($tmpnum, 1);
            $data .= $chapter['title'];
        }
        fputs($this->umd_fd, $data, strlen($data));
        $point = 0;
        $psize = 32768;
        $content = '';
        foreach ($this->chapters as $i => $chapter) {
            $content .= $chapter['content'] . chr(41) . chr(32);
        }
        $clen = strlen($content);
        $packnum = ceil($clen / $psize);
        $rnd1 = rand(0, $packnum - 1);
        $rand2 = rand(0, $packnum - 1);
        $rndary = array();
        for ($i = 0; $i < $packnum; $i++) {
            $data = '';
            $data .= chr(36);
            $rnd_content = rand(4026531841.0, 4294967294.0);
            $rndary[$i] = $rnd_content;
            $data .= $this->umddechex($rnd_content, 4);
            $tmpdata = substr($content, $point, $psize);
            $point += $psize;
            $tmpgz = gzcompress($tmpdata);
            $tmpnum = 9 + strlen($tmpgz);
            $data .= $this->umddechex($tmpnum, 4);
            $data .= $tmpgz;
            if ($i == $rnd1) {
                $data .= chr(35) . chr(241) . chr(0) . chr(0) . chr(21) . chr(0) . chr(0) . chr(0) . chr(0) . chr(0) . chr(0) . chr(0) . chr(0) . chr(0) . chr(0) . chr(0) . chr(0) . chr(0) . chr(0) . chr(0) . chr(0);
            }
            if ($i == $rnd2) {
                $data .= chr(35) . chr(10) . chr(0) . chr(0) . chr(9);
                $data .= $this->umddechex($this->bookinfo['id'] + 268435456, 4);
            }
            fputs($this->umd_fd, $data, strlen($data));
        }
        $data = '';
        $data .= chr(35) . chr(129) . chr(0) . chr(1) . chr(9);
        $tmpnum = rand(8192, 12287);
        $data .= $this->umddechex($tmpnum, 4);
        $data .= chr(36);
        $data .= $this->umddechex($tmpnum, 4);
        $tmpnum = 9 + $packnum * 4;
        $data .= $this->umddechex($tmpnum, 4);
        for ($i = 0; $i < $packnum; $i++) {
            $data .= $this->umddechex($rndary[$i], 4);
        }
        fputs($this->umd_fd, $data, strlen($data));
        $data = '';
        if (!empty($this->bookinfo['corver']) && is_file($this->bookinfo['corver'])) {
            $data .= chr(35) . chr(130) . chr(0) . chr(1) . chr(10) . chr(1);
            $tmpnum = rand(4096, 8191);
            $data .= $this->umddechex($tmpnum, 4);
            $data .= chr(36);
            $data .= $this->umddechex($tmpnum, 4);
            $corver_content = file_get_contents($this->bookinfo['corver']);
            $tmpnum = strlen($corver_content) + 9;
            $data .= $this->umddechex($tmpnum, 4);
            $data .= $corver_content;
            fputs($this->umd_fd, $data, strlen($data));
            $data = '';
        }
        $tmpnum1 = $this->articlelen + $this->chaptercount * 2;
        $tmpnum2 = rand(28672, 32767);
        $data .= chr(35) . chr(135) . chr(0) . chr(1) . chr(11) . chr(16) . chr(208);
        $data .= $this->umddechex($tmpnum2, 4);
        $data .= chr(36);
        $data .= $this->umddechex($tmpnum2, 4);
        $tmpnum = 17;
        $data .= $this->umddechex($tmpnum, 4);
        $tmpnum = 0;
        $data .= $this->umddechex($tmpnum, 4);
        $data .= $this->umddechex($tmpnum1, 4);
        $data .= chr(35) . chr(135) . chr(0) . chr(1) . chr(11) . chr(16) . chr(176);
        $data .= $this->umddechex($tmpnum2, 4);
        $data .= chr(36);
        $data .= $this->umddechex($tmpnum2, 4);
        $tmpnum = 17;
        $data .= $this->umddechex($tmpnum, 4);
        $tmpnum = 0;
        $data .= $this->umddechex($tmpnum, 4);
        $data .= $this->umddechex($tmpnum1, 4);
        $data .= chr(35) . chr(135) . chr(0) . chr(1) . chr(11) . chr(12) . chr(208);
        $data .= $this->umddechex($tmpnum2, 4);
        $data .= chr(36);
        $data .= $this->umddechex($tmpnum2, 4);
        $tmpnum = 17;
        $data .= $this->umddechex($tmpnum, 4);
        $tmpnum = 0;
        $data .= $this->umddechex($tmpnum, 4);
        $data .= $this->umddechex($tmpnum1, 4);
        $data .= chr(35) . chr(135) . chr(0) . chr(1) . chr(11) . chr(12) . chr(176);
        $data .= $this->umddechex($tmpnum2, 4);
        $data .= chr(36);
        $data .= $this->umddechex($tmpnum2, 4);
        $tmpnum = 17;
        $data .= $this->umddechex($tmpnum, 4);
        $tmpnum = 0;
        $data .= $this->umddechex($tmpnum, 4);
        $data .= $this->umddechex($tmpnum1, 4);
        $data .= chr(35) . chr(135) . chr(0) . chr(5) . chr(11) . chr(10) . chr(166);
        $data .= $this->umddechex($tmpnum2, 4);
        $data .= chr(36);
        $data .= $this->umddechex($tmpnum2, 4);
        $tmpnum = 17;
        $data .= $this->umddechex($tmpnum, 4);
        $tmpnum = 0;
        $data .= $this->umddechex($tmpnum, 4);
        $data .= $this->umddechex(floor($tmpnum1 / 2), 4);
        $data .= chr(35) . chr(12) . chr(0) . chr(1) . chr(9);
        $tmpnum = 4 + strlen($data) + ftell($this->umd_fd);
        $data .= $this->umddechex($tmpnum, 4);
        fputs($this->umd_fd, $data, strlen($data));
        $data = '';
        flock($this->umd_fd, LOCK_UN);
        fclose($this->umd_fd);
        chmod($umdfile, 511);
    }
    public function umdmakeinfo($instr, $order)
    {
        $retstr = chr(35) . chr($order) . chr(0) . chr(0);
        $retstr .= $this->umddechex(strlen($instr) + 5, 1);
        $retstr .= $instr;
        return $retstr;
    }
    public function umddechex($num, $bytes)
    {
        $retstr = '';
        $bytes = $bytes * 2;
        $tmpvar = substr(sprintf('%0' . $bytes . 's', dechex($num)), -$bytes);
        for ($i = 0; $i < $bytes; $i += 2) {
            $retstr = chr(hexdec(substr($tmpvar, $i, 2))) . $retstr;
        }
        return $retstr;
    }
}