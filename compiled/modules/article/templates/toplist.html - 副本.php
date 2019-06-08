<?php
echo '

<table class="grid" width="100%" align="center">
<caption>
	<div class="fl">
	&nbsp;&nbsp;
	<span class="dropdown">'.$this->_tpl_vars['ordername'].'<b class="dropico"></b>
	<ul class="droplist">
	';
if (empty($this->_tpl_vars['toprows'])) $this->_tpl_vars['toprows'] = array();
elseif (!is_array($this->_tpl_vars['toprows'])) $this->_tpl_vars['toprows'] = (array)$this->_tpl_vars['toprows'];
$this->_tpl_vars['i']=array();
$this->_tpl_vars['i']['columns'] = 1;
$this->_tpl_vars['i']['count'] = count($this->_tpl_vars['toprows']);
$this->_tpl_vars['i']['addrows'] = count($this->_tpl_vars['toprows']) % $this->_tpl_vars['i']['columns'] == 0 ? 0 : $this->_tpl_vars['i']['columns'] - count($this->_tpl_vars['toprows']) % $this->_tpl_vars['i']['columns'];
$this->_tpl_vars['i']['loops'] = $this->_tpl_vars['i']['count'] + $this->_tpl_vars['i']['addrows'];
reset($this->_tpl_vars['toprows']);
for($this->_tpl_vars['i']['index'] = 0; $this->_tpl_vars['i']['index'] < $this->_tpl_vars['i']['loops']; $this->_tpl_vars['i']['index']++){
	$this->_tpl_vars['i']['order'] = $this->_tpl_vars['i']['index'] + 1;
	$this->_tpl_vars['i']['row'] = ceil($this->_tpl_vars['i']['order'] / $this->_tpl_vars['i']['columns']);
	$this->_tpl_vars['i']['column'] = $this->_tpl_vars['i']['order'] % $this->_tpl_vars['i']['columns'];
	if($this->_tpl_vars['i']['column'] == 0) $this->_tpl_vars['i']['column'] = $this->_tpl_vars['i']['columns'];
	if($this->_tpl_vars['i']['index'] < $this->_tpl_vars['i']['count']){
		list($this->_tpl_vars['i']['key'], $this->_tpl_vars['i']['value']) = each($this->_tpl_vars['toprows']);
		$this->_tpl_vars['i']['append'] = 0;
	}else{
		$this->_tpl_vars['i']['key'] = '';
		$this->_tpl_vars['i']['value'] = '';
		$this->_tpl_vars['i']['append'] = 1;
	}
	echo '
	';
if($this->_tpl_vars['toprows'][$this->_tpl_vars['i']['key']]['publish'] > 0){
echo '
	<li><a href="'.jieqi_geturl('article','toplist','1',$this->_tpl_vars['i']['key'],$this->_tpl_vars['sortid'],$this->_tpl_vars['fullflag']).'"';
if($this->_tpl_vars['order'] == $this->_tpl_vars['i']['key']){
echo ' class="hot"';
}
echo '>'.$this->_tpl_vars['toprows'][$this->_tpl_vars['i']['key']]['caption'].'</a></li>
	';
}
echo '
	';
}
echo '
	</ul>
	</span>
	&nbsp;&nbsp;
	<span class="dropdown">';
if($this->_tpl_vars['sort'] == ''){
echo '全部分类';
}else{
echo $this->_tpl_vars['sort'];
}
echo '<b class="dropico"></b>
	<ul class="droplist">
	<li><a href="'.jieqi_geturl('article','toplist','1',$this->_tpl_vars['order'],'0',$this->_tpl_vars['fullflag']).'" ';
if($this->_tpl_vars['sortid'] == 0){
echo ' class="hot"';
}
echo '>全部分类</a></li>
	';
if (empty($this->_tpl_vars['sortrows'])) $this->_tpl_vars['sortrows'] = array();
elseif (!is_array($this->_tpl_vars['sortrows'])) $this->_tpl_vars['sortrows'] = (array)$this->_tpl_vars['sortrows'];
$this->_tpl_vars['i']=array();
$this->_tpl_vars['i']['columns'] = 1;
$this->_tpl_vars['i']['count'] = count($this->_tpl_vars['sortrows']);
$this->_tpl_vars['i']['addrows'] = count($this->_tpl_vars['sortrows']) % $this->_tpl_vars['i']['columns'] == 0 ? 0 : $this->_tpl_vars['i']['columns'] - count($this->_tpl_vars['sortrows']) % $this->_tpl_vars['i']['columns'];
$this->_tpl_vars['i']['loops'] = $this->_tpl_vars['i']['count'] + $this->_tpl_vars['i']['addrows'];
reset($this->_tpl_vars['sortrows']);
for($this->_tpl_vars['i']['index'] = 0; $this->_tpl_vars['i']['index'] < $this->_tpl_vars['i']['loops']; $this->_tpl_vars['i']['index']++){
	$this->_tpl_vars['i']['order'] = $this->_tpl_vars['i']['index'] + 1;
	$this->_tpl_vars['i']['row'] = ceil($this->_tpl_vars['i']['order'] / $this->_tpl_vars['i']['columns']);
	$this->_tpl_vars['i']['column'] = $this->_tpl_vars['i']['order'] % $this->_tpl_vars['i']['columns'];
	if($this->_tpl_vars['i']['column'] == 0) $this->_tpl_vars['i']['column'] = $this->_tpl_vars['i']['columns'];
	if($this->_tpl_vars['i']['index'] < $this->_tpl_vars['i']['count']){
		list($this->_tpl_vars['i']['key'], $this->_tpl_vars['i']['value']) = each($this->_tpl_vars['sortrows']);
		$this->_tpl_vars['i']['append'] = 0;
	}else{
		$this->_tpl_vars['i']['key'] = '';
		$this->_tpl_vars['i']['value'] = '';
		$this->_tpl_vars['i']['append'] = 1;
	}
	echo '
	<li><a href="'.jieqi_geturl('article','toplist','1',$this->_tpl_vars['order'],$this->_tpl_vars['i']['key'],$this->_tpl_vars['fullflag']).'" ';
if($this->_tpl_vars['sortid'] == $this->_tpl_vars['i']['key']){
echo ' class="hot"';
}
echo '>'.$this->_tpl_vars['sortrows'][$this->_tpl_vars['i']['key']]['caption'].'</a></li>
	';
}
echo '
	</ul>
	</span>
	</div>
	<div class="fr">
	<label class="checkbox fsb nw"><input type="checkbox" name="showfull" value="1"';
