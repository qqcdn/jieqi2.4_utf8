<?php
echo '<div class="cf">
	<ul class="tabb tab2">
		<li><a href="javascript:void(0)" onclick="selecttab(this)" class="selected">开始章节</a></li>
		<li><a href="javascript:void(0)" onclick="selecttab(this)">最新章节</a></li>
	</ul>
</div>
<div class="mts">
	<div>
		<ul class="ullist">
		';
if (empty($this->_tpl_vars['chapterstart'])) $this->_tpl_vars['chapterstart'] = array();
elseif (!is_array($this->_tpl_vars['chapterstart'])) $this->_tpl_vars['chapterstart'] = (array)$this->_tpl_vars['chapterstart'];
$this->_tpl_vars['i']=array();
$this->_tpl_vars['i']['columns'] = 1;
$this->_tpl_vars['i']['count'] = count($this->_tpl_vars['chapterstart']);
$this->_tpl_vars['i']['addrows'] = count($this->_tpl_vars['chapterstart']) % $this->_tpl_vars['i']['columns'] == 0 ? 0 : $this->_tpl_vars['i']['columns'] - count($this->_tpl_vars['chapterstart']) % $this->_tpl_vars['i']['columns'];
$this->_tpl_vars['i']['loops'] = $this->_tpl_vars['i']['count'] + $this->_tpl_vars['i']['addrows'];
reset($this->_tpl_vars['chapterstart']);
for($this->_tpl_vars['i']['index'] = 0; $this->_tpl_vars['i']['index'] < $this->_tpl_vars['i']['loops']; $this->_tpl_vars['i']['index']++){
	$this->_tpl_vars['i']['order'] = $this->_tpl_vars['i']['index'] + 1;
	$this->_tpl_vars['i']['row'] = ceil($this->_tpl_vars['i']['order'] / $this->_tpl_vars['i']['columns']);
	$this->_tpl_vars['i']['column'] = $this->_tpl_vars['i']['order'] % $this->_tpl_vars['i']['columns'];
	if($this->_tpl_vars['i']['column'] == 0) $this->_tpl_vars['i']['column'] = $this->_tpl_vars['i']['columns'];
	if($this->_tpl_vars['i']['index'] < $this->_tpl_vars['i']['count']){
		list($this->_tpl_vars['i']['key'], $this->_tpl_vars['i']['value']) = each($this->_tpl_vars['chapterstart']);
		$this->_tpl_vars['i']['append'] = 0;
	}else{
		$this->_tpl_vars['i']['key'] = '';
		$this->_tpl_vars['i']['value'] = '';
		$this->_tpl_vars['i']['append'] = 1;
	}
	echo '
		<li><a href="'.$this->_tpl_vars['chapterstart'][$this->_tpl_vars['i']['key']]['url_chapter'].'">'.$this->_tpl_vars['chapterstart'][$this->_tpl_vars['i']['key']]['volumename'].' '.$this->_tpl_vars['chapterstart'][$this->_tpl_vars['i']['key']]['chaptername'].'</a>';
if($this->_tpl_vars['chapterstart'][$this->_tpl_vars['i']['key']]['isvip_n'] > 0){
echo '<i class="hot">vip</i>';
}
echo '</li>
		';
}
echo '
		</ul>
		<a href="'.$this->_tpl_vars['url_start'].'" class="more">显示全部章节<i class="iconfont">&#xee6a;</i></a>
	</div>
	<div style="display:none;">
		<ul class="ullist">
		';
if (empty($this->_tpl_vars['chapterlast'])) $this->_tpl_vars['chapterlast'] = array();
elseif (!is_array($this->_tpl_vars['chapterlast'])) $this->_tpl_vars['chapterlast'] = (array)$this->_tpl_vars['chapterlast'];
$this->_tpl_vars['i']=array();
$this->_tpl_vars['i']['columns'] = 1;
$this->_tpl_vars['i']['count'] = count($this->_tpl_vars['chapterlast']);
$this->_tpl_vars['i']['addrows'] = count($this->_tpl_vars['chapterlast']) % $this->_tpl_vars['i']['columns'] == 0 ? 0 : $this->_tpl_vars['i']['columns'] - count($this->_tpl_vars['chapterlast']) % $this->_tpl_vars['i']['columns'];
$this->_tpl_vars['i']['loops'] = $this->_tpl_vars['i']['count'] + $this->_tpl_vars['i']['addrows'];
reset($this->_tpl_vars['chapterlast']);
for($this->_tpl_vars['i']['index'] = 0; $this->_tpl_vars['i']['index'] < $this->_tpl_vars['i']['loops']; $this->_tpl_vars['i']['index']++){
	$this->_tpl_vars['i']['order'] = $this->_tpl_vars['i']['index'] + 1;
	$this->_tpl_vars['i']['row'] = ceil($this->_tpl_vars['i']['order'] / $this->_tpl_vars['i']['columns']);
	$this->_tpl_vars['i']['column'] = $this->_tpl_vars['i']['order'] % $this->_tpl_vars['i']['columns'];
	if($this->_tpl_vars['i']['column'] == 0) $this->_tpl_vars['i']['column'] = $this->_tpl_vars['i']['columns'];
	if($this->_tpl_vars['i']['index'] < $this->_tpl_vars['i']['count']){
		list($this->_tpl_vars['i']['key'], $this->_tpl_vars['i']['value']) = each($this->_tpl_vars['chapterlast']);
		$this->_tpl_vars['i']['append'] = 0;
	}else{
		$this->_tpl_vars['i']['key'] = '';
		$this->_tpl_vars['i']['value'] = '';
		$this->_tpl_vars['i']['append'] = 1;
	}
	echo '
		<li><a href="'.$this->_tpl_vars['chapterlast'][$this->_tpl_vars['i']['key']]['url_chapter'].'">'.$this->_tpl_vars['chapterlast'][$this->_tpl_vars['i']['key']]['volumename'].' '.$this->_tpl_vars['chapterlast'][$this->_tpl_vars['i']['key']]['chaptername'].'</a>';
if($this->_tpl_vars['chapterlast'][$this->_tpl_vars['i']['key']]['isvip_n'] > 0){
echo '<i class="hot">vip</i>';
}
echo '</li>
		';
}
echo '
		</ul>
		<a href="'.$this->_tpl_vars['url_last'].'" class="more">显示全部章节<i class="iconfont">&#xee6a;</i></a>
	</div>
</div>';
?>