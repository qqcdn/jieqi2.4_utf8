//查找所有图片链接
var divimgs = new Array(); 
function imgsearch(){
	var divs = document.getElementsByTagName('div');
	var j = 0;
	for (i=0; i < divs.length; i++){
		if(divs[i].className == 'divimage'){
			divimgs[j]=divs[i];
			j++;
		}
	}
}

//点击链接显示图片
function imgclickshow(id, url){
	 if(document.getElementById(id).innerHTML.toLowerCase().indexOf('<img') == -1) document.getElementById(id).innerHTML = '<img src="' + url + '" border="0" class="imagecontent" />';
}

//自动显示图片
function imgautoshow() {
	var documentTop = document.documentElement.scrollTop|| document.body.scrollTop;
	var docHeight = document.documentElement.clientHeight|| document.body.clientHeight;
	for (i=0; i < divimgs.length; i++){
		if(documentTop > divimgs[i].offsetTop - docHeight - docHeight && documentTop < divimgs[i].offsetTop + divimgs[i].offsetHeight  && divimgs[i].innerHTML.toLowerCase().indexOf('<img') == -1){
			divimgs[i].innerHTML = '<img src="' + divimgs[i].title + '" border="0" class="imagecontent" />';
		}
	}
	setTimeout("imgautoshow()", 300);
}

//内容图片显示处理
function imgcontentinit(){
	imgsearch();
	imgautoshow();
}

//载入图片显示函数
if (document.all){
	window.attachEvent('onload',imgcontentinit);
}else{
	window.addEventListener('load',imgcontentinit,false);
}