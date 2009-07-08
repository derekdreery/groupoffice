/** 
 * Copyright Intermesh
 * 
 * This file is part of Group-Office. You should have received a copy of the
 * Group-Office license along with Group-Office. See the file /LICENSE.TXT
 * 
 * If you have questions write an e-mail to info@intermesh.nl
 * 
 * @version $Id: LoginDialog.js 1847 2009-02-09 14:40:39Z mschering $
 * @copyright Copyright Intermesh
 * @author Merijn Schering <mschering@intermesh.nl>
 */

GO.gnupg.PasswordDialog = function(config){
	
	if(!config)
	{
		config={};
	}
	

	config.modal=true;

	Ext.apply(this, config);
	
	
	this.formPanel = new Ext.FormPanel({
        labelWidth: 120, // label settings here cascade unless overridden
        url:'action.php',        
        defaultType: 'textfield',
        autoHeight:true,
        waitMsgTarget:true,
        //cls:'go-form-panel',
        
        bodyStyle:'padding:5px 10px 5px 10px',
        items: [{
                fieldLabel: GO.lang.strPassword,
                name: 'password',
                inputType: 'password',
                allowBlank:false,
                anchor:'100%'
        }]
		});

	
	//var logo = Ext.getBody().createChild({tag: 'div', cls: 'go-app-logo'});
	
	GO.gnupg.PasswordDialog.superclass.constructor.call(this, {
    layout: 'fit',
		
		autoHeight:true,
		width:400,
		resizable: false,
		closeAction:'hide',
		title:GO.gnupg.lang.decryptPasswordRequired,
		closable: false,
		focus: function(){
 		    this.formPanel.form.findField('password').focus(true);
		}.createDelegate(this),
		items: [			
			this.formPanel
		],
		
		buttons: [{				
				text: GO.lang['cmdOk'],
				handler: function(){this.pressButton('ok');},
				scope:this
			},{
				text: GO.lang.cmdCancel,
				handler:function(){
					this.pressButton('cancel');					
				},
				scope:this
			}
		],
		keys: [{
            key: Ext.EventObject.ENTER,
            fn: function(){this.pressButton('ok');},
            scope:this
        }]
    });
    
    this.addEvents({buttonpressed: true});
    
};

Ext.extend(GO.gnupg.PasswordDialog, Ext.Window, {
	pressButton : function(button){
		this.fireEvent('buttonpressed', button, this.formPanel.form.findField('password').getValue());
		this.formPanel.form.reset();
		this.hide();
	}
});


