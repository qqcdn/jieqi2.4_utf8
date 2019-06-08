<?php
echo '<table class="grid" width="100%" align="center">
<caption>系统信息及优化建议</caption>
  <tr>
    <th width="20%">变量名</th>
    <th width="20%">变量值</th>
    <th width="60%">推荐或备注</th>
  </tr>
';
if (empty($this->_tpl_vars['sysinfos'])) $this->_tpl_vars['sysinfos'] = array();
elseif (!is_array($this->_tpl_vars['sysinfos'])) $this->_tpl_vars['sysinfos'] = (array)$this->_tpl_vars['sysinfos'];
$this->_tpl_vars['i']=array();
$this->_tpl_vars['i']['columns'] = 1;
$this->_tpl_vars['i']['count'] = count($this->_tpl_vars['sysinfos']);
$this->_tpl_vars['i']['addrows'] = count($this->_tpl_vars['sysinfos']) % $this->_tpl_vars['i']['columns'] == 0 ? 0 : $this->_tpl_vars['i']['columns'] - count($this->_tpl_vars['sysinfos']) % $this->_tpl_vars['i']['columns'];
$this->_tpl_vars['i']['loops'] = $this->_tpl_vars['i']['count'] + $this->_tpl_vars['i']['addrows'];
reset($this->_tpl_vars['sysinfos']);
for($this->_tpl_vars['i']['index'] = 0; $this->_tpl_vars['i']['index'] < $this->_tpl_vars['i']['loops']; $this->_tpl_vars['i']['index']++){
	$this->_tpl_vars['i']['order'] = $this->_tpl_vars['i']['index'] + 1;
	$this->_tpl_vars['i']['row'] = ceil($this->_tpl_vars['i']['order'] / $this->_tpl_vars['i']['columns']);
	$this->_tpl_vars['i']['column'] = $this->_tpl_vars['i']['order'] % $this->_tpl_vars['i']['columns'];
	if($this->_tpl_vars['i']['column'] == 0) $this->_tpl_vars['i']['column'] = $this->_tpl_vars['i']['columns'];
	if($this->_tpl_vars['i']['index'] < $this->_tpl_vars['i']['count']){
		list($this->_tpl_vars['i']['key'], $this->_tpl_vars['i']['value']) = each($this->_tpl_vars['sysinfos']);
		$this->_tpl_vars['i']['append'] = 0;
	}else{
		$this->_tpl_vars['i']['key'] = '';
		$this->_tpl_vars['i']['value'] = '';
		$this->_tpl_vars['i']['append'] = 1;
	}
	echo '
  <tr>
    <td>'.$this->_tpl_vars['sysinfos'][$this->_tpl_vars['i']['key']]['name'].'</td>
    <td><span class="'.$this->_tpl_vars['sysinfos'][$this->_tpl_vars['i']['key']]['state'].'">'.$this->_tpl_vars['sysinfos'][$this->_tpl_vars['i']['key']]['value'].'</span></td>
    <td><span class="'.$this->_tpl_vars['sysinfos'][$this->_tpl_vars['i']['key']]['state'].'">'.$this->_tpl_vars['sysinfos'][$this->_tpl_vars['i']['key']]['note'].'</span></td>
  </tr>
';
}
echo '
</table>';
?>