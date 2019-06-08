<?php

class BlockSystemCustom extends JieqiBlock
{
    public $module = 'system';
    public function __construct(&$vars)
    {
        global $jieqiTpl;
        if ($vars['contenttype'] == JIEQI_CONTENT_PHP) {
            $this->cachetime = -1;
        }
        parent::__construct($vars);
        if (!empty($this->blockvars['filename']) && preg_match('/^\\w+$/', $this->blockvars['filename']) && empty($this->blockvars['template'])) {
            $this->blockvars['template'] = $this->blockvars['filename'] . '.html';
            $this->blockvars['filename'] = '';
        }
    }
    public function updateContent($isreturn = false)
    {
        global $jieqiTpl;
        global $jieqiBlockset;
        $ret = '';
        include_once JIEQI_ROOT_PATH . '/class/blocks.php';
        $blocks_handler = JieqiBlocksHandler::getInstance('JieqiBlocksHandler');
        if (!empty($this->blockvars['bid'])) {
            $block = $blocks_handler->get($this->blockvars['bid']);
            if (is_object($block)) {
                switch ($block->getVar('contenttype')) {
                    case JIEQI_CONTENT_TXT:
                        $ret = $block->getVar('content', 's');
                        break;
                    case JIEQI_CONTENT_HTML:
                        $ret = $block->getVar('content', 'n');
                        break;
                    case JIEQI_CONTENT_JS:
                        $ret = '<script type="text/javascript">' . $block->getVar('content', 'n') . '</script>';
                        break;
                    case JIEQI_CONTENT_MIX:
                        $ret = $block->getVar('content', 'n');
                        break;
                    case JIEQI_CONTENT_PHP:
                        break;
                }
                $blocks_handler->saveContent($block->getVar('bid'), $block->getVar('modname'), $block->getVar('contenttype'), $ret);
            } else {
                $ret = 'block not exists! (id:' . $this->blockvars['bid'] . ')';
            }
        } else {
            if (!empty($this->blockvars['template']) && preg_match('/^\\w+\\.html$/', $this->blockvars['template'])) {
                if (!empty($this->blockvars['filename']) && preg_match('/^\\w+$/', $this->blockvars['filename'])) {
                    $jieqiBlockset = array();
                    if ($this->blockvars['module'] == 'system') {
                        $file = JIEQI_ROOT_PATH . '/configs/' . $this->blockvars['filename'] . '.php';
                    } else {
                        $file = JIEQI_ROOT_PATH . '/configs/' . $this->blockvars['module'] . '/' . $this->blockvars['filename'] . '.php';
                    }
                    $file = @realpath($file);
                    if (preg_match('/\\.php$/i', $file)) {
                        if (defined('JIEQI_THEME_ROOTNEW') && is_file(str_replace(array('\\', JIEQI_ROOT_PATH), array('/', JIEQI_THEME_ROOTPATH), $file))) {
                            include str_replace(array('\\', JIEQI_ROOT_PATH), array('/', JIEQI_THEME_ROOTPATH), $file);
                        } else {
                            include $file;
                        }
                    }
                    if (!is_array($jieqiBlockset)) {
                        $jieqiBlockset = array();
                    }
                    $jieqi_blockset = jieqi_funtoarray('jieqi_htmlstr', $jieqiBlockset);
                    if (isset($jieqiTpl)) {
                        $jieqiTpl->assign_by_ref('jieqi_blockset', $jieqi_blockset);
                    }
                }
                $blockpath = $this->blockvars['module'] == 'system' ? JIEQI_ROOT_PATH : $GLOBALS['jieqiModules'][$this->blockvars['module']]['path'];
                $blockpath .= '/templates/blocks/' . $this->blockvars['template'];
                if (defined('JIEQI_THEME_ROOTNEW') && is_file(str_replace(JIEQI_ROOT_PATH, JIEQI_THEME_ROOTPATH, $blockpath))) {
                    $blockpath = str_replace(JIEQI_ROOT_PATH, JIEQI_THEME_ROOTPATH, $blockpath);
                }
                if (isset($jieqiTpl)) {
                    if (JIEQI_USE_CACHE && 0 < $this->blockvars['cachetime']) {
                        $jieqiTpl->setCaching(2);
                        $jieqiTpl->setCachType(0);
                    } else {
                        $jieqiTpl->setCaching(0);
                    }
                    $ret = $jieqiTpl->fetch($blockpath);
                } else {
                    $ret = jieqi_readfile($blockpath);
                }
                $blocks_handler->saveContent($this->blockvars['template'], $this->blockvars['module'], JIEQI_CONTENT_HTML, $ret);
            } else {
                $ret = 'empty block id!';
            }
        }
        if ($isreturn) {
            return $ret;
        }
    }
}