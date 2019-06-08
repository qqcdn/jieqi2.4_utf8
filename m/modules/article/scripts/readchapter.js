//阅读章节和阅读书架自动判断最新阅读，需要先加载 /scripts/json2.js

var hisStorageName = 'jieqiHistoryBooks'; //变量名
var hisStorageValue = Storage.get(hisStorageName); //读取记录
var hisBookAry = []; //记录数组

try{
	hisBookAry = JSON.parse(hisStorageValue);
	if(!hisBookAry) hisBookAry = [];
}catch(e){
}

//如果有记录最近阅读章节就跳转到对应章节，否则显示第一章
function read_chapter(aid){
	var idx = -1;
	if(hisBookAry.length > 0){
		for(var i = hisBookAry.length - 1; i >= 0; i--){
			if(hisBookAry[i].articleid == aid && hisBookAry[i].chapterid > 0){
				idx = i;
				break;
			}
		}
	}
	if(idx >= 0){
		if(parseInt(hisBookAry[idx].chapterisvip) > 0) window.location.href = '/modules/obook/reader.php?aid=' + aid + '&cid=' + hisBookAry[idx].chapterid;
		else window.location.href = '/modules/article/reader.php?aid=' + aid + '&cid=' + hisBookAry[idx].chapterid;
	}else{
		window.location.href = '/modules/article/firstchapter.php?aid=' + aid;
	}
}

//针对书架的继续阅读
function read_bookcase(aid, cid, bid){
	if(hisBookAry.length > 0){
		for(var i = hisBookAry.length - 1; i >= 0; i--){
			if(hisBookAry[i].articleid == aid){
				if(hisBookAry[i].chapterid > 0) cid = hisBookAry[i].chapterid;
				break;
			}
		}
	}
	if(cid > 0){
		window.location.href = '/modules/article/readbookcase.php?bid=' + bid + '&aid=' + aid + '&cid=' + cid;
	}else{
		window.location.href = '/modules/article/firstchapter.php?aid=' + aid;
	}
}