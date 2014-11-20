/** 
 * Copyright Intermesh
 * 
 * This file is part of Group-Office. You should have received a copy of the
 * Group-Office license along with Group-Office. See the file /LICENSE.TXT
 * 
 * If you have questions write an e-mail to info@intermesh.nl
 * 
 * @copyright Copyright Intermesh
 * @version $Id$
 * @author Merijn Schering <mschering@intermesh.nl>
 * @author Wilmar van Beusekom <wilmar@intermesh.nl>
 */

 
GO.addressbook.MailingDialog = function(config){
	
	
	if(!config)
	{
		config={};
	}
	
	
	this.buildForm();
	
	var focusFirstField = function(){
		this.propertiesPanel.items.items[0].focus();
	};
	
	
	config.maximizable=true;
	config.layout='fit';
	config.modal=false;
	config.resizable=false;
	config.width=750;
	config.height=600;
	config.closeAction='hide';
	config.title= GO.addressbook.lang.addresslist;
	config.items= this.formPanel;
	config.focus= focusFirstField.createDelegate(this);
	config.buttons=[{
			text: GO.lang['cmdOk'],
			handler: function(){
				this.submitForm(true);
			},
			scope: this
		},{
			text: GO.lang['cmdApply'],
			handler: function(){
				this.submitForm();
			},
			scope:this
		},{
			text: GO.lang['cmdClose'],
			handler: function(){
				this.hide();
			},
			scope:this
		}					
	];

	
	GO.addressbook.MailingDialog.superclass.constructor.call(this, config);
	
	this.addEvents({'save' : true});	
}

Ext.extend(GO.addressbook.MailingDialog, Ext.Window,{
	
	show : function (mailing_id) {
		
		if(!this.rendered)
		{
			this.render(Ext.getBody());
		}

		this.tabPanel.setActiveTab(0);

		if(!mailing_id)
		{
			mailing_id=0;			
		}
			
		this.setMailingId(mailing_id);
		
		if(this.mailing_id>0)
		{
			this.formPanel.load({
				url : GO.url('addressbook/addresslist/load'),
				
				success:function(form, action)
				{
					this.readPermissionsTab.setAcl(action.result.data.acl_id);
					this.selectUser.setRemoteText(action.result.data.user_name);					
					this.inline_attachments = action.result.data.inline_attachments;	
					
					GO.addressbook.MailingDialog.superclass.show.call(this);
				},
				failure:function(form, action)
				{
					GO.errorDialog.show(action.result.feedback)
				},
				scope: this
				
			});
		}else 
		{
			
			this.formPanel.form.reset();
			
			GO.addressbook.MailingDialog.superclass.show.call(this);
		}
	},
	
	

	setMailingId : function(mailing_id)
	{
		this.contactsGrid.setMailingId(mailing_id);
		this.companiesGrid.setMailingId(mailing_id);
//		this.usersGrid.setMailingId(mailing_id);
		
		
		
		this.formPanel.form.baseParams['id']=mailing_id;
		this.mailing_id=mailing_id;		
		
		if(this.mailing_id==0)
		{
			this.readPermissionsTab.setAcl(0);
		}
		
	},
	
	
	submitForm : function(hide){
		this.formPanel.form.submit(
		{
			url: GO.url('addressbook/addresslist/submit'),
			params: {
				inline_attachments: Ext.encode(this.inline_attachments)
			},
			waitMsg:GO.lang['waitMsgSave'],
			success:function(form, action){
				
				this.fireEvent('save', this);
				
				if(hide)
				{
					this.hide();	
				}else
				{
					if(action.result.id)
					{
						this.setMailingId(action.result.id);
						
						this.readPermissionsTab.setAcl(action.result.acl_id);
						
					}
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
		
	},
	
	
	buildForm : function () {
		
		this.propertiesPanel = new Ext.Panel({
			url: GO.url('addressbook/addresslist/load'),
			border: false,	
			title:GO.lang['strProperties'],			
			cls:'go-form-panel',			
			layout:'form',
			autoScroll:true,
			items:[{
				xtype: 'textfield',
			  name: 'name',
				anchor: '100%',
			  allowBlank:false,
			  fieldLabel: GO.lang.strName
			},new GO.form.HtmlComponent({
				html: GO.addressbook.lang.defaultSalutationText,
				style:'padding:10px 0px'
			}),{
				xtype: 'textfield',
			  name: 'default_salutation',
				anchor: '100%',
			  allowBlank:false,
			  fieldLabel: GO.addressbook.lang.cmdFormLabelSalutation,
			  value: GO.addressbook.lang.cmdSalutation+' '+GO.addressbook.lang.cmdSir+'/'+GO.addressbook.lang.cmdMadam
			},this.selectUser = new GO.form.SelectUser({
				fieldLabel:GO.lang.strOwner,
				disabled: !GO.settings.has_admin_permission,
				value: GO.settings.user_id,
				anchor: '100%'
			})]
		});

		var items  = [this.propertiesPanel];
		
		this.contactsGrid = new GO.addressbook.AddresslistContactsGrid();
		items.push(this.contactsGrid);
		
		this.companiesGrid = new GO.addressbook.AddresslistCompaniesGrid();
		items.push(this.companiesGrid);	
		
    this.readPermissionsTab = new GO.grid.PermissionsPanel({
			title: GO.lang['strPermissions']
		});
	
    
    items.push(this.readPermissionsTab);
		
 
    this.tabPanel = new Ext.TabPanel({
      activeTab: 0,      
      deferredRender: false,
    	border: false,
      items: items,
      anchor: '100% 100%'
    }) ;    
    
    
    this.formPanel = new Ext.form.FormPanel({
    	waitMsgTarget:true,
			border: false,
			baseParams: new Object(),				
			items:this.tabPanel				
		});
    
    
	}
});


