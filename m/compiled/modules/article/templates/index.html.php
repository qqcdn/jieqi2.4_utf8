<?php
echo '
<!doctype html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset='.$this->_tpl_vars['jieqi_charset'].'" />
<meta name="viewport" content="initial-scale=1.0,maximum-scale=1.0,minimum-scale=1.0,user-scalable=0,width=device-width" />
<meta name="keywords" content="'.$this->_tpl_vars['articlename'].' '.$this->_tpl_vars['author'].' '.$this->_tpl_vars['sort'].' '.$this->_tpl_vars['jieqi_sitename'].'" />
<meta name="description" content="'.truncate($this->_tpl_vars['intro'],'500','..').'" />
<meta name="author" content="'.$this->_tpl_vars['meta_author'].'" />
<meta name="copyright" content="'.$this->_tpl_vars['meta_copyright'].'" />
<meta name="generator" content="jieqi.com" />
<title>'.$this->_tpl_vars['articlename'].'-'.$this->_tpl_vars['author'].'-'.$this->_tpl_vars['sort'].'-'.$this->_tpl_vars['jieqi_sitename'].'</title>
<link rel="stylesheet" href="'.$this->_tpl_vars['jieqi_themeurl'].'style.css" type="text/css" media="all" />
<link rel="stylesheet" href="'.$this->_tpl_vars['jieqi_modules']['article']['themeurl'].'/css/page.css" type="text/css" media="all" />
<script type="text/javascript" src="'.$this->_tpl_vars['jieqi_url'].'/scripts/common.js"></script>
<script type="text/javascript" src="'.$this->_tpl_vars['jieqi_url'].'/scripts/theme.js"></script>
<script type="text/javascript">
var ReadParams = {
	url_previous: \''.$this->_tpl_vars['url_previous'].'\',
    url_next: \''.$this->_tpl_vars['url_next'].'\',
    url_index: \''.$this->_tpl_vars['url_index'].'\',
    url_articleinfo: \''.$this->_tpl_vars['url_articleinfo'].'\',
    url_image: \''.$this->_tpl_vars['url_image'].'\',
    url_home: \''.$this->_tpl_vars['jieqi_url'].'/\',
    articleid: \''.$this->_tpl_vars['articleid'].'\',
    articlename: \''.$this->_tpl_vars['articlename'].'\',
    authorid: \''.$this->_tpl_vars['authorid'].'\',
    author: \''.$this->_tpl_vars['author'].'\',
    chapterid: \'0\',
    chaptername: \'\',
    userid: \''.$this->_tpl_vars['jieqi_userid'].'\',
    egoldname: \''.$this->_tpl_vars['egoldname'].'\'
}
</script>
</head>
<body>
<div class="pagetitle cf"><a href="'.jieqi_geturl('article','article',$this->_tpl_vars['articleid'],'info',$this->_tpl_vars['articlecode']).'"><i class="iconfont fl">&#xee69;</i></a><div class="fr">';
if($this->_tpl_vars['index_order'] == 'desc'){
echo '<a class="btnlink b_s b_note" href="'.jieqi_geturl('article','article',$this->_tpl_vars['articleid'],'index',$this->_tpl_vars['articlecode'],'1','asc').'">正序&nbsp;</a>';
}else{
echo '<a class="btnlink b_s b_note" href="'.jieqi_geturl('article','article',$this->_tpl_vars['articleid'],'index',$this->_tpl_vars['articlecode'],'1','desc').'">逆序&nbsp;</a>';
}
echo '</div>作品目录</div>

<div class="main">
	<ul class="tabb tab3 cf mb">
		<li><a href="'.jieqi_geturl('article','article',$this->_tpl_vars['articleid'],'info',$this->_tpl_vars['articlecode']).'">信息</a></li>
		<li><a href="javascript:void(0);" class="selected">目录</a></li>
		<li><a href="'.$this->_tpl_vars['jieqi_modules']['article']['url'].'/reviews.php?aid='.$this->_tpl_vars['articleid'].'">书评</a></li>
	</ul>

<div class="atitle">'.$this->_tpl_vars['articlename'].' <span class="ainfo"><a href="'.jieqi_geturl('article','author',$this->_tpl_vars['authorid'],$this->_tpl_vars['author']).'">'.$this->_tpl_vars['author'].'</a> 着</span></div>



<dl class="index" id="jieqi_page_contents">
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
if($this->_tpl_vars['chapterrows'][$this->_tpl_vars['i']['key']]['chaptertype'] == 0){
echo '
		<dd>
		';
if($this->_tpl_vars['chapterrows'][$this->_tpl_vars['i']['key']]['isvip'] > 0){
echo '
		<a class="db" href="'.$this->_tpl_vars['chapterrows'][$this->_tpl_vars['i']['key']]['url_chapter'].'" title="'.date('Y-m-d H:i',$this->_tpl_vars['chapterrows'][$this->_tpl_vars['i']['key']]['lastupdate']).'更新，共'.$this->_tpl_vars['chapterrows'][$this->_tpl_vars['i']['key']]['words'].'字，价格：'.$this->_tpl_vars['chapterrows'][$this->_tpl_vars['i']['key']]['saleprice'].'"';
if($this->_tpl_vars['chapterrows'][$this->_tpl_vars['i']['key']]['display'] != 0){
echo ' class="gray"';
}
echo '><em>VIP</em>'.$this->_tpl_vars['chapterrows'][$this->_tpl_vars['i']['key']]['chaptername'].'</a>
		';
}else{
echo '
		<a class="db" href="'.$this->_tpl_vars['chapterrows'][$this->_tpl_vars['i']['key']]['url_chapter'].'" title="'.date('Y-m-d H:i',$this->_tpl_vars['chapterrows'][$this->_tpl_vars['i']['key']]['lastupdate']).'更新，共'.$this->_tpl_vars['chapterrows'][$this->_tpl_vars['i']['key']]['words'].'字"';
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

<div class="pages">'.$this->_tpl_vars['url_jumppage'].'</div>

</div>

</body>
</html>
';
?>