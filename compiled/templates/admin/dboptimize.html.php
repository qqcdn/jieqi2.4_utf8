<?php
echo '<table class="grid" width="100%" align="center">
  <caption>提示</caption>
  <tr><td>
  <ul>
  <li>数据表优化可以去除数据库中的碎片，使记录排列紧密，提高读写速度。</li>
  <li>数据表修复可修复数据库在进行查询，删除，更新等操作时产生的错误。</li>
  </ul>
  </td></tr>
</table>
<br />
<form action="'.$this->_tpl_vars['url_action'].'" method="post" name="checkform" id="checkform">
<table class="grid" width="100%" align="center">
<caption>数据表优化/修复</caption>
  <tr>
    <th width="5%"><input type="checkbox" name="checkall" value="checkall" onclick="for (var i=0;i<this.form.elements.length;i++){ if (this.form.elements[i].name != \'checkkall\') this.form.elements[i].checked = this.form.checkall.checked; }"></th>
    <th width="30%">数据表</th>
    <th width="13%">类型</th>
    <th width="13%">记录数</th>
    <th width="13%">数据</th>
	<th width="13%">索引</th>
	<th width="13%">碎片</th>
  </tr>
';
if (empty($this->_tpl_vars['tablerows'])) $this->_tpl_vars['tablerows'] = array();
elseif (!is_array($this->_tpl_vars['tablerows'])) $this->_tpl_vars['tablerows'] = (array)$this->_tpl_vars['tablerows'];
$this->_tpl_vars['i']=array();
$this->_tpl_vars['i']['columns'] = 1;
$this->_tpl_vars['i']['count'] = count($this->_tpl_vars['tablerows']);
$this->_tpl_vars['i']['addrows'] = count($this->_tpl_vars['tablerows']) % $this->_tpl_vars['i']['columns'] == 0 ? 0 : $this->_tpl_vars['i']['columns'] - count($this->_tpl_vars['tablerows']) % $this->_tpl_vars['i']['columns'];
$this->_tpl_vars['i']['loops'] = $this->_tpl_vars['i']['count'] + $this->_tpl_vars['i']['addrows'];
reset($this->_tpl_vars['tablerows']);
for($this->_tpl_vars['i']['index'] = 0; $this->_tpl_vars['i']['index'] < $this->_tpl_vars['i']['loops']; $this->_tpl_vars['i']['index']++){
	$this->_tpl_vars['i']['order'] = $this->_tpl_vars['i']['index'] + 1;
	$this->_tpl_vars['i']['row'] = ceil($this->_tpl_vars['i']['order'] / $this->_tpl_vars['i']['columns']);
	$this->_tpl_vars['i']['column'] = $this->_tpl_vars['i']['order'] % $this->_tpl_vars['i']['columns'];
	if($this->_tpl_vars['i']['column'] == 0) $this->_tpl_vars['i']['column'] = $this->_tpl_vars['i']['columns'];
	if($this->_tpl_vars['i']['index'] < $this->_tpl_vars['i']['count']){
		list($this->_tpl_vars['i']['key'], $this->_tpl_vars['i']['value']) = each($this->_tpl_vars['tablerows']);
		$this->_tpl_vars['i']['append'] = 0;
	}else{
		$this->_tpl_vars['i']['key'] = '';
		$this->_tpl_vars['i']['value'] = '';
		$this->_tpl_vars['i']['append'] = 1;
	}
	echo '
  <tr>
    <td>'.$this->_tpl_vars['tablerows'][$this->_tpl_vars['i']['key']]['checkbox'].'</td>
    <td>'.$this->_tpl_vars['tablerows'][$this->_tpl_vars['i']['key']]['Name'].'</td>
    <td>'.$this->_tpl_vars['tablerows'][$this->_tpl_vars['i']['key']]['Type'].'</td>
    <td>'.$this->_tpl_vars['tablerows'][$this->_tpl_vars['i']['key']]['Rows'].'</td>
    <td>'.$this->_tpl_vars['tablerows'][$this->_tpl_vars['i']['key']]['Data_length'].'</td>
	<td>'.$this->_tpl_vars['tablerows'][$this->_tpl_vars['i']['key']]['Index_length'].'</td>
    <td>'.$this->_tpl_vars['tablerows'][$this->_tpl_vars['i']['key']]['Data_free'].'</td>
  </tr>
';
}
echo '
  <tr>
    <th></th>
    <th>'.$this->_tpl_vars['totaltable'].'个表</th>
    <th></th>
    <th>'.$this->_tpl_vars['totalrows'].'条记录</th>
    <th>'.$this->_tpl_vars['totalsize'].'</th>
	<th>'.$this->_tpl_vars['totalindex'].'</th>
    <th>'.$this->_tpl_vars['totalfree'].'</th>
  </tr>
  <tr>
    <td colspan="7" class="foot">
	<input type="button" name="allcheck" value="全部选中" class="button" onclick="for (var i=0;i<this.form.elements.length;i++){ if(this.form.elements[i].type == \'checkbox\') this.form.elements[i].checked = true; }">&nbsp;&nbsp;
	<input type="button" name="nocheck" value="全部取消" class="button" onclick="for (var i=0;i<this.form.elements.length;i++){ if(this.form.elements[i].type == \'checkbox\') this.form.elements[i].checked = false; }">&nbsp;&nbsp;
	<label class="radio"><input name="act" type="radio" value="optimize"';
if($this->_tpl_vars['option'] == "optimize"){
echo ' checked="checked"';
}
echo ' />优化表</label> 
	<label class="radio"><input name="act" type="radio" value="repair"';
if($this->_tpl_vars['option'] == "repair"){
echo ' checked="checked"';
}
echo ' />修复表</label> &nbsp;&nbsp;&nbsp;
	<button type="submit" name="Submit" class="button"> 提 交 </button>
	'.$this->_tpl_vars['jieqi_token_input'].'
	</td>
  </tr>
</table>
</form>
<br /><br />';
?>