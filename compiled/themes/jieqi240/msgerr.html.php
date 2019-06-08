<?php
echo '<div class="msgbox">
	<div class="block">
		<div class="blocktitle">出现错误！</div>
		<div class="blockcontent">
			<div style="padding:10px;text-align:left;">
				错误原因：'.$this->_tpl_vars['errorinfo'].'
				';
if($this->_tpl_vars['debuginfo'] != ''){
echo '<br/>'.$this->_tpl_vars['debuginfo'];
}
echo '
				';
if($this->_tpl_vars['ispost'] > 0){
echo '<br />请 <a href="javascript:history.back(1)">返 回</a> 并修正';
}
echo '
			</div>
		</div>
	</div>
</div>';
?>