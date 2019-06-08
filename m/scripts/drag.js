/**
 * Base class of Drag
 * @example:
 * Drag.init( header_element, element );
 */
var Drag = {
	// 对这个element的引用，一次只能拖拽一个Element
	obj: null , 
	/**
	 * @param: elementHeader	used to drag..
	 * @param: element			used to follow..
	 */
	init: function(elementHeader, element) {
		// 将 start 绑定到 onmousedown/ontouchstart 事件，按下鼠标触发 start
		elementHeader.onmousedown = Drag.start;
		elementHeader.addEventListener("touchstart", Drag.start, false);

		// 将 element 存到 header 的 obj 里面，方便 header 拖拽的时候引用
		elementHeader.obj = element;
		// 初始化绝对的坐标，因为不是 position = absolute 所以不会起什么作用，但是防止后面 onDrag 的时候 parse 出错了
		if(isNaN(parseInt(element.style.left))) {
			element.style.left = "0px";
		}
		if(isNaN(parseInt(element.style.top))) {
			element.style.top = "0px";
		}
		// 挂上空 Function，初始化这几个成员，在 Drag.init 被调用后才帮定到实际的函数
		element.onDragStart = new Function();
		element.onDragEnd = new Function();
		element.onDrag = new Function();
	},
	// 开始拖拽的绑定，绑定到鼠标的移动的 event 上
	start: function(event) {
		var element = Drag.obj = this.obj;
		// 解决不同浏览器的 event 模型不同的问题
		event = Drag.fixE(event);
		
		// 看看是不是左键点击
		if(typeof event.which != "undefined" && event.which != 1){
			// 除了左键都不起作用
			return true ;
		}
		// 参照这个函数的解释，挂上开始拖拽的钩子
		element.onDragStart();
		// 记录鼠标坐标
		element.lastMouseX = event.clientX;
		element.lastMouseY = event.clientY;
		// 绑定事件
		document.onmouseup = Drag.end;
		document.addEventListener("touchend", Drag.end, false);
		document.onmousemove = Drag.drag;
		document.addEventListener("touchmove", Drag.drag, false);
		document.addEventListener("touchmove", Drag.defaultE, false);
		return false ;
	}, 
	// Element正在被拖动的函数
	drag: function(event) {
		event = Drag.fixE(event);
		if(typeof event.which != "undefined" && event.which == 0 ) {
		 	return Drag.end();
		}
		// 正在被拖动的Element
		var element = Drag.obj;
		// 鼠标坐标
		var _clientX = event.clientY;
		var _clientY = event.clientX;
		// 如果鼠标没动就什么都不作
		if(element.lastMouseX == _clientY && element.lastMouseY == _clientX) {
			return	false ;
		}
		// 刚才 Element 的坐标
		var _lastX = parseInt(element.style.top);
		var _lastY = parseInt(element.style.left);
		// 新的坐标
		var newX, newY;
		// 计算新的坐标：原先的坐标+鼠标移动的值差
		newX = _lastY + _clientY - element.lastMouseX;
		newY = _lastX + _clientX - element.lastMouseY;
		// 修改 element 的显示坐标
		element.style.left = newX + "px";
		element.style.top = newY + "px";
		// 记录 element 现在的坐标供下一次移动使用
		element.lastMouseX = _clientY;
		element.lastMouseY = _clientX;
		// 参照这个函数的解释，挂接上 Drag 时的钩子
		element.onDrag(newX, newY);
		return false;
	},
	// Element 正在被释放的函数，停止拖拽
	end: function(event) {
		event = Drag.fixE(event);
		// 解除事件绑定
		document.onmousemove = null;
		document.onmouseup = null;
		document.removeEventListener("touchmove", Drag.drag, false);
		document.removeEventListener("touchend", Drag.end, false);
		document.removeEventListener("touchmove", Drag.defaultE, false);
		// 先记录下 onDragEnd 的钩子，好移除 obj
		var _onDragEndFuc = Drag.obj.onDragEnd();
		// 拖拽完毕，obj 清空
		Drag.obj = null ;
		return _onDragEndFuc;
	},
	// 解决不同浏览器的 event 模型不同的问题
	fixE: function(evt) {
		if( typeof evt == "undefined" ) {
			evt = window.event;
		}
		if(typeof evt.targetTouches != "undefined"){
			var touch = evt.targetTouches[0];
			return touch;
		}
		if( typeof evt.which == "undefined" ) {
			evt.which = evt.button;
		}
		return evt;
	},
	defaultE: function(e){
		e.preventDefault();
	}
};