/**
 * Copyright Intermesh
 *
 * This file is part of Group-Office. You should have received a copy of the
 * Group-Office license along with Group-Office. See the file /LICENSE.TXT
 *
 * If you have questions write an e-mail to info@intermesh.nl
 *
 * @version $Id: Overrides.js 0000 2010-12-29 08:59:17 wsmits $
 * @copyright Copyright Intermesh
 * @author Merijn Schering <mschering@intermesh.nl>
 */

Ext.ns("GO.smime");

GO.smime.passwordsInSession = {};

GO.moduleManager.onModuleReady('email',function(){
	Ext.override(GO.email.EmailComposer, {
		
		initComponent : GO.email.EmailComposer.prototype.initComponent.createSequence(function(){
			this.optionsMenu.add(['-',this.signCheck = new Ext.menu.CheckItem({
				text:GO.smime.lang.sign,
				checked: false,
				listeners : {
					checkchange: function(check, checked) {	
						
						this.sendParams['sign_smime'] = checked
						? '1'
						: '0';
					},
					scope:this
				}
			}),this.encryptCheck = new Ext.menu.CheckItem({
				text:GO.smime.lang.encrypt,
				checked: false,
				listeners : {
					checkchange: function(check, checked) {						
						this.sendParams['encrypt_smime'] = checked
						? '1'
						: '0';
					},
					scope:this
				}
			})]);
		
			this.on('afterShowAndLoad',function(){
				this.signCheck.setChecked(false);
				this.encryptCheck.setChecked(false);
				this.sendParams['encrypt_smime'] ="0";
				this.sendParams['sign_smime'] ="0";				

				this.checkSmimeSupport();
			}, this);

			
			this.fromCombo.on('change',function(){
				this.checkSmimeSupport();
			}, this);
			
			
			this.on('beforesendmail',this.askPassword,this);
			
		}),	
		
		
		askPassword : function(){				
				
			if(!GO.smime.passwordsInSession[this.fromCombo.getValue()] && this.sendParams['sign_smime']=="1"){
				
				
				if(!this.passwordDialog)
				{
					this.passwordDialog = new GO.dialog.PasswordDialog({
						title:GO.smime.lang.enterPassword,
						fn:function(btn, password){
					
							if(btn=="cancel")
								return false;

							Ext.Ajax.request({
								url: GO.settings.modules.smime.url+ 'checkpassword.php',
								success: function(response){
									var res = Ext.decode(response.responseText);

									if(res.success)
									{
										GO.smime.passwordsInSession[this.fromCombo.getValue()]=true;
										this.sendMail();
									}else
									{
										this.askPassword();							
									}
								},
								params: {
									account_id: this.fromCombo.getValue(),
									password:password
								},
								scope:this
							});

						},
						scope:this
					});
				}
				this.passwordDialog.show();
				
				return false;
			}else
			{
				return true;
			}			
		},

		checkSmimeSupport : function(){
			var current_id = this.fromCombo.getValue();			
			var record = this.fromCombo.store.getById(current_id);
			
			this.signCheck.setDisabled(!record.json.has_smime_cert);			
			if(record.json.has_smime_cert && record.json.always_sign){
				this.signCheck.setChecked(true);
				this.sendParams['sign_smime'] ="1";	
			}
			if(!record.json.has_smime_cert){
				this.signCheck.setChecked(false);
				this.sendParams['sign_smime'] ="0";	
			}
		}
	}
	)
});
        