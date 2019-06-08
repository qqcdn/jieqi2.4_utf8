var rate_image_url = '/images/rate/ratestar.gif'; //星星图片相对地址
var rate_star_width = 20; //单颗星宽度（更换图片后修改这里）
var rate_star_height = 20; //单颗星高度（更换图片后修改这里）
var rate_font_text = 16; //提示文字大小
var rate_font_num = 20; //评分值文字大小
var rate_star_max = 10; //默认最多几颗星，可在showRating调用参数里变更
var rate_root_url = ''; //网站根路径URL，默认可以留空，程序自动判断
var rate_css_load = false; //CSS载入标志，自动判断
var rate_url_check = false; //检查图片URL标志，自动判断

//显示星级评分效果(点击直接评分)
//参数1：整数，最高几分（如 10）
//参数2：小数，当前几分（如 8.5）
//参数3：字符串，点击调用的函数名（默认函数名是“rating”）
//参数4：字符串，函数调用的附加参数（默认为空）
//例子： showRating(10, 8.5, 'myrating', 'article');  输出10颗星的评分效果，当点击第五颗星，会调用函数 myrating(5, 'article');
function showRating(){
	checkRootURl();

	var maxscore = 10;
	var nowscore = 0;
	var funname = 'rating';
	var funvars = '';
	if(arguments.length >= 1) maxscore = parseInt(arguments[0]);
	if(arguments.length >= 2) nowscore = parseFloat(arguments[1]);
	if(arguments.length >= 3) funname = arguments[2];
	if(arguments.length >= 4) funvars = arguments[3];
	if(maxscore < 1) maxscore = 1;
	if(maxscore > rate_star_max) rate_star_max = maxscore;
	if(funvars != '') funvars = ", " + funvars;
	var ratewidth = maxscore * rate_star_width;
	var spercent = nowscore * 100 / maxscore;
	
	loadRateCSS();

	var html = "<ul style=\"width: " + ratewidth + "px;\" class=\"rateunit\"><li style=\"width:" + spercent + "%;\" class=\"rpercent\"></li>";
	for(var i=1; i<=maxscore; i++){
		html += "<li><a href=\"javascript:;\" onclick=\"" + funname + "(" + i + "" + funvars + ");\" style=\"position: absolute;\" class=\"r" + i + "\" title=\" (" + i + ") \">" + i + "</a></li>";
	}
	html += "</ul>";
	document.write(html);
}

//显示星级评分效果(点击选中，赋值给input hidden)
//参数1：整数，最高几分（如 10）
//参数2：小数，默认几分（如 5）
//参数3：字符串，赋值给input名
//例子： showRateSelect(10, 5, 'rate');
function showRateSelect(){
	checkRootURl();

	var maxscore = 10;
	var nowscore = 0;
	if(arguments.length >= 1) maxscore = parseInt(arguments[0]);
	if(arguments.length >= 2) nowscore = parseFloat(arguments[1]);
	if(arguments.length >= 3) inputname = arguments[2];

	if(maxscore < 1) maxscore = 1;
	if(maxscore > rate_star_max) rate_star_max = maxscore;
	var ratewidth = maxscore * rate_star_width;
	var spercent = nowscore * 100 / maxscore;
	
	loadRateCSS();

	var html = "<ul style=\"width: " + ratewidth + "px;\" class=\"rateunit\"><li style=\"width:" + spercent + "%;\" class=\"rpercent\"></li>";
	for(var i=1; i<=maxscore; i++){
		html += "<li><a href=\"javascript:;\" onclick=\"rateSelectClick(this, " + i + "," + maxscore + ");\" style=\"position: absolute;\" class=\"r" + i + "\" title=\" (" + i + ") \">" + i + "</a></li>";
	}
	html += "</ul><input type=\"hidden\" name=\""+inputname+"\" value=\""+nowscore+"\" />";
	document.write(html);
}

//上面函数打分后改变input值
function rateSelectClick(obj, nowscore, maxscore){
	obj.parentNode.parentNode.childNodes[0].style.width = (nowscore * 100 / maxscore) + "%";
	obj.parentNode.parentNode.nextSibling.value = nowscore;
}


