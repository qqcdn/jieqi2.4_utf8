<?php
if(count($this->_tpl_vars['reviewrows']) > 0){
echo '
<div class="cf bb">
<ul class="ultab fl">
<li><a href="javascript:;" class="selected">&nbsp;&nbsp;最新书评&nbsp;&nbsp;</a></li>
</ul>
<span class="fr">
<a href="'.jieqi_geturl('article','reviews','1',$this->_tpl_vars['reviewaid'],'good').'" target="_blank">精华书评</a> |
<a href="'.jieqi_geturl('article','reviews','1',$this->_tpl_vars['reviewaid'],'').'" target="_blank">全部书评</a>
</span>
</div>
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
	<div class="c_row cf">
		<div style="float: left; width: 100px; text-align: center;">
		';
if($this->_tpl_vars['reviewrows'][$this->_tpl_vars['i']['key']]['posterid'] > 0){
echo '
		<a href="'.jieqi_geturl('system','user',$this->_tpl_vars['reviewrows'][$this->_tpl_vars['i']['key']]['posterid']).'" target="_blank"><img class="avatars" src="'.jieqi_geturl('system','avatar',$this->_tpl_vars['reviewrows'][$this->_tpl_vars['i']['key']]['posterid'],'s','2').'" onerror="this.src=\''.$this->_tpl_vars['jieqi_url'].'/images/noavatars.jpg\';this.onerror=null;" /><p';
if($this->_tpl_vars['reviewrows'][$this->_tpl_vars['i']['key']]['posterid'] == $this->_tpl_vars['authorid']){
echo ' class="hot"';
}
echo '>'.$this->_tpl_vars['reviewrows'][$this->_tpl_vars['i']['key']]['poster'].'</p></a>
		';
}else{
echo '
		<img class="avatars" src="'.$this->_tpl_vars['jieqi_url'].'/images/noavatars.jpg" />
		<p class="gray">游客</p>
		';
}
echo '
		<a href="'.jieqi_geturl('system','user',$this->_tpl_vars['reviewrows'][$this->_tpl_vars['i']['key']]['posterid']).'" target="_blank">
		</a>
		</div>
		<div style="margin-left: 100px;">
			<div class="c_subject">';
if($this->_tpl_vars['reviewrows'][$this->_tpl_vars['i']['key']]['istop'] == 1){
echo '<span class="pop">[顶]</span>';
}
if($this->_tpl_vars['reviewrows'][$this->_tpl_vars['i']['key']]['isgood'] == 1){
echo '<span class="pop">[精]</span>';
}
echo '<a class="pop" href="'.jieqi_geturl('article','reviewshow','1',$this->_tpl_vars['reviewrows'][$this->_tpl_vars['i']['key']]['topicid']).'" target="_blank">'.truncate(str_replace('<br />',' ',$this->_tpl_vars['reviewrows'][$this->_tpl_vars['i']['key']]['title']),'80','..').'</a></div>
			<div class="c_description">'.truncate($this->_tpl_vars['reviewrows'][$this->_tpl_vars['i']['key']]['content'],'320','..').'</div>
			<div class="c_tag">
				<span class="fr"><a href="'.jieqi_geturl('article','reviewshow','1',$this->_tpl_vars['reviewrows'][$this->_tpl_vars['i']['key']]['topicid']).'#postnew" target="_blank">[我要回复]</a></span>
				<span class="c_label">时间：</span><span class="c_value">'.date('Y-m-d H:i:s',$this->_tpl_vars['reviewrows'][$this->_tpl_vars['i']['key']]['replytime']).'</span>
				<span class="c_label">点击：</span><span class="c_value">'.$this->_tpl_vars['reviewrows'][$this->_tpl_vars['i']['key']]['views'].'</span>
				<span class="c_label">回复：</span><span class="c_value">'.$this->_tpl_vars['reviewrows'][$this->_tpl_vars['i']['key']]['replies'].'</span>
			</div>
		</div>
	</div>
';
}
}

?>