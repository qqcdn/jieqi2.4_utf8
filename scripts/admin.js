//下拉菜单
function menufix() {
	var sfEls=document.getElementById("jieqi_menu");
	if(sfEls) sfEls=sfEls.getElementsByTagName("li");
	if(sfEls){
		for(var i=0; i<sfEls.length; i++){
			sfEls[i].onmouseover=function(){ this.className="sfhover"; }
			sfEls[i].onmouseout=function(){ this.className="nohover"; }
			sfEls[i].onmousedown=function(){ this.className="sfhover"; }
			sfEls[i].onmouseup=function(){ this.className="sfhover"; }
		}
	}
}
if (document.all){
	window.attachEvent('onload',menufix);
}else{
	window.addEventListener('load',menufix,false);
}

//浮动菜单 menu-菜单对象id，box-浮动框对象id，参数3-right靠右对齐，默认靠左，参数4-top显示在上方，默认下方
function menubox(menu, box) {
	menu = $_(menu);
	box = $_(box);
	if (box.style.display == 'none') {
		box.style.display = 'block';
		box.style.position = 'absolute';
	} else {
		box.style.display = 'none';
		return;
	}
	var pos = menu.getPosition();
	if (arguments.length > 2 && arguments[2] == 'right') box.style.left = (pos.x + menu.offsetWidth - box.offsetWidth) + 'px';
	else box.style.left = pos.x + 'px';
	if (arguments.length > 3 && arguments[3] == 'top') box.style.top = (pos.y - box.offsetHeight + 1) + 'px';
	else box.style.top = (pos.y + menu.offsetHeight - 1) + 'px';
	return;
}

//浮动提示框（显示title内容）
var tipname = "tips";
var tiptag = "a,label,div,img,span"; 
var tipx = 0;
var tipy = 15;
var tipobj = null;

function tipinit() {
	var tipNameSpaceURI = "http://www.w3.org/1999/xhtml";
	if(!tipContainerID){ var tipContainerID = tipname;}
	var tipContainer = document.getElementById(tipContainerID);

	if(!tipContainer) {
	  tipContainer = document.createElementNS ? document.createElementNS(tipNameSpaceURI, "div") : document.createElement("div");
		tipContainer.setAttribute("id", tipContainerID);
	  document.getElementsByTagName("body").item(0).appendChild(tipContainer);
	}

	if (!document.getElementById) return;
	tipobj = document.getElementById(tipname);
	if(tipobj) document.onmousemove = function(evt){tipmove(evt)};

	var a, sTitle, elements;
	
	var elementList = tiptag.split(",");
	for(var j = 0; j < elementList.length; j++)
	{	
		elements = document.getElementsByTagName(elementList[j]);
		if(elements)
		{
			for (var i = 0; i < elements.length; i ++)
			{
				a = elements[i];
				sTitle = a.getAttribute("title");				
				if(sTitle && typeof(a.onmouseover) != "function" && typeof(a.onmouseout) != "function")
				{
					a.setAttribute("tiptitle", sTitle);
					a.removeAttribute("title");
					a.removeAttribute("alt");
					a.onmouseover = function() {tipshow(this.getAttribute('tiptitle'))};
					a.onmouseout = function() {tiphide()};
				}
			}
		}
	}
}

function tipmove(evt) {
	if(tipobj.style.display == "block"){
		var x=0, y=0;
		if (document.all) {
			x = (document.documentElement && document.documentElement.scrollLeft) ? document.documentElement.scrollLeft : document.body.scrollLeft;
			y = (document.documentElement && document.documentElement.scrollTop) ? document.documentElement.scrollTop : document.body.scrollTop;
			x += window.event.clientX;
			y += window.event.clientY;
		} else {
			x = evt.pageX;
			y = evt.pageY;
		}
		tipobj.style.left = (x + tipx) + "px";
		tipobj.style.top = (y + tipy) + "px";
	}
}

function tipshow(text) {
	if (!tipobj) return;
	tipobj.innerHTML = text;
	tipobj.style.display = "block";
}

function tiphide() {
	if (!tipobj) return;
	tipobj.innerHTML = "";
	tipobj.style.display = "none";
}

