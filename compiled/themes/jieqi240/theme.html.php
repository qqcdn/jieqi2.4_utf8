<?php
echo '<!doctype html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset='.$this->_tpl_vars['jieqi_charset'].'" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta name="keywords" content="'.$this->_tpl_vars['meta_keywords'].'" />
	<meta name="description" content="'.$this->_tpl_vars['meta_description'].'" />
	<meta name="author" content="'.$this->_tpl_vars['meta_author'].'" />
	<meta name="copyright" content="'.$this->_tpl_vars['meta_copyright'].'" />
	<meta name="generator" content="jieqi.com" />
	<title>'.$this->_tpl_vars['jieqi_pagetitle'].'</title>
	<link rel="stylesheet" href="'.$this->_tpl_vars['jieqi_themeurl'].'style.css" type="text/css" media="all" />
	<!--[if lt IE 9]><script type="text/javascript" src="'.$this->_tpl_vars['jieqi_url'].'/scripts/html5.js"></script><![endif]-->
	<!--[if lt IE 9]><script type="text/javascript" src="'.$this->_tpl_vars['jieqi_url'].'/scripts/css3-mediaqueries.js"></script><![endif]-->
	<script type="text/javascript" src="'.$this->_tpl_vars['jieqi_url'].'/scripts/common.js"></script>
	<script type="text/javascript" src="'.$this->_tpl_vars['jieqi_url'].'/scripts/theme.js"></script>
	'.$this->_tpl_vars['jieqi_head'].'
</head>
<body>
<div class="top cf">
	<div class="main">
		<div class="fl">
			';
if($this->_tpl_vars['jieqi_userid'] == 0){
echo '
			<form name="t_frmlogin" id="t_frmlogin" method="post" action="'.$this->_tpl_vars['jieqi_user_url'].'/login.php">
				&nbsp;用户名：<input type="text" class="text t_s" size="10" maxlength="30" style="width:70px;" name="username" onKeyPress="javascript: if (event.keyCode==32) return false;">
				&nbsp;密码：<input type="password" class="text t_s" size="10" maxlength="30" style="width:70px;" name="password">
				';
if($this->_tpl_vars['jieqi_login_checkcode'] > 0){
echo '
				验证码：<input type="text" class="text t_s" style="width:35px;" name="checkcode" onfocus="if(this.form.imgccode.style.display == \'none\'){this.form.imgccode.src = \''.$this->_tpl_vars['jieqi_url'].'/checkcode.php?rand='.$this->_tpl_vars['jieqi_time'].'\';this.form.imgccode.style.display = \'\';}" title="点击显示验证码"><img name="imgccode" src="" style="cursor:pointer;vertical-align:middle;margin-left:3px;display:none;" onclick="this.src=\''.$this->_tpl_vars['jieqi_url'].'/checkcode.php?rand=\'+Math.random();" title="点击刷新验证码">
				';
}
echo '
				<label class="checkbox"><input type="checkbox" name="usecookie" value="1" />记住</label>
				<button type="button" class="button b_s" name="t_btnlogin" id="t_btnlogin" onclick="Ajax.Tip(\'t_frmlogin\', {timeout:3000, onLoading:\'登录中...\', onComplete:\'登录成功，页面跳转中...\'});">登录</button>
				<input type="hidden" name="act" value="login">
				<input type="hidden" name="jumpreferer" value="1">
				';
if($this->_tpl_vars['jieqi_api_sites']['weixin']['publish'] > 0){
echo '<a href="'.$this->_tpl_vars['jieqi_url'].'/api/weixin/login.php" rel="nofollow" target="_top"><img src="'.$this->_tpl_vars['jieqi_url'].'/images/api/weixin_ico.gif" title="用微信扫码登录" style="border:0;vertical-align:middle;"></a>';
}
echo '
				';
if($this->_tpl_vars['jieqi_api_sites']['qq']['publish'] > 0){
echo '<a href="'.$this->_tpl_vars['jieqi_url'].'/api/qq/login.php" rel="nofollow" target="_top"><img src="'.$this->_tpl_vars['jieqi_url'].'/images/api/qq_ico.gif" title="用QQ账号登录" style="border:0;vertical-align:middle;"></a>';
}
echo '
				';
if($this->_tpl_vars['jieqi_api_sites']['weibo']['publish'] > 0){
echo '<a href="'.$this->_tpl_vars['jieqi_url'].'/api/weibo/login.php" rel="nofollow" target="_top"><img src="'.$this->_tpl_vars['jieqi_url'].'/images/api/weibo_ico.gif" title="用新浪微博账号登录" style="border:0;vertical-align:middle;"></a>';
}
echo '
			</form>
			';
}else{
echo '
			<ul class="topnav">
				<li><strong>'.$this->_tpl_vars['jieqi_username'].'：</strong></li>
				<li><a href="'.$this->_tpl_vars['jieqi_url'].'/message.php?box=inbox" class="droplink"><i class="iconfont">&#xee36;</i>消息</a>';
if($this->_tpl_vars['jieqi_newmessage'] > 0){
echo '<sup>'.$this->_tpl_vars['jieqi_newmessage'].'</sup>';
}
echo '</li>
				<li class="dropdown"><a href="'.$this->_tpl_vars['jieqi_url'].'/userdetail.php" class="droplink"><i class="iconfont">&#xee21;</i>会员<b class="dropico"></b></a>
					<ul class="droplist">
						';
if($this->_tpl_vars['jieqi_modules']['article']['publish'] > 0){
echo '<li><a href="'.$this->_tpl_vars['jieqi_modules']['article']['url'].'/bookcase.php">我的书架</a></li>';
}
echo '
						';
if($this->_tpl_vars['jieqi_modules']['obook']['publish'] > 0){
echo '<li><a href="'.$this->_tpl_vars['jieqi_modules']['obook']['url'].'/buylist.php">我的订阅</a></li>';
}
echo '
						';
if($this->_tpl_vars['jieqi_modules']['pay']['publish'] > 0){
echo '<li><a href="'.$this->_tpl_vars['jieqi_modules']['pay']['url'].'/buyegold.php">帐户充值</a></li>';
}
echo '
						<li><a href="'.$this->_tpl_vars['jieqi_url'].'/useredit.php">修改资料</a></li>
						<li><a href="'.$this->_tpl_vars['jieqi_user_url'].'/logout.php">退出登录</a></li>
					</ul>
				</li>
				';
if($this->_tpl_vars['jieqi_modules']['article']['publish'] > 0){
echo '<li><a href="'.$this->_tpl_vars['jieqi_modules']['article']['url'].'/bookcase.php" class="droplink"><i class="iconfont">&#xee43;</i>书架</a></li>';
}
echo '
				';
if($this->_tpl_vars['jieqi_modules']['pay']['publish'] > 0){
echo '<li><a href="'.$this->_tpl_vars['jieqi_modules']['pay']['url'].'/buyegold.php" class="droplink"><i class="iconfont">&#xee3c;</i>充值</a></li>';
}
echo '
				<li><a href="'.$this->_tpl_vars['jieqi_user_url'].'/logout.php" class="droplink"><i class="iconfont">&#xee2a;</i>退出</a></li>
			</ul>
			';
}
echo '
		</div>
		<div class="fr">
			';
