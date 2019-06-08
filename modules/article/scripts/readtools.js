//根据Cookie获取用户登录信息
var jieqiUserInfo = {
    jieqiUserId: 0,
    jieqiUserName: '',
    jieqiUserPassword: '',
    jieqiUserToken: '',
    jieqiUserGroup: 0,
    jieqiNewMessage: 0,
    jieqiCodeLogin: 0,
    jieqiCodePost: 0
};
if (document.cookie.indexOf('jieqiUserInfo') >= 0) {
    var cinfo = Cookie.get('jieqiUserInfo');
    start = 0;
    offset = cinfo.indexOf(',', start);
    while (offset > 0) {
        tmpval = cinfo.substring(start, offset);
        tmpidx = tmpval.indexOf('=');
        if (tmpidx > 0) {
            tmpname = tmpval.substring(0, tmpidx);
            tmpval = tmpval.substring(tmpidx + 1, tmpval.length);
            if (jieqiUserInfo.hasOwnProperty(tmpname)) jieqiUserInfo[tmpname] = tmpval;
        }
        start = offset + 1;
        if (offset < cinfo.length) {
            offset = cinfo.indexOf(',', start);
            if (offset == -1) offset = cinfo.length;
        } else {
            offset = -1;
        }
    }
}

//翻页方式设置
var usePages = false; //是否启用翻页模式
if (usePages && ('columnWidth' in document.documentElement.style || 'MozColumnWidth' in document.documentElement.style || 'WebkitColumnWidth' in document.documentElement.style || 'OColumnWidth' in document.documentElement.style || 'msColumnWidth' in document.documentElement.style)) usePages = true;
else usePages = false;

