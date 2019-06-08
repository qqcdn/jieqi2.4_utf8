<?php
echo '<ul class="ulhover">
';
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
	<li onmouseover="selecttab(this)"';
if($this->_tpl_vars['i']['order'] == 1){
echo ' class="selected"';
}
echo '>
		<span><em>'.$this->_tpl_vars['articlerows'][$this->_tpl_vars['i']['key']]['author'].'</em> ';
if($this->_tpl_vars['i']['order'] < 4){
echo '<b>'.$this->_tpl_vars['i']['order'].'</b>';
}else{
echo '<i>'.$this->_tpl_vars['i']['order'].'</i>';
}
echo '<a href="'.$this->_tpl_vars['articlerows'][$this->_tpl_vars['i']['key']]['url_articleinfo'].'" target="_blank">'.$this->_tpl_vars['articlerows'][$this->_tpl_vars['i']['key']]['articlename'].'</a></span>
		<div class="cf">
			';
if($this->_tpl_vars['i']['order'] < 4){
echo '<b>'.$this->_tpl_vars['i']['order'].'</b>';
}else{
echo '<i>'.$this->_tpl_vars['i']['order'].'</i>';
}
echo '
			<a href="'.$this->_tpl_vars['articlerows'][$this->_tpl_vars['i']['key']]['url_articleinfo'].'" target="_blank"><img src="'.$this->_tpl_vars['articlerows'][$this->_tpl_vars['i']['key']]['url_simage'].'" alt="'.$this->_tpl_vars['articlerows'][$this->_tpl_vars['i']['key']]['articlename'].'"></a>
			<p>
			<a class="hot" href="'.$this->_tpl_vars['articlerows'][$this->_tpl_vars['i']['key']]['url_articleinfo'].'" target="_blank">'.$this->_tpl_vars['articlerows'][$this->_tpl_vars['i']['key']]['articlename'].'</a><br />
			作者：'.$this->_tpl_vars['articlerows'][$this->_tpl_vars['i']['key']]['author'].'<br />
			分类：'.$this->_tpl_vars['articlerows'][$this->_tpl_vars['i']['key']]['sort'].'
			</p>
		</div>
	</li>
';
}
echo '
</ul>';
?>