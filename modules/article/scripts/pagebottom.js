document.write('<div class="copyright">作品本身仅代表作者本人的观点，与本站立场无关。如因而由此导致任何法律问题或后果，本站均不负任何责任。</div>');

document.oncontextmenu = function(){return false;};
document.ondragstart = function(){return false;};
document.onselectstart = function(){return false;};
document.onbeforecopy = function(){return false;};
document.onselect = function(){window.getSelection ? window.getSelection().empty() : document.selection.empty();};
document.oncopy = function(){window.getSelection ? window.getSelection().empty() : document.selection.empty();};

//把2个英文空格换成1个全角空格
addEvent(window, 'load', function(){document.getElementById('acontent').innerHTML = document.getElementById('acontent').innerHTML.replace(/&nbsp;&nbsp;/g, '&emsp;');});