<?php
echo '

<table class="grid" width="100%" align="center">
<caption>
《<a href="'.jieqi_geturl('article','article',$this->_tpl_vars['articleid'],'info',$this->_tpl_vars['articlecode']).'">'.$this->_tpl_vars['articlename'].'</a>》TXT单章节下载
</caption>
  <tr align="center">
    <th width="10%">序号</th>
	<th width="40%">章节名</th>
	<th width="10%">字数</th>
    <th width="15%">时间</th>
    <th width="15%">下载</th>
  </tr>
  ';
if (empty($this->_tpl_vars['packrows'])) $this->_tpl_vars['packrows'] = array();
elseif (!is_array($this->_tpl_vars['packrows'])) $this->_tpl_vars['packrows'] = (array)$this->_tpl_vars['packrows'];
$this->_tpl_vars['i']=array();
$this->_tpl_vars['i']['columns'] = 1;
$this->_tpl_vars['i']['count'] = count($this->_tpl_vars['packrows']);
$this->_tpl_vars['i']['addrows'] = count($this->_tpl_vars['packrows']) % $this->_tpl_vars['i']['columns'] == 0 ? 0 : $this->_tpl_vars['i']['columns'] - count($this->_tpl_vars['packrows']) % $this->_tpl_vars['i']['columns'];
$this->_tpl_vars['i']['loops'] = $this->_tpl_vars['i']['count'] + $this->_tpl_vars['i']['addrows'];
reset($this->_tpl_vars['packrows']);
for($this->_tpl_vars['i']['index'] = 0; $this->_tpl_vars['i']['index'] < $this->_tpl_vars['i']['loops']; $this->_tpl_vars['i']['index']++){
	$this->_tpl_vars['i']['order'] = $this->_tpl_vars['i']['index'] + 1;
	$this->_tpl_vars['i']['row'] = ceil($this->_tpl_vars['i']['order'] / $this->_tpl_vars['i']['columns']);
	$this->_tpl_vars['i']['column'] = $this->_tpl_vars['i']['order'] % $this->_tpl_vars['i']['columns'];
	if($this->_tpl_vars['i']['column'] == 0) $this->_tpl_vars['i']['column'] = $this->_tpl_vars['i']['columns'];
	if($this->_tpl_vars['i']['index'] < $this->_tpl_vars['i']['count']){
		list($this->_tpl_vars['i']['key'], $this->_tpl_vars['i']['value']) = each($this->_tpl_vars['packrows']);
		$this->_tpl_vars['i']['append'] = 0;
	}else{
		$this->_tpl_vars['i']['key'] = '';
		$this->_tpl_vars['i']['value'] = '';
		$this->_tpl_vars['i']['append'] = 1;
	}
	echo '
  <tr>
    <td align="center">'.$this->_tpl_vars['i']['order'].'</td>
    <td>'.$this->_tpl_vars['packrows'][$this->_tpl_vars['i']['key']]['volumename'].' '.$this->_tpl_vars['packrows'][$this->_tpl_vars['i']['key']]['chaptername'];
if($this->_tpl_vars['packrows'][$this->_tpl_vars['i']['key']]['isvip'] > 0){
echo '<em class="iconfont hot">&#xee8b;</em>';
}
echo '</td>
	<td align="center">'.$this->_tpl_vars['packrows'][$this->_tpl_vars['i']['key']]['words'].'</td>
    <td align="center">'.date('Y-m-d',$this->_tpl_vars['packrows'][$this->_tpl_vars['i']['key']]['postdate']).'</td>
    <td align="center">';
if($this->_tpl_vars['packrows'][$this->_tpl_vars['i']['key']]['isvip'] > 0){
echo '<a href="'.jieqi_geturl('article','chapter',$this->_tpl_vars['packrows'][$this->_tpl_vars['i']['key']]['chapterid'],$this->_tpl_vars['articleid'],$this->_tpl_vars['packrows'][$this->_tpl_vars['i']['key']]['isvip'],$this->_tpl_vars['articlecode']).'" rel="nofollow">在线阅读</a>';
}else{
echo '<a href="'.$this->_tpl_vars['article_static_url'].'/packdown.php?type=txt&id='.$this->_tpl_vars['articleid'].'&cid='.$this->_tpl_vars['packrows'][$this->_tpl_vars['i']['key']]['chapterid'].'" rel="nofollow">下载本章</a>';
}
echo '</td>
  </tr>
  ';
}
echo '
</table>';
?>