<?php
echo '<table  class="grid" width="100%" align="center">
  <caption>区块配置文件管理</caption>
  <tr>
    <td align="center" width="20%">配置文件标题</td>
    <td align="center" width="20%">所属模块</td>
	<td align="center" width="40%">配置文件路径及名称</td>
    <td align="center" width="20%">操作</td>
  </tr>
';
if (empty($this->_tpl_vars['blockfiles'])) $this->_tpl_vars['blockfiles'] = array();
elseif (!is_array($this->_tpl_vars['blockfiles'])) $this->_tpl_vars['blockfiles'] = (array)$this->_tpl_vars['blockfiles'];
$this->_tpl_vars['i']=array();
$this->_tpl_vars['i']['columns'] = 1;
$this->_tpl_vars['i']['count'] = count($this->_tpl_vars['blockfiles']);
$this->_tpl_vars['i']['addrows'] = count($this->_tpl_vars['blockfiles']) % $this->_tpl_vars['i']['columns'] == 0 ? 0 : $this->_tpl_vars['i']['columns'] - count($this->_tpl_vars['blockfiles']) % $this->_tpl_vars['i']['columns'];
$this->_tpl_vars['i']['loops'] = $this->_tpl_vars['i']['count'] + $this->_tpl_vars['i']['addrows'];
reset($this->_tpl_vars['blockfiles']);
for($this->_tpl_vars['i']['index'] = 0; $this->_tpl_vars['i']['index'] < $this->_tpl_vars['i']['loops']; $this->_tpl_vars['i']['index']++){
	$this->_tpl_vars['i']['order'] = $this->_tpl_vars['i']['index'] + 1;
	$this->_tpl_vars['i']['row'] = ceil($this->_tpl_vars['i']['order'] / $this->_tpl_vars['i']['columns']);
	$this->_tpl_vars['i']['column'] = $this->_tpl_vars['i']['order'] % $this->_tpl_vars['i']['columns'];
	if($this->_tpl_vars['i']['column'] == 0) $this->_tpl_vars['i']['column'] = $this->_tpl_vars['i']['columns'];
	if($this->_tpl_vars['i']['index'] < $this->_tpl_vars['i']['count']){
		list($this->_tpl_vars['i']['key'], $this->_tpl_vars['i']['value']) = each($this->_tpl_vars['blockfiles']);
		$this->_tpl_vars['i']['append'] = 0;
	}else{
		$this->_tpl_vars['i']['key'] = '';
		$this->_tpl_vars['i']['value'] = '';
		$this->_tpl_vars['i']['append'] = 1;
	}
	echo '
  <tr>
    <td align="center">'.$this->_tpl_vars['blockfiles'][$this->_tpl_vars['i']['key']]['caption'].'</td>
    <td align="center">'.$this->_tpl_vars['blockfiles'][$this->_tpl_vars['i']['key']]['modname'].'</td>
	<td>/configs';
if($this->_tpl_vars['blockfiles'][$this->_tpl_vars['i']['key']]['module'] != 'system'){
echo '/'.$this->_tpl_vars['blockfiles'][$this->_tpl_vars['i']['key']]['module'];
}
echo '/'.$this->_tpl_vars['blockfiles'][$this->_tpl_vars['i']['key']]['filename'].'.php</td>
    <td align="center"><a href="'.$this->_tpl_vars['jieqi_url'].'/admin/blockfiles.php?act=blocks&module='.urlencode($this->_tpl_vars['blockfiles'][$this->_tpl_vars['i']['key']]['module']).'&filename='.urlencode($this->_tpl_vars['blockfiles'][$this->_tpl_vars['i']['key']]['filename']).'">管理区块参数</a></td>
  </tr>
';
}
echo '
</table>';
?>