/** 
 * Copyright Intermesh
 * 
 * This file is part of Group-Office. You should have received a copy of the
 * Group-Office license along with Group-Office. See the file /LICENSE.TXT
 * 
 * If you have questions write an e-mail to info@intermesh.nl
 * 
 * @copyright Copyright Intermesh
 * @version $Id: ContactDialog.js 2942 2008-09-02 12:24:54Z mschering $
 * @author Merijn Schering <mschering@intermesh.nl>
 */


GO.addressbook.ContactDialog = function(config)
{
	Ext.apply(this, config);
	
	if(GO.files)
	{
		this.fileBrowser = new GO.files.FileBrowser({
			title: GO.lang.strFiles, 
			treeRootVisible:true, 
			treeCollapsed:true,
			disabled:true
			});
	}
	
	this.personalPanel = new GO.addressbook.ContactProfilePanel();
			
	this.commentPanel = new Ext.Panel({
		title: GO.addressbook.lang['cmdPanelComments'], 
		layout: 'fit',
		border:false,
		items: [ new Ext.form.TextArea({
			name: 'comment',
			id: 'comment',
			fieldLabel: '',
			hideLabel: true,
			anchor:'100% 100%'
		})
		]
	});
	
	this.personalPanel.on('show', 
		function()
		{ 
			var firstName = Ext.get('first_name');					
			if (firstName)
			{
				firstName.focus();
			}
		}, this);
		
	this.commentPanel.on('show', function(){ Ext.get('comment').focus(); }, this);
	
	//var selectMailingsPanel = new GO.addressbook.SelectMailingsPanel();
	
	var items = [
	      	this.personalPanel,
	      	this.commentPanel];
	      
	if(GO.mailings)
	{	
		items.push(new GO.mailings.SelectMailingsPanel());
	}
	
	this.linksPanel = new GO.grid.LinksPanel({title: GO.lang['strLinks']});
	items.push(this.linksPanel);				      	
	      
  if(GO.files)
	{
		items.push(this.fileBrowser);
	}
  
  if(GO.customfields && GO.customfields.types["2"])
	{
  	for(var i=0;i<GO.customfields.types["2"].panels.length;i++)
  	{			  	
  		items.push(GO.customfields.types["2"].panels[i]);
  	}
	}
	
			
	this.formPanel = new Ext.FormPanel({
		url: GO.settings.modules.addressbook.url+ 'json.php',
		baseParams: {},
		border: false,
    items: [
    	this.tabPanel = new Ext.TabPanel({
    		border: false,
    		activeTab: 0,
    		hideLabel: true,
    		deferredRender: false,
    		anchor:'100% 100%',
	      items: items   		
    	})
    ]
	});
	
	
	this.downloadDocumentButton = new Ext.Button();					

	this.layout= 'fit';
	this.modal=false;
	this.animCollapse=false;
	this.shadow= false;
	this.border= false;
	this.height= 545;
	//autoHeight= true;
	this.width= 820;
	this.plain= true;
	this.closeAction= 'hide';
	//this.iconCls= 'btn-addressbook-contact';
	this.title= GO.addressbook.lang['cmdContactDialog'];
	this.items= this.formPanel;
	this.buttons= [
		{ 
			text: GO.lang['cmdOk'], 
			handler:function(){
				this.saveContact(true);
				}, 
			scope: this 
		},
		{ 
			text: GO.lang['cmdApply'], 
			handler: function(){
				this.saveContact();
				}, 
			scope: this 
		},
		{		 
			text: GO.lang['cmdClose'], 
			handler: function()
			{
				this.hide();
			}, 
			scope: this 
		}
	];
	this.focus= function(){
		Ext.get('first_name').focus();
	}
	
	
	GO.addressbook.ContactDialog.superclass.constructor.call(this);
	
	this.addEvents({'save':true});
}

