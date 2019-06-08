<?php
echo '

<table class="grid" width="100%" align="center">
  <tr align="center">
    <th width="15%">分类</th>
    <th width="50%">书名/作者</th>
		<th width="20%">';
if($this->_tpl_vars['orderfield'] == 'lastupdate'){
echo '字数';
}else{
echo $this->_tpl_vars['ordername'];
}
echo '</th>
    <th width="15%">状态</th>
  </tr>
  <tbody id="jieqi_page_contents">
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
  <tr>
    <td align="center">'.truncate($this->_tpl_vars['articlerows'][$this->_tpl_vars['i']['key']]['sort'],'4','').'</td>
    <td><a class="db nw" href="'.$this->_tpl_vars['articlerows'][$this->_tpl_vars['i']['key']]['url_articleinfo'].'">'.$this->_tpl_vars['articlerows'][$this->_tpl_vars['i']['key']]['articlename'].'<span class="gray fss">/'.$this->_tpl_vars['articlerows'][$this->_tpl_vars['i']['key']]['author'].'</span></a></td>
		<td align="center">';
if($this->_tpl_vars['orderfield'] == 'lastupdate'){
echo $this->_tpl_vars['articlerows'][$this->_tpl_vars['i']['key']]['words'];
}else{
echo $this->_tpl_vars['articlerows'][$this->_tpl_vars['i']['key']]['ordervalue'];
}
echo '</td>
    <td align="center">'.$this->_tpl_vars['articlerows'][$this->_tpl_vars['i']['key']]['fullflag'].'</td>
  </tr>
  ';
}
echo '
  </tbody>
</table>
<div class="pages">'.$this->_tpl_vars['url_jumppage'].'</div>';
?>