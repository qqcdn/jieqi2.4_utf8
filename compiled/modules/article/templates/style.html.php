<?php
echo '

<div class="main">
<div class="mainbody" id="apage">
<div class="headlink cf">
<div class="linkleft"><a href="'.$this->_tpl_vars['jieqi_main_url'].'/">'.$this->_tpl_vars['jieqi_sitename'].'</a> &gt; <a href="'.jieqi_geturl('article','articlelist','1',$this->_tpl_vars['sortid']).'">'.$this->_tpl_vars['sort'].'</a> &gt; <a href="'.jieqi_geturl('article','article',$this->_tpl_vars['articleid'],'info',$this->_tpl_vars['articlecode']).'">'.$this->_tpl_vars['articlename'].'</a></div>

<div class="linkright"><a href="'.$this->_tpl_vars['url_previous'].'">上一章</a> | <a href="'.$this->_tpl_vars['url_index'].'">返回书目</a> | <a href="'.$this->_tpl_vars['url_next'].'">下一章</a> | <a id="a_addbookcase" href="javascript:;" onclick="Ajax.Tip(\''.$this->_tpl_vars['jieqi_modules']['article']['url'].'/addbookcase.php?bid='.$this->_tpl_vars['articleid'].'&cid='.$this->_tpl_vars['chapterid'].'\', {method: \'POST\'});">加入书签</a> | <a id="a_uservote"  href="javascript:;" onclick="Ajax.Tip(\''.$this->_tpl_vars['jieqi_modules']['article']['url'].'/uservote.php?id='.$this->_tpl_vars['articleid'].'\', {method: \'POST\'});">推荐本书</a> | <a href="'.jieqi_geturl('article','article',$this->_tpl_vars['articleid'],'info',$this->_tpl_vars['articlecode']).'">返回书页</a></div>
</div>

<div class="fullbar"><script type="text/javascript" src="'.$this->_tpl_vars['jieqi_modules']['article']['url'].'/scripts/pagetop.js"></script></div>

<div class="atitle">'.$this->_tpl_vars['chaptername'].'</div>

<div class="ainfo">作者：<a href="'.jieqi_geturl('article','author',$this->_tpl_vars['authorid'],$this->_tpl_vars['author']).'" target="_blank">'.$this->_tpl_vars['author'].'</a> &nbsp;&nbsp;&nbsp;&nbsp; 更新：'.date('Y-m-d H:i',$this->_tpl_vars['chaptertime']).'</div>

<div id="acontent" class="acontent">'.$this->_tpl_vars['jieqi_content'].'</div>

<div class="footlink"><a href="'.$this->_tpl_vars['url_previous'].'" title="'.$this->_tpl_vars['previous_chaptername'].'">上一章</a> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <a href="'.$this->_tpl_vars['url_index'].'">返回目录</a> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <a href="'.$this->_tpl_vars['url_next'].'" title="'.$this->_tpl_vars['next_chaptername'].'">下一章</a></div>

<div class="fullbar"><script type="text/javascript" src="'.$this->_tpl_vars['jieqi_modules']['article']['url'].'/scripts/pagebottom.js"></script></div>
</div>
</div>
<script type="text/javascript" src="'.$this->_tpl_vars['jieqi_url'].'/scripts/json2.js"></script>
<script type="text/javascript" src="'.$this->_tpl_vars['jieqi_modules']['article']['url'].'/scripts/readlog.js"></script>';
?>