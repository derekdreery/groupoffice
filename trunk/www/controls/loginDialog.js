/**
 * @copyright Copyright Intermesh 2007
 * @author Merijn Schering <mschering@intermesh.nl>
 * 
 * This file is part of Group-Office.
 * 
 * Group-Office is free software; you can redistribute it and/or modify it
 * under the terms of the GNU General Public License as published by the
 * Free Software Foundation; either version 2 of the License, or (at your
 * option) any later version.
 * 
 * See file /LICENSE.GPL
 */

Ext.LoginDialog = function(config){
	
	Ext.apply(this, config);
	
	this.formPanel = new Ext.FormPanel({
        labelWidth: 75, // label settings here cascade unless overridden
        url:'action.php',
        defaults: {width: 150},
        defaultType: 'textfield',
        bodyStyle:'padding:5px;',

        items: [{
        		id: 'username',
                fieldLabel: 'Username',
                name: 'username',
                allowBlank:false
            },{
                fieldLabel: 'Password',
                name: 'password',
                inputType: 'password',
                allowBlank:false
            }]
		});

	
	
	Ext.Window.superclass.constructor.call(this, {
    	layout: 'fit',
		modal:true,
		height:140,
		width:300,
		resizable: false,
		closeAction:'hide',
		title:GOlang['strLogin'],
		closable: false,
		iconCls: 'go-app-icon',
		focus: function(){
 		    Ext.get('username').focus();
		},

		items: [
			this.formPanel
		],
		
		buttons: [
			{
				id: 'ok',
				text: GOlang['cmdOk'],
				handler: function(){							
					this.formPanel.form.submit({
						url:BaseHref+'action.php',
						params: {'task' : 'login'},	
						success:function(form, action){
							
							if(this.callback)
							{
								if(!this.scope)
								{
									this.scope=this;
								}
								var callback = this.callback.createDelegate(this.scope);
								callback.call();
							}
							this.hide();
							
						},
	
						failure: function(form, action) {
							Ext.MessageBox.alert(GOlang['strError'], action.result.errors);
						},
						scope: this
					});
				},
				scope:this
			}
		]
    });
};

Ext.extend(Ext.LoginDialog, Ext.Window, {
	

	/*show : function()
	{
		
		Ext.LinksDialog.superclass.show.call(this);
		//If I don't put a 100ms delay it doesn't work in Firefox 2.0 on Linux
		this.formPanel.form.findField('username').focus.defer(100, this.formPanel.form);
	}*/
	
	
});


