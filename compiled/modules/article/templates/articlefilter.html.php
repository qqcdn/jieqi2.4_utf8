<?php
echo '
<table width="100%" class="grid" cellspacing="1" align="center">
<caption>筛选条件</caption>
';
if(count($this->_tpl_vars['rgrouprows']) > 1){
echo '
<tr valign="middle" align="left">
  <td class="tdl" width="100"><strong>所属频道：</strong></td>
  <td class="tdr">
    ';
if (empty($this->_tpl_vars['rgrouprows'])) $this->_tpl_vars['rgrouprows'] = array();
elseif (!is_array($this->_tpl_vars['rgrouprows'])) $this->_tpl_vars['rgrouprows'] = (array)$this->_tpl_vars['rgrouprows'];
$this->_tpl_vars['i']=array();
$this->_tpl_vars['i']['columns'] = 1;
$this->_tpl_vars['i']['count'] = count($this->_tpl_vars['rgrouprows']);
$this->_tpl_vars['i']['addrows'] = count($this->_tpl_vars['rgrouprows']) % $this->_tpl_vars['i']['columns'] == 0 ? 0 : $this->_tpl_vars['i']['columns'] - count($this->_tpl_vars['rgrouprows']) % $this->_tpl_vars['i']['columns'];
$this->_tpl_vars['i']['loops'] = $this->_tpl_vars['i']['count'] + $this->_tpl_vars['i']['addrows'];
reset($this->_tpl_vars['rgrouprows']);
for($this->_tpl_vars['i']['index'] = 0; $this->_tpl_vars['i']['index'] < $this->_tpl_vars['i']['loops']; $this->_tpl_vars['i']['index']++){
	$this->_tpl_vars['i']['order'] = $this->_tpl_vars['i']['index'] + 1;
	$this->_tpl_vars['i']['row'] = ceil($this->_tpl_vars['i']['order'] / $this->_tpl_vars['i']['columns']);
	$this->_tpl_vars['i']['column'] = $this->_tpl_vars['i']['order'] % $this->_tpl_vars['i']['columns'];
	if($this->_tpl_vars['i']['column'] == 0) $this->_tpl_vars['i']['column'] = $this->_tpl_vars['i']['columns'];
	if($this->_tpl_vars['i']['index'] < $this->_tpl_vars['i']['count']){
		list($this->_tpl_vars['i']['key'], $this->_tpl_vars['i']['value']) = each($this->_tpl_vars['rgrouprows']);
		$this->_tpl_vars['i']['append'] = 0;
	}else{
		$this->_tpl_vars['i']['key'] = '';
		$this->_tpl_vars['i']['value'] = '';
		$this->_tpl_vars['i']['append'] = 1;
	}
	echo '
    ';
if($this->_tpl_vars['rgrouprows'][$this->_tpl_vars['i']['key']]['selected'] > 0){
echo '
    <a href="'.$this->_tpl_vars['rgrouprows'][$this->_tpl_vars['i']['key']]['url'].'" class="hot"><strong>'.$this->_tpl_vars['rgrouprows'][$this->_tpl_vars['i']['key']]['name'].'</strong></a>&nbsp;
    ';
}else{
echo '
    <a href="'.$this->_tpl_vars['rgrouprows'][$this->_tpl_vars['i']['key']]['url'].'">'.$this->_tpl_vars['rgrouprows'][$this->_tpl_vars['i']['key']]['name'].'</a>&nbsp;
    ';
}
echo '
    ';
}
echo '
  </td>
</tr>
';
}
echo '
<tr valign="middle" align="left">
  <td class="tdl" width="100"><strong>作品分类：</strong></td>
  <td class="tdr">
  ';