Ext.extend(GO.addressbook.ContactDialog, Ext.Window, {

	show : function(contact_id)
	{
		if(!this.rendered)
		{
			this.render(Ext.getBody());
		}
		
		if(GO.mailings && !GO.mailings.writableMailingsStore.loaded)
		{
			GO.mailings.writableMailingsStore.load({
				callback:function(){
					
					var values = this.formPanel.form.getValues();
					this.show(contact_id);
					this.formPanel.form.setValues(values);
				},
				scope:this
			});
		}else
		{
				
			
				
			if(contact_id)
			{
				this.contact_id = contact_id;
			} else {
				this.contact_id = 0;
			}			
			
			
			
			if(!GO.addressbook.writableAddressbooksStore.loaded)
			{
				GO.addressbook.writableAddressbooksStore.load(
				{
					callback: function(){		
						GO.addressbook.writableAddressbooksStore.loaded=true;
						if(this.personalPanel.formAddressBooks.getValue()<1)
						{
							this.personalPanel.formAddressBooks.selectFirst();
						}			
					},
					scope:this
				});
			}else
			{
				if(this.personalPanel.formAddressBooks.getValue()<1)
				{
					this.personalPanel.formAddressBooks.selectFirst();
				}	
			}
			
	
						
			if(this.contact_id > 0)
			{
				this.loadContact(contact_id);
			} else {				
				var tempAddressbookID = this.personalPanel.formAddressBooks.getValue();
				this.formPanel.form.reset();
							
				this.personalPanel.formAddressBooks.setValue(tempAddressbookID);	
				this.linksPanel.setDisabled(true);			
				
				if(GO.files)
				{
					this.fileBrowser.setDisabled(true);
				}
				
				GO.addressbook.ContactDialog.superclass.show.call(this);
			}	
			this.tabPanel.setActiveTab(0);
		}	
	},

	
	/*setAddressbookId : function(addressbook_id)
	{
		this.personalPanel.formAddressBooks.setValue(addressbook_id);
		this.personalPanel.formCompany.store.baseParams['addressbook_id'] = addressbook_id;			
		this.addressbook_id = addressbook_id;
	},*/
	
	loadContact : function(id)
	{
		this.formPanel.form.load({
			url: GO.settings.modules.addressbook.url+ 'json.php', 
			params: {contact_id: id, task: 'load_contact'},
			//
			success: function(form, action) {
				
				if(!action.result.data.write_permission)
				{
					Ext.Msg.alert(GO.lang['strError'], GO.lang['strNoWritePermissions']);						
				}else
				{
					
					this.personalPanel.setAddressbookID(action.result.data.addressbook_id);
					//this.oldAddressbookId = task.result.data.addressbook_id;
					//this.oldCompanyId = task.result.data.company_id;
					
					this.formPanel.form.findField('company_id').setRemoteText(action.result.data.company_name);
					this.linksPanel.loadLinks(action.result.data['id'], 2);
					
					
					if(GO.files)
					{
						this.fileBrowser.setRootPath(action.result.data.files_path);
						this.fileBrowser.setDisabled(false);
					}	
					
					GO.addressbook.ContactDialog.superclass.show.call(this);
				}
	    },
	    scope: this
		});
	},
	
	saveContact : function(hide)
	{		
		var company = this.personalPanel.formCompany.getRawValue();
		
		this.formPanel.form.submit({
			url:GO.settings.modules.addressbook.url+ 'action.php',
			waitMsg:GO.lang['waitMsgSave'],
			params:
			{
				task : 'save_contact',
				contact_id : this.contact_id,
				company: company
			},
			success:function(form, action){
				if(action.result.contact_id)
				{
					this.contact_id = action.result.contact_id;
					this.linksPanel.loadLinks(action.result.contact_id, 2);
					
					if(GO.files && action.result.files_path)
					{
						this.fileBrowser.setRootPath(action.result.files_path);
						this.fileBrowser.setDisabled(false);
					}	
				}
				this.fireEvent('save', this);

				if (hide)
				{
					this.hide();
				}
			},
			failure: function(form, action) {				
				if(action.failureType == 'client')
				{					
					Ext.MessageBox.alert(GO.lang['strError'], GO.lang['strErrorsInForm']);			
				} else {
					Ext.MessageBox.alert(GO.lang['strError'], action.result.feedback);
				}
			},
			scope: this
		});	
	}
});
