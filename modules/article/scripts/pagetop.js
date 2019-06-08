//改变阅读背景、字体大小和颜色的javascript
var ReadSet = {
	bgcolor : ["#e7f4fe", "#ffffed", "#efefef", "#fcefff", "#ffffff", "#eefaee"],
	bgcname : ["淡蓝海洋", "明黄淡雅", "灰色世界", "红粉世家", "白雪天地", "绿意春色"],
	bgcvalue : "#ffffff",
	fontcolor : ["#000000", "#ff0000", "#008000", "#ffc0cb", "#0000ff", "#ffffff"],
	fontcname : ["经典黑色", "红色激情", "春意盎然", "红粉世家", "蓝色海洋", "白色天使"],
	fontcvalue : "#000000",
	fontsize : ["12px", "14px", "16px", "20px", "28px"],
	fontsname : ["很小", "较小", "中等", "较大", "很大"],
	fontsvalue : "16px",
	pageid : "apage",
	textid : "atext",
	contentid : "acontent",
	SetBgcolor : function(color){
		if(document.getElementById(this.pageid)) document.getElementById(this.pageid).style.backgroundColor = color;
		else document.getElementById(this.contentid).style.backgroundColor = color;
		if(this.bgcvalue != color) this.SetCookies("bgcolor",color);
		this.bgcvalue = color;
	},
	SetFontcolor : function(color){
		if(document.getElementById(this.textid)) document.getElementById(this.textid).style.color = color;
		else document.getElementById(this.contentid).style.color = color;
		if(this.fontcvalue != color) this.SetCookies("fontcolor",color);
		this.fontcvalue = color;
	},
	SetFontsize : function(size){
		document.getElementById(this.contentid).style.fontSize = size;
		if(this.fontsvalue != size) this.SetCookies("fontsize",size);
		this.fontsvalue = size;
	},
	LoadCSS : function(){
			var style = "";
			style += ".readSet{padding:3px;clear:both;line-height:20px;text-align:center;margin:auto;}\n";
			style += ".readSet .rc{color:#333333;padding-left:20px;}\n";
			style += ".readSet a.ra{border:1px solid #cccccc;display:inline-block;width:16px;height:16px;margin-left:6px;overflow:hidden;vertical-align: middle;}\n";
			style += ".readSet .rf{}\n";
			style += ".readSet .rt{padding:0px 5px;}\n";
			
			if(document.createStyleSheet){
				var sheet = document.createStyleSheet();
				sheet.cssText = style;
			}else{
				var sheet = document.createElement("style");
				sheet.type = "text/css";
				sheet.innerHTML = style;
				document.getElementsByTagName("HEAD").item(0).appendChild(sheet);
			}
	},
	Show : function(){
		var output;
		output = '<div class="readSet">';
		output += '<span class="rc">背景色：</span>';
		for(i=0; i<this.bgcolor.length; i++){
			output += '<a style="background-color: '+this.bgcolor[i]+'" class="ra" title="'+this.bgcname[i]+'" onclick="ReadSet.SetBgcolor(\''+this.bgcolor[i]+'\')" href="javascript:;"></a>';
		}
		output += '<span class="rc">前景色：</span>';
		for(i=0; i<this.fontcolor.length; i++){
			output += '<a style="background-color: '+this.fontcolor[i]+'" class="ra" title="'+this.fontcname[i]+'" onclick="ReadSet.SetFontcolor(\''+this.fontcolor[i]+'\')" href="javascript:;"></a>';
		}
		output += '<span class="rc">字体：</span><span class="rf">[';
		for(i=0; i<this.fontsize.length; i++){
			output += '<a class="rt" onclick="ReadSet.SetFontsize(\''+this.fontsize[i]+'\')" href="javascript:;">'+this.fontsname[i]+'</a>';
		}
		output += ']</span>';
		output += '<div style="font-size:0px;clear:both;"></div></div>';
		document.write(output);
	},
	SetCookies : function(cookieName,cookieValue, expirehours){
		var today = new Date();
		var expire = new Date();
		expire.setTime(today.getTime() + 3600000 * 356 * 24);
		document.cookie = cookieName+'='+escape(cookieValue)+ ';expires='+expire.toGMTString()+'; path=/';
	},
	ReadCookies : function(cookieName){
		var theCookie=''+document.cookie;
		var ind=theCookie.indexOf(cookieName);
		if (ind==-1 || cookieName=='') return ''; 
		var ind1=theCookie.indexOf(';',ind);
		if (ind1==-1) ind1=theCookie.length;
		return unescape(theCookie.substring(ind+cookieName.length+1,ind1));
	},
	SaveSet : function(){
		this.SetCookies("bgcolor",this.bgcvalue);
		this.SetCookies("fontcolor",this.fontcvalue);
		this.SetCookies("fontsize",this.fontsvalue);
	},
	LoadSet : function(){
		tmpstr = this.ReadCookies("bgcolor");
		if(tmpstr != "") this.bgcvalue = tmpstr;
		this.SetBgcolor(this.bgcvalue);
		tmpstr = this.ReadCookies("fontcolor");
		if(tmpstr != "") this.fontcvalue = tmpstr;
		this.SetFontcolor(this.fontcvalue);
		tmpstr = this.ReadCookies("fontsize");
		if(tmpstr != "") this.fontsvalue = tmpstr;
		this.SetFontsize(this.fontsvalue);
	}
}

ReadSet.LoadCSS();
ReadSet.Show();
function LoadReadSet(){
	ReadSet.LoadSet();
}
if (window.attachEvent){
	window.attachEvent('onload',LoadReadSet);
}else{
	window.addEventListener('load',LoadReadSet,false);
} 