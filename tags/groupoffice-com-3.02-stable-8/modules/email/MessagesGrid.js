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
		config.cm =  new Ext.grid.ColumnModel([
			{
				header:"&nbsp;",
				width:50,
				dataIndex: 'icon',
				renderer: this.renderIcon,
				hideable:false,
				sortable:false
			},{
				header: GO.email.lang.from,
				dataIndex: 'from'
			},{
				header: GO.email.lang.subject,
				dataIndex: 'subject'	
			},{
				header: GO.lang.strDate,
				dataIndex: 'date',
				width:65,
				align:'right'
			}]);
		config.view=new Ext.grid.GridView({
			autoFill: true,
			forceFit: true,
			emptyText: GO.lang['strNoItems'],
			getRowClass:function(row, index) {
				if (row.data['new'] == '1') {
					return 'ml-new-row';
				}
			}		
		});
	
	}else
	{	
		config.cm =  new Ext.grid.ColumnModel([
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
					css: 'white-space:normal;'
		
				},{
					header: GO.lang.strDate,
					dataIndex: 'date',
					width:65,
					align:'right'
				}]);
		config.bbar = new Ext.PagingToolbar({
  					cls: 'go-paging-tb',
	          store: config.store,
	          pageSize: parseInt(GO.settings['max_rows_list']),
	          displayInfo: true,
	          displayMsg: GO.lang.displayingItemsShort,
	          emptyMsg: GO.lang['strNoItems']
	      });
				
		config.autoExpandColumn=1;
		
		config.view=new Ext.grid.GridView({
			emptyText: GO.lang['strNoItems']		
		});
	}
	config.cm.defaultSortable = true;
	
	config.sm=new Ext.grid.RowSelectionModel();
	config.loadMask=true;	
			
	config.border=false;	
	config.split= true;
		
	config.enableDragDrop= true;
	config.ddGroup = 'EmailDD';
	config.animCollapse=false;
	
	GO.email.MessagesGrid.superclass.constructor.call(this, config);	
};

Ext.extend(GO.email.MessagesGrid, GO.grid.GridPanel,{

	renderMessageSmallRes : function(value, p, record){
		
		if(record.data['new']=='1')
		{
			return String.format('<div id="sbj_'+record.data['uid']+'" class="NewSubject">{0}</div>{1}', value, record.data['subject']);
		}else
		{
			return String.format('<div id="sbj_'+record.data['uid']+'" class="Subject">{0}</div>{1}', value, record.data['subject']);
		}
	}, 
	
	renderMessage : function(value, p, record){
		
		if(record.data['new']=='1')
		{
			return String.format('<div id="sbj_'+record.data['uid']+'" class="NewSubject">{0}</div>{1}', value, record.data['subject']);
		}else
		{
			return String.format('<div id="sbj_'+record.data['uid']+'" class="Subject">{0}</div>{1}', value, record.data['subject']);
		}
	},
	renderIcon : function(src, p, record){
		var str = '';
		if(record.data['answered']=='1')
		{
			str += '<div class="email-grid-icon btn-message-answered"></div>';
		}else
		{
			str += '<div class="email-grid-icon btn-message"></div>';
		}
		
		if(record.data['attachments']=='1')
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
