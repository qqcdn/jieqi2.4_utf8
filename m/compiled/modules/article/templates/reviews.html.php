<?php
echo '

<script type="text/javascript">
//置顶置后
function act_top(url){
	var o = getTarget();
	var param = {
		method: \'POST\', 
		onFinish: \'\'
	}
	if(o.getAttribute(\'switch\') == \'0\'){
		url = url.replace(\'act=untop\', \'act=top\');
		param.onFinish = function(res){
			if(res.match(\'成功\')){
				o.setAttribute(\'switch\', \'1\');
				o.innerHTML = \'置后\';
			}
		}
	}else{
		url = url.replace(\'act=top\', \'act=untop\');
		param.onFinish = function(res){
			if(res.match(\'成功\')){
				o.setAttribute(\'switch\', \'0\');
				o.innerHTML = \'置顶\';
			}
		}
	}
	Ajax.Tip(url, param);
	return false;
}
//加精去精
function act_good(url){
	var o = getTarget();
	var param = {
		method: \'POST\', 
		onReturn: \'\'
	}
	if(o.getAttribute(\'switch\') == \'0\'){
		url = url.replace(\'act=normal\', \'act=good\');
		param.onFinish = function(res){
			if(res.match(\'成功\')){
			o.setAttribute(\'switch\', \'1\');
			o.innerHTML = \'去精\';
			}
		}
	}else{
		url = url.replace(\'act=good\', \'act=normal\');
		param.onFinish = function(res){
			if(res.match(\'成功\')){
			o.setAttribute(\'switch\', \'0\');
			o.innerHTML = \'加精\';
			}
		}
	}
	Ajax.Tip(url, param);
	return false;
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
	if(confirm(\'确实要删除该书评么？\')) Ajax.Tip(url, param);
	return false;
}
</script>

<ul class="tabb tab3 cf mb">
	<li><a href="'.$this->_tpl_vars['url_articleinfo'].'">信息</a></li>
	<li><a href="'.$this->_tpl_vars['url_articleindex'].'">目录</a></li>
	<li><a href="javascript:void(0);" class="selected">书评</a></li>
</ul>

';
if($this->_tpl_vars['newpost'] > 0){
echo '
<div class="textbox hot" id="postresult">'.$this->_tpl_vars['postresult'].'</div>
<script type="text/javascript">
setTimeout(function(){$_(\'postresult\').hide()}, 3000);
</script>
';
}
echo '
<div class="blockc mt">
<div class="blocktitle">';
if($this->_tpl_vars['enablepost'] == 1){
echo '<a class="fr hot" href="#postnew">我要评论</a>';
}
echo '<span class="fl">共 '.$this->_tpl_vars['jieqi_page_totalrows'].' 条评论</span></div>
<ul class="ulrow">
';
if (empty($this->_tpl_vars['reviewrows'])) $this->_tpl_vars['reviewrows'] = array();
elseif (!is_array($this->_tpl_vars['reviewrows'])) $this->_tpl_vars['reviewrows'] = (array)$this->_tpl_vars['reviewrows'];
$this->_tpl_vars['i']=array();
$this->_tpl_vars['i']['columns'] = 1;
$this->_tpl_vars['i']['count'] = count($this->_tpl_vars['reviewrows']);
$this->_tpl_vars['i']['addrows'] = count($this->_tpl_vars['reviewrows']) % $this->_tpl_vars['i']['columns'] == 0 ? 0 : $this->_tpl_vars['i']['columns'] - count($this->_tpl_vars['reviewrows']) % $this->_tpl_vars['i']['columns'];
$this->_tpl_vars['i']['loops'] = $this->_tpl_vars['i']['count'] + $this->_tpl_vars['i']['addrows'];
reset($this->_tpl_vars['reviewrows']);
for($this->_tpl_vars['i']['index'] = 0; $this->_tpl_vars['i']['index'] < $this->_tpl_vars['i']['loops']; $this->_tpl_vars['i']['index']++){
	$this->_tpl_vars['i']['order'] = $this->_tpl_vars['i']['index'] + 1;
	$this->_tpl_vars['i']['row'] = ceil($this->_tpl_vars['i']['order'] / $this->_tpl_vars['i']['columns']);
	$this->_tpl_vars['i']['column'] = $this->_tpl_vars['i']['order'] % $this->_tpl_vars['i']['columns'];
	if($this->_tpl_vars['i']['column'] == 0) $this->_tpl_vars['i']['column'] = $this->_tpl_vars['i']['columns'];
	if($this->_tpl_vars['i']['index'] < $this->_tpl_vars['i']['count']){
		list($this->_tpl_vars['i']['key'], $this->_tpl_vars['i']['value']) = each($this->_tpl_vars['reviewrows']);
		$this->_tpl_vars['i']['append'] = 0;
	}else{
		$this->_tpl_vars['i']['key'] = '';
		$this->_tpl_vars['i']['value'] = '';
		$this->_tpl_vars['i']['append'] = 1;
	}
	echo '
	<li>
	<a class="db cf" href="'.jieqi_geturl('article','reviewshow','1',$this->_tpl_vars['reviewrows'][$this->_tpl_vars['i']['key']]['topicid']).'">
	<em>'.date('Y-m-d H:i:s',$this->_tpl_vars['reviewrows'][$this->_tpl_vars['i']['key']]['posttime']).'</em><b>';
if($this->_tpl_vars['reviewrows'][$this->_tpl_vars['i']['key']]['posterid'] > 0){
echo $this->_tpl_vars['reviewrows'][$this->_tpl_vars['i']['key']]['poster'];
}else{
echo '游客';
}
echo '：</b>
	<p class="gray">';
if($this->_tpl_vars['reviewrows'][$this->_tpl_vars['i']['key']]['istop'] == 1){
echo '<span class="pop">[顶]</span>';
}
if($this->_tpl_vars['reviewrows'][$this->_tpl_vars['i']['key']]['isgood'] == 1){
echo '<span class="pop">[精]</span>';
}
echo $this->_tpl_vars['reviewrows'][$this->_tpl_vars['i']['key']]['title'].'</p>
	</a>
	</li>
';
}
echo '
</ul>

<div class="pages">'.$this->_tpl_vars['url_jumppage'].'</div>
</div>

';
if($this->_tpl_vars['enablepost'] == 1){
echo '
<script type="text/javascript">
function frmreview_validate(){
  if(document.frmreview.pcontent.value == ""){
    alert("请输入内容");
    document.frmreview.pcontent.focus();
    return false;
  }
}
</script>
<a name="postnew"></a>
<div class="block">
	<div class="blocktitle">发表书评</div>
	<div class="blockcontent">
	';
if($this->_tpl_vars['jieqi_userid'] > 0){
echo '
		<form class="cf" name="frmreview" id="frmreview" method="post" action="'.$this->_tpl_vars['jieqi_modules']['article']['url'].'/reviews.php?aid='.$this->_tpl_vars['articleid'].'" onsubmit="return frmreview_validate();" enctype="multipart/form-data">
		<fieldset>
		<div class="frow">
		<textarea class="textarea" name="pcontent" id="pcontent" style="width:100%;height:5em;"></textarea>
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
		<br />您需要 <a href="'.$this->_tpl_vars['jieqi_url'].'/login.php">登录</a> 才能发表书评！<br /><br />
	';
}
echo '
	</div>
</div>

';
}

?>