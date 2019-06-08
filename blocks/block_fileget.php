<?php

class BlockSystemFileget extends JieqiBlock
{
    public $module = 'system';
    public $template = '';
    public $cachetime = JIEQI_CACHE_LIFETIME;
    public $exevars = array('fname' => '', 'cachetime' => 0, 'charset' => '');
    public function __construct(&$vars)
    {
        parent::__construct($vars);
        if (!empty($this->blockvars['vars'])) {
            $varary = explode(',', trim($this->blockvars['vars']));
            $arynum = count($varary);
            if (0 < $arynum) {
                $varary[0] = trim($varary[0]);
                if (!empty($varary[0])) {
                    if (preg_match('/^https?:\\/\\//is', $varary[0])) {
                        $this->exevars['fname'] = $varary[0];
                    } else {
                        $this->exevars['fname'] = substr($varary[0], 0, 1) == '/' ? JIEQI_URL . $varary[0] : JIEQI_URL . '/' . $varary[0];
                    }
                }
            }
            if (1 < $arynum) {
                $varary[1] = trim($varary[1]);
                if (is_numeric($varary[1])) {
                    $varary[1] = intval($varary[1]);
                    if (0 < $varary[1]) {
                        $this->blockvars['cachetime'] = $varary[1];
                    } else {
                        if ($varary[1] < 0) {
                            $this->blockvars['cachetime'] = 0;
                        }
                    }
                }
            }
            if (2 < $arynum) {
                $varary[2] = strtolower(trim($varary[2]));
                if (in_array($varary[2], array('gbk', 'gb2312', 'big5', 'utf-8'))) {
                    $this->exevars['charset'] = $varary[2];
                }
            }
        }
        $this->blockvars['cacheid'] = md5(serialize($this->exevars) . '|' . $this->blockvars['template']);
    }
    public function getContent()
    {
        global $jieqiTpl;
        global $jieqiCache;
        $cachefile = JIEQI_CACHE_PATH . '/templates/blocks/block_fileget/' . $this->blockvars['cacheid'] . '.html';
        $usecache = false;
        if (JIEQI_USE_CACHE && 0 < $this->blockvars['cachetime']) {
            $ret = $jieqiCache->get($cachefile, $this->blockvars['cachetime']);
            if ($ret !== false) {
                return $ret;
            }
        }
        return $this->updateContent(true);
    }
    public function updateContent($isreturn = false)
    {
        global $jieqiTpl;
        global $jieqiCache;
        if (empty($this->exevars['fname'])) {
            return '';
        }
        $bcontent = '';
        if (defined('PHP_VERSION') && '5.0.0' <= PHP_VERSION) {
            $context = array('http' => array('header' => 'User-Agent: Mozilla/5.0 (compatible; MSIE 10.0; Windows NT 6.1; WOW64; Trident/6.0)' . "\r\n" . ''));
            $stream_context = stream_context_create($context);
            $bcontent = file_get_contents($this->exevars['fname'], false, $stream_context);
        } else {
            $bcontent = file_get_contents($this->exevars['fname']);
        }
        if (!empty($this->exevars['charset']) && $jieqi_charset_map[$this->exevars['charset']] != $jieqi_charset_map[JIEQI_SYSTEM_CHARSET]) {
            $charset_convert_block = 'jieqi_' . $jieqi_charset_map[$this->exevars['charset']] . '2' . $jieqi_charset_map[JIEQI_SYSTEM_CHARSET];
            if (function_exists($charset_convert_block)) {
                $bcontent = $charset_convert_block($bcontent);
            }
        }
        if (!empty($this->blockvars['tlpfile'])) {
            $jieqiTpl->assign('block_main_content', $bcontent);
            $jieqiTpl->setCaching(0);
            $bcontent = $jieqiTpl->fetch($this->blockvars['tlpfile']);
        }
        if (JIEQI_USE_CACHE && 0 < $this->blockvars['cachetime']) {
            $jieqiCache->set(JIEQI_CACHE_PATH . '/templates/blocks/block_fileget/' . $this->blockvars['cacheid'] . '.html', $bcontent, $this->blockvars['cachetime']);
        }
        if ($isreturn) {
            return $bcontent;
        }
    }
}