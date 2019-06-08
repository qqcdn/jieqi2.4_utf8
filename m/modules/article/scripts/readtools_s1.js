//根据Cookie获取用户登录信息
var jieqiUserInfo = {jieqiUserId:0, jieqiUserName:'', jieqiUserPassword:'', jieqiUserToken:'', jieqiUserGroup:0, jieqiNewMessage:0, jieqiCodeLogin:0, jieqiCodePost:0};
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
            if(jieqiUserInfo.hasOwnProperty(tmpname)) jieqiUserInfo[tmpname] = tmpval;
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
//显示阅读工具
var ReadTools = {
    bgcolor: ["#fdf7f7", "#ffebcd", "#e7f4fe", "#000000"],
    fontcolor: ["#000000", "#000000", "#000000", "#ffffff"],
    night: ["#000000", "#555555"],
    fontsize: ["0.857em", "1em", "1.143em", "1.286em", "1.429em", "1.714em"],
    tipegold: [20, 50, 100, 200, 500],
    colorid: 0,
    fontid: 1,
    isnight: 0,
    isscroll: false,
    sspeed: 5,
    stimer: null,
    ttimer: null,
    tiptime: 4000,
    contentid: "aread",
    showtools: false,
    CallTools: function () {
        if (ReadTools.showtools) {
            ReadTools.CallHide();
            ReadTools.showtools = false;
        } else {
            document.getElementById("readtools").style.display = "";
            ReadTools.showtools = true;
        }

        if (ReadTools.isscroll) {
            clearInterval(ReadTools.stimer);
            ReadTools.isscroll = false;
        }
    },
    CallShow: function (id) {
        ReadTools.CallHide();
        document.getElementById(id).style.display = "";
    },
    CallHide: function () {
        document.getElementById("readtools").style.display = "none";
        document.getElementById("sizecolor").style.display = "none";
        document.getElementById("givetip").style.display = "none";
        document.getElementById("readtip").style.display = "none";
        document.getElementById("addreview").style.display = "none";
    },
    ShowTip: function (str) {
        document.getElementById("readtip").innerHTML = str;
        ReadTools.CallShow("readtip");
        ReadTools.TipTimeout();
    },
    TipTimeout: function () {
        if (ReadTools.ttimer) clearTimeout(ReadTools.ttimer);
        ReadTools.ttimer = setTimeout(function () {
            if (document.getElementById("readtip").style.display == "") {
                ReadTools.CallHide();
                ReadTools.showtools = false;
            }
        }, ReadTools.tiptime);
    },
    SetNight: function () {
        if (ReadTools.isnight == 0) {
            document.getElementById(this.contentid).style.backgroundColor = this.night[0];
            document.getElementById(this.contentid).style.color = this.night[1];
            if (ReadTools.isnight != 1) this.SetCookies("isnight", 1);
            ReadTools.isnight = 1;
        } else {
            ReadTools.SetColor(ReadTools.colorid);
            if (ReadTools.isnight != 0) this.SetCookies("isnight", 0);
            ReadTools.isnight = 0;
        }
    },
    SetColor: function (id) {
        document.getElementById(this.contentid).style.backgroundColor = this.bgcolor[id];
        document.getElementById(this.contentid).style.color = this.fontcolor[id];
        if (ReadTools.colorid != id) this.SetCookies("colorid", id);
        ReadTools.colorid = id;
        if (ReadTools.isnight != 0) this.SetCookies("isnight", 0);
        ReadTools.isnight = 0;
    },
    SetFont: function (id) {
        document.getElementById(this.contentid).style.fontSize = this.fontsize[id];
        if (ReadTools.fontid != id) this.SetCookies("fontid", id);
        ReadTools.fontid = id;
    },
    FontSmall: function () {
        if (ReadTools.fontid > 0) {
            ReadTools.fontid--;
            document.getElementById(this.contentid).style.fontSize = this.fontsize[ReadTools.fontid];
            this.SetCookies("fontid", ReadTools.fontid);
        }
    },
    FontBig: function () {
        if (ReadTools.fontid < this.fontsize.length - 1) {
            ReadTools.fontid++;
            document.getElementById(this.contentid).style.fontSize = this.fontsize[ReadTools.fontid];
            this.SetCookies("fontid", ReadTools.fontid);
        }
    },
    AddBookcase: function () {
        if (jieqiUserInfo.jieqiUserId) {
            Ajax.Request('/modules/article/addbookcase.php?bid=' + articleid + '&cid=' + chapterid, {
                method: 'POST',
                onComplete: function () {
                    ReadTools.ShowTip(this.response);
                }
            });
        } else {
            ReadTools.ShowTip('请先 <a href="/login.php"><b>登录</b>.</a>');
        }
    },
    UserVote: function () {
        if (jieqiUserInfo.jieqiUserId) {
            Ajax.Request('/modules/article/uservote.php?id=' + articleid, {
                method: 'POST', onComplete: function () {
                    ReadTools.ShowTip(this.response);
                }
            });
        } else {
            ReadTools.ShowTip('请先 <a href="/login.php"><b>登录</b></a>');
        }
    },
    GiveTip: function (egold) {
        if (jieqiUserInfo.jieqiUserId) {
            Ajax.Request('/modules/article/tip.php', {
                method: 'POST', parameters: 'act=post&id=' + articleid + '&payegold=' + parseInt(egold) + '&jieqi_token=' + jieqiUserInfo.jieqiUserToken, onComplete: function () {
                    ReadTools.ShowTip(this.response);
                }
            });
        } else {
            ReadTools.ShowTip('请先 <a href="/login.php"><b>登录</b></a>');
        }
    },
    CallScroll: function () {
        if (ReadTools.isscroll) {
            clearInterval(ReadTools.stimer);
            ReadTools.isscroll = false;
        } else {
            ReadTools.stimer = setInterval(ReadTools.Scrolling, 50 / ReadTools.sspeed);
            ReadTools.isscroll = true;
            var tiphtml = '<span class="button" style="float:left;margin-left:5px;padding:0;font-size:1.2em;width:2em;height:2em;line-height:2em;text-align:center;-webkit-tap-highlight-color:rgba(0, 0, 0, 0);-webkit-touch-callout:none;" id="speedsub" onclick="document.getElementById(\'sspeedvalue\').innerHTML = ReadTools.ScrollSpeed(false);">-</span><span class="button" style="float:right;margin-right:5px;padding:0;font-size:1.2em;width:2em;height:2em;line-height:2em;text-align:center;-webkit-tap-highlight-color:rgba(0, 0, 0, 0);-webkit-touch-callout:none;" id="speedadd" onclick="document.getElementById(\'sspeedvalue\').innerHTML = ReadTools.ScrollSpeed(true);">+</span><span id="sspeedvalue" style="font-size:1.2em;line-height:2em;">' + ReadTools.sspeed + '</span>';
            ReadTools.ShowTip(tiphtml);
        }
    },
    Scrolling: function () {
        var currentpos = document.documentElement.scrollTop || window.pageYOffset || document.body.scrollTop || 0;
        window.scrollTo(0, ++currentpos);
        if ((document.compatMode === "CSS1Compat" && currentpos + document.documentElement.clientHeight > document.documentElement.scrollHeight) || (document.compatMode !== "CSS1Compat" && currentpos + document.body.clientHeight > document.body.scrollHeight)) {
            clearInterval(ReadTools.stimer);
            ReadTools.isscroll = false;
        }
    },
    ScrollSpeed: function (add) {
        ReadTools.sspeed = add ? ReadTools.sspeed + 1 : ReadTools.sspeed - 1;
        if (ReadTools.sspeed > 10) ReadTools.sspeed = 10;
        if (ReadTools.sspeed < 1) ReadTools.sspeed = 1;
        if (ReadTools.isscroll) {
            clearInterval(ReadTools.stimer);
            ReadTools.stimer = setInterval(ReadTools.Scrolling, 50 / ReadTools.sspeed);
        }
        this.SetCookies("sspeed", ReadTools.sspeed);
        ReadTools.TipTimeout();
        return ReadTools.sspeed;
    },
    Show: function () {
        var output = "";
        var isdisplay = ReadTools.showtools ? '' : 'none';
        output = '<div id="readtools" class="readtools cf" style="display:' + isdisplay + ';">\
	<ul>\
	<li class="iconfont" onclick="ReadTools.CallShow(\'sizecolor\');">&#xee76;</li>\
	<li class="iconfont" onclick="ReadTools.SetNight();">&#xee78;</li>\
	<li class="iconfont" onclick="ReadTools.CallScroll();">&#xee7a;</li>\
	<li class="iconfont" onclick="ReadTools.AddBookcase();">&#xee60;</li>\
	<li class="iconfont" onclick="window.location.href = url_index;">&#xee32;</li>\
	<li class="iconfont" onclick="window.location.href = url_previous;">&#xee69;</li>\
	<li class="iconfont" onclick="ReadTools.UserVote();">&#xee5d;</li>\
	<li class="iconfont" onclick="ReadTools.CallShow(\'addreview\');">&#xee3a;</li>\
	<li class="iconfont" onclick="ReadTools.CallShow(\'givetip\');">&#xee70;</li>\
	<li class="iconfont" onclick="window.location.href = url_next;">&#xee6a;</li>\
	</ul>\
</div>';

        output += '<div id="sizecolor" class="sizecolor" style="display:none;">\
                <ul class="fontsize cf">\
                    <li style="float: left;" onclick="ReadTools.FontSmall();">Aa-</li>\
                    <li style="float: right;" onclick="ReadTools.FontBig();">Aa+</li>\
                </ul>\
                <ul class="fontcolor cf">';
        for (i = 0; i < this.bgcolor.length; i++) {
            output += ' <li style="background: ' + this.bgcolor[i] + ';color: ' + this.fontcolor[i] + '" onclick="ReadTools.SetColor(' + i + ')">Aa</li>';
        }
        output += '    </ul>\
        </div>';
        output += '<div id="addreview" class="addreview" style="display:none;"><form name="frmreview" id="frmreview" method="post" action="/modules/article/reviews.php?aid=' + articleid + '">\
<div><textarea class="textarea" name="pcontent" id="pcontent" style="font-family:Verdana;font-size:100%;width:94%;height:4.5em;margin:0 auto 0.3em auto;"></textarea></div>';
        if(jieqiUserInfo.jieqiCodePost) output += '<div style="margin-bottom: 0.3em;text-align: left;text-indent: 3%;">验证码：<input type="text" class="text" size="8" maxlength="8" name="checkcode" onfocus="if($_(\'p_imgccode\').style.display == \'none\'){$_(\'p_imgccode\').src = \'/checkcode.php\';$_(\'p_imgccode\').style.display = \'\';}" title="点击显示验证码"><img id="p_imgccode" src="" style="cursor:pointer;vertical-align:middle;margin-left:3px;display:none;" onclick="this.src=\'/checkcode.php?rand=\'+Math.random();" title="点击刷新验证码"></div>';
        output += '<input type="button" name="Submit" class="button" value="发表评论" style="cursor:pointer;" onclick="Ajax.Request(\'frmreview\',{onComplete:function(){ReadTools.ShowTip(this.response);}});">\
<input type="hidden" name="act" id="act" value="newpost" />\
</form></div>';
        output += '<div id="givetip" class="givetip" style="display:none;">\
        <dl>\
        <dt>请选择打赏虚拟币金额</dt>';
        for (i = 0; i < this.tipegold.length; i++) {
            output += ' <dd onclick="ReadTools.GiveTip(' + this.tipegold[i] + ')">' + this.tipegold[i] + '</dd>';
        }
        output += '</dl>\
        </div>';
        output += '<div id="readtip" class="readtip" style="display:none;">\
        </div>';

        document.write(output);
    },
    SetCookies: function (cookieName, cookieValue, expirehours) {
        var today = new Date();
        var expire = new Date();
        expire.setTime(today.getTime() + 3600000 * 356 * 24);
        document.cookie = cookieName + '=' + escape(cookieValue) + ';expires=' + expire.toGMTString() + '; path=/';
    },
    ReadCookies: function (cookieName) {
        var theCookie = '' + document.cookie;
        var ind = theCookie.indexOf(cookieName);
        if (ind == -1 || cookieName == '') return '-1';
        var ind1 = theCookie.indexOf(';', ind);
        if (ind1 == -1) ind1 = theCookie.length;
        return unescape(theCookie.substring(ind + cookieName.length + 1, ind1));
    },
    SaveSet: function () {
        this.SetCookies("colorid", ReadTools.colorid);
        this.SetCookies("fontid", ReadTools.fontid);
        this.SetCookies("isnight", ReadTools.isnight);
        this.SetCookies("sspeed", ReadTools.sspeed);
    },
    LoadSet: function () {
        var id = 0;
        var isn = 0;

        isn = parseInt(this.ReadCookies("isnight"));
        if (isn == 1) {
            this.SetNight();
        }

        id = parseInt(this.ReadCookies("colorid"));
        if(id >= 0 && id < this.bgcolor.length) ReadTools.colorid = id;
        if (isn == 0) this.SetColor(ReadTools.colorid);

        id = parseInt(this.ReadCookies("fontid"));
        if(id >= 0 && id < this.fontsize.length) ReadTools.fontid = id;
        this.SetFont(ReadTools.fontid);

        id = parseInt(this.ReadCookies("sspeed"));
        if(id > 0 && id <= 10) ReadTools.sspeed = id;

    }
};

document.getElementById(ReadTools.contentid).onclick = ReadTools.CallTools;
ReadTools.LoadSet();
ReadTools.Show();