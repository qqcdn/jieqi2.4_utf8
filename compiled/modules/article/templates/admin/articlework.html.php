<?php
echo '

<table class="grid" width="100%" align="center">
<caption>
  更新排行榜
  <select class="select" size="1" onchange="document.location.href=\'?order=\'+this.options[this.options.selectedIndex].value;" name="toporder">
    <option value="monthupds"';
if($this->_tpl_vars['_request']['order'] == 'monthupds'){
echo ' selected="selected"';
}
echo '>按本月更新天数</option>
    <option value="monthwords"';
if($this->_tpl_vars['_request']['order'] == 'monthwords'){
echo ' selected="selected"';
}
echo '>按本月更新字数</option>
    <option value="preupds"';
if($this->_tpl_vars['_request']['order'] == 'preupds'){
echo ' selected="selected"';
}
echo '>按上月更新天数</option>
    <option value="prewords"';
if($this->_tpl_vars['_request']['order'] == 'prewords'){
echo ' selected="selected"';
}
echo '>按上月更新字数</option>
    <option value="words"';
if($this->_tpl_vars['_request']['order'] == 'words'){
echo ' selected="selected"';
}
echo '>按总字数</option>
  </select>
</caption>
  <tr align="center">
    <th width="15%">小说名称</th>
    <th width="25%">最新章节</th>
    <th width="10%">作者</th>
    <th width="8%">总字数</th>
    <th width="16%">上月更新</th>
    <th width="16%">本月更新</th>
    <th width="12%">更新时间</th>
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
    <td><a href="'.jieqi_geturl('article','article',$this->_tpl_vars['articlerows'][$this->_tpl_vars['i']['key']]['articleid'],'info',$this->_tpl_vars['articlerows'][$this->_tpl_vars['i']['key']]['articlecode']).'" target="_blank">'.$this->_tpl_vars['articlerows'][$this->_tpl_vars['i']['key']]['articlename'].'</a>';
if($this->_tpl_vars['articlerows'][$this->_tpl_vars['i']['key']]['isvip_n'] > 0){
echo '<span class="hot">vip</span>';
}
echo '</td>
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
    <td align="center">'.$this->_tpl_vars['articlerows'][$this->_tpl_vars['i']['key']]['words'].'</td>
    <td align="center">';
if($this->_tpl_vars['articlerows'][$this->_tpl_vars['i']['key']]['prework'] > 0){
echo '<strong class="hot">'.$this->_tpl_vars['articlerows'][$this->_tpl_vars['i']['key']]['preupds'].'天：'.$this->_tpl_vars['articlerows'][$this->_tpl_vars['i']['key']]['prewords'].'字</strong>';
}else{
echo $this->_tpl_vars['articlerows'][$this->_tpl_vars['i']['key']]['preupds'].'天：'.$this->_tpl_vars['articlerows'][$this->_tpl_vars['i']['key']]['prewords'].'字';
}
echo '</td>
    <td align="center">';
if($this->_tpl_vars['articlerows'][$this->_tpl_vars['i']['key']]['monthwork'] > 0){
echo '<strong class="hot">'.$this->_tpl_vars['articlerows'][$this->_tpl_vars['i']['key']]['monthupds'].'天：'.$this->_tpl_vars['articlerows'][$this->_tpl_vars['i']['key']]['monthwords'].'字</strong>';
}else{
echo $this->_tpl_vars['articlerows'][$this->_tpl_vars['i']['key']]['monthupds'].'天：'.$this->_tpl_vars['articlerows'][$this->_tpl_vars['i']['key']]['monthwords'].'字';
}
echo '</td>
    <td align="center">'.date('Y-m-d',$this->_tpl_vars['articlerows'][$this->_tpl_vars['i']['key']]['lastupdate']).'</td>
  </tr>
  ';
}
echo '
  </tbody>
</table>

<div class="pages">'.$this->_tpl_vars['url_jumppage'].'</div>';
?>