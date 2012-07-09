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


GO.addressbook.MainPanel = function(config)
{

	if(!config)
	{
		config={};
	}

	this.contactsGrid = new GO.addressbook.ContactsGrid({
		layout: 'fit',
		region: 'center',
		id: 'ab-contacts-grid-panel'
	});

	this.contactsGrid.on("delayedrowselect",function(grid, rowIndex, r){
		//this.contactsGrid.getSelectionModel().on("rowselect",function(sm, rowIndex, r){
		this.contactEastPanel.load(r.get('id'));
	}, this);
	this.contactsGrid.on("rowdblclick", function(){
		this.contactEastPanel.editHandler();
	}, this);

	this.contactsGrid.store.on('load', function(){
		this.setAdvancedSearchNotification(this.contactsGrid.store);
	}, this);

	if (GO.email) {
		this.contactsGrid.on("rowcontextmenu",function(grid,row,e){
			{
				if(typeof(this.contactsGrid.contextMenu)=='undefined')
				{
					this.contactsGrid.contextMenu = new GO.addressbook.ContextMenu();
				}
				this.contactsGrid.contextMenu.setSelected(grid, "GO_Addressbook_Model_Contact");
				e.stopEvent();
				this.contactsGrid.contextMenu.showAt(e.getXY());
			}
		},this);
	}

	this.companiesGrid = new GO.addressbook.CompaniesGrid({
		layout: 'fit',
		region: 'center',
		id: 'ab-company-grid-panel'
	});

	this.companiesGrid.on("delayedrowselect",function(grid, rowIndex, r){
		this.companyEastPanel.load(r.get('id'));
	}, this);
	this.companiesGrid.on("rowdblclick", function(){
		this.companyEastPanel.editHandler();
	}, this);

	if (GO.email) {
		this.companiesGrid.on("rowcontextmenu",function(grid,row,e){
			{
				if(typeof(this.companiesGrid.contextMenu)=='undefined')
				{
					this.companiesGrid.contextMenu = new GO.addressbook.ContextMenu();
				}
				this.companiesGrid.contextMenu.setSelected(grid, "GO_Addressbook_Model_Company");
				e.stopEvent();
				this.companiesGrid.contextMenu.showAt(e.getXY());
			}
		},this);
	}


	this.companiesGrid.store.on('load', function(){
		this.setAdvancedSearchNotification(this.companiesGrid.store);
	}, this);



	this.searchPanel = new GO.addressbook.SearchPanel({
		region: 'north',
		ab:this
	});

	this.searchPanel.on('queryChange', function(params){
		this.setSearchParams(params);
	}, this);

	this.contactEastPanel = new GO.addressbook.ContactReadPanel({
		id:'ab-contact-panel',
		region : 'east',
		title: GO.addressbook.lang['cmdPanelContact'],
		width:420,
		collapseMode:'mini',
		collapsible:true,
		split:true
	});

	this.companyEastPanel = new GO.addressbook.CompanyReadPanel({
		id:'ab-company-panel',
		region : 'east',
		title: GO.addressbook.lang['cmdPanelCompany'],
		width:420,
		collapseMode:'mini',
		collapsible:true,
		split:true
	});

	this.contactsPanel = new Ext.Panel({
		id: 'ab-contacts-grid',
		title: GO.addressbook.lang.contacts,
		layout: 'border',
		items:[
		this.contactsGrid,
		this.contactEastPanel
		]
	});
	this.contactsPanel.on("show", function(){
		this.setAdvancedSearchNotification(this.contactsGrid.store);
		this.addressbooksGrid.setType('contact');
	}, this);

	this.companyPanel = new Ext.Panel({
		id: 'ab-company-grid',
		title: GO.addressbook.lang.companies,
		layout: 'border',
		items:[
		this.companiesGrid,
		this.companyEastPanel
		]
	});

	//this.companyPanel.on("show",this.companiesGrid.onGridShow, this.companiesGrid);

	this.companyPanel.on("show", function(){
		this.setAdvancedSearchNotification(this.companiesGrid.store);
		this.addressbooksGrid.setType('company');
	}, this);


	this.addressbooksGrid = new GO.addressbook.AddresbooksGrid({
		region:'west',
		id:'ab-addressbook-grid',
		width:180,
		height:250
	});

	this.addressbooksGrid.on('change', function(grid, abooks, records)
	{
		var books = Ext.encode(abooks);
		var panel = this.tabPanel.getActiveTab();
		
		this.companiesGrid.store.baseParams.books = books;
		this.contactsGrid.store.baseParams.books = books;
			
		if(panel.id=='ab-contacts-grid')
		{
			this.contactsGrid.store.load();
			//delete this.contactsGrid.store.baseParams.books;
		}else
		{
			
			this.companiesGrid.store.load();
			//delete this.companiesGrid.store.baseParams.books;
		}

		if(records.length)
		{
			GO.addressbook.defaultAddressbook = records[0];
		}
	}, this);

	/*this.addressbooksGrid.on('rowclick', function(grid, rowIndex){


	}, this);*/

	/*
	this.addressbooksGrid.getSelectionModel().on('rowselect', function(sm, rowIndex, r){
		GO.addressbook.defaultAddressbook = sm.getSelected().get('id');

		var record = this.addressbooksGrid.getStore().getAt(rowIndex);
		this.setSearchParams({addressbook_id : record.get("id")});
	}, this);
	*/


	this.addressbooksGrid.on('drop', function(type)
	{
		if(type == 'company')
		{
			this.companiesGrid.store.reload();
		}else
		{
			this.contactsGrid.store.reload();
		}
	}, this);

	this.tabPanel = new Ext.TabPanel({
		region : 'center',
		activeTab: 0,
		border: true,
		listeners:{
			scope:this,
			tabchange:function(tabPanel, activeTab){
				if(activeTab.id=='ab-contacts-grid')
					this.contactsGrid.store.load();
				else
					this.companiesGrid.store.load();
			}
		},
		items: [
		this.contactsPanel,
		this.companyPanel
		]
	});

	config.layout='border';
	config.border=false;
//
//	if(GO.addressbook)
//	{

		this.mailingsFilterPanel= new GO.grid.MultiSelectGrid({
			region:'center',
			id:'ab-mailingsfilter-panel',
			title:GO.addressbook.lang.filterMailings,
			loadMask:true,
			store:GO.addressbook.readableAddresslistsStore,
			allowNoSelection:true
		});

		this.mailingsFilterPanel.on('change', function(grid, addresslist_filter){
			var panel = this.tabPanel.getActiveTab();
			if(panel.id=='ab-contacts-grid')
			{
				this.contactsGrid.store.baseParams.addresslist_filter = Ext.encode(addresslist_filter);
				this.contactsGrid.store.load();
				//delete this.contactsGrid.store.baseParams.addresslist_filter;
			}else
			{
				this.companiesGrid.store.baseParams.addresslist_filter = Ext.encode(addresslist_filter);
				this.companiesGrid.store.load();
				//delete this.companiesGrid.store.baseParams.addresslist_filter;
			}
		}, this);

		this.addressbooksGrid.region='north';
		var westPanel = new Ext.Panel({
			layout:'border',
			border:false,
			region:'west',
			width:180,
			split:true,
			items:[this.addressbooksGrid,this.mailingsFilterPanel]
		});
		config.items= [
		this.searchPanel,
		westPanel,
		this.tabPanel
		];
//	}else
//	{
//		config.items= [
//		this.searchPanel,
//		this.addressbooksGrid,
//		this.tabPanel
//		];
//	}

	var tbar=[
	{
		iconCls: 'btn-addressbook-add-contact',
		text: GO.addressbook.lang['btnAddContact'],
		cls: 'x-btn-text-icon',
		handler: function(){
			//GO.addressbook.showContactDialog(0);
			this.contactEastPanel.reset();
			this.contactEastPanel.editHandler();

			this.tabPanel.setActiveTab('ab-contacts-grid');
		},
		scope: this
	},
	{
		iconCls: 'btn-addressbook-add-company',
		text: GO.addressbook.lang['btnAddCompany'],
		cls: 'x-btn-text-icon',
		handler: function(){
			//GO.addressbook.showCompanyDialog(0);
			this.companyEastPanel.reset();
			this.companyEastPanel.editHandler();
			this.tabPanel.setActiveTab('ab-company-grid');
		},
		scope: this
	},
	{
		iconCls: 'btn-delete',
		text: GO.lang['cmdDelete'],
		cls: 'x-btn-text-icon',
		handler: function(){
			var activetab = this.tabPanel.getActiveTab();

			switch(activetab.id)
			{
				case 'ab-contacts-grid':
					this.contactsGrid.deleteSelected({
						callback : this.contactEastPanel.gridDeleteCallback,
						scope: this.contactEastPanel
					});
					break;
				case 'ab-company-grid':
					this.companiesGrid.deleteSelected({
						callback : this.companyEastPanel.gridDeleteCallback,
						scope: this.companyEastPanel
					});
					break;
			}
		},
		scope: this
	},
	'-',
	{
		iconCls: 'btn-addressbook-manage',
		text: GO.lang.administration,
		cls: 'x-btn-text-icon',
		handler:function(){
			if(!this.manageDialog)
			{
				this.manageDialog = new GO.addressbook.ManageDialog();
			}
			this.manageDialog.show();
		},
		scope: this
	}];

	if(GO.addressbook.exportPermission == '1')
	{
		tbar.push(
			new Ext.Button({
				iconCls: 'btn-export',
				text: GO.lang.cmdExport,
				cls: 'x-btn-text-icon',
				handler:function(){
					var activetab = this.tabPanel.getActiveTab();
					
//					var config = {};
					var url;
					var name;
					var title;
					var colmodel;
					var documentTitle;
					switch(activetab.id)
					{
						case 'ab-contacts-grid':
							url = 'addressbook/contact/export';
							name = 'contact';
							documentTitle = 'ExportContact';
							colmodel = this.contactsGrid.getColumnModel();
							
							if(!this.exportDialogContacts) {
								this.exportDialogContacts = new GO.ExportGridDialog({
									url: url,
									name: name,
									exportClassPath:'modules/addressbook/export',
									documentTitle: title,
									colModel: colmodel
								});
							} else {
								this.exportDialogContacts.documentTitle=documentTitle;
								this.exportDialogContacts.documentTitle=name;
								this.exportDialogContacts.documentTitle=url;
								this.exportDialogContacts.colmodel=colmodel;
							}
							this.exportDialogContacts.show();
							break;
						case 'ab-company-grid':
							url = 'addressbook/company/export';
							name = 'company';
							documentTitle = 'ExportCompany';
							colmodel = this.companiesGrid.getColumnModel();
							
							if(!this.exportDialogCompanies) {
								this.exportDialogCompanies = new GO.ExportGridDialog({
									url: url,
									name: name,
									documentTitle: title,
									colModel: colmodel
								});
							} else {
								this.exportDialogCompanies.documentTitle=documentTitle;
								this.exportDialogCompanies.documentTitle=name;
								this.exportDialogCompanies.documentTitle=url;
								this.exportDialogCompanies.colmodel=colmodel;
							}
							this.exportDialogCompanies.show();
							break;
					}

				


//					config.title = activetab.title;
//					var query = this.searchPanel.queryField.getValue();
//					if(!GO.util.empty(query))
//					{
//						config.subtitle= GO.lang.searchQuery+': '+query;
//					}else
//					{
//						config.subtile='';
//					}
//
//					if(activetab.id == 'ab-contacts-grid')
//					{
//						if(!this.exportDialogExtended)
//						{
//							var columns=[];
//							for (var i = 0; i < this.companiesGrid.colModel.getColumnCount(); i++) {
//								var c = this.companiesGrid.colModel.config[i];
//								columns.push(c.dataIndex + ':' + GO.addressbook.lang.company +' '+c.header);
//							}
//
//							this.exportDialogExtended = new GO.ExportQueryDialog({
//								query:'contactsearch',
//								loadParams:{
//									export_directory:'modules/addressbook/exporters/',
//									books:this.contactsGrid.store.baseParams.books,
//									companyColumns:columns
//								},
//								customTypes:[{
//									boxLabel : GO.addressbook.lang.exportWithCompanies,
//									name : 'type',
//									inputValue : 'with_companies_export_query'
//								}]
//							});
//
//							config.subtitle= GO.lang.searchQuery+': '+query;
//						}else
//						{
//							config.subtitle='';
//						}
//
//
//						//config.showAllFields=true;
//
//
//						this.exportDialogExtended.show(config);
//					}else
//					{
//						if(!this.exportDialog)
//						{
//							this.exportDialog = new GO.ExportQueryDialog({
//								query:config.query
//							});
//						}
//
//						this.exportDialog.show(config);
//					}
				},
				scope: this
			})
		)
	}

	if(GO.email)
	{
		tbar.push('-');
		tbar.push({
			iconCls: 'ml-btn-mailings',
			text: GO.addressbook.lang.newsletters,
			cls: 'x-btn-text-icon',
			handler: function(){
				if(!this.mailingStatusWindow)
				{
					this.mailingStatusWindow = new GO.addressbook.MailingStatusWindow();
				}
				this.mailingStatusWindow.show();
			},
			scope: this
		});
	}
	config.tbar=new Ext.Toolbar({
		cls:'go-head-tb',
		items: tbar
	});


	/*config.listeners={
		scope:this,
		show:function(){
			this.searchPanel.queryField.focus(true);
		}
	}*/


	GO.addressbook.MainPanel.superclass.constructor.call(this, config);

};