//显示阅读工具
var ReadTools = {
    bgcolor: ['#fdf7f7', '#000000', '#ffebcd', '#c7edcc', '#e7f4fe'],
    fontcolor: ['#000000', '#555555', '#000000', '#333333', '#000000'],
    bgname: ['白天', '夜间', '怀旧', '护眼', '青春'],
    fontsize: ['0.857em', '1em', '1.143em', '1.286em', '1.429em'],
    fontname: ['小', '中', '大', '较大', '超大'],
    tipegold: [20, 50, 100, 200, 500, 1000],
    colorid: 0,
    fontid: 1,
    ttimer: null,
    tiptime: 4000,
    contentid: 'aread',
    showtools: false,
    CallTools: function () {
        if (ReadTools.showtools) {
            ReadTools.CallHide();
        } else {
            document.getElementById('toptools').style.display = '';
            document.getElementById('bottomtools').style.display = '';
            ReadTools.showtools = true;
        }
    },
    CallShow: function (id) {
        ReadTools.CallHide(1);
        document.getElementById(id).style.display = '';
    },
    CallHide: function () {
        if (!arguments[0]) {
            document.getElementById('toptools').style.display = 'none';
            document.getElementById('bottomtools').style.display = 'none';
            ReadTools.showtools = false;
        }
        document.getElementById('sizecolor').style.display = 'none';
        document.getElementById('givetip').style.display = 'none';
        document.getElementById('readtip').style.display = 'none';
        document.getElementById('addreview').style.display = 'none';
    },
    ShowTip: function (str) {
        document.getElementById('readtip').innerHTML = str;
        ReadTools.CallHide(1);
        ReadTools.CallShow('readtip');
        ReadTools.TipTimeout();
    },
    TipTimeout: function () {
        if (ReadTools.ttimer) clearTimeout(ReadTools.ttimer);
        ReadTools.ttimer = setTimeout(function () {
            if (document.getElementById('readtip').style.display == '') {
                ReadTools.CallHide(1);
            }
        }, ReadTools.tiptime);
    },
    SetColor: function (id) {
        document.getElementById(this.contentid).style.backgroundColor = this.bgcolor[id];
        document.getElementById(this.contentid).style.color = this.fontcolor[id];
        if (ReadTools.colorid != id) Storage.set('read_colorid', id);
        ReadTools.colorid = id;

        var lis = document.getElementById('fontcolor').getElementsByTagName('li');
        for (i = 0; i < lis.length; i++) {
            if (id == i) lis[i].className = 'selected';
            else lis[i].className = '';
        }

    },
    SetFont: function (id) {
        document.getElementById(this.contentid).style.fontSize = this.fontsize[id];
        if (ReadTools.fontid != id) Storage.set('read_fontid', id);
        ReadTools.fontid = id;
        var lis = document.getElementById('fontsize').getElementsByTagName('li');
        for (i = 0; i < lis.length; i++) {
            if (id == i) lis[i].className = 'selected';
            else lis[i].className = '';
        }
        if (usePages) ReadPages.MakePages();
    },
    FontSmall: function () {
        if (ReadTools.fontid > 0) {
            ReadTools.fontid--;
            document.getElementById(this.contentid).style.fontSize = this.fontsize[ReadTools.fontid];
            Storage.set('read_fontid', ReadTools.fontid);
        }
    },
    FontBig: function () {
        if (ReadTools.fontid < this.fontsize.length - 1) {
            ReadTools.fontid++;
            document.getElementById(this.contentid).style.fontSize = this.fontsize[ReadTools.fontid];
            Storage.set('read_fontid', ReadTools.fontid);
        }
    },
    AddBookcase: function () {
        if (jieqiUserInfo.jieqiUserId) {
            Ajax.Request('/modules/article/addbookcase.php?bid=' + ReadParams.articleid + '&cid=' + ReadParams.chapterid, {
                method: 'POST',
                onComplete: function () {
                    ReadTools.ShowTip(this.response);
                }
            });
        } else {
            var jumpurl = window.location.href.indexOf('?') > -1 ? window.location.href + '&before_act=addbookcase' : window.location.href + '?before_act=addbookcase';
            ReadTools.ShowLogin(jumpurl);
        }
    },
    UserVote: function () {
        if (jieqiUserInfo.jieqiUserId) {
            Ajax.Request('/modules/article/uservote.php?id=' + ReadParams.articleid, {
                method: 'POST', onComplete: function () {
                    ReadTools.ShowTip(this.response);
                }
            });
        } else {
            var jumpurl = window.location.href.indexOf('?') > -1 ? window.location.href + '&before_act=uservote' : window.location.href + '?before_act=uservote';
            ReadTools.ShowLogin(jumpurl);
        }
    },
    GiveTip: function (egold) {
        if (jieqiUserInfo.jieqiUserId) {
            Ajax.Request('/modules/article/tip.php', {
                method: 'POST',
                parameters: 'act=post&id=' + ReadParams.articleid + '&payegold=' + parseInt(egold) + '&jieqi_token=' + jieqiUserInfo.jieqiUserToken,
                onComplete: function () {
                    ReadTools.ShowTip(this.response);
                }
            });
        } else {
            var jumpurl = window.location.href.indexOf('?') > -1 ? window.location.href + '&before_act=givetip' : window.location.href + '?before_act=givetip';
            ReadTools.ShowLogin(jumpurl);
        }
    },
    Show: function () {
        var output = '';
        var isdisplay = ReadTools.showtools ? '' : 'none';

        output += '<div id="toptools" class="toptools cf" style="display:' + isdisplay + ';">\
		<a href="javascript: window.location.href = ReadParams.url_articleinfo;" class="iconfont fl">&#xee69;</a>\
		<a href="javascript: window.location.href = ReadParams.url_home;" class="iconfont fr">&#xee27;</a>\
		<a href="javascript: ReadTools.CallShow(\'sizecolor\');" class="iconfont fr">&#xee26;</a>\
		<a href="javascript: ReadTools.CallShow(\'givetip\');" class="iconfont fr">&#xee42;</a>\
		<a href="javascript: ReadTools.AddBookcase();" class="iconfont fr">&#xee60;</a>\
		<!--<a href="javascript: ReadTools.CallShow(\'addreview\');" class="iconfont fr">&#xee3a;</a>-->\
</div>';

        output += '<div id="bottomtools" class="bottomtools cf" style="display:' + isdisplay + ';">\
		<ul>\
	<li onclick="window.location.href = ReadParams.url_previous;"><p class="iconfont f_l">&#xee68;</p><p>上一章</p></li>\
	<li onclick="window.location.href = ReadParams.url_index;"><p class="iconfont f_l">&#xee32;</p><p>目录</p></li>\
	<li onclick="window.location.href = ReadParams.url_articleinfo;"><p class="iconfont f_l">&#xee50;</p><p>详情</p></li>\
	<li onclick="window.location.href = ReadParams.url_next;"><p class="iconfont f_l">&#xee67;</p><p>下一章</p></li>\
	</ul>\
</div>';

        output += '<div id="sizecolor" class="sizecolor" style="display:none;">\
				<p>字体大小</p>\
                <ul id="fontsize" class="fontsize cf">';
        for (i = 0; i < this.fontsize.length; i++) {
            output += ' <li';
            if (this.fontid == i) output += ' class="selected"';
            output += ' onclick="ReadTools.SetFont(' + i + ')">' + this.fontname[i] + '</li>';
        }
        output += '</ul>\
				<p>背景设置</p>\
                <ul id="fontcolor" class="fontcolor cf">';
        for (i = 0; i < this.bgcolor.length; i++) {
            output += ' <li';
            if (this.colorid == i) output += ' class="selected"';
            output += ' style="background: ' + this.bgcolor[i] + ';color: ' + this.fontcolor[i] + '" onclick="ReadTools.SetColor(' + i + ')">' + this.bgname[i] + '</li>';
        }
        output += '    </ul>\
        </div>';
        output += '<div id="addreview" class="addreview" style="display:none;"><form name="frmreview" id="frmreview" method="post" action="/modules/article/reviews.php?aid=' + ReadParams.articleid + '">\
<div><textarea class="textarea" name="pcontent" id="pcontent" style="font-family:Verdana;font-size:100%;width:94%;height:4.5em;margin:0 auto 0.3em auto;"></textarea></div>';
        if (jieqiUserInfo.jieqiCodePost) output += '<div style="margin-bottom: 0.3em;text-align: left;text-indent: 3%;">验证码：<input type="text" class="text" size="8" maxlength="8" name="checkcode" onfocus="if($_(\'p_imgccode\').style.display == \'none\'){$_(\'p_imgccode\').src = \'/checkcode.php\';$_(\'p_imgccode\').style.display = \'\';}" title="点击显示验证码"><img id="p_imgccode" src="" style="cursor:pointer;vertical-align:middle;margin-left:3px;display:none;" onclick="this.src=\'/checkcode.php?rand=\'+Math.random();" title="点击刷新验证码"></div>';
        output += '<input type="button" name="Submit" class="button" value="发表评论" style="cursor:pointer;" onclick="Ajax.Request(\'frmreview\',{onComplete:function(){ReadTools.ShowTip(this.response);}});">\
<input type="hidden" name="act" id="act" value="newpost" />\
</form></div>';
        output += '<div id="givetip" class="givetip" style="display:none;">\
        <dl>\
        <dt>请选择打赏金额</dt>';
        for (i = 0; i < this.tipegold.length; i++) {
            output += ' <dd onclick="ReadTools.GiveTip(' + this.tipegold[i] + ')">' + this.tipegold[i] + ReadParams.egoldname + '</dd>';
        }
        output += '</dl>\
        </div>';
        output += '<div id="readtip" class="readtip" style="display:none;">\
        </div>';
        document.write(output);
    },
    SaveSet: function () {
        Storage.set('read_colorid', ReadTools.colorid);
        Storage.set('read_fontid', ReadTools.fontid);
    },
    LoadSet: function () {
        var id = 0;

        id = parseInt(Storage.get('read_colorid'));
        if (id >= 0 && id < this.bgcolor.length) ReadTools.colorid = id;
        this.SetColor(ReadTools.colorid);

        id = parseInt(Storage.get('read_fontid'));
        if (id >= 0 && id < this.fontsize.length) ReadTools.fontid = id;
        this.SetFont(ReadTools.fontid);

    },
    ShowLogin: function(jumpurl){
        ReadTools.ShowTip('请点击 <a class="fsl fwb" href="/login.php?jumpurl='+encodeURIComponent(jumpurl)+'">登录</a> 后使用本功能！');
    },
    GetQueryString: function(name){
        var reg = new RegExp("(^|&)"+ name +"=([^&]*)(&|$)");
        var r = window.location.search.substr(1).match(reg);
        if(r != null) return unescape(r[2]);
        return null;
    },
    DoBefore: function(){
        var before_act = ReadTools.GetQueryString('before_act');
        switch(before_act){
            case 'addbookcase':
                ReadTools.CallTools();
                ReadTools.ShowTip('登录成功，请重新点击收藏！');
                break;
            case 'uservote':
                ReadTools.CallTools();
                ReadTools.ShowTip('登录成功，请重新点击推荐！');
                break;
            case 'givetip':
                ReadTools.CallTools();
                ReadTools.ShowTip('登录成功，请重新点击打赏！');
                break;
        }
    }
};

