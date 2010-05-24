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
 
GO.postfixadmin.FetchmailConfigsGrid = function(config){
	
	if(!config)
	{
		config = {};
	}
	
	config.title = GO.postfixadmin.lang.fetchmailConfigs;
	config.layout='fit';
	config.autoScroll=true;
	config.split=true;
	config.store = new GO.data.JsonStore({
	    url: GO.settings.modules.postfixadmin.url+ 'json.php',
	    baseParams: {
	    	task: 'fetchmail_configs'
	    	},
	    root: 'results',
	    id: 'id',
	    totalProperty:'total',
	    fields: ['id','mailbox','src_server','src_auth','src_user','src_password','src_folder','poll_time','fetchall','keep','protocol','extra_options','returned_text','mda','date'],
	    remoteSort: true
	});
	
	config.paging=true;
	var columnModel =  new Ext.grid.ColumnModel([
	   		{
			header: GO.postfixadmin.lang.mailbox, 
			dataIndex: 'mailbox'
		},		{
			header: GO.postfixadmin.lang.srcServer, 
			dataIndex: 'src_server'
		},		{
			header: GO.postfixadmin.lang.srcAuth, 
			dataIndex: 'src_auth'
		},		{
			header: GO.postfixadmin.lang.srcUser, 
			dataIndex: 'src_user'
		},		{
			header: GO.postfixadmin.lang.srcPassword, 
			dataIndex: 'src_password'
		},		{
			header: GO.postfixadmin.lang.srcFolder, 
			dataIndex: 'src_folder'
		},		{
			header: GO.postfixadmin.lang.pollTime, 
			dataIndex: 'poll_time'
		},		{
			header: GO.postfixadmin.lang.fetchall, 
			dataIndex: 'fetchall'
		},		{
			header: GO.postfixadmin.lang.keep, 
			dataIndex: 'keep'
		},		{
			header: GO.postfixadmin.lang.protocol, 
			dataIndex: 'protocol'
		},		{
			header: GO.postfixadmin.lang.extraOptions, 
			dataIndex: 'extra_options'
		},		{
			header: GO.postfixadmin.lang.returnedText, 
			dataIndex: 'returned_text'
		},		{
			header: GO.postfixadmin.lang.mda, 
			dataIndex: 'mda'
		},		{
			header: GO.postfixadmin.lang.date, 
			dataIndex: 'date'
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
	
	
	this.fetchmailConfigDialog = new GO.postfixadmin.FetchmailConfigDialog();
	    			    		
		this.fetchmailConfigDialog.on('save', function(){   
			this.store.reload();	    			    			
		}, this);
	
	
	config.tbar=[{
			iconCls: 'btn-add',							
			text: GO.lang['cmdAdd'],
			cls: 'x-btn-text-icon',
			handler: function(){
				
	    	this.fetchmailConfigDialog.show();
	    	
	    	
	    	
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
	
	
	
	GO.postfixadmin.FetchmailConfigsGrid.superclass.constructor.call(this, config);
	
	this.on('rowdblclick', function(grid, rowIndex){
		var record = grid.getStore().getAt(rowIndex);	
		
		this.fetchmailConfigDialog.show(record.data.id);
		
		}, this);
	
};

Ext.extend(GO.postfixadmin.FetchmailConfigsGrid, GO.grid.GridPanel,{
	
});