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
 
GO.users.UserDialog = function(config){
	
	
	if(!config)
	{
		config={};
	}
	
	this.buildForm();


	
	config.tbar = [
		this.linkBrowseButton = new Ext.Button({
			iconCls: 'btn-link', 
			cls: 'x-btn-text-icon', 
			text: GO.lang.cmdBrowseLinks,
			disabled:true,
			handler: function(){
				GO.linkBrowser.show({link_id: this.user_id,link_type: "8",folder_id: "0"});				
			},
			scope: this
		})];
		
		if(GO.files)
		{		
			config.tbar.push(this.fileBrowseButton = new Ext.Button({
				iconCls: 'go-menu-icon-files', 
				cls: 'x-btn-text-icon', 
				text: GO.files.lang.files,
				handler: function(){
					GO.files.openFolder(this.files_folder_id);				
				},
				scope: this,
				disabled: true
			}));
		}
	

	config.layout='fit';
	config.modal=false;
	config.resizable=true;
	config.maximizable=true;
	config.width=750;
	config.collapsible=true;
	config.height=430;
	config.closeAction='hide';
	config.title= GO.users.lang.userSettings;					
	config.items= this.formPanel;
	config.buttons=[{
			text: GO.lang['cmdOk'],
			handler: function(){
				this.submitForm(true);
			},
			scope: this
		},{
			text: GO.lang.cmdSavePlusNew,
			handler: function(){
				this.submitForm(false, true);
				
			},
			scope:this
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

	
	GO.users.UserDialog.superclass.constructor.call(this, config);
	
	
	this.addEvents({'save' : true, 'set_id' : true});	
}

Ext.extend(GO.users.UserDialog, Ext.Window,{

	user_id : 0,
	
	files_folder_id : '',
	
	setUserId : function(user_id){
		this.formPanel.form.baseParams['user_id']=user_id;
		this.user_id=user_id;	
		
		this.permissionsTab.setUserId(user_id);
		this.accountTab.setUserId(user_id);
		
		if(this.serverclientFieldSet)
		{
			var visible = user_id>0;
			this.serverclientFieldSet.setVisible(!visible);
		}		
		
		this.linkBrowseButton.setDisabled(user_id<1);
		if(GO.files)
		{
			this.fileBrowseButton.setDisabled(user_id<1);
		}

		this.fireEvent('set_id', this);
	},
	
	serverclientDomainCheckboxes : [],
	
	
	setDefaultEmail : function(){
		
		if(this.rendered)
		{
			for(var i=0;i<this.serverclientDomainCheckboxes.length;i++)
			{
				if(this.serverclientDomainCheckboxes[i].getValue())
				{
					var username = this.formPanel.form.findField('username').getValue();
					var emailField = this.formPanel.form.findField('email');
					
					if(emailField)
						this.formPanel.form.findField('email').setValue(username+'@'+GO.serverclient.domains[i]);
						
					break;
				}
			}
		}	
	},
	
	
	show : function (user_id) {
		
		if(!this.rendered)
		{
			if(GO.serverclient && GO.serverclient.domains)
			{				
				this.serverclientFieldSet = new Ext.form.FieldSet({
					title: GO.serverclient.lang.mailboxes, 
					autoHeight:true,
					items:new GO.form.HtmlComponent({
						html:'<p class="go-form-text">'+GO.serverclient.lang.createMailbox+':</p>'
					})
				});
				
				for(var i=0;i<GO.serverclient.domains.length;i++)
				{
					this.serverclientDomainCheckboxes[i]=new Ext.form.Checkbox({						
						checked:(i==0),
						name:'serverclient_domains[]',
						autoCreate: {tag: "input", type: "checkbox", value: GO.serverclient.domains[i]},						
						hideLabel:true,
						boxLabel: GO.serverclient.domains[i]
					});
					
					this.serverclientDomainCheckboxes[i].on('check', this.setDefaultEmail, this);
					this.serverclientFieldSet.add(this.serverclientDomainCheckboxes[i]);
				}
				
				this.accountTab.add(this.serverclientFieldSet);
			}		
			
			this.render(Ext.getBody());
		}
		
		if(GO.serverclient && GO.serverclient.domains)
		{
			this.formPanel.form.findField('username').on('change', this.setDefaultEmail, this);
		}
		
		this.accountTab.show();

		//reset form
		this.formPanel.form.reset();
		
		this.setUserId(user_id);
		
		if(user_id>0)
		{
			this.formPanel.load({
				url : GO.settings.modules.users.url+'json.php',
				
				success:function(form, action)
				{				
					this.loaded=true;
					GO.users.UserDialog.superclass.show.call(this);
					
					this.files_folder_id = action.result.data.files_folder_id;
					
					this.lookAndFeelTab.startModuleField.setRemoteText(action.result.data.start_module_name);
				},
				failure:function(form, action)
				{
					Ext.Msg.alert(GO.lang['strError'], action.result.feedback)
				},
				scope: this
				
			});
		}else
		{			
			GO.users.UserDialog.superclass.show.call(this);
		}
	},
	

	submitForm : function(hide, reset){
		var params = this.permissionsTab.getPermissionParameters();
		
		params.task='save_user';
		
		this.formPanel.form.submit(
		{
			url:GO.settings.modules.users.url+'action.php',
			params: params,
			waitMsg:GO.lang['waitMsgSave'],
			success:function(form, action){
				
				
				this.fireEvent('save', this);
				
				if(hide)
				{
					this.hide();
				}else if(reset)
				{
					
					this.setUserId(0);
					
					var resetFields = [
						'username',
						'password1',
						'password2',
						'first_name',
						'middle_name',
						'last_name',
						'title',
						'initials',
						'sex',
						'birthday',
						'address',
						'address_no',
						'city',
						'zip',
						'email',
						'home_phone',
						'fax',
						'cellular',
						'department',
						'function'
					];
					
					for(var i=0;i<resetFields.length;i++)
					{
						this.formPanel.form.findField(resetFields[i]).reset();
					}
					
					this.permissionsTab.onShow();
					
					
				}else if(action.result.user_id)
				{
					this.setUserId(action.result.user_id);
					
					this.files_folder_id = action.result.files_folder_id;
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
		this.accountTab = new GO.users.AccountPanel();			
		this.personalTab = new GO.users.PersonalPanel();		
		this.companyTab = new GO.users.CompanyPanel();
		this.loginTab = new GO.users.LoginPanel();		
		this.permissionsTab = new GO.users.PermissionsPanel();
		this.regionalSettingsTab = new GO.users.RegionalSettingsPanel();
		this.lookAndFeelTab = new GO.users.LookAndFeelPanel();
 
    this.tabPanel = new Ext.TabPanel({     
      deferredRender: false,
			anchor:'100% 100%',
      layoutOnTabChange:true,
    	border: false,
      items: [
      	this.accountTab,
      	this.personalTab,
      	this.companyTab,
      	this.loginTab,
      	this.permissionsTab,
      	this.regionalSettingsTab,
      	this.lookAndFeelTab
      ]
    }) ;    
    
    this.formPanel = new Ext.form.FormPanel({
			items:this.tabPanel,
			baseParams:{task:'user'},
	    waitMsgTarget:true,
	    border:false
		});
    
    
	}
});