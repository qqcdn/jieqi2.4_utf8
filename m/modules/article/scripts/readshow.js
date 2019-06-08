//显示最近阅读记录，需要先加载 /scripts/json2.js

var hisStorageName = 'jieqiHistoryBooks'; //变量名
var hisStorageValue = Storage.get(hisStorageName); //读取记录
var hisBookAry = []; //记录数组

try{
	hisBookAry = JSON.parse(hisStorageValue);
	if(!hisBookAry) hisBookAry = [];
}catch(e){
}

//显示最近阅读
if(hisBookAry.length > 0){
	var htmlStr;
	for(var i = hisBookAry.length - 1; i >= 0; i--){
		htmlStr = '<div class="c_row cf"><a class="db" href="';
		htmlStr += parseInt(hisBookAry[i].chapterisvip) > 0 ? '/modules/obook/reader.php' : '/modules/article/reader.php';
		htmlStr += '?aid=' + hisBookAry[i].articleid + '&cid=' + hisBookAry[i].chapterid + '"><p class="nw"><span class="pop">' + hisBookAry[i].articlename + '</span>';
		if(hisBookAry[i].chapterid > 0) htmlStr += '&nbsp;<span class="gray">' + hisBookAry[i].chaptername+'</span>';
		htmlStr += '</p></a></div>';
		document.write(htmlStr);
	}
}else{
	document.write('<div class="blockc mt mb"><div class="blockcontent fsl pd tc"><div class="mb">您还没有阅读记录</div><a class="btnlink b_hot" href="/modules/article/articlefilter.php">去书库看书</a></div></div>');
}

//删除一本书
function hisBookRemove(aid){
	var o = arguments.length > 1 ? arguments[1] : getTarget();
	for(var i = 0; i < hisBookAry.length; i++){
		if(hisBookAry[i].articleid == aid){
			hisBookAry.splice(i, 1);
			hisStorageValue = JSON.stringify(hisBookAry);
			Storage.set(hisStorageName, hisStorageValue);
			o.parentNode.parentNode.parentNode.removeChild(o.parentNode.parentNode);
			break;
		}
	}
}