Ext.extend(GO.addressbook.MainPanel, Ext.Panel,{

	setAdvancedSearchNotification : function (store)
	{
		if(!GO.util.empty(store.baseParams.advancedQuery))
		{
			this.searchPanel.queryField.setValue("[ "+GO.addressbook.lang.advancedSearch+" ]");
			this.searchPanel.queryField.setDisabled(true);
		}else
		{
			if(this.searchPanel.queryField.getValue()=="[ "+GO.addressbook.lang.advancedSearch+" ]")
			{
				this.searchPanel.queryField.setValue("");
			}
			this.searchPanel.queryField.setDisabled(false);
		}
	},

	init : function(){
		this.getEl().mask(GO.lang.waitMsgLoad);
		GO.request({
			maskEl:this.getEl(),
			url: "core/multiRequest",
			params:{
				requests:Ext.encode({
//					contacts:{r:"addressbook/contact/store"},
//					companies:{r:"addressbook/company/store"},
					addressbooks:{r:"addressbook/addressbook/store", limit: GO.settings.config.nav_page_size},
					writable_addresslists:{r:"addressbook/addresslist/store",permissionLevel: GO.permissionLevels.write},
					readable_addresslists:{r:"addressbook/addresslist/store",permissionLevel: GO.permissionLevels.read}
				})
			},
			success: function(options, response, result)
			{
				GO.addressbook.readableAddressbooksStore.loadData(result.addressbooks);
//				this.contactsGrid.store.loadData(result.contacts);
//				if(GO.addressbook)
//				{
					GO.addressbook.readableAddresslistsStore.loadData(result.readable_addresslists);
					GO.addressbook.writableAddresslistsStore.loadData(result.writable_addresslists);
//				}
				this.getEl().unmask();
			},
			scope:this
		});
	},

	afterRender : function()
	{
		GO.addressbook.MainPanel.superclass.afterRender.call(this);

		this.init();

		GO.dialogListeners.add('contact',{
			scope:this,
			'save':function(){
				var panel = this.tabPanel.getActiveTab();
				if(panel.id=='ab-contacts-grid')
				{
					this.contactsGrid.store.reload();
				}
			}
		});

		GO.dialogListeners.add('company',{
			scope:this,
			'save':function(){
				var panel = this.tabPanel.getActiveTab();
				if(panel.id=='ab-company-grid')
				{
					this.companiesGrid.store.reload();
				}
			}
		});
	},


	setSearchParams : function(params)
	{
		var panel = this.tabPanel.getActiveTab();

		for(var name in params)
		{
			if(name!='advancedQuery' || panel.id=='ab-contacts-grid')
			{
				this.contactsGrid.store.baseParams[name] = params[name];
			}
			if(name!='advancedQuery' || panel.id!='ab-contacts-grid')
			{
				this.companiesGrid.store.baseParams[name] = params[name];
			}
		}

		if(panel.id=='ab-contacts-grid')
		{
			this.contactsGrid.store.load();
		}else
		{
			this.companiesGrid.store.load();
		}
	}
});

