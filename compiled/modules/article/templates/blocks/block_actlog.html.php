<?php
echo '<ul class="ultop">
';
if (empty($this->_tpl_vars['actlogrows'])) $this->_tpl_vars['actlogrows'] = array();
elseif (!is_array($this->_tpl_vars['actlogrows'])) $this->_tpl_vars['actlogrows'] = (array)$this->_tpl_vars['actlogrows'];
$this->_tpl_vars['i']=array();
$this->_tpl_vars['i']['columns'] = 1;
$this->_tpl_vars['i']['count'] = count($this->_tpl_vars['actlogrows']);
$this->_tpl_vars['i']['addrows'] = count($this->_tpl_vars['actlogrows']) % $this->_tpl_vars['i']['columns'] == 0 ? 0 : $this->_tpl_vars['i']['columns'] - count($this->_tpl_vars['actlogrows']) % $this->_tpl_vars['i']['columns'];
$this->_tpl_vars['i']['loops'] = $this->_tpl_vars['i']['count'] + $this->_tpl_vars['i']['addrows'];
reset($this->_tpl_vars['actlogrows']);
for($this->_tpl_vars['i']['index'] = 0; $this->_tpl_vars['i']['index'] < $this->_tpl_vars['i']['loops']; $this->_tpl_vars['i']['index']++){
	$this->_tpl_vars['i']['order'] = $this->_tpl_vars['i']['index'] + 1;
	$this->_tpl_vars['i']['row'] = ceil($this->_tpl_vars['i']['order'] / $this->_tpl_vars['i']['columns']);
	$this->_tpl_vars['i']['column'] = $this->_tpl_vars['i']['order'] % $this->_tpl_vars['i']['columns'];
	if($this->_tpl_vars['i']['column'] == 0) $this->_tpl_vars['i']['column'] = $this->_tpl_vars['i']['columns'];
	if($this->_tpl_vars['i']['index'] < $this->_tpl_vars['i']['count']){
		list($this->_tpl_vars['i']['key'], $this->_tpl_vars['i']['value']) = each($this->_tpl_vars['actlogrows']);
		$this->_tpl_vars['i']['append'] = 0;
	}else{
		$this->_tpl_vars['i']['key'] = '';
		$this->_tpl_vars['i']['value'] = '';
		$this->_tpl_vars['i']['append'] = 1;
	}
	echo '
  <li>
  <span class="fr">'.date('H:i',$this->_tpl_vars['actlogrows'][$this->_tpl_vars['i']['key']]['addtime']).'</span><a href="'.jieqi_geturl('system','user',$this->_tpl_vars['actlogrows'][$this->_tpl_vars['i']['key']]['uid'],'info').'" target="_blank">'.$this->_tpl_vars['actlogrows'][$this->_tpl_vars['i']['key']]['uname'].'</a>
  <br />
  ';
if($this->_tpl_vars['actlogrows'][$this->_tpl_vars['i']['key']]['actname'] == 'tip'){
echo '
    ';
if($this->_tpl_vars['actlogrows'][$this->_tpl_vars['i']['key']]['acttype'] > 0){
echo '赠送 '.$this->_tpl_vars['actlogrows'][$this->_tpl_vars['i']['key']]['actnum'].' '.$this->_tpl_vars['tiptyperows'][$this->_tpl_vars['actlogrows'][$this->_tpl_vars['i']['key']]['acttype']]['unit'].$this->_tpl_vars['tiptyperows'][$this->_tpl_vars['actlogrows'][$this->_tpl_vars['i']['key']]['acttype']]['caption'];
}else{
echo '打赏 '.$this->_tpl_vars['actlogrows'][$this->_tpl_vars['i']['key']]['actnum'].' '.$this->_tpl_vars['egoldname'];
}
echo '
  ';
}elseif($this->_tpl_vars['actlogrows'][$this->_tpl_vars['i']['key']]['actname'] == 'vipvote'){
echo '
  投了 '.$this->_tpl_vars['actlogrows'][$this->_tpl_vars['i']['key']]['actnum'].' 张月票
  ';
}
echo '
  </li>
';
}
echo '
</ul>';
?>