if($this->_tpl_vars['jieqi_userid'] == 0){
echo '
			<a class="hot" href="'.$this->_tpl_vars['jieqi_user_url'].'/register.php" onclick="openDialog(\''.$this->_tpl_vars['jieqi_user_url'].'/register.php?ajax_gets=jieqi_contents\', false);stopEvent();">注册用户</a> | <a class="hot" href="'.$this->_tpl_vars['jieqi_user_url'].'/getpass.php" onclick="openDialog(\''.$this->_tpl_vars['jieqi_user_url'].'/getpass.php?ajax_gets=jieqi_contents\', false);stopEvent();">忘记密码？</a>
			';
}
echo '
		</div>
	</div>
</div>

<div class="header cf">
	<div class="main row cf">
		<div class="col9">
			<div class="logo">
			<a href="'.$this->_tpl_vars['jieqi_url'].'/"><img src="'.$this->_tpl_vars['jieqi_themeurl'].'logo.png" border="0" alt="'.$this->_tpl_vars['jieqi_sitename'].'" /></a>
			</div>
			<div class="mainnav">
				<ul class="dropmenu">
					<li><a href="'.$this->_tpl_vars['jieqi_url'].'/">首页</a></li>
					<li><a href="'.$this->_tpl_vars['jieqi_modules']['article']['url'].'/articlefilter.php">书库</a></li>
					<li><a href="'.jieqi_geturl('article','toplist','1','allvisit').'">排行</a></li>
					<li><a href="'.jieqi_geturl('article','toplist','1','allvisit','','1').'">全本</a></li>
					<li><a href="'.$this->_tpl_vars['jieqi_modules']['forum']['url'].'/">论坛</a></li>
					<li><a href="'.$this->_tpl_vars['jieqi_modules']['pay']['url'].'/buyegold.php">充值</a></li>
					<li><a href="'.$this->_tpl_vars['jieqi_modules']['article']['url'].'/myarticle.php">作家专区</a>
						<ul>
							<li><a href="'.$this->_tpl_vars['jieqi_modules']['article']['url'].'/newarticle.php">发表小说</a></li>
							<li><a href="'.$this->_tpl_vars['jieqi_modules']['article']['url'].'/masterpage.php">管理小说</a></li>
							<li><a href="'.$this->_tpl_vars['jieqi_modules']['obook']['url'].'/masterpage.php">收入管理</a></li>
							<li><a href="'.$this->_tpl_vars['jieqi_modules']['article']['url'].'/newdraft.php">新建草稿</a></li>
							<li><a href="'.$this->_tpl_vars['jieqi_modules']['article']['url'].'/draft.php">管理草稿</a></li>
							<li><a href="'.$this->_tpl_vars['jieqi_url'].'/ptopics.php?oid=self">用户留言</a></li>
							<li><a href="'.$this->_tpl_vars['jieqi_modules']['article']['url'].'/authorpage.php">我的专栏</a></li>
						</ul>
					</li>
				</ul>
			</div>
		</div>
		<div class="col3 last banner">
			';
if($this->_tpl_vars['jieqi_banner'] == ''){
echo '
			<form style="width:235px;height:32px;padding:0;margin:15px auto 0 auto;position:relative;background:#fff;border:2px solid #ffaa00;border-radius:5px;" name="t_frmsearch" method="post" action="'.$this->_tpl_vars['jieqi_modules']['article']['url'].'/search.php">
				<select name="searchtype" style="width:55px;height:30px;padding:0;margin:0;line-height:30px;border:0;font-size:14px;position:absolute;left:5px;top:0;">
					<option value="all" selected="selected">综合</option>
					<option value="articlename">书名</option>
					<option value="author">作者</option>
					<option value="keywords">标签</option>
				</select>
				<input name="searchkey" type="text" style="width:128px;height:30px;padding:0;margin:0;line-height:30px;border:0;font-size:14px;position:absolute;left:62px;top:0;">
				<button type="submit" name="t_btnsearch" class="iconfont" style="width:45px;height:32px;padding:0;margin:0;line-height:32px;text-align:center;background:#ffaa00;color:#fff;cursor:pointer;border:0;font-size:14px;position:absolute;right:-2px;top:0;border-radius:0 5px 5px 0;">&#xee28;</button>
			</form>
			';
}else{
echo '
			'.$this->_tpl_vars['jieqi_banner'].'
			';
}
echo '
		</div>
	</div>
</div>



<div class="subnav">
	<div class="main">
		<a href="'.jieqi_geturl('article','articlelist','1','1').'">玄幻·魔法</a>|
		<a href="'.jieqi_geturl('article','articlelist','1','2').'">武侠·修真</a>|
		<a href="'.jieqi_geturl('article','articlelist','1','3').'">都市·言情</a>|
		<a href="'.jieqi_geturl('article','articlelist','1','4').'">历史·军事</a>|
		<a href="'.jieqi_geturl('article','articlelist','1','5').'">穿越·架空</a>|
		<a href="'.jieqi_geturl('article','articlelist','1','6').'">游戏·竞技</a>|
		<a href="'.jieqi_geturl('article','articlelist','1','7').'">科幻·灵异</a>|
		<a href="'.jieqi_geturl('article','articlelist','1','8').'">同人·动漫</a>|
		<a href="'.jieqi_geturl('article','articlelist','1','9').'">社会·文学</a>|
		<a href="'.jieqi_geturl('article','articlelist','1','10').'">综合·其他</a>
	</div>
