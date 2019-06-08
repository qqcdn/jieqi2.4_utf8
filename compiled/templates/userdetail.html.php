<?php
echo '

'.jieqi_get_block(array('bid'=>'0', 'blockname'=>'用户设置', 'module'=>'system', 'filename'=>'', 'classname'=>'BlockSystemCustom', 'side'=>'-1', 'title'=>'', 'vars'=>'', 'template'=>'block_userset_tab.html', 'contenttype'=>'4', 'custom'=>'1', 'publish'=>'3', 'hasvars'=>'0'), 1).'
<table class="grid" width="100%" align="center">
<caption>用户资料</caption>
  <tr align="left">
    <td width="20%"  class="tdl">用户ID：</td>
    <td width="40%" class="tdr">'.$this->_tpl_vars['uid'].'</td>
    <td width="40%" rowspan="9" class="tdr" align="center">
	<img src="'.jieqi_geturl('system','avatar',$this->_tpl_vars['uid'],'l',$this->_tpl_vars['avatar']);
if($this->_tpl_vars['refresh'] > 0){
echo '?'.$this->_tpl_vars['jieqi_time'];
}
echo '" class="avatar" alt="头像"><br />
	';
if($this->_tpl_vars['jieqi_modules']['badge']['publish'] > 0){
echo '
	<br />
    ';
if($this->_tpl_vars['url_group'] != ""){
echo '<img src="'.$this->_tpl_vars['url_group'].'" border="0" title="'.$this->_tpl_vars['jieqi_groupname'].'"><br />';
}
echo '
	';
if($this->_tpl_vars['url_honor'] != ""){
echo '<img src="'.$this->_tpl_vars['url_honor'].'" border="0" title="'.$this->_tpl_vars['jieqi_honor'].'"><br />';
}
echo '
    ';
if (empty($this->_tpl_vars['badgerows'])) $this->_tpl_vars['badgerows'] = array();
elseif (!is_array($this->_tpl_vars['badgerows'])) $this->_tpl_vars['badgerows'] = (array)$this->_tpl_vars['badgerows'];
$this->_tpl_vars['i']=array();
$this->_tpl_vars['i']['columns'] = 1;
$this->_tpl_vars['i']['count'] = count($this->_tpl_vars['badgerows']);
$this->_tpl_vars['i']['addrows'] = count($this->_tpl_vars['badgerows']) % $this->_tpl_vars['i']['columns'] == 0 ? 0 : $this->_tpl_vars['i']['columns'] - count($this->_tpl_vars['badgerows']) % $this->_tpl_vars['i']['columns'];
$this->_tpl_vars['i']['loops'] = $this->_tpl_vars['i']['count'] + $this->_tpl_vars['i']['addrows'];
reset($this->_tpl_vars['badgerows']);
for($this->_tpl_vars['i']['index'] = 0; $this->_tpl_vars['i']['index'] < $this->_tpl_vars['i']['loops']; $this->_tpl_vars['i']['index']++){
	$this->_tpl_vars['i']['order'] = $this->_tpl_vars['i']['index'] + 1;
	$this->_tpl_vars['i']['row'] = ceil($this->_tpl_vars['i']['order'] / $this->_tpl_vars['i']['columns']);
	$this->_tpl_vars['i']['column'] = $this->_tpl_vars['i']['order'] % $this->_tpl_vars['i']['columns'];
	if($this->_tpl_vars['i']['column'] == 0) $this->_tpl_vars['i']['column'] = $this->_tpl_vars['i']['columns'];
	if($this->_tpl_vars['i']['index'] < $this->_tpl_vars['i']['count']){
		list($this->_tpl_vars['i']['key'], $this->_tpl_vars['i']['value']) = each($this->_tpl_vars['badgerows']);
		$this->_tpl_vars['i']['append'] = 0;
	}else{
		$this->_tpl_vars['i']['key'] = '';
		$this->_tpl_vars['i']['value'] = '';
		$this->_tpl_vars['i']['append'] = 1;
	}
	echo '<img src="'.$this->_tpl_vars['badgerows'][$this->_tpl_vars['i']['key']]['imageurl'].'" border="0" title="'.$this->_tpl_vars['badgerows'][$this->_tpl_vars['i']['key']]['caption'].'">';
}
echo '
    ';
}
echo '
	</td>
  </tr>
  <tr align="left">
    <td class="tdl">用户名：</td>
    <td class="tdr">'.$this->_tpl_vars['uname'].'</td>
  </tr>
  <tr align="left">
    <td class="tdl">昵称：</td>
    <td class="tdr">'.$this->_tpl_vars['name'].'</td>
  </tr>
  <tr align="left">
    <td class="tdl">等级：</td>
    <td class="tdr">'.$this->_tpl_vars['group'].'</td>
  </tr>
  <tr align="left">
    <td class="tdl">头衔：</td>
    <td class="tdr">'.$this->_tpl_vars['honor'].'</td>
  </tr>
  <tr align="left">
    <td class="tdl">性别：</td>
    <td class="tdr">'.$this->_tpl_vars['sex'].'</td>
  </tr>
  <tr align="left">
    <td class="tdl">Email：</td>
    <td class="tdr">
      ';
