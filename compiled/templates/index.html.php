<?php
echo '
<div class="main">
    <div class="mainbody">

		';
if($this->_tpl_vars['jieqi_pageblocks']['200']['content'] != ''){
echo '
        <div class="mb">
            '.$this->_tpl_vars['jieqi_pageblocks']['200']['content'].'
        </div>
		';
}
echo '


        <div class="row">
            <div class="col3">
                <div class="block" style="height:365px;">
                    <div class="blocktitle">推荐榜</div>
                    <div class="blockcontent">
                        '.$this->_tpl_vars['jieqi_pageblocks']['131']['content'].'
                    </div>
                </div>
            </div>

            <div class="col6">
                <div class="block" style="height:365px;">
                    <div class="blocktitle">
                        <ul class="tabb tab2">
                            <li>最近更新</li>
                        </ul>
                    </div>
                    <div class="blockcontent">
                        <div>'.$this->_tpl_vars['jieqi_pageblocks']['321']['content'].'</div>
                       
                    </div>
                </div>
            </div>

            <div class="col3 last">
                <div class="block" style="height:365px;">
                    <div class="blocktitle">收藏榜</div>
                    <div class="blockcontent">'.$this->_tpl_vars['jieqi_pageblocks']['331']['content'].'</div>
                </div>
            </div>
        </div>
    </div>
</div>';
?>