GO.addressbook.showContactDialog = function(contact_id, config){

	if(!GO.addressbook.contactDialog)
		GO.addressbook.contactDialog = new GO.addressbook.ContactDialog();

	if(GO.addressbook.contactDialogListeners){
		GO.addressbook.contactDialog.on(GO.addressbook.contactDialogListeners);
		delete GO.addressbook.contactDialogListeners;
	}

	GO.addressbook.contactDialog.show(contact_id, config);
}

GO.addressbook.showCompanyDialog = function(company_id){

	if(!GO.addressbook.companyDialog)
		GO.addressbook.companyDialog = new GO.addressbook.CompanyDialog();

	if(GO.addressbook.companyDialogListeners){
		GO.addressbook.companyDialog.on(GO.addressbook.companyDialogListeners);
		delete GO.addressbook.companyDialogListeners;
	}

	GO.addressbook.companyDialog.show(company_id);
}

GO.addressbook.searchSenderStore = new GO.data.JsonStore({
	url: GO.url('addressbook/addressbook/searchSender'),
	baseParams: {
		email:''
	},
	root: 'results',
	totalProperty: 'total',
	id: 'id',
	fields:['id','name'],
	remoteSort:true
});

GO.addressbook.searchSender = function(sender, name){
	GO.addressbook.searchSenderStore.baseParams.email=sender;
	GO.addressbook.searchSenderStore.load({
		callback:function(){
			switch(GO.addressbook.searchSenderStore.getCount())
			{
				case 0:
					var names = name.split(' ');
					var params = {
						email:sender,
						first_name: names[0]
					};

					if(names[2])
					{
						params.last_name=names[2];
						params.middle_name=names[1];
					}else if(names[1])
					{
						params.middle_name='';
						params.last_name=names[1];
					}

					if(!GO.addressbook.unknownEmailWin)
					{
						GO.addressbook.unknownEmailWin=new GO.Window({
							title:GO.addressbook.lang.unknownEmail,
							items:{
								autoScroll:true,
								items: [{
									xtype: 'plainfield',
									hideLabel: true,
									value: GO.addressbook.lang.strUnknownEmail
								}],
								cls:'go-form-panel'
							},
							layout:'fit',
							autoScroll:true,
							closeAction:'hide',
							closeable:true,
							height:120,
							width:400,
							buttons:[{
								text: GO.lang.cmdAdd,
								handler: function(){
									GO.addressbook.showContactDialog(0, {values:GO.addressbook.unknownEmailWin.params});
									GO.addressbook.unknownEmailWin.hide();
								}
							},{
								text: GO.lang.cmdEdit,
								handler: function(){
									if(!GO.email.findContactDialog)
										GO.email.findContactDialog = new GO.email.FindContactDialog();

									GO.email.findContactDialog.show(GO.addressbook.unknownEmailWin.params);
									GO.addressbook.unknownEmailWin.hide();
								}
							},{
								text: GO.lang['cmdCancel'],
								handler: function(){
									GO.addressbook.unknownEmailWin.hide();
								}
							}],
						scope: this
						});
					}
					GO.addressbook.unknownEmailWin.params=params;
					GO.addressbook.unknownEmailWin.show();
					/*
					if(confirm(GO.addressbook.lang.confirmCreate))
					{
						GO.addressbook.showContactDialog();

						var names = name.split(' ');
						var params = {
							email:sender,
							first_name: names[0]
						};
						if(names[2])
						{
							params.last_name=names[2];
							params.middle_name=names[1];
						}else if(names[1])
						{
							params.last_name=names[1];
						}


						var tldi = sender.lastIndexOf('.');
						if(tldi)
						{
							var tld = sender.substring(tldi+1, sender.length).toUpperCase();
							if(GO.lang.countries[tld])
							{
								params.country=tld;
							}
						}

						GO.addressbook.contactDialog.formPanel.form.setValues(params);
					}*/

					break;
				case 1:
					var r = GO.addressbook.searchSenderStore.getAt(0);
					GO.linkHandlers["GO_Addressbook_Model_Contact"].call(this, r.get('id'));
					break;
				default:
					if(!GO.addressbook.searchSenderWin)
					{
						var list = new GO.grid.SimpleSelectList({
							store: GO.addressbook.searchSenderStore
						});

						list.on('click', function(dataview, index){
							var contact_id = dataview.store.data.items[index].id;
							list.clearSelections();
							GO.addressbook.searchSenderWin.hide();
							GO.linkHandlers["GO_Addressbook_Model_Contact"].call(this, contact_id);
						}, this);
						GO.addressbook.searchSenderWin=new GO.Window({
							title:GO.addressbook.lang.strSelectContact,
							items:{
								autoScroll:true,
								items: list,
								cls:'go-form-panel'
							},
							layout:'fit',
							autoScroll:true,
							closeAction:'hide',
							closeable:true,
							height:400,
							width:400,
							buttons:[{
								text: GO.lang['cmdClose'],
								handler: function(){
									GO.addressbook.searchSenderWin.hide();
								}
							}]
						});
					}
					GO.addressbook.searchSenderWin.show();
					break;
			}
		},
		scope:this
	});

}


