<?php
echo '

'.jieqi_get_block(array('bid'=>'0', 'blockname'=>'会员工具', 'module'=>'system', 'filename'=>'', 'classname'=>'BlockSystemCustom', 'side'=>'-1', 'title'=>'', 'vars'=>'', 'template'=>'block_userbox_tab.html', 'contenttype'=>'4', 'custom'=>'1', 'publish'=>'3', 'hasvars'=>'0'), 1).'
<script type="text/javascript">
function check_confirm(){
	var checkform = document.getElementById(\'checkform\');
	var checknum = 0;
	for (var i=0; i < checkform.elements.length; i++){
	 if (checkform.elements[i].name == \'checkid[]\' && checkform.elements[i].checked == true) checknum++; 
	}
	if(checknum == 0){
		alert(\'请先选择要操作的书目！\');
		return false;
	}
	var newclassid = document.getElementById(\'newclassid\');
	if(newclassid.value == -1){
		if(confirm(\'确实要将选中书目移出书架么？\')) return true;
		else return false;
	}else{
		return true;
	}
}
//删除
function act_delete(url){
	var o = getTarget();
	var param = {
		method: \'POST\', 
		onReturn: function(){
			$_(o.parentNode.parentNode).remove();
		}
	}
	if(confirm(\'确实要将本书移出书架么？\')) Ajax.Tip(url, param);
	return false;
}
</script>
<form action="'.$this->_tpl_vars['jieqi_modules']['article']['url'].'/bookcase.php" method="post" name="checkform" id="checkform" onsubmit="return check_confirm();">
<table class="grid" width="100%" align="center">
  <caption>
  您的书架可收藏 '.$this->_tpl_vars['maxbookcase'].' 本，已收藏 '.$this->_tpl_vars['nowbookcase'].' 本，本组有 '.$this->_tpl_vars['classbookcase'].' 本。
  ';
if($this->_tpl_vars['maxmarkclass'] > 0){
echo '
  &nbsp;&nbsp;&nbsp;&nbsp;选择分组
  <select name="classlist" onchange="javascript:document.location=\'bookcase.php?classid=\'+this.value;">
    <option value="0"';
if($this->_tpl_vars['classid'] == 0){
echo ' selected="selected"';
}
echo '>默认书架</option>
	';
if (empty($this->_tpl_vars['markclassrows'])) $this->_tpl_vars['markclassrows'] = array();
elseif (!is_array($this->_tpl_vars['markclassrows'])) $this->_tpl_vars['markclassrows'] = (array)$this->_tpl_vars['markclassrows'];
$this->_tpl_vars['i']=array();
$this->_tpl_vars['i']['columns'] = 1;
$this->_tpl_vars['i']['count'] = count($this->_tpl_vars['markclassrows']);
$this->_tpl_vars['i']['addrows'] = count($this->_tpl_vars['markclassrows']) % $this->_tpl_vars['i']['columns'] == 0 ? 0 : $this->_tpl_vars['i']['columns'] - count($this->_tpl_vars['markclassrows']) % $this->_tpl_vars['i']['columns'];
$this->_tpl_vars['i']['loops'] = $this->_tpl_vars['i']['count'] + $this->_tpl_vars['i']['addrows'];
reset($this->_tpl_vars['markclassrows']);
for($this->_tpl_vars['i']['index'] = 0; $this->_tpl_vars['i']['index'] < $this->_tpl_vars['i']['loops']; $this->_tpl_vars['i']['index']++){
	$this->_tpl_vars['i']['order'] = $this->_tpl_vars['i']['index'] + 1;
	$this->_tpl_vars['i']['row'] = ceil($this->_tpl_vars['i']['order'] / $this->_tpl_vars['i']['columns']);
	$this->_tpl_vars['i']['column'] = $this->_tpl_vars['i']['order'] % $this->_tpl_vars['i']['columns'];
	if($this->_tpl_vars['i']['column'] == 0) $this->_tpl_vars['i']['column'] = $this->_tpl_vars['i']['columns'];
	if($this->_tpl_vars['i']['index'] < $this->_tpl_vars['i']['count']){
		list($this->_tpl_vars['i']['key'], $this->_tpl_vars['i']['value']) = each($this->_tpl_vars['markclassrows']);
		$this->_tpl_vars['i']['append'] = 0;
	}else{
		$this->_tpl_vars['i']['key'] = '';
		$this->_tpl_vars['i']['value'] = '';
		$this->_tpl_vars['i']['append'] = 1;
	}
	echo '
    <option value="'.$this->_tpl_vars['markclassrows'][$this->_tpl_vars['i']['key']]['classid'].'"';
if($this->_tpl_vars['classid'] == $this->_tpl_vars['markclassrows'][$this->_tpl_vars['i']['key']]['classid']){
echo ' selected="selected"';
}
echo '>'.$this->_tpl_vars['markclassrows'][$this->_tpl_vars['i']['key']]['caption'].'</option>
	';
}
echo '
  </select>
  ';
}
echo '
  </caption>
  <tr align="center">
    <th width="5%"><input type="checkbox" id="checkall" name="checkall" value="checkall" onclick="for (var i=0;i<this.form.elements.length;i++){ if (this.form.elements[i].name != \'checkkall\') this.form.elements[i].checked = this.form.checkall.checked; }"></th>
    <th width="21%">小说名称</th>
    <th width="30%">最新章节</th>
    <th width="30%">书签</th>
    <th width="7%">更新</th>
    <th width="7%">操作</th>
  </tr>
';
if (empty($this->_tpl_vars['bookcaserows'])) $this->_tpl_vars['bookcaserows'] = array();
elseif (!is_array($this->_tpl_vars['bookcaserows'])) $this->_tpl_vars['bookcaserows'] = (array)$this->_tpl_vars['bookcaserows'];
$this->_tpl_vars['i']=array();
$this->_tpl_vars['i']['columns'] = 1;
$this->_tpl_vars['i']['count'] = count($this->_tpl_vars['bookcaserows']);
$this->_tpl_vars['i']['addrows'] = count($this->_tpl_vars['bookcaserows']) % $this->_tpl_vars['i']['columns'] == 0 ? 0 : $this->_tpl_vars['i']['columns'] - count($this->_tpl_vars['bookcaserows']) % $this->_tpl_vars['i']['columns'];
$this->_tpl_vars['i']['loops'] = $this->_tpl_vars['i']['count'] + $this->_tpl_vars['i']['addrows'];
reset($this->_tpl_vars['bookcaserows']);
for($this->_tpl_vars['i']['index'] = 0; $this->_tpl_vars['i']['index'] < $this->_tpl_vars['i']['loops']; $this->_tpl_vars['i']['index']++){
	$this->_tpl_vars['i']['order'] = $this->_tpl_vars['i']['index'] + 1;
	$this->_tpl_vars['i']['row'] = ceil($this->_tpl_vars['i']['order'] / $this->_tpl_vars['i']['columns']);
	$this->_tpl_vars['i']['column'] = $this->_tpl_vars['i']['order'] % $this->_tpl_vars['i']['columns'];
	if($this->_tpl_vars['i']['column'] == 0) $this->_tpl_vars['i']['column'] = $this->_tpl_vars['i']['columns'];
	if($this->_tpl_vars['i']['index'] < $this->_tpl_vars['i']['count']){
		list($this->_tpl_vars['i']['key'], $this->_tpl_vars['i']['value']) = each($this->_tpl_vars['bookcaserows']);
		$this->_tpl_vars['i']['append'] = 0;
	}else{
		$this->_tpl_vars['i']['key'] = '';
		$this->_tpl_vars['i']['value'] = '';
		$this->_tpl_vars['i']['append'] = 1;
	}
	echo '
  <tr>
    <td align="center">
	<input type="checkbox" id="checkid[]" name="checkid[]" value="'.$this->_tpl_vars['bookcaserows'][$this->_tpl_vars['i']['key']]['caseid'].'">    </td>
    <td>';
if($this->_tpl_vars['bookcaserows'][$this->_tpl_vars['i']['key']]['hasnew'] == 1){
echo '<span class="hot">新</span>';
}
echo '<a href="'.$this->_tpl_vars['bookcaserows'][$this->_tpl_vars['i']['key']]['url_index'].'" target="_blank">'.$this->_tpl_vars['bookcaserows'][$this->_tpl_vars['i']['key']]['articlename'].'</a></td>
    <td>
	';
if($this->_tpl_vars['bookcaserows'][$this->_tpl_vars['i']['key']]['viptime'] > $this->_tpl_vars['bookcaserows'][$this->_tpl_vars['i']['key']]['freetime']){
echo '
	<a href="'.$this->_tpl_vars['jieqi_modules']['article']['url'].'/readbookcase.php?bid='.$this->_tpl_vars['bookcaserows'][$this->_tpl_vars['i']['key']]['caseid'].'&aid='.$this->_tpl_vars['bookcaserows'][$this->_tpl_vars['i']['key']]['articleid'].'&cid='.$this->_tpl_vars['bookcaserows'][$this->_tpl_vars['i']['key']]['vipchapterid'].'" target="_blank">'.$this->_tpl_vars['bookcaserows'][$this->_tpl_vars['i']['key']]['vipchapter'].'</a><em class="hot">vip</em>
	';
}else{
echo '
	<a href="'.$this->_tpl_vars['jieqi_modules']['article']['url'].'/readbookcase.php?bid='.$this->_tpl_vars['bookcaserows'][$this->_tpl_vars['i']['key']]['caseid'].'&aid='.$this->_tpl_vars['bookcaserows'][$this->_tpl_vars['i']['key']]['articleid'].'&cid='.$this->_tpl_vars['bookcaserows'][$this->_tpl_vars['i']['key']]['lastchapterid'].'" target="_blank">'.$this->_tpl_vars['bookcaserows'][$this->_tpl_vars['i']['key']]['lastchapter'].'</a>
	';
}
echo '
	</td>
    <td><a href="'.$this->_tpl_vars['jieqi_modules']['article']['url'].'/readbookcase.php?bid='.$this->_tpl_vars['bookcaserows'][$this->_tpl_vars['i']['key']]['caseid'].'&aid='.$this->_tpl_vars['bookcaserows'][$this->_tpl_vars['i']['key']]['articleid'].'&cid='.$this->_tpl_vars['bookcaserows'][$this->_tpl_vars['i']['key']]['chapterid'].'" target="_blank">'.$this->_tpl_vars['bookcaserows'][$this->_tpl_vars['i']['key']]['articlemark'].'</a></td>
    <td align="center">'.date('m-d',$this->_tpl_vars['bookcaserows'][$this->_tpl_vars['i']['key']]['lastupdate']).'</td>
    <td align="center"><a id="act_delete_'.$this->_tpl_vars['bookcaserows'][$this->_tpl_vars['i']['key']]['caseid'].'" href="javascript:;" onclick="act_delete(\''.$this->_tpl_vars['jieqi_modules']['article']['url'].'/bookcase.php?bid='.$this->_tpl_vars['bookcaserows'][$this->_tpl_vars['i']['key']]['caseid'].'&act=delete'.$this->_tpl_vars['jieqi_token_url'].'\');">移除</a></td>
';
}
echo '
  </tr>
  <tr>
    <td colspan="6" align="center" class="foot">选中项目
	<select name="newclassid" id="newclassid">
	<option value="-1" selected="selected">移出书架</option>
	';
if($this->_tpl_vars['maxmarkclass'] > 0){
echo '<option value="0">移到默认书架</option>';
}
echo '
	';
if (empty($this->_tpl_vars['markclassrows'])) $this->_tpl_vars['markclassrows'] = array();
elseif (!is_array($this->_tpl_vars['markclassrows'])) $this->_tpl_vars['markclassrows'] = (array)$this->_tpl_vars['markclassrows'];
$this->_tpl_vars['i']=array();
$this->_tpl_vars['i']['columns'] = 1;
$this->_tpl_vars['i']['count'] = count($this->_tpl_vars['markclassrows']);
$this->_tpl_vars['i']['addrows'] = count($this->_tpl_vars['markclassrows']) % $this->_tpl_vars['i']['columns'] == 0 ? 0 : $this->_tpl_vars['i']['columns'] - count($this->_tpl_vars['markclassrows']) % $this->_tpl_vars['i']['columns'];
$this->_tpl_vars['i']['loops'] = $this->_tpl_vars['i']['count'] + $this->_tpl_vars['i']['addrows'];
reset($this->_tpl_vars['markclassrows']);
for($this->_tpl_vars['i']['index'] = 0; $this->_tpl_vars['i']['index'] < $this->_tpl_vars['i']['loops']; $this->_tpl_vars['i']['index']++){
	$this->_tpl_vars['i']['order'] = $this->_tpl_vars['i']['index'] + 1;
	$this->_tpl_vars['i']['row'] = ceil($this->_tpl_vars['i']['order'] / $this->_tpl_vars['i']['columns']);
	$this->_tpl_vars['i']['column'] = $this->_tpl_vars['i']['order'] % $this->_tpl_vars['i']['columns'];
	if($this->_tpl_vars['i']['column'] == 0) $this->_tpl_vars['i']['column'] = $this->_tpl_vars['i']['columns'];
	if($this->_tpl_vars['i']['index'] < $this->_tpl_vars['i']['count']){
		list($this->_tpl_vars['i']['key'], $this->_tpl_vars['i']['value']) = each($this->_tpl_vars['markclassrows']);
		$this->_tpl_vars['i']['append'] = 0;
	}else{
		$this->_tpl_vars['i']['key'] = '';
		$this->_tpl_vars['i']['value'] = '';
		$this->_tpl_vars['i']['append'] = 1;
	}
	echo '
    <option value="'.$this->_tpl_vars['markclassrows'][$this->_tpl_vars['i']['key']]['classid'].'">移到'.$this->_tpl_vars['markclassrows'][$this->_tpl_vars['i']['key']]['caption'].'</option>
	';
}
echo '
    </select> 
    <button type="submit" name="btnsubmit" class="button"> 确认 </button>
	<input type="hidden" name="clsssid" value="'.$this->_tpl_vars['classid'].'" />
	<input type="hidden" name="act" value="move">'.$this->_tpl_vars['jieqi_token_input'].'
	</td>
  </tr>
</table>
</form>

';
?>