if(count($this->_tpl_vars['rgrouprows']) > 1){
echo '
    ';
if (empty($this->_tpl_vars['rgrouprows'])) $this->_tpl_vars['rgrouprows'] = array();
elseif (!is_array($this->_tpl_vars['rgrouprows'])) $this->_tpl_vars['rgrouprows'] = (array)$this->_tpl_vars['rgrouprows'];
$this->_tpl_vars['i']=array();
$this->_tpl_vars['i']['columns'] = 1;
$this->_tpl_vars['i']['count'] = count($this->_tpl_vars['rgrouprows']);
$this->_tpl_vars['i']['addrows'] = count($this->_tpl_vars['rgrouprows']) % $this->_tpl_vars['i']['columns'] == 0 ? 0 : $this->_tpl_vars['i']['columns'] - count($this->_tpl_vars['rgrouprows']) % $this->_tpl_vars['i']['columns'];
$this->_tpl_vars['i']['loops'] = $this->_tpl_vars['i']['count'] + $this->_tpl_vars['i']['addrows'];
reset($this->_tpl_vars['rgrouprows']);
for($this->_tpl_vars['i']['index'] = 0; $this->_tpl_vars['i']['index'] < $this->_tpl_vars['i']['loops']; $this->_tpl_vars['i']['index']++){
	$this->_tpl_vars['i']['order'] = $this->_tpl_vars['i']['index'] + 1;
	$this->_tpl_vars['i']['row'] = ceil($this->_tpl_vars['i']['order'] / $this->_tpl_vars['i']['columns']);
	$this->_tpl_vars['i']['column'] = $this->_tpl_vars['i']['order'] % $this->_tpl_vars['i']['columns'];
	if($this->_tpl_vars['i']['column'] == 0) $this->_tpl_vars['i']['column'] = $this->_tpl_vars['i']['columns'];
	if($this->_tpl_vars['i']['index'] < $this->_tpl_vars['i']['count']){
		list($this->_tpl_vars['i']['key'], $this->_tpl_vars['i']['value']) = each($this->_tpl_vars['rgrouprows']);
		$this->_tpl_vars['i']['append'] = 0;
	}else{
		$this->_tpl_vars['i']['key'] = '';
		$this->_tpl_vars['i']['value'] = '';
		$this->_tpl_vars['i']['append'] = 1;
	}
	echo '
      ';
if($this->_tpl_vars['rgrouprows'][$this->_tpl_vars['i']['key']]['id'] > 0){
echo '
      <p>
      ';
if (empty($this->_tpl_vars['sortidrows'])) $this->_tpl_vars['sortidrows'] = array();
elseif (!is_array($this->_tpl_vars['sortidrows'])) $this->_tpl_vars['sortidrows'] = (array)$this->_tpl_vars['sortidrows'];
$this->_tpl_vars['j']=array();
$this->_tpl_vars['j']['columns'] = 1;
$this->_tpl_vars['j']['count'] = count($this->_tpl_vars['sortidrows']);
$this->_tpl_vars['j']['addrows'] = count($this->_tpl_vars['sortidrows']) % $this->_tpl_vars['j']['columns'] == 0 ? 0 : $this->_tpl_vars['j']['columns'] - count($this->_tpl_vars['sortidrows']) % $this->_tpl_vars['j']['columns'];
$this->_tpl_vars['j']['loops'] = $this->_tpl_vars['j']['count'] + $this->_tpl_vars['j']['addrows'];
reset($this->_tpl_vars['sortidrows']);
for($this->_tpl_vars['j']['index'] = 0; $this->_tpl_vars['j']['index'] < $this->_tpl_vars['j']['loops']; $this->_tpl_vars['j']['index']++){
	$this->_tpl_vars['j']['order'] = $this->_tpl_vars['j']['index'] + 1;
	$this->_tpl_vars['j']['row'] = ceil($this->_tpl_vars['j']['order'] / $this->_tpl_vars['j']['columns']);
	$this->_tpl_vars['j']['column'] = $this->_tpl_vars['j']['order'] % $this->_tpl_vars['j']['columns'];
	if($this->_tpl_vars['j']['column'] == 0) $this->_tpl_vars['j']['column'] = $this->_tpl_vars['j']['columns'];
	if($this->_tpl_vars['j']['index'] < $this->_tpl_vars['j']['count']){
		list($this->_tpl_vars['j']['key'], $this->_tpl_vars['j']['value']) = each($this->_tpl_vars['sortidrows']);
		$this->_tpl_vars['j']['append'] = 0;
	}else{
		$this->_tpl_vars['j']['key'] = '';
		$this->_tpl_vars['j']['value'] = '';
		$this->_tpl_vars['j']['append'] = 1;
	}
	echo '
      ';
if($this->_tpl_vars['sortidrows'][$this->_tpl_vars['j']['key']]['rgroup'] == $this->_tpl_vars['rgrouprows'][$this->_tpl_vars['i']['key']]['id']){
echo '
      ';
if($this->_tpl_vars['sortidrows'][$this->_tpl_vars['j']['key']]['selected'] > 0){
echo '
      <a href="'.$this->_tpl_vars['sortidrows'][$this->_tpl_vars['j']['key']]['url'].'" class="hot"><strong>'.$this->_tpl_vars['sortidrows'][$this->_tpl_vars['j']['key']]['name'].'</strong></a>&nbsp;
      ';
}else{
echo '
      <a href="'.$this->_tpl_vars['sortidrows'][$this->_tpl_vars['j']['key']]['url'].'">'.$this->_tpl_vars['sortidrows'][$this->_tpl_vars['j']['key']]['name'].'</a>&nbsp;
      ';
}
echo '
      ';
}
echo '
      ';
}
echo '
      <p>
      ';
if($this->_tpl_vars['nowrgroup'] == $this->_tpl_vars['rgrouprows'][$this->_tpl_vars['i']['key']]['id'] && count($this->_tpl_vars['typeidrows']) > 0){
echo '
      <div class="textbox">
      ';
if (empty($this->_tpl_vars['typeidrows'])) $this->_tpl_vars['typeidrows'] = array();
elseif (!is_array($this->_tpl_vars['typeidrows'])) $this->_tpl_vars['typeidrows'] = (array)$this->_tpl_vars['typeidrows'];
$this->_tpl_vars['k']=array();
$this->_tpl_vars['k']['columns'] = 1;
$this->_tpl_vars['k']['count'] = count($this->_tpl_vars['typeidrows']);
$this->_tpl_vars['k']['addrows'] = count($this->_tpl_vars['typeidrows']) % $this->_tpl_vars['k']['columns'] == 0 ? 0 : $this->_tpl_vars['k']['columns'] - count($this->_tpl_vars['typeidrows']) % $this->_tpl_vars['k']['columns'];
$this->_tpl_vars['k']['loops'] = $this->_tpl_vars['k']['count'] + $this->_tpl_vars['k']['addrows'];
reset($this->_tpl_vars['typeidrows']);
for($this->_tpl_vars['k']['index'] = 0; $this->_tpl_vars['k']['index'] < $this->_tpl_vars['k']['loops']; $this->_tpl_vars['k']['index']++){
	$this->_tpl_vars['k']['order'] = $this->_tpl_vars['k']['index'] + 1;
	$this->_tpl_vars['k']['row'] = ceil($this->_tpl_vars['k']['order'] / $this->_tpl_vars['k']['columns']);
	$this->_tpl_vars['k']['column'] = $this->_tpl_vars['k']['order'] % $this->_tpl_vars['k']['columns'];
	if($this->_tpl_vars['k']['column'] == 0) $this->_tpl_vars['k']['column'] = $this->_tpl_vars['k']['columns'];
	if($this->_tpl_vars['k']['index'] < $this->_tpl_vars['k']['count']){
		list($this->_tpl_vars['k']['key'], $this->_tpl_vars['k']['value']) = each($this->_tpl_vars['typeidrows']);
		$this->_tpl_vars['k']['append'] = 0;
	}else{
		$this->_tpl_vars['k']['key'] = '';
		$this->_tpl_vars['k']['value'] = '';
		$this->_tpl_vars['k']['append'] = 1;
	}
	echo '
      ';
if($this->_tpl_vars['typeidrows'][$this->_tpl_vars['k']['key']]['selected'] > 0){
echo '
      <a href="'.$this->_tpl_vars['typeidrows'][$this->_tpl_vars['k']['key']]['url'].'" class="hot"><strong>'.$this->_tpl_vars['typeidrows'][$this->_tpl_vars['k']['key']]['name'].'</strong></a>&nbsp;
      ';
}else{
echo '
      <a href="'.$this->_tpl_vars['typeidrows'][$this->_tpl_vars['k']['key']]['url'].'">'.$this->_tpl_vars['typeidrows'][$this->_tpl_vars['k']['key']]['name'].'</a>&nbsp;
      ';
}
echo '
      ';
}
echo '
      </div>
      ';
}
echo '
      ';
}
echo '
    ';
}
echo '
  ';
}else{
echo '
    ';
if (empty($this->_tpl_vars['sortidrows'])) $this->_tpl_vars['sortidrows'] = array();
elseif (!is_array($this->_tpl_vars['sortidrows'])) $this->_tpl_vars['sortidrows'] = (array)$this->_tpl_vars['sortidrows'];
$this->_tpl_vars['i']=array();
$this->_tpl_vars['i']['columns'] = 1;
$this->_tpl_vars['i']['count'] = count($this->_tpl_vars['sortidrows']);
$this->_tpl_vars['i']['addrows'] = count($this->_tpl_vars['sortidrows']) % $this->_tpl_vars['i']['columns'] == 0 ? 0 : $this->_tpl_vars['i']['columns'] - count($this->_tpl_vars['sortidrows']) % $this->_tpl_vars['i']['columns'];
$this->_tpl_vars['i']['loops'] = $this->_tpl_vars['i']['count'] + $this->_tpl_vars['i']['addrows'];
reset($this->_tpl_vars['sortidrows']);
for($this->_tpl_vars['i']['index'] = 0; $this->_tpl_vars['i']['index'] < $this->_tpl_vars['i']['loops']; $this->_tpl_vars['i']['index']++){
	$this->_tpl_vars['i']['order'] = $this->_tpl_vars['i']['index'] + 1;
	$this->_tpl_vars['i']['row'] = ceil($this->_tpl_vars['i']['order'] / $this->_tpl_vars['i']['columns']);
	$this->_tpl_vars['i']['column'] = $this->_tpl_vars['i']['order'] % $this->_tpl_vars['i']['columns'];
	if($this->_tpl_vars['i']['column'] == 0) $this->_tpl_vars['i']['column'] = $this->_tpl_vars['i']['columns'];
	if($this->_tpl_vars['i']['index'] < $this->_tpl_vars['i']['count']){
		list($this->_tpl_vars['i']['key'], $this->_tpl_vars['i']['value']) = each($this->_tpl_vars['sortidrows']);
		$this->_tpl_vars['i']['append'] = 0;
	}else{
		$this->_tpl_vars['i']['key'] = '';
		$this->_tpl_vars['i']['value'] = '';
		$this->_tpl_vars['i']['append'] = 1;
	}
	echo '
      ';
if($this->_tpl_vars['sortidrows'][$this->_tpl_vars['i']['key']]['selected'] > 0){
echo '
      <a href="'.$this->_tpl_vars['sortidrows'][$this->_tpl_vars['i']['key']]['url'].'" class="hot"><strong>'.$this->_tpl_vars['sortidrows'][$this->_tpl_vars['i']['key']]['name'].'</strong></a>&nbsp;
      ';
}else{
echo '
      <a href="'.$this->_tpl_vars['sortidrows'][$this->_tpl_vars['i']['key']]['url'].'">'.$this->_tpl_vars['sortidrows'][$this->_tpl_vars['i']['key']]['name'].'</a>&nbsp;
      ';
}
echo '
    ';
}
echo '
    ';
if(count($this->_tpl_vars['typeidrows']) > 0){
echo '
    <div class="textbox">
    ';
if (empty($this->_tpl_vars['typeidrows'])) $this->_tpl_vars['typeidrows'] = array();
elseif (!is_array($this->_tpl_vars['typeidrows'])) $this->_tpl_vars['typeidrows'] = (array)$this->_tpl_vars['typeidrows'];
$this->_tpl_vars['i']=array();
$this->_tpl_vars['i']['columns'] = 1;
$this->_tpl_vars['i']['count'] = count($this->_tpl_vars['typeidrows']);
$this->_tpl_vars['i']['addrows'] = count($this->_tpl_vars['typeidrows']) % $this->_tpl_vars['i']['columns'] == 0 ? 0 : $this->_tpl_vars['i']['columns'] - count($this->_tpl_vars['typeidrows']) % $this->_tpl_vars['i']['columns'];
$this->_tpl_vars['i']['loops'] = $this->_tpl_vars['i']['count'] + $this->_tpl_vars['i']['addrows'];
reset($this->_tpl_vars['typeidrows']);
for($this->_tpl_vars['i']['index'] = 0; $this->_tpl_vars['i']['index'] < $this->_tpl_vars['i']['loops']; $this->_tpl_vars['i']['index']++){
	$this->_tpl_vars['i']['order'] = $this->_tpl_vars['i']['index'] + 1;
	$this->_tpl_vars['i']['row'] = ceil($this->_tpl_vars['i']['order'] / $this->_tpl_vars['i']['columns']);
	$this->_tpl_vars['i']['column'] = $this->_tpl_vars['i']['order'] % $this->_tpl_vars['i']['columns'];
	if($this->_tpl_vars['i']['column'] == 0) $this->_tpl_vars['i']['column'] = $this->_tpl_vars['i']['columns'];
	if($this->_tpl_vars['i']['index'] < $this->_tpl_vars['i']['count']){
		list($this->_tpl_vars['i']['key'], $this->_tpl_vars['i']['value']) = each($this->_tpl_vars['typeidrows']);
		$this->_tpl_vars['i']['append'] = 0;
	}else{
		$this->_tpl_vars['i']['key'] = '';
		$this->_tpl_vars['i']['value'] = '';
		$this->_tpl_vars['i']['append'] = 1;
	}
	echo '
      ';
if($this->_tpl_vars['typeidrows'][$this->_tpl_vars['i']['key']]['selected'] > 0){
echo '
      <a href="'.$this->_tpl_vars['typeidrows'][$this->_tpl_vars['i']['key']]['url'].'" class="hot"><strong>'.$this->_tpl_vars['typeidrows'][$this->_tpl_vars['i']['key']]['name'].'</strong></a>&nbsp;
      ';
}else{
echo '
      <a href="'.$this->_tpl_vars['typeidrows'][$this->_tpl_vars['i']['key']]['url'].'">'.$this->_tpl_vars['typeidrows'][$this->_tpl_vars['i']['key']]['name'].'</a>&nbsp;
      ';
}
echo '
    ';
}
echo '
    </div>
    ';
}
echo '
  ';
}
echo '
  </td>
