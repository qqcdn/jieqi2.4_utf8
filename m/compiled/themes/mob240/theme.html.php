<?php
echo '<!doctype html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset='.$this->_tpl_vars['jieqi_charset'].'" />
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, minimum-scale=1, user-scalable=no" />
<meta name="apple-mobile-web-app-capable" content="yes" />
<meta name="apple-touch-fullscreen" content="yes" />
<meta name="apple-mobile-web-app-title" content="'.$this->_tpl_vars['jieqi_sitename'].'" />
<meta name="keywords" content="'.$this->_tpl_vars['meta_keywords'].'" />
<meta name="description" content="'.$this->_tpl_vars['meta_description'].'" />
<meta name="author" content="'.$this->_tpl_vars['meta_author'].'" />
<meta name="copyright" content="'.$this->_tpl_vars['meta_copyright'].'" />
<meta name="generator" content="jieqi.com" />
<meta name="layoutmode" content="standard" />
<title>'.$this->_tpl_vars['jieqi_pagetitle'].'</title>
<link rel="stylesheet" href="'.$this->_tpl_vars['jieqi_themeurl'].'style.css" type="text/css" media="all" />
<script type="text/javascript" src="'.$this->_tpl_vars['jieqi_url'].'/scripts/common.js"></script>
<script type="text/javascript" src="'.$this->_tpl_vars['jieqi_url'].'/scripts/theme.js"></script>
'.$this->_tpl_vars['jieqi_head'].'
</head>
<body>
';
if($this->_tpl_vars['jieqi_contenttitle'] != ''){
echo '
<div class="pagetitle cf"><a href="';
if($this->_tpl_vars['jieqi_urlback'] != ''){
echo $this->_tpl_vars['jieqi_urlback'];
}else{
echo 'javascript:if(history.length > 1) history.back(); else document.location.href=\''.$this->_tpl_vars['jieqi_url'].'/\';';
}
echo '"><i class="iconfont fl">&#xee69;</i></a><a href="'.$this->_tpl_vars['jieqi_url'].'/"><i class="iconfont fr">&#xee27;</i></a><span id="pagettext">'.$this->_tpl_vars['jieqi_contenttitle'].'</span></div>
';
}else{
echo '
<div class="header cf">
	<div class="logo">
		<a href="'.$this->_tpl_vars['jieqi_url'].'/"><img src="'.$this->_tpl_vars['jieqi_themeurl'].'logo.png" border="0" alt="'.$this->_tpl_vars['jieqi_sitename'].'" /></a>
	</div>
	<div class="banner">
		';
if($this->_tpl_vars['jieqi_userid'] == 0){
echo '
		<a href="';
if($this->_tpl_vars['jieqi_browser'] == 'weixin'){
echo $this->_tpl_vars['jieqi_url'].'/api/wxmp/login.php?jumpurl='.urlencode($this->_tpl_vars['jieqi_url']).'%2Fuserdetail.php';
}else{
echo $this->_tpl_vars['jieqi_user_url'].'/login.php?jumpurl='.urlencode($this->_tpl_vars['jieqi_url']).'%2Fuserdetail.php';
}
echo '" class="iconfont" title="登录">&#xee21;</a>
		<a href="'.$this->_tpl_vars['jieqi_modules']['article']['url'].'/recentread.php" class="iconfont" title="最近阅读">&#xee43;</a>
		';
}else{
echo '
		<a href="'.$this->_tpl_vars['jieqi_user_url'].'/userdetail.php" class="iconfont" title="会员">&#xee21;</a>
		<a href="'.$this->_tpl_vars['jieqi_modules']['article']['url'].'/bookcase.php" class="iconfont" title="书架">&#xee43;</a>
		';
}
echo '
	</div>
</div>
<div class="mainnav cf">
	<ul class="df">
	<li><a href="'.$this->_tpl_vars['jieqi_modules']['article']['url'].'/articlefilter.php">书库</a></li>
	<li><a href="'.$this->_tpl_vars['jieqi_modules']['article']['url'].'/sortselect.php">分类</a></li>
	<li><a href="'.$this->_tpl_vars['jieqi_modules']['article']['url'].'/top.php">排行</a></li>
	<li><a href="'.$this->_tpl_vars['jieqi_modules']['article']['url'].'/toplist.php?fullflag=1">全本</a></li>
	<li><a href="'.$this->_tpl_vars['jieqi_modules']['pay']['url'].'/buyegold.php">充值</a></li>
	</ul>
</div>
';
}
echo '

