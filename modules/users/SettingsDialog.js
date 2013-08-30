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

GO.users.SettingsDialog = function(config){
	if(!config)
	{
		config={};
	}

	this.formPanel = new Ext.form.FormPanel({
		border:false,
		waitMsgTarget:true,
		cls:'go-form-panel',
		items:[{
			xtype:'textfield',
			fieldLabel:GO.lang.strSubject,
			name:'register_email_subject',
			anchor:'100%'
		},{
			hideLabel:true,
			xtype:'textarea',
			anchor:'100% -30',
			name:'register_email_body'
		}]
	});


	config.layout='fit';
	config.modal=false;
	config.resizable=false;
	config.width=750;
	config.height=500;
	config.closeAction='hide';
	config.title= GO.lang.cmdSettings;
	config.items=this.formPanel;
	config.buttons=[{
		text: GO.lang.cmdOk,
		handler: function(){
			this.submitForm(true);
		},
		scope:this
	},{
		text: GO.lang.cmdClose,
		handler: function(){
			this.hide();
		},
		scope:this
	}];

	GO.users.SettingsDialog.superclass.constructor.call(this, config);
}
Ext.extend(GO.users.SettingsDialog, Ext.Window,{
	show : function(){

		if(!this.rendered)
		{
			this.render(Ext.getBody());
		}

		this.formPanel.load({
			url : GO.settings.modules.users.url+'json.php',
			params:{
				task:'settings'
			},
			waitMsg:GO.lang['waitMsgLoad'],
			success:function(form, action)
			{
				GO.users.SettingsDialog.superclass.show.call(this);
			},
			failure:function(form, action)
			{
				GO.errorDialog.show(action.result.feedback)
			},
			scope: this
		});		
	},
	submitForm : function(hide){
		this.formPanel.form.submit(
		{
			url:GO.settings.modules.users.url+'action.php',
			params: {
				'task' : 'save_settings'
			},
			waitMsg:GO.lang['waitMsgSave'],
			success:function(form, action){				
				if(hide)
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