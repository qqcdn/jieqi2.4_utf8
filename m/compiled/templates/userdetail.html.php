<?php
echo '

<div class="block mt">
	<div class="blocktitle">会员信息</div>
	<div class="blockcontent">

	<div class="c_row cf">
	<div class="row_cover">
	<a href="'.$this->_tpl_vars['jieqi_url'].'/setavatar.php">
	<img src="'.jieqi_geturl('system','avatar',$this->_tpl_vars['uid'],'s',$this->_tpl_vars['avatar']);
if($this->_tpl_vars['refresh'] > 0){
echo '?'.$this->_tpl_vars['jieqi_time'];
}
echo '" class="avatar bdc" alt="头像">
	</a>
	</div>

	<div class="row_text">
	<a class="db" href="'.$this->_tpl_vars['jieqi_url'].'/useredit.php">
		<h4>'.$this->_tpl_vars['name'];
if($this->_tpl_vars['isvip'] > 0){
echo ' <span class="iconfont fsl hot">&#xee6f;</span>';
}
echo '</h4>
	<p class="gray">'.$this->_tpl_vars['group'].' / '.$this->_tpl_vars['honor'].'</p>
  </a>
	</div>
	</div>

	<ul class="ulrow mts">
	<li>'.$this->_tpl_vars['egoldname'].'：'.$this->_tpl_vars['egold'].' &nbsp; <a class="btnlink b_s b_hot" href="'.$this->_tpl_vars['jieqi_modules']['pay']['url'].'/buyegold.php">&nbsp;充值&nbsp;</a></li>
	<li>积分：'.$this->_tpl_vars['score'].'</li>
	</ul>
	</div>
</div>
';
?>