';
if($this->_tpl_vars['jieqi_contents'] != ""){
echo '<div id="content">'.$this->_tpl_vars['jieqi_contents'].'</div>';
}
echo '

';
if (empty($this->_tpl_vars['jieqi_pageblocks'])) $this->_tpl_vars['jieqi_pageblocks'] = array();
elseif (!is_array($this->_tpl_vars['jieqi_pageblocks'])) $this->_tpl_vars['jieqi_pageblocks'] = (array)$this->_tpl_vars['jieqi_pageblocks'];
$this->_tpl_vars['i']=array();
$this->_tpl_vars['i']['columns'] = 1;
$this->_tpl_vars['i']['count'] = count($this->_tpl_vars['jieqi_pageblocks']);
$this->_tpl_vars['i']['addrows'] = count($this->_tpl_vars['jieqi_pageblocks']) % $this->_tpl_vars['i']['columns'] == 0 ? 0 : $this->_tpl_vars['i']['columns'] - count($this->_tpl_vars['jieqi_pageblocks']) % $this->_tpl_vars['i']['columns'];
$this->_tpl_vars['i']['loops'] = $this->_tpl_vars['i']['count'] + $this->_tpl_vars['i']['addrows'];
reset($this->_tpl_vars['jieqi_pageblocks']);
for($this->_tpl_vars['i']['index'] = 0; $this->_tpl_vars['i']['index'] < $this->_tpl_vars['i']['loops']; $this->_tpl_vars['i']['index']++){
	$this->_tpl_vars['i']['order'] = $this->_tpl_vars['i']['index'] + 1;
	$this->_tpl_vars['i']['row'] = ceil($this->_tpl_vars['i']['order'] / $this->_tpl_vars['i']['columns']);
	$this->_tpl_vars['i']['column'] = $this->_tpl_vars['i']['order'] % $this->_tpl_vars['i']['columns'];
	if($this->_tpl_vars['i']['column'] == 0) $this->_tpl_vars['i']['column'] = $this->_tpl_vars['i']['columns'];
	if($this->_tpl_vars['i']['index'] < $this->_tpl_vars['i']['count']){
		list($this->_tpl_vars['i']['key'], $this->_tpl_vars['i']['value']) = each($this->_tpl_vars['jieqi_pageblocks']);
		$this->_tpl_vars['i']['append'] = 0;
	}else{
		$this->_tpl_vars['i']['key'] = '';
		$this->_tpl_vars['i']['value'] = '';
		$this->_tpl_vars['i']['append'] = 1;
	}
	if($this->_tpl_vars['jieqi_pageblocks'][$this->_tpl_vars['i']['key']]['side'] >= 0){
if($this->_tpl_vars['jieqi_pageblocks'][$this->_tpl_vars['i']['key']]['title'] != ""){
echo '
<div class="block">
	<div class="blocktitle">'.$this->_tpl_vars['jieqi_pageblocks'][$this->_tpl_vars['i']['key']]['title'].'</div>
	<div class="blockcontent">'.$this->_tpl_vars['jieqi_pageblocks'][$this->_tpl_vars['i']['key']]['content'].'</div>
</div>
';
}else{
echo '
<div class="blockc">'.$this->_tpl_vars['jieqi_pageblocks'][$this->_tpl_vars['i']['key']]['content'].'</div>
';
}
}
}
echo '

<div class="footer">
Powered by <strong><a href="http://www.jieqi.com">JIEQI CMS</a></strong><br /> &copy;2004-'.date('Y',$this->_tpl_vars['jieqi_time']).' <a href="http://www.jieqi.com">杰奇网络（jieqi.com）</a>
<br />Processed in '.$this->_tpl_vars['jieqi_exetime'].' second(s). 
</div>

<div><a class="gotop" id="gotop" href="javascript:scroll(0,0);"><i class="iconfont">&#xee6b;</i></a></div>
<script type="text/javascript">
window.onscroll = function(){
	if(document.body.scrollTop || document.documentElement.scrollTop > 0) document.getElementById(\'gotop\').style.display = \'block\';
	else document.getElementById(\'gotop\').style.display = \'none\';
}
</script>

</body>
</html>';
?>