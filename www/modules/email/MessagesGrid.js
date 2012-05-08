/** 
 * Copyright Intermesh
 * 
 * This file is part of Group-Office. You should have received a copy of the
 * Group-Office license along with Group-Office. See the file /LICENSE.TXT
 * 
 * If you have questions write an e-mail to info@intermesh.nl
 * 
 * @version $Id$
 * @copyright Copyright Intermesh
 * @author Merijn Schering <mschering@intermesh.nl>
 */
 
GO.email.MessagesGrid = function(config){
	
	if(!config)
	{
		config = {};
	}
	
	config.layout='fit';
	config.autoScroll=true;
	config.paging=true;

	if(config.region=='north')
	{
		this.searchtypeWidth = 150;
		this.searchfieldWidth = 320;
		config.cm = new Ext.grid.ColumnModel({
		defaults:{
			sortable:true
			},
			columns:[
			{
				header:"&nbsp;",
				width:50,
				dataIndex: 'icon',
				renderer: this.renderIcon,
				hideable:false,
				sortable:false
			},{
				header: GO.email.lang.from,
				dataIndex: 'from',
				id:'from',
				width:200
			},{
				header: GO.email.lang.subject,
				dataIndex: 'subject',
				width:200
			},{
				header: GO.lang.strDate,
				dataIndex: 'date',
				width:65,
				align:'right'
			},{
				header: GO.lang.strSize,
				dataIndex: 'size',
				width:65,
				align:'right',
				hidden:true,
				renderer:Ext.util.Format.fileSize
			}]
		});
		config.view=new Ext.grid.GridView({
			emptyText: GO.lang['strNoItems'],
			getRowClass:function(row, index) {
				if (row.data['seen'] == '0') {
					return 'ml-new-row';
				}
			}
		});
	
	}else
	{
		this.searchtypeWidth = 120;
		this.searchfieldWidth = 150;
		config.cm =  new Ext.grid.ColumnModel({
		defaults:{
			sortable:true
		},
		columns:[
		{
			header:"&nbsp;",
			width:46,
			dataIndex: 'icon',
			renderer: this.renderIcon,
			hideable:false,
			sortable:false
		},{
			header: GO.email.lang.message,
			dataIndex: 'from',
			renderer: this.renderMessage,
			css: 'white-space:normal;',
			id:'message'
		
		},{
			header: GO.lang.strDate,
			dataIndex: 'date',
			width:65,
			align:'right'
		},{
				header: GO.lang.strSize,
				dataIndex: 'size',
				width:65,
				align:'right',
				hidden:true,
				renderer:Ext.util.Format.fileSize
			}]
		});
		config.bbar = new Ext.PagingToolbar({
			cls: 'go-paging-tb',
			store: config.store,
			pageSize: parseInt(GO.settings['max_rows_list']),
			displayInfo: true,
			displayMsg: GO.lang.displayingItemsShort,
			emptyMsg: GO.lang['strNoItems']
		});
				
		config.autoExpandColumn='message';
		
		config.view=new Ext.grid.GridView({
			emptyText: GO.lang['strNoItems']
		});
	}
	
	config.sm=new Ext.grid.RowSelectionModel();
	config.loadMask=true;
			
	config.border=false;
	config.split= true;
	config.header=false;
	config.enableDragDrop= true;
	config.ddGroup = 'EmailDD';
	config.animCollapse=false;

	this.searchType = new GO.form.ComboBox({
		width:this.searchtypeWidth,
		store: new Ext.data.SimpleStore({
			fields: ['value', 'text'],
			data : [
			['any', GO.email.lang.anyField],
			['from', GO.email.lang.searchFrom],
			['subject', GO.email.lang.subject],
			['to', GO.email.lang.searchTo],
			['cc', GO.email.lang.searchCC]
			]
		}),
		value:GO.email.search_type_default,
		valueField:'value',
		displayField:'text',
		mode:'local',
		triggerAction:'all',
		editable:false,
		selectOnFocus:true,
		forceSelection:true
	});

	this.searchField = new GO.form.SearchField({
		store: config.store,
		paramName:'search',
		emptyText:GO.lang['strSearch'],
		width:this.searchfieldWidth
	});
        
	this.showUnreadButton = new Ext.Button({
		text:GO.email.lang.showUnread,
		enableToggle:true,
		toggleHandler:this.toggleUnread,
		pressed:false,
		style:'margin-left:10px'
	});

	if(!config.hideSearch)
	{
		config.tbar = [this.searchType, this.searchField, this.showUnreadButton];
	}	
	
	GO.email.MessagesGrid.superclass.constructor.call(this, config);

	var origRefreshHandler = this.getBottomToolbar().refresh.handler;

	this.getBottomToolbar().refresh.handler=function(){
		this.store.baseParams.refresh=true;
		origRefreshHandler.call(this);
		delete this.store.baseParams.refresh;
	};

	this.searchType.on('select', function(combo, record)
	{
		GO.email.search_type = record.data.value;

		if(this.searchField.getValue())
		{
			GO.email.messagesGrid.store.baseParams['search'] = this.searchField.getValue();
			this.searchField.hasSearch = true;
                        
			GO.email.messagesGrid.store.reload();
		}
                
	}, this);
        
};