if($this->_tpl_vars['email'] != ''){
echo '
        <a href="mailto:'.$this->_tpl_vars['email'].'">'.$this->_tpl_vars['email'].'</a>
        ';
if($this->_tpl_vars['verify']['email'] > 0){
echo '<span class="hot">&nbsp;&nbsp;已验证</span> <a href="'.$this->_tpl_vars['jieqi_url'].'/emailverify.php?sendemail=1&cancel=1">[发解绑邮件]</a>';
}elseif($this->_tpl_vars['sendemail'] > 0){
echo '&nbsp;&nbsp;<span class="hot">验证邮件已发送！</span><a href="'.$this->_tpl_vars['email_link'].'" target="_blank">[查看]</a>';
}else{
echo '<span class="hot">&nbsp;&nbsp;未验证</span> <a href="'.$this->_tpl_vars['jieqi_url'].'/emailverify.php?sendemail=1">[发验证邮件]</a>';
}
echo '
      ';
}
echo '
    </td>
  </tr>
  <tr align="left">
    <td class="tdl">QQ：</td>
    <td class="tdr">'.$this->_tpl_vars['qq'].'</td>
  </tr>
  <tr align="left">
    <td class="tdl">网站：</td>
    <td class="tdr"><a href="'.$this->_tpl_vars['url'].'" target="_blank">'.$this->_tpl_vars['url'].'</a></td>
  </tr>
  <tr>
    <td colspan="3" class="foot">积分相关</td>
  </tr>
  <tr align="left">
    <td class="tdl">注册日期：</td>
    <td colspan="2" class="tdr">'.date('Y-m-d',$this->_tpl_vars['regdate']).'</td>
  </tr>
  <!--
  <tr align="left">
    <td class="tdl">贡献值：</td>
    <td colspan="2" class="tdr">'.$this->_tpl_vars['credit'].'</td>
  </tr>
  -->
  <tr align="left">
    <td class="tdl">经验值：</td>
    <td colspan="2" class="tdr">'.$this->_tpl_vars['experience'].'</td>
  </tr>
  <tr align="left">
    <td class="tdl">现有积分：</td>
    <td colspan="2" class="tdr">'.$this->_tpl_vars['score'].'</td>
  </tr>
  <tr align="left">
    <td class="tdl">最多好友数：</td>
    <td colspan="2" class="tdr">'.intval($this->_tpl_vars['right']['system']['maxfriends']).'</td>
  </tr>
  <tr align="left">
    <td class="tdl">信箱最多消息数：</td>
    <td colspan="2" class="tdr">'.intval($this->_tpl_vars['right']['system']['maxmessages']).'</td>
  </tr>
  <tr align="left">
    <td class="tdl">书架最大收藏量：</td>
    <td colspan="2" class="tdr">'.intval($this->_tpl_vars['right']['article']['maxbookmarks']).'</td>
  </tr>
  <tr align="left">
    <td class="tdl">每天允许推荐次数：</td>
    <td colspan="2" class="tdr">'.intval($this->_tpl_vars['right']['article']['dayvotes']).'</td>
  </tr>
  <tr align="left">
    <td class="tdl">每天允许评分次数：</td>
    <td colspan="2" class="tdr">'.intval($this->_tpl_vars['right']['article']['dayrates']).'</td>
  </tr>
  <tr align="left">
    <td class="tdl">现有月票数：</td>
    <td colspan="2" class="tdr">'.intval($this->_tpl_vars['setting']['gift']['vipvote']).'</td>
  </tr>
  <tr>
    <td colspan="3" class="foot">VIP信息</td>
  </tr>
  <tr align="left">
    <td class="tdl">VIP类型：</td>
    <td colspan="2" class="tdr">';
if($this->_tpl_vars['isvip'] <= 0){
echo '非vip会员';
}else{
echo 'VIP会员';
}
echo '</td>
  </tr>
  <tr align="left">
    <td class="tdl">'.$this->_tpl_vars['egoldname'].'：</td>
    <td colspan="2" class="tdr">'.$this->_tpl_vars['egold'].' &nbsp; [<a href="'.$this->_tpl_vars['jieqi_modules']['pay']['url'].'/buyegold.php">充值</a>]</td>
  </tr>
  ';
if($this->_tpl_vars['channelerate'] > 0 || $this->_tpl_vars['esilver']){
echo '
  <tr align="left">
    <td class="tdl">分成收入：</td>
    <td colspan="2" class="tdr">'.fen2yuan($this->_tpl_vars['esilver']).'元';
if($this->_tpl_vars['esilver'] > 0 && $this->_tpl_vars['exchangerate'] > 0){
echo ' &nbsp; [<a href="'.$this->_tpl_vars['jieqi_modules']['pay']['url'].'/exchange.php">兑换'.$this->_tpl_vars['egoldname'].'</a>]';
}
if($this->_tpl_vars['settlemin'] > 0 && $this->_tpl_vars['esilver'] > $this->_tpl_vars['settlemin']|yuan2fen){
echo ' &nbsp; [<a href="'.$this->_tpl_vars['jieqi_url'].'/newmessage.php?tosys=1&title=分成收入申请提现&content=我的分成收入已达'.fen2yuan($this->_tpl_vars['esilver']).'元，现申请提现！">申请提现</a>]';
}
echo '</td>
  </tr>
  ';
}
echo '
  <tr>
    <td colspan="3" class="foot">其他信息</td>
  </tr>
  <tr align="left">
    <td class="tdl">用户签名：</td>
    <td colspan="2" class="tdr">'.$this->_tpl_vars['sign'].'</td>
  </tr>
  <tr align="left">
    <td class="tdl">个人简介：</td>
    <td colspan="2" class="tdr">'.$this->_tpl_vars['intro'].'</td>
  </tr>
</table>
';
?>