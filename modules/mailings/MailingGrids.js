
GO.mailings.createMailingGrids = function(){

	GO.mailings.MailingUsersGrid = function(config){

		if(!config)
		{
			config = {};
		}

		config.store = new GO.data.JsonStore({
			url: GO.settings.modules.mailings.url+'json.php',
				baseParams: {
					task: 'mailing_users',
					mailing_id: '0'
				},
				root: 'results',
				id: 'id',
				totalProperty:'total',
				fields: ['id', 'name', 'email', 'company_name', 'home_phone', 'work_phone', 'cellular'],
				remoteSort: true
		});

		this.selectUserDialog = new GO.dialog.SelectUsers({
				handler: function(grid){
					var selModel = grid.getSelectionModel();
					this.store.baseParams.add_keys = Ext.encode(selModel.selections.keys);
					this.store.load();
					delete this.store.baseParams.add_keys;
				},
				scope : this
			});

		config.border=false;
		config.paging=true;
		config.disabled=true;

		var columnModel =  new Ext.grid.ColumnModel(
		{
		defaults:{
			sortable:true
		},
		columns:[
			{
				header: GO.lang['strName'],
				dataIndex: 'name'
			},
			{
				header: GO.lang['strEmail'],
				dataIndex: 'email' ,
				width: 150
			}
		]
		});
		
		config.cm=columnModel;

		config.view=new Ext.grid.GridView({
			autoFill: true,
			forceFit: true,
			emptyText: GO.lang.strNoItems
		});

		config.sm=new Ext.grid.RowSelectionModel();
		config.loadMask=true;

		config.title=GO.addressbook.lang.users;
		config.tbar = [
			{
				iconCls: 'btn-add',
				text: GO.lang.cmdAdd,
				cls: 'x-btn-text-icon',
				handler: function(){
					this.selectUserDialog.show();
				},
				scope: this
			},
			{
				iconCls: 'btn-delete',
				text: GO.lang['cmdDelete'],
				cls: 'x-btn-text-icon',
				handler: function(){
					this.deleteSelected();
				},
				scope: this
			}
			];


		GO.mailings.MailingUsersGrid.superclass.constructor.call(this, config);

	};

	Ext.extend(GO.mailings.MailingUsersGrid, GO.grid.GridPanel, {
		onShow : function(){
			if(!this.store.loaded)
			{
				this.store.load();
			}
			GO.mailings.MailingCompaniesGrid.superclass.onShow.call(this);
		},
		setMailingId : function(mailing_id)
		{
			this.store.baseParams['mailing_id']=mailing_id;
			this.store.loaded=false;
			this.setDisabled(mailing_id==0);
		}
	});







	GO.mailings.MailingContactsGrid = Ext.extend(function(config){

		if(!config)
		{
			config = {};
		}

		config.title=GO.addressbook.lang.contacts;

		this.selectContactDialog = new GO.addressbook.SelectContactDialog({
			handler: function(grid, allResults){
				if(!allResults){
					var selModel = grid.getSelectionModel();
					this.store.baseParams.add_keys = Ext.encode(selModel.selections.keys);
					this.store.load();
					delete this.store.baseParams.add_keys;
				}else
				{
					this.store.baseParams.add_search_result = Ext.encode(grid.store.baseParams);
					this.store.load();
					delete this.store.baseParams.add_search_result;
				}
			},
			scope : this
		});

		config.disabled=true;

		config.tbar = [
			{
				iconCls: 'btn-add',
				text: GO.lang.cmdAdd,
				cls: 'x-btn-text-icon',
				handler: function(){
					this.selectContactDialog.show();
				},
				scope: this
			},
			{
				iconCls: 'btn-add',
				text: GO.mailings.lang.addEntireAddressbook,
				cls: 'x-btn-text-icon',
				handler: function(){
					if(!this.selectAddressbookWindow)
					{
						this.selectAddressbookWindow = new GO.mailings.SelectAddressbookWindow();
						this.selectAddressbookWindow.on('select', function(addressbook_id){

							if(confirm(GO.mailings.lang.confirmAddEntireAddressbook))
							{
								this.store.load({
									params:{'start': 0, 'add_addressbook_id': addressbook_id}
								});
							}
						}, this);
					}
					this.selectAddressbookWindow.show();
				},
				scope: this
			},
			{
				iconCls: 'btn-delete',
				text: GO.lang['cmdDelete'],
				cls: 'x-btn-text-icon',
				handler: function(){
					this.deleteSelected();
				},
				scope: this
			}
			];


		config.store = new GO.data.JsonStore({
				url: GO.settings.modules.mailings.url+ 'json.php',
				baseParams: {
					task: 'mailing_contacts',
					mailing_id: '0'
				},
				root: 'results',
				id: 'id',
				totalProperty:'total',
				fields: ['id', 'name', 'company_name', 'email', 'home_phone', 'work_phone', 'cellular'],
				remoteSort: true
			});


		config.paging=true;
		config.border=false;
		var contactsColumnModel =  new Ext.grid.ColumnModel({
		defaults:{
			sortable:true
		},
		columns:[
			{
				header: GO.lang['strName'],
				dataIndex: 'name'
			},
			{
				header: GO.lang['strCompany'],
				dataIndex: 'company_name'
			},
			{
				header: GO.lang['strEmail'],
				dataIndex: 'email' ,
				width: 150,
				hidden:true
			},
			{
				header: GO.lang['strPhone'],
				dataIndex: 'home_phone' ,
				width: 100,
				hidden:true
			},
			{
				header: GO.lang['strWorkPhone'],
				dataIndex: 'work_phone' ,
				width: 100,
				hidden:true
			},
			{
				header: GO.lang['strWorkFax'],
				dataIndex: 'work_fax' ,
				width: 100,
				hidden:true
			},
			{
				header: GO.lang['strCellular'],
				dataIndex: 'cellular' ,
				width: 100,
				hidden:true
			}
		]
		});
		
		config.cm=contactsColumnModel;

		config.view=new Ext.grid.GridView({
			autoFill: true,
			forceFit: true,
			emptyText: GO.lang.strNoItems
		}),
		config.sm=new Ext.grid.RowSelectionModel();
		config.loadMask=true;

		GO.mailings.MailingContactsGrid.superclass.constructor.call(this, config);

	}, GO.grid.GridPanel, {
		onShow : function(){
			if(!this.store.loaded)
			{
				this.store.load();
			}
			GO.mailings.MailingContactsGrid.superclass.onShow.call(this);
		},
		setMailingId : function(mailing_id)
		{
			this.store.baseParams['mailing_id']=mailing_id;
			this.store.loaded=false;
			this.setDisabled(mailing_id==0);
		}
	});







	GO.mailings.MailingCompaniesGrid = Ext.extend(function(config){

		if(!config)
		{
			config = {};
		}

		config.title=GO.addressbook.lang.companies;
		this.selectCompanyDialog = new GO.addressbook.SelectCompanyDialog({
			handler: function(grid, allResults){
				if(!allResults){
					var selModel = grid.getSelectionModel();
					this.store.baseParams.add_keys = Ext.encode(selModel.selections.keys);
					this.store.load();
					delete this.store.baseParams.add_keys;
				}else
				{
					this.store.baseParams.add_search_result = Ext.encode(grid.store.baseParams);
					this.store.load();
					delete this.store.baseParams.add_search_result;
				}
			},
			scope : this
		});

		config.disabled=true;

		config.tbar = [
			{
				iconCls: 'btn-add',
				text: GO.lang.cmdAdd,
				cls: 'x-btn-text-icon',
				handler: function(){
					this.selectCompanyDialog.show();
				},
				scope: this
			},
			{
				iconCls: 'btn-add',
				text: GO.mailings.lang.addEntireAddressbook,
				cls: 'x-btn-text-icon',
				handler: function(){
					if(!this.selectAddressbookWindow)
					{
						this.selectAddressbookWindow = new GO.mailings.SelectAddressbookWindow();
						this.selectAddressbookWindow.on('select', function(addressbook_id){

							if(confirm(GO.mailings.lang.confirmAddEntireAddressbook))
							{
								this.store.load({
									params:{'start': 0, 'add_addressbook_id': addressbook_id}
								});
							}

						}, this);
					}
					this.selectAddressbookWindow.show();
				},
				scope: this
			},
			{
				iconCls: 'btn-delete',
				text: GO.lang['cmdDelete'],
				cls: 'x-btn-text-icon',
				handler: function(){
					this.deleteSelected();
				},
				scope: this
			}
			];

		config.store = new GO.data.JsonStore({
			url: GO.settings.modules.mailings.url+ 'json.php',
			baseParams: {
				task: 'mailing_companies',
				mailing_id: '0'
			},
			root: 'results',
			id: 'id',
			totalProperty:'total',
			fields: ['id', 'name', 'homepage', 'email', 'phone', 'fax'],
			remoteSort: true
		});


		config.border=false;
		config.paging=true;

		var companiesColumnModel =  new Ext.grid.ColumnModel({
		defaults:{
			sortable:true
		},
		columns:[
			{
				header: GO.lang['strName'],
				dataIndex: 'name'
			},
			{
				header: GO.lang['strEmail'],
				dataIndex: 'email' ,
				width: 150,
				hidden:true
			},
			{
				header: GO.lang['strHomepage'],
				dataIndex: 'homepage' ,
				width: 100,
				hidden:true
			},
			{
				header: GO.lang['strPhone'],
				dataIndex: 'phone' ,
				width: 100
			},
			{
				header: GO.lang['strFax'],
				dataIndex: 'fax' ,
				width: 100,
				hidden:true
			}
		]
		});
		
		config.cm=companiesColumnModel;

		config.view=new Ext.grid.GridView({
			autoFill: true,
			forceFit: true,
			emptyText: GO.lang.strNoItems
		}),
		config.sm=new Ext.grid.RowSelectionModel();
		config.loadMask=true;

		GO.addressbook.CompaniesGrid.superclass.constructor.call(this, config);

	},GO.grid.GridPanel, {
		onShow : function(){
			if(!this.store.loaded)
			{
				this.store.load();
			}
			GO.mailings.MailingContactsGrid.superclass.onShow.call(this);
		},


		setMailingId : function(mailing_id)
		{
			this.store.baseParams['mailing_id']=mailing_id;
			this.store.loaded=false;
			this.setDisabled(mailing_id==0);
		}
	});

};	
		


if(!GO.addressbook)
{
	GO.moduleManager.onModuleReady('addressbook', GO.mailings.createMailingGrids);
}else
{
	GO.mailings.createMailingGrids();
} 
