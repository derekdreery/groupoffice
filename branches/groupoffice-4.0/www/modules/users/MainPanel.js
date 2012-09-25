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
 * @author Boy Wijnmaalen <bwijnmaalen@intermesh.nl>
 */

GO.users.MainPanel = function(config)
{	
	if(!config)
	{
		config = {};
	}

	var fields = {
		fields:['id', 'username', 'name','logins','lastlogin','ctime','address','address_no','zip','city','state','country','home_phone','email',
	    	'waddress','waddress_no','wzip','wcity','wstate','wcountry','wphone','enabled'],
		columns:[
        {header: GO.lang['strUsername'], dataIndex: 'username', width: 200},
        {header: GO.lang['strName'], dataIndex: 'name', width: 250},
        {header: GO.users.lang.numberOfLogins, dataIndex: 'logins', width: 100, align:"right"},
        {header: GO.users.lang['cmdFormLabelLastLogin'], dataIndex: 'lastlogin', width: 100},
        {header: GO.users.lang['cmdFormLabelRegistrationTime'], dataIndex: 'ctime', width: 100},      
        {header: GO.lang['strEmail'], dataIndex: 'email',  hidden: false, width: 150}       
    ]
	};

	if(GO.customfields)
	{
		GO.customfields.addColumns(8, fields);
	}

	config.store = new GO.data.JsonStore({
	    url: GO.url('users/user/store'),
	    baseParams: {task: 'users'},
	    id: 'id',
	    totalProperty: 'total',
	    root: 'results',
	    fields: fields.fields,
	    remoteSort: true
	});

	config.loadMask=true;
						
	config.store.setDefaultSort('username', 'ASC');

  this.searchField = new GO.form.SearchField({
		store: config.store,
		width:320
  });			
 
	config.view = new Ext.grid.GridView({
		getRowClass : function(record, rowIndex, p, store){
			if(GO.util.empty(record.data.enabled)){
				return 'user-disabled';
			}
		}
	});

	config.deleteConfig={extraWarning:GO.users.lang.deleteWarning+"\n\n"};
			
	config.cm = new Ext.grid.ColumnModel({
		defaults:{
			sortable:true
		},
		columns:fields.columns
	});	
		    	
	config.tbar = new Ext.Toolbar({		
			cls:'go-head-tb',
			items: [
		  	{
		  		iconCls: 'btn-add', 
		  		text: GO.lang['cmdAdd'], 
		  		cls: 'x-btn-text-icon', 
		  		handler: function(){
		  			if(GO.settings.config.max_users>0 && this.store.totalLength>=GO.settings.config.max_users)
		  			{
		  				Ext.Msg.alert(GO.lang.strError, GO.users.lang.maxUsersReached);
		  			}else
		  			{
		  				GO.users.showUserDialog();
		  			}
		  		}, 
		  		scope: this
		  	},
		  	{
		  		iconCls: 'btn-delete', 
		  		text: GO.lang['cmdDelete'], 
		  		cls: 'x-btn-text-icon', 
		  		handler: function(){
						Ext.Ajax.timeout = 180000; //3 minutes
						this.deleteSelected();
						Ext.Ajax.timeout = 30000; //30 seconds
					},
		  		scope: this
		  	},{
		  		iconCls: 'btn-upload',
		  		text:GO.lang.cmdImport,
		  		handler:function(){
		  			if(!this.importDialog)
		  			{
		  				this.importDialog = new GO.users.ImportDialog();
		  				this.importDialog.on('import', function(){this.store.reload();}, this);
		  			}
		  			this.importDialog.show();
		  		},
		  		scope:this		  		
		  	},{
				iconCls:'btn-settings',
				text:GO.lang.administration,
				handler:function(){
					if(!this.settingsDialog)
					{
						this.settingsDialog = new GO.users.SettingsDialog();
					}
					this.settingsDialog.show();
				},
				scope:this
			},{
				enableToggle:true,
				text:GO.users.lang.showProUsers,
				toggleHandler:function(btn, pressed){
					this.store.baseParams.show_licensed=pressed ? 1 : 0;
					this.store.load();
				},
				scope:this
			},
				'-',
		         GO.lang['strSearch']+':',
		        this.searchField
		    ]});
    
   if(GO.settings.config.max_users>0)
   {
	   config.bbar = new Ext.PagingToolbar({
	   			cls: 'go-paging-tb',
	        store: config.store,
	        pageSize: parseInt(GO.settings['max_rows_list']),
	        displayInfo: true,
	        displayMsg: GO.lang['displayingItems']+'. '+GO.lang.strMax+' '+GO.settings.config.max_users,
	        emptyMsg: GO.lang['strNoItems']
	    });
   }

		config.sm = new Ext.grid.RowSelectionModel();
		config.paging=true;		
				
		GO.users.MainPanel.superclass.constructor.call(this,config);
};
		
Ext.extend(GO.users.MainPanel, GO.grid.GridPanel,{
	
	afterRender : function(){
		GO.users.MainPanel.superclass.afterRender.call(this);
		
		this.on("rowdblclick",this.rowDoubleClick, this);			
		this.store.load();


		GO.dialogListeners.add('user',{
			scope:this,
			save:function(){
				this.store.reload();
			}
		});
	},			
	
	rowDoubleClick : function (grid, rowIndex, event)
	{
		var selectionModel = grid.getSelectionModel();
		var record = selectionModel.getSelected();
		GO.users.showUserDialog(record.data['id']);
	}
});

GO.users.showUserDialog = function(user_id, config){

	if(!GO.users.userDialog)
		GO.users.userDialog = new GO.users.UserDialog();

	GO.users.userDialog.show(user_id, config);
}


GO.linkHandlers["GO_Base_Model_User"]=function(id){
	//GO.users.showUserDialog(id);
	if(!GO.users.userLinkWindow){
		var userPanel = new GO.users.UserPanel();
		GO.users.userLinkWindow = new GO.LinkViewWindow({
			title: GO.lang.strUser,
			closeAction:'hide',
			items: userPanel,
			userPanel: userPanel
		});
	}
	GO.users.userLinkWindow.userPanel.load(id);
	GO.users.userLinkWindow.show();
	return GO.users.userLinkWindow;
};

GO.linkPreviewPanels["GO_Base_Model_User"]=function(config){
	config = config || {};
	return new GO.users.UserPanel(config);
}


GO.moduleManager.addModule('users', GO.users.MainPanel, {
	title : GO.lang.users,
	iconCls : 'go-tab-icon-users',
	admin :true
});
