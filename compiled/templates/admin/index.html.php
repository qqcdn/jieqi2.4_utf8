<?php
echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>'.$this->_tpl_vars['jieqi_pagetitle'].'</title>
<meta http-equiv="Content-Type" content="text/html; charset='.$this->_tpl_vars['jieqi_charset'].'" />
<meta name="generator" content="jieqi.com" />
<link rel="stylesheet" type="text/css" media="all" href="'.$this->_tpl_vars['jieqi_url'].'/css/admin_frame.css" />
<script type="text/javascript">
var leftmenus = new Array(\'mu_system\', \'mu_users\', \'mu_tools\', \'mu_database\', \'mu_plguin\');
';
if (empty($this->_tpl_vars['jieqimodules'])) $this->_tpl_vars['jieqimodules'] = array();
elseif (!is_array($this->_tpl_vars['jieqimodules'])) $this->_tpl_vars['jieqimodules'] = (array)$this->_tpl_vars['jieqimodules'];
$this->_tpl_vars['i']=array();
$this->_tpl_vars['i']['columns'] = 1;
$this->_tpl_vars['i']['count'] = count($this->_tpl_vars['jieqimodules']);
$this->_tpl_vars['i']['addrows'] = count($this->_tpl_vars['jieqimodules']) % $this->_tpl_vars['i']['columns'] == 0 ? 0 : $this->_tpl_vars['i']['columns'] - count($this->_tpl_vars['jieqimodules']) % $this->_tpl_vars['i']['columns'];
$this->_tpl_vars['i']['loops'] = $this->_tpl_vars['i']['count'] + $this->_tpl_vars['i']['addrows'];
reset($this->_tpl_vars['jieqimodules']);
for($this->_tpl_vars['i']['index'] = 0; $this->_tpl_vars['i']['index'] < $this->_tpl_vars['i']['loops']; $this->_tpl_vars['i']['index']++){
	$this->_tpl_vars['i']['order'] = $this->_tpl_vars['i']['index'] + 1;
	$this->_tpl_vars['i']['row'] = ceil($this->_tpl_vars['i']['order'] / $this->_tpl_vars['i']['columns']);
	$this->_tpl_vars['i']['column'] = $this->_tpl_vars['i']['order'] % $this->_tpl_vars['i']['columns'];
	if($this->_tpl_vars['i']['column'] == 0) $this->_tpl_vars['i']['column'] = $this->_tpl_vars['i']['columns'];
	if($this->_tpl_vars['i']['index'] < $this->_tpl_vars['i']['count']){
		list($this->_tpl_vars['i']['key'], $this->_tpl_vars['i']['value']) = each($this->_tpl_vars['jieqimodules']);
		$this->_tpl_vars['i']['append'] = 0;
	}else{
		$this->_tpl_vars['i']['key'] = '';
		$this->_tpl_vars['i']['value'] = '';
		$this->_tpl_vars['i']['append'] = 1;
	}
	echo '
leftmenus[\'mod_'.$this->_tpl_vars['i']['key'].'\'] = \'mod_'.$this->_tpl_vars['i']['key'].'\';
';
}
echo '

function showleftmenu(id) {
  for(k in leftmenus) {
    if(leftframe.document.getElementById(leftmenus[k])) {
	  if(leftmenus[k] == id){
	    leftframe.document.getElementById(leftmenus[k]).style.display = \'block\';
		leftframe.document.getElementById(\'sm\' + leftmenus[k].substr(leftmenus[k].indexOf(\'_\'))).style.display = \'block\';
	  }else{
	    leftframe.document.getElementById(leftmenus[k]).style.display = \'none\';
	  }
    }
  }
}

function showmodmenu(){
  for(k in leftmenus) {
    if(leftframe.document.getElementById(leftmenus[k])) {
	  if(leftmenus[k].substr(0,4) == \'mod_\'){
	    leftframe.document.getElementById(leftmenus[k]).style.display = \'block\';
		leftframe.document.getElementById(\'sm_\' + leftmenus[k].substr(4)).style.display = \'none\';
	  }else{
	    leftframe.document.getElementById(leftmenus[k]).style.display = \'none\';
	  }
    }
  }
}

function switchframe(){
  var ls = document.getElementById(\'leftswitch\');
  var al = document.getElementById(\'adminleft\');
  if (ls.className == \'arrow_left\'){
    ls.className = \'arrow_right\';
    al.style.display = \'none\';
  }else{
    ls.className = \'arrow_left\';
    al.style.display = \'\';
  }
}


function adminmenuFix() {
  var sfEls = document.getElementById("admin_menu").getElementsByTagName("li");
  for (var i=0; i<sfEls.length; i++) {
    sfEls[i].onmouseover=function() {
      this.className+=(this.className.length>0? " ": "") + "sfhover";
    }
    sfEls[i].onMouseDown=function() {
      this.className+=(this.className.length>0? " ": "") + "sfhover";
    }
    sfEls[i].onMouseUp=function() {
      this.className+=(this.className.length>0? " ": "") + "sfhover";
    }
    sfEls[i].onmouseout=function() {
      this.className=this.className.replace(new RegExp("( ?|^)sfhover\\\\b"), "");
    }
  }
}
window.onload=adminmenuFix;
</script>
</head>
<body>
<table width="100%" height="100%" cellspacing="0" cellpadding="0" border="0">
<tr><td id="frametop">
	<div id="admintop">
		<div id="top_logo"><img src="'.$this->_tpl_vars['jieqi_url'].'/images/admin/logo.gif" /></div>

		<div id="top_nav">
		<ul id="admin_menu">
			<li><a href="#" onClick="showleftmenu(\'mu_system\'); return false;">系统设置</a></li>
			<li><a href="#" onClick="showleftmenu(\'mu_users\'); return false;">用户管理</a></li>
			<li><a href="#" onClick="showmodmenu();">模块管理<span class="arrow_down"></span></a>
			<ul>
				';
if (empty($this->_tpl_vars['jieqimodules'])) $this->_tpl_vars['jieqimodules'] = array();
elseif (!is_array($this->_tpl_vars['jieqimodules'])) $this->_tpl_vars['jieqimodules'] = (array)$this->_tpl_vars['jieqimodules'];
$this->_tpl_vars['i']=array();
$this->_tpl_vars['i']['columns'] = 1;
$this->_tpl_vars['i']['count'] = count($this->_tpl_vars['jieqimodules']);
$this->_tpl_vars['i']['addrows'] = count($this->_tpl_vars['jieqimodules']) % $this->_tpl_vars['i']['columns'] == 0 ? 0 : $this->_tpl_vars['i']['columns'] - count($this->_tpl_vars['jieqimodules']) % $this->_tpl_vars['i']['columns'];
$this->_tpl_vars['i']['loops'] = $this->_tpl_vars['i']['count'] + $this->_tpl_vars['i']['addrows'];
reset($this->_tpl_vars['jieqimodules']);
for($this->_tpl_vars['i']['index'] = 0; $this->_tpl_vars['i']['index'] < $this->_tpl_vars['i']['loops']; $this->_tpl_vars['i']['index']++){
	$this->_tpl_vars['i']['order'] = $this->_tpl_vars['i']['index'] + 1;
	$this->_tpl_vars['i']['row'] = ceil($this->_tpl_vars['i']['order'] / $this->_tpl_vars['i']['columns']);
	$this->_tpl_vars['i']['column'] = $this->_tpl_vars['i']['order'] % $this->_tpl_vars['i']['columns'];
	if($this->_tpl_vars['i']['column'] == 0) $this->_tpl_vars['i']['column'] = $this->_tpl_vars['i']['columns'];
	if($this->_tpl_vars['i']['index'] < $this->_tpl_vars['i']['count']){
		list($this->_tpl_vars['i']['key'], $this->_tpl_vars['i']['value']) = each($this->_tpl_vars['jieqimodules']);
		$this->_tpl_vars['i']['append'] = 0;
	}else{
		$this->_tpl_vars['i']['key'] = '';
		$this->_tpl_vars['i']['value'] = '';
		$this->_tpl_vars['i']['append'] = 1;
	}
	echo '<li><a href="#" onClick="showleftmenu(\'mod_'.$this->_tpl_vars['i']['key'].'\'); return false;">'.$this->_tpl_vars['jieqimodules'][$this->_tpl_vars['i']['key']]['caption'].'</a></li>';
}
echo '
			</ul>
			</li>
			<li><a href="#" onClick="showleftmenu(\'mu_tools\'); return false;">系统工具</a></li>
			<li><a href="#" onClick="showleftmenu(\'mu_database\'); return false;">数据维护</a></li>
			<li><a href="#" onClick="return false;">帮助<span class="arrow_down"></span></a>
			<ul>
				<li><a href="http://www.jieqi.com/" target="_blank">官方网站</a></li>
				<li><a href="'.$this->_tpl_vars['jieqi_url'].'/admin/faq.php" target="mainframe">使用技巧</a></li>
				<li><a href="'.$this->_tpl_vars['jieqi_url'].'/admin/license.php" target="mainframe">授权信息</a></li>
			</ul>
			</li>
		</ul>			
		</div>

		<div id="top_link">
			<a href="http://www.jieqi.com" target="_blank"> 官方网站</a> | <a href="http://help.jieqi.com"  target="_blank">帮助中心</a> | <a href="'.$this->_tpl_vars['jieqi_url'].'/admin/license.php" target="mainframe">授权信息</a>
			<br />
			<a href="'.$this->_tpl_vars['jieqi_url'].'/">JIEQI CMS '.$this->_tpl_vars['jieqi_version'].' '.$this->_tpl_vars['jieqi_vtype'].'</a> - '.$this->_tpl_vars['username'].' <a href="'.$this->_tpl_vars['jieqi_url'].'/logout.php" target="_top">[退出]</a>
		</div>

		<div class="cb"></div>
	</div>
</td></tr>
<tr valign="top"><td id="framemiddle">
<table height="100%"  width="100%" cellspacing="0" cellpadding="0" border="0" id="adminframe">
  <tr>
	<td valign="top" id="adminleft" name="adminleft"><iframe frameborder="0" id="leftframe" name="leftframe" scrolling="auto" src="'.$this->_tpl_vars['jieqi_adminleft'].'" marginwidth="0" marginheight="0"></iframe></td>
	<td valign="middle" id="adminmiddle" name="adminmiddle" onClick="switchframe()"><span id="leftswitch" class="arrow_left"></span></td>
	<td valign="top" id="adminright" name="adminright">
    <iframe frameborder="0" id="mainframe" name="mainframe" scrolling="auto" src="'.$this->_tpl_vars['jieqi_adminmain'].'" marginwidth="0" marginheight="0"></iframe>
	</td>
  </tr>
</table>
</td></tr>
</table>
</body>
</html>';
?>