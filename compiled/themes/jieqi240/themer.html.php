<?php
echo '<!doctype html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset='.$this->_tpl_vars['jieqi_charset'].'" />
<meta name="keywords" content="'.$this->_tpl_vars['articlename'].' '.$this->_tpl_vars['chaptername'].' '.$this->_tpl_vars['author'].' '.$this->_tpl_vars['sort'].' '.$this->_tpl_vars['jieqi_sitename'].'" />
<meta name="description" content="';
if(isset($this->_tpl_vars['summary']) == true){
echo truncate($this->_tpl_vars['summary'],'500','..');
}else{
echo truncate($this->_tpl_vars['intro'],'500','..');
}
echo '" />
<meta name="author" content="'.$this->_tpl_vars['meta_author'].'" />
<meta name="copyright" content="'.$this->_tpl_vars['meta_copyright'].'" />
<meta name="generator" content="jieqi.com" />
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>'.$this->_tpl_vars['articlename'].'-'.$this->_tpl_vars['chaptername'].'-'.$this->_tpl_vars['author'].'-'.$this->_tpl_vars['sort'].'-'.$this->_tpl_vars['jieqi_sitename'].'</title>
<link rel="stylesheet" href="'.$this->_tpl_vars['jieqi_themeurl'].'style.css" type="text/css" media="all" />
<link rel="stylesheet" href="'.$this->_tpl_vars['jieqi_modules']['article']['url'].'/css/page.css" type="text/css" media="all"  />
<!--[if lt IE 9]><script type="text/javascript" src="'.$this->_tpl_vars['jieqi_url'].'/scripts/html5.js"></script><![endif]-->
<!--[if lt IE 9]><script type="text/javascript" src="'.$this->_tpl_vars['jieqi_url'].'/scripts/css3-mediaqueries.js"></script><![endif]--> 
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
	articleid: \''.intval($this->_tpl_vars['articleid']).'\',
	articlename: \''.$this->_tpl_vars['articlename'].'\',
	authorid: \''.intval($this->_tpl_vars['authorid']).'\',
	author: \''.$this->_tpl_vars['author'].'\',
	chapterid: \''.intval($this->_tpl_vars['chapterid']).'\',
	chaptername: \''.$this->_tpl_vars['chaptername'].'\',
	chapterisvip: \''.$this->_tpl_vars['chapterisvip'].'\',
	userid: \''.intval($this->_tpl_vars['jieqi_userid']).'\',
	egoldname: \''.$this->_tpl_vars['egoldname'].'\'
}

function jumpPage() {
  if(document.activeElement && [\'INPUT\', \'TEXTAREA\', \'BUTTON\'].indexOf(document.activeElement.tagName) == -1){
	var event = getEvent();
    if (event.keyCode == 37) window.location.href = ReadParams.url_previous;
    if (event.keyCode == 39) window.location.href = ReadParams.url_next;
    if (event.keyCode == 13) window.location.href = ReadParams.url_index;
  }
}
document.onkeydown=jumpPage;
</script>
</head>
<body>
<div class="top cf">
	<div class="main">
	<script type="text/javascript" src="'.$this->_tpl_vars['jieqi_url'].'/scripts/toplink.js"></script>
	</div>
</div>
'.$this->_tpl_vars['jieqi_contents'].'

</body>
</html>';
?>