<?php
echo '

<div style="text-align:center; padding:4em 0;">
    <div class="block">
      <div class="blockcontent fsl pd tc error">
		<p>'.$this->_tpl_vars['content'].'</p>
		';
if($this->_tpl_vars['debuginfo'] != ''){
echo '<p>'.$this->_tpl_vars['debuginfo'].'</p>';
}
echo '
	  </div>
	</div>
</div>';
?>