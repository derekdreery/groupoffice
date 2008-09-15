/** 
 * Copyright Intermesh
 * 
 * This file is part of Group-Office. You should have received a copy of the
 * Group-Office license along with Group-Office. See the file /LICENSE.TXT
 * 
 * If you have questions write an e-mail to info@intermesh.nl
 * 
 * @version $Id: LoginDialog.js 2938 2008-09-01 20:39:51Z mschering $
 * @copyright Copyright Intermesh
 * @author Merijn Schering <mschering@intermesh.nl>
 */
 
GO.LogoComponent = Ext.extend(Ext.BoxComponent, {
	onRender : function(ct, position){
		this.el = ct.createChild({tag: 'div', cls: "go-app-logo"});
	}
});

 /**
 * @class GO.dialog.LoginDialog
 * @extends Ext.Window
 * The Group-Office login dialog window.
 * 
 * @cfg {Function} callback A function called when the login was successfull
 * @cfg {Object} scope The scope of the callback
 * 
 * @constructor
 * @param {Object} config The config object
 */
 
GO.dialog.LoginDialog = function(config){
	
	Ext.apply(this, config);
	
	var langCombo = new Ext.form.ComboBox({
			fieldLabel: GO.lang.strLanguage,
			name: 'language_text',
			store:  new Ext.data.SimpleStore({
					fields: ['id', 'language'],
					data : GO.Languages
				}),
			anchor:'100%',
			hiddenName: 'language',
			displayField:'language',
			valueField: 'id',			
			mode:'local',
			triggerAction:'all',			
			forceSelection: true,
			editable: false,
			value: GO.settings.language
		});
		
	langCombo.on('select', function(){
		document.location=BaseHref+'index.php?SET_LANGUAGE='+langCombo.getValue();
	}, this);
	
	this.formPanel = new Ext.FormPanel({
        labelWidth: 120, // label settings here cascade unless overridden
        url:'action.php',        
        defaultType: 'textfield',
        autoHeight:true,
        waitMsgTarget:true,
        //cls:'go-form-panel',
        
        bodyStyle:'padding:5px 10px 5px 10px',
        items: [new GO.LogoComponent(),
        		langCombo,
        		{
        				id: 'username',
                fieldLabel: GO.lang.strUsername,
                name: 'username',
                allowBlank:false,
                anchor:'100%'
            },{
                fieldLabel: GO.lang.strPassword,
                name: 'password',
                inputType: 'password',
                allowBlank:false,
                anchor:'100%'
            },{
            	xtype: 'checkbox',
            	hideLabel:true,
            	boxLabel: GO.lang.remindPassword,
            	name:'remind'
            }]
		});

	
	//var logo = Ext.getBody().createChild({tag: 'div', cls: 'go-app-logo'});
	
	GO.dialog.LoginDialog.superclass.constructor.call(this, {
    layout: 'fit',
		modal:true,
		autoHeight:true,
		width:340,
		resizable: false,
		closeAction:'hide',
		title:GO.lang['strLogin'],
		closable: false,
		focus: function(){
 		    Ext.get('username').focus();
		},

		items: [
			
			this.formPanel
		],
		
		buttons: [
			{				
				text: GO.lang.lostPassword,
				handler: function(){
					
					// Prompt for user data and process the result using a callback:
					Ext.Msg.prompt(GO.lang.lostPassword, GO.lang.lostPasswordText, function(btn, text){
					    if (btn == 'ok'){
					        
					        Ext.Ajax.request({
					        	url:'action.php',
					        	params:{
					        		task:'lost_password',
					        		email:text
					        	},
					        	callback: function(options, success, response)
										{						
											if(!success)
											{
												Ext.MessageBox.alert(GO.lang['strError'], GO.lang['strRequestError']);
											}else
											{												
												
												var responseParams = Ext.decode(response.responseText);
												if(!responseParams.success)
												{
													Ext.MessageBox.alert(GO.lang['strError'], responseParams.feedback);
												}else
												{
													Ext.MessageBox.alert(GO.lang['strSuccess'], responseParams.feedback);
												}
											}
										}				
									});
					        
					    }
					})
					
				},
				scope:this
			},
			{				
				text: GO.lang['cmdOk'],
				handler: this.doLogin,
				scope:this
			}
		],
		keys: [{
            key: Ext.EventObject.ENTER,
            fn: this.doLogin,
            scope:this
        }]
    });
    
    
};

Ext.extend(GO.dialog.LoginDialog, Ext.Window, {
	
	callbacks : new Array(),
	
	addCallback : function(callback, scope)
	{		
		this.callbacks.push({callback: callback, scope: scope});		
	},
	
	doLogin : function(){							
		this.formPanel.form.submit({
			url:BaseHref+'action.php',
			params: {'task' : 'login'},	
			waitMsg:GO.lang.waitMsgLoad,
			success:function(form, action){
				
				//reload user settings
				window.GO.settings=action.result.settings;
				
				for(var i=0;i<this.callbacks.length;i++)
				{
					if(this.callbacks[i].callback)
					{
						var scope = this.callbacks[i].scope ? this.callbacks[i].scope : this;
						//var callback = this.callbacks[i].callback.createDelegate(this.callbacks[i].scope, scope);
						this.callbacks[i].callback.call(scope);
					}
				}
				
				this.callbacks=[];
				this.hide();
				
			},

			failure: function(form, action) {
				if(action.result)
				{
					Ext.MessageBox.alert(GO.lang['strError'], action.result.feedback);
				}
			},
			scope: this
		});
	}
	
});

GO.mainLayout.onReady(function(){
	GO.loginDialog = new GO.dialog.LoginDialog();
	
});


