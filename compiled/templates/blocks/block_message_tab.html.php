<?php
echo '<ul class="ultab">
<li><a href="'.$this->_tpl_vars['jieqi_url'].'/message.php?box=inbox"';
if(isset($this->_tpl_vars['_request']['box']) && $this->_tpl_vars['_request']['box'] == 'inbox'){
echo ' class="selected"';
}
echo '><i class="iconfont">&#xee36;</i>收件箱</a></li>
<li><a href="'.$this->_tpl_vars['jieqi_url'].'/message.php?box=outbox"';
if(isset($this->_tpl_vars['_request']['box']) && $this->_tpl_vars['_request']['box'] == 'outbox'){
echo ' class="selected"';
}
echo '><i class="iconfont">&#xee37;</i>发件箱</a></li>
<li><a href="'.$this->_tpl_vars['jieqi_url'].'/newmessage.php"';
if(basename($this->_tpl_vars['jieqi_thisfile']) == 'newmessage.php' && empty($this->_tpl_vars['_request']['tosys'])){
echo ' class="selected"';
}
echo '><i class="iconfont">&#xee38;</i>写新消息</a></li>
<li><a href="'.$this->_tpl_vars['jieqi_url'].'/newmessage.php?tosys=1"';
if(basename($this->_tpl_vars['jieqi_thisfile']) == 'newmessage.php' && isset($this->_tpl_vars['_request']['tosys']) && $this->_tpl_vars['_request']['tosys'] == 1){
echo ' class="selected"';
}
echo '><i class="iconfont">&#xee39;</i>写给管理员</a></li>
</ul>';
?>