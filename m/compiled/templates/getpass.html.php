<?php
echo '

<script type="text/javascript">
function frmgetpass_validate(){
  if(document.frmgetpass.uname.value == ""){
	alert( "请输入用户名" );
	document.frmgetpass.uname.focus();
	return false;
  }
  if(document.frmgetpass.email.value == ""){
	alert( "请输入Email" );
	document.frmgetpass.email.focus();
	return false;
  }
}
</script>

<form class="form cf" name="frmgetpass" method="post" action="'.$this->_tpl_vars['jieqi_url'].'/getpass.php?do=submit" onsubmit="return frmgetpass_validate();">
	<fieldset>
		<div class="frow">
			<label class="col4 flabel">用户名：</label>
			<div class="col8 last">
			  <input type="text" class="text" name="uname" value="" />
			</div>
		</div>

		<div class="frow">
			<label class="col4 flabel">Email：</label>
			<div class="col8 last">
			  <input type="text" class="text" name="email" value="" />
			</div>
		</div>

		<div class="frow">
			<label class="col4 flabel"><input type="hidden" name="act" value="sendpass" />'.$this->_tpl_vars['jieqi_token_input'].'</label>
			<div class="col8 last">
			  <button type="submit" class="button" name="submit">提 交</button>
			</div>
		</div>
	</fieldset>
</form>';
?>