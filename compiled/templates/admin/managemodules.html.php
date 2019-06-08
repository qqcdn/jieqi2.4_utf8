<?php
echo '<form action="managemodules.php" method="post">
  <table class="grid" width="98%" align="center">
    <caption>
      模块配置管理
    </caption>
    <tr>
      <td width="15%" height="29" align="center">模块名称</td>
      <td width="18%" align="center">相对路径</td>
      <td width="45%" align="center">URL地址</td>
      <td width="14%" align="center">模板</td>
      <td width="8%" align="center">是否显示</td>
    </tr>
    ';
if (empty($this->_tpl_vars['jieqiModules'])) $this->_tpl_vars['jieqiModules'] = array();
elseif (!is_array($this->_tpl_vars['jieqiModules'])) $this->_tpl_vars['jieqiModules'] = (array)$this->_tpl_vars['jieqiModules'];
$this->_tpl_vars['i']=array();
$this->_tpl_vars['i']['columns'] = 1;
$this->_tpl_vars['i']['count'] = count($this->_tpl_vars['jieqiModules']);
$this->_tpl_vars['i']['addrows'] = count($this->_tpl_vars['jieqiModules']) % $this->_tpl_vars['i']['columns'] == 0 ? 0 : $this->_tpl_vars['i']['columns'] - count($this->_tpl_vars['jieqiModules']) % $this->_tpl_vars['i']['columns'];
$this->_tpl_vars['i']['loops'] = $this->_tpl_vars['i']['count'] + $this->_tpl_vars['i']['addrows'];
reset($this->_tpl_vars['jieqiModules']);
for($this->_tpl_vars['i']['index'] = 0; $this->_tpl_vars['i']['index'] < $this->_tpl_vars['i']['loops']; $this->_tpl_vars['i']['index']++){
	$this->_tpl_vars['i']['order'] = $this->_tpl_vars['i']['index'] + 1;
	$this->_tpl_vars['i']['row'] = ceil($this->_tpl_vars['i']['order'] / $this->_tpl_vars['i']['columns']);
	$this->_tpl_vars['i']['column'] = $this->_tpl_vars['i']['order'] % $this->_tpl_vars['i']['columns'];
	if($this->_tpl_vars['i']['column'] == 0) $this->_tpl_vars['i']['column'] = $this->_tpl_vars['i']['columns'];
	if($this->_tpl_vars['i']['index'] < $this->_tpl_vars['i']['count']){
		list($this->_tpl_vars['i']['key'], $this->_tpl_vars['i']['value']) = each($this->_tpl_vars['jieqiModules']);
		$this->_tpl_vars['i']['append'] = 0;
	}else{
		$this->_tpl_vars['i']['key'] = '';
		$this->_tpl_vars['i']['value'] = '';
		$this->_tpl_vars['i']['append'] = 1;
	}
	echo '
  <tr>
    <td align="center"><input name="jieqiModules['.$this->_tpl_vars['i']['key'].'][caption]" type="text" id="jieqiModules['.$this->_tpl_vars['i']['key'].'][caption]" value="'.$this->_tpl_vars['jieqiModules'][$this->_tpl_vars['i']['key']]['caption'].'" size="12" maxlength="8">
        <input type="hidden" name="jieqiModules['.$this->_tpl_vars['i']['key'].'][path]" id="jieqiModules['.$this->_tpl_vars['i']['key'].'][path]" value="'.$this->_tpl_vars['jieqiModules'][$this->_tpl_vars['i']['key']]['path'].'"></td>
    <td>'.$this->_tpl_vars['jieqiModules'][$this->_tpl_vars['i']['key']]['dir'].'
      <input name="jieqiModules['.$this->_tpl_vars['i']['key'].'][dir]" type="hidden" id="jieqiModules['.$this->_tpl_vars['i']['key'].'][dir]" value="'.$this->_tpl_vars['jieqiModules'][$this->_tpl_vars['i']['key']]['dir'].'" size="40" readonly></td>
    <td align="center"><input name="jieqiModules['.$this->_tpl_vars['i']['key'].'][url]" type="text" value="'.$this->_tpl_vars['jieqiModules'][$this->_tpl_vars['i']['key']]['url'].'" size="50">
    </td>
    <td align="center"><select name="jieqiModules['.$this->_tpl_vars['i']['key'].'][theme]">
	  ';
if (empty($this->_tpl_vars['themes'])) $this->_tpl_vars['themes'] = array();
elseif (!is_array($this->_tpl_vars['themes'])) $this->_tpl_vars['themes'] = (array)$this->_tpl_vars['themes'];
$this->_tpl_vars['a']=array();
$this->_tpl_vars['a']['columns'] = 1;
$this->_tpl_vars['a']['count'] = count($this->_tpl_vars['themes']);
$this->_tpl_vars['a']['addrows'] = count($this->_tpl_vars['themes']) % $this->_tpl_vars['a']['columns'] == 0 ? 0 : $this->_tpl_vars['a']['columns'] - count($this->_tpl_vars['themes']) % $this->_tpl_vars['a']['columns'];
$this->_tpl_vars['a']['loops'] = $this->_tpl_vars['a']['count'] + $this->_tpl_vars['a']['addrows'];
reset($this->_tpl_vars['themes']);
for($this->_tpl_vars['a']['index'] = 0; $this->_tpl_vars['a']['index'] < $this->_tpl_vars['a']['loops']; $this->_tpl_vars['a']['index']++){
	$this->_tpl_vars['a']['order'] = $this->_tpl_vars['a']['index'] + 1;
	$this->_tpl_vars['a']['row'] = ceil($this->_tpl_vars['a']['order'] / $this->_tpl_vars['a']['columns']);
	$this->_tpl_vars['a']['column'] = $this->_tpl_vars['a']['order'] % $this->_tpl_vars['a']['columns'];
	if($this->_tpl_vars['a']['column'] == 0) $this->_tpl_vars['a']['column'] = $this->_tpl_vars['a']['columns'];
	if($this->_tpl_vars['a']['index'] < $this->_tpl_vars['a']['count']){
		list($this->_tpl_vars['a']['key'], $this->_tpl_vars['a']['value']) = each($this->_tpl_vars['themes']);
		$this->_tpl_vars['a']['append'] = 0;
	}else{
		$this->_tpl_vars['a']['key'] = '';
		$this->_tpl_vars['a']['value'] = '';
		$this->_tpl_vars['a']['append'] = 1;
	}
	echo '
      <option value="'.$this->_tpl_vars['themes'][$this->_tpl_vars['a']['key']].'" ';
if($this->_tpl_vars['themes'][$this->_tpl_vars['a']['key']]==$this->_tpl_vars['jieqiModules'][$this->_tpl_vars['i']['key']]['theme']){
echo 'selected';
}
echo '>'.$this->_tpl_vars['themes'][$this->_tpl_vars['a']['key']].'</option>
	   ';
}
echo '
      
    </select></td>
    <td align="center"><select name="jieqiModules['.$this->_tpl_vars['i']['key'].'][publish]">
      <option value="1" ';
if($this->_tpl_vars['jieqiModules'][$this->_tpl_vars['i']['key']]['publish']==1){
echo 'selected';
}
echo '>是</option>
      <option value="0" ';
if($this->_tpl_vars['jieqiModules'][$this->_tpl_vars['i']['key']]['publish']==0){
echo 'selected';
}
echo '>否</option>
    </select>
    </td>
  </tr>
    ';
}
echo '
  <tr>
    <td colspan="6" align="center"><button type="submit" class="button" name="dosubmit" id="dosubmit">确定</button></td>
  </tr>
  </table>
</form>';
?>