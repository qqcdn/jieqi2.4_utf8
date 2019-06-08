// 要保存的内容对象FormContent
var FormContent = document.getElementById("content");
// 显示返回信息的对象
var AutoSaveMsg = document.getElementById("AutoSaveMsg");
// 自动保存时间间隔
var AutoSaveTime = 60000;
// 计时器对象
var AutoSaveTimer;
// 首先设置一次自动保存状态
SetAutoSave();
// 自动保存函数
function AutoSave(){
// 如果内容为空，则不进行处理，直接返回
if(!FormContent.value) return;
// 创建AJAXRequest对象，
var ajaxobj=new AJAXRequest;
ajaxobj.url="inc/autosave.asp";
ajaxobj.content="postcontent="+escape(FormContent.value);
ajaxobj.callback=function(xmlObj) 
{
// 显示反馈信息
AutoSaveMsg.innerHTML=xmlObj.responseText;
}
ajaxobj.send();
}


// 设置自动保存状态函数
function SetAutoSave() 
{
AutoSaveTimer=setInterval("AutoSave()",AutoSaveTime);
}

// 恢复最后保存的草稿
function AutoSaveRestore()
{
// 创建AJAXRequest对象
var ajaxobj=new AJAXRequest;
AutoSaveMsg.innerHTML="正在恢复，请稍候……"
ajaxobj.url="inc/autosave.asp";
ajaxobj.content="action=restore";
ajaxobj.callback=function(xmlObj)
{
AutoSaveMsg.innerHTML="恢复最后保存成功";
// 如果内容为空则不改写textarea的内容
if(xmlObj.responseText!="")
{
// 恢复草稿
FormContent.value=xmlObj.responseText;
}
}
ajaxobj.send()
}