</tr>
<tr valign="middle" align="left">
  <td class="tdl"><strong>作品字数：</strong></td>
  <td class="tdr">
  ';
if (empty($this->_tpl_vars['wordsrows'])) $this->_tpl_vars['wordsrows'] = array();
elseif (!is_array($this->_tpl_vars['wordsrows'])) $this->_tpl_vars['wordsrows'] = (array)$this->_tpl_vars['wordsrows'];
$this->_tpl_vars['i']=array();
$this->_tpl_vars['i']['columns'] = 1;
$this->_tpl_vars['i']['count'] = count($this->_tpl_vars['wordsrows']);
$this->_tpl_vars['i']['addrows'] = count($this->_tpl_vars['wordsrows']) % $this->_tpl_vars['i']['columns'] == 0 ? 0 : $this->_tpl_vars['i']['columns'] - count($this->_tpl_vars['wordsrows']) % $this->_tpl_vars['i']['columns'];
$this->_tpl_vars['i']['loops'] = $this->_tpl_vars['i']['count'] + $this->_tpl_vars['i']['addrows'];
reset($this->_tpl_vars['wordsrows']);
for($this->_tpl_vars['i']['index'] = 0; $this->_tpl_vars['i']['index'] < $this->_tpl_vars['i']['loops']; $this->_tpl_vars['i']['index']++){
	$this->_tpl_vars['i']['order'] = $this->_tpl_vars['i']['index'] + 1;
	$this->_tpl_vars['i']['row'] = ceil($this->_tpl_vars['i']['order'] / $this->_tpl_vars['i']['columns']);
	$this->_tpl_vars['i']['column'] = $this->_tpl_vars['i']['order'] % $this->_tpl_vars['i']['columns'];
	if($this->_tpl_vars['i']['column'] == 0) $this->_tpl_vars['i']['column'] = $this->_tpl_vars['i']['columns'];
	if($this->_tpl_vars['i']['index'] < $this->_tpl_vars['i']['count']){
		list($this->_tpl_vars['i']['key'], $this->_tpl_vars['i']['value']) = each($this->_tpl_vars['wordsrows']);
		$this->_tpl_vars['i']['append'] = 0;
	}else{
		$this->_tpl_vars['i']['key'] = '';
		$this->_tpl_vars['i']['value'] = '';
		$this->_tpl_vars['i']['append'] = 1;
	}
	echo '
  ';
if($this->_tpl_vars['wordsrows'][$this->_tpl_vars['i']['key']]['selected'] > 0){
echo '
  <a href="'.$this->_tpl_vars['wordsrows'][$this->_tpl_vars['i']['key']]['url'].'" class="hot"><strong>'.$this->_tpl_vars['wordsrows'][$this->_tpl_vars['i']['key']]['name'].'</strong></a>&nbsp;
  ';
}else{
echo '
  <a href="'.$this->_tpl_vars['wordsrows'][$this->_tpl_vars['i']['key']]['url'].'">'.$this->_tpl_vars['wordsrows'][$this->_tpl_vars['i']['key']]['name'].'</a>&nbsp;
  ';
}
echo '
  ';
}
echo '
  </td>
