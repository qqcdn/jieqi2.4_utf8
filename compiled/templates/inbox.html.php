<?php
echo '

'.jieqi_get_block(array('bid'=>'0', 'blockname'=>'站内消息', 'module'=>'system', 'filename'=>'', 'classname'=>'BlockSystemCustom', 'side'=>'-1', 'title'=>'', 'vars'=>'', 'template'=>'block_message_tab.html', 'contenttype'=>'4', 'custom'=>'1', 'publish'=>'3', 'hasvars'=>'0'), 1).'
<form action="'.$this->_tpl_vars['url_action'].'" method="post" name="checkform" id="checkform">
<table class="grid" width="100%" align="center">
<!-- <caption>'.$this->_tpl_vars['boxname'].'（收/发件箱共允许消息数：'.$this->_tpl_vars['maxmessage'].'，现有消息数：'.$this->_tpl_vars['nowmessage'].'）</caption> -->
  <tr>
    <th width="5%"><input type="checkbox" name="checkall" value="checkall" onclick="for(var i=0;i<this.form.elements.length;i++){ if(this.form.elements[i].name != \'checkkall\') this.form.elements[i].checked = this.form.checkall.checked; }"></th>
    <th width="20%">'.$this->_tpl_vars['usertitle'].'</th>
    <th width="50%">标题</th>
    <th width="15%">日期</th>
    <th width="10%">状态</th>
  </tr>
  <tbody id="jieqi_page_contents">
';
if (empty($this->_tpl_vars['messagerows'])) $this->_tpl_vars['messagerows'] = array();
elseif (!is_array($this->_tpl_vars['messagerows'])) $this->_tpl_vars['messagerows'] = (array)$this->_tpl_vars['messagerows'];
$this->_tpl_vars['i']=array();
$this->_tpl_vars['i']['columns'] = 1;
$this->_tpl_vars['i']['count'] = count($this->_tpl_vars['messagerows']);
$this->_tpl_vars['i']['addrows'] = count($this->_tpl_vars['messagerows']) % $this->_tpl_vars['i']['columns'] == 0 ? 0 : $this->_tpl_vars['i']['columns'] - count($this->_tpl_vars['messagerows']) % $this->_tpl_vars['i']['columns'];
$this->_tpl_vars['i']['loops'] = $this->_tpl_vars['i']['count'] + $this->_tpl_vars['i']['addrows'];
reset($this->_tpl_vars['messagerows']);
for($this->_tpl_vars['i']['index'] = 0; $this->_tpl_vars['i']['index'] < $this->_tpl_vars['i']['loops']; $this->_tpl_vars['i']['index']++){
	$this->_tpl_vars['i']['order'] = $this->_tpl_vars['i']['index'] + 1;
	$this->_tpl_vars['i']['row'] = ceil($this->_tpl_vars['i']['order'] / $this->_tpl_vars['i']['columns']);
	$this->_tpl_vars['i']['column'] = $this->_tpl_vars['i']['order'] % $this->_tpl_vars['i']['columns'];
	if($this->_tpl_vars['i']['column'] == 0) $this->_tpl_vars['i']['column'] = $this->_tpl_vars['i']['columns'];
	if($this->_tpl_vars['i']['index'] < $this->_tpl_vars['i']['count']){
		list($this->_tpl_vars['i']['key'], $this->_tpl_vars['i']['value']) = each($this->_tpl_vars['messagerows']);
		$this->_tpl_vars['i']['append'] = 0;
	}else{
		$this->_tpl_vars['i']['key'] = '';
		$this->_tpl_vars['i']['value'] = '';
		$this->_tpl_vars['i']['append'] = 1;
	}
	echo '
  <tr>
    <td align="center"><input type="checkbox" name="id[]" value="'.$this->_tpl_vars['messagerows'][$this->_tpl_vars['i']['key']]['messageid'].'"></td>
    <td>';
if($this->_tpl_vars['messagerows'][$this->_tpl_vars['i']['key']]['fromid'] > 0){
echo '<a href="'.jieqi_geturl('system','user',$this->_tpl_vars['messagerows'][$this->_tpl_vars['i']['key']]['fromid']).'" target="_blank">'.$this->_tpl_vars['messagerows'][$this->_tpl_vars['i']['key']]['fromname'].'</a>';
}else{
echo '<span class="hot">网站管理员</span>';
}
echo '</td>
    <td><a href="'.$this->_tpl_vars['jieqi_url'].'/messagedetail.php?id='.$this->_tpl_vars['messagerows'][$this->_tpl_vars['i']['key']]['messageid'].'">'.$this->_tpl_vars['messagerows'][$this->_tpl_vars['i']['key']]['title'].'</a></td>
    <td align="center">'.date('Y-m-d',$this->_tpl_vars['messagerows'][$this->_tpl_vars['i']['key']]['postdate']).'</td>
    <td align="center">';
if($this->_tpl_vars['messagerows'][$this->_tpl_vars['i']['key']]['isread'] == 0){
echo '<span class="hot">未读</span>';
}else{
echo '已读';
}
echo '</td>
  </tr>
';
}
echo '
  </tbody>
  <tr>
    <td colspan="5" class="foot">
		<input type="button" name="selectall" value="全部选中" class="button" onclick="for (var i=0;i<this.form.elements.length;i++){ this.form.elements[i].checked = true; }" />&nbsp;&nbsp;
		<input type="button" name="cancelall" value="全部取消" class="button" onclick="for (var i=0;i<this.form.elements.length;i++){ this.form.elements[i].checked = false; }" />&nbsp;&nbsp;
		<button type="button" name="act_remove" class="button" onclick="if(confirm(\'确实要删除选中消息么？\')){this.form.act.value=\'delete\'; this.form.submit();}">批量删除</button>&nbsp;&nbsp;
        <button type="button" name="act_read" class="button" id="act_read" onclick="this.form.act.value=\'read\';this.form.submit();">设为已读</button>
		<!-- &nbsp;&nbsp;<button type="button" name="act_clear" class="button" id="act_clear" onclick="if(confirm(\'确实要清空全部消息么？\')) Ajax.Tip(\''.$this->_tpl_vars['jieqi_url'].'/message.php?box='.$this->_tpl_vars['box'].'&act=clear'.$this->_tpl_vars['jieqi_token_url'].'\', {method: \'POST\'});">全部清空</button> -->
		<input type="hidden" name="act" id="act" value="delete">
		'.$this->_tpl_vars['jieqi_token_input'].'
	</td>
  </tr>
</table>
</form>
<div class="pages">'.$this->_tpl_vars['url_jumppage'].'</div>

';
?>