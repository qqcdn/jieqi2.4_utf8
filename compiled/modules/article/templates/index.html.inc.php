<?php
$GLOBALS['jieqiTset']['jieqi_page_template'] = 'themer.html';
@$this->_tpl_vars['jieqi_pagetitle'] = "{$this->_tpl_vars['articlename']}-{$this->_tpl_vars['author']}-{$this->_tpl_vars['sort']}-{$this->_tpl_vars['jieqi_sitename']}";
@$this->_tpl_vars['meta_keywords'] = "{$this->_tpl_vars['articlename']} {$this->_tpl_vars['author']} {$this->_tpl_vars['sort']} {$this->_tpl_vars['jieqi_sitename']}";
@$this->_tpl_vars['jieqi_articleread'] = '1';

?>