</tr>
<tr valign="middle" align="left">
  <td class="tdl"><strong>排序方式：</strong></td>
  <td class="tdr">
  ';
if (empty($this->_tpl_vars['orderrows'])) $this->_tpl_vars['orderrows'] = array();
elseif (!is_array($this->_tpl_vars['orderrows'])) $this->_tpl_vars['orderrows'] = (array)$this->_tpl_vars['orderrows'];
$this->_tpl_vars['i']=array();
$this->_tpl_vars['i']['columns'] = 1;
$this->_tpl_vars['i']['count'] = count($this->_tpl_vars['orderrows']);
$this->_tpl_vars['i']['addrows'] = count($this->_tpl_vars['orderrows']) % $this->_tpl_vars['i']['columns'] == 0 ? 0 : $this->_tpl_vars['i']['columns'] - count($this->_tpl_vars['orderrows']) % $this->_tpl_vars['i']['columns'];
$this->_tpl_vars['i']['loops'] = $this->_tpl_vars['i']['count'] + $this->_tpl_vars['i']['addrows'];
reset($this->_tpl_vars['orderrows']);
for($this->_tpl_vars['i']['index'] = 0; $this->_tpl_vars['i']['index'] < $this->_tpl_vars['i']['loops']; $this->_tpl_vars['i']['index']++){
	$this->_tpl_vars['i']['order'] = $this->_tpl_vars['i']['index'] + 1;
	$this->_tpl_vars['i']['row'] = ceil($this->_tpl_vars['i']['order'] / $this->_tpl_vars['i']['columns']);
	$this->_tpl_vars['i']['column'] = $this->_tpl_vars['i']['order'] % $this->_tpl_vars['i']['columns'];
	if($this->_tpl_vars['i']['column'] == 0) $this->_tpl_vars['i']['column'] = $this->_tpl_vars['i']['columns'];
	if($this->_tpl_vars['i']['index'] < $this->_tpl_vars['i']['count']){
		list($this->_tpl_vars['i']['key'], $this->_tpl_vars['i']['value']) = each($this->_tpl_vars['orderrows']);
		$this->_tpl_vars['i']['append'] = 0;
	}else{
		$this->_tpl_vars['i']['key'] = '';
		$this->_tpl_vars['i']['value'] = '';
		$this->_tpl_vars['i']['append'] = 1;
	}
	echo '
  ';
if($this->_tpl_vars['orderrows'][$this->_tpl_vars['i']['key']]['selected'] > 0){
echo '
  <a href="'.$this->_tpl_vars['orderrows'][$this->_tpl_vars['i']['key']]['url'].'" class="hot"><strong>'.$this->_tpl_vars['orderrows'][$this->_tpl_vars['i']['key']]['name'].'</strong></a>&nbsp;
  ';
}else{
echo '
  <a href="'.$this->_tpl_vars['orderrows'][$this->_tpl_vars['i']['key']]['url'].'">'.$this->_tpl_vars['orderrows'][$this->_tpl_vars['i']['key']]['name'].'</a>&nbsp;
  ';
}
echo '
  ';
}
echo '
  </td>
