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
 
GO.postfixadmin.MailboxesGrid = function(config){
	
	if(!config)
	{
		config = {};
	}
	
	config.title = GO.postfixadmin.lang.mailboxes;
	config.layout='fit';
	config.autoScroll=true;
	config.split=true;
	config.store = new GO.data.JsonStore({
	    url: GO.settings.modules.postfixadmin.url+ 'json.php',
	    baseParams: {
	    	task: 'mailboxes'
	    	},
	    root: 'results',
	    id: 'id',
	    totalProperty:'total',
	    fields: ['id','username','name','quota','usage','ctime','mtime','active'],
	    remoteSort: true
	});
	
	config.disabled=true;
	config.paging=true;
	var columnModel =  new Ext.grid.ColumnModel({
		defaults:{
			sortable:true
		},
		columns:[
	   		{
			header: GO.postfixadmin.lang.username, 
			dataIndex: 'username'
		},		{
			header: GO.lang.strName, 
			dataIndex: 'name'
		},	{
			header: GO.postfixadmin.lang.quota, 
			dataIndex: 'quota'
		}, {
			header: GO.postfixadmin.lang.usage,
			dataIndex: 'usage'
		}, 		{
			header: GO.lang.strCtime, 
			dataIndex: 'ctime'
		},		{
			header: GO.lang.strMtime, 
			dataIndex: 'mtime'
		},		{
			header: GO.postfixadmin.lang.active, 
			dataIndex: 'active'
		}
	]});
	
	config.cm=columnModel;
	
	config.view=new Ext.grid.GridView({
		autoFill: true,
		forceFit: true,
		emptyText: GO.lang['strNoItems']		
	});
	config.sm=new Ext.grid.RowSelectionModel();
	config.loadMask=true;
	
	
	this.mailboxDialog = new GO.postfixadmin.MailboxDialog();
	    			    		
		this.mailboxDialog.on('save', function(){   
			this.store.reload();	    			    			
		}, this);
	
	
	config.tbar=[{
			iconCls: 'btn-add',							
			text: GO.lang['cmdAdd'],
			cls: 'x-btn-text-icon',
			handler: function(){
				
	    	this.mailboxDialog.show();	    	
	    	this.mailboxDialog.formPanel.form.findField('quota').setValue(GO.postfixadmin.domainDialog.quotaField.getValue());
	    	
	    	
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
	
	
	
	GO.postfixadmin.MailboxesGrid.superclass.constructor.call(this, config);
	
	this.on('rowdblclick', function(grid, rowIndex){
		var record = grid.getStore().getAt(rowIndex);	
		
		this.mailboxDialog.show(record.data.id);
		
		}, this);
	
};

Ext.extend(GO.postfixadmin.MailboxesGrid, GO.grid.GridPanel,{
	onShow : function(){
		if(!this.store.loaded)
		{
			this.store.load();
		}
		
		GO.postfixadmin.MailboxesGrid.superclass.onShow.call(this);
	},
	
	setDomainId : function(domain_id)
	{
		this.store.baseParams.domain_id=domain_id;
		this.store.loaded=false;
		this.mailboxDialog.formPanel.baseParams.domain_id=domain_id;
		this.setDisabled(domain_id<1);
	}
});