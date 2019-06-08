//根据id寻找物件
function jieqiGetElementById(id){
	if (document.getElementById) {
		return (document.getElementById(id));
	} else if (document.all) {
		return (document.all[id]);
	} else {
		if ((navigator.appname.indexOf("Netscape") != -1) && parseInt(navigator.appversion == 4)) {
			return (document.layers[id]);
		}
	}
}

//记录插入位置
function jieqiSavePosition(id)
{
	var textareaDom = jieqiGetElementById(id);
	if (textareaDom.createTextRange) {
		textareaDom.caretPos = document.selection.createRange().duplicate();
	}
}

//插入文本
function jieqiInsertText(obj, text)
{
	if (obj.createTextRange && obj.caretPos){
  		var caretPos = obj.caretPos;
		caretPos.text = caretPos.text.charAt(caretPos.text.length - 1) 
== ' ' ? text + ' ' : text;  
	} else if (obj.getSelection && obj.caretPos){
		var caretPos = obj.caretPos;
		caretPos.text = caretPos.text.charat(caretPos.text.length - 1)  
== ' ' ? text + ' ' : text;
	} else {
		obj.value = obj.value + text;
  	}
}

//插入表情
function jieqiCodeSmile(id, code){
	var obj=jieqiGetElementById(id);
	jieqiInsertText(obj, code)
	obj.focus();
}

//文字代码，字体加粗、变斜等
function jieqiCodeText(id,code){
	if (document.selection && document.selection.type == "Text") {
		var range = document.selection.createRange();
		range.text = "["+code+"]" + range.text + "[/"+code+"]";
    } else {
        var obj=jieqiGetElementById(id);
	    jieqiInsertText(obj, "["+code+"][/"+code+"]");
	    obj.focus();
    }
}

//插入文字样式，大小、颜色、字体
function jieqiCodeStyle(id,code,val) {
    if (document.selection && document.selection.type == "Text") {
		var range = document.selection.createRange();
		range.text = "["+code+"="+val+"]"+range.text+"[/"+code+"]";
    } else{
		var obj=jieqiGetElementById(id);
	    jieqiInsertText(obj, "["+code+"="+val+"][/"+code+"]");
	    obj.focus();
    }
}

//提示插入超链
function jieqiCodeUrl(id){
	var obj = jieqiGetElementById(id);
	var text = prompt("请输入链接显示的文字(如果为空则直接显示链接地址)", "");
	var text2 = prompt("请输入链接的 URL", "http://");
	if ( text2 != null && text2 != "" ) {
        if ( text == null || text == "" ) {
			var result = "[url=" + text2 + "]" + text2 + "[/url]";
		}else{
			var result = "[url=" + text2 + "]" + text + "[/url]";
		}
		jieqiInsertText(obj, result);
	}
	obj.focus();
}

//提示插入图片
function jieqiCodeImg(id){
	var text = prompt("请输入图片的 URL", "http://");
	var obj = jieqiGetElementById(id);
	if ( text != null && text != "" ) {
		var text2 = prompt("请输入图片对齐方式\n“l”表示靠左，“r”表示靠右，留空表示默认", "");
		while ( ( text2 != "" ) && ( text2 != "r" ) && ( text2 != "R" ) && ( text2 != "l" ) && ( text2 != "L" ) && ( text2 != null ) ) {
			text2 = prompt("请输入图片对齐方式\n“l”表示靠左，“r”表示靠右，留空表示默认", "");
		}
		if ( text2 == "l" || text2 == "L" ) {
			text2 = " align=left";
		} else if ( text2 == "r" || text2 == "R" ) {
			text2 = " align=right";
		} else {
			text2 = "";
		}
		var result = "[img" + text2 + "]" + text + "[/img]";
		jieqiInsertText(obj, result);
	}
	obj.focus();
}
//提示插入Email
function jieqiCodeEmail(id){
	var text = prompt("请输入Email地址", "");
	var obj = jieqiGetElementById(id);
	if ( text != null && text != "" ) {
		var result = "[email]" + text + "[/email]";
		jieqiInsertText(obj, result);
	}
	obj.focus();
}
//提示插入引文
function jieqiCodeQuote(id){
	var text = prompt("请输入引文内容", "");
	var obj = jieqiGetElementById(id);
	if ( text != null && text != "" ) {
		var pos = text.indexOf(unescape('%00'));
		if(0 < pos){
			text = text.substr(0,pos);
		}
		var result = "[quote]" + text + "[/quote]";
		jieqiInsertText(obj, result);
	}
	obj.focus();
}
//提示插入代码
function jieqiCodeCode(id){
	var text = prompt("请输入代码内容", "");
	var obj = jieqiGetElementById(id);
	if ( text != null && text != "" ) {
		var result = "[code]" + text + "[/code]";
		jieqiInsertText(obj, result);
	}
	obj.focus();
}

