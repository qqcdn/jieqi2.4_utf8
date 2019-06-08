/**
 * 无限级联动选择菜单类
 * sortValue:存放选择项值的页面元素名称
 * sortSelect:显示该菜单的页面元素名称
 * sortArray:显示菜单所需的数组，格式如下
 * sortArray[0] = ["类别ID1", "类别一", "父类ID1"];
 * sortArray[1] = ["类别ID2", "类别二", "父类ID2"];
 * sortCaption:是下拉框默认显示的字符串，如“请选择...”
 */
function sortMenu(sortValue, sortSelect, sortArray, selectSize, sortCaption)
{
	this.sortValue=document.getElementById(sortValue);
    this.sortSelect=document.getElementById(sortSelect);
	this.sortArray=sortArray;
	this.selectSize=selectSize;
	this.sortCaption=sortCaption;

	/**
	 * 获取第一层分类，并显示在sortSelect中
	 */
	this.initSorts=function()
	{
        this.sortValue.value=0;
        _select=document.createElement("select");
		this.sortSelect.insertBefore(_select,this.sortSelect.firstChild); 
        _select.sortMenuObj=this;
        _select.onchange=function()
        {
            this.sortMenuObj.setSorts(this,this.sortMenuObj);
        }
		_select.size = this.selectSize;
        _select.options.add(new Option(this.sortCaption,""));
		for (var i = 0; i < this.sortArray.length; i++)
		{
			if (this.sortArray[i][2] == 0)
			{
                _select.options.add(new Option(this.sortArray[i][1],this.sortArray[i][0]));
			}
		}		
	}

	/**
	 * 下拉框联动
	 * _curSelect:当前选择的下拉框
	 */
	this.setSorts=function(_curSelect)
	{
		//若当前下拉框后面还有下拉框，即有下级下拉框时，清除下级下拉框，在后面会重新生成下级部分
		//下级下拉框与当前下拉框由于都是显示在sortSelect中，故它们是兄弟关系，所以用nextSibling获取
		while (_curSelect.nextSibling)
		{
			_curSelect.parentNode.removeChild(_curSelect.nextSibling);
		}
		
		//获取当前选项的值
		_iValue = _curSelect.options[_curSelect.selectedIndex].value;
		//如果选择的是下拉框第一项(第一项的值为"")
		if (_iValue == "")
		{
			//若存在上级下拉框
			if (_curSelect.previousSibling)
			{
				//取值为上级下拉框选中值
				this.sortValue.value = _curSelect.previousSibling.options[_curSelect.previousSibling.selectedIndex].value;
			}
			else
			{
				//没上级则取值为0
				this.sortValue.value = 0;
			}
			//选择第一项(请选择...),没有下级选项,所以要返回
			return false;
		}
		//选择的不是第一项
		this.sortValue.value = _iValue;
		
		//去掉当前下拉框原来的选择状态
        //将选中的选项对应代码更改为 selected
        for (i=0;i<_curSelect.options.length;i++)
        {
            if (_curSelect.options[i].selected=="selected")
            {
                _curSelect.options[i].removeAttribute("selected");
            }
            if (_curSelect.options[i].value==_iValue)
            {
                _curSelect.options[i].selected="selected";
            }
        }
        //新生成的下级下拉列表
        _hasChild=false;
        for (var i = 0; i < this.sortArray.length; i++)
		{
            if (this.sortArray[i][2] == _iValue)
            {
                if (_hasChild==false)
                {
                    _siblingSelect=document.createElement("select");
                    this.sortSelect.appendChild(_siblingSelect);
                    _siblingSelect.sortMenuObj=this;
                    _siblingSelect.onchange=function()
                    {
                        this.sortMenuObj.setSorts(this,this.sortMenuObj);
                    }
					_siblingSelect.size = this.selectSize;
                    _siblingSelect.options.add(new Option(this.sortCaption,""));
                    _siblingSelect.options.add(new Option(this.sortArray[i][1],this.sortArray[i][0]));
                    _hasChild=true;
                }
                else
                {                   
                    _siblingSelect.options.add(new Option(this.sortArray[i][1],this.sortArray[i][0]));
                }
            }
        }
	}

	/**
	 * 根据最小类选取值生成整个联动菜单,由后往前递归完成
	 * _minCataValue:最小类的取值
	 */
	this.init=function(_minCataValue)
	{
        if (this.sortValue.value=="undefined" || this.sortValue.value=="")
        {
			_isExists = false;
			for (var i = 0; i < this.sortArray.length; i++)
			{
				if (_minCataValue == this.sortArray[i][0])
				{
					_isExists = true
					break;
				}
			}
			if(_isExists) this.sortValue.value=_minCataValue;
			else _minCataValue = 0;
        }
		if (_minCataValue == 0)
		{
			//minCataValue为0，也就是初始化了
			this.initSorts();
			//初始化完成后，退出函数
			return false;
		}
		//父级ID
		_parentID=null;
        _select=document.createElement("select");
        _select.sortMenuObj=this;
        _select.onchange=function()
        {
            this.sortMenuObj.setSorts(this,this.sortMenuObj);
        }
		this.sortSelect.insertBefore(_select,this.sortSelect.firstChild); 
		_select.size = this.selectSize;
        _select.options.add(new Option(this.sortCaption,""));	
		for (var i = 0; i < this.sortArray.length; i++)
		{
			if (_minCataValue == this.sortArray[i][0])
			{
				_parentID = this.sortArray[i][2];
				break;
			}
		}
		for (var i = 0; i < this.sortArray.length; i++)
		{
			if (this.sortArray[i][2] == _parentID)
			{
				if (this.sortArray[i][0] == _minCataValue)
				{
                    _opt=new Option(this.sortArray[i][1],this.sortArray[i][0]); 
                    _select.options.add(_opt);
                    _opt.selected="selected";
				}
				else					
				{
                    _select.options.add(new Option(this.sortArray[i][1],this.sortArray[i][0]));
                }
			}
		}		
		if (_parentID > 0)
		{
			this.init(_parentID);
		}
	}
}