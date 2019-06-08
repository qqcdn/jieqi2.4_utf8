<?php
echo '

<div class="blockb">
	<div class="blocktitle" id="paytname">&nbsp;';
if($this->_tpl_vars['jieqi_browser'] == 'weixin'){
echo '微信支付';
}else{
echo '支付宝充值';
}
echo '</div>
<div class="blockcontent">
	<form name="frmdftpay" id="frmdftpay" method="post" action="'.$this->_tpl_vars['jieqi_modules']['pay']['url'].'/';
if($this->_tpl_vars['jieqi_browser'] == 'weixin'){
echo 'wxjsapi.php';
}else{
echo 'aliwap.php';
}
echo '">
	<div class="checkgroup cf">
	  <label class="radio"><input type="radio" name="egold" value="1000" checked="checked"><span> 1000&nbsp;&nbsp;'.$this->_tpl_vars['egoldname'].'（10元）</span></label>
	  <label class="radio"><input type="radio" name="egold" value="2000"><span> 2000&nbsp;&nbsp;'.$this->_tpl_vars['egoldname'].'（20元）</span></label>
	  <label class="radio"><input type="radio" name="egold" value="5000"><span> 5000&nbsp;&nbsp;'.$this->_tpl_vars['egoldname'].'（50元）</span></label>
	  <label class="radio"><input type="radio" name="egold" value="10000"><span> 10000&nbsp;'.$this->_tpl_vars['egoldname'].'（100元）</span></label>
	  <label class="radio"><input type="radio" name="egold" value="20000"><span> 20000&nbsp;'.$this->_tpl_vars['egoldname'].'（200元）</span></label>
	  <label class="radio"><input type="radio" name="egold" value="50000"><span> 50000&nbsp;'.$this->_tpl_vars['egoldname'].'（500元）</span></label>
	</div>
	<button type="submit" name="Submit" class="button">进入下一步</button>
    <input type="hidden" name="act" value="pay" />'.$this->_tpl_vars['jieqi_token_input'].'
	<input type="hidden" name="jumpurl" value="'.$this->_tpl_vars['jumpurl'].'" />
	</form>
</div>
</div>

<div class="textbox">
<strong>说明：</strong><br />
充值兑换比例：<span class="hot">1</span>元=<span class="hot">100</span>'.$this->_tpl_vars['egoldname'].'<br />

</div>';
?>