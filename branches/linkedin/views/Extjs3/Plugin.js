
/*GO.moduleManager.on('moduleconstructed',function(moduleManager, moduleName, panel){
	if(moduleName=='timeregistration'){
		panel.commentsPanel.collapsed=true;
		panel.commentsPanel.hide();
	}
});*/

Ext.namespace('GO.linkedin');



GO.moduleManager.onModuleReady('addressbook',function(){
	Ext.override(GO.addressbook.AddressbookDialog, {
		setInfoField : function() {
			if (this.autoImportSetCB.getValue()!=true) {
				this.enabledInfoField.setValue(GO.linkedin.lang['autoImportNotSetInfo']);
			} else if (this.autoImportEnabledField.getValue()===true) {
				this.enabledInfoField.setValue(GO.linkedin.lang['autoImportEnabledInfo']);
			} else {
				this.enabledInfoField.setValue(GO.linkedin.lang['autoImportDisabledInfo']);
			}
		},
		setAccessCodeFields : function() {
			
			console.log(this.autoImportSetCB.getValue());
			console.log(typeof(this.autoImportSetCB.getValue()));
			
			this.accessCodeFields1.setDisabled(this.autoImportSetCB.getValue()==true);
			this.accessCodeFields1.setVisible(this.autoImportSetCB.getValue()!=true);
//			this.authenticateInfo.container.up('div.x-form-item').setDisplayed(this.autoImportSetCB.getValue()!=true);
//			this.authenticateButton.container.up('div.x-form-item').setDisplayed(this.autoImportSetCB.getValue()!=true);
//			this.accessCodeFields1.container.up('div.x-form-item').setDisplayed(this.autoImportSetCB.getValue()!=true);
//			this.accessCodeFields1.setVisible(this.autoImportSetCB.getValue()!=true);
			
			
			this.accessCodeFields2.setDisabled(this.autoImportSetCB.getValue()==true);
			this.accessCodeFields2.setVisible(this.autoImportSetCB.getValue()!=true);
//			this.pinVerificationLabel.container.up('div.x-form-item').setDisplayed(this.autoImportSetCB.getValue()!=true);
//			this.pinVerificationField.container.up('div.x-form-item').setDisplayed(this.autoImportSetCB.getValue()!=true);
//			this.pinVerificationButton.container.up('div.x-form-item').setDisplayed(this.autoImportSetCB.getValue()!=true);
//			this.accessCodeFields2.container.up('div.x-form-item').setDisplayed(this.autoImportSetCB.getValue()!=true);
//			this.accessCodeFields2.setVisible(this.autoImportSetCB.getValue()!=true);
			
			
			this.autoImportEnabledField.container.up('div.x-form-item').setDisplayed(this.autoImportSetCB.getValue()==true);

			this.removeAccessCodeFields.setDisabled(this.autoImportSetCB.getValue()!=true);
			this.removeAccessCodeFields.setVisible(this.autoImportSetCB.getValue()==true);
//			this.removeConnectionInfoField.container.up('div.x-form-item').setDisplayed(this.autoImportSetCB.getValue()==true);
//			this.removeConnectionButton.container.up('div.x-form-item').setDisplayed(this.autoImportSetCB.getValue()==true);
//			this.removeAccessCodeFields.container.up('div.x-form-item').setDisplayed(this.autoImportSetCB.getValue()==true);
//			this.removeAccessCodeFields.setVisible(this.autoImportSetCB.getValue()==true);
			

			this.doLayout();
		},
		afterSubmit : GO.addressbook.AddressbookDialog.prototype.buildForm.createSequence(function(){
			//////////////////////////////////////////////////////////////////////////
			// The LinkedIn plugin uses the address book's submit only for enabling
			// and disabling already existing LinkedIn connection.
			/////////////////////////////////////////////////////////////////////////
			this.setInfoField();
		}),
		removeAccessToken : function() {
			GO.request({
				url: 'linkedin/gate/removeAccessToken',
				params: {
					addressbookId : this.remoteModelId
				},
				success: function(options, response, result) {
					this.autoImportSetCB.setValue(false);
					this.autoImportEnabledField.setDisabled(true);
					this.setInfoField();
					this.setAccessCodeFields();
				},
				failure: function(options, response, result) {
					Ext.MessageBox.alert(GO.lang['strError'],result['feedback']);
					this.autoImportEnabledField.setValue(false);
					this.autoImportSetCB.setValue(true);
				},
				scope: this
			});

		},
		setAccessToken : function() {
			GO.request({
				url: 'linkedin/gate/requestAccessToken',
				params: {
					pinVerification : this.pinVerificationField.getValue(),
					addressbookId : this.remoteModelId
				},
				success: function(options, response, result) {
					this.autoImportSetCB.setValue(true);
					this.autoImportEnabledField.setDisabled(false);
					this.setInfoField();
					this.setAccessCodeFields();
				},
				failure: function(options, response, result) {
					Ext.MessageBox.alert(GO.lang['strError'],result['feedback']);
					this.autoImportEnabledField.setValue(false);
					this.autoImportSetCB.setValue(true);
				},
				scope: this
			});

		},
//		initComponent :GO.addressbook.AddressbookDialog.prototype.initComponent.createSequence(function(){
//			this._tabPanel.layoutOnTabChange=true;
//		}),
		buildForm : GO.addressbook.AddressbookDialog.prototype.buildForm.createSequence(function(){
			
			this.linkedinImportPanel = new Ext.Panel({
//				tbar:[{
//						text:'test',
//						enableToggle:true,
//						toggleHandler:function(btn, pressed){
//							this.autoImportSetCB.setValue(pressed);
//							this.setAccessCodeFields();
//						},
//						scope:this
//				}],
				title:GO.linkedin.lang['linkedinImport'],
				layout: 'form',
				labelWidth: 140,
				defaultType: 'textfield',
				border: false,
				bodyStyle:'padding:5px',
				defaults: {anchor:'100%'},
//				listeners:[{
//						show:function(){
//							this.setAccessCodeFields();
//						},
//						scope: this
//				}],
				items : [
					this.autoImportSetCB = new Ext.form.Checkbox({
						name : 'auto_linkedin_import_set',
						hidden : true
					}),
					this.enabledInfoField = new GO.form.PlainField({
						hideLabel : true,
						height: 40
					}),
					this.accessCodeFields1 = new Ext.Panel({
//						xtype: 'compositefield',
//						layout: 'form',
						layout : 'column',
						hideLabel : true,
						border: false,
						items: [
							this.authenticateInfo = new GO.form.PlainField({
//								xtype: 'plainfield',
								hideLabel : true,
								value: GO.linkedin.lang['retrieveAccessCode'],
								width: 350
							}),
							this.authenticateButton = new Ext.Button({
								text: GO.linkedin.lang['retrieve'],
								width: '20%',
								handler: function(){
									GO.request({
										url: 'linkedin/gate/connectToLinkedin',
										success: function(options, response, result) {
											if (!GO.util.empty(result.authenticateUrl)) {
												window.open(result.authenticateUrl);
												this.pinVerificationField.setDisabled(false);
												this.pinVerificationButton.setDisabled(false);
											}
										},
										scope: this
									});
								},
								scope: this
							})
						]
					}),
					this.accessCodeFields2 = new Ext.Panel({
//						xtype: 'compositefield',
						border : false,
						layout : 'column',
						hideLabel : true,
						items : [
							this.pinVerificationLabel = new GO.form.PlainField({
								value : GO.linkedin.lang['personalAccessCode']+':',
								hideLabel : true,
								width : 350
							}),
							this.pinVerificationField = new Ext.form.TextField({
								name : 'pinVerification',
								hideLabel : true,
								disabled : true
							}),
							this.pinVerificationButton = new Ext.Button({
								text: GO.lang['cmdOk'],
								disabled: true,
								style: 'margin-left:10px;',
								handler: function(){
									this.setAccessToken();
								},
								scope: this
							})
						]
					}),
					this.autoImportEnabledField = new Ext.form.Checkbox({
						name : 'auto_linkedin_import_enabled',
						boxLabel : GO.linkedin.lang['enableAutoImport'],
						hideLabel : true,
						checked : false
					}),
					this.removeAccessCodeFields = new Ext.Panel({
						border : false,
						hideLabel : true,
						layout : 'column',
						items : [
							this.removeConnectionInfoField = new GO.form.PlainField({
								value : GO.linkedin.lang['removeConnection']+':',
								hideLabel : true,
								width: 350
							}),
							this.removeConnectionButton = new Ext.Button({
								text: GO.lang['cmdDelete'],
//								disabled: true,
								handler: function(){
									Ext.Msg.show({
										title: GO.lang['cmdDelete'],
										icon: Ext.MessageBox.WARNING,
										msg: GO.linkedin.lang['deleteConnectionAreYouSure'],
										buttons: Ext.Msg.YESNO,
										scope:this,
										fn: function(btn) {
											if (btn=='yes') {
												this.removeAccessToken();
											}
										}
									});
								},
								scope: this
							})
						]
					})
				]
			});
					
			this.addPanel(this.linkedinImportPanel);
		}),
		afterLoad : GO.addressbook.AddressbookDialog.prototype.afterLoad.createSequence(function(remoteModelId, config, action){
			this.pinVerificationField.setDisabled(true);
			this.pinVerificationButton.setDisabled(true);
			this.autoImportEnabledField.setDisabled(this.autoImportSetCB.getValue()!=true);

			// These two functions use the value of this.autoImportSetCB:
			this.setInfoField();
			this.setAccessCodeFields();
		})
	});
});