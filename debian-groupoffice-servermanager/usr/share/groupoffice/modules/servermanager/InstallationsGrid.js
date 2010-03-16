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
 
GO.servermanager.InstallationsGrid = function(config){
	
	if(!config)
	{
		config = {};
	}
	
	config.title = GO.servermanager.lang.installations;
	config.layout='fit';
	config.autoScroll=true;
	config.split=true;
	config.store = new GO.data.JsonStore({
	    url: GO.settings.modules.servermanager.url+ 'json.php',
	    baseParams: {
	    	task: 'installations'
	    	},
	    root: 'results',
	    id: 'id',
	    totalProperty:'total',
	    fields: ['id','status','name','webmaster_email','title','default_country','language','default_timezone','default_currency','default_date_format','default_date_separator','default_thousands_separator','theme','allow_themes','allow_password_change','allow_registration','allow_duplicate_email','auto_activate_accounts','notify_admin_of_registration','registration_fields','required_registration_fields','register_modules_read','register_modules_write','register_user_groups','register_visible_user_groups','max_users','ctime','mtime','count_users', 'install_time','lastlogin','total_logins','database_usage','file_storage_usage','mailbox_usage','total_usage', 'comment', 'ctime', 'mailbox_domains', 'features', 'billing', 'professional', 'enabled'],
	    remoteSort: true
	});
	
	config.paging=true;
	var columnModel =  new Ext.grid.ColumnModel([
	   		{
			header: GO.lang.strName, 
			dataIndex: 'name'
		},{
			header: GO.servermanager.lang.status,
			dataIndex: 'status'
		},		{
			header: GO.servermanager.lang.webmasterEmail, 
			dataIndex: 'webmaster_email'
		},		{
			header: GO.servermanager.lang.title, 
			dataIndex: 'title'
		},	{
			header: GO.servermanager.lang.maxUsers, 
			dataIndex: 'max_users'
		},		{
			header: GO.lang.strCtime, 
			dataIndex: 'ctime'
		},		{
			header: GO.lang.strMtime, 
			dataIndex: 'mtime'
		},	{
			header: GO.servermanager.lang.countUsers,
			dataIndex: 'count_users',
			align:'right'
		},		{
			header: GO.servermanager.lang.installTime,
			dataIndex: 'install_time'
		},	{
			header: GO.servermanager.lang.lastlogin,
			dataIndex: 'lastlogin'
		},		{
			header: GO.servermanager.lang.totalLogins,
			dataIndex: 'total_logins',
			align:'right'
		},		{
			header: GO.servermanager.lang.databaseUsage,
			dataIndex: 'database_usage',
			align:'right'
		},
		{
			header: GO.servermanager.lang.fileStorageUsage,
			dataIndex: 'file_storage_usage',
			align:'right'
		},
		{
			header: GO.servermanager.lang.mailboxUsage,
			dataIndex: 'mailbox_usage',
			align:'right'
		},
		{
			header: GO.servermanager.lang.totalUsage,
			dataIndex: 'total_usage',
			align:'right'
		},{
			header: GO.servermanager.lang.strComment,
			dataIndex: 'comment'
		},{
			header: GO.servermanager.lang.mailDomains,
			dataIndex: 'mailbox_domains'
		},{
			header: GO.servermanager.lang.billing,
			dataIndex: 'billing',
			renderer:function(v){
				return v=="1" ? GO.lang.cmdYes : GO.lang.cmdNo;
			}
		},{
			header: 'Professional',
			dataIndex: 'professional',
			renderer:function(v){
				return v=="1" ? GO.lang.cmdYes : GO.lang.cmdNo;
			}
		}
	]);
	columnModel.defaultSortable = true;
	config.cm=columnModel;
	
	config.view=new Ext.grid.GridView({
		autoFill: true,
		forceFit: true,
		emptyText: GO.lang['strNoItems'],
		getRowClass : function(record, rowIndex, p, store){
			if(GO.util.empty(record.data.enabled)){
				return 'installation-disabled';
			}
		}
	});
	config.sm=new Ext.grid.RowSelectionModel();
	config.loadMask=true;
	
	this.searchField = new GO.form.SearchField({
		store: config.store,
		width:320
  });
	
		    			    		
	GO.servermanager.installationDialog.on('save', function(){   
		this.store.reload();	    			    			
	}, this);
	
	
	
	
	
	
	GO.servermanager.InstallationsGrid.superclass.constructor.call(this, config);
	
	this.on('rowdblclick', function(grid, rowIndex){
		var record = grid.getStore().getAt(rowIndex);	
		
		
		GO.servermanager.installationDialog.show(record.data.id);
		}, this);
	
};

Ext.extend(GO.servermanager.InstallationsGrid, GO.grid.GridPanel,{
	afterRender : function(){
		GO.servermanager.InstallationsGrid.superclass.afterRender.call(this);
		
		this.store.load();
	}
});