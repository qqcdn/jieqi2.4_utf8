<?php
echo '
<script type="text/javascript" src="'.$this->_tpl_vars['jieqi_url'].'/scripts/rating.js"></script>
<div class="divbox cf">
	<div style="width:25%;margin:1%;float:left;text-align:center;">
	<img src="'.$this->_tpl_vars['url_simage'].'" style="border:1px solid #cccccc;width:100%;" />
    <ul class="ulrow mt tc">
    <li><a class="btnlink b_hot mbs" href="'.$this->_tpl_vars['url_read'].'">点击阅读</a></li>
	<li><a class="btnlink mbs" id="a_addbookcase" href="javascript:;" onclick="if('.$this->_tpl_vars['jieqi_userid'].') Ajax.Tip(\''.$this->_tpl_vars['url_bookcase'].'\', {method: \'POST\'}); else openDialog(\''.$this->_tpl_vars['jieqi_user_url'].'/login.php?jumpurl='.urlencode($this->_tpl_vars['jieqi_thisurl']).'&ajax_gets=jieqi_contents\', false);">加入书架</a></li>
	';
if($this->_tpl_vars['makefull'] > 0){
echo '<li><a class="btnlink mbs" href="'.$this->_tpl_vars['url_fullpage'].'">全文阅读</a></li>';
}
echo '
    </ul>
  </div>
  <div style="width:70%;margin:1%;float:right;">
    <div style="text-align:center;">
      <span style="font-size:20px;font-weight:bold;color:#f27622;">'.$this->_tpl_vars['articlename'].'</span>
      <span>&nbsp;&nbsp;作者：<a href="'.jieqi_geturl('article','author',$this->_tpl_vars['authorid'],$this->_tpl_vars['author']).'" target="_blank">'.$this->_tpl_vars['author'].'</a></span>
    </div>
	<div style="text-align:center;margin:8px 0px;">
	  <div class="ratediv"><b class="fl">评分：</b>
	    <div class="rateblock" id="rate_star">
	    <script type="text/javascript">
		  showRating('.$this->_tpl_vars['ratemax'].', '.$this->_tpl_vars['rateavg'].', \'rating\', \''.$this->_tpl_vars['articleid'].'\');
		  function rating(score, id){
			Ajax.Tip(\''.$this->_tpl_vars['article_dynamic_url'].'/rating.php?score=\'+score+\'&id=\'+id, {method: \'POST\', eid: \'rate_star\'});
		  }
	    </script>
	    </div>
		<span class="ratenum">'.$this->_tpl_vars['rateavg'].'</span> <span class="gray">('.$this->_tpl_vars['ratenum'].'人已评)</span>
      </div>
	</div>
	<div>
      <div class="tablist">
         <ul>
             <li><a href="javascript:void(0)" onmouseover="selecttab(this)" class="selected">内容介绍</a></li>
             <li><a href="javascript:void(0)" onmouseover="selecttab(this)">作品信息</a></li>
         </ul>
      </div>
      <div class="tabcontent">
         <div class="tabvalue" style="height:210px;">
		   <div style="padding:3px;border:0;height:100%;width:100%;overflow-y:scroll;">'.htmlclickable($this->_tpl_vars['intro']).'</div>
		 </div>
         <div class="tabvalue" style="display:none;height:210px;">
		   <table width="100%" class="hide">
			<tr>
              <td width="32%">作品分类：'.$this->_tpl_vars['sort'].'</td>
              <td width="32%">连载状态：'.$this->_tpl_vars['fullflag'].'</td>
              <td width="36%">最后更新：'.date('Y-m-d',$this->_tpl_vars['lastupdate']).'</td>
            </tr>
			<tr>
              <td>作品性质：'.$this->_tpl_vars['isvip'].'</td>
              <td>授权级别：'.$this->_tpl_vars['permission'].'</td>
              <td>首发网站：'.$this->_tpl_vars['firstflag'].'</td>
            </tr>
			<tr>
              <td>全文字数：'.$this->_tpl_vars['words'].'</td>
              <td>章 节 数：'.$this->_tpl_vars['chapters'].'</td>
              <td>收 藏 数：'.$this->_tpl_vars['goodnum'].'</td>
            </tr>
            <tr>
              <td>总点击数：'.$this->_tpl_vars['allvisit'].'</td>
              <td>本月点击：'.$this->_tpl_vars['monthvisit'].'</td>
              <td>本周点击：'.$this->_tpl_vars['weekvisit'].'</td>
            </tr>
            <tr>
              <td>总推荐数：'.$this->_tpl_vars['allvote'].'</td>
              <td>本月推荐：'.$this->_tpl_vars['monthvote'].'</td>
              <td>本周推荐：'.$this->_tpl_vars['weekvote'].'</td>
            </tr>
          </table>
		 </div>
      </div>
    </div>
   </div>
</div>
<div>
<ul class="ultab">
<li><a href="javascript:;" class="selected">&nbsp;&nbsp;最新章节&nbsp;&nbsp;</a></li>
</ul>
</div>
<div class="divbox">
';
if($this->_tpl_vars['isvip_n'] > 0 && $this->_tpl_vars['vipchapterid'] > 0){
echo '
<h3><a href="'.$this->_tpl_vars['url_vipchapter'].'">';
if($this->_tpl_vars['vipvolume'] != ''){
echo $this->_tpl_vars['vipvolume'].' ';
}
echo $this->_tpl_vars['vipchapter'].'</a> <i class="hot">vip</i></h3>
<p class="fss">'.$this->_tpl_vars['vipsummary'].'</p>
';
}else{
echo '
<h3><a href="'.$this->_tpl_vars['url_lastchapter'].'">';
if($this->_tpl_vars['lastvolume'] != ''){
echo $this->_tpl_vars['lastvolume'].' ';
}
echo $this->_tpl_vars['lastchapter'].'</a></h3>
<p class="fss">'.$this->_tpl_vars['lastsummary'].'</p>
';
}
echo '
<div class="divbg">'.jieqi_get_block(array('bid'=>'0', 'blockname'=>'最新章节', 'module'=>'article', 'filename'=>'block_achapters', 'classname'=>'BlockArticleAchapters', 'side'=>'-1', 'title'=>'', 'vars'=>'chapterorder,12,0,$articleid,1', 'template'=>'chapter_infolist.html', 'contenttype'=>'4', 'custom'=>'0', 'publish'=>'3', 'hasvars'=>'1'), 1).'</div>
</div>

';
if($this->_tpl_vars['showvote'] > 0){
echo '
<div class="divbox">
  <form name="frmvote" id="frmvote" method="post" action="'.$this->_tpl_vars['jieqi_modules']['article']['url'].'/votedo.php" target="_blank">  
  <table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td width="50%"><span class="hot">作品投票调查：</span></td>
    <td width="50%" align="right">
	  <input name="aid" type="hidden" value="'.$this->_tpl_vars['articleid'].'" />
	  <input name="vid" type="hidden" value="'.$this->_tpl_vars['voteid'].'" />
      <!-- <button type="submit" name="votepost" class="button">提交投票</button>&nbsp; -->
	  <button type="button" name="votepost" id="votepost" class="button" style="cursor:pointer;" onclick="Ajax.Request(\'frmvote\',{onComplete:function(){alert(this.response.replace(/<br[^<>]*>/g,\'\\n\')); Form.reset(\'frmvote\');}});">提交投票</button>&nbsp;
      <button type="button" name="voteshow" class="button" onclick="window.open(\''.$this->_tpl_vars['jieqi_modules']['article']['url'].'/voteresult.php?id='.$this->_tpl_vars['voteid'].'\')">查看结果</button>
      &nbsp;</td>
  </tr>
  <tr>
    <td colspan="2">主题：<strong>'.$this->_tpl_vars['votetitle'].'</strong></td>
  </tr>
  <tr>
    <td colspan="2">
	<ul>
		';
if (empty($this->_tpl_vars['voteitemrows'])) $this->_tpl_vars['voteitemrows'] = array();
elseif (!is_array($this->_tpl_vars['voteitemrows'])) $this->_tpl_vars['voteitemrows'] = (array)$this->_tpl_vars['voteitemrows'];
$this->_tpl_vars['i']=array();
$this->_tpl_vars['i']['columns'] = 1;
$this->_tpl_vars['i']['count'] = count($this->_tpl_vars['voteitemrows']);
$this->_tpl_vars['i']['addrows'] = count($this->_tpl_vars['voteitemrows']) % $this->_tpl_vars['i']['columns'] == 0 ? 0 : $this->_tpl_vars['i']['columns'] - count($this->_tpl_vars['voteitemrows']) % $this->_tpl_vars['i']['columns'];
$this->_tpl_vars['i']['loops'] = $this->_tpl_vars['i']['count'] + $this->_tpl_vars['i']['addrows'];
reset($this->_tpl_vars['voteitemrows']);
for($this->_tpl_vars['i']['index'] = 0; $this->_tpl_vars['i']['index'] < $this->_tpl_vars['i']['loops']; $this->_tpl_vars['i']['index']++){
	$this->_tpl_vars['i']['order'] = $this->_tpl_vars['i']['index'] + 1;
	$this->_tpl_vars['i']['row'] = ceil($this->_tpl_vars['i']['order'] / $this->_tpl_vars['i']['columns']);
	$this->_tpl_vars['i']['column'] = $this->_tpl_vars['i']['order'] % $this->_tpl_vars['i']['columns'];
	if($this->_tpl_vars['i']['column'] == 0) $this->_tpl_vars['i']['column'] = $this->_tpl_vars['i']['columns'];
	if($this->_tpl_vars['i']['index'] < $this->_tpl_vars['i']['count']){
		list($this->_tpl_vars['i']['key'], $this->_tpl_vars['i']['value']) = each($this->_tpl_vars['voteitemrows']);
		$this->_tpl_vars['i']['append'] = 0;
	}else{
		$this->_tpl_vars['i']['key'] = '';
		$this->_tpl_vars['i']['value'] = '';
		$this->_tpl_vars['i']['append'] = 1;
	}
	echo '
		<li style="width:49%;float:left;padding:3px;">
		';
if($this->_tpl_vars['mulselect'] == 1){
echo '
		<label class="checkbox"><input type="checkbox" name="voteitem[]" value="'.$this->_tpl_vars['voteitemrows'][$this->_tpl_vars['i']['key']]['id'].'" />'.$this->_tpl_vars['voteitemrows'][$this->_tpl_vars['i']['key']]['item'].'</label>
		';
}else{
echo '
		<label class="radio"><input type="radio" name="voteitem" value="'.$this->_tpl_vars['voteitemrows'][$this->_tpl_vars['i']['key']]['id'].'" />'.$this->_tpl_vars['voteitemrows'][$this->_tpl_vars['i']['key']]['item'].'</label>
		';
}
echo '
		</li>
		';
}
echo '
    </ul>
	</td>
  </tr>
  </table>
  </form>
</div>
';
}

?>