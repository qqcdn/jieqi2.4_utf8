<?php
echo '<ul class="ultab">
<li><a href="'.$this->_tpl_vars['jieqi_url'].'/userdetail.php"';
if(basename($this->_tpl_vars['jieqi_thisfile']) == 'userdetail.php'){
echo ' class="selected"';
}
echo '><i class="iconfont">&#xee21;</i>用户资料</a></li>
<li><a href="'.$this->_tpl_vars['jieqi_url'].'/useredit.php"';
if(basename($this->_tpl_vars['jieqi_thisfile']) == 'useredit.php'){
echo ' class="selected"';
}
echo '><i class="iconfont">&#xee4a;</i>修改资料</a></li>
<li><a href="'.$this->_tpl_vars['jieqi_url'].'/setavatar.php"';
if(basename($this->_tpl_vars['jieqi_thisfile']) == 'setavatar.php'){
echo ' class="selected"';
}
echo '><i class="iconfont">&#xee25;</i>设置头像</a></li>
<li><a href="'.$this->_tpl_vars['jieqi_url'].'/passedit.php"';
if(basename($this->_tpl_vars['jieqi_thisfile']) == 'passedit.php'){
echo ' class="selected"';
}
echo '><i class="iconfont">&#xee29;</i>修改密码</a></li>
</ul>';
?>