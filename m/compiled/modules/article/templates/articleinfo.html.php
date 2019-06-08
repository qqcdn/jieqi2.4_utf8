<?php
echo '
<script type="text/javascript" src="'.$this->_tpl_vars['jieqi_url'].'/scripts/json2.js"></script>
<script type="text/javascript" src="'.$this->_tpl_vars['jieqi_modules']['article']['themeurl'].'/scripts/readchapter.js"></script>
<ul class="tabb tab3 cf mb">
	<li><a href="javascript:void(0);" class="selected">信息</a></li>
	<li><a href="'.$this->_tpl_vars['url_index'].'">目录</a></li>
	<li><a href="'.$this->_tpl_vars['jieqi_modules']['article']['url'].'/reviews.php?aid='.$this->_tpl_vars['articleid'].'">书评</a></li>
</ul>
<div class="blockc mt">
	<div class="c_row cf">
	<div class="row_coverl">
	<img class="cover_l" src="'.$this->_tpl_vars['url_simage'].'" />
	</div>

	<div class="row_textl">
	<h4 class="mbs"><span class="fr fss">['.$this->_tpl_vars['sort'].']</span>'.$this->_tpl_vars['articlename'].'</h4>
    <p class="gray fss">
		<span class="fr fss">'.$this->_tpl_vars['words'].'字</span>'.$this->_tpl_vars['author'].' 着<br />
		<span class="fr fss">'.$this->_tpl_vars['allvote'].'人推荐</span>'.$this->_tpl_vars['allvisit'].'人看过<br />
	</p>
		<div class="mt">
			<a class="btnlink b_hot" href="javascript:read_chapter('.$this->_tpl_vars['articleid'].');"><span class="iconfont">&#xee50;</span>追书</a>
			<a class="btnlink b_s" id="a_addbookcase" href="javascript:;" onclick="Ajax.Tip(\''.$this->_tpl_vars['url_bookcase'].'\', {method: \'POST\'});"><span class="iconfont">&#xee81;</span>收藏</a>
			<a class="btnlink b_s" id="a_uservote" href="javascript:;" onclick="Ajax.Tip(\''.$this->_tpl_vars['url_uservote'].'\', {method: \'POST\'});"><span class="iconfont">&#xee5d;</span>推荐</a>
		</div>
	</div>
	</div>

	<div class="c_row nw">
		<span class="note">最新：</span>';
if($this->_tpl_vars['isvip_n'] > 0 && $this->_tpl_vars['vipchapterid'] > 0){
echo '<a href="'.$this->_tpl_vars['url_vipchapter'].'">';
if($this->_tpl_vars['vipvolume'] != ''){
echo $this->_tpl_vars['vipvolume'].' ';
}
echo $this->_tpl_vars['vipchapter'].'</a> <i class="hot">vip</i>';
}else{
echo '<a href="'.$this->_tpl_vars['url_lastchapter'].'">';
if($this->_tpl_vars['lastvolume'] != ''){
echo $this->_tpl_vars['lastvolume'].' ';
}
echo $this->_tpl_vars['lastchapter'].'</a>';
}
echo '
	</div>
</div>