</tr>
<tr valign="middle" align="left">
  <td class="tdl"><strong>更新时间：</strong></td>
  <td class="tdr">
  ';
if (empty($this->_tpl_vars['updaterows'])) $this->_tpl_vars['updaterows'] = array();
elseif (!is_array($this->_tpl_vars['updaterows'])) $this->_tpl_vars['updaterows'] = (array)$this->_tpl_vars['updaterows'];
$this->_tpl_vars['i']=array();
$this->_tpl_vars['i']['columns'] = 1;
$this->_tpl_vars['i']['count'] = count($this->_tpl_vars['updaterows']);
$this->_tpl_vars['i']['addrows'] = count($this->_tpl_vars['updaterows']) % $this->_tpl_vars['i']['columns'] == 0 ? 0 : $this->_tpl_vars['i']['columns'] - count($this->_tpl_vars['updaterows']) % $this->_tpl_vars['i']['columns'];
$this->_tpl_vars['i']['loops'] = $this->_tpl_vars['i']['count'] + $this->_tpl_vars['i']['addrows'];
reset($this->_tpl_vars['updaterows']);
for($this->_tpl_vars['i']['index'] = 0; $this->_tpl_vars['i']['index'] < $this->_tpl_vars['i']['loops']; $this->_tpl_vars['i']['index']++){
	$this->_tpl_vars['i']['order'] = $this->_tpl_vars['i']['index'] + 1;
	$this->_tpl_vars['i']['row'] = ceil($this->_tpl_vars['i']['order'] / $this->_tpl_vars['i']['columns']);
	$this->_tpl_vars['i']['column'] = $this->_tpl_vars['i']['order'] % $this->_tpl_vars['i']['columns'];
	if($this->_tpl_vars['i']['column'] == 0) $this->_tpl_vars['i']['column'] = $this->_tpl_vars['i']['columns'];
	if($this->_tpl_vars['i']['index'] < $this->_tpl_vars['i']['count']){
		list($this->_tpl_vars['i']['key'], $this->_tpl_vars['i']['value']) = each($this->_tpl_vars['updaterows']);
		$this->_tpl_vars['i']['append'] = 0;
	}else{
		$this->_tpl_vars['i']['key'] = '';
		$this->_tpl_vars['i']['value'] = '';
		$this->_tpl_vars['i']['append'] = 1;
	}
	echo '
  ';
if($this->_tpl_vars['updaterows'][$this->_tpl_vars['i']['key']]['selected'] > 0){
echo '
  <a href="'.$this->_tpl_vars['updaterows'][$this->_tpl_vars['i']['key']]['url'].'" class="hot"><strong>'.$this->_tpl_vars['updaterows'][$this->_tpl_vars['i']['key']]['name'].'</strong></a>&nbsp;
  ';
}else{
echo '
  <a href="'.$this->_tpl_vars['updaterows'][$this->_tpl_vars['i']['key']]['url'].'">'.$this->_tpl_vars['updaterows'][$this->_tpl_vars['i']['key']]['name'].'</a>&nbsp;
  ';
}
echo '
  ';
}
echo '
  </td>
