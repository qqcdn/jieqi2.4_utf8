//目录页显示最近阅读书签标志，需要先加载 /scripts/json2.js

var hisStorageName = 'jieqiHistoryBooks'; //变量名
var hisStorageValue = Storage.get(hisStorageName); //读取记录
var hisBookAry = []; //记录数组

try{
	hisBookAry = JSON.parse(hisStorageValue);
	if(!hisBookAry) hisBookAry = [];
}catch(e){
}

//如果有记录最近阅读章节，显示书签标志
var cid = 0;
if(hisBookAry.length > 0){
	for(var i = hisBookAry.length - 1; i >= 0; i--){
		if(hisBookAry[i].articleid == ReadParams.articleid){
			if(hisBookAry[i].chapterid > 0){
				var chaps = $_("chapterindex").getElementsByTagName("dd");
				if(chaps.length > 0){
					for(var j = 0; j < chaps.length; j++){
						if(chaps[j].id == "cid_" + hisBookAry[i].chapterid){
							chaps[j].className = "readmark";
							break;
						}
					}
				}
			}
			break;
		}
	}
}