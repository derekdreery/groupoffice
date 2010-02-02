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
	//this.userDialog = new UserDialog();

	config.store = new GO.data.JsonStore({
	    url: GO.settings.modules.users.url+'non_admin_json.php',
	    baseParams: {task: 'users'},
	    id: 'id',
	    totalProperty: 'total',
	    root: 'results',
	    fields: ['id', 'username', 'name','company','logins','lastlogin','registration_time','address','zip','city','state','country','phone','email',
	    	'waddress','wzip','wcity','wstate','wcountry','wphone','enabled'],
	    remoteSort: true
	});
						
	config.store.setDefaultSort('username', 'ASC');


  this.searchField = new GO.form.SearchField({
		store: config.store,
		width:320
  });			
 
	config.view = new Ext.grid.GridView({
		forceFit: true,
		autoFill: true,
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
		columns:[
        {header: GO.lang['strUsername'], dataIndex: 'username'},
        {header: GO.lang['strName'], dataIndex: 'name', width: 250},
        {header: GO.lang['strCompany'], dataIndex: 'company', width: 200},
        {header: GO.users.lang['cmdFormLabelTotalLogins'], dataIndex: 'logins', width: 100},
        {header: GO.users.lang['cmdFormLabelLastLogin'], dataIndex: 'lastlogin', width: 100},
        {header: GO.users.lang['cmdFormLabelRegistrationTime'], dataIndex: 'registration_time', width: 100},
        
        {header: GO.lang['strAddress'], dataIndex: 'address', width: 100, hidden: true},
        {header: GO.lang['strZip'], dataIndex: 'zip', width: 100, hidden: true},
        {header: GO.lang['strCity'], dataIndex: 'city', width: 100, hidden: true},
        {header: GO.lang['strState'], dataIndex: 'state', width: 100, hidden: true},
        {header: GO.lang['strCountry'], dataIndex: 'country', width: 100, hidden: true},
        {header: GO.lang['strPhone'], dataIndex: 'phone', width: 100, hidden: true},
        {header: GO.lang['strEmail'], dataIndex: 'email', width: 100, hidden: true},
        {header: GO.lang['strWorkAddress'], dataIndex: 'waddress', width: 100, hidden: true},
        {header: GO.lang['strWorkZip'], dataIndex: 'wzip', width: 100, hidden: true},
        {header: GO.lang['strWorkCity'], dataIndex: 'wcity', width: 100, hidden: true},
        {header: GO.lang['strWorkState'], dataIndex: 'wstate', width: 100, hidden: true},
        {header: GO.lang['strWorkCountry'], dataIndex: 'wcountry', width: 100, hidden: true},
        {header: GO.lang['strWorkPhone'], dataIndex: 'wphone', width: 100, hidden: true}      			        
    ]
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
		  				GO.users.userDialog.show();
		  			}
		  		}, 
		  		scope: this
		  	},
		  	{
		  		iconCls: 'btn-delete', 
		  		text: GO.lang['cmdDelete'], 
		  		cls: 'x-btn-text-icon', 
		  		handler: function(){this.deleteSelected();},
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
		
		
		if(!GO.users.userDialog.hasListener('save'))
		{
			GO.users.userDialog.on('save', function(){
				this.store.reload();
			}, this);
		}
		
	},			
	
	rowDoubleClick : function (grid, rowIndex, event)
	{
		var selectionModel = grid.getSelectionModel();
		var record = selectionModel.getSelected();
		GO.users.userDialog.show(record.data['id']);
	}
});

GO.mainLayout.onReady(function(){
	GO.users.userDialog = new GO.users.UserDialog();
});


GO.linkHandlers[8]=function(id){
	GO.users.userDialog.show(id);
};

GO.linkPreviewPanels[8]=function(config){
	config = config || {};
	return new GO.users.UserPanel(config);
}


GO.moduleManager.addModule('users', GO.users.MainPanel, {
	title : GO.lang.users,
	iconCls : 'go-tab-icon-users',
	admin :true
});