if (document.all){
	window.attachEvent('onload',tipinit);
}else{
	window.addEventListener('load',tipinit,false);
} 

//tab效果
function selecttab(obj) {
	var i = 0;
	var n = 0;
	var ul = obj.tagName.toLowerCase() == 'li' ? obj.parentNode : obj.parentNode.parentNode;
	var tabs = ul.getElementsByTagName('li');
	for (i = 0; i < tabs.length; i++) {
		tmp = obj.tagName.toLowerCase() == 'li' ? tabs[i] : tabs[i].getElementsByTagName('a')[0];
		if (tmp == obj) {
			tmp.className = 'selected';
			n = i;
		} else {
			tmp.className = '';
		}
	}
	var tabdiv = ul.parentNode;
	if(typeof tabdiv == 'undefined' || tabdiv.tagName.toLowerCase() != 'div') return true;
	var tabchilds = tabdiv.parentNode.childNodes;
	if(typeof tabchilds == 'undefined' || tabchilds.length <= 1) return true;
	
	var tabcontent;
	for (i = tabchilds.length - 1; i >= 0; i--) {
		if (typeof tabchilds[i].tagName != 'undefined' && tabchilds[i].tagName.toLowerCase() == 'div' && tabchilds[i] != tabdiv) {
			tabcontent = tabchilds[i];
			break;
		}
	}
	if (typeof tabcontent.tagName == 'undefined' || tabcontent.tagName.toLowerCase() != 'div')  return true;
	var contents = tabcontent.childNodes;
	var k = 0;
	for (i = 0; i < contents.length; i++) {
		if (typeof contents[i].tagName != 'undefined' && contents[i].tagName.toLowerCase() == 'div') {
			contents[i].style.display = k == n ? 'block': 'none';
			k++;
		}
	}
	return true;
}

//切换下一个tab
function nexttab(obj) {
	var i = 0;
	var n = 0;
	if (typeof obj == 'string') obj = document.getElementById(obj);
	var tabs = obj.getElementsByTagName('li');
	for (i = 0; i < tabs.length; i++) {
		tmp = tabs[i].getElementsByTagName('a')[0];
		if (tmp.className == 'selected') {
			if (arguments.length > 1 && arguments[1] == true) n = i > 0 ? i - 1 : tabs.length - 1;
			else n = i >= tabs.length - 1 ? 0 : i + 1;
			break;
		}
	}
	tmp = tabs[n].getElementsByTagName('a')[0];
	selecttab(tmp);
}

//tab 轮换
function slidetab(obj) {
	var i = 0;
	var n = 0;
	var time = 5000;
	if (arguments[1]) time = arguments[1];
	if (time == 0) return;
	if (typeof obj == 'string') obj = document.getElementById(obj);
	var tabs = obj.getElementsByTagName('li');
	for (i = 0; i < tabs.length; i++) {
		tmp = tabs[i].getElementsByTagName('a')[0];
		if (tmp.className == 'selected') {
			n = i + 1;
			if (n >= tabs.length) n = 0;
			break;
		}
	}
	tmp = tabs[n].getElementsByTagName('a')[0];
	selecttab(tmp);
	setTimeout(function() {
		slidetab(obj, time);
	},
	time);
}

//选择标签到文本框
function selecttag(txt, tag){
	txt = $_(txt);
	tag = $_(tag);
	var ts = tag.innerHTML.trim();
	var re = new RegExp('(^| )' + ts + '($| )', 'g');
	if(tag.className != 'taguse'){
		tag.className = 'taguse';
		if(!re.test(txt.value)){
		  if(txt.value != '') txt.value += ' ';
		  txt.value += ts;
		}
	}else{
		tag.className = '';
		txt.value = txt.value.replace(re, ' ');
	}
	txt.value = txt.value.replace(/\s{2,}/g, ' ').replace(/^\s+/g, '');
}

//单双行切换
function sheetrow(){
	var sheets = getByClass('sheet', document, 'table');
	for(var i = 0; i < sheets.length; i++){
		var trs = sheets[i].getElementsByTagName('tr'); 
		for(var j = 0; j < trs.length; j++){
			trs[j].className = (j % 2 == 1) ? 'even' : 'odd';
		}
	}
}
addEvent(window, 'load', sheetrow);