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
	
  this.companiesGrid = new GO.addressbook.CompaniesGrid({
  	layout: 'fit',
  	region: 'center',
  	id: 'ab-company-grid-panel'  	
  });
  
  this.companiesGrid.on("delayedrowselect",function(grid, rowIndex, r){
			this.companyEastPanel.load(r.get('id'));			
		}, this); 
	  
	this.searchPanel = new GO.addressbook.SearchPanel({
		region: 'north'
	});
	
	this.searchPanel.on('queryChange', function(params){		
		this.setSearchParams(params);
	}, this);	
	
	this.contactEastPanel = new GO.addressbook.ContactReadPanel({
		region : 'east',
		title: GO.addressbook.lang['cmdPanelContact'],
		width:400
	});
	
	this.companyEastPanel = new GO.addressbook.CompanyReadPanel({
		region : 'east',
		title: GO.addressbook.lang['cmdPanelCompany'],
		width:400
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
	this.contactsPanel.on("show", this.contactsGrid.onGridShow, this.contactsGrid);
	
	this.companyPanel = new Ext.Panel({
	    	id: 'ab-company-grid',
	    	title: GO.addressbook.lang.companies,
	    	layout: 'border',
	    	items:[
		    	this.companiesGrid,
		    	this.companyEastPanel
	    	]
	    });
	
	this.companyPanel.on("show",this.companiesGrid.onGridShow, this.companiesGrid);
	
	
	this.addressbooksGrid = new GO.addressbook.AddresbooksGrid({
		region:'west',
		width:180
	});
	
	this.addressbooksGrid.on('rowclick', function(grid, rowIndex){
			var record = grid.getStore().getAt(rowIndex);
			this.setSearchParams({addressbook_id : record.get("id")});			
	}, this);

	
	this.tabPanel = new Ext.TabPanel({
    region : 'center',
    activeTab: 0,
  	border: true,
    items: [
	    this.contactsPanel,
	    this.companyPanel				
    ]
	});

	config.layout='border';
	config.border=false;
	config.id='ab-tbar';
	
	if(GO.mailings)
	{
		this.mailingsFilterPanel = new GO.mailings.MailingsFilterPanel({
			region:'center'
		});
		
		GO.mailings.readableMailingsStore.on('load', function(){
			if(GO.mailings.readableMailingsStore.getCount()==0)
			{
				this.mailingsFilterPanel.hide();
			}else
			{
				this.mailingsFilterPanel.show();
			}
		}, this);
		
		this.mailingsFilterPanel.on('change', function(grid, mailings_filter){
			var panel = this.tabPanel.getActiveTab();		
			if(panel.id=='ab-contacts-grid')
			{
				this.contactsGrid.store.baseParams.mailings_filter = Ext.encode(mailings_filter);
				this.contactsGrid.store.load();
				delete this.contactsGrid.store.baseParams.mailings_filter;
			}else
			{
				this.companiesGrid.store.baseParams.mailings_filter = Ext.encode(mailings_filter);
				this.companiesGrid.store.load();
				delete this.companiesGrid.store.baseParams.mailings_filter;
			}			
		}, this);
			
		this.addressbooksGrid.region='north';
		this.addressbooksGrid.height=200;
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
	}else
	{
		config.items= [
			this.searchPanel,
			this.addressbooksGrid,
			this.tabPanel
		];
	}
	
	var tbar=[
		{
			iconCls: 'btn-addressbook-add-contact', 
			text: GO.addressbook.lang['btnAddContact'], 
			cls: 'x-btn-text-icon', 
			handler: function(){
				GO.addressbook.contactDialog.show(0);
				this.tabPanel.setActiveTab('ab-contacts-grid');
			}, 
			scope: this
		},
		{
			iconCls: 'btn-addressbook-add-company', 
			text: GO.addressbook.lang['btnAddCompany'], 
			cls: 'x-btn-text-icon',
			handler: function(){
				GO.addressbook.companyDialog.show(0);
					this.tabPanel.setActiveTab('ab-company-grid');
			},  
			scope: this
		},
		{
			iconCls: 'btn-addressbook-manage', 
			text: GO.addressbook.lang['btnManage'], 
			cls: 'x-btn-text-icon', 
			handler:function(){
				if(!this.manageDialog)
				{
					this.manageDialog = new GO.addressbook.ManageDialog();
				}
				this.manageDialog.show();
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
						this.contactsGrid.deleteSelected();
					break;
					case 'ab-company-grid':					
						this.companiesGrid.deleteSelected();
					break;				
				}	
			}, 
			scope: this
		}
		
	];

	if(GO.mailings)
	{
		tbar.push({
				iconCls: 'ml-btn-mailings', 
				text: GO.addressbook.lang.sendMailing, 
				cls: 'x-btn-text-icon', 
				handler: function(){
					if(!this.selectMailingGroupWindow)
					{
						this.selectMailingGroupWindow=new GO.mailings.SelectMailingGroupWindow();
					}	
					this.selectMailingGroupWindow.show();
				}, 
				scope: this
			});
	}
	config.tbar=new Ext.Toolbar({		
			cls:'go-head-tb',
			items: tbar});
	
	
	GO.addressbook.MainPanel.superclass.constructor.call(this, config);
	
};