if($this->_tpl_vars['fullflag'] > 0){
echo ' checked="checked"';
}
echo ' onclick="if(this.checked) document.location=\''.jieqi_geturl('article','toplist','1',$this->_tpl_vars['order'],$this->_tpl_vars['sortid'],'1').'\';else document.location=\''.jieqi_geturl('article','toplist','1',$this->_tpl_vars['order'],$this->_tpl_vars['sortid'],'0').'\';">只看全本</label>
	</div>
</caption>
  <tr align="center">
    <th width="25%">小说名称</th>
    <th width="30%">最新章节</th>
    <th width="15%">作者</th>
	<th width="8%">';
if($this->_tpl_vars['orderfield'] == 'lastupdate'){
echo '字数';
}else{
echo $this->_tpl_vars['ordername'];
}
echo '</th>
    <th width="15%">更新</th>
    <th width="7%">状态</th>
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
    <td><a href="'.$this->_tpl_vars['articlerows'][$this->_tpl_vars['i']['key']]['url_articleindex'].'" class="note iconfont" target="_blank" title="目录">&#xee32;</a><a href="'.$this->_tpl_vars['articlerows'][$this->_tpl_vars['i']['key']]['url_articleinfo'].'">'.$this->_tpl_vars['articlerows'][$this->_tpl_vars['i']['key']]['articlename'].'</a></td>
    <td>';
if($this->_tpl_vars['articlerows'][$this->_tpl_vars['i']['key']]['vipchapterid'] > 0){
echo '<a href="'.$this->_tpl_vars['articlerows'][$this->_tpl_vars['i']['key']]['url_vipchapter'].'" target="_blank">'.$this->_tpl_vars['articlerows'][$this->_tpl_vars['i']['key']]['vipvolume'].' '.$this->_tpl_vars['articlerows'][$this->_tpl_vars['i']['key']]['vipchapter'].'</a><span class="hot">vip</span>';
}else{
echo '<a href="'.$this->_tpl_vars['articlerows'][$this->_tpl_vars['i']['key']]['url_lastchapter'].'" target="_blank">'.$this->_tpl_vars['articlerows'][$this->_tpl_vars['i']['key']]['lastvolume'].' '.$this->_tpl_vars['articlerows'][$this->_tpl_vars['i']['key']]['lastchapter'].'</a>';
}
echo '</td>
    <td>';
if($this->_tpl_vars['articlerows'][$this->_tpl_vars['i']['key']]['authorid'] == 0){
echo $this->_tpl_vars['articlerows'][$this->_tpl_vars['i']['key']]['author'];
}else{
echo '<a href="'.$this->_tpl_vars['jieqi_modules']['article']['url'].'/authorpage.php?id='.$this->_tpl_vars['articlerows'][$this->_tpl_vars['i']['key']]['authorid'].'" target="_blank">'.$this->_tpl_vars['articlerows'][$this->_tpl_vars['i']['key']]['author'].'</a>';
}
echo '</td>
		<td>';
if($this->_tpl_vars['orderfield'] == 'lastupdate'){
echo $this->_tpl_vars['articlerows'][$this->_tpl_vars['i']['key']]['words'];
}else{
echo $this->_tpl_vars['articlerows'][$this->_tpl_vars['i']['key']]['ordervalue'];
}
echo '</td>
    <td align="center">'.date('Y-m-d',$this->_tpl_vars['articlerows'][$this->_tpl_vars['i']['key']]['lastupdate']).'</td>
    <td align="center">'.$this->_tpl_vars['articlerows'][$this->_tpl_vars['i']['key']]['fullflag'].'</td>
  </tr>
  ';
}
echo '
  </tbody>
</table>
<div class="pages">'.$this->_tpl_vars['url_jumppage'].'</div>';
?>