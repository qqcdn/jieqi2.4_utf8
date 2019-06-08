<?php
echo '

<div class="blockb">
<div class="blockcontent" id="jieqi_page_contents">
	<ul class="ulrow">
	';
if (empty($this->_tpl_vars['paylogrows'])) $this->_tpl_vars['paylogrows'] = array();
elseif (!is_array($this->_tpl_vars['paylogrows'])) $this->_tpl_vars['paylogrows'] = (array)$this->_tpl_vars['paylogrows'];
$this->_tpl_vars['i']=array();
$this->_tpl_vars['i']['columns'] = 1;
$this->_tpl_vars['i']['count'] = count($this->_tpl_vars['paylogrows']);
$this->_tpl_vars['i']['addrows'] = count($this->_tpl_vars['paylogrows']) % $this->_tpl_vars['i']['columns'] == 0 ? 0 : $this->_tpl_vars['i']['columns'] - count($this->_tpl_vars['paylogrows']) % $this->_tpl_vars['i']['columns'];
$this->_tpl_vars['i']['loops'] = $this->_tpl_vars['i']['count'] + $this->_tpl_vars['i']['addrows'];
reset($this->_tpl_vars['paylogrows']);
for($this->_tpl_vars['i']['index'] = 0; $this->_tpl_vars['i']['index'] < $this->_tpl_vars['i']['loops']; $this->_tpl_vars['i']['index']++){
	$this->_tpl_vars['i']['order'] = $this->_tpl_vars['i']['index'] + 1;
	$this->_tpl_vars['i']['row'] = ceil($this->_tpl_vars['i']['order'] / $this->_tpl_vars['i']['columns']);
	$this->_tpl_vars['i']['column'] = $this->_tpl_vars['i']['order'] % $this->_tpl_vars['i']['columns'];
	if($this->_tpl_vars['i']['column'] == 0) $this->_tpl_vars['i']['column'] = $this->_tpl_vars['i']['columns'];
	if($this->_tpl_vars['i']['index'] < $this->_tpl_vars['i']['count']){
		list($this->_tpl_vars['i']['key'], $this->_tpl_vars['i']['value']) = each($this->_tpl_vars['paylogrows']);
		$this->_tpl_vars['i']['append'] = 0;
	}else{
		$this->_tpl_vars['i']['key'] = '';
		$this->_tpl_vars['i']['value'] = '';
		$this->_tpl_vars['i']['append'] = 1;
	}
	echo '
	<li>
	<div><em>'.$this->_tpl_vars['paylogrows'][$this->_tpl_vars['i']['key']]['payflag'].'</em>'.date('m-d H:i',$this->_tpl_vars['paylogrows'][$this->_tpl_vars['i']['key']]['buytime']).'购买';
if($this->_tpl_vars['paylogrows'][$this->_tpl_vars['i']['key']]['actname'] == ''){
echo $this->_tpl_vars['paylogrows'][$this->_tpl_vars['i']['key']]['egold'].$this->_tpl_vars['egoldname'];
}else{
echo $this->_tpl_vars['paylogrows'][$this->_tpl_vars['i']['key']]['actname'];
}
echo '</div>
	<div class="gray"><em>'.$this->_tpl_vars['paylogrows'][$this->_tpl_vars['i']['key']]['paytype'].'</em>流水号：'.$this->_tpl_vars['paylogrows'][$this->_tpl_vars['i']['key']]['payid'].'</div>
	</li>
	';
}
echo '
	</ul>
</div>
</div>

<div class="pages">'.$this->_tpl_vars['url_jumppage'].'</div>
';
?>