</tr>
<tr valign="middle" align="left">
  <td class="tdl"><strong>书名首字母：</strong></td>
  <td class="tdr">
  ';
if (empty($this->_tpl_vars['initialrows'])) $this->_tpl_vars['initialrows'] = array();
elseif (!is_array($this->_tpl_vars['initialrows'])) $this->_tpl_vars['initialrows'] = (array)$this->_tpl_vars['initialrows'];
$this->_tpl_vars['i']=array();
$this->_tpl_vars['i']['columns'] = 1;
$this->_tpl_vars['i']['count'] = count($this->_tpl_vars['initialrows']);
$this->_tpl_vars['i']['addrows'] = count($this->_tpl_vars['initialrows']) % $this->_tpl_vars['i']['columns'] == 0 ? 0 : $this->_tpl_vars['i']['columns'] - count($this->_tpl_vars['initialrows']) % $this->_tpl_vars['i']['columns'];
$this->_tpl_vars['i']['loops'] = $this->_tpl_vars['i']['count'] + $this->_tpl_vars['i']['addrows'];
reset($this->_tpl_vars['initialrows']);
for($this->_tpl_vars['i']['index'] = 0; $this->_tpl_vars['i']['index'] < $this->_tpl_vars['i']['loops']; $this->_tpl_vars['i']['index']++){
	$this->_tpl_vars['i']['order'] = $this->_tpl_vars['i']['index'] + 1;
	$this->_tpl_vars['i']['row'] = ceil($this->_tpl_vars['i']['order'] / $this->_tpl_vars['i']['columns']);
	$this->_tpl_vars['i']['column'] = $this->_tpl_vars['i']['order'] % $this->_tpl_vars['i']['columns'];
	if($this->_tpl_vars['i']['column'] == 0) $this->_tpl_vars['i']['column'] = $this->_tpl_vars['i']['columns'];
	if($this->_tpl_vars['i']['index'] < $this->_tpl_vars['i']['count']){
		list($this->_tpl_vars['i']['key'], $this->_tpl_vars['i']['value']) = each($this->_tpl_vars['initialrows']);
		$this->_tpl_vars['i']['append'] = 0;
	}else{
		$this->_tpl_vars['i']['key'] = '';
		$this->_tpl_vars['i']['value'] = '';
		$this->_tpl_vars['i']['append'] = 1;
	}
	echo '
  ';
if($this->_tpl_vars['initialrows'][$this->_tpl_vars['i']['key']]['selected'] > 0){
echo '
  <a href="'.$this->_tpl_vars['initialrows'][$this->_tpl_vars['i']['key']]['url'].'" class="hot"><strong>'.$this->_tpl_vars['initialrows'][$this->_tpl_vars['i']['key']]['name'].'</strong></a>&nbsp;
  ';
}else{
echo '
  <a href="'.$this->_tpl_vars['initialrows'][$this->_tpl_vars['i']['key']]['url'].'">'.$this->_tpl_vars['initialrows'][$this->_tpl_vars['i']['key']]['name'].'</a>&nbsp;
  ';
}
echo '
  ';
}
echo '
  </td>
