<?php 
$jieqiBlocks[] = array('bid'=>0, 'blockname'=>'用户设置', 'module'=>'system', 'filename'=>'', 'classname'=>'BlockSystemCustom', 'side'=>9, 'title'=>'用户设置', 'vars'=>'', 'template'=>'block_userset.html', 'contenttype'=>4, 'custom'=>1, 'publish'=>3, 'hasvars'=>0);

$jieqiBlocks[] = array('bid'=>0, 'blockname'=>'站内消息', 'module'=>'system', 'filename'=>'', 'classname'=>'BlockSystemCustom', 'side'=>9, 'title'=>'站内消息', 'vars'=>'', 'template'=>'block_message.html', 'contenttype'=>4, 'custom'=>1, 'publish'=>3, 'hasvars'=>0);

$jieqiBlocks[] = array('bid'=>0, 'blockname'=>'会员工具', 'module'=>'system', 'filename'=>'', 'classname'=>'BlockSystemCustom', 'side'=>9, 'title'=>'会员工具', 'vars'=>'', 'template'=>'block_userbox.html', 'contenttype'=>4, 'custom'=>1, 'publish'=>3, 'hasvars'=>0);

global $jieqiConfigs;
if(!isset($jieqiConfigs['system'])) jieqi_getconfigs('system', 'configs', 'jieqiConfigs');
if(isset($jieqiConfigs['system']['channelerate']) && is_numeric($jieqiConfigs['system']['channelerate']) && $jieqiConfigs['system']['channelerate'] > 0){
	$jieqiBlocks[] = array('bid'=>0, 'blockname'=>'推广分成', 'module'=>'system', 'filename'=>'', 'classname'=>'BlockSystemCustom', 'side'=>9, 'title'=>'推广分成', 'vars'=>'', 'template'=>'block_channelnav.html', 'contenttype'=>4, 'custom'=>1, 'publish'=>3, 'hasvars'=>0);
}

?>