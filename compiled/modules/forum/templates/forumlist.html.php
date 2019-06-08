<?php
echo '<div style="padding:5px;">
<div class="fl hot">论坛首页</div>
<div class="fr"><a class="hot" href="'.$this->_tpl_vars['url_search'].'">论坛搜索</a></div>
<div class="cb"></div>
</div>

';
if (empty($this->_tpl_vars['forumcats'])) $this->_tpl_vars['forumcats'] = array();
elseif (!is_array($this->_tpl_vars['forumcats'])) $this->_tpl_vars['forumcats'] = (array)$this->_tpl_vars['forumcats'];
$this->_tpl_vars['i']=array();
$this->_tpl_vars['i']['columns'] = 1;
$this->_tpl_vars['i']['count'] = count($this->_tpl_vars['forumcats']);
$this->_tpl_vars['i']['addrows'] = count($this->_tpl_vars['forumcats']) % $this->_tpl_vars['i']['columns'] == 0 ? 0 : $this->_tpl_vars['i']['columns'] - count($this->_tpl_vars['forumcats']) % $this->_tpl_vars['i']['columns'];
$this->_tpl_vars['i']['loops'] = $this->_tpl_vars['i']['count'] + $this->_tpl_vars['i']['addrows'];
reset($this->_tpl_vars['forumcats']);
for($this->_tpl_vars['i']['index'] = 0; $this->_tpl_vars['i']['index'] < $this->_tpl_vars['i']['loops']; $this->_tpl_vars['i']['index']++){
	$this->_tpl_vars['i']['order'] = $this->_tpl_vars['i']['index'] + 1;
	$this->_tpl_vars['i']['row'] = ceil($this->_tpl_vars['i']['order'] / $this->_tpl_vars['i']['columns']);
	$this->_tpl_vars['i']['column'] = $this->_tpl_vars['i']['order'] % $this->_tpl_vars['i']['columns'];
	if($this->_tpl_vars['i']['column'] == 0) $this->_tpl_vars['i']['column'] = $this->_tpl_vars['i']['columns'];
	if($this->_tpl_vars['i']['index'] < $this->_tpl_vars['i']['count']){
		list($this->_tpl_vars['i']['key'], $this->_tpl_vars['i']['value']) = each($this->_tpl_vars['forumcats']);
		$this->_tpl_vars['i']['append'] = 0;
	}else{
		$this->_tpl_vars['i']['key'] = '';
		$this->_tpl_vars['i']['value'] = '';
		$this->_tpl_vars['i']['append'] = 1;
	}
	echo '
<table class="grid" width="100%" align="center">
<caption>'.$this->_tpl_vars['forumcats'][$this->_tpl_vars['i']['key']].'</caption>
    <tr align="center" class="head">
        <td width="5%">&nbsp;</td>
        <td width="40%">论坛</td>
        <td width="9%">主题数</td>
        <td width="9%">帖子数</td>
        <td width="18%">最后发表</td>
		<td width="19%">版主</td>
    </tr>
	';
if (empty($this->_tpl_vars['forums'][$this->_tpl_vars['i']['key']])) $this->_tpl_vars['forums'][$this->_tpl_vars['i']['key']] = array();
elseif (!is_array($this->_tpl_vars['forums'][$this->_tpl_vars['i']['key']])) $this->_tpl_vars['forums'][$this->_tpl_vars['i']['key']] = (array)$this->_tpl_vars['forums'][$this->_tpl_vars['i']['key']];
$this->_tpl_vars['j']=array();
$this->_tpl_vars['j']['columns'] = 1;
$this->_tpl_vars['j']['count'] = count($this->_tpl_vars['forums'][$this->_tpl_vars['i']['key']]);
$this->_tpl_vars['j']['addrows'] = count($this->_tpl_vars['forums'][$this->_tpl_vars['i']['key']]) % $this->_tpl_vars['j']['columns'] == 0 ? 0 : $this->_tpl_vars['j']['columns'] - count($this->_tpl_vars['forums'][$this->_tpl_vars['i']['key']]) % $this->_tpl_vars['j']['columns'];
$this->_tpl_vars['j']['loops'] = $this->_tpl_vars['j']['count'] + $this->_tpl_vars['j']['addrows'];
reset($this->_tpl_vars['forums'][$this->_tpl_vars['i']['key']]);
for($this->_tpl_vars['j']['index'] = 0; $this->_tpl_vars['j']['index'] < $this->_tpl_vars['j']['loops']; $this->_tpl_vars['j']['index']++){
	$this->_tpl_vars['j']['order'] = $this->_tpl_vars['j']['index'] + 1;
	$this->_tpl_vars['j']['row'] = ceil($this->_tpl_vars['j']['order'] / $this->_tpl_vars['j']['columns']);
	$this->_tpl_vars['j']['column'] = $this->_tpl_vars['j']['order'] % $this->_tpl_vars['j']['columns'];
	if($this->_tpl_vars['j']['column'] == 0) $this->_tpl_vars['j']['column'] = $this->_tpl_vars['j']['columns'];
	if($this->_tpl_vars['j']['index'] < $this->_tpl_vars['j']['count']){
		list($this->_tpl_vars['j']['key'], $this->_tpl_vars['j']['value']) = each($this->_tpl_vars['forums'][$this->_tpl_vars['i']['key']]);
		$this->_tpl_vars['j']['append'] = 0;
	}else{
		$this->_tpl_vars['j']['key'] = '';
		$this->_tpl_vars['j']['value'] = '';
		$this->_tpl_vars['j']['append'] = 1;
	}
	echo '
    <tr>
        <td align="center" valign="middle"><span class="iconfont note fsl">&#xee3a;</span></td>
        <td valign="middle">
		[ <a href="'.jieqi_geturl('forum','topiclist','1',$this->_tpl_vars['forums'][$this->_tpl_vars['i']['key']][$this->_tpl_vars['j']['key']]['forumid']).'"><strong>'.$this->_tpl_vars['forums'][$this->_tpl_vars['i']['key']][$this->_tpl_vars['j']['key']]['forumname'].'</strong></a> ]
		<br />
		&nbsp;&nbsp;&nbsp;&nbsp;'.$this->_tpl_vars['forums'][$this->_tpl_vars['i']['key']][$this->_tpl_vars['j']['key']]['desc'].'
		</td>
        <td align="center" valign="middle">'.$this->_tpl_vars['forums'][$this->_tpl_vars['i']['key']][$this->_tpl_vars['j']['key']]['topics'].'</td>
        <td align="center" valign="middle">'.$this->_tpl_vars['forums'][$this->_tpl_vars['i']['key']][$this->_tpl_vars['j']['key']]['posts'].'</td>
        <td valign="middle">';
if($this->_tpl_vars['forums'][$this->_tpl_vars['i']['key']][$this->_tpl_vars['j']['key']]['lasttime'] > 0){
echo date('Y-m-d H:i:s',$this->_tpl_vars['forums'][$this->_tpl_vars['i']['key']][$this->_tpl_vars['j']['key']]['lasttime']).'<br />由 ';
if($this->_tpl_vars['forums'][$this->_tpl_vars['i']['key']][$this->_tpl_vars['j']['key']]['lastuid'] > 0){
echo '<a href="'.jieqi_geturl('system','user',$this->_tpl_vars['forums'][$this->_tpl_vars['i']['key']][$this->_tpl_vars['j']['key']]['lastuid']).'">'.$this->_tpl_vars['forums'][$this->_tpl_vars['i']['key']][$this->_tpl_vars['j']['key']]['lastuname'].'</a>';
}else{
echo '<em>游客</em>';
}
echo '发表';
}
echo '</td>
		<td valign="middle">';
if (empty($this->_tpl_vars['forums'][$this->_tpl_vars['i']['key']][$this->_tpl_vars['j']['key']]['masters'])) $this->_tpl_vars['forums'][$this->_tpl_vars['i']['key']][$this->_tpl_vars['j']['key']]['masters'] = array();
elseif (!is_array($this->_tpl_vars['forums'][$this->_tpl_vars['i']['key']][$this->_tpl_vars['j']['key']]['masters'])) $this->_tpl_vars['forums'][$this->_tpl_vars['i']['key']][$this->_tpl_vars['j']['key']]['masters'] = (array)$this->_tpl_vars['forums'][$this->_tpl_vars['i']['key']][$this->_tpl_vars['j']['key']]['masters'];
$this->_tpl_vars['k']=array();
$this->_tpl_vars['k']['columns'] = 1;
$this->_tpl_vars['k']['count'] = count($this->_tpl_vars['forums'][$this->_tpl_vars['i']['key']][$this->_tpl_vars['j']['key']]['masters']);
$this->_tpl_vars['k']['addrows'] = count($this->_tpl_vars['forums'][$this->_tpl_vars['i']['key']][$this->_tpl_vars['j']['key']]['masters']) % $this->_tpl_vars['k']['columns'] == 0 ? 0 : $this->_tpl_vars['k']['columns'] - count($this->_tpl_vars['forums'][$this->_tpl_vars['i']['key']][$this->_tpl_vars['j']['key']]['masters']) % $this->_tpl_vars['k']['columns'];
$this->_tpl_vars['k']['loops'] = $this->_tpl_vars['k']['count'] + $this->_tpl_vars['k']['addrows'];
reset($this->_tpl_vars['forums'][$this->_tpl_vars['i']['key']][$this->_tpl_vars['j']['key']]['masters']);
for($this->_tpl_vars['k']['index'] = 0; $this->_tpl_vars['k']['index'] < $this->_tpl_vars['k']['loops']; $this->_tpl_vars['k']['index']++){
	$this->_tpl_vars['k']['order'] = $this->_tpl_vars['k']['index'] + 1;
	$this->_tpl_vars['k']['row'] = ceil($this->_tpl_vars['k']['order'] / $this->_tpl_vars['k']['columns']);
	$this->_tpl_vars['k']['column'] = $this->_tpl_vars['k']['order'] % $this->_tpl_vars['k']['columns'];
	if($this->_tpl_vars['k']['column'] == 0) $this->_tpl_vars['k']['column'] = $this->_tpl_vars['k']['columns'];
	if($this->_tpl_vars['k']['index'] < $this->_tpl_vars['k']['count']){
		list($this->_tpl_vars['k']['key'], $this->_tpl_vars['k']['value']) = each($this->_tpl_vars['forums'][$this->_tpl_vars['i']['key']][$this->_tpl_vars['j']['key']]['masters']);
		$this->_tpl_vars['k']['append'] = 0;
	}else{
		$this->_tpl_vars['k']['key'] = '';
		$this->_tpl_vars['k']['value'] = '';
		$this->_tpl_vars['k']['append'] = 1;
	}
	echo '<a href="'.jieqi_geturl('system','user',$this->_tpl_vars['forums'][$this->_tpl_vars['i']['key']][$this->_tpl_vars['j']['key']]['masters'][$this->_tpl_vars['k']['key']]['uid']).'">'.$this->_tpl_vars['forums'][$this->_tpl_vars['i']['key']][$this->_tpl_vars['j']['key']]['masters'][$this->_tpl_vars['k']['key']]['uname'].'</a> ';
}
echo '</td>
    </tr>
	';
}
echo '
</table>
<br />
';
}
echo '

';
?>