</tr>
<tr valign="middle" align="left">
  <td class="tdl"><strong>写作进度：</strong></td>
  <td class="tdr">
  ';
if (empty($this->_tpl_vars['isfullrows'])) $this->_tpl_vars['isfullrows'] = array();
elseif (!is_array($this->_tpl_vars['isfullrows'])) $this->_tpl_vars['isfullrows'] = (array)$this->_tpl_vars['isfullrows'];
$this->_tpl_vars['i']=array();
$this->_tpl_vars['i']['columns'] = 1;
$this->_tpl_vars['i']['count'] = count($this->_tpl_vars['isfullrows']);
$this->_tpl_vars['i']['addrows'] = count($this->_tpl_vars['isfullrows']) % $this->_tpl_vars['i']['columns'] == 0 ? 0 : $this->_tpl_vars['i']['columns'] - count($this->_tpl_vars['isfullrows']) % $this->_tpl_vars['i']['columns'];
$this->_tpl_vars['i']['loops'] = $this->_tpl_vars['i']['count'] + $this->_tpl_vars['i']['addrows'];
reset($this->_tpl_vars['isfullrows']);
for($this->_tpl_vars['i']['index'] = 0; $this->_tpl_vars['i']['index'] < $this->_tpl_vars['i']['loops']; $this->_tpl_vars['i']['index']++){
	$this->_tpl_vars['i']['order'] = $this->_tpl_vars['i']['index'] + 1;
	$this->_tpl_vars['i']['row'] = ceil($this->_tpl_vars['i']['order'] / $this->_tpl_vars['i']['columns']);
	$this->_tpl_vars['i']['column'] = $this->_tpl_vars['i']['order'] % $this->_tpl_vars['i']['columns'];
	if($this->_tpl_vars['i']['column'] == 0) $this->_tpl_vars['i']['column'] = $this->_tpl_vars['i']['columns'];
	if($this->_tpl_vars['i']['index'] < $this->_tpl_vars['i']['count']){
		list($this->_tpl_vars['i']['key'], $this->_tpl_vars['i']['value']) = each($this->_tpl_vars['isfullrows']);
		$this->_tpl_vars['i']['append'] = 0;
	}else{
		$this->_tpl_vars['i']['key'] = '';
		$this->_tpl_vars['i']['value'] = '';
		$this->_tpl_vars['i']['append'] = 1;
	}
	echo '
  ';
if($this->_tpl_vars['isfullrows'][$this->_tpl_vars['i']['key']]['selected'] > 0){
echo '
  <a href="'.$this->_tpl_vars['isfullrows'][$this->_tpl_vars['i']['key']]['url'].'" class="hot"><strong>'.$this->_tpl_vars['isfullrows'][$this->_tpl_vars['i']['key']]['name'].'</strong></a>&nbsp;
  ';
}else{
echo '
  <a href="'.$this->_tpl_vars['isfullrows'][$this->_tpl_vars['i']['key']]['url'].'">'.$this->_tpl_vars['isfullrows'][$this->_tpl_vars['i']['key']]['name'].'</a>&nbsp;
  ';
}
echo '
  ';
}
echo '
  </td>
</tr>
<tr valign="middle" align="left">
  <td class="tdl"><strong>VIP状态：</strong></td>
  <td class="tdr">
  ';