Ext.extend(GO.email.MessagesGrid, GO.grid.GridPanel,{
        
	show : function()
	{
		if(GO.email.messagesGrid.store.baseParams['unread'] != undefined)
		{
			this.showUnreadButton.toggle(GO.email.messagesGrid.store.baseParams['unread']);
		}

		if(!GO.email.search_type)
		{
			GO.email.search_type = GO.email.search_type_default;
		}
		this.setSearchFields(GO.email.search_type, GO.email.search_query);

		GO.email.MessagesGrid.superclass.show.call(this);
	},
	resetSearch : function()
	{
		GO.email.search_type = GO.email.search_type_default;
		GO.email.search_query = '';
                
		this.setSearchFields(GO.email.search_type, GO.email.search_query);
	},
	setSearchFields : function(type, query)
	{
		this.searchType.setValue(type);
		this.searchField.setValue(query);

		this.searchField.hasSearch = (query) ? true : false;
	},
	toggleUnread : function(item, pressed)
	{
		GO.email.messagesGrid.store.baseParams['unread']=pressed;
                
		GO.email.messagesGrid.store.load();
	},
	renderMessageSmallRes : function(value, p, record){
		
		if(record.data['seen']=='0')
		{
			return String.format('<div id="sbj_'+record.data['uid']+'" class="NewSubject">{0}</div>{1}', value, record.data['subject']);
		}else
		{
			return String.format('<div id="sbj_'+record.data['uid']+'" class="Subject">{0}</div>{1}', value, record.data['subject']);
		}
	},
	
	renderMessage : function(value, p, record){
		if(record.data['seen']=='0')
		{
			return String.format('<div id="sbj_'+record.data['uid']+'" class="NewSubject">{0}</div>{1}', value, record.data['subject']);
		}else
		{
			return String.format('<div id="sbj_'+record.data['uid']+'" class="Subject">{0}</div>{1}', value, record.data['subject']);
		}
	},
	renderIcon : function(src, p, record){
		var str = '';

		var cls = "email-grid-icon ";

		if(record.data.answered=='1' && record.data.forwarded=='1')
		{
			cls += "btn-message-answered-and-forwarded";
		}else if(record.data.answered=='1'){
			cls += "btn-message-answered";
		}else if(record.data.forwarded=='1'){
			cls += "btn-message-forwarded";
		}else
		{
			cls += "btn-message";
		}
		str += '<div class="'+cls+'"></div>';
		
		if(record.data['has_attachments']=='1')
		{
			str += '<div class="email-grid-icon ml-icon-attach"></div>';
		//str += '<img src=\"' + GOimages['attach'] +' \" style="display:block" />';
		}else
		{
		//str += '<br />';
		}
		
		if(record.data['priority'])
		{
			if(record.data['priority'] < 3)
			{
				str += '<div class="email-grid-icon btn-high-priority"></div>';
			}
			
			if(record.data['priority'] > 3)
			{
				str += '<div class="email-grid-icon btn-low-priority"></div>';
			}
		}
		
		if(record.data['flagged']==1)
		{
			//str += '<img src=\"' + GOimages['flag'] +' \" style="display:block" />';
			str += '<div class="email-grid-icon btn-flag"></div>';
		}
		
		return str;
		
	},

		

	renderFlagged : function(value, p, record){

		var str = '';

		if(record.data['flagged']==1)
		{
			//str += '<img src=\"' + GOimages['flag'] +' \" style="display:block" />';
			str += '<div class="go-icon btn-flag"></div>';
		}
		if(record.data['attachments'])
		{
			str += '<div class="go-icon btn-attach"></div>';
		//str += '<img src=\"' + GOimages['attach'] +' \" style="display:block" />';
		}
		return str;

	}
});
