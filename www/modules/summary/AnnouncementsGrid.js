/** 
 * Copyright Intermesh
 * 
 * This file is part of Group-Office. You should have received a copy of the
 * Group-Office license along with Group-Office. See the file /LICENSE.TXT
 * 
 * If you have questions write an e-mail to info@intermesh.nl
 * 
 * @version $Id: GridPanel.tpl 1858 2008-04-29 14:09:19Z mschering $
 * @copyright Copyright Intermesh
 * @author Merijn Schering <mschering@intermesh.nl>
 */
GO.summary.AnnouncementsGrid = function(config){
	if(!config)
	{
		config = {};
	}
	config.title = GO.summary.lang.announcements;
	config.layout='fit';
	config.autoScroll=true;
	config.split=true;
	config.store = new GO.data.JsonStore({
	    url: GO.settings.modules.summary.url+ 'json.php',
	    baseParams: {
	    	task: 'announcements'
	    	},
	    root: 'results',
	    id: 'id',
	    totalProperty:'total',
	    fields: ['id','user_name','host','ip','link_id','ctime','mtime','name','expires','upgrades','notified'],
	    remoteSort: true
	});
	config.paging=true;
	var columnModel =  new Ext.grid.ColumnModel([
	   		{
			header: GO.lang.strOwner, 
			dataIndex: 'user_name',
		  sortable: false
		}
,		{
			header: GO.summary.lang.host, 
			dataIndex: 'host'
		}
,		{
			header: GO.summary.lang.ip, 
			dataIndex: 'ip'
		}
,		{
			header: GO.summary.lang.linkId, 
			dataIndex: 'link_id'
		}
,		{
			header: GO.lang.strCtime, 
			dataIndex: 'ctime'
		}
,		{
			header: GO.lang.strMtime, 
			dataIndex: 'mtime'
		}
,		{
			header: GO.lang.strName, 
			dataIndex: 'name'
		}
,		{
			header: GO.summary.lang.expires, 
			dataIndex: 'expires'
		}
,		{
			header: GO.summary.lang.upgrades, 
			dataIndex: 'upgrades'
		}
,		{
			header: GO.summary.lang.notified, 
			dataIndex: 'notified'
		}

	]);
	columnModel.defaultSortable = true;
	config.cm=columnModel;
	config.view=new Ext.grid.GridView({
		autoFill: true,
		forceFit: true,
		emptyText: GO.lang['strNoItems']		
	});
	config.sm=new Ext.grid.RowSelectionModel();
	config.loadMask=true;
	this.announcementDialog = new GO.summary.AnnouncementDialog();
		this.announcementDialog.on('save', function(){   
			this.store.reload();	    			    			
		}, this);
	config.tbar=[{
			iconCls: 'btn-add',							
			text: GO.lang['cmdAdd'],
			cls: 'x-btn-text-icon',
			handler: function(){
	    	this.announcementDialog.show();
			},
			scope: this
		},{
			iconCls: 'btn-delete',
			text: GO.lang['cmdDelete'],
			cls: 'x-btn-text-icon',
			handler: function(){
				this.deleteSelected();
			},
			scope: this
		}];
	GO.summary.AnnouncementsGrid.superclass.constructor.call(this, config);
	this.on('rowdblclick', function(grid, rowIndex){
		var record = grid.getStore().getAt(rowIndex);	
		this.announcementDialog.show(record.data.id);
		}, this);
};
Ext.extend(GO.summary.AnnouncementsGrid, GO.grid.GridPanel,{
	loaded : false,
	afterRender : function()
	{
		GO.summary.AnnouncementsGrid.superclass.afterRender.call(this);
		if(this.isVisible())
		{
			this.onGridShow();
		}
	},
	onGridShow : function(){
		if(!this.loaded && this.rendered)
		{
			this.store.load();
			this.loaded=true;
		}
	}
});
