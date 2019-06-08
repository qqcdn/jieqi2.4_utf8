//***************************
//显示分享到图标
// 用法一(直接显示div)：shareTo(url, title, content, tag)
//***************************
//解析网站url
var siteUrl = '';
var scripts = document.getElementsByTagName('script');
for (var i=0; i<scripts.length; i++) {
	if(scripts[i].src.indexOf('/scripts/share') != -1){
		siteUrl = scripts[i].src.substr(0, scripts[i].src.indexOf('/scripts/share'));
	}
}
//分享的网站设置
var shareSites = [
['QQ书签', '\'http://shuqian.qq.com/post?from=3&title=\'+encodeURIComponent(\'<{$title}>\')+\'&uri=\'+encodeURIComponent(\'<{$url}>\')+\'&jumpback=2&noui=1\'', 'width=930,height=470,left=50,top=50,toolbar=no,menubar=no,location=no,scrollbars=yes,status=yes,resizable=yes', 'qqs.gif'],
['百度收藏', '\'http://cang.baidu.com/do/add?it=\'+encodeURIComponent(\'<{$title}>\'.substring(0,76))+\'&iu=\'+encodeURIComponent(\'<{$url}>\')+\'&fr=ien#nw=1\'', 'scrollbars=no,width=600,height=450,left=75,top=20,status=no,resizable=yes', 'baiduc.gif'],
['谷歌书签', '\'http://www.google.com/bookmarks/mark?op=add&bkmk=\'+encodeURIComponent(\'<{$url}>\')+\'&title=\'+encodeURIComponent(\'<{$title}>\')', 'width=700,height=500,left=50,top=50,toolbar=no,menubar=no,location=no,scrollbars=yes,status=yes,resizable=yes', 'googleb.gif'],
['豆瓣', '\'http://www.douban.com/recommend/?url=\'+encodeURIComponent(\'<{$url}>\')+\'&title=\'+encodeURIComponent(\'<{$title}>\')+\'&sel=\'+encodeURIComponent(\'<{$desc}>\')+\'&v=1\'', 'width=450,height=350,left=50,top=50,toolbar=no,menubar=no,location=no,scrollbars=yes,status=yes,resizable=yes', 'douban.gif'],
['开心网', '\'http://www.kaixin001.com/repaste/share.php?rtitle=\'+encodeURIComponent(\'<{$title}>\'.substring(0,76))+\'&rurl=\'+encodeURIComponent(\'<{$url}>\')+\'&rcontent=\'+encodeURIComponent(\'<{$desc}>\'.substring(0,76))', 'width=600,height=450,left=50,top=50,toolbar=no,menubar=no,location=no,scrollbars=yes,status=yes,resizable=yes', 'kaixin001.gif'],
['人人网', '\'http://share.renren.com/share/buttonshare.do?link=\'+encodeURIComponent(\'<{$url}>\')+\'&title=\'+encodeURIComponent(\'<{$title}>\')', 'width=626,height=436,left=50,top=50,toolbar=no,menubar=no,location=no,scrollbars=yes,status=yes,resizable=yes', 'renren.gif'],
['QQ空间', '\'http://sns.qzone.qq.com/cgi-bin/qzshare/cgi_qzshare_onekey?url=\'+encodeURIComponent(\'<{$url}>\')', 'width=700,height=400,left=50,top=50,toolbar=no,menubar=no,location=no,scrollbars=yes,status=yes,resizable=yes', 'qqz.gif'],
['新浪微博', '\'http://v.t.sina.com.cn/share/share.php?title=\'+encodeURIComponent(\'<{$title}>\')+\'&url=\'+encodeURIComponent(\'<{$url}>\')+\'&source=bookmark\'', 'width=600,height=450,left=50,top=50,toolbar=no,menubar=no,location=no,scrollbars=yes,status=yes,resizable=yes', 'sinat.gif'],
['腾讯微博', '\'http://v.t.qq.com/share/share.php?title=\'+encodeURIComponent(\'<{$title}>\')+\'&url=\'+encodeURIComponent(\'<{$url}>\')+\'&source=bookmark\'', 'width=700,height=450,left=50,top=50,toolbar=no,menubar=no,location=no,scrollbars=yes,status=yes,resizable=yes', 'qqt.gif']
];
//是否已载入CSS
var loadedSCSS = false;


//显示分享到的完整代码
//参数一：提交的url地址，留空表示当前网址
//参数二：提交的标题，留空表示当前网页标题
//参数三：提交的内容描述，默认为空
//参数四：提交的tag标签，默认为空
function shareTo(){
	if(arguments.length > 0 && arguments[0].length > 0) url = arguments[0];
	else url = document.location.href;
	if(arguments.length > 1 && arguments[1].length > 0) title = arguments[1];
	else title = document.title;
	if(arguments.length > 2 && arguments[2].length > 0) desc = arguments[2];
	else desc = '';
	if(arguments.length > 3 && arguments[3].length > 0) tag = arguments[3];
	else tag = '';
	
	loadShareCSS();
	var ret = shareLink(url, title, desc, tag);
	ret = '<div class="shareTo"><p>分享到：</p>' + ret + '</div>';
	document.write(ret);
	return true;
}

//显示分享到图标代码
//参数一：提交的url地址，留空表示当前网址
//参数二：提交的标题，留空表示当前网页标题
//参数三：提交的内容描述，默认为空
//参数四：提交的tag标签，默认为空
function shareIcon(){
	if(arguments.length > 0 && arguments[0].length > 0) url = arguments[0];
	else url = document.location.href;
	if(arguments.length > 1 && arguments[1].length > 0) title = arguments[1];
	else title = document.title;
	if(arguments.length > 2 && arguments[2].length > 0) desc = arguments[2];
	else desc = '';
	if(arguments.length > 3 && arguments[3].length > 0) tag = arguments[3];
	else tag = '';
	
	var ret = shareLink(url, title, desc, tag);
	document.write(ret);
	return true;
}

//获得分享到图标显示代码
function shareLink(){
	if(arguments.length > 0 && arguments[0].length > 0) url = arguments[0];
	else url = document.location.href;
	if(arguments.length > 1 && arguments[1].length > 0) title = arguments[1];
	else title = document.title;
	if(arguments.length > 2 && arguments[2].length > 0) desc = arguments[2];
	else desc = '';
	if(arguments.length > 3 && arguments[3].length > 0) tag = arguments[3];
	else tag = '';

	var sn = shareSites.length;
	var surl = '';
	var icon = '';
	var name = '';
	var ret = '';
	for(var i = 0; i < sn; i++){
		surl = shareSites[i][1].replace('<{$url}>', url).replace('<{$title}>', title).replace('<{$desc}>', desc).replace('<{$tag}>', tag);
		icon = siteUrl + '/images/share/' + shareSites[i][3];
		name =  '分享到' + shareSites[i][0];
		ret += '<a rel="nofollow" href="javascript:window.open(' + surl + ',\'\',\'' + shareSites[i][2] + '\');void(0);"><img src="' + icon + '" alt="' + name + '" title="' + name + '" align="absMiddle" border="0" /></a>';
	}
	return ret;
}

//载入分享到的CSS
function loadShareCSS(){
	if(!loadedSCSS){
		var style = "";
		style +=".shareTo{float:left;height:20px;padding:0px;margin:0px;font-size:0px;}\n";
		style +=".shareTo p{float:left;font-size:12px;font-weight:bold;line-height:20px;}\n";
		style +=".shareTo img{width:16px;height:16px;border:0;margin-right:2px;vertical-align:middle;}\n";
		if (document.all){
			var oStyle=document.styleSheets[0];
			var a=style.split("\n");	
			for(var i=0;i<a.length;i++){
				if(a[i]=="") continue;
				var ad=a[i].replace(/([\s\S]*)\{([\s\S]*)\}/,"$1|$2").split("|");
				oStyle.addRule(ad[0],ad[1]);
			}
		}else{
			var styleobj = document.createElement('style');
			styleobj.type = 'text/css';
			styleobj.innerHTML=style;
			document.getElementsByTagName('HEAD').item(0).appendChild(styleobj);
		}
		loadedSCSS = true;
	}
	return true;
}