<?php
echo '

<div class="blockb">
<div class="blocktitle">“'.$this->_tpl_vars['author'].'” 的作品</div>
<div class="blockcontent" id="jieqi_page_contents">
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
	<div class="c_row cf">
	<a class="db cf" href="'.$this->_tpl_vars['articlerows'][$this->_tpl_vars['i']['key']]['url_articleinfo'].'">
	<div class="row_cover">
	<img class="cover_m" src="'.$this->_tpl_vars['articlerows'][$this->_tpl_vars['i']['key']]['url_simage'].'" />
	</div>

	<div class="row_text">
	<h4>'.$this->_tpl_vars['articlerows'][$this->_tpl_vars['i']['key']]['articlename'].'</h4>
    <p class="gray">'.$this->_tpl_vars['articlerows'][$this->_tpl_vars['i']['key']]['sort'].' | '.$this->_tpl_vars['articlerows'][$this->_tpl_vars['i']['key']]['author'].'<br />'.truncate($this->_tpl_vars['articlerows'][$this->_tpl_vars['i']['key']]['intro'],'76','..').'</p>
	</div>
	</a>
	</div>
	';
}
echo '
</div>
</div>

<div class="pages">'.$this->_tpl_vars['url_jumppage'].'</div>';
?>