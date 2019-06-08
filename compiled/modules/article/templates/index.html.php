<?php
echo '

<div class="main">
<div class="mainbody">
<div class="headlink cf">
<div class="linkleft"><a href="'.$this->_tpl_vars['jieqi_main_url'].'/">'.$this->_tpl_vars['jieqi_sitename'].'</a> &gt; <a href="'.jieqi_geturl('article','articlelist','1',$this->_tpl_vars['sortid']).'">'.$this->_tpl_vars['sort'].'</a> &gt; <a href="'.jieqi_geturl('article','article',$this->_tpl_vars['articleid'],'info',$this->_tpl_vars['articlecode']).'">'.$this->_tpl_vars['articlename'].'</a></div>

<div class="linkright"><a id="a_addbookcase" href="javascript:;" onclick="Ajax.Tip(\''.$this->_tpl_vars['jieqi_modules']['article']['url'].'/addbookcase.php?bid='.$this->_tpl_vars['articleid'].'\', {method: \'POST\'});">加入书架</a> | <a id="a_uservote"  href="javascript:;" onclick="Ajax.Tip(\''.$this->_tpl_vars['jieqi_modules']['article']['url'].'/uservote.php?id='.$this->_tpl_vars['articleid'].'\', {method: \'POST\'});">推荐本书</a> | <a href="'.jieqi_geturl('article','article',$this->_tpl_vars['articleid'],'info',$this->_tpl_vars['articlecode']).'">返回书页</a></div>
</div>

<div class="fullbar"><script type="text/javascript" src="'.$this->_tpl_vars['jieqi_modules']['article']['url'].'/scripts/indextop.js"></script></div>

<div class="atitle">'.$this->_tpl_vars['articlename'].'</div>

<div class="ainfo">作者：<a href="'.jieqi_geturl('article','author',$this->_tpl_vars['authorid'],$this->_tpl_vars['author']).'" target="_blank">'.$this->_tpl_vars['author'].'</a></div>

<dl class="index">
';
if (empty($this->_tpl_vars['chapterrows'])) $this->_tpl_vars['chapterrows'] = array();
elseif (!is_array($this->_tpl_vars['chapterrows'])) $this->_tpl_vars['chapterrows'] = (array)$this->_tpl_vars['chapterrows'];
$this->_tpl_vars['i']=array();
$this->_tpl_vars['i']['columns'] = 1;
$this->_tpl_vars['i']['count'] = count($this->_tpl_vars['chapterrows']);
$this->_tpl_vars['i']['addrows'] = count($this->_tpl_vars['chapterrows']) % $this->_tpl_vars['i']['columns'] == 0 ? 0 : $this->_tpl_vars['i']['columns'] - count($this->_tpl_vars['chapterrows']) % $this->_tpl_vars['i']['columns'];
$this->_tpl_vars['i']['loops'] = $this->_tpl_vars['i']['count'] + $this->_tpl_vars['i']['addrows'];
reset($this->_tpl_vars['chapterrows']);
for($this->_tpl_vars['i']['index'] = 0; $this->_tpl_vars['i']['index'] < $this->_tpl_vars['i']['loops']; $this->_tpl_vars['i']['index']++){
	$this->_tpl_vars['i']['order'] = $this->_tpl_vars['i']['index'] + 1;
	$this->_tpl_vars['i']['row'] = ceil($this->_tpl_vars['i']['order'] / $this->_tpl_vars['i']['columns']);
	$this->_tpl_vars['i']['column'] = $this->_tpl_vars['i']['order'] % $this->_tpl_vars['i']['columns'];
	if($this->_tpl_vars['i']['column'] == 0) $this->_tpl_vars['i']['column'] = $this->_tpl_vars['i']['columns'];
	if($this->_tpl_vars['i']['index'] < $this->_tpl_vars['i']['count']){
		list($this->_tpl_vars['i']['key'], $this->_tpl_vars['i']['value']) = each($this->_tpl_vars['chapterrows']);
		$this->_tpl_vars['i']['append'] = 0;
	}else{
		$this->_tpl_vars['i']['key'] = '';
		$this->_tpl_vars['i']['value'] = '';
		$this->_tpl_vars['i']['append'] = 1;
	}
	echo '
	';
if($this->_tpl_vars['chapterrows'][$this->_tpl_vars['i']['key']]['chaptertype'] > 0){
echo '
		<dt>
		'.$this->_tpl_vars['chapterrows'][$this->_tpl_vars['i']['key']]['chaptername'].'
		</dt>
	';
}else{
echo '
		<dd>
		';
if($this->_tpl_vars['chapterrows'][$this->_tpl_vars['i']['key']]['isvip'] > 0){
echo '
		<a href="'.$this->_tpl_vars['chapterrows'][$this->_tpl_vars['i']['key']]['url_chapter'].'" title="'.date('Y-m-d H:i',$this->_tpl_vars['chapterrows'][$this->_tpl_vars['i']['key']]['lastupdate']).'更新，共'.$this->_tpl_vars['chapterrows'][$this->_tpl_vars['i']['key']]['words'].'字，价格：'.$this->_tpl_vars['chapterrows'][$this->_tpl_vars['i']['key']]['saleprice'].'"';
if($this->_tpl_vars['chapterrows'][$this->_tpl_vars['i']['key']]['display'] != 0){
echo ' class="gray"';
}
echo '>'.$this->_tpl_vars['chapterrows'][$this->_tpl_vars['i']['key']]['chaptername'].'</a><em class="iconfont">&#xee8b;</em>
		';
}else{
echo '
		<a href="'.$this->_tpl_vars['chapterrows'][$this->_tpl_vars['i']['key']]['url_chapter'].'" title="'.date('Y-m-d H:i',$this->_tpl_vars['chapterrows'][$this->_tpl_vars['i']['key']]['lastupdate']).'更新，共'.$this->_tpl_vars['chapterrows'][$this->_tpl_vars['i']['key']]['words'].'字"';
if($this->_tpl_vars['chapterrows'][$this->_tpl_vars['i']['key']]['display'] != 0){
echo ' class="gray"';
}
echo '>'.$this->_tpl_vars['chapterrows'][$this->_tpl_vars['i']['key']]['chaptername'].'</a>
		';
}
echo '
		</dd>
	';
}
}
echo '
</dl>

<div class="fullbar"><script type="text/javascript" src="'.$this->_tpl_vars['jieqi_modules']['article']['url'].'/scripts/indexbottom.js"></script></div>
</div>
</div>
';
?>