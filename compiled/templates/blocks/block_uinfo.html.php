<?php
if($this->_tpl_vars['ownerid'] > 0){
echo '
<div style="padding-left:10px">
<div>
  ';
if($this->_tpl_vars['avatar'] > 0){
echo '<img src="'.jieqi_geturl('system','avatar',$this->_tpl_vars['uid'],'s',$this->_tpl_vars['avatar']).'" class="avatars" style="float:left; margin:10px 10px 0 0;" alt="'.$this->_tpl_vars['name'].'">';
}
echo '
  <p><a href="'.jieqi_geturl('system','user',$this->_tpl_vars['uid'],'info').'"><strong>'.$this->_tpl_vars['name'].'</strong></a></p>
  ';
if($this->_tpl_vars['jieqi_modules']['badge']['publish'] > 0){
echo '
    ';
if($this->_tpl_vars['url_group'] != ""){
echo '<p><img src="'.$this->_tpl_vars['url_group'].'" border="0" title="'.$this->_tpl_vars['group'].'"></p>';
}
echo '
    ';
if($this->_tpl_vars['url_honor'] != ""){
echo '<p><img src="'.$this->_tpl_vars['url_honor'].'" border="0" title="'.$this->_tpl_vars['honor'].'"></p>';
}
echo '
    ';
if(count($this->_tpl_vars['badgerows']) > 0){
echo '<p>';
}
echo '
    ';
if (empty($this->_tpl_vars['badgerows'])) $this->_tpl_vars['badgerows'] = array();
elseif (!is_array($this->_tpl_vars['badgerows'])) $this->_tpl_vars['badgerows'] = (array)$this->_tpl_vars['badgerows'];
$this->_tpl_vars['i']=array();
$this->_tpl_vars['i']['columns'] = 1;
$this->_tpl_vars['i']['count'] = count($this->_tpl_vars['badgerows']);
$this->_tpl_vars['i']['addrows'] = count($this->_tpl_vars['badgerows']) % $this->_tpl_vars['i']['columns'] == 0 ? 0 : $this->_tpl_vars['i']['columns'] - count($this->_tpl_vars['badgerows']) % $this->_tpl_vars['i']['columns'];
$this->_tpl_vars['i']['loops'] = $this->_tpl_vars['i']['count'] + $this->_tpl_vars['i']['addrows'];
reset($this->_tpl_vars['badgerows']);
for($this->_tpl_vars['i']['index'] = 0; $this->_tpl_vars['i']['index'] < $this->_tpl_vars['i']['loops']; $this->_tpl_vars['i']['index']++){
	$this->_tpl_vars['i']['order'] = $this->_tpl_vars['i']['index'] + 1;
	$this->_tpl_vars['i']['row'] = ceil($this->_tpl_vars['i']['order'] / $this->_tpl_vars['i']['columns']);
	$this->_tpl_vars['i']['column'] = $this->_tpl_vars['i']['order'] % $this->_tpl_vars['i']['columns'];
	if($this->_tpl_vars['i']['column'] == 0) $this->_tpl_vars['i']['column'] = $this->_tpl_vars['i']['columns'];
	if($this->_tpl_vars['i']['index'] < $this->_tpl_vars['i']['count']){
		list($this->_tpl_vars['i']['key'], $this->_tpl_vars['i']['value']) = each($this->_tpl_vars['badgerows']);
		$this->_tpl_vars['i']['append'] = 0;
	}else{
		$this->_tpl_vars['i']['key'] = '';
		$this->_tpl_vars['i']['value'] = '';
		$this->_tpl_vars['i']['append'] = 1;
	}
	echo '
      <img src="'.$this->_tpl_vars['badgerows'][$this->_tpl_vars['i']['key']]['imageurl'].'" border="0" alt="'.$this->_tpl_vars['badgerows'][$this->_tpl_vars['i']['key']]['caption'].'">
    ';
}
echo '
    ';
if(count($this->_tpl_vars['badgerows']) > 0){
echo '</p>';
}
echo '
  ';
}else{
echo '
   <p>'.$this->_tpl_vars['group'].'</p>
    <p>'.$this->_tpl_vars['honor'].'</p>
  ';
}
echo '
</div>
<div class="cb"></div>
<ul class="ulcenter" style="margin:10px 0">
  <li style="width:50%;float:left;"><a href="javascript:;" onclick="openDialog(\''.$this->_tpl_vars['jieqi_url'].'/newmessage.php?receiver='.urlencode($this->_tpl_vars['name']).'&ajax_gets=jieqi_contents\', false);" class="message">发送消息</a></li>
  <li style="width:50%;float:left;"><a id="addfriends'.$this->_tpl_vars['uid'].'" href="javascript:;" onclick="Ajax.Tip(\''.$this->_tpl_vars['jieqi_url'].'/addfriends.php?id='.$this->_tpl_vars['uid'].'&act=add'.$this->_tpl_vars['jieqi_token_url'].'\', {method: \'POST\'});" class="friend">加为好友</a></li>
  ';
if($this->_tpl_vars['jieqi_modules']['space']['publish']==1){
echo '
  <li style="width:50%;float:left;"><a href="'.jieqi_geturl('system','user',$this->_tpl_vars['uid'],'space').'" class="space">个人空间</a></li>
  ';
}else{
echo '
  <li style="width:50%;float:left;"><a href="'.jieqi_geturl('system','user',$this->_tpl_vars['uid'],'info').'"  class="userinfo">查看资料</a></li>
  ';
}
echo '
  <li style="width:50%;float:left;"><a href="'.$this->_tpl_vars['jieqi_url'].'/ptopics.php?oid='.$this->_tpl_vars['uid'].'"  class="parlor">会 客 室</a></li>
</ul>
<div class="cb"></div>
</div>
';
}

?>