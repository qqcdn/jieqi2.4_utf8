<?php
echo '<ul class="ultab">
';
if($this->_tpl_vars['jieqi_modules']['article']['publish'] > 0){
echo '<li><a href="'.$this->_tpl_vars['jieqi_modules']['article']['url'].'/bookcase.php"';
if(basename($this->_tpl_vars['jieqi_thisfile']) == 'bookcase.php'){
echo ' class="selected"';
}
echo '><i class="iconfont">&#xee43;</i>我的书架</a></li>';
}
if($this->_tpl_vars['jieqi_modules']['article']['publish'] > 0){
echo '<li><a href="'.$this->_tpl_vars['jieqi_modules']['article']['url'].'/myreviews.php"';
if(basename($this->_tpl_vars['jieqi_thisfile']) == 'myreviews.php'){
echo ' class="selected"';
}
echo '><i class="iconfont">&#xee4c;</i>我的书评</a></li>';
}
if($this->_tpl_vars['jieqi_modules']['obook']['publish'] > 0){
echo '<li><a href="'.$this->_tpl_vars['jieqi_modules']['obook']['url'].'/buylist.php"';
if(basename($this->_tpl_vars['jieqi_thisfile']) == 'buylist.php'){
echo ' class="selected"';
}
echo '><i class="iconfont">&#xee45;</i>我的订阅</a></li>';
}
if($this->_tpl_vars['jieqi_modules']['article']['publish'] > 0){
echo '<li><a href="'.$this->_tpl_vars['jieqi_modules']['article']['url'].'/myactlog.php?act=tip"';
if(basename($this->_tpl_vars['jieqi_thisfile']) == 'myactlog.php' && $this->_tpl_vars['_request']['act'] == 'tip'){
echo ' class="selected"';
}
echo '><i class="iconfont">&#xee3f;</i>打赏记录</a></li>';
}
if($this->_tpl_vars['jieqi_modules']['pay']['publish'] > 0){
echo '<li><a href="'.$this->_tpl_vars['jieqi_modules']['pay']['url'].'/paylog.php"';
if(basename($this->_tpl_vars['jieqi_thisfile']) == 'paylog.php'){
echo ' class="selected"';
}
echo '><i class="iconfont">&#xee41;</i>充值记录</a></li>';
}
echo '
<li><a href="'.$this->_tpl_vars['jieqi_url'].'/myfriends.php"';
if(basename($this->_tpl_vars['jieqi_thisfile']) == 'myfriends.php'){
echo ' class="selected"';
}
echo '><i class="iconfont">&#xee23;</i>我的好友</a></li>
<li><a href="'.$this->_tpl_vars['jieqi_url'].'/myptopics.php"';
if(basename($this->_tpl_vars['jieqi_thisfile']) == 'myptopics.php'){
echo ' class="selected"';
}
echo '><i class="iconfont">&#xee3a;</i>我的留言</a></li>
</ul>';
?>