if (empty($this->_tpl_vars['isviprows'])) $this->_tpl_vars['isviprows'] = array();
elseif (!is_array($this->_tpl_vars['isviprows'])) $this->_tpl_vars['isviprows'] = (array)$this->_tpl_vars['isviprows'];
$this->_tpl_vars['i']=array();
$this->_tpl_vars['i']['columns'] = 1;
$this->_tpl_vars['i']['count'] = count($this->_tpl_vars['isviprows']);
$this->_tpl_vars['i']['addrows'] = count($this->_tpl_vars['isviprows']) % $this->_tpl_vars['i']['columns'] == 0 ? 0 : $this->_tpl_vars['i']['columns'] - count($this->_tpl_vars['isviprows']) % $this->_tpl_vars['i']['columns'];
$this->_tpl_vars['i']['loops'] = $this->_tpl_vars['i']['count'] + $this->_tpl_vars['i']['addrows'];
reset($this->_tpl_vars['isviprows']);
for($this->_tpl_vars['i']['index'] = 0; $this->_tpl_vars['i']['index'] < $this->_tpl_vars['i']['loops']; $this->_tpl_vars['i']['index']++){
	$this->_tpl_vars['i']['order'] = $this->_tpl_vars['i']['index'] + 1;
	$this->_tpl_vars['i']['row'] = ceil($this->_tpl_vars['i']['order'] / $this->_tpl_vars['i']['columns']);
	$this->_tpl_vars['i']['column'] = $this->_tpl_vars['i']['order'] % $this->_tpl_vars['i']['columns'];
	if($this->_tpl_vars['i']['column'] == 0) $this->_tpl_vars['i']['column'] = $this->_tpl_vars['i']['columns'];
	if($this->_tpl_vars['i']['index'] < $this->_tpl_vars['i']['count']){
		list($this->_tpl_vars['i']['key'], $this->_tpl_vars['i']['value']) = each($this->_tpl_vars['isviprows']);
		$this->_tpl_vars['i']['append'] = 0;
	}else{
		$this->_tpl_vars['i']['key'] = '';
		$this->_tpl_vars['i']['value'] = '';
		$this->_tpl_vars['i']['append'] = 1;
	}
	echo '
  ';
if($this->_tpl_vars['isviprows'][$this->_tpl_vars['i']['key']]['selected'] > 0){
echo '
  <a href="'.$this->_tpl_vars['isviprows'][$this->_tpl_vars['i']['key']]['url'].'" class="hot"><strong>'.$this->_tpl_vars['isviprows'][$this->_tpl_vars['i']['key']]['name'].'</strong></a>&nbsp;
  ';
}else{
echo '
  <a href="'.$this->_tpl_vars['isviprows'][$this->_tpl_vars['i']['key']]['url'].'">'.$this->_tpl_vars['isviprows'][$this->_tpl_vars['i']['key']]['name'].'</a>&nbsp;
  ';
}
echo '
  ';
}
echo '
  </td>
</tr>
</table>

<table class="grid" width="100%" align="center">
<caption>作品列表</caption>
  <tr align="center">
    <th width="25%">小说名称</th>
    <th width="32%">最新章节</th>
    <th width="15%">作者</th>
	<th width="12%">';
if($this->_tpl_vars['orderfield'] == 'lastupdate'){
echo '字数';
}else{
echo $this->_tpl_vars['ordername'];
}
echo '</th>
    <th width="10%">更新时间</th>
    <th width="6%">状态</th>
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
    <td><a href="'.$this->_tpl_vars['articlerows'][$this->_tpl_vars['i']['key']]['url_articleindex'].'" class="note iconfont" target="_blank" title="目录">&#xee32;</a><a class="pop" href="'.$this->_tpl_vars['articlerows'][$this->_tpl_vars['i']['key']]['url_articleinfo'].'">'.$this->_tpl_vars['articlerows'][$this->_tpl_vars['i']['key']]['articlename'].'</a></td>
    <td>';
if($this->_tpl_vars['articlerows'][$this->_tpl_vars['i']['key']]['vipchapterid'] > 0){
echo '<a href="'.$this->_tpl_vars['articlerows'][$this->_tpl_vars['i']['key']]['url_vipchapter'].'" target="_blank">'.$this->_tpl_vars['articlerows'][$this->_tpl_vars['i']['key']]['vipvolume'].' '.$this->_tpl_vars['articlerows'][$this->_tpl_vars['i']['key']]['vipchapter'].'</a><span class="hot">vip</span>';
}else{
echo '<a href="'.$this->_tpl_vars['articlerows'][$this->_tpl_vars['i']['key']]['url_lastchapter'].'" target="_blank">'.$this->_tpl_vars['articlerows'][$this->_tpl_vars['i']['key']]['lastvolume'].' '.$this->_tpl_vars['articlerows'][$this->_tpl_vars['i']['key']]['lastchapter'].'</a>';
}
echo '</td>
    <td align="center">';
if($this->_tpl_vars['articlerows'][$this->_tpl_vars['i']['key']]['authorid'] == 0){
echo $this->_tpl_vars['articlerows'][$this->_tpl_vars['i']['key']]['author'];
}else{
echo '<a href="'.$this->_tpl_vars['jieqi_modules']['article']['url'].'/authorpage.php?id='.$this->_tpl_vars['articlerows'][$this->_tpl_vars['i']['key']]['authorid'].'" target="_blank">'.$this->_tpl_vars['articlerows'][$this->_tpl_vars['i']['key']]['author'].'</a>';
}
echo '</td>
	<td align="center">';
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