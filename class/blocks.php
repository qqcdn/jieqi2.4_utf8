<?php
jieqi_includedb();
class JieqiBlocks extends JieqiObjectData
{
    public function __construct()
    {
        $this->initVar('bid', JIEQI_TYPE_INT, 0, '序号', false, 8);
        $this->initVar('blockname', JIEQI_TYPE_TXTBOX, '', '区块名称', true, 50);
        $this->initVar('modname', JIEQI_TYPE_TXTBOX, '', '模块名称', true, 50);
        $this->initVar('filename', JIEQI_TYPE_TXTBOX, '', '文件名称', false, 50);
        $this->initVar('classname', JIEQI_TYPE_TXTBOX, '', '类名称', true, 50);
        $this->initVar('side', JIEQI_TYPE_INT, 0, '区块位置', false, 3);
        $this->initVar('title', JIEQI_TYPE_TXTAREA, '', '区块标题', false, NULL);
        $this->initVar('description', JIEQI_TYPE_TXTAREA, '', '区块描述', false, NULL);
        $this->initVar('content', JIEQI_TYPE_TXTAREA, '', '区块内容', false, NULL);
        $this->initVar('vars', JIEQI_TYPE_TXTBOX, '', '区块参数', false, 255);
        $this->initVar('template', JIEQI_TYPE_TXTBOX, '', '模板文件名称', false, 50);
        $this->initVar('cachetime', JIEQI_TYPE_INT, 0, '缓存时间', false, 11);
        $this->initVar('contenttype', JIEQI_TYPE_INT, 0, '内容类型', false, 3);
        $this->initVar('weight', JIEQI_TYPE_INT, 0, '排列顺序', false, 8);
        $this->initVar('showstatus', JIEQI_TYPE_INT, 0, '显示状态', false, 1);
        $this->initVar('custom', JIEQI_TYPE_INT, 0, '是否自定义区块', false, 1);
        $this->initVar('canedit', JIEQI_TYPE_INT, 0, '可否编辑', false, 1);
        $this->initVar('publish', JIEQI_TYPE_INT, 0, '是否激活', false, 1);
        $this->initVar('hasvars', JIEQI_TYPE_INT, 0, '是否支持参数', false, 1);
    }
}
class JieqiBlocksHandler extends JieqiObjectHandler
{
    public $sideary = array();
    public $contentary = array();
    public function __construct($db = '')
    {
        parent::__construct($db);
        $this->basename = 'blocks';
        $this->autoid = 'bid';
        $this->dbname = 'system_blocks';
        $this->sideary = array(JIEQI_SIDEBLOCK_CUSTOM => '自定义', JIEQI_SIDEBLOCK_LEFT => '左边', JIEQI_SIDEBLOCK_RIGHT => '右边', JIEQI_CENTERBLOCK_LEFT => '中左', JIEQI_CENTERBLOCK_RIGHT => '中右', JIEQI_CENTERBLOCK_TOP => '中上', JIEQI_CENTERBLOCK_MIDDLE => '中中', JIEQI_CENTERBLOCK_BOTTOM => '中下', JIEQI_TOPBLOCK_ALL => '顶部', JIEQI_BOTTOMBLOCK_ALL => '底部', JIEQI_NAVBLOCK_LEFT => '导航');
        $this->contentary = array(JIEQI_CONTENT_TXT => '纯文本', JIEQI_CONTENT_HTML => '纯HTML', JIEQI_CONTENT_JS => '纯JAVASCRIPT', JIEQI_CONTENT_MIX => 'HTML和SCRIPT混合', JIEQI_CONTENT_PHP => 'PHP代码');
    }
    public function getSideary()
    {
        return $this->sideary;
    }
    public function getSide($side)
    {
        if (isset($this->sideary[$side])) {
            return $this->sideary[$side];
        } else {
            return '隐藏';
        }
    }
    public function getShowlist($type)
    {
        $ret = array();
        foreach ($this->showary as $k => $v) {
            if (0 < ($type & $k)) {
                $ret[] = $k;
            }
        }
        return $ret;
    }
    public function getPublish($type)
    {
        if ($type == 3) {
            return '都显示';
        } else {
            if ($type == 1) {
                return '登陆前显示';
            } else {
                if ($type == 2) {
                    return '登陆后显示';
                } else {
                    return '不显示';
                }
            }
        }
    }
    public function getContentary($custom = true)
    {
        return $this->contentary;
    }
    public function getContenttype($type)
    {
        if (isset($this->contentary[$type])) {
            return $this->contentary[$type];
        } else {
            return '未知';
        }
    }
    public function saveContent($bid, $modname, $contenttype, &$content)
    {
        global $jieqiCache;
        $ret = false;
        if (0 < strlen($bid) && 0 < strlen($modname)) {
            $val = '';
            $fname = '';
            switch ($contenttype) {
                case JIEQI_CONTENT_TXT:
                    $val = jieqi_htmlstr($content);
                    $fname = '.html';
                    break;
                case JIEQI_CONTENT_HTML:
                    $val = $content;
                    $fname = '.html';
                    break;
                case JIEQI_CONTENT_JS:
                    $val = $content;
                    $fname = '.html';
                    break;
                case JIEQI_CONTENT_MIX:
                    $val = $content;
                    $fname = '.html';
                    break;
            }
            if (0 < strlen($fname)) {
                $cache_file = JIEQI_CACHE_PATH;
                if (0 < strlen($modname) && $modname != 'system') {
                    $cache_file .= '/modules/' . $modname;
                }
                if (is_numeric($bid)) {
                    $cache_file .= '/templates/blocks/block_custom' . $bid . $fname;
                } else {
                    if (substr($bid, -5) == '.html') {
                        $cache_file .= '/templates/blocks/' . $bid;
                    } else {
                        $cache_file .= '/templates/blocks/' . $bid . '.html';
                    }
                }
                if ($fname != '.php') {
                    $jieqiCache->set($cache_file, $val);
                } else {
                    jieqi_checkdir(dirname($cache_file), true);
                    jieqi_writefile($cache_file, $val);
                }
                $ret = true;
            }
        }
        return $ret;
    }
}