<div class="block">
	<div class="blocktitle">送礼物捧场</div>
	<div class="blockcontent">
		<script type="text/javascript">
		function act_tiptype(tiptype, tipegold, tipname){
			if(confirm(\'确定要赠送1\'+tipname+\'么？ （价值\'+tipegold+\''.$this->_tpl_vars['egoldname'].'）\')) Ajax.Tip(\''.$this->_tpl_vars['jieqi_modules']['article']['url'].'/tip.php?act=post&id='.$this->_tpl_vars['articleid'].'&tiptype=\'+tiptype+\''.$this->_tpl_vars['jieqi_token_url'].'\', {method: \'POST\'});
		}
		</script>
		<ul class="df mts mb">
			';
if (empty($this->_tpl_vars['tiptyperows'])) $this->_tpl_vars['tiptyperows'] = array();
elseif (!is_array($this->_tpl_vars['tiptyperows'])) $this->_tpl_vars['tiptyperows'] = (array)$this->_tpl_vars['tiptyperows'];
$this->_tpl_vars['i']=array();
$this->_tpl_vars['i']['columns'] = 1;
$this->_tpl_vars['i']['count'] = count($this->_tpl_vars['tiptyperows']);
$this->_tpl_vars['i']['addrows'] = count($this->_tpl_vars['tiptyperows']) % $this->_tpl_vars['i']['columns'] == 0 ? 0 : $this->_tpl_vars['i']['columns'] - count($this->_tpl_vars['tiptyperows']) % $this->_tpl_vars['i']['columns'];
$this->_tpl_vars['i']['loops'] = $this->_tpl_vars['i']['count'] + $this->_tpl_vars['i']['addrows'];
reset($this->_tpl_vars['tiptyperows']);
for($this->_tpl_vars['i']['index'] = 0; $this->_tpl_vars['i']['index'] < $this->_tpl_vars['i']['loops']; $this->_tpl_vars['i']['index']++){
	$this->_tpl_vars['i']['order'] = $this->_tpl_vars['i']['index'] + 1;
	$this->_tpl_vars['i']['row'] = ceil($this->_tpl_vars['i']['order'] / $this->_tpl_vars['i']['columns']);
	$this->_tpl_vars['i']['column'] = $this->_tpl_vars['i']['order'] % $this->_tpl_vars['i']['columns'];
	if($this->_tpl_vars['i']['column'] == 0) $this->_tpl_vars['i']['column'] = $this->_tpl_vars['i']['columns'];
	if($this->_tpl_vars['i']['index'] < $this->_tpl_vars['i']['count']){
		list($this->_tpl_vars['i']['key'], $this->_tpl_vars['i']['value']) = each($this->_tpl_vars['tiptyperows']);
		$this->_tpl_vars['i']['append'] = 0;
	}else{
		$this->_tpl_vars['i']['key'] = '';
		$this->_tpl_vars['i']['value'] = '';
		$this->_tpl_vars['i']['append'] = 1;
	}
	echo '
			<li class="tc"><p id="a_tiptype_'.$this->_tpl_vars['i']['key'].'" class="iconfont bg_'.$this->_tpl_vars['i']['order'].'" style="cursor:pointer; color:#fff; font-size:2.2em;width:1.5em;height:1.5em;line-height:1.5em;" onclick="act_tiptype('.$this->_tpl_vars['i']['key'].', '.$this->_tpl_vars['tiptyperows'][$this->_tpl_vars['i']['key']]['eprice'].', \''.$this->_tpl_vars['tiptyperows'][$this->_tpl_vars['i']['key']]['unit'].$this->_tpl_vars['tiptyperows'][$this->_tpl_vars['i']['key']]['caption'].'\');" title="1'.$this->_tpl_vars['tiptyperows'][$this->_tpl_vars['i']['key']]['unit'].$this->_tpl_vars['tiptyperows'][$this->_tpl_vars['i']['key']]['caption'].'='.$this->_tpl_vars['tiptyperows'][$this->_tpl_vars['i']['key']]['eprice'].$this->_tpl_vars['egoldname'].'">&#xeea'.$this->_tpl_vars['i']['order'].';</a><p>'.$this->_tpl_vars['tiptyperows'][$this->_tpl_vars['i']['key']]['caption'].'</p><p class="hot">'.intval($this->_tpl_vars['setting']['tipinfo'][$this->_tpl_vars['i']['key']]).$this->_tpl_vars['tiptyperows'][$this->_tpl_vars['i']['key']]['unit'].'</p></li>
			';
}
echo '
		</ul>
	</div>
</div>

<div class="block">
	<div class="blocktitle">内容简介</div>
	<div class="blockcontent fss">
		<div id="introl">'.truncate(str_replace('<br />','&nbsp;',$this->_tpl_vars['intro']),'150','..').' <a href="javascript:;" onclick="$_(\'introl\').style.display = \'none\';$_(\'introa\').style.display = \'\';$_(\'introd\').style.height = \'auto\';" class="hot">[显示全部]</a></div>
		<div id="introa" style="display:none;">'.$this->_tpl_vars['intro'].' <a href="javascript:;" onclick="$_(\'introl\').style.display = \'\';$_(\'introa\').style.display = \'none\';$_(\'introd\').style.height = \'4.5em;\';" class="hot">[收起内容]</a></div>
	</div>
</div>

<div class="block">
	<div class="blockcontent">
		'.jieqi_get_block(array('bid'=>'0', 'blockname'=>'开始最新章节', 'module'=>'article', 'filename'=>'block_achapters', 'classname'=>'BlockArticleAchapters', 'side'=>'-1', 'title'=>'', 'vars'=>'chapterorder,5,2,$articleid,1', 'template'=>'mchapter_startlast.html', 'contenttype'=>'4', 'custom'=>'0', 'publish'=>'3', 'hasvars'=>'1'), 1).'
	</div>
</div>

<div class="block">
	<div class="blocktitle">最新书评</div>
	<div class="blockcontent">
	'.jieqi_get_block(array('bid'=>'0', 'blockname'=>'最新书评', 'module'=>'article', 'filename'=>'block_areviews', 'classname'=>'BlockArticleAreviews', 'side'=>'-1', 'title'=>'', 'vars'=>'6,0,0,id', 'template'=>'mblock_areviews.html', 'contenttype'=>'4', 'custom'=>'0', 'publish'=>'3', 'hasvars'=>'1'), 1).'
	</div>
</div>

<div class="block">
	<div class="blocktitle">发表书评</div>
	<div class="blockcontent">
	';
if($this->_tpl_vars['jieqi_userid'] > 0){
echo '
		<form class="cf" name="frmreview" id="frmreview" method="post" action="'.$this->_tpl_vars['jieqi_modules']['article']['url'].'/reviews.php?aid='.$this->_tpl_vars['articleid'].'">
		<fieldset>
		<div class="frow">
		<textarea class="textarea" name="pcontent" id="pcontent" rows="5"></textarea>
		</div>
		';
if($this->_tpl_vars['postcheckcode'] > 0){
echo '
		<div class="frow">验证码：<input type="text" class="text" style="width:6em;" name="checkcode" id="checkcode" onfocus="if(this.form.imgccode.style.display == \'none\'){this.form.imgccode.src = \''.$this->_tpl_vars['jieqi_url'].'/checkcode.php?rand='.$this->_tpl_vars['jieqi_time'].'\';this.form.imgccode.style.display = \'\';}" title="点击显示验证码"><img name="imgccode" src="" style="cursor:pointer;vertical-align:middle;margin-left:3px;display:none;" onclick="this.src=\''.$this->_tpl_vars['jieqi_url'].'/checkcode.php?rand=\'+Math.random();" title="点击刷新验证码"></div>
		';
}
echo '
		<div class="frow">
		<input type="hidden" name="act" value="newpost" />'.$this->_tpl_vars['jieqi_token_input'].'
		<button type="button" name="Submit" class="button" style="cursor:pointer;" onclick="postsubmit();"> 发表书评 </button>
		</div>
		</fieldset>
		</form>
	';
}else{
echo '
		<br />您需要 <a class="hot" href="'.$this->_tpl_vars['jieqi_url'].'/login.php">登录</a> 才能发表书评！<br /><br />
		如果您尚未注册，请先点击 <a class="hot" href="'.$this->_tpl_vars['jieqi_url'].'/register.php">注册</a><br /><br />
	';
}
echo '
	</div>
</div>';
?>