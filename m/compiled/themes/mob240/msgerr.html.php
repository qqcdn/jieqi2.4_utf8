<?php
echo '

<div style="text-align:center; padding:4em 0;">
	<div class="block">
		<div class="blockcontent fsl pd tc error">
			<p>'.$this->_tpl_vars['errorinfo'].'</p>
			';
if($this->_tpl_vars['debuginfo'] != ''){
echo '<p>'.$this->_tpl_vars['debuginfo'].'</p>';
}
echo '
			';
if($this->_tpl_vars['ispost'] > 0){
echo '<br /><p class="mt mb"><a class="btnlink" href="javascript:history.back(1)">返回上一步</a></p>';
}
echo '
		</div>
	</div>
</div>';
?>