GO.moduleManager.addModule('addressbook', GO.addressbook.MainPanel, {
	title : GO.addressbook.lang.addressbook,
	iconCls : 'go-tab-icon-addressbook'
});

GO.linkHandlers["GO_Addressbook_Model_Contact"]=GO.mailFunctions.showContact=GO.addressbook.showContact=function(id){
	if(!GO.addressbook.linkContactWindow){
		var contactPanel = new GO.addressbook.ContactReadPanel();
		GO.addressbook.linkContactWindow = new GO.LinkViewWindow({
			title: GO.addressbook.lang.contact,
			items: contactPanel,
			contactPanel: contactPanel,
			closeAction:"hide"
		});
	}
	GO.addressbook.linkContactWindow.contactPanel.load(id);
	GO.addressbook.linkContactWindow.show();
	return GO.addressbook.linkContactWindow;
}

GO.linkPreviewPanels["GO_Addressbook_Model_Contact"]=function(config){
	config = config || {};
	return new GO.addressbook.ContactReadPanel(config);
}

GO.linkPreviewPanels["GO_Addressbook_Model_Company"]=function(config){
	config = config || {};
	return new GO.addressbook.CompanyReadPanel(config);
}


GO.linkHandlers["GO_Addressbook_Model_Company"]=function(id){

	if(!GO.addressbook.linkCompanyWindow){
		var companyPanel = new GO.addressbook.CompanyReadPanel();
		GO.addressbook.linkCompanyWindow = new GO.LinkViewWindow({
			title: GO.addressbook.lang.company,
			items: companyPanel,
			companyPanel: companyPanel,
			closeAction:"hide"
		});
	}
	GO.addressbook.linkCompanyWindow.companyPanel.load(id);
	GO.addressbook.linkCompanyWindow.show();
	return GO.addressbook.linkCompanyWindow;
}