//章节页面保存阅读记录，需要先加载 /scripts/json2.js

var hisStorageName = 'jieqiHistoryBooks'; //变量名
var hisStorageValue = Storage.get(hisStorageName); //读取记录
var hisBookAry = []; //记录数组
var hisBookMax = 20; //最多保留几条阅读记录
var hisBookIndex = -1; //当前作品的数组下标

try{
	hisBookAry = JSON.parse(hisStorageValue);
	if(!hisBookAry) hisBookAry = [];
}catch(e){
}

for(var i = 0; i < hisBookAry.length; i++){
	if(hisBookAry[i].articleid == ReadParams.articleid){
		hisBookIndex = i;
		break;
	}
}

if(hisBookIndex < 0){
	//新的书加入阅读记录，如果记录已达到最大值，先删除一条
	if(hisBookAry.length >= hisBookMax){
		hisBookAry.shift();
	}
	hisBookAry.push(ReadParams);
	hisStorageValue = JSON.stringify(hisBookAry);
	Storage.set(hisStorageName, hisStorageValue);
}else if(ReadParams.chapterid > 0){
	//书已经存在，判断章节是否需要更新
	hisBookAry[hisBookIndex].chapterid = ReadParams.chapterid;
	hisBookAry[hisBookIndex].chaptername = ReadParams.chaptername;
	hisBookAry[hisBookIndex].chapterisvip = ReadParams.chapterisvip;
	hisStorageValue = JSON.stringify(hisBookAry);
	Storage.set(hisStorageName, hisStorageValue);
}