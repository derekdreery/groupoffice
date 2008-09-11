/** 
 * Copyright Intermesh
 * 
 * This file is part of Group-Office. You should have received a copy of the
 * Group-Office license along with Group-Office. See the file /LICENSE.TXT
 * 
 * If you have questions write an e-mail to info@intermesh.nl
 * 
 * @version $Id: SettingsDialog.js 2095 2008-06-13 08:11:29Z mschering $
 * @copyright Copyright Intermesh
 * @author Merijn Schering <mschering@intermesh.nl>
 * @author Boy Wijnmaalen <bwijnmaalen@intermesh.nl>
 */ 
MyHtmlEditor = function(config)
{

	Ext.form.HtmlEditor.superclass.constructor.call(this, config);
}

Ext.extend(MyHtmlEditor, Ext.form.HtmlEditor, {

	getDocMarkup : function(){
    return '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">'+"\n"+'<html><head><style type="text/css">body{border:0;margin:0;padding:3px;height:98%;cursor:text;}</style></head><body></body></html>';
	}
	
});
 
 
GO.users.SettingsDialog = function()
{
	

	
	return {
		
		init : function()
		{		
			this.newUserActiveMail = new Ext.Panel({
				title: GO.users.lang['cmdPanelEnabledMail'],
				layout: 'form',
				border: false,
				hideMode:'offsets',
				//bodyStyle: 'padding: 5px 5px 5px 5px;',
				items:[
					this.textField('confirmed_subject',GO.users.lang['cmdFormLabelSubject']),
					this.htmlEditor('confirmed', '')
				]
			});

			this.newUserPassiveMail = new Ext.Panel({
				title: GO.users.lang['cmdPanelDisabledMail'],
				layout: 'form',
				border: false,
				hideMode:'offsets',
				items:[
					this.textField('unconfirmed_subject', GO.users.lang['cmdFormLabelSubject']),
					this.htmlEditor('unconfirmed', '')
				]
			});			
			
			if(!this.dialog)
			{

				this.formPanel = new Ext.FormPanel({
					id: 'settingsPanel',	
					border: false,
			        items: [{
			        	anchor:'100% 100%',
			        	xtype:'tabpanel',
			        	monitorValid: true,
				        activeTab: 0,
						deferredRender: false,
			        	border: false,
				        //enableTabScroll: true,
				        items: [
				        	this.newUserActiveMail,
				        	this.newUserPassiveMail,
				        ]
			        }]		
				});
				
				this.formPanel.form.load({
					url: GO.settings.modules.users.url+'json.php', 
					params: {task: 'settings'},
					
					success: function(form, task) {

				    },
				    scope: this
				});

				var buttons = [
					{id: 'ok', text: GO.lang['cmdOk'], handler: this.buttonHandler, scope: this },
					{id: 'apply', text: GO.lang['cmdApply'], handler: this.buttonHandler, scope: this },
					{id: 'close', text: GO.lang['cmdClose'], handler: function(){this.dialog.hide();}, scope: this }
				];
				
				this.dialog = new Ext.Window({
					layout: 'fit',
					modal:false,
					shadow:false,
					height: 450,
					width: 600,
					plain: false,
					closeAction: 'hide',
					title: GO.users.lang['cmdNotificationWindowTitle'],
					enableTabScroll: true,
					items: this.formPanel,
					buttons: buttons	
				});
			}
			this.dialog.show();
		},
		
		htmlEditor : function(name, fieldLabel)
		{
			 var htmlEditor = new MyHtmlEditor({
	            hideLabel: true,
	            name: name,
	            fieldLabel: fieldLabel,
	            anchor: '100% -32'	,
	            border: false          
	        });	
	        
	        return htmlEditor;			
		},
		
		textField : function(name, fieldLabel)
		{
			 var textField = new Ext.form.TextField({
			 	xtype: 'textfield', 
			 	fieldLabel: fieldLabel,
			 	itemCls: 'input-form-setting',
			 	width: '90%',
			 	name: name
			 });
	        
	        return textField;			
		},		
		
		buttonHandler : function(btn)
		{
			this.formPanel.container.mask(GO.lang['waitMsgSave'] + 'x-mask-loading');
			switch(btn.id)
			{
				case 'ok':
					this.save(true);
				break;
				case 'apply':
					this.save();
				break;
			}
		},
		
		save :  function(hide)
		{
			
			this.formPanel.form.submit({
				url:GO.settings.modules.users.url+'action.php',
				params: 
				{
					task : 'save_setting' 
				},		
				success:function(form, task){
					
					this.formPanel.container.unmask();
					
					if (hide)
					{
						this.dialog.hide();
					}						
				},
				failure: function(form, action) {
					this.formPanel.container.unmask();
					Ext.MessageBox.alert(GO.lang['strError'], action.result.feedback);			
				},
				scope: this
			});		
		}
	};
}();
