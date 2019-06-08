<?php
echo '<a href="'.$this->_tpl_vars['url_more'].'" class="moretop"><i class="iconfont">&#xee6a;</i></a>
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
	if($this->_tpl_vars['i']['order'] < 2){
echo '
<div class="c_row cf">
<a class="db cf" href="'.$this->_tpl_vars['articlerows'][$this->_tpl_vars['i']['key']]['url_articleinfo'].'">
	<div class="row_cover">
	<img class="cover_i" src="'.$this->_tpl_vars['articlerows'][$this->_tpl_vars['i']['key']]['url_simage'].'" />
	</div>

	<div class="row_text">
	<h4>'.$this->_tpl_vars['articlerows'][$this->_tpl_vars['i']['key']]['articlename'].'</h4>
    <p class="gray fss">'.$this->_tpl_vars['articlerows'][$this->_tpl_vars['i']['key']]['sort'].' | '.$this->_tpl_vars['articlerows'][$this->_tpl_vars['i']['key']]['author'].'<br />'.truncate($this->_tpl_vars['articlerows'][$this->_tpl_vars['i']['key']]['intro'],'80','..').'</p>
	</div>
</a>
</div>
';
}else{
if($this->_tpl_vars['i']['order'] == 2){
echo '<ul class="ullist">';
}
echo '
	<li><a class="db" href="'.$this->_tpl_vars['articlerows'][$this->_tpl_vars['i']['key']]['url_articleinfo'].'">'.$this->_tpl_vars['articlerows'][$this->_tpl_vars['i']['key']]['articlename'].' <span class="gray fss">['.$this->_tpl_vars['articlerows'][$this->_tpl_vars['i']['key']]['sort'].']</span></a></li>
';
if($this->_tpl_vars['i']['order'] == $this->_tpl_vars['i']['count']){
echo '</ul>';
}
}
}

?>