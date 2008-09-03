/** 
 * Copyright Intermesh
 * 
 * This file is part of Group-Office. You should have received a copy of the
 * Group-Office license along with Group-Office. See the file /LICENSE.TXT
 * 
 * If you have questions write an e-mail to info@intermesh.nl
 * 
 * @copyright Copyright Intermesh
 * @version $Id: CompanyDialog.js 2942 2008-09-02 12:24:54Z mschering $
 * @author Merijn Schering <mschering@intermesh.nl>
 */

GO.addressbook.CompanyDialog = function(config)
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
	
	
	this.personalPanel = new GO.addressbook.CompanyProfilePanel();	    
		    
	this.commentPanel = new Ext.Panel({
		title: GO.addressbook.lang['cmdPanelComments'], 
  	layout: 'fit',
  	border:false,
		items: [
			new Ext.form.TextArea({
				name: 'comment',
				id: 'comment-company',
				fieldLabel: '',
				hideLabel: true
			})
		]
	});

	/* employees Grid */
  this.employeePanel = new GO.addressbook.EmployeesPanel();

  
  var items = [
	      	this.personalPanel,
	      	this.commentPanel];
	      	
	if(GO.mailings)
	{
		items.push(new GO.mailings.SelectMailingsPanel());
	}
	items.push(this.employeePanel);
	this.linksPanel = new GO.grid.LinksPanel({title: GO.lang['strLinks']});
	items.push(this.linksPanel);
	
	if(GO.files)
	{
		items.push(this.fileBrowser);
	}
	     
  
  if(GO.customfields && GO.customfields.types["3"])
	{
  	for(var i=0;i<GO.customfields.types["3"].panels.length;i++)
  	{			  	
  		items.push(GO.customfields.types["3"].panels[i]);
  	}
	}
	
	
	this.companyForm = new Ext.FormPanel({
		id: 'addressbook-form-company',
		border: false,
		baseParams: {},
    items: [
    	this.tabPanel = new Ext.TabPanel({
    		border: false,
    		activeTab: 0,
    		deferredRender: false,
    		hideLabel: true,
    		anchor:'100% 100%',
	      items: items       		
    	})
    ]
	});				
    


	this.id= 'addressbook-window-new-company';
	this.layout= 'fit';
	this.modal= false;
	this.shadow= false;
	this.border= false;
	this.height= 470;
	this.width= 820;
	this.plain= true;
	this.closeAction= 'hide';
	//this.iconCls= 'btn-addressbook-company';
	this.title= GO.addressbook.lang['cmdCompanyDialog'];
	this.items= this.companyForm;
	this.buttons=  [
			{
				id: 'ok', 
				text: GO.lang['cmdOk'], 
				handler: function(){
					this.saveCompany(true);
				}, 
				scope: this 
			},
			{
				id: 'apply', 
				text: GO.lang['cmdApply'], 
				handler: function(){
					this.saveCompany();
				}, 
				scope: this 
			},
			{
				id: 'close', 
				text: GO.lang['cmdClose'], 
				handler: function()
				{
					this.hide();
				}, 
				scope: this 
			}
		];
	this.focus= function(){
		Ext.get('companyName').focus();
	}					


	GO.addressbook.CompanyDialog.superclass.constructor.call(this);
	
	this.addEvents({'save':true});
}
	
Ext.extend(GO.addressbook.CompanyDialog, Ext.Window, {
		
	show : function(company_id)
	{
		if(GO.mailings && !GO.mailings.writableMailingsStore.loaded)
		{
			GO.mailings.writableMailingsStore.load({
				callback:function(){
					this.show(company_id);
				},
				scope:this
			});
		}else
		{
			if(!this.rendered)
			{
				this.render(Ext.getBody());
			}
			
			
			
			if(company_id)
			{
				this.company_id = company_id;
			} else {
				this.company_id = 0;
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
		
			this.tabPanel.setActiveTab(0);
			
			if(this.company_id > 0)
			{
				this.loadCompany(company_id);				
			} else {
				this.linksPanel.setDisabled(true);
				this.employeePanel.setCompanyId(0);
				var tempAddressbookID = this.personalPanel.formAddressBooks.getValue();
				
				this.companyForm.form.reset();
				this.personalPanel.formAddressBooks.setValue(tempAddressbookID);	
				
				this.personalPanel.setCompanyId(0);
				
				
				if(GO.files)
				{
					this.fileBrowser.setDisabled(true);
				}
				
				
				GO.addressbook.CompanyDialog.superclass.show.call(this);
			}		
		}
	},	

	loadCompany : function(id)
	{
		this.companyForm.form.load({
			url: GO.settings.modules.addressbook.url+ 'json.php', 
			params: {company_id: id, task: 'load_company'},
			waitMsg:GO.lang['waitMsgLoad'],
			success: function(form, action) {
				
				if(!action.result.data.write_permission)
				{
					Ext.Msg.alert(GO.lang['strError'], GO.lang['strNoWritePermissions']);						
				}else
				{					
					this.linksPanel.loadLinks(action.result.data['id'], 3);
					this.employeePanel.setCompanyId(action.result.data['id']);
					this.personalPanel.setCompanyId(action.result.data['id']);
					
					if(GO.files)
					{
						this.fileBrowser.setRootPath(action.result.data.files_path);
						this.fileBrowser.setDisabled(false);
					}	
					
					GO.addressbook.CompanyDialog.superclass.show.call(this);
				}						
	   	},
		  failure: function(form, action)
		  {
				if(action.failureType == 'client')
				{					
					Ext.MessageBox.alert(GO.lang['strError'], GO.lang['strErrorsInForm']);			
				} else {
					Ext.MessageBox.alert(GO.lang['strError'], action.result.feedback);
				}			 		
		  },
		 scope: this
		});			
	},

	
	saveCompany : function(hide)
	{	
		this.companyForm.form.submit({
			url:GO.settings.modules.addressbook.url+ 'action.php',
			params:
			{
				task : 'save_company',
				company_id : this.company_id
			},
			waitMsg:GO.lang['waitMsgSave'],
			success:function(form, action){
				if(action.result.company_id)
				{
					this.company_id = action.result.company_id;				
					this.linksPanel.loadLinks(action.result.company_id, 3);
					this.employeePanel.setCompanyId(action.result.company_id);
					
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
