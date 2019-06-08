<?php
echo '

<ul class="tabb tab3 cf mb">
	<li><a href="javascript:void(0);" class="selected">最近阅读</a></li>
	<li><a href="'.$this->_tpl_vars['jieqi_modules']['article']['url'].'/bookcase.php">我的书架</a></li>
	<li><a href="'.$this->_tpl_vars['jieqi_modules']['obook']['url'].'/buylist.php">我的订阅</a></li>
</ul>

<div class="blockn">
	<div class="blockcontent">	
	<script type="text/javascript" src="'.$this->_tpl_vars['jieqi_url'].'/scripts/json2.js"></script>
	<script type="text/javascript" src="'.$this->_tpl_vars['jieqi_modules']['article']['url'].'/scripts/readshow.js"></script>
	</div>
</div>';
?>