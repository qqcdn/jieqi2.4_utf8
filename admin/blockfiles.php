<?php

define('JIEQI_MODULE_NAME', 'system');
require_once '../global.php';
include_once JIEQI_ROOT_PATH . '/class/power.php';
$power_handler = JieqiPowerHandler::getInstance('JieqiPowerHandler');
$power_handler->getSavedVars('system');
jieqi_checkpower($jieqiPower['system']['adminblock'], $jieqiUsersStatus, $jieqiUsersGroup, false, true);
jieqi_loadlang('blocks', JIEQI_MODULE_NAME);
jieqi_getconfigs('system', 'blockfiles', 'jieqiBlockfiles');
include_once JIEQI_ROOT_PATH . '/admin/header.php';
if (!isset($_POST['act']) && isset($_GET['act']) && in_array($_GET['act'], array('files', 'blocks', 'edit'))) {
    $_POST['act'] = $_GET['act'];
}
if (empty($_POST['act'])) {
    $_POST['act'] = 'files';
}
switch ($_POST['act']) {
    case 'blocks':
        if (empty($_REQUEST['module']) || !preg_match('/^\\w+$/', $_REQUEST['module']) || empty($jieqiModules[$_REQUEST['module']]['publish']) || empty($_REQUEST['filename']) || !preg_match('/^\\w+$/', $_REQUEST['filename'])) {
            jieqi_printfail(LANG_ERROR_PARAMETER);
        }
        unset($jieqiBlocks);
        jieqi_getconfigs($_REQUEST['module'], $_REQUEST['filename'], 'jieqiBlocks');
        if (!isset($jieqiBlocks)) {
            jieqi_printfail($jieqiLang['system']['block_config_notexists']);
        }
        include_once JIEQI_ROOT_PATH . '/class/blocks.php';
        $blocks_handler = JieqiBlocksHandler::getInstance('JieqiBlocksHandler');
        foreach ($jieqiBlocks as $i => $value) {
            foreach ($jieqiBlocks[$i] as $k => $v) {
                $jieqiBlocks[$i][$k] = jieqi_htmlchars($v, ENT_QUOTES);
            }
            $jieqiBlocks[$i]['modname'] = $jieqiModules[$value['module']]['caption'];
            $jieqiBlocks[$i]['side'] = intval($jieqiBlocks[$i]['side']);
            $jieqiBlocks[$i]['sidename'] = $blocks_handler->getSide(intval($jieqiBlocks[$i]['side']));
            $jieqiBlocks[$i]['contenttype'] = intval($jieqiBlocks[$i]['contenttype']);
            $jieqiBlocks[$i]['showtype'] = intval($jieqiBlocks[$i]['showtype']);
            $jieqiBlocks[$i]['custom'] = intval($jieqiBlocks[$i]['custom']);
            $jieqiBlocks[$i]['publish'] = intval($jieqiBlocks[$i]['publish']);
            $jieqiBlocks[$i]['hasvars'] = intval($jieqiBlocks[$i]['hasvars']);
        }
        $jieqiTpl->assign_by_ref('blocks', $jieqiBlocks);
        $jieqiTpl->assign('module', jieqi_htmlstr($_REQUEST['module']));
        $jieqiTpl->assign('filename', jieqi_htmlstr($_REQUEST['filename']));
        $modname = isset($jieqiModules[$_REQUEST['module']]['caption']) ? $jieqiModules[$_REQUEST['module']]['caption'] : $_REQUEST['module'];
        $jieqiTpl->assign('modname', jieqi_htmlstr($modname));
        $blockfile = array();
        foreach ($jieqiBlockfiles as $k => $v) {
            if ($v['module'] == $_REQUEST['module'] && $v['filename'] == $_REQUEST['filename']) {
                $blockfile = $v;
                break;
            }
        }
        $blockfile = jieqi_funtoarray('jieqi_htmlstr', $blockfile);
        $jieqiTpl->assign_by_ref('blockfile', $blockfile);
        $jieqiTpl->setCaching(0);
        $jieqiTset['jieqi_contents_template'] = $jieqiModules['system']['path'] . '/templates/admin/blockblocks.html';
        include_once JIEQI_ROOT_PATH . '/admin/footer.php';
        break;
    case 'edit':
        if (empty($_REQUEST['module']) || !preg_match('/^\\w+$/', $_REQUEST['module']) || empty($_REQUEST['filename']) || !preg_match('/^\\w+$/', $_REQUEST['filename']) || !isset($_REQUEST['key']) || !is_numeric($_REQUEST['key'])) {
            jieqi_printfail(LANG_ERROR_PARAMETER);
        }
        unset($jieqiBlocks);
        jieqi_getconfigs($_REQUEST['module'], $_REQUEST['filename'], 'jieqiBlocks');
        if (!isset($jieqiBlocks)) {
            jieqi_printfail($jieqiLang['system']['block_config_notexists']);
        }
        if (!isset($jieqiBlocks[$_REQUEST['key']])) {
            jieqi_printfail($jieqiLang['system']['block_not_exists']);
        }
        $blockSet = $jieqiBlocks[$_REQUEST['key']];
        if (!empty($blockSet['filename']) && preg_match('/^\\w+$/', $blockSet['filename']) && empty($blockSet['template'])) {
            $blockSet['template'] = $blockSet['filename'] . '.html';
            $blockSet['filename'] = '';
        }
        include_once JIEQI_ROOT_PATH . '/class/blocks.php';
        $blocks_handler = JieqiBlocksHandler::getInstance('JieqiBlocksHandler');
        include_once JIEQI_ROOT_PATH . '/lib/html/formloader.php';
        if ($blockSet['custom'] == 1) {
            $blockSet['content'] = '';
            if (!empty($blockSet['bid'])) {
                $block = $blocks_handler->get(intval($blockSet['bid']));
                if (is_object($block)) {
                    $blockSet['content'] = $block->getVar('content', 'n');
                }
            }
            $blocks_form = new JieqiThemeForm($jieqiLang['system']['edit_custom_block'], 'blockedit', JIEQI_URL . '/admin/blockfiles.php?module=' . urlencode($_REQUEST['module']) . '&filename=' . urlencode($_REQUEST['filename']) . '&key=' . urlencode($_REQUEST['key']));
            $blocks_form->addElement(new JieqiFormText($jieqiLang['system']['table_blocks_blockname'], 'blockname', 60, 200, jieqi_htmlchars($blockSet['blockname'], ENT_QUOTES)), true);
            $modselect = new JieqiFormSelect($jieqiLang['system']['table_blocks_modname'], 'modname', jieqi_htmlchars($blockSet['module'], ENT_QUOTES));
            foreach ($jieqiModules as $k => $v) {
                $modselect->addOption($k, jieqi_htmlchars($v['caption'], ENT_QUOTES));
            }
            $blocks_form->addElement($modselect);
        } else {
            $criteria = new CriteriaCompo(new Criteria('modname', $blockSet['module']));
            $criteria->add(new Criteria('classname', $blockSet['classname']));
            $blocks_handler->queryObjects($criteria);
            $block = $blocks_handler->getObject();
            if (is_object($block)) {
                $blockSet['description'] = $block->getVar('description', 'n');
            }
            $blocks_form = new JieqiThemeForm($jieqiLang['system']['edit_system_block'], 'blockedit', JIEQI_URL . '/admin/blockfiles.php?module=' . urlencode($_REQUEST['module']) . '&filename=' . urlencode($_REQUEST['filename']) . '&key=' . urlencode($_REQUEST['key']));
            $blockfile = $blockSet['filename'] . '.php';
            $blocks_form->addElement(new JieqiFormLabel($jieqiLang['system']['table_blocks_filename'], jieqi_htmlstr($blockfile)));
            if (isset($jieqiModules[$blockSet['module']])) {
                $blocks_form->addElement(new JieqiFormLabel($jieqiLang['system']['table_blocks_modname'], jieqi_htmlstr($jieqiModules[$blockSet['module']]['caption'])));
            } else {
                $blocks_form->addElement(new JieqiFormLabel($jieqiLang['system']['table_blocks_modname'], LANG_UNKNOWN));
            }
            $blocks_form->addElement(new JieqiFormText($jieqiLang['system']['table_blocks_blockname'], 'blockname', 60, 200, jieqi_htmlchars($blockSet['blockname'], ENT_QUOTES)), true);
        }
        $sideary = $blocks_handler->getSideary();
        $sideselect = new JieqiFormSelect($jieqiLang['system']['table_blocks_side'], 'side', jieqi_htmlchars($blockSet['side'], ENT_QUOTES));
        $sideselect->addOptionArray($sideary);
        $blocks_form->addElement($sideselect);
        $showradio = new JieqiFormRadio($jieqiLang['system']['table_blocks_publish'], 'publish', jieqi_htmlchars($blockSet['publish'], ENT_QUOTES));
        $showradio->addOption(0, $jieqiLang['system']['block_show_no']);
        $showradio->addOption(1, $jieqiLang['system']['block_show_logout']);
        $showradio->addOption(2, $jieqiLang['system']['block_show_login']);
        $showradio->addOption(3, $jieqiLang['system']['block_show_both']);
        $blocks_form->addElement($showradio);
        $blocks_form->addElement(new JieqiFormTextArea($jieqiLang['system']['table_blocks_title'], 'title', jieqi_htmlchars($blockSet['title'], ENT_QUOTES), 3, 60));
        if ($blockSet['custom'] == 1) {
            $blocks_form->addElement(new JieqiFormLabel($jieqiLang['system']['table_blocks_contenttype'], 'HTML'));
        } else {
            $tmpary = $blocks_handler->getContentary();
            if (isset($tmpary[$blockSet['contenttype']])) {
                $blocks_form->addElement(new JieqiFormLabel($jieqiLang['system']['table_blocks_contenttype'], $tmpary[$blockSet['contenttype']]));
            } else {
                $blocks_form->addElement(new JieqiFormLabel($jieqiLang['system']['table_blocks_contenttype'], LANG_UNKNOWN));
            }
        }
        if ($blockSet['custom'] == 1) {
            if (!empty($blockSet['bid'])) {
                $blocks_form->addElement(new JieqiFormTextArea($jieqiLang['system']['table_blocks_content'], 'content', jieqi_htmlchars($blockSet['content'], ENT_QUOTES), 10, 60));
            }
            if (!empty($blockSet['filename']) && preg_match('/^\\w+$/', $blockSet['filename'])) {
                $jieqiBlockset = array();
                if ($blockSet['module'] == 'system') {
                    $file = JIEQI_ROOT_PATH . '/configs/' . $blockSet['filename'] . '.php';
                } else {
                    $file = JIEQI_ROOT_PATH . '/configs/' . $blockSet['module'] . '/' . $blockSet['filename'] . '.php';
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
                if (!empty($jieqiBlockset)) {
                    foreach ($jieqiBlockset as $k => $v) {
                        $tmpvar = 'set_' . $k;
                        if (1 < $v['lines']) {
                            ${$tmpvar} = new JieqiFormTextArea(jieqi_htmlstr($v['caption']), jieqi_htmlstr('set_' . $k), jieqi_htmlchars($v['value'], ENT_QUOTES), intval($v['lines']), 60);
                        } else {
                            ${$tmpvar} = new JieqiFormText(jieqi_htmlstr($v['caption']), jieqi_htmlstr('set_' . $k), 60, 500, jieqi_htmlchars($v['value'], ENT_QUOTES));
                        }
                        if (!empty($v['description'])) {
                            ${$tmpvar}->setDescription(jieqi_htmlstr($v['description']));
                        }
                        $blocks_form->addElement($tmpvar);
                    }
                    $blocks_form->addElement(new JieqiFormHidden('cacheupdate', '1'));
                }
            }
        } else {
            $blockdesc = trim($blockSet['description']);
            if (!empty($blockdesc)) {
                $blocks_form->addElement(new JieqiFormLabel($jieqiLang['system']['table_blocks_description'], $blockdesc));
            }
        }
        if ($blockSet['hasvars']) {
            $blocks_form->addElement(new JieqiFormTextArea($jieqiLang['system']['table_blocks_blockvars'], 'blockvars', jieqi_htmlchars($blockSet['vars'], ENT_QUOTES), 3, 60));
            $blocks_form->addElement(new JieqiFormText($jieqiLang['system']['block_template_file'], 'blocktemplate', 60, 200, jieqi_htmlchars($blockSet['template'], ENT_QUOTES)), true);
            if ($blockSet['hasvars'] == 2) {
                $blocks_form->addElement(new JieqiFormHidden('cacheupdate', '1'));
            }
        }
        $blocks_form->addElement(new JieqiFormHidden('act', 'update'));
        $blocks_form->addElement(new JieqiFormHidden(JIEQI_TOKEN_NAME, $_SESSION['jieqiUserToken']));
        $blocks_form->addElement(new JieqiFormButton('&nbsp;', 'submit', $jieqiLang['system']['save_block'], 'submit'));
        $jieqiTpl->setCaching(0);
        $jieqiTpl->assign('jieqi_contents', '<br />' . $blocks_form->render(JIEQI_FORM_MAX) . '<br />');
        include_once JIEQI_ROOT_PATH . '/admin/footer.php';
        break;
    case 'update':
        jieqi_checkpost();
        if (empty($_REQUEST['module']) || !preg_match('/^\\w+$/', $_REQUEST['module']) || empty($_REQUEST['filename']) || !preg_match('/^\\w+$/', $_REQUEST['filename']) || !isset($_REQUEST['key']) || !is_numeric($_REQUEST['key'])) {
            jieqi_printfail(LANG_ERROR_PARAMETER);
        }
        unset($jieqiBlocks);
        jieqi_getconfigs($_REQUEST['module'], $_REQUEST['filename'], 'jieqiBlocks');
        if (!isset($jieqiBlocks)) {
            jieqi_printfail($jieqiLang['system']['block_config_notexists']);
        }
        if (!isset($jieqiBlocks[$_REQUEST['key']])) {
            jieqi_printfail($jieqiLang['system']['block_not_exists']);
        }
        if (isset($_POST['modname']) && !preg_match('/^[\\w]*$/', $_POST['modname'])) {
            jieqi_printfail($jieqiLang['system']['block_modname_error']);
        }
        if (isset($_POST['blocktemplate']) && !preg_match('/^[\\w\\.]*$/', $_POST['blocktemplate'])) {
            jieqi_printfail($jieqiLang['system']['block_template_errorformat']);
        }
        $blockSet = $jieqiBlocks[$_REQUEST['key']];
        $blockSet['blockname'] = $_POST['blockname'];
        $blockSet['side'] = $_POST['side'];
        $blockSet['publish'] = $_POST['publish'];
        $blockSet['title'] = $_POST['title'];
        if ($blockSet['hasvars']) {
            $blockSet['vars'] = $_POST['blockvars'];
            $blockSet['template'] = $_POST['blocktemplate'];
        }
        if ($blockSet['custom'] == 1 && isset($_POST['content'])) {
            jieqi_includedb();
            $query = JieqiQueryHandler::getInstance('JieqiQueryHandler');
            $sql = 'UPDATE ' . jieqi_dbprefix('system_blocks') . ' SET blockname = \'' . jieqi_dbslashes($_POST['blockname']) . '\', side = ' . intval($_POST['side']) . ', title = \'' . jieqi_dbslashes($_POST['title']) . '\', content = \'' . jieqi_dbslashes($_POST['content']) . '\', publish = ' . intval($_POST['publish']) . ' WHERE bid = ' . intval($blockSet['bid']);
            $query->execute($sql);
            include_once JIEQI_ROOT_PATH . '/class/blocks.php';
            $blocks_handler = JieqiBlocksHandler::getInstance('JieqiBlocksHandler');
            $blocks_handler->saveContent($blockSet['bid'], $blockSet['module'], JIEQI_CONTENT_HTML, $_POST['content']);
        } else {
            if ($blockSet['custom'] == 1 && !empty($blockSet['filename']) && preg_match('/^\\w+$/', $blockSet['filename'])) {
                $jieqiBlockset = array();
                if ($blockSet['module'] == 'system') {
                    $file = JIEQI_ROOT_PATH . '/configs/' . $blockSet['filename'] . '.php';
                } else {
                    $file = JIEQI_ROOT_PATH . '/configs/' . $blockSet['module'] . '/' . $blockSet['filename'] . '.php';
                }
                $file = @realpath($file);
                if (preg_match('/\\.php$/i', $file)) {
                    if (defined('JIEQI_THEME_ROOTNEW') && is_file(str_replace(array('\\', JIEQI_ROOT_PATH), array('/', JIEQI_THEME_ROOTPATH), $file))) {
                        include str_replace(array('\\', JIEQI_ROOT_PATH), array('/', JIEQI_THEME_ROOTPATH), $file);
                    } else {
                        include $file;
                    }
                    if (!is_array($jieqiBlockset)) {
                        $jieqiBlockset = array();
                    }
                    if (!empty($jieqiBlockset)) {
                        foreach ($jieqiBlockset as $k => $v) {
                            if (isset($_POST['set_' . $k])) {
                                $jieqiBlockset[$k]['value'] = $_POST['set_' . $k];
                            }
                        }
                        $varstring = '<?php' . "\r\n" . '' . jieqi_extractvars('jieqiBlockset', $jieqiBlockset) . '' . "\r\n" . '?>';
                        jieqi_writefile($file, $varstring);
                    }
                }
            }
        }
        $jieqiBlocks[$_REQUEST['key']] = $blockSet;
        jieqi_setconfigs($_REQUEST['filename'], 'jieqiBlocks', $jieqiBlocks, $_REQUEST['module']);
        if (isset($_POST['cacheupdate']) && $_POST['cacheupdate'] == 1) {
            $modname = $blockSet['module'];
            $filename = $blockSet['filename'];
            if ($blockSet['custom'] == 1) {
                include_once JIEQI_ROOT_PATH . '/blocks/block_custom.php';
            } else {
                if (preg_match('/^\\w+$/is', $filename)) {
                    if ($modname == 'system') {
                        include JIEQI_ROOT_PATH . '/blocks/' . $filename . '.php';
                    } else {
                        include $jieqiModules[$modname]['path'] . '/blocks/' . $filename . '.php';
                    }
                }
            }
            $classname = $blockSet['classname'];
            include_once JIEQI_ROOT_PATH . '/lib/template/template.php';
            $jieqiTpl = JieqiTpl::getInstance();
            $cblock = new $classname($blockSet);
            $cblock->updateContent();
            unset($jieqiTpl);
            unset($cblock);
            unset($vars);
        }
        jieqi_jumppage(JIEQI_URL . '/admin/blockfiles.php?act=blocks&module=' . urlencode($_REQUEST['module']) . '&filename=' . urlencode($_REQUEST['filename']), LANG_DO_SUCCESS, $jieqiLang['system']['block_update_success']);
        break;
    case 'updatelist':
        jieqi_checkpost();
        if (empty($_REQUEST['module']) || !preg_match('/^\\w+$/', $_REQUEST['module']) || empty($_REQUEST['filename']) || !preg_match('/^\\w+$/', $_REQUEST['filename'])) {
            jieqi_printfail(LANG_ERROR_PARAMETER);
        }
        unset($jieqiBlocks);
        jieqi_getconfigs($_REQUEST['module'], $_REQUEST['filename'], 'jieqiBlocks');
        if (!isset($jieqiBlocks)) {
            jieqi_printfail($jieqiLang['system']['block_config_notexists']);
        }
        asort($_REQUEST['key']);
        $newBlocks = array();
        $kk = -1;
        foreach ($_REQUEST['key'] as $oldk => $newk) {
            $newk = intval($newk);
            if ($newk < 0) {
                jieqi_printfail($jieqiLang['system']['block_key_lowzero']);
            } else {
                if ($kk == $newk) {
                    jieqi_printfail(sprintf($jieqiLang['system']['block_key_notrepeat'], $newk));
                }
            }
            $kk = $newk;
            $newBlocks[intval($newk)] = $jieqiBlocks[$oldk];
            if (0 < $jieqiBlocks[$oldk]['hasvars'] && isset($_REQUEST['vars'][$oldk])) {
                $newBlocks[intval($newk)]['vars'] = $_REQUEST['vars'][$oldk];
            }
        }
        foreach ($newBlocks as $i => $value) {
            $newBlocks[$i]['side'] = intval($newBlocks[$i]['side']);
            $newBlocks[$i]['contenttype'] = intval($newBlocks[$i]['contenttype']);
            $newBlocks[$i]['showtype'] = intval($newBlocks[$i]['showtype']);
            $newBlocks[$i]['custom'] = intval($newBlocks[$i]['custom']);
            $newBlocks[$i]['publish'] = intval($newBlocks[$i]['publish']);
            $newBlocks[$i]['hasvars'] = intval($newBlocks[$i]['hasvars']);
        }
        jieqi_setconfigs($_REQUEST['filename'], 'jieqiBlocks', $newBlocks, $_REQUEST['module']);
        jieqi_jumppage(JIEQI_URL . '/admin/blockfiles.php?act=blocks&module=' . urlencode($_REQUEST['module']) . '&filename=' . urlencode($_REQUEST['filename']), LANG_DO_SUCCESS, $jieqiLang['system']['block_update_success']);
        break;
    case 'files':
    default:
        foreach ($jieqiBlockfiles as $k => $v) {
            $jieqiBlockfiles[$k]['modname'] = $jieqiModules[$v['module']]['caption'];
        }
        $blockfiles = jieqi_funtoarray('jieqi_htmlstr', $jieqiBlockfiles);
        $jieqiTpl->assign_by_ref('blockfiles', $blockfiles);
        $jieqiTpl->setCaching(0);
        $jieqiTset['jieqi_contents_template'] = $jieqiModules['system']['path'] . '/templates/admin/blockfiles.html';
        include_once JIEQI_ROOT_PATH . '/admin/footer.php';
        break;
}