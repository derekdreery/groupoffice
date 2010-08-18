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
 
GO.gnupg.ImportKeyDialog = function(config){
	if(!config)
	{
		config={};
	}

	
	this.formPanel = new Ext.FormPanel({
		fileUpload : true,
		cls:'go-form-panel',
		defaults:{anchor : '100%'},
		waitMsgTarget:true,
		autoHeight:true,
		items:[new GO.form.UploadFile({
						inputName : 'keys',
						addText : GO.gnupg.lang.selectKeyFile
					})]	
	});
	

	config.maximizable=true;
	config.layout='fit';
	config.modal=false;
	config.resizable=false;
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
	
	GO.gnupg.ImportKeyDialog.superclass.constructor.call(this, config);
	
	this.addEvents({'save':true});
}

Ext.extend(GO.gnupg.ImportKeyDialog, Ext.Window,{	
	submitForm : function(){
		this.formPanel.form.submit(
		{
			url:GO.settings.modules.gnupg.url+'action.php',
			params: {'task' : 'import_key'},
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