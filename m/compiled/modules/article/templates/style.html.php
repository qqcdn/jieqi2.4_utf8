<?php
echo '<!doctype html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset='.$this->_tpl_vars['jieqi_charset'].'" />
<meta name="viewport" content="initial-scale=1.0,maximum-scale=1.0,minimum-scale=1.0,user-scalable=0,width=device-width" />
<meta name="keywords" content="'.$this->_tpl_vars['articlename'].' '.$this->_tpl_vars['author'].' '.$this->_tpl_vars['sort'].' '.$this->_tpl_vars['jieqi_sitename'].'" />
<meta name="description" content="'.truncate($this->_tpl_vars['intro'],'500','..').'" />
<meta name="author" content="'.$this->_tpl_vars['meta_author'].'" />
<meta name="copyright" content="'.$this->_tpl_vars['meta_copyright'].'" />
<meta name="generator" content="jieqi.com" />
<title>'.$this->_tpl_vars['articlename'].'-'.$this->_tpl_vars['author'].'-'.$this->_tpl_vars['sort'].'-'.$this->_tpl_vars['jieqi_sitename'].'</title>
<link rel="stylesheet" href="'.$this->_tpl_vars['jieqi_themeurl'].'style.css" type="text/css" media="all" />
<link rel="stylesheet" href="'.$this->_tpl_vars['jieqi_modules']['article']['themeurl'].'/css/page.css" type="text/css" media="all" />
<script type="text/javascript" src="'.$this->_tpl_vars['jieqi_url'].'/scripts/common.js"></script>
<script type="text/javascript" src="'.$this->_tpl_vars['jieqi_url'].'/scripts/theme.js"></script>
<script type="text/javascript">
var ReadParams = {
	url_previous: \''.$this->_tpl_vars['url_previous'].'\',
    url_next: \''.$this->_tpl_vars['url_next'].'\',
    url_index: \''.$this->_tpl_vars['url_index'].'\',
    url_articleinfo: \''.$this->_tpl_vars['url_articleinfo'].'\',
    url_image: \''.$this->_tpl_vars['url_image'].'\',
    url_home: \''.$this->_tpl_vars['jieqi_url'].'/\',
    articleid: \''.$this->_tpl_vars['articleid'].'\',
    articlename: \''.$this->_tpl_vars['articlename'].'\',
    authorid: \''.$this->_tpl_vars['authorid'].'\',
    author: \''.$this->_tpl_vars['author'].'\',
    chapterid: \''.$this->_tpl_vars['chapterid'].'\',
    chaptername: \''.$this->_tpl_vars['chaptername'].'\',
    chapterisvip: \''.$this->_tpl_vars['chapterisvip'].'\',
    userid: \''.$this->_tpl_vars['jieqi_userid'].'\',
    egoldname: \''.$this->_tpl_vars['egoldname'].'\'
}
</script>
</head>
<body>
<!-- <div class="pagetitle cf"><a href="'.$this->_tpl_vars['url_index'].'"><i class="iconfont fl">&#xee69;</i></a><a href="'.$this->_tpl_vars['jieqi_url'].'/"><i class="iconfont fr">&#xee27;</i></a>章节阅读</div> -->

<div id="aread" class="main cf">
	<div class="cb"></div>
	<div id="abox" class="abox">
	<div id="apage" class="apage">
		<div id="atitle" class="atitle">'.$this->_tpl_vars['chaptername'].'</div>
		<div id="acontent" class="acontent">'.$this->_tpl_vars['jieqi_content'].'</div>
		<div id="footlink" class="footlink"><a href="'.$this->_tpl_vars['url_previous'].'">上一章</a> &nbsp;&nbsp;&nbsp;&nbsp; <a href="'.$this->_tpl_vars['url_index'].'">返回目录</a> &nbsp;&nbsp;&nbsp;&nbsp; <a href="'.$this->_tpl_vars['url_next'].'">下一章</a></div>
	</div>
	</div>

	<div id="toptext" class="toptext" style="display:none;"></div>
	<div id="bottomtext" class="bottomtext" style="display:none;"></div>
	<div id="operatetip" class="operatetip" style="display:none;" onclick="this.style.display=\'none\';">
		<div class="tipl"><p>翻上页</p></div>
		<div class="tipc"><p>呼出功能</p></div>
		<div class="tipr"><p>翻下页</p></div>
	</div>

</div>
<script type="text/javascript" src="'.$this->_tpl_vars['jieqi_modules']['article']['themeurl'].'/scripts/readtools.js"></script>
<script type="text/javascript" src="'.$this->_tpl_vars['jieqi_url'].'/scripts/json2.js"></script>
<script type="text/javascript" src="'.$this->_tpl_vars['jieqi_modules']['article']['themeurl'].'/scripts/readlog.js"></script>

</body>
</html>';
?>