//显示翻页
var ReadPages = {
    totalPages: 0, //总页数
    currentPage: 0, //当前页码
    pageWidth: 0, //页宽
    pageHeight: 0, //页高
    pageGapX: 15,//左右边距
    pageGapY: 20,//上下边距

    PageClick: function () {
        var e = window.event ? window.event : getEvent();
        if (e.clientX < ReadPages.pageWidth * 0.333) ReadPages.ShowPage('previous');
        else if (e.clientX > ReadPages.pageWidth * 0.666) ReadPages.ShowPage('next');
        else ReadTools.CallTools();
    },
    MakePages: function () {
        ReadPages.pageWidth = document.documentElement.clientWidth; //页宽
        ReadPages.pageHeight = document.documentElement.clientHeight; //页高

        var footlink = $_('footlink');
        if(footlink) footlink.setStyle('display', 'none');

        var abox = $_('abox');
        abox.setStyle('overflow', 'hidden');
        abox.setStyle('margin', ReadPages.pageGapY + 'px ' + ReadPages.pageGapX + 'px');
        abox.setStyle('width', (ReadPages.pageWidth - ReadPages.pageGapX * 2) + 'px');
        abox.setStyle('height', (ReadPages.pageHeight - ReadPages.pageGapY * 2) + 'px');

        var apage = $_('apage');
        apage.setStyle('position', 'relative');
        apage.setStyle('height', (ReadPages.pageHeight - ReadPages.pageGapY * 2) + 'px');
        apage.setStyle('columnWidth', (ReadPages.pageWidth - ReadPages.pageGapX * 2) + 'px', true);
        apage.setStyle('columnGap', '0', true);

        var pagecount = Math.ceil(apage.scrollWidth / apage.clientWidth);

        if (ReadPages.totalPages != pagecount) {
            if (ReadPages.currentPage > 1) ReadPages.currentPage = Math.floor(pagecount * ReadPages.currentPage / ReadPages.totalPages);
            ReadPages.totalPages = pagecount;
        }
        if (window.location.href.indexOf('#lastPage') > -1 && ReadPages.currentPage == 0) ReadPages.currentPage = ReadPages.totalPages;
        if (ReadPages.currentPage < 1) ReadPages.currentPage = 1;
        if (ReadPages.currentPage > ReadPages.totalPages) ReadPages.currentPage = ReadPages.totalPages;

        ReadPages.ShowPage();
    },
    ShowPage: function () {
        if (arguments[0]) {
            if (arguments[0] == 'next') {
                ReadPages.currentPage++;

                if (ReadPages.currentPage > ReadPages.totalPages) {
                    document.location.href = ReadParams.url_next;
                    return true;
                }
            } else if (arguments[0] == 'previous') {
                ReadPages.currentPage--;
                if (ReadPages.currentPage < 1) {
                    document.location.href = ReadParams.url_previous + '#lastPage';
                    return true;
                }
            }
        }

        if (ReadPages.currentPage < 1) ReadPages.currentPage = 1;
        if (ReadPages.currentPage > ReadPages.totalPages) ReadPages.currentPage = ReadPages.totalPages;

        if (ReadPages.currentPage == 1) apage.setStyle('left', '0');
        else apage.setStyle('left', '-' + ((ReadPages.currentPage - 1) * (ReadPages.pageWidth - ReadPages.pageGapX * 2)) + 'px');


        var toptext = $_('toptext');
        if (ReadPages.currentPage > 1) {
            toptext.innerHTML = $_('atitle').innerHTML;
            toptext.setStyle('display', '');
        } else {
            toptext.setStyle('display', 'none');
        }

        var bottomtext = $_('bottomtext');
        bottomtext.innerHTML = ReadPages.currentPage + '/' + ReadPages.totalPages;
        bottomtext.setStyle('display', '');
    }
}

if (usePages) {
    addEvent(window, 'load', ReadPages.MakePages);
    addEvent(window, 'resize', ReadPages.MakePages);
    document.getElementById(ReadTools.contentid).onclick = ReadPages.PageClick;

    //显示翻页提示
    var read_hidetip = Storage.get('read_hidetip');
    if (read_hidetip != 1) {
        $_('operatetip').style.display = '';
        Storage.set('read_hidetip', '1');
    }
} else {
    document.getElementById(ReadTools.contentid).onclick = ReadTools.CallTools;
}

ReadTools.Show();
ReadTools.LoadSet();
ReadTools.DoBefore();

//把2个英文空格换成1个全角空格
//addEvent(window, 'load', function(){document.getElementById('acontent').innerHTML = document.getElementById('acontent').innerHTML.replace(/&nbsp;&nbsp;/g, '&emsp;');});