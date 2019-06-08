//从cookie获取用户登录信息
var jieqiUserInfo = new  Array(); //用户信息数组
jieqiUserInfo['jieqiUserId'] = 0; //用户ID
jieqiUserInfo['jieqiUserUname'] = ''; //用户账号
jieqiUserInfo['jieqiUserUname_un'] = ''; //UNICODE编码的用户账号
jieqiUserInfo['jieqiUserName'] = ''; //用户名（昵称）
jieqiUserInfo['jieqiUserName_un'] = ''; //UNICODE编码的用户名（昵称）
jieqiUserInfo['jieqiUserGroup'] = 0; //用户组ID
jieqiUserInfo['jieqiUserGroupName'] = ''; //用户组
jieqiUserInfo['jieqiUserGroupName_un'] = ''; //UNICODE编码的用户组
jieqiUserInfo['jieqiUserVip'] = 0; //VIP等级 
jieqiUserInfo['jieqiUserHonorId'] = 0; //头衔ID
jieqiUserInfo['jieqiUserHonor'] = ''; //头衔
jieqiUserInfo['jieqiUserHonor_un'] = ''; //UNICODE编码的头衔
jieqiUserInfo['jieqiNewMessage'] = 0; //新消息数量，默认 0 
jieqiUserInfo['jieqiUserPassword'] = ''; //用户密码（MD5后的值）

//读取COOKIE，解析后赋值到数组
if(document.cookie.indexOf('jieqiUserInfo') >= 0){
	var cookieInfo = get_cookie_value('jieqiUserInfo');
	start = 0;
	offset = cookieInfo.indexOf(',', start); 
	while(offset > 0){
		tmpval = cookieInfo.substring(start, offset);
		tmpidx = tmpval.indexOf('=');
		if(tmpidx > 0){
           tmpname = tmpval.substring(0, tmpidx);
		   tmpval = tmpval.substring(tmpidx+1, tmpval.length);
		   jieqiUserInfo[tmpname] = tmpval;
		}
		start = offset+1;
		if(offset < cookieInfo.length){
		  offset = cookieInfo.indexOf(',', start); 
		  if(offset == -1) offset =  cookieInfo.length;
		}else{
          offset = -1;
		}
	}
}

//读取COOKIE函数
function get_cookie_value(Name) {
	var search = Name + "=";
	var returnvalue = ""; 
	if (document.cookie.length > 0) {
		offset = document.cookie.indexOf(search);
		if (offset != -1) {
			offset += search.length;
			end = document.cookie.indexOf(";", offset);
			if (end == -1) end = document.cookie.length;
			returnvalue = unescape(document.cookie.substring(offset, end));
		}
	}
	return returnvalue; 
}

//自定义处理代码
/*
if(jieqiUserInfo['jieqiUserId'] > 0 && document.cookie.indexOf('PHPSESSID') != -1){
	document.write('已经登录');
}else{
	document.write('还未登录');
}
*/