<?php
if (empty($this->_tpl_vars['articlerows'])) $this->_tpl_vars['articlerows'] = array();
elseif (!is_array($this->_tpl_vars['articlerows'])) $this->_tpl_vars['articlerows'] = (array)$this->_tpl_vars['articlerows'];
$this->_tpl_vars['i']=array();
$this->_tpl_vars['i']['columns'] = 1;
$this->_tpl_vars['i']['count'] = count($this->_tpl_vars['articlerows']);
$this->_tpl_vars['i']['addrows'] = count($this->_tpl_vars['articlerows']) % $this->_tpl_vars['i']['columns'] == 0 ? 0 : $this->_tpl_vars['i']['columns'] - count($this->_tpl_vars['articlerows']) % $this->_tpl_vars['i']['columns'];
$this->_tpl_vars['i']['loops'] = $this->_tpl_vars['i']['count'] + $this->_tpl_vars['i']['addrows'];
reset($this->_tpl_vars['articlerows']);
for($this->_tpl_vars['i']['index'] = 0; $this->_tpl_vars['i']['index'] < $this->_tpl_vars['i']['loops']; $this->_tpl_vars['i']['index']++){
	$this->_tpl_vars['i']['order'] = $this->_tpl_vars['i']['index'] + 1;
	$this->_tpl_vars['i']['row'] = ceil($this->_tpl_vars['i']['order'] / $this->_tpl_vars['i']['columns']);
	$this->_tpl_vars['i']['column'] = $this->_tpl_vars['i']['order'] % $this->_tpl_vars['i']['columns'];
	if($this->_tpl_vars['i']['column'] == 0) $this->_tpl_vars['i']['column'] = $this->_tpl_vars['i']['columns'];
	if($this->_tpl_vars['i']['index'] < $this->_tpl_vars['i']['count']){
		list($this->_tpl_vars['i']['key'], $this->_tpl_vars['i']['value']) = each($this->_tpl_vars['articlerows']);
		$this->_tpl_vars['i']['append'] = 0;
	}else{
		$this->_tpl_vars['i']['key'] = '';
		$this->_tpl_vars['i']['value'] = '';
		$this->_tpl_vars['i']['append'] = 1;
	}
	echo '
	';
if($this->_tpl_vars['i']['order'] < 2){
echo '
	<a href="'.$this->_tpl_vars['articlerows'][$this->_tpl_vars['i']['key']]['url_articleinfo'].'" target="_blank"><img src="'.$this->_tpl_vars['articlerows'][$this->_tpl_vars['i']['key']]['url_image'].'" /></a>
    <strong><a href="'.$this->_tpl_vars['articlerows'][$this->_tpl_vars['i']['key']]['url_articleinfo'].'" target="_blank">'.truncate($this->_tpl_vars['articlerows'][$this->_tpl_vars['i']['key']]['articlename'],'18','..').'</a></strong>
    <p>'.truncate($this->_tpl_vars['articlerows'][$this->_tpl_vars['i']['key']]['intro'],'70','..').'<a class="hot" href="'.$this->_tpl_vars['articlerows'][$this->_tpl_vars['i']['key']]['url_articleindex'].'" target="_blank">[阅读]</a></p>
    <div class="cb"></div>
	';
}else{
echo '
		';
if($this->_tpl_vars['i']['order'] == 2){
echo '<ul>';
}
echo '
		<li><span class="gray">▌</span><a href="'.$this->_tpl_vars['articlerows'][$this->_tpl_vars['i']['key']]['url_articleinfo'].'" target="_blank">'.truncate($this->_tpl_vars['articlerows'][$this->_tpl_vars['i']['key']]['articlename'],'20','..').'</a></li>
		';
if($this->_tpl_vars['i']['order'] == $this->_tpl_vars['i']['count']){
echo '</ul>';
}
echo '
	';
}
}

?>