</div>

<div class="main">
    ';
if(empty($this->_tpl_vars['jieqi_sideblocks']) == false){
echo '<div class="mainbody">';
}
echo '
		';
if($this->_tpl_vars['jieqi_top_bar'] != ""){
echo '<div class="row"><div class="col12">'.$this->_tpl_vars['jieqi_top_bar'].'</div></div>';
}
echo '

		';
if(empty($this->_tpl_vars['jieqi_sideblocks']) == false){
echo '
		';
if(empty($this->_tpl_vars['jieqi_sideblocks']['7']) == false){
echo '
		<div class="row">
			<div class="col12">
				';
if (empty($this->_tpl_vars['jieqi_sideblocks']['7'])) $this->_tpl_vars['jieqi_sideblocks']['7'] = array();
elseif (!is_array($this->_tpl_vars['jieqi_sideblocks']['7'])) $this->_tpl_vars['jieqi_sideblocks']['7'] = (array)$this->_tpl_vars['jieqi_sideblocks']['7'];
$this->_tpl_vars['i']=array();
$this->_tpl_vars['i']['columns'] = 1;
$this->_tpl_vars['i']['count'] = count($this->_tpl_vars['jieqi_sideblocks']['7']);
$this->_tpl_vars['i']['addrows'] = count($this->_tpl_vars['jieqi_sideblocks']['7']) % $this->_tpl_vars['i']['columns'] == 0 ? 0 : $this->_tpl_vars['i']['columns'] - count($this->_tpl_vars['jieqi_sideblocks']['7']) % $this->_tpl_vars['i']['columns'];
$this->_tpl_vars['i']['loops'] = $this->_tpl_vars['i']['count'] + $this->_tpl_vars['i']['addrows'];
reset($this->_tpl_vars['jieqi_sideblocks']['7']);
for($this->_tpl_vars['i']['index'] = 0; $this->_tpl_vars['i']['index'] < $this->_tpl_vars['i']['loops']; $this->_tpl_vars['i']['index']++){
	$this->_tpl_vars['i']['order'] = $this->_tpl_vars['i']['index'] + 1;
	$this->_tpl_vars['i']['row'] = ceil($this->_tpl_vars['i']['order'] / $this->_tpl_vars['i']['columns']);
	$this->_tpl_vars['i']['column'] = $this->_tpl_vars['i']['order'] % $this->_tpl_vars['i']['columns'];
	if($this->_tpl_vars['i']['column'] == 0) $this->_tpl_vars['i']['column'] = $this->_tpl_vars['i']['columns'];
	if($this->_tpl_vars['i']['index'] < $this->_tpl_vars['i']['count']){
		list($this->_tpl_vars['i']['key'], $this->_tpl_vars['i']['value']) = each($this->_tpl_vars['jieqi_sideblocks']['7']);
		$this->_tpl_vars['i']['append'] = 0;
	}else{
		$this->_tpl_vars['i']['key'] = '';
		$this->_tpl_vars['i']['value'] = '';
		$this->_tpl_vars['i']['append'] = 1;
	}
	echo '
				';
if($this->_tpl_vars['jieqi_sideblocks']['7'][$this->_tpl_vars['i']['key']]['title'] != ""){
echo '
				<div class="block">
					<div class="blocktitle">'.$this->_tpl_vars['jieqi_sideblocks']['7'][$this->_tpl_vars['i']['key']]['title'].'</div>
					<div class="blockcontent">'.$this->_tpl_vars['jieqi_sideblocks']['7'][$this->_tpl_vars['i']['key']]['content'].'</div>
				</div>
				';
}else{
echo '
				<div class="blockc">'.$this->_tpl_vars['jieqi_sideblocks']['7'][$this->_tpl_vars['i']['key']]['content'].'</div>
				';
}
echo '
				';
}
echo '
			</div>
		</div>
		';
}
echo '

		';
if(empty($this->_tpl_vars['jieqi_sideblocks']['2']) == false || empty($this->_tpl_vars['jieqi_sideblocks']['3']) == false){
echo '
		<div class="row">
			<div class="col6">
				';
if (empty($this->_tpl_vars['jieqi_sideblocks']['2'])) $this->_tpl_vars['jieqi_sideblocks']['2'] = array();
elseif (!is_array($this->_tpl_vars['jieqi_sideblocks']['2'])) $this->_tpl_vars['jieqi_sideblocks']['2'] = (array)$this->_tpl_vars['jieqi_sideblocks']['2'];
$this->_tpl_vars['i']=array();
$this->_tpl_vars['i']['columns'] = 1;
$this->_tpl_vars['i']['count'] = count($this->_tpl_vars['jieqi_sideblocks']['2']);
$this->_tpl_vars['i']['addrows'] = count($this->_tpl_vars['jieqi_sideblocks']['2']) % $this->_tpl_vars['i']['columns'] == 0 ? 0 : $this->_tpl_vars['i']['columns'] - count($this->_tpl_vars['jieqi_sideblocks']['2']) % $this->_tpl_vars['i']['columns'];
$this->_tpl_vars['i']['loops'] = $this->_tpl_vars['i']['count'] + $this->_tpl_vars['i']['addrows'];
reset($this->_tpl_vars['jieqi_sideblocks']['2']);
for($this->_tpl_vars['i']['index'] = 0; $this->_tpl_vars['i']['index'] < $this->_tpl_vars['i']['loops']; $this->_tpl_vars['i']['index']++){
	$this->_tpl_vars['i']['order'] = $this->_tpl_vars['i']['index'] + 1;
	$this->_tpl_vars['i']['row'] = ceil($this->_tpl_vars['i']['order'] / $this->_tpl_vars['i']['columns']);
	$this->_tpl_vars['i']['column'] = $this->_tpl_vars['i']['order'] % $this->_tpl_vars['i']['columns'];
	if($this->_tpl_vars['i']['column'] == 0) $this->_tpl_vars['i']['column'] = $this->_tpl_vars['i']['columns'];
	if($this->_tpl_vars['i']['index'] < $this->_tpl_vars['i']['count']){
		list($this->_tpl_vars['i']['key'], $this->_tpl_vars['i']['value']) = each($this->_tpl_vars['jieqi_sideblocks']['2']);
		$this->_tpl_vars['i']['append'] = 0;
	}else{
		$this->_tpl_vars['i']['key'] = '';
		$this->_tpl_vars['i']['value'] = '';
		$this->_tpl_vars['i']['append'] = 1;
	}
	echo '
				';
if($this->_tpl_vars['jieqi_sideblocks']['2'][$this->_tpl_vars['i']['key']]['title'] != ""){
echo '
				<div class="block">
					<div class="blocktitle">'.$this->_tpl_vars['jieqi_sideblocks']['2'][$this->_tpl_vars['i']['key']]['title'].'</div>
					<div class="blockcontent">'.$this->_tpl_vars['jieqi_sideblocks']['2'][$this->_tpl_vars['i']['key']]['content'].'</div>
				</div>
				';
}else{
echo '
				<div class="blockc">'.$this->_tpl_vars['jieqi_sideblocks']['2'][$this->_tpl_vars['i']['key']]['content'].'</div>
				';
}
echo '
				';
}
echo '
			</div>
			<div class="col6 last">
				';
if (empty($this->_tpl_vars['jieqi_sideblocks']['3'])) $this->_tpl_vars['jieqi_sideblocks']['3'] = array();
elseif (!is_array($this->_tpl_vars['jieqi_sideblocks']['3'])) $this->_tpl_vars['jieqi_sideblocks']['3'] = (array)$this->_tpl_vars['jieqi_sideblocks']['3'];
$this->_tpl_vars['i']=array();
$this->_tpl_vars['i']['columns'] = 1;
$this->_tpl_vars['i']['count'] = count($this->_tpl_vars['jieqi_sideblocks']['3']);
$this->_tpl_vars['i']['addrows'] = count($this->_tpl_vars['jieqi_sideblocks']['3']) % $this->_tpl_vars['i']['columns'] == 0 ? 0 : $this->_tpl_vars['i']['columns'] - count($this->_tpl_vars['jieqi_sideblocks']['3']) % $this->_tpl_vars['i']['columns'];
$this->_tpl_vars['i']['loops'] = $this->_tpl_vars['i']['count'] + $this->_tpl_vars['i']['addrows'];
reset($this->_tpl_vars['jieqi_sideblocks']['3']);
for($this->_tpl_vars['i']['index'] = 0; $this->_tpl_vars['i']['index'] < $this->_tpl_vars['i']['loops']; $this->_tpl_vars['i']['index']++){
	$this->_tpl_vars['i']['order'] = $this->_tpl_vars['i']['index'] + 1;
	$this->_tpl_vars['i']['row'] = ceil($this->_tpl_vars['i']['order'] / $this->_tpl_vars['i']['columns']);
	$this->_tpl_vars['i']['column'] = $this->_tpl_vars['i']['order'] % $this->_tpl_vars['i']['columns'];
	if($this->_tpl_vars['i']['column'] == 0) $this->_tpl_vars['i']['column'] = $this->_tpl_vars['i']['columns'];
	if($this->_tpl_vars['i']['index'] < $this->_tpl_vars['i']['count']){
		list($this->_tpl_vars['i']['key'], $this->_tpl_vars['i']['value']) = each($this->_tpl_vars['jieqi_sideblocks']['3']);
		$this->_tpl_vars['i']['append'] = 0;
	}else{
		$this->_tpl_vars['i']['key'] = '';
		$this->_tpl_vars['i']['value'] = '';
		$this->_tpl_vars['i']['append'] = 1;
	}
	echo '
				';
if($this->_tpl_vars['jieqi_sideblocks']['3'][$this->_tpl_vars['i']['key']]['title'] != ""){
echo '
				<div class="block">
					<div class="blocktitle">'.$this->_tpl_vars['jieqi_sideblocks']['3'][$this->_tpl_vars['i']['key']]['title'].'</div>
					<div class="blockcontent">'.$this->_tpl_vars['jieqi_sideblocks']['3'][$this->_tpl_vars['i']['key']]['content'].'</div>
				</div>
				';
}else{
echo '
				<div class="blockc">'.$this->_tpl_vars['jieqi_sideblocks']['3'][$this->_tpl_vars['i']['key']]['content'].'</div>
				';
}
echo '
				';
}
echo '
			</div>
		</div>
		';
}
echo '

		<div class="row">
			';
if(empty($this->_tpl_vars['jieqi_sideblocks']['9']) == false){
echo '
			<div class="col2">
				';
if (empty($this->_tpl_vars['jieqi_sideblocks']['9'])) $this->_tpl_vars['jieqi_sideblocks']['9'] = array();
elseif (!is_array($this->_tpl_vars['jieqi_sideblocks']['9'])) $this->_tpl_vars['jieqi_sideblocks']['9'] = (array)$this->_tpl_vars['jieqi_sideblocks']['9'];
$this->_tpl_vars['i']=array();
$this->_tpl_vars['i']['columns'] = 1;
$this->_tpl_vars['i']['count'] = count($this->_tpl_vars['jieqi_sideblocks']['9']);
$this->_tpl_vars['i']['addrows'] = count($this->_tpl_vars['jieqi_sideblocks']['9']) % $this->_tpl_vars['i']['columns'] == 0 ? 0 : $this->_tpl_vars['i']['columns'] - count($this->_tpl_vars['jieqi_sideblocks']['9']) % $this->_tpl_vars['i']['columns'];
$this->_tpl_vars['i']['loops'] = $this->_tpl_vars['i']['count'] + $this->_tpl_vars['i']['addrows'];
reset($this->_tpl_vars['jieqi_sideblocks']['9']);
for($this->_tpl_vars['i']['index'] = 0; $this->_tpl_vars['i']['index'] < $this->_tpl_vars['i']['loops']; $this->_tpl_vars['i']['index']++){
	$this->_tpl_vars['i']['order'] = $this->_tpl_vars['i']['index'] + 1;
	$this->_tpl_vars['i']['row'] = ceil($this->_tpl_vars['i']['order'] / $this->_tpl_vars['i']['columns']);
	$this->_tpl_vars['i']['column'] = $this->_tpl_vars['i']['order'] % $this->_tpl_vars['i']['columns'];
	if($this->_tpl_vars['i']['column'] == 0) $this->_tpl_vars['i']['column'] = $this->_tpl_vars['i']['columns'];
	if($this->_tpl_vars['i']['index'] < $this->_tpl_vars['i']['count']){
		list($this->_tpl_vars['i']['key'], $this->_tpl_vars['i']['value']) = each($this->_tpl_vars['jieqi_sideblocks']['9']);
		$this->_tpl_vars['i']['append'] = 0;
	}else{
		$this->_tpl_vars['i']['key'] = '';
		$this->_tpl_vars['i']['value'] = '';
		$this->_tpl_vars['i']['append'] = 1;
	}
	echo '
				';
if($this->_tpl_vars['jieqi_sideblocks']['9'][$this->_tpl_vars['i']['key']]['title'] != ""){
echo '
				<div class="block">
					<div class="blocktitle">'.$this->_tpl_vars['jieqi_sideblocks']['9'][$this->_tpl_vars['i']['key']]['title'].'</div>
					<div class="blockcontent">'.$this->_tpl_vars['jieqi_sideblocks']['9'][$this->_tpl_vars['i']['key']]['content'].'</div>
				</div>
				';
}else{
echo '
				<div class="blockc">'.$this->_tpl_vars['jieqi_sideblocks']['9'][$this->_tpl_vars['i']['key']]['content'].'</div>
				';
}
echo '
				';
}
echo '
			</div>
			';
if(empty($this->_tpl_vars['jieqi_sideblocks']['1']) == false){
echo '<div class="col7">';
}else{
echo '<div class="col10 last">';
}
echo '
			';
}elseif(empty($this->_tpl_vars['jieqi_sideblocks']['0']) == false){
echo '
			<div class="col3">
				';
if (empty($this->_tpl_vars['jieqi_sideblocks']['0'])) $this->_tpl_vars['jieqi_sideblocks']['0'] = array();
elseif (!is_array($this->_tpl_vars['jieqi_sideblocks']['0'])) $this->_tpl_vars['jieqi_sideblocks']['0'] = (array)$this->_tpl_vars['jieqi_sideblocks']['0'];
$this->_tpl_vars['i']=array();
$this->_tpl_vars['i']['columns'] = 1;
$this->_tpl_vars['i']['count'] = count($this->_tpl_vars['jieqi_sideblocks']['0']);
$this->_tpl_vars['i']['addrows'] = count($this->_tpl_vars['jieqi_sideblocks']['0']) % $this->_tpl_vars['i']['columns'] == 0 ? 0 : $this->_tpl_vars['i']['columns'] - count($this->_tpl_vars['jieqi_sideblocks']['0']) % $this->_tpl_vars['i']['columns'];
$this->_tpl_vars['i']['loops'] = $this->_tpl_vars['i']['count'] + $this->_tpl_vars['i']['addrows'];
reset($this->_tpl_vars['jieqi_sideblocks']['0']);
for($this->_tpl_vars['i']['index'] = 0; $this->_tpl_vars['i']['index'] < $this->_tpl_vars['i']['loops']; $this->_tpl_vars['i']['index']++){
	$this->_tpl_vars['i']['order'] = $this->_tpl_vars['i']['index'] + 1;
	$this->_tpl_vars['i']['row'] = ceil($this->_tpl_vars['i']['order'] / $this->_tpl_vars['i']['columns']);
	$this->_tpl_vars['i']['column'] = $this->_tpl_vars['i']['order'] % $this->_tpl_vars['i']['columns'];
	if($this->_tpl_vars['i']['column'] == 0) $this->_tpl_vars['i']['column'] = $this->_tpl_vars['i']['columns'];
	if($this->_tpl_vars['i']['index'] < $this->_tpl_vars['i']['count']){
		list($this->_tpl_vars['i']['key'], $this->_tpl_vars['i']['value']) = each($this->_tpl_vars['jieqi_sideblocks']['0']);
		$this->_tpl_vars['i']['append'] = 0;
	}else{
		$this->_tpl_vars['i']['key'] = '';
		$this->_tpl_vars['i']['value'] = '';
		$this->_tpl_vars['i']['append'] = 1;
	}
	echo '
				';
if($this->_tpl_vars['jieqi_sideblocks']['0'][$this->_tpl_vars['i']['key']]['title'] != ""){
echo '
				<div class="block">
					<div class="blocktitle">'.$this->_tpl_vars['jieqi_sideblocks']['0'][$this->_tpl_vars['i']['key']]['title'].'</div>
					<div class="blockcontent">'.$this->_tpl_vars['jieqi_sideblocks']['0'][$this->_tpl_vars['i']['key']]['content'].'</div>
				</div>
				';
}else{
echo '
				<div class="blockc">'.$this->_tpl_vars['jieqi_sideblocks']['0'][$this->_tpl_vars['i']['key']]['content'].'</div>
				';
}
echo '
				';
}
echo '
			</div>
			';
if(empty($this->_tpl_vars['jieqi_sideblocks']['1']) == false){
echo '<div class="col6">';
}else{
echo '<div class="col9 last">';
}
echo '
			';
}else{
echo '
			';
if(empty($this->_tpl_vars['jieqi_sideblocks']['1']) == false){
echo '<div class="col9">';
}else{
echo '<div class="col12">';
}
echo '
			';
}
echo '
				';
if(empty($this->_tpl_vars['jieqi_sideblocks']['4']) == false){
echo '
				';
if (empty($this->_tpl_vars['jieqi_sideblocks']['4'])) $this->_tpl_vars['jieqi_sideblocks']['4'] = array();
elseif (!is_array($this->_tpl_vars['jieqi_sideblocks']['4'])) $this->_tpl_vars['jieqi_sideblocks']['4'] = (array)$this->_tpl_vars['jieqi_sideblocks']['4'];
$this->_tpl_vars['i']=array();
$this->_tpl_vars['i']['columns'] = 1;
$this->_tpl_vars['i']['count'] = count($this->_tpl_vars['jieqi_sideblocks']['4']);
$this->_tpl_vars['i']['addrows'] = count($this->_tpl_vars['jieqi_sideblocks']['4']) % $this->_tpl_vars['i']['columns'] == 0 ? 0 : $this->_tpl_vars['i']['columns'] - count($this->_tpl_vars['jieqi_sideblocks']['4']) % $this->_tpl_vars['i']['columns'];
$this->_tpl_vars['i']['loops'] = $this->_tpl_vars['i']['count'] + $this->_tpl_vars['i']['addrows'];
reset($this->_tpl_vars['jieqi_sideblocks']['4']);
for($this->_tpl_vars['i']['index'] = 0; $this->_tpl_vars['i']['index'] < $this->_tpl_vars['i']['loops']; $this->_tpl_vars['i']['index']++){
	$this->_tpl_vars['i']['order'] = $this->_tpl_vars['i']['index'] + 1;
	$this->_tpl_vars['i']['row'] = ceil($this->_tpl_vars['i']['order'] / $this->_tpl_vars['i']['columns']);
	$this->_tpl_vars['i']['column'] = $this->_tpl_vars['i']['order'] % $this->_tpl_vars['i']['columns'];
	if($this->_tpl_vars['i']['column'] == 0) $this->_tpl_vars['i']['column'] = $this->_tpl_vars['i']['columns'];
	if($this->_tpl_vars['i']['index'] < $this->_tpl_vars['i']['count']){
		list($this->_tpl_vars['i']['key'], $this->_tpl_vars['i']['value']) = each($this->_tpl_vars['jieqi_sideblocks']['4']);
		$this->_tpl_vars['i']['append'] = 0;
	}else{
		$this->_tpl_vars['i']['key'] = '';
		$this->_tpl_vars['i']['value'] = '';
		$this->_tpl_vars['i']['append'] = 1;
	}
	echo '
				';
if($this->_tpl_vars['jieqi_sideblocks']['4'][$this->_tpl_vars['i']['key']]['title'] != ""){
echo '
				<div class="block">
						<div class="blocktitle">'.$this->_tpl_vars['jieqi_sideblocks']['4'][$this->_tpl_vars['i']['key']]['title'].'</div>
						<div class="blockcontent">'.$this->_tpl_vars['jieqi_sideblocks']['4'][$this->_tpl_vars['i']['key']]['content'].'</div>
					</div>
				';
}else{
echo '
				<div class="blockc">'.$this->_tpl_vars['jieqi_sideblocks']['4'][$this->_tpl_vars['i']['key']]['content'].'</div>
				';
}
echo '
				';
}
echo '
				';
}
echo '
				';
if(empty($this->_tpl_vars['jieqi_sideblocks']['5']) == false){
echo '
				';
if (empty($this->_tpl_vars['jieqi_sideblocks']['5'])) $this->_tpl_vars['jieqi_sideblocks']['5'] = array();
elseif (!is_array($this->_tpl_vars['jieqi_sideblocks']['5'])) $this->_tpl_vars['jieqi_sideblocks']['5'] = (array)$this->_tpl_vars['jieqi_sideblocks']['5'];
$this->_tpl_vars['i']=array();
$this->_tpl_vars['i']['columns'] = 1;
$this->_tpl_vars['i']['count'] = count($this->_tpl_vars['jieqi_sideblocks']['5']);
$this->_tpl_vars['i']['addrows'] = count($this->_tpl_vars['jieqi_sideblocks']['5']) % $this->_tpl_vars['i']['columns'] == 0 ? 0 : $this->_tpl_vars['i']['columns'] - count($this->_tpl_vars['jieqi_sideblocks']['5']) % $this->_tpl_vars['i']['columns'];
$this->_tpl_vars['i']['loops'] = $this->_tpl_vars['i']['count'] + $this->_tpl_vars['i']['addrows'];
reset($this->_tpl_vars['jieqi_sideblocks']['5']);
for($this->_tpl_vars['i']['index'] = 0; $this->_tpl_vars['i']['index'] < $this->_tpl_vars['i']['loops']; $this->_tpl_vars['i']['index']++){
	$this->_tpl_vars['i']['order'] = $this->_tpl_vars['i']['index'] + 1;
	$this->_tpl_vars['i']['row'] = ceil($this->_tpl_vars['i']['order'] / $this->_tpl_vars['i']['columns']);
	$this->_tpl_vars['i']['column'] = $this->_tpl_vars['i']['order'] % $this->_tpl_vars['i']['columns'];
	if($this->_tpl_vars['i']['column'] == 0) $this->_tpl_vars['i']['column'] = $this->_tpl_vars['i']['columns'];
	if($this->_tpl_vars['i']['index'] < $this->_tpl_vars['i']['count']){
		list($this->_tpl_vars['i']['key'], $this->_tpl_vars['i']['value']) = each($this->_tpl_vars['jieqi_sideblocks']['5']);
		$this->_tpl_vars['i']['append'] = 0;
	}else{
		$this->_tpl_vars['i']['key'] = '';
		$this->_tpl_vars['i']['value'] = '';
		$this->_tpl_vars['i']['append'] = 1;
	}
	echo '
				';
if($this->_tpl_vars['jieqi_sideblocks']['5'][$this->_tpl_vars['i']['key']]['title'] != ""){
echo '
				<div class="block">
						<div class="blocktitle">'.$this->_tpl_vars['jieqi_sideblocks']['5'][$this->_tpl_vars['i']['key']]['title'].'</div>
						<div class="blockcontent">'.$this->_tpl_vars['jieqi_sideblocks']['5'][$this->_tpl_vars['i']['key']]['content'].'</div>
					</div>
				';
}else{
echo '
				<div class="blockc">'.$this->_tpl_vars['jieqi_sideblocks']['5'][$this->_tpl_vars['i']['key']]['content'].'</div>
				';
}
echo '
				';
}
echo '
				';
}
echo '

				';
if($this->_tpl_vars['jieqi_contents'] != ""){
echo '<div id="content">'.$this->_tpl_vars['jieqi_contents'].'</div>';
}
echo '

				';
if(empty($this->_tpl_vars['jieqi_sideblocks']['6']) == false){
echo '
				';
if (empty($this->_tpl_vars['jieqi_sideblocks']['6'])) $this->_tpl_vars['jieqi_sideblocks']['6'] = array();
elseif (!is_array($this->_tpl_vars['jieqi_sideblocks']['6'])) $this->_tpl_vars['jieqi_sideblocks']['6'] = (array)$this->_tpl_vars['jieqi_sideblocks']['6'];
$this->_tpl_vars['i']=array();
$this->_tpl_vars['i']['columns'] = 1;
$this->_tpl_vars['i']['count'] = count($this->_tpl_vars['jieqi_sideblocks']['6']);
$this->_tpl_vars['i']['addrows'] = count($this->_tpl_vars['jieqi_sideblocks']['6']) % $this->_tpl_vars['i']['columns'] == 0 ? 0 : $this->_tpl_vars['i']['columns'] - count($this->_tpl_vars['jieqi_sideblocks']['6']) % $this->_tpl_vars['i']['columns'];
$this->_tpl_vars['i']['loops'] = $this->_tpl_vars['i']['count'] + $this->_tpl_vars['i']['addrows'];
reset($this->_tpl_vars['jieqi_sideblocks']['6']);
for($this->_tpl_vars['i']['index'] = 0; $this->_tpl_vars['i']['index'] < $this->_tpl_vars['i']['loops']; $this->_tpl_vars['i']['index']++){
	$this->_tpl_vars['i']['order'] = $this->_tpl_vars['i']['index'] + 1;
	$this->_tpl_vars['i']['row'] = ceil($this->_tpl_vars['i']['order'] / $this->_tpl_vars['i']['columns']);
	$this->_tpl_vars['i']['column'] = $this->_tpl_vars['i']['order'] % $this->_tpl_vars['i']['columns'];
	if($this->_tpl_vars['i']['column'] == 0) $this->_tpl_vars['i']['column'] = $this->_tpl_vars['i']['columns'];
	if($this->_tpl_vars['i']['index'] < $this->_tpl_vars['i']['count']){
		list($this->_tpl_vars['i']['key'], $this->_tpl_vars['i']['value']) = each($this->_tpl_vars['jieqi_sideblocks']['6']);
		$this->_tpl_vars['i']['append'] = 0;
	}else{
		$this->_tpl_vars['i']['key'] = '';
		$this->_tpl_vars['i']['value'] = '';
		$this->_tpl_vars['i']['append'] = 1;
	}
	echo '
				';
if($this->_tpl_vars['jieqi_sideblocks']['6'][$this->_tpl_vars['i']['key']]['title'] != ""){
echo '
				<div class="block">
						<div class="blocktitle">'.$this->_tpl_vars['jieqi_sideblocks']['6'][$this->_tpl_vars['i']['key']]['title'].'</div>
						<div class="blockcontent">'.$this->_tpl_vars['jieqi_sideblocks']['6'][$this->_tpl_vars['i']['key']]['content'].'</div>
					</div>
				';
}else{
echo '
				<div class="blockc">'.$this->_tpl_vars['jieqi_sideblocks']['6'][$this->_tpl_vars['i']['key']]['content'].'</div>
				';
}
echo '
				';
}
echo '
				';
}
echo '
			</div>

			';
if(empty($this->_tpl_vars['jieqi_sideblocks']['1']) == false){
echo '
			<div class="col3 last">
						';
if (empty($this->_tpl_vars['jieqi_sideblocks']['1'])) $this->_tpl_vars['jieqi_sideblocks']['1'] = array();
elseif (!is_array($this->_tpl_vars['jieqi_sideblocks']['1'])) $this->_tpl_vars['jieqi_sideblocks']['1'] = (array)$this->_tpl_vars['jieqi_sideblocks']['1'];
$this->_tpl_vars['i']=array();
$this->_tpl_vars['i']['columns'] = 1;
$this->_tpl_vars['i']['count'] = count($this->_tpl_vars['jieqi_sideblocks']['1']);
$this->_tpl_vars['i']['addrows'] = count($this->_tpl_vars['jieqi_sideblocks']['1']) % $this->_tpl_vars['i']['columns'] == 0 ? 0 : $this->_tpl_vars['i']['columns'] - count($this->_tpl_vars['jieqi_sideblocks']['1']) % $this->_tpl_vars['i']['columns'];
$this->_tpl_vars['i']['loops'] = $this->_tpl_vars['i']['count'] + $this->_tpl_vars['i']['addrows'];
reset($this->_tpl_vars['jieqi_sideblocks']['1']);
for($this->_tpl_vars['i']['index'] = 0; $this->_tpl_vars['i']['index'] < $this->_tpl_vars['i']['loops']; $this->_tpl_vars['i']['index']++){
	$this->_tpl_vars['i']['order'] = $this->_tpl_vars['i']['index'] + 1;
	$this->_tpl_vars['i']['row'] = ceil($this->_tpl_vars['i']['order'] / $this->_tpl_vars['i']['columns']);
	$this->_tpl_vars['i']['column'] = $this->_tpl_vars['i']['order'] % $this->_tpl_vars['i']['columns'];
	if($this->_tpl_vars['i']['column'] == 0) $this->_tpl_vars['i']['column'] = $this->_tpl_vars['i']['columns'];
	if($this->_tpl_vars['i']['index'] < $this->_tpl_vars['i']['count']){
		list($this->_tpl_vars['i']['key'], $this->_tpl_vars['i']['value']) = each($this->_tpl_vars['jieqi_sideblocks']['1']);
		$this->_tpl_vars['i']['append'] = 0;
	}else{
		$this->_tpl_vars['i']['key'] = '';
		$this->_tpl_vars['i']['value'] = '';
		$this->_tpl_vars['i']['append'] = 1;
	}
	echo '
						';
if($this->_tpl_vars['jieqi_sideblocks']['1'][$this->_tpl_vars['i']['key']]['title'] != ""){
echo '
						<div class="block">
							<div class="blocktitle">'.$this->_tpl_vars['jieqi_sideblocks']['1'][$this->_tpl_vars['i']['key']]['title'].'</div>
							<div class="blockcontent">'.$this->_tpl_vars['jieqi_sideblocks']['1'][$this->_tpl_vars['i']['key']]['content'].'</div>
						</div>
						';
}else{
echo '
						<div class="blockc">'.$this->_tpl_vars['jieqi_sideblocks']['1'][$this->_tpl_vars['i']['key']]['content'].'</div>
						';
}
echo '
						';
}
echo '
					</div>
			';
}
echo '
			<div class="cb"></div>
		</div>

		';
if(empty($this->_tpl_vars['jieqi_sideblocks']['8']) == false){
echo '
		<div class="row">
					<div class="col12">
						';
if (empty($this->_tpl_vars['jieqi_sideblocks']['8'])) $this->_tpl_vars['jieqi_sideblocks']['8'] = array();
elseif (!is_array($this->_tpl_vars['jieqi_sideblocks']['8'])) $this->_tpl_vars['jieqi_sideblocks']['8'] = (array)$this->_tpl_vars['jieqi_sideblocks']['8'];
$this->_tpl_vars['i']=array();
$this->_tpl_vars['i']['columns'] = 1;
$this->_tpl_vars['i']['count'] = count($this->_tpl_vars['jieqi_sideblocks']['8']);
$this->_tpl_vars['i']['addrows'] = count($this->_tpl_vars['jieqi_sideblocks']['8']) % $this->_tpl_vars['i']['columns'] == 0 ? 0 : $this->_tpl_vars['i']['columns'] - count($this->_tpl_vars['jieqi_sideblocks']['8']) % $this->_tpl_vars['i']['columns'];
$this->_tpl_vars['i']['loops'] = $this->_tpl_vars['i']['count'] + $this->_tpl_vars['i']['addrows'];
reset($this->_tpl_vars['jieqi_sideblocks']['8']);
for($this->_tpl_vars['i']['index'] = 0; $this->_tpl_vars['i']['index'] < $this->_tpl_vars['i']['loops']; $this->_tpl_vars['i']['index']++){
	$this->_tpl_vars['i']['order'] = $this->_tpl_vars['i']['index'] + 1;
	$this->_tpl_vars['i']['row'] = ceil($this->_tpl_vars['i']['order'] / $this->_tpl_vars['i']['columns']);
	$this->_tpl_vars['i']['column'] = $this->_tpl_vars['i']['order'] % $this->_tpl_vars['i']['columns'];
	if($this->_tpl_vars['i']['column'] == 0) $this->_tpl_vars['i']['column'] = $this->_tpl_vars['i']['columns'];
	if($this->_tpl_vars['i']['index'] < $this->_tpl_vars['i']['count']){
		list($this->_tpl_vars['i']['key'], $this->_tpl_vars['i']['value']) = each($this->_tpl_vars['jieqi_sideblocks']['8']);
		$this->_tpl_vars['i']['append'] = 0;
	}else{
		$this->_tpl_vars['i']['key'] = '';
		$this->_tpl_vars['i']['value'] = '';
		$this->_tpl_vars['i']['append'] = 1;
	}
	echo '
						';
if($this->_tpl_vars['jieqi_sideblocks']['8'][$this->_tpl_vars['i']['key']]['title'] != ""){
echo '
						<div class="block">
							<div class="blocktitle">'.$this->_tpl_vars['jieqi_sideblocks']['8'][$this->_tpl_vars['i']['key']]['title'].'</div>
							<div class="blockcontent">'.$this->_tpl_vars['jieqi_sideblocks']['8'][$this->_tpl_vars['i']['key']]['content'].'</div>
						</div>
						';
}else{
echo '
						<div class="blockc">'.$this->_tpl_vars['jieqi_sideblocks']['8'][$this->_tpl_vars['i']['key']]['content'].'</div>
						';
}
echo '
						';
}
echo '
					</div>
				</div>
		';
}
echo '

		';
}else{
echo '
		<div id="content" class="row">'.$this->_tpl_vars['jieqi_contents'].'</div>
		';
}
echo '

		';
