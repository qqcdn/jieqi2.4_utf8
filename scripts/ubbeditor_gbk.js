var ubb_subdiv;
var UBBEditor = {
	Create : function(eid){
		this.siteUrl = '';
		var scripts = document.getElementsByTagName('script');
		for (var i=0; i<scripts.length; i++) {
			if(scripts[i].src.indexOf('/scripts/ubbeditor') != -1){
				this.siteUrl = scripts[i].src.substr(0, scripts[i].src.indexOf('/scripts/ubbeditor'));
			}
		}
		this.TextId = eid;
		this.TextBox = document.getElementById(this.TextId);
		this.Menu = this.BuildMenu();
		this.Init();
	},
	Init : function(){
		this.TextBox.parentNode.insertBefore(this.Menu, this.TextBox);
		this.BuildStyle();
		this.FontSize();
		this.Bold();
		this.Italic();
		this.UnderLine();
		this.Delete();
		this.ColorList();
		this.Code();
		this.Quote();
		this.InsertUrl();
		this.InsertEmail();
		this.InsertImage();
		this.SmileList();
		this.AlignLeft();
		this.AlignCenter();
		this.AlignRight();
		this.ClearBothDiv();
	},
	BuildStyle : function(){
		var style = "";
		//style +="<style type='text/css'>";
		style +="body{margin: 0px;padding: 0px;}\n";
		style +=".UBB_Menu{height: 16px; padding: 3px; display:inline;}\n";
		style +=".UBB_MenuItem{margin-right: 2px; height: 20px; width: 21px; float: left; border: none; background-color: transparent; background-position: center center; background-repeat : no-repeat; cursor: pointer;}\n";
		style +=".UBB_FontSizeList{position: absolute; z-index: 999; background-color: #f5f5f5; float: left; clear: both;}\n";
		style +=".UBB_FontSizeList ul{width:40px; list-style: none; text-align: center; padding: 0px; margin: 0px;}\n";
		style +=".UBB_FontSizeList li{line-height:1.2; display:block; list-style: none; margin: 0; padding:0; width: 100%; border: solid 1px #cccccc; cursor: pointer;}\n";
		style +=".UBB_ColorList{position: absolute; z-index: 999; background-color: #ffffff; float: left; clear: both;}\n";
		style +=".UBB_ColorList table{width:auto; border: solid 1px #cccccc;}\n";
		style +=".UBB_ColorList table td{background-color: #ffffff; border: solid 1px #dddddd;	text-align: center; padding:0px; margin:0px; line-height: 16px;}\n";
		style +=".UBB_ColorList input{background-color: #ffffff; cursor: pointer; border: none; padding: 0px; margin: 0px; width: 16px; height: 16px;}\n";
		style +=".UBB_SmileList{position: absolute; z-index: 999; background-color: #ffffff; float: left;	clear: both;}\n";
		style +=".UBB_SmileList table{width:auto; border: solid 1px #cccccc;}\n";
		style +=".UBB_SmileList table td{background-color: #ffffff; border: none; text-align: center; padding:0px; margin:0px;}\n";
		//style +="</style>";

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
	},
	GetPosition : function(e) {
		if (document.documentElement.getBoundingClientRect) {
            return {
                x: e.getBoundingClientRect().left + Math.max(document.body.scrollLeft, document.documentElement.scrollLeft),
                y: e.getBoundingClientRect().top + Math.max(document.body.scrollTop, document.documentElement.scrollTop)
            };
        } else {
            var t = e.offsetTop;
            var l = e.offsetLeft;
            while (e = e.offsetParent) {
                t += e.offsetTop;
                l += e.offsetLeft;
            }
            return {x: l, y: t};
        }
	},
	BuildMenu : function(){
		menudiv = document.createElement("div");
		menudiv.className = "UBB_Menu";
		menudiv.id = "UBB_Menu";
		return menudiv;
	},
	InsertText : function(eid,text){
		var obj = document.getElementById(eid);
		 var objLength=obj.value.length;
		obj.focus();
		if(typeof document.selection != "undefined"){
			document.selection.createRange().text = text;  
		}else{
			var st = obj.selectionStart;
			obj.value = obj.value.substr(0, st) + text + obj.value.substring(st, objLength);
			obj.setSelectionRange(st + text.length, st + text.length);  
		}
	},
	InsertTag : function(eid,tag,val,txt){
		document.getElementById(eid).focus();
		var tagEnd = "[/" + tag + "]";
		var tagStart = "[" + tag;
		if(val != "") tagStart += "=" + val;
		tagStart += "]";
		if(txt != ""){
			UBBEditor.InsertText(eid, tagStart + txt + tagEnd);
		}else{
			if(document.selection && document.selection.type == "Text"){
				var oStr = document.selection.createRange();
				oStr.text = tagStart + oStr.text + tagEnd;
				oStr.select(); 
			}else if(window.getSelection && document.getElementById(eid).selectionStart > - 1){
				var st = document.getElementById(eid).selectionStart;
				var ed = document.getElementById(eid).selectionEnd;
				document.getElementById(eid).value = document.getElementById(eid).value.substring(0, st) + tagStart + document.getElementById(eid).value.substring(st, ed) + tagEnd + document.getElementById(eid).value.slice(ed);
				document.getElementById(eid).setSelectionRange(ed + tagStart.length + tagEnd.length ,ed + tagStart.length + tagEnd.length);  
			}else{
				UBBEditor.InsertText(eid, tagStart + tagEnd);
			}
		}
	},
	FontSize : function(){
		var menuFontSize = document.createElement("input");
		menuFontSize.type = "button";
		menuFontSize.id = "menuItemFontSize";
		menuFontSize.title = "字体大小";
		menuFontSize.className = "UBB_MenuItem";
		menuFontSize.style.backgroundImage = "url('" + this.siteUrl + "/images/ubb/bb_size.gif" + "')";
		this.Menu.appendChild(menuFontSize);
		var ftsize = ["9", "10", "12", "14", "16", "18", "24", "36"];
		var ftList = "<ul>";
		for(var temp=0; temp<ftsize.length; temp++){
			ftList += "<li onclick='UBBEditor.GetFontSize(\"" + ftsize[temp] + "\",\""+this.TextId+"\")'>" + ftsize[temp] + "</li>";
		}
		ftList += "</ul>";

		var fts = document.createElement("div");
		fts.id = "FontSizeTable";
		fts.style.display = "none";
		fts.className = "UBB_FontSizeList";
		fts.innerHTML = ftList;
		this.TextBox.parentNode.insertBefore(fts, this.TextBox);
		

		menuFontSize.onclick = function(e){
			if(ubb_subdiv){
				hideeve(ubb_subdiv);
			}
			ubb_subdiv = fts.id;
			if(fts.style.display == "none"){
				fts.style.display = "";	
				var p = UBBEditor.GetPosition(menuFontSize);		
				fts.style.left = p['x']+'px';
				fts.style.top = (p['y'] + 20)+'px';
			}
			var evt = (window.event || e);
			if(evt.preventDefault){
				evt.preventDefault();
				evt.stopPropagation();
			}else{
				evt.cancelBubble = true;
				evt.returnValue = false;
			}
			if(document.attachEvent){
				document.attachEvent("onclick", function(){fts.style.display = "none";});
			}else{
				document.addEventListener("click", hideeve, false);
			}
		};
	},
	GetFontSize : function(fs,eid){
		UBBEditor.InsertTag(eid, "size", fs, "");
	},
	Bold : function(){
		var menuBold = document.createElement("input");
		menuBold.type = "button";
		menuBold.id = "menuItemBold";
		menuBold.title = "粗体";
		menuBold.className = "UBB_MenuItem";
		menuBold.style.backgroundImage = "url('" + this.siteUrl + "/images/ubb/bb_bold.gif" + "')";
		this.Menu.appendChild(menuBold);
		var eid = this.TextId;
		menuBold.onclick = function(){
			UBBEditor.InsertTag(eid, "b", "", "");
		};
	},
	Italic : function(){
		var menuItalic = document.createElement("input");
		menuItalic.type = "button";
		menuItalic.id = "menuItemItalic";
		menuItalic.title = "斜体";
		menuItalic.className = "UBB_MenuItem";
		menuItalic.style.backgroundImage = "url('" + this.siteUrl + "/images/ubb/bb_italic.gif" + "')";
		this.Menu.appendChild(menuItalic);
		var eid = this.TextId;
		menuItalic.onclick = function(){
			UBBEditor.InsertTag(eid, "i", "", "");
		};
	},
	UnderLine : function(){
		var menuUnderLine = document.createElement("input");
		menuUnderLine.type = "button";
		menuUnderLine.id = "menuItemUnderLine";
		menuUnderLine.title = "下划线";
		menuUnderLine.className = "UBB_MenuItem";
		menuUnderLine.style.backgroundImage = "url('" + this.siteUrl + "/images/ubb/bb_underline.gif" + "')";
		this.Menu.appendChild(menuUnderLine);
		var eid = this.TextId;
		menuUnderLine.onclick = function(){
			UBBEditor.InsertTag(eid, "u", "", "");
		};
	},
	Delete : function(){
		var menuDelete = document.createElement("input");
		menuDelete.type = "button";
		menuDelete.id = "menuItemDelete";
		menuDelete.title = "删除线";
		menuDelete.className = "UBB_MenuItem";
		menuDelete.style.backgroundImage = "url('" + this.siteUrl + "/images/ubb/bb_delete.gif" + "')";
		this.Menu.appendChild(menuDelete);
		var eid = this.TextId;
		menuDelete.onclick = function(){
			UBBEditor.InsertTag(eid, "d", "", "");
		};
	},
	ColorList : function(){
		var menuColorList = document.createElement("input");
		menuColorList.type = "button";
		menuColorList.id = "menuItemColorList";
		menuColorList.title = "字体颜色";
		menuColorList.className = "UBB_MenuItem";
		menuColorList.style.backgroundImage = "url('" + this.siteUrl + "/images/ubb/bb_color.gif" + "')";
		this.Menu.appendChild(menuColorList);

		var clList = ["000000", "993300", "333300", "003300", "003366", "000090", "333399", "333333", "900000", "FF6600", "909000", "009000", "009090", "0000FF", "666699", "909090", "FF0000", "FF9900", "99CC00", "339966", "33CCCC", "3366FF", "900090", "999999", "FF00FF", "FFCC00", "FFFF00", "00FF00", "00FFFF", "00CCFF", "993366", "C0C0C0", "FF99CC", "FFCC99", "FFFF99", "CCFFCC", "CCFFFF", "99CCFF", "CC99FF", "FFFFFF"];

		var clrTB = "<table border='0' cellpadding='0' cellspacing='4'>";
		count = clList.length;
		point = 0;
		cols = 0;
		while(point < count){
			if(cols == 0) clrTB += "<tr>";
			clrTB +="<td><input type='button' style='background-color:#" + clList[point] + "' onclick='UBBEditor.GetColor(\"" + clList[point] + "\",\""+this.TextId+"\")' /></td>";
			cols++;
			if(cols >= 8){
				clrTB += "</tr>";
				cols = 0;
			}
			point++;
		}
		if(cols > 0) clrTB += "</tr>";
		clrTB += "</table>";

		var clrlst = document.createElement("div");
		clrlst.id = "ColorListTable";
		clrlst.style.display = "none";
		clrlst.className = "UBB_ColorList";
		clrlst.innerHTML = clrTB;
		this.TextBox.parentNode.insertBefore(clrlst, this.TextBox);

		menuColorList.onclick = function(e){
			if(ubb_subdiv){
				hideeve(ubb_subdiv);
			}
			ubb_subdiv = clrlst.id;
			if(clrlst.style.display == "none"){
				clrlst.style.display = "";
				var p = UBBEditor.GetPosition(menuColorList);		
				clrlst.style.left = p['x']+'px';
				clrlst.style.top = (p['y'] + 20)+'px';
			}

			var evt = (window.event || e);

			if(evt.preventDefault){
				evt.preventDefault();
				evt.stopPropagation();
			}else{
				evt.cancelBubble = true;
				evt.returnValue = false;
			}

			if(document.attachEvent){
				document.attachEvent("onclick", function(){clrlst.style.display = "none";});
			}else{
				document.addEventListener("click", hideeve, false);
			}
		};
	},
	GetColor : function(clr,eid){
		UBBEditor.InsertTag(eid, "color", clr, "");
	},
	Code : function(){
		var menuCode = document.createElement("input");
		menuCode.type = "button";
		menuCode.id = "menuItemCodeList";
		menuCode.title = "插入源代码";
		menuCode.className = "UBB_MenuItem";
		menuCode.style.backgroundImage = "url('" + this.siteUrl + "/images/ubb/bb_code.gif" + "')";
		this.Menu.appendChild(menuCode);
		var eid = this.TextId;
		menuCode.onclick = function(){
			UBBEditor.InsertTag(eid, "code", "", "");
		};
	},
	Quote : function(){
		var menuQuote = document.createElement("input");
		menuQuote.type = "button";
		menuQuote.id = "menuItemQuoteList";
		menuQuote.title = "插入引用";
		menuQuote.className = "UBB_MenuItem";
		menuQuote.style.backgroundImage = "url('" + this.siteUrl + "/images/ubb/bb_quote.gif" + "')";
		this.Menu.appendChild(menuQuote);
		var eid = this.TextId;
		menuQuote.onclick = function(){
			UBBEditor.InsertTag(eid, "quote", "", "");
		};
	},
	InsertUrl : function(){
		var menuInsertUrl = document.createElement("input");
		menuInsertUrl.type = "button";
		menuInsertUrl.id = "menuItemInsertUrl";
		menuInsertUrl.title = "插入超链接";
		menuInsertUrl.className = "UBB_MenuItem";
		menuInsertUrl.style.backgroundImage = "url('" + this.siteUrl + "/images/ubb/bb_url.gif" + "')";
		this.Menu.appendChild(menuInsertUrl);
		var eid = this.TextId;
		menuInsertUrl.onclick = function(){
			var url = prompt("请输入超链接地址", "http://");
			if(url != null && url.indexOf("http://") < 0){
				alert("请输入完整的超链接地址！");
				return;
			}
			if(url != null){
				if((document.selection && document.selection.type == "Text") || (window.getSelection && document.getElementById(eid).selectionStart > - 1 && document.getElementById(eid).selectionEnd > document.getElementById(eid).selectionStart)) UBBEditor.InsertTag(eid, "url", url, '');
				else UBBEditor.InsertTag(eid, "url", url, url);
			}
		};
	},
	InsertEmail : function(){
		var menuInsertEmail = document.createElement("input");
		menuInsertEmail.type = "button";
		menuInsertEmail.id = "menuItemInsertEmail";
		menuInsertEmail.title = "插入Email";
		menuInsertEmail.className = "UBB_MenuItem";
		menuInsertEmail.style.backgroundImage = "url('" + this.siteUrl + "/images/ubb/bb_email.gif" + "')";
		this.Menu.appendChild(menuInsertEmail);
		var eid = this.TextId;
		menuInsertEmail.onclick = function(){
			var mail = prompt("请输入Email：", "");
			if(mail != null && !/^\w+((-\w+)|(\.\w+))*\@[A-Za-z0-9]+((\.|-)[A-Za-z0-9]+)*\.[A-Za-z0-9]+$/.test(mail))
			{
				return;
			}
			if(mail != null){
				UBBEditor.InsertTag(eid, "email", "", mail);
			}
		};
	},
	InsertImage : function(){
		var menuInsertImage = document.createElement("input");
		menuInsertImage.type = "button";
		menuInsertImage.id = "menuItemInsertImage";
		menuInsertImage.title = "插入图片";
		menuInsertImage.className = "UBB_MenuItem";
		menuInsertImage.style.backgroundImage = "url('" + this.siteUrl + "/images/ubb/bb_image.gif" + "')";
		this.Menu.appendChild(menuInsertImage);
		var eid = this.TextId;
		menuInsertImage.onclick = function(){
			var imgurl = prompt("请输入图片路径", "http://");
			if(imgurl != null && imgurl.indexOf("http://") < 0){
				alert("请输入完整的图片路径！");
				return;
			}
			if(imgurl != null){
				UBBEditor.InsertTag(eid, "img", "", imgurl);
			}
		};
	},
	AlignLeft : function(){
		var menuAlignLeft = document.createElement("input");
		menuAlignLeft.type = "button";
		menuAlignLeft.id = "menuItemAlignLeft";
		menuAlignLeft.title = "左对齐";
		menuAlignLeft.className = "UBB_MenuItem";
		menuAlignLeft.style.backgroundImage = "url('" + this.siteUrl + "/images/ubb/bb_left.gif" + "')";
		this.Menu.appendChild(menuAlignLeft);
		var eid = this.TextId;
		menuAlignLeft.onclick = function(){
			UBBEditor.InsertTag(eid, "align", "left", "");
		};
	},
	AlignCenter : function(){
		var menuAlignCenter = document.createElement("input");
		menuAlignCenter.type = "button";
		menuAlignCenter.id = "menuItemAlignCenter";
		menuAlignCenter.title = "居中对齐";
		menuAlignCenter.className = "UBB_MenuItem";
		menuAlignCenter.style.backgroundImage = "url('" + this.siteUrl + "/images/ubb/bb_center.gif" + "')";
		this.Menu.appendChild(menuAlignCenter);
		var eid = this.TextId;
		menuAlignCenter.onclick = function(){
			UBBEditor.InsertTag(eid, "align", "center", "");
		};
	},
	AlignRight : function(){
		var menuAlignRight = document.createElement("input");
		menuAlignRight.type = "button";
		menuAlignRight.id = "menuItemAlignRight";
		menuAlignRight.title = "右对齐";
		menuAlignRight.className = "UBB_MenuItem";
		menuAlignRight.style.backgroundImage = "url('" + this.siteUrl + "/images/ubb/bb_right.gif" + "')";
		this.Menu.appendChild(menuAlignRight);
		var eid = this.TextId;
		menuAlignRight.onclick = function(){
			UBBEditor.InsertTag(eid, "align", "right", "");
		};
	},
	SmileList : function(){
		var menuSmileList = document.createElement("input");
		menuSmileList.type = "button";
		menuSmileList.id = "menuItemSmileList";
		menuSmileList.title = "表情";
		menuSmileList.className = "UBB_MenuItem";
		menuSmileList.style.backgroundImage = "url('" + this.siteUrl + "/images/ubb/bb_smile.gif" + "')";
		this.Menu.appendChild(menuSmileList);

		var smList = [];
		smList.push(['/:O', '1.gif', '惊讶']);
		smList.push(['/:~', '2.gif', '撇嘴']);
		smList.push(['/:*', '3.gif', '色色']);
		smList.push(['/:|', '4.gif', '发呆']);
		smList.push(['/8-)', '5.gif', '得意']);
		smList.push(['/:LL', '6.gif', '流泪']);
		smList.push(['/:$', '7.gif', '害羞']);
		smList.push(['/:X', '8.gif', '闭嘴']);
		smList.push(['/:Z', '9.gif', '睡觉']);
		smList.push(['/:`(', '10.gif', '大哭']);
		smList.push(['/:-', '11.gif', '尴尬']);
		smList.push(['/:@', '12.gif', '发怒']);
		smList.push(['/:P', '13.gif', '调皮']);
		smList.push(['/:D', '14.gif', '呲牙']);
		smList.push(['/:)', '15.gif', '微笑']);
		smList.push(['/:(', '16.gif', '难过']);
		smList.push(['/:+', '17.gif', '耍酷']);
		smList.push(['/:#', '18.gif', '禁言']);
		smList.push(['/:Q', '19.gif', '抓狂']);
		smList.push(['/:T', '20.gif', '呕吐']);

		var smCol = 4;
		var smTB = "<table border='0' cellpadding='0' cellspacing='" + smCol + "'>";
		count = smList.length;
		point = 0;
		cols = 0;
		while(point < count){
			if(cols == 0) smTB += "<tr>";
			smTB +="<td><img class='smile' src='" + this.siteUrl + "/images/smiles/" + smList[point][1] + "' title='" + smList[point][2] + "' style='cursor:pointer' onclick='UBBEditor.InsertText(\""+this.TextId+"\", \"" + smList[point][0] + "\")' /></td>";
			cols++;
			if(cols >= smCol){
				smTB += "</tr>";
				cols = 0;
			}
			point++;
		}
		if(cols > 0) smTB += "</tr>";
		smTB += "</table>";

		var smlst = document.createElement("div");
		smlst.id = "SmileListTable";
		smlst.style.display = "none";
		smlst.className = "UBB_SmileList";
		smlst.innerHTML = smTB;
		this.TextBox.parentNode.insertBefore(smlst, this.TextBox);
		menuSmileList.onclick = function(e){
			if(ubb_subdiv){
				hideeve(ubb_subdiv);
			}
			ubb_subdiv = smlst.id;
			if(smlst.style.display == "none"){
				smlst.style.display = "";
				var p = UBBEditor.GetPosition(menuSmileList);		
				smlst.style.left = p['x']+'px';
				smlst.style.top = (p['y'] + 20)+'px';
			}

			var evt = (window.event || e);

			if(evt.preventDefault){
				evt.preventDefault();
				evt.stopPropagation();
			}else{
				evt.cancelBubble = true;
				evt.returnValue = false;
			}

			if(document.attachEvent){
				document.attachEvent("onclick", function(){smlst.style.display = "none";});
			}else{
				document.addEventListener("click", hideeve, false);
			}
		};
	},
	ClearBothDiv : function(){
		var BlankDiv = document.createElement("div");
		BlankDiv.style.clear = "both";
		BlankDiv.style.floatStyle = "both";
		BlankDiv.innerHTML = " ";
		this.Menu.appendChild(BlankDiv);
	}
}
function hideeve(){
	document.getElementById(ubb_subdiv).style.display = "none";
}