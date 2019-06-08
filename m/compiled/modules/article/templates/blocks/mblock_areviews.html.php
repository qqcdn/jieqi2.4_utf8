<?php
echo '<a class="moretop" href="'.jieqi_geturl('article','reviews','1',$this->_tpl_vars['reviewaid'],'').'">显示更多<i class="iconfont">&#xee6a;</i></a>
<ul class="ullist">
';
if (empty($this->_tpl_vars['reviewrows'])) $this->_tpl_vars['reviewrows'] = array();
elseif (!is_array($this->_tpl_vars['reviewrows'])) $this->_tpl_vars['reviewrows'] = (array)$this->_tpl_vars['reviewrows'];
$this->_tpl_vars['i']=array();
$this->_tpl_vars['i']['columns'] = 1;
$this->_tpl_vars['i']['count'] = count($this->_tpl_vars['reviewrows']);
$this->_tpl_vars['i']['addrows'] = count($this->_tpl_vars['reviewrows']) % $this->_tpl_vars['i']['columns'] == 0 ? 0 : $this->_tpl_vars['i']['columns'] - count($this->_tpl_vars['reviewrows']) % $this->_tpl_vars['i']['columns'];
$this->_tpl_vars['i']['loops'] = $this->_tpl_vars['i']['count'] + $this->_tpl_vars['i']['addrows'];
reset($this->_tpl_vars['reviewrows']);
for($this->_tpl_vars['i']['index'] = 0; $this->_tpl_vars['i']['index'] < $this->_tpl_vars['i']['loops']; $this->_tpl_vars['i']['index']++){
	$this->_tpl_vars['i']['order'] = $this->_tpl_vars['i']['index'] + 1;
	$this->_tpl_vars['i']['row'] = ceil($this->_tpl_vars['i']['order'] / $this->_tpl_vars['i']['columns']);
	$this->_tpl_vars['i']['column'] = $this->_tpl_vars['i']['order'] % $this->_tpl_vars['i']['columns'];
	if($this->_tpl_vars['i']['column'] == 0) $this->_tpl_vars['i']['column'] = $this->_tpl_vars['i']['columns'];
	if($this->_tpl_vars['i']['index'] < $this->_tpl_vars['i']['count']){
		list($this->_tpl_vars['i']['key'], $this->_tpl_vars['i']['value']) = each($this->_tpl_vars['reviewrows']);
		$this->_tpl_vars['i']['append'] = 0;
	}else{
		$this->_tpl_vars['i']['key'] = '';
		$this->_tpl_vars['i']['value'] = '';
		$this->_tpl_vars['i']['append'] = 1;
	}
	echo '
	<li>
	<a class="db cf" href="'.jieqi_geturl('article','reviewshow','1',$this->_tpl_vars['reviewrows'][$this->_tpl_vars['i']['key']]['topicid']).'">
	<em>'.date('Y-m-d H:i:s',$this->_tpl_vars['reviewrows'][$this->_tpl_vars['i']['key']]['posttime']).'</em><b>';
if($this->_tpl_vars['reviewrows'][$this->_tpl_vars['i']['key']]['posterid'] > 0){
echo $this->_tpl_vars['reviewrows'][$this->_tpl_vars['i']['key']]['poster'];
}else{
echo '游客';
}
echo '：</b>
	<p>';
if($this->_tpl_vars['reviewrows'][$this->_tpl_vars['i']['key']]['istop'] == 1){
echo '<span class="pop">[顶]</span>';
}
if($this->_tpl_vars['reviewrows'][$this->_tpl_vars['i']['key']]['isgood'] == 1){
echo '<span class="pop">[精]</span>';
}
echo truncate($this->_tpl_vars['reviewrows'][$this->_tpl_vars['i']['key']]['posttext'],'320','..').'</p>
	</a>
	</li>
';
}
echo '
</ul>';
?>