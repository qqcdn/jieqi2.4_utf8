<?php
echo '

<div class="block">
    <div class="dropbutton">
        <p onclick="document.getElementById(\'dropbox\').style.display = (document.getElementById(\'dropbox\').style.display != \'block\') ? \'block\':\'none\';">筛选条件<b class="dropico"></b></p>
        <div class="dropbox" id="dropbox">
            <div class="c_row cf">
                <div><strong>作品分类：</strong></div>
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
            </div>

            <div class="c_row cf">
                <div><strong>作品字数：</strong></div>
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
            </div>

            <div class="c_row cf">
                <div><strong>排序方式：</strong></div>
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
            </div>

            <div class="c_row cf">
                <div><strong>更新时间：</strong></div>
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
            </div>

            <div class="c_row cf">
                <div><strong>写作进度：</strong></div>
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
            </div>

            <div class="c_row cf">
                <div><strong>VIP状态：</strong></div>
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
            </div>
        </div>
    </div>
</div>


<table class="grid" width="100%" align="center">
    <tr align="center">
        <th width="15%">分类</th>
        <th width="50%">书名/作者</th>
        <th width="20%">';
if($this->_tpl_vars['orderfield'] == 'lastupdate'){
echo '字数';
}else{
echo $this->_tpl_vars['ordername'];
}
echo '</th>
        <th width="15%">状态</th>
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
        <td align="center">'.truncate($this->_tpl_vars['articlerows'][$this->_tpl_vars['i']['key']]['sort'],'4','').'</td>
        <td>
            <a class="db nw" href="'.$this->_tpl_vars['articlerows'][$this->_tpl_vars['i']['key']]['url_articleinfo'].'">'.$this->_tpl_vars['articlerows'][$this->_tpl_vars['i']['key']]['articlename'].'<span class="gray fss">/'.$this->_tpl_vars['articlerows'][$this->_tpl_vars['i']['key']]['author'].'</span></a>
        </td>
        <td align="center">';
if($this->_tpl_vars['orderfield'] ==
            'lastupdate'){
echo $this->_tpl_vars['articlerows'][$this->_tpl_vars['i']['key']]['words'];
}else{
echo $this->_tpl_vars['articlerows'][$this->_tpl_vars['i']['key']]['ordervalue'];
}
echo '
        </td>
        <td align="center">'.$this->_tpl_vars['articlerows'][$this->_tpl_vars['i']['key']]['fullflag'].'</td>
    </tr>
    ';
}
echo '
    </tbody>
</table>
<div class="pages">'.$this->_tpl_vars['url_jumppage'].'</div>';
?>