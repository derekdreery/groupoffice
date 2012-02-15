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
 
GO.gnupg.GenKeyDialog = function(config){
	if(!config)
	{
		config={};
	}
	var pw1, pw2;
	
	this.formPanel = new Ext.FormPanel({
		cls:'go-form-panel',
		defaults:{anchor : '100%'},
		waitMsgTarget:true,
		autoHeight:true,
		items:[
		       this.accountCombo = new Ext.form.ComboBox({
		    	   store : new GO.data.JsonStore({
					url : BaseHref
							+ 'modules/email/json.php',
					baseParams : {
						"task" : 'accounts',
						personal_only : true
					},
					fields : ['id', 'email', 'signature'],
					root : 'results',
					totalProperty : 'total',
					id : 'id'
				}),
			fieldLabel : GO.email.lang.from,
			name : 'account_name',				
			displayField : 'email',
			valueField : 'id',
			hiddenName : 'account_id',
			forceSelection : true,
			triggerAction : 'all',
			mode : 'local'
			}),{
				id:'gen-key-pass',
				xtype:'textfield',
				inputType : 'password',
				name:'passphrase',
				fieldLabel:GO.lang.strPassword,
				allowBlank:false
			},{
				xtype:'textfield',
				inputType : 'password',
				fieldLabel:GO.lang.strConfirm,
				name:'passphrase2',
				allowBlank:false,
				vtype: 'password',
        initialPassField: 'gen-key-pass'
			},{
				xtype:'textfield',				
				fieldLabel:GO.gnupg.lang.comment,
				name:'comment',
				allowBlank:false				
			}]	
	});

	this.formPanel.form.timeout=300;
	

	config.maximizable=true;
	config.layout='fit';
	config.modal=false;
	config.resizable=true;
	config.width=400;
	config.autoHeight=true;
	config.closeAction='hide';
	config.title= GO.gnupg.lang.genKey;
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
	
	GO.gnupg.GenKeyDialog.superclass.constructor.call(this, config);
	
	this.addEvents({'save':true});
}

Ext.extend(GO.gnupg.GenKeyDialog, Ext.Window,{	
	show : function(){	
		this.accountCombo.store.load();		
		this.formPanel.form.reset();
		GO.gnupg.GenKeyDialog.superclass.show.call(this);	
	},
	submitForm : function(){
		var origTimeout = Ext.Ajax.timeout;

		this.formPanel.form.submit(
		{
			url:GO.settings.modules.gnupg.url+'action.php',
			params: {'task' : 'gen_key'},
			waitMsg:GO.lang['waitMsgSave'],
			success:function(form, action){								
				this.fireEvent('save', this);
				this.hide();

				Ext.MessageBox.alert(GO.gnupg.lang.generateKeyStarted, GO.gnupg.lang.generateKeyStartedText);
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