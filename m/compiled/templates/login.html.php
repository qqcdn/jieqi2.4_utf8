<?php
echo '

<form class="form cf" name="frmlogin" method="post" action="'.$this->_tpl_vars['url_login'].'">
	<fieldset>
		<div class="frow">
			<label class="col4 flabel">用户名：</label>
			<div class="col8 last">
				<input type="text" class="text" maxlength="30" name="username" onKeyPress="javascript: if (event.keyCode == 32 || event.which == 32) return false;">
			</div>
		</div>

		<div class="frow">
			<label class="col4 flabel">密　码：</label>
			<div class="col8 last">
			<input type="password" class="text" maxlength="30" name="password">
			</div>
		</div>
		';
if($this->_tpl_vars['show_checkcode'] == 1){
echo '
		<div class="frow">
			<label class="col4 flabel">验证码：</label>
			<div class="col8 last">
			<input type="text" class="text" style="width:6em;" name="checkcode" id="checkcode" onfocus="if(this.form.imgccode.style.display == \'none\'){this.form.imgccode.src = \''.$this->_tpl_vars['jieqi_url'].'/checkcode.php?rand='.$this->_tpl_vars['jieqi_time'].'\';this.form.imgccode.style.display = \'\';}" title="点击显示验证码"><img name="imgccode" src="" style="cursor:pointer;vertical-align:middle;margin-left:3px;display:none;" onclick="this.src=\''.$this->_tpl_vars['jieqi_url'].'/checkcode.php?rand=\'+Math.random();" title="点击刷新验证码">
			</div>
		</div>
		';
}
echo '

		<div class="frow">
			<label class="col4 flabel"><input type="hidden" name="act" value="login"/><input type="hidden" name="usecookie" value="2592000"/></label>
			<div class="col8 last">
			<button type="submit" class="button" name="submit">登　录</button>
			</div>
		</div>

		';
if($this->_tpl_vars['jieqi_api_sites']['qq']['publish'] > 0 || $this->_tpl_vars['jieqi_api_sites']['weibo']['publish'] > 0){
echo '
		<div class="frow">
			<label class="col4 flabel">&nbsp;</label>
			<div class="col8 last">
			<ul class="ullist">
				<li>【其它账号登录】</li>
				';
if($this->_tpl_vars['jieqi_api_sites']['qq']['publish'] > 0){
echo '<li><a href="'.$this->_tpl_vars['jieqi_url'].'/api/qq/login.php'.$this->_tpl_vars['jumpquery'].'"><img src="'.$this->_tpl_vars['jieqi_url'].'/images/api/qq_login.gif" alt="用QQ账号登录" border="0"></a></li>';
}
echo '
				';
if($this->_tpl_vars['jieqi_api_sites']['weibo']['publish'] > 0){
echo '<li><a href="'.$this->_tpl_vars['jieqi_url'].'/api/weibo/login.php'.$this->_tpl_vars['jumpquery'].'"><img src="'.$this->_tpl_vars['jieqi_url'].'/images/api/weibo_login.gif" alt="用新浪微博账号登录" border="0"></a></li>';
}
echo '
				';
if($this->_tpl_vars['jieqi_api_sites']['weixin']['publish'] > 0 && $this->_tpl_vars['jieqi_browser'] == 'weixin'){
echo '<li><a href="'.$this->_tpl_vars['jieqi_url'].'/api/wxmp/login.php'.$this->_tpl_vars['jumpquery'].'"><img src="'.$this->_tpl_vars['jieqi_url'].'/images/api/weixin_login.gif" alt="用微信账号登录" border="0"></a></li>';
}
echo '
			</ul>
			</div>
		</div>
		';
}
echo '

		<div class="frow foot">
			<a href="'.$this->_tpl_vars['url_register'].'">注册账号</a> &nbsp;&nbsp;|&nbsp;&nbsp; <a href="'.$this->_tpl_vars['url_getpass'].'">忘记密码？</a>
		</div>
	</fieldset>
</form>';
?>