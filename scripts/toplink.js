var jieqiUserInfo = {jieqiUserId:0, jieqiUserName:'', jieqiUserPassword:'', jieqiUserToken:'', jieqiUserGroup:0, jieqiNewMessage:0, jieqiCodeLogin:0, jieqiCodePost:0};

function get_cookie_value(n) {
    var dc = '; ' + document.cookie + '; ';
    var coo = dc.indexOf('; ' + n + '=');
    if (coo != -1) {
        var s = dc.substring(coo + n.length + 3, dc.length);
        return unescape(s.substring(0, s.indexOf('; ')));
    } else {
        return null;
    }
}

if (document.cookie.indexOf('jieqiUserInfo') >= 0) {
    var cinfo = get_cookie_value('jieqiUserInfo');
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

if (jieqiUserInfo.jieqiUserId != 0 && jieqiUserInfo.jieqiUserName != '' && (document.cookie.indexOf('PHPSESSID') != -1 || jieqiUserInfo.jieqiUserPassword != '')) {
    var html = '<ul class="topnav">\
    <li><strong>' + jieqiUserInfo.jieqiUserName + '：</strong></li>\
<li><a href="/message.php?box=inbox" class="droplink"><i class="iconfont">&#xee36;</i>消息</a>';
    if (jieqiUserInfo.jieqiNewMessage > 0) {
        html += '<sup>' + jieqiUserInfo.jieqiNewMessage + '</sup>';
    }
    html += '</li>\
    <li class="dropdown"><a href="/userdetail.php" class="droplink"><i class="iconfont">&#xee21;</i>会员<b></b></a>\
    <ul class="droplist">\
    <li><a href="/modules/article/bookcase.php">我的书架</a></li>\
    <li><a href="/modules/obook/buylist.php">我的订阅</a></li>\
    <li><a href="/modules/pay/buyegold.php">帐户充值</a></li>\
    <li><a href="/logout.php">退出登录</a></li>\
    </ul>\
    </li>\
    <li><a href="/modules/article/bookcase.php" class="droplink"><i class="iconfont">&#xee43;</i>书架</a></li>\
    <li><a href="/modules/pay/buyegold.php" class="droplink"><i class="iconfont">&#xee3c;</i>充值</a></li>\
    <li><a href="/logout.php" class="droplink"><i class="iconfont">&#xee2a;</i>退出</a></li>\
    </ul>';
    document.write(html);
} else {
    var jumpurl = "";
    if (location.href.indexOf("jumpurl") == -1) {
        jumpurl = location.href;
    }
    var html = '<div class="fl">\
    <form name="t_frmlogin" id="t_frmlogin" method="post" action="/login.php">\
    &nbsp;用户名：<input type="text" class="text t_s" size="10" maxlength="30" style="width:70px;" name="username" onKeyPress="javascript: if (event.keyCode==32) return false;">\
    密码：<input type="password" class="text t_s" size="10" maxlength="30" style="width:70px;" name="password">\
        <!--\
        验证码：<input type="text" class="text t_s" size="4" maxlength="8" style="width:35px;" name="checkcode" onfocus="if($_(\'t_imgccode\').style.display == \'none\'){$_(\'t_imgccode\').src = \'/checkcode.php\';$_(\'t_imgccode\').style.display = \'\';}" title="点击显示验证码"><img id="t_imgccode" src="" style="cursor:pointer;vertical-align:middle;margin-left:3px;display:none;" onclick="this.src=\'/checkcode.php?rand=\'+Math.random();" title="点击刷新验证码">\
        -->\
    <input type="checkbox" class="checkbox" name="usecookie" value="1" />记住\
    <input type="button" class="button b_s" value="登录" name="t_btnlogin" id="t_btnlogin" onclick="Ajax.Tip(\'t_frmlogin\', {timeout:3000, onLoading:\'登录中...\', onComplete:\'登录成功，页面跳转中...\'});">\
    <input type="hidden" name="act" value="login">\
    <input type="hidden" name="jumpreferer" value="1">\
    <!--\
    <a href="/api/qq/login.php" rel="nofollow" target="_top"><img src="/images/api/qq_ico.gif" title="用QQ账号登录" style="border:0;vertical-align:middle;"></a>\
    <a href="/api/weibo/login.php" rel="nofollow" target="_top"><img src="/images/api/weibo_ico.gif" title="用新浪微博账号登录" style="border:0;vertical-align:middle;"></a>\
    <a href="/api/taobao/login.php" rel="nofollow" target="_top"><img src="/images/api/taobao_ico.gif" title="用淘宝账号登录" style="border:0;vertical-align:middle;"></a>\
    -->\
    </form>\
    </div>\
    <div class="fr">\
        <a class="hot" href="/register.php" onclick="openDialog(\'/register.php?ajax_gets=jieqi_contents\', false);stopEvent();">注册用户</a>\
         ┊ <a class="hot" href="/getpass.php" onclick="openDialog(\'/getpass.php?ajax_gets=jieqi_contents\', false);stopEvent();">忘记密码？</a>\
    </div>';
    document.write(html);
}