//提交窗口
function jieqiPostForm(event)
{
	if((event.ctrlKey && event.keyCode == 13)||(event.altKey && event.keyCode == 83))
	{
		this.document.frmpost.submit();
	}
}

function openWithSelfMain(url,name,width,height) {
	var options = "width=" + width + ",height=" + height + "toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=yes,copyhistory=no";

	new_window = window.open(url, name, options);
	window.self.name = "main";
	new_window.focus();
}

function setElementColor(id, color){
	jieqiGetElementById(id).style.color = "#" + color;
}

function setElementFont(id, font){
	jieqiGetElementById(id).style.fontFamily = font;
}

function setElementSize(id, size){
	jieqiGetElementById(id).style.fontSize = size;
}

function changeDisplay(id){
	var elestyle = jieqiGetElementById(id).style;
	if (elestyle.display == "") {
		elestyle.display = "none";
	} else {
		elestyle.display = "block";
	}
}

function setVisible(id){
	jieqiGetElementById(id).style.visibility = "visible";
}

function setHidden(id){
	jieqiGetElementById(id).style.visibility = "hidden";
}

function makeBold(id){
	var eleStyle = jieqiGetElementById(id).style;
	if (eleStyle.fontWeight != "bold") {
		eleStyle.fontWeight = "bold";
	} else {
		eleStyle.fontWeight = "normal";
	}
}

function makeItalic(id){
	var eleStyle = jieqiGetElementById(id).style;
	if (eleStyle.fontStyle != "italic") {
		eleStyle.fontStyle = "italic";
	} else {
		eleStyle.fontStyle = "normal";
	}
}

function makeUnderline(id){
	var eleStyle = jieqiGetElementById(id).style;
	if (eleStyle.textDecoration != "underline") {
		eleStyle.textDecoration = "underline";
	} else {
		eleStyle.textDecoration = "none";
	}
}

function makeLineThrough(id){
	var eleStyle = jieqiGetElementById(id).style;
	if (eleStyle.textDecoration != "line-through") {
		eleStyle.textDecoration = "line-through";
	} else {
		eleStyle.textDecoration = "none";
	}
}


function appendSelectOption(selectMenuId, optionName, optionValue){
	var selectMenu = jieqiGetElementById(selectMenuId);
	var newoption = new Option(optionName, optionValue);
	selectMenu.options[selectMenu.length] = newoption;
	selectMenu.options[selectMenu.length].selected = true;
}

function disableElement(target){
	var targetDom = jieqiGetElementById(target);
	if (targetDom.disabled != true) {
		targetDom.disabled = true;
	} else {
		targetDom.disabled = false;
	}
}
function jieqiCheckAll(formname, switchid) {
	var ele = document.forms[formname].elements;
	var switch_cbox = jieqiGetElementById(switchid);
	for (var i = 0; i < ele.length; i++) {
		var e = ele[i];
		if ( (e.name != switch_cbox.name) && (e.type == 'checkbox') ) {
			e.checked = switch_cbox.checked;
		}
	}
}