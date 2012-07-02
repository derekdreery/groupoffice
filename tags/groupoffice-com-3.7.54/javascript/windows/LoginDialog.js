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
 
GO.LogoComponent = Ext.extend(Ext.BoxComponent, {
	onRender : function(ct, position){
		this.el = ct.createChild({
			tag: 'div',
			cls: "go-app-logo"
		});
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
	
	if(!config)
	{
		config={};
	}
	
	if(typeof(config.modal)=='undefined')
	{
		config.modal=true;
	}
	Ext.apply(this, config);
	
	var langCombo = new GO.form.ComboBoxReset({
		fieldLabel: GO.lang.strLanguage,
		name: 'language_text',
		store:  new Ext.data.SimpleStore({
			fields: ['id', 'language'],
			data : GO.Languages
		}),
		anchor:'100%',
		hiddenName: 'login_language',
		displayField:'language',
		valueField: 'id',
		mode:'local',
		triggerAction:'all',
		forceSelection: false,
		emptyText: GO.lang.userSelectedLanguage,
		editable: false,
		value: GO.loginSelectedLanguage || ""
	});
		
	langCombo.on('select', function(){
		if(langCombo.getValue()!='')
			document.location=BaseHref+'index.php?SET_LANGUAGE='+langCombo.getValue();
	}, this);

	this.formPanel = new Ext.FormPanel({
		labelWidth: 120, // label settings here cascade unless overridden
		url:'action.php',
		defaultType: 'textfield',
		//autoHeight:true,
		waitMsgTarget:true,        
		bodyStyle:'padding:5px 10px 5px 10px',
		items: [new GO.LogoComponent(),
		langCombo,		
		{
			itemId: 'username',
			fieldLabel: GO.lang.strUsername,
			name: 'username',
			allowBlank:false
			,anchor:'100%'
		}
		
	
		,{
			fieldLabel: GO.lang.strPassword,
			name: 'password',
			inputType: 'password',
			allowBlank:false,
			anchor:'100%'
		},{
			xtype: 'checkbox',
			hideLabel:true,
			boxLabel: GO.lang.remindPassword,
			name:'remind',
			height:20//explicit height for IE7 bug with ext 3.2
		},this.fullscreenField = new Ext.form.Checkbox({
			hideLabel:true,
			boxLabel: GO.lang.fullscreen,
			checked:GO.fullscreen,
			name:'fullscreen',
			height:20//explicit height for IE7 bug with ext 3.2
		})
		]
	});

	
	GO.dialog.LoginDialog.superclass.constructor.call(this, {
		autoHeight:true,
		width:400,
		draggable:false,
		resizable: false,
		closeAction:'hide',
		title:GO.lang['strLogin'],
		closable: false,
		items: [			
		this.formPanel
		],		
		buttons: [
		{
			text: GO.lang.lostPassword,
			handler: function(){
					
				// Prompt for user data and process the result using a callback:
				Ext.Msg.prompt(GO.lang.lostPassword, GO.lang.lostPasswordText.replace('{product_name}', GO.settings.config.product_name), function(btn, text){
					if (btn == 'ok'){

						this.hide();

						Ext.getBody().mask(GO.lang.waitMsgLoad);
						Ext.Ajax.request({
							url:BaseHref+'action.php',
							scope:this,
							params:{
								task:'lost_password',
								email:text
							},
							callback: function(options, success, response)
							{
								Ext.getBody().unmask();
								this.show();

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
				}, this);
					
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
    
	this.addEvents({
		callbackshandled: true
	});

};

Ext.extend(GO.dialog.LoginDialog, GO.Window, {
	
	callbacks : new Array(),
	
	hideDialog : true,

	focus: function(){
		this.formPanel.form.findField('username').focus(true);
	},
	
	addCallback : function(callback, scope)
	{		
		this.callbacks.push({
			callback: callback,
			scope: scope
		});
	},
	
	doLogin : function(){							
		this.formPanel.form.submit({
			url:BaseHref+'action.php',
			params: {
				'task' : 'login'
			},
			waitMsg:GO.lang.waitMsgLoad,
			success:function(form, action){

				//Another user logs in after a session expire			
				if(GO.settings.user_id>0 && action.result.user_id!=GO.settings.user_id)
				{
					document.location=document.location;
					return true;
				}

				Ext.apply(GO.settings, action.result.settings);
				
				if(action.result.name=='')
				{
					this.completeProfileDialog();
					this.profileFormPanel.form.setValues({email:action.result.email});
				}else
				{					
					this.handleCallbacks();
				}
				
				if(this.hideDialog)
					this.hide();
				
			},

			failure: function(form, action) {
				if(action.result)
				{
					Ext.MessageBox.alert(GO.lang['strError'], action.result.feedback, function(){
						this.formPanel.form.findField('username').focus(true);
					},this);
				}
			},
			scope: this
		});
	},
	
	handleCallbacks : function(){
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
		
		this.fireEvent('callbackshandled', this);
	},
	
	completeProfileDialog : function(){
		
		/*var formPanel = new Ext.form.FormPanel({
			waitMsgTarget:true,
			url: BaseHref+'action.php',
			border: false,
			autoHeight: true,
			cls:'go-form-panel',
			baseParams: {
				task: 'complete_profile'
			},
			defaults:{
				xtype:'textfield',
				anchor:'100%'
			},
			items:[{
				fieldLabel: GO.lang['strFirstName'], 
				name: 'first_name', 
				allowBlank: false
			},
			{
				fieldLabel: GO.lang['strMiddleName'], 
				name: 'middle_name'
			},
			{
				fieldLabel: GO.lang['strLastName'], 
				name: 'last_name', 
				allowBlank: false
			}]				
		});
		*/
		var focusFirstField = function(){
			this.profileFormPanel.form.findField('first_name').focus();
		};

		this.profileFormPanel = new Ext.form.FormPanel({
			items: new GO.users.ProfilePanel({
				header:false
				}),
			baseParams:{
				task:'complete_profile'
			},
			url:BaseHref+'action.php',
			waitMsgTarget:true,
			border:false
		});

		this.completeProfileDialog = new GO.Window({
			width: 900,
			height:560,
			title:GO.lang.completeProfile,
			autoScroll:true,
			resizable:true,
			items: this.profileFormPanel,
			closable:false,
			layout:'fit',
			focus:focusFirstField.createDelegate(this),
			buttons:[{
				text: GO.lang['cmdOk'],
				handler: function(){
					this.profileFormPanel.form.submit(
					{
						waitMsg:GO.lang['waitMsgSave'],
						success:function(form, action){
							this.fireEvent('save', this);
							if(this.profileFormPanel.onSaveSettings)
							{
								var func = this.profileFormPanel.onSaveSettings.createDelegate(this.profileFormPanel, [action]);
								func.call();
							}

							if(this.reload)
							{
								document.location=GO.settings.config.host;
							}
							this.hide();
							this.handleCallbacks();
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
				},
				scope: this
			}]		 
		});
		
		this.completeProfileDialog.show();		
	}
	
});



