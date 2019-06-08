<?php
echo '

<ul class="tabb tab3 cf mb">
	<li><a href="'.$this->_tpl_vars['jieqi_modules']['article']['url'].'/recentread.php">最近阅读</a></li>
	<li><a href="javascript:void(0);" class="selected">我的书架</a></li>
	<li><a href="'.$this->_tpl_vars['jieqi_modules']['obook']['url'].'/buylist.php">我的订阅</a></li>
</ul>

';
if(count($this->_tpl_vars['bookcaserows']) == 0){
echo '
<div class="blockc mt">'.jieqi_get_block(array('bid'=>'0', 'blockname'=>'作品搜索', 'module'=>'article', 'filename'=>'', 'classname'=>'BlockSystemCustom', 'side'=>'-1', 'title'=>'', 'vars'=>'', 'template'=>'marticle_search.html', 'contenttype'=>'4', 'custom'=>'1', 'publish'=>'3', 'hasvars'=>'0'), 1).'</div>
<div style="text-align:center; padding-bottom:4em;">
  <div class="block">
    <div class="blockcontent fsl pd tc error">
		您的书架空空如也，<br />
		快去找喜欢的书放入书架！
	  </div>
	</div>
</div>
';
}else{
echo '
<script type="text/javascript" src="'.$this->_tpl_vars['jieqi_url'].'/scripts/json2.js"></script>
<script type="text/javascript" src="'.$this->_tpl_vars['jieqi_modules']['article']['themeurl'].'/scripts/readchapter.js"></script>
<script type="text/javascript">
//删除
function act_delete(url){
	var o = getTarget();
	var param = {
		method: \'POST\', 
		onReturn: function(){
			$_(o.parentNode.parentNode.parentNode).remove();
		}
	}
	if(confirm(\'确实要将本书移出书架么？\')) Ajax.Tip(url, param);
	return false;
}
</script>

<form action="'.$this->_tpl_vars['jieqi_modules']['article']['url'].'/bookcase.php" method="post" name="checkform" id="checkform" onsubmit="return check_confirm();">
<div class="blockb">
<div class="blockcontent" id="jieqi_page_contents">
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
	<div class="c_row cf">
	
	<div class="row_cover">
	<a class="db cf" href="'.$this->_tpl_vars['bookcaserows'][$this->_tpl_vars['i']['key']]['url_articleinfo'].'">
	<img class="cover_m" src="'.jieqi_geturl('article','cover',$this->_tpl_vars['bookcaserows'][$this->_tpl_vars['i']['key']]['articleid'],'s',$this->_tpl_vars['bookcaserows'][$this->_tpl_vars['i']['key']]['imgflag']).'" />
	</a>
	</div>

	<div class="row_text">
	<h4><a href="'.$this->_tpl_vars['bookcaserows'][$this->_tpl_vars['i']['key']]['url_index'].'">'.$this->_tpl_vars['bookcaserows'][$this->_tpl_vars['i']['key']]['articlename'].'</a>';
if($this->_tpl_vars['bookcaserows'][$this->_tpl_vars['i']['key']]['hasnew'] == 1){
echo '<span class="btnlink b_s b_ico b_hot">新</span>';
}
echo '</h4>
    <p class="gray" style="line-height:2">
	最新：';
if($this->_tpl_vars['bookcaserows'][$this->_tpl_vars['i']['key']]['viptime'] > $this->_tpl_vars['bookcaserows'][$this->_tpl_vars['i']['key']]['freetime']){
echo '<a href="'.$this->_tpl_vars['jieqi_modules']['article']['url'].'/readbookcase.php?bid='.$this->_tpl_vars['bookcaserows'][$this->_tpl_vars['i']['key']]['caseid'].'&aid='.$this->_tpl_vars['bookcaserows'][$this->_tpl_vars['i']['key']]['articleid'].'&cid='.$this->_tpl_vars['bookcaserows'][$this->_tpl_vars['i']['key']]['vipchapterid'].'">'.$this->_tpl_vars['bookcaserows'][$this->_tpl_vars['i']['key']]['vipchapter'].'</a><em class="hot">vip</em>
	';
}else{
echo '<a href="'.$this->_tpl_vars['jieqi_modules']['article']['url'].'/readbookcase.php?bid='.$this->_tpl_vars['bookcaserows'][$this->_tpl_vars['i']['key']]['caseid'].'&aid='.$this->_tpl_vars['bookcaserows'][$this->_tpl_vars['i']['key']]['articleid'].'&cid='.$this->_tpl_vars['bookcaserows'][$this->_tpl_vars['i']['key']]['lastchapterid'].'">'.$this->_tpl_vars['bookcaserows'][$this->_tpl_vars['i']['key']]['lastchapter'].'</a>
	';
}
echo '
	</p>
	<p class="mt"><a class="btnlink b_hot" href="javascript:read_bookcase('.$this->_tpl_vars['bookcaserows'][$this->_tpl_vars['i']['key']]['articleid'].', '.$this->_tpl_vars['bookcaserows'][$this->_tpl_vars['i']['key']]['chapterid'].', '.$this->_tpl_vars['bookcaserows'][$this->_tpl_vars['i']['key']]['caseid'].');">继续阅读</a> &nbsp; <a class="btnlink b_gray" id="act_delete_'.$this->_tpl_vars['bookcaserows'][$this->_tpl_vars['i']['key']]['caseid'].'" href="javascript:;" onclick="act_delete(\''.$this->_tpl_vars['jieqi_modules']['article']['url'].'/bookcase.php?bid='.$this->_tpl_vars['bookcaserows'][$this->_tpl_vars['i']['key']]['caseid'].'&act=delete'.$this->_tpl_vars['jieqi_token_url'].'\');">移出书架</a></p>
	</div>

	</div>
	';
}
echo '
</div>
</div>
</form>
';
}

?>