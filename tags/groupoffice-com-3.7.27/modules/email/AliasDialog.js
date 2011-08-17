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
GO.email.AliasDialog = function(config) {
	if (!config) {
		config = {};
	}
	this.buildForm();
	var focusFirstField = function() {
		this.formPanel.items.items[0].focus();
	};
	config.layout = 'fit';
	config.modal = false;
	config.resizable = false;
	config.width = 500;
	config.autoHeight = true;
	config.closeAction = 'hide';
	config.title = GO.email.lang.alias;
	config.items = this.formPanel;
	config.focus = focusFirstField.createDelegate(this);
	config.buttons = [{
				text : GO.lang['cmdOk'],
				handler : function() {
					this.submitForm(true);
				},
				scope : this
			}, {
				text : GO.lang['cmdApply'],
				handler : function() {
					this.submitForm();
				},
				scope : this
			}, {
				text : GO.lang['cmdClose'],
				handler : function() {
					this.hide();
				},
				scope : this
			}];
	GO.email.AliasDialog.superclass.constructor.call(this, config);
	this.addEvents({
				'save' : true
			});
}
Ext.extend(GO.email.AliasDialog, Ext.Window, {
			show : function(alias_id, config) {
				if (!this.rendered) {
					this.render(Ext.getBody());
				}
				this.formPanel.form.reset();

				if (!alias_id) {
					alias_id = 0;
				}
				this.setAliasId(alias_id);
				if (this.alias_id > 0) {
					this.formPanel.load({
								url : GO.settings.modules.email.url
										+ 'json.php',
								waitMsg : GO.lang['waitMsgLoad'],
								success : function(form, action) {
									GO.email.AliasDialog.superclass.show
											.call(this);
								},
								failure : function(form, action) {
									Ext.Msg.alert(GO.lang['strError'],
											action.result.feedback)
								},
								scope : this
							});
				} else {
					GO.email.AliasDialog.superclass.show.call(this);
				}
			},
			setAliasId : function(alias_id) {
				this.formPanel.form.baseParams['alias_id'] = alias_id;
				this.alias_id = alias_id;
			},
			submitForm : function(hide) {
				this.formPanel.form.submit({
							url : GO.settings.modules.email.url + 'action.php',
							params : {
								'task' : 'save_alias'
							},
							waitMsg : GO.lang['waitMsgSave'],
							success : function(form, action) {
								if (action.result.alias_id) {
									this.setAliasId(action.result.alias_id);
								}
								this.fireEvent('save', this, this.alias_id);
								if (hide) {
									this.hide();
								}
							},
							failure : function(form, action) {
								if (action.failureType == 'client') {
									Ext.MessageBox.alert(GO.lang['strError'],
											GO.lang['strErrorsInForm']);
								} else {
									Ext.MessageBox.alert(GO.lang['strError'],
											action.result.feedback);
								}
							},
							scope : this
						});
			},
			buildForm : function() {

				this.formPanel = new Ext.form.FormPanel({
							waitMsgTarget : true,
							url : GO.settings.modules.email.url + 'action.php',
							border : false,
							baseParams : {
								task : 'alias',
								account_id : 0
							},
							cls : 'go-form-panel',
							autoHeight : true,
							items : [{
										xtype : 'textfield',
										name : 'name',
										anchor : '100%',
										allowBlank:false,
										fieldLabel : GO.lang.strName
									}, {
										xtype : 'textfield',
										name : 'email',
										anchor : '100%',
										allowBlank:false,
										fieldLabel : GO.email.lang.email
									}, {
										xtype : 'textarea',
										name : 'signature',
										anchor : '100%',
										height:150,
										fieldLabel : GO.email.lang.signature
									}]
						});
			}
		});