if($this->_tpl_vars['jieqi_bottom_bar'] != ""){
echo '<div class="row"><div class="col12">'.$this->_tpl_vars['jieqi_bottom_bar'].'</div></div>';
}
echo '
		<div class="cb"></div>
    ';
if(empty($this->_tpl_vars['jieqi_sideblocks']) == false){
echo '</div>';
}
echo '
</div>

<div class="footer">
	<div class="main">
		<p><a href="'.$this->_tpl_vars['jieqi_url'].'/page.php?bid=11">关于本站</a>&emsp;|&emsp;<a href="'.$this->_tpl_vars['jieqi_url'].'/page.php?bid=12">联系我们</a>&emsp;|&emsp;<a href="'.$this->_tpl_vars['jieqi_url'].'/page.php?bid=13">用户指南</a>&emsp;|&emsp;<a href="'.$this->_tpl_vars['jieqi_url'].'/page.php?bid=14">版权声明</a>&emsp;|&emsp;<a href="'.$this->_tpl_vars['jieqi_url'].'/page.php?bid=15">作家福利</a>';
if($this->_tpl_vars['jieqi_mobile_lcocation'] != ''){
echo '&emsp;|&emsp;<a href="'.$this->_tpl_vars['jieqi_mobile_lcocation'].'/?device=wap">手机版</a>';
}
echo '</p>
		<p>Powered by <strong><a href="http://www.jieqi.com" target="_blank">JIEQI CMS</a></strong> &copy; 2004-'.date('Y',$this->_tpl_vars['jieqi_time']).' <a href="http://www.jieqi.com" target="_blank">杰奇网络（jieqi.com）</a></p>
		<p>'.date('Y-m-d H:i:s',$this->_tpl_vars['jieqi_time']).', Processed in '.$this->_tpl_vars['jieqi_exetime'].' second(s).</p>
	</div>
</div>

</body>
</html>';
?>