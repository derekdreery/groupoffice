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
 
GO.users.AccountPanel = function(config)
{
	if(!config)
	{
		config={};
	}
	
	config.autoHeight=true;
	config.border=true;
	config.hideLabel=true;
	config.title = GO.users.lang.account;
	config.layout='form';
	config.defaults={anchor:'100%'};
	config.defaultType = 'textfield';
	//config.cls='go-form-panel';
	config.labelWidth=140;
	
	this.passwordField1 = new Ext.form.TextField({
		inputType: 'password', 
		fieldLabel: GO.users.lang['cmdFormLabelPassword'], 
		name: 'password1'
		});
	this.passwordField2 = new Ext.form.TextField({
		inputType: 'password', 
		fieldLabel: GO.users.lang.confirmPassword, 
		name: 'password2'
		});
		
	this.usernameField = new Ext.form.TextField({
			fieldLabel: GO.lang['strUsername'], 
			name: 'username'
		});
		
	this.enabledField = new Ext.form.Checkbox({
		boxLabel: GO.users.lang['cmdBoxLabelEnabled'],
		name: 'enabled',
		checked: true,
		hideLabel:true
	});

	this.invitationField = new Ext.form.Checkbox({
		boxLabel: GO.users.lang.sendInvitation,
		name: 'send_invitation',
		checked: true,
		hideLabel:true
	});

	config.items=[
		this.usernameField,
		this.passwordField1,
		this.passwordField2,{
			xtype:'panel',
			hideLabel:true,
			border:false,
			bodyStyle:'padding:0',
			layout:'column',
			defaults:{bodyStyle:'padding:0',border:false, layout:'form', columnWidth:.5},
			items:[{
				items:this.enabledField
			},{
				items:this.invitationField
			}]
		}		
	];

	GO.users.AccountPanel.superclass.constructor.call(this, config);		
}


Ext.extend(GO.users.AccountPanel, Ext.form.FieldSet,{
	
	setUserId : function(user_id)
	{
		this.invitationField.getEl().up('.x-form-item').setDisplayed(!user_id);
		this.usernameField.setDisabled(user_id>0);
		this.passwordField2.allowBlank=(user_id>0);
		this.passwordField1.allowBlank=(user_id>0);
	}
});			