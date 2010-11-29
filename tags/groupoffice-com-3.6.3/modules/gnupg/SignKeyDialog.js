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
 */
 
GO.gnupg.SignKeyDialog = function(config){
	if(!config)
	{
		config={};
	}
	
	this.formPanel = new Ext.FormPanel({
		cls:'go-form-panel',
		defaults:{anchor : '100%'},
		waitMsgTarget:true,
		autoHeight:true,
		baseParams:{public_key:''},
		items:[
       this.keysCombo = new Ext.form.ComboBox({
    	   store : new GO.data.JsonStore({
				    url: GO.settings.modules.gnupg.url+ 'json.php',
				    baseParams: {
				    	task: 'private_keys'	    	
				    	},
				    root: 'results',
				    id: 'fingerprint',
				    totalProperty:'total',
				    fields: ['fingerprint', 'id', 'uid', 'type']
				}),
			fieldLabel : GO.gnupg.lang.key,
			//name : 'private_key',				
			displayField : 'uid',
			valueField : 'fingerprint',
			hiddenName : 'private_key',
			forceSelection : true,
			triggerAction : 'all',
			mode : 'local'
			}),{
				xtype:'textfield',
				inputType : 'password',
				name:'passphrase',
				fieldLabel:GO.lang.strPassword
			}]	
	});
	

	config.maximizable=true;
	config.layout='fit';
	config.modal=false;
	config.resizable=false;
	config.width=400;
	config.autoHeight=true;
	config.closeAction='hide';
	config.title= GO.gnupg.lang.signKey;					
	config.items=this.formPanel;
	config.buttons=[{
			text: GO.lang['cmdOk'],
			handler: function(){
				this.submitForm();
			},
			scope: this
		},{
			text: GO.lang['cmdClose'],
			handler: function(){
				this.hide();				
			},
			scope:this
		}					
	];
	
	GO.gnupg.SignKeyDialog.superclass.constructor.call(this, config);
	
	this.addEvents({'save':true});
}

Ext.extend(GO.gnupg.SignKeyDialog, Ext.Window,{	
	show : function(public_key){	
		this.keysCombo.store.load();		
		this.formPanel.form.reset();
		this.formPanel.baseParams.public_key=public_key;
		GO.gnupg.SignKeyDialog.superclass.show.call(this);	
	},
	submitForm : function(){
		this.formPanel.form.submit(
		{
			url:GO.settings.modules.gnupg.url+'action.php',
			params: {'task' : 'sign_key'},
			waitMsg:GO.lang['waitMsgSave'],
			success:function(form, action){								
				this.fireEvent('save', this);
				this.hide();							
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