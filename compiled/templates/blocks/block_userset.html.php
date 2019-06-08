<?php
echo '<ul class="ulnav">
<li><i class="iconfont">&#xee21;</i><a href="'.$this->_tpl_vars['jieqi_url'].'/userdetail.php"';
if(basename($this->_tpl_vars['jieqi_thisfile']) == 'userdetail.php'){
echo ' class="selected"';
}
echo '>用户资料</a></li>
<li><i class="iconfont">&#xee4a;</i><a href="'.$this->_tpl_vars['jieqi_url'].'/useredit.php"';
if(basename($this->_tpl_vars['jieqi_thisfile']) == 'useredit.php'){
echo ' class="selected"';
}
echo '>修改资料</a></li>
<li><i class="iconfont">&#xee24;</i><a href="'.$this->_tpl_vars['jieqi_url'].'/persondetail.php"';
if(basename($this->_tpl_vars['jieqi_thisfile']) == 'persondetail.php'){
echo ' class="selected"';
}
echo '>联系方式</a></li>
<li><i class="iconfont">&#xee25;</i><a href="'.$this->_tpl_vars['jieqi_url'].'/setavatar.php"';
if(basename($this->_tpl_vars['jieqi_thisfile']) == 'setavatar.php'){
echo ' class="selected"';
}
echo '>设置头像</a></li>
';
if($this->_tpl_vars['jieqi_api_oauth'] > 0){
echo '<li><i class="iconfont">&#xee24;</i><a href="'.$this->_tpl_vars['jieqi_url'].'/userbind.php"';
if(basename($this->_tpl_vars['jieqi_thisfile']) == 'userbind.php'){
echo ' class="selected"';
}
echo '>账号绑定</a></li>';
}
echo '
<li><i class="iconfont">&#xee29;</i><a href="'.$this->_tpl_vars['jieqi_url'].'/passedit.php"';
if(basename($this->_tpl_vars['jieqi_thisfile']) == 'passedit.php'){
echo ' class="selected"';
}
echo '>修改密码</a></li>
</ul>';
?>