Ext.extend(GO.addressbook.MainPanel, Ext.Panel,{
		
		afterRender : function()
		{
			GO.addressbook.MainPanel.superclass.afterRender.call(this);

			GO.addressbook.readableAddressbooksStore.load();
			
			if(GO.mailings)
			{
				GO.mailings.ooTemplatesStore.load();			
				GO.mailings.writableMailingsStore.load();
			}

						
			GO.addressbook.contactDialog.on('save', function(){
				var panel = this.tabPanel.getActiveTab();		
				if(panel.id=='ab-contacts-grid')
				{
					this.contactsGrid.store.reload();
				}
			}, this);
			
			
			GO.addressbook.companyDialog.on('save', function(){
				var panel = this.tabPanel.getActiveTab();		
				if(panel.id=='ab-company-grid')
				{
					this.companiesGrid.store.reload();
				}
			}, this);
			
		},
		

		setSearchParams : function(params)
		{
			for(var name in params)
			{
				this.contactsGrid.store.baseParams[name] = params[name];
				this.companiesGrid.store.baseParams[name] = params[name];
			}
			
			var panel = this.tabPanel.getActiveTab();		
			if(panel.id=='ab-contacts-grid')
			{
				this.companiesGrid.loaded=false;
				this.contactsGrid.store.load();
			}else
			{
				this.contactsGrid.loaded=false;
				this.companiesGrid.store.load();
			}
		},
		

		
		rowDoubleClick : function()
		{
			var activetab = this.tabPanel.getActiveTab();
			
			switch(activetab.id)
			{
				case 'ab-contacts-grid':
					GO.addressbook.contactDialog.show(this.contactsGrid.selModel.selections.items[0].data.id);
				break;
				case 'ab-company-grid':
					GO.addressbook.companyDialog.show(this.companiesGrid.selModel.selections.items[0].data.id);
				break;				
			}
		}
});


GO.mainLayout.onReady(function(){
	GO.addressbook.contactDialog = new GO.addressbook.ContactDialog();
	GO.addressbook.companyDialog = new GO.addressbook.CompanyDialog();
});
			

GO.addressbook.searchSender = function(sender, name){
	
	Ext.Ajax.request({
		url: GO.settings.modules.addressbook.url+'json.php',
		params: {
			task:'search_sender',
			email: sender			
		},
		callback: function(options, success, response)
		{
			if(!success)
			{
				alert( GO.lang['strRequestError']);
			}else
			{	
				
				var responseParams = Ext.decode(response.responseText);
				if(!responseParams.contact_id)
				{
					if(confirm(GO.addressbook.lang.confirmCreate))
					{
						GO.addressbook.contactDialog.show();
						
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
						
						GO.addressbook.contactDialog.formPanel.form.setValues(params);				
					}
				}else
				{
					GO.linkHandlers[2].call(this, responseParams.contact_id);
				}			
			}
		}	
			
		
	});
	
}


GO.moduleManager.addModule('addressbook', GO.addressbook.MainPanel, {
	title : GO.addressbook.lang.addressbook,
	iconCls : 'go-tab-icon-addressbook'
});

GO.linkHandlers[2]=function(id){
		//GO.addressbook.contactDialog.show(id);
		
	var contactPanel = new GO.addressbook.ContactReadPanel();
	var linkWindow = new GO.LinkViewWindow({
		title: GO.addressbook.lang.contact,
		items: contactPanel
	});
	contactPanel.load(id);
	linkWindow.show();
}

GO.linkHandlers[3]=function(id){
	//GO.addressbook.companyDialog.show(id);	
	
	var companyPanel = new GO.addressbook.CompanyReadPanel();
	var linkWindow = new GO.LinkViewWindow({
		title: GO.addressbook.lang.company,
		items: companyPanel
	});
	companyPanel.load(id);
	linkWindow.show();
}
