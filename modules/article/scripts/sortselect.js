var SortSelect = {
	elements : {union : null, rgroup : null, sort : null,	type : null},
	init : function(){
		this.elements.union = document.getElementById(ArticleSorts.tags.union);

		var html = '';
		var items = 0;
        for(var rgroup in ArticleSorts.rgroups){
	        if(ArticleSorts.values.rgroup == 0) ArticleSorts.values.rgroup = rgroup;
	        html += '<option value="' + rgroup + '"';
	        if(rgroup == ArticleSorts.values.rgroup) html += ' selected="selected"';
	        html += '>' + ArticleSorts.rgroups[rgroup] + '</option>';
	        items++;
		}
		if(items > 0){
			html = '<select class="select" size="1" onchange="SortSelect.showSorts(this.options[this.options.selectedIndex].value)" name="rgroupid" id="rgroupid">' + html + '</select>';
			this.elements.rgroup = document.createElement('span');
        	this.elements.rgroup.id = ArticleSorts.tags.rgroup + '_box';
        	this.elements.union.appendChild(this.elements.rgroup);
			this.elements.rgroup.innerHTML = html;
		}
		this.showSorts();
		
	},
	showSorts : function(){
		var html = '';
		var items = 0;
		if(arguments.length > 0 && arguments[0] != 0 && ArticleSorts.values.rgroup != arguments[0]){
			ArticleSorts.values.rgroup = arguments[0];
			ArticleSorts.values.sort = 0;
			ArticleSorts.values.type = 0;
		}
        for(var sort in ArticleSorts.sorts){
	        if(ArticleSorts.values.rgroup == 0 || ArticleSorts.values.rgroup == ArticleSorts.sorts[sort].group){
	        	if(ArticleSorts.values.sort == 0) ArticleSorts.values.sort = sort;
	        	html += '<option value="' + sort + '"';
	        	if(sort == ArticleSorts.values.sort) html += ' selected="selected"';
	        	html += '>' + ArticleSorts.sorts[sort].caption + '</option>';
	        	items++;
        	}
		}
		if(items > 0){
			html = '<select class="select" size="1" onchange="SortSelect.showTypes(this.options[this.options.selectedIndex].value)" name="sortid" id="sortid">' + html + '</select>';
			if(!this.elements.sort){
				this.elements.sort = document.createElement('span');
        		this.elements.sort.id = ArticleSorts.tags.sort + '_box';
        		this.elements.union.appendChild(this.elements.sort);
    		}
			this.elements.sort.innerHTML = html;
			this.showTypes();
		}else if(this.elements.sort){
			this.elements.sort.innerHTML = '';
		}
	},
	showTypes : function(){
		if(ArticleSorts.values.sort > 0){
			var html = '';
			var items = 0;
			if(arguments.length > 0 && arguments[0] != 0 && ArticleSorts.values.sort != arguments[0]){
				ArticleSorts.values.sort = arguments[0];
				ArticleSorts.values.type = 0;
			}
			var types = ArticleSorts.sorts[ArticleSorts.values.sort].types;
        	for(var type in types){
	        	if(ArticleSorts.values.type == 0) ArticleSorts.values.type = type;
	        	html += '<option value="' + type + '"';
	        	if(type == ArticleSorts.values.type) html += ' selected="selected"';
	        	html += '>' + types[type] + '</option>';
	        	items++;
			}
			if(items > 0){
				html = '<select class="select" size="1" name="typeid" id="typeid">' + html + '</select>';
				if(!this.elements.type){
					this.elements.type = document.createElement('span');
        			this.elements.type.id = ArticleSorts.tags.type + '_box';
        			this.elements.union.appendChild(this.elements.type);
    			}
				this.elements.type.innerHTML = html;
			}else if(this.elements.type){
				this.elements.type.innerHTML = '';
			}
		}else if(this.elements.type){
			this.elements.type.innerHTML = '';
		}
	}
}