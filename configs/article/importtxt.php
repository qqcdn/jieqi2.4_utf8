<?php
//导入txt小说配置

//过滤html代码
$jieqiImporttxt['txtfilter'] = array('preg' => '/<([\x0A-\x3B\x3F-\x7F\x3D]|“|”|　)*>/', 'replace' => '');

//匹配分卷名
$jieqiImporttxt['volume'] = array('preg' => '/^\s*(第([0-9]|零|一|二|三|四|五|六|七|八|九|十)+卷.*)$/', 'no' => 1, 'maxlen' => 60, 'tagmatch' => '/(章|节|卷|话).*([0-9]|零|一|二|三|四|五|六|七|八|九|十)|([0-9]|零|一|二|三|四|五|六|七|八|九|十).*(章|节|卷|话)/');

//匹配章节名
$jieqiImporttxt['chapter'] = array('preg' => '/^\s*(第([0-9]|零|一|二|三|四|五|六|七|八|九|十)+章.*)$/', 'no' => 1, 'maxlen' => 60, 'tagmatch' => '/(章|节|卷|话).*([0-9]|零|一|二|三|四|五|六|七|八|九|十)|([0-9]|零|一|二|三|四|五|六|七|八|九|十).*(章|节|卷|话)/');


//过滤章节内容中的广告或标记
$jieqiImporttxt['chapterfilter'] = array();
?>