//显示星级评分结果，用于评分完成，不允许继续评分的情况
//参数1：整数，最高几分（如 10）
//参数2：小数，当前几分（如 8.5）
function showRateStar(){
	checkRootURl();

	var maxscore = 10;
	var nowscore = 0;
	var funname = 'rating';
	var funvars = '';
	if(arguments.length >= 1) maxscore = parseInt(arguments[0]);
	if(arguments.length >= 2) nowscore = parseFloat(arguments[1]);
	if(maxscore < 1) maxscore = 1;
	if(maxscore > rate_star_max) rate_star_max = maxscore;
	var ratewidth = maxscore * rate_star_width;
	var spercent = nowscore * 100 / maxscore;
	
	loadRateCSS();

	var html = "<ul style=\"width: " + ratewidth + "px;\" class=\"rateunit\"><li style=\"width:" + spercent + "%;\" class=\"rpercent\"></li>";
	html += "</ul>";
	document.write(html);
}

//检查网站根URL
function checkRootURl(){
	if (rate_url_check) return true;
	var scripts = document.getElementsByTagName('script');
	for (var i=0; i<scripts.length; i++) {
		if(scripts[i].src.indexOf('/scripts/rating') != -1){
			rate_root_url = scripts[i].src.substr(0, scripts[i].src.indexOf('/scripts/rating'));
			break;
		}
	}
	if(rate_root_url != '') rate_image_url = rate_root_url + rate_image_url;
	rate_url_check = true;
	return true;
}

//载入CSS
function loadRateCSS(){
	if (rate_css_load) return true;
	var divheight = rate_star_height + 5;
	var fonttxt = rate_font_text;
	var fontnum = rate_font_num;
	var style = "";
	style +=".ratediv{font-size:" + fonttxt + "px; height:" + divheight + "px;line-height:" + divheight + "px;text-align:left;}\n";
	style +=".ratenum{font-size:" + fontnum + "px; font-weight:bold;color: #ff5a00;margin-left:5px;}\n";
	style +=".rateblock {display:block; text-align:left; float:left;}\n";
	style +=".rateunit {list-style:none; margin: 0px; padding:0px; height: " + rate_star_height + "px; position: relative; background: url('" + rate_image_url + "') top left repeat-x; font-size:0px; line-height:0px;}\n";
	style +=".rateunit li{text-indent: -4500px; padding:0px; margin:0px; float: left; height: " + rate_star_height + "px; font-size:0px; line-height:0px;}\n";
	style +=".rateunit li a {outline: none; display:block; width:" + rate_star_width + "px; height: " + rate_star_height + "px; text-decoration: none; text-indent: -4500px; z-index: 20; position: absolute; padding: 0px; font-size:0px;}\n";
	style +=".rateunit li a:hover{position:static;top:0px;left:0px;background: url('" + rate_image_url + "') left center; z-index: 10; left: 0px; height: " + rate_star_height + "px;}\n";
	var starlen = 0;
	for(var i=1; i<=rate_star_max; i++){
		style +=".rateunit a.r" + i + "{left: " + starlen + "px;}\n";
		starlen += rate_star_width;
		style +=".rateunit a.r" + i + ":hover{width:" + starlen + "px;}\n";
	}
	style +=".rateunit li.rpercent{background: url('" + rate_image_url + "') left bottom; position: absolute; height: " + rate_star_height + "px; display: block; text-indent: -4500px; z-index: 1; font-size:0px; line-height:0px;}\n";

	if (document.all){
		var oStyle=document.styleSheets[0];
		var a=style.split("\n");	
		for(var i=0;i<a.length;i++){
			if(a[i]=="")continue;
			var ad=a[i].replace(/([\s\S]*)\{([\s\S]*)\}/,"$1|$2").split("|");
			oStyle.addRule(ad[0],ad[1]);
		}
	}else{
		var styleobj = document.createElement('style');
		styleobj.type = 'text/css';
		styleobj.innerHTML=style;
		document.getElementsByTagName('HEAD').item(0).appendChild(styleobj);
	}
	rate_css_load = true;
	return true;
}