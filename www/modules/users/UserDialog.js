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
 GO.users.UserDialog = Ext.extend(GO.dialog.TabbedFormDialog , {
	
	customFieldType : "GO_Base_Model_User",
	initComponent : function(){
		
		Ext.apply(this, {
			goDialogId:'user',
			title:GO.users.lang.userSettings,
			formControllerUrl: 'users/user',
			height:580,
			width:800
			
		});
		
//		this.tbar = [
//		this.linkBrowseButton = new Ext.Button({
//			iconCls: 'btn-link', 
//			cls: 'x-btn-text-icon', 
//			text: GO.lang.cmdBrowseLinks,
//			disabled:true,
//			handler: function(){
//				if(!GO.linkBrowser){
//					GO.linkBrowser = new GO.LinkBrowser();
//				}
//				GO.linkBrowser.show({link_id: this.user_id,link_type: "8",folder_id: "0"});				
//			},
//			scope: this
//		})];
//		
//		if(GO.files)
//		{		
//			this.tbar.push(this.fileBrowseButton = new Ext.Button({
//				iconCls: 'btn-files',
//				cls: 'x-btn-text-icon', 
//				text: GO.files.lang.files,
//				handler: function(){
//					GO.files.openFolder(this.files_folder_id);				
//				},
//				scope: this,
//				disabled: true
//			}));
//		}	
		
		GO.users.UserDialog.superclass.initComponent.call(this);	
	},

	user_id : 0,
	
	files_folder_id : '',
	
	afterLoad : function(remoteModelId, config, action){
		
		this.permissionsTab.setUserId(remoteModelId);
		this.accountTab.setUserId(remoteModelId);
		
		if(this.serverclientFieldSet)
		{
			var visible = remoteModelId>0;
			this.serverclientFieldSet.setVisible(!visible);
		}

		this.loginTab.setVisible(remoteModelId>0);
		
//		this.linkBrowseButton.setDisabled(!remoteModelId);
//		if(GO.files)
//		{
//			this.fileBrowseButton.setDisabled(!remoteModelId);
//		}

		this.lookAndFeelTab.startModuleField.clearLastSearch();
		this.lookAndFeelTab.modulesStore.baseParams.user_id=remoteModelId;

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
	
	
	afterSubmit : function(action){
		this.permissionsTab.commit();
		this.files_folder_id = action.result.files_folder_id;
	},
	
	getSubmitParams : function(){
		return this.permissionsTab.getPermissionParameters();
	},
	
	buildForm : function () {
		this.accountTab = new GO.users.AccountPanel();
		

		this.loginTab = new GO.users.LoginPanel();
		this.permissionsTab = new GO.users.PermissionsPanel();
		this.regionalSettingsTab = new GO.users.RegionalSettingsPanel();
		this.lookAndFeelTab = new GO.users.LookAndFeelPanel();

		this.profileTab = new Ext.Panel({
			title:GO.users.lang.account,
			autoScroll:true,
			layout:'column',
			//cls:'go-form-panel',
			bodyStyle:'padding:5px',
			items:[{
				columnWidth:.5,
				items:[this.accountTab],
				bodyStyle:'padding-right:5px',
				border:false
			},this.rightCol = new Ext.Panel({
				columnWidth:.5,
				bodyStyle:'padding-left:5px',
				items:[this.loginTab],
				border:false
			})]
		});
		
		this.addPanel(this.profileTab);
		
		if(GO.addressbook){
			this.contactPanel = new  GO.addressbook.ContactProfilePanel({
				forUser:true
			});
			this.addPanel(this.contactPanel);
		}
		
		
		this.addPanel(this.permissionsTab);
    this.addPanel(this.regionalSettingsTab);
    this.addPanel(this.lookAndFeelTab);
     

		if(GO.customfields && GO.customfields.types["GO_Base_Model_User"])
		{
			for(var i=0;i<GO.customfields.types["GO_Base_Model_User"].panels.length;i++)
			{
				this.addPanel(GO.customfields.types["GO_Base_Model_User"].panels[i]);
			}
		}   
		
		if(GO.serverclient && GO.serverclient.domains)
		{				
			this.serverclientFieldSet = new Ext.form.FieldSet({
				title: GO.serverclient.lang.mailboxes, 
				height:100,
				autoScroll:true,
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

			this.rightCol.add(this.serverclientFieldSet);
			
			this.on('render',function(){
				this.formPanel.form.findField('username').on('change', this.setDefaultEmail, this);
			},this);
			
		}		
	}
});