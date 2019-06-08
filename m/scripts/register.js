//发送Email验证码
var sendemailset = {
	id: 'btnemailrand',
	eid: 'email',
	wait: 60,
	count: this.wait
}
function sendemailrand(ele){
	var email = document.getElementById(sendemailset.eid);
	if(email.value == ''){
		alert('请先输入Email地址！');
		email.focus();
	}else {
		Ajax.Request('/emailverify.php?sendemail=1&type=randcode&email=' + email.value, {onLoading: sendemailloading, onComplete: sendemailcomplete});
	}
}
function sendemailloading(){
	document.getElementById(sendemailset.id).value = '正在发送...';
}
function sendemailcomplete(ele){
	if(this.response.indexOf('成功') != -1){
		sendemailset.count = sendemailset.wait;
		document.getElementById(sendemailset.id).setAttribute("disabled", true);
		setTimeout(sendemailwait, 1000);
	}else{
		alert(this.response.replace(/<br[^<>]*>/g,'\n'));
		document.getElementById(sendemailset.id).value = '发送验证码';
	}
}

function sendemailwait(){
	var ele = document.getElementById(sendemailset.id);
	if (sendemailset.count <= 0) {
		ele.removeAttribute("disabled");
		ele.value="发送验证码";
	} else {
		ele.value="重新发送(" + sendemailset.count + ")";
		sendemailset.count--;
		setTimeout(sendemailwait, 1000);
	}
}