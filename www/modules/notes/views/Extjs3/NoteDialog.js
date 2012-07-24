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
 
GO.notes.NoteDialog = Ext.extend(GO.dialog.TabbedFormDialog , {
	
	customFieldType : "GO_Notes_Model_Note",
	_encrypted : undefined,
	
	initComponent : function(){
		
		Ext.apply(this, {
			titleField:'name',
			goDialogId:'note',
			title:GO.notes.lang.note,
			height: 560,
			formControllerUrl: 'notes/note'
		});
		
		GO.notes.NoteDialog.superclass.initComponent.call(this);	
	},
	
	beforeLoad : function(remoteModelId,config) {
		this._encrypted = undefined;
	},
	
	afterLoad : function(remoteModelId,config,action) {
		var responseData = Ext.decode(action.response.responseText);
		this.contentField.setDisabled(responseData.data.encrypted);
		this._toggleNewPasswordFields(false);
		this.buttonOk.setDisabled(responseData.data.encrypted);
		this.buttonApply.setDisabled(responseData.data.encrypted);
		if (responseData.data.encrypted) {
			if (GO.util.empty(this.unlockDialog)) {
				this.unlockDialog = new GO.Window({
					title: GO.lang.decryptContent,
					width: 280,
					height: 100,
					layout: 'fit',
					items: [new Ext.form.FormPanel({
						layout: 'form',
						items: [this.unlockPasswordField = new Ext.form.TextField({
							name: 'userInputPassword',
							fieldLabel: GO.lang['password'],
							inputType: 'password'
						})],
						buttons: [{
							text: GO.lang['cmdOk'],
							handler: function(){
								this.formPanel.form.load({
									url: GO.url('notes/note/load'),
									params: {
										'userInputPassword' : this.unlockPasswordField.getValue()
									},
									success: function(form, action) {
										var responseData2 = Ext.decode(action.response.responseText);
										this.contentField.setDisabled(responseData2.data.encrypted);
										this.buttonOk.setDisabled(responseData2.data.encrypted);
										this.buttonApply.setDisabled(responseData2.data.encrypted);
										this.unlockDialog.hide();
									},
									failure: function(form, action) {
										this.encryptCheckbox.setValue(true);
										this._toggleNewPasswordFields(false);
										if (action.failureType == 'client') {					
											Ext.MessageBox.alert(GO.lang['strError'], GO.lang['strErrorsInForm']);			
										} else {
											Ext.MessageBox.alert(GO.lang['strError'], action.result.feedback);
										}
									},
									scope: this
								})
							},
							scope: this
						},
						{
							text: GO.lang['cmdCancel'],
							handler: function()
							{
								this.hide();
								this.unlockDialog.hide();
							},
							scope: this
						}]
					})]
				});
//				this.unlockDialog.on('hide',function(){
//					this._encrypted = undefined;
//				},this);
				this.unlockDialog.on('show',function(){
					this.unlockPasswordField.setValue('');
				},this);
			}
			this.unlockDialog.show();
		}
		this._encrypted = responseData.data.encrypted;
	},
		
	afterSubmit : function(action) {
		var responseData = Ext.decode(action.response.responseText);
		if (responseData.encrypted) {
			this.contentField.setValue(GO.lang['contentEncrypted']);
			this._toggleNewPasswordFields(false);
			this.buttonOk.setDisabled(true);
			this.buttonApply.setDisabled(true);
		}
		this.contentField.setDisabled(responseData.encrypted);
		
		this._encrypted = responseData.encrypted;
		
		// if decrypted --> encrypted:
		// - turn off uipassword1 & uipassword2
		// - turn disable content && set content value to 'this is encrypted'
		// if encrypted --> decrypted:
		// - turn on uipassword1 & uipassword2
		// - show content
	},
	
	buildForm : function () {
		
		this.selectLinkField = new GO.form.SelectLink({
			anchor:'100%'
		});

		this.propertiesPanel = new Ext.Panel({
			title:GO.lang['strProperties'],
			cls:'go-form-panel',
			layout:'form',
			items:[{
				xtype: 'textfield',
				name: 'name',
				width:300,
				anchor: '100%',
				maxLength: 100,
				allowBlank:false,
				fieldLabel: GO.lang.strName
			},this.selectCategory = new GO.form.ComboBox({
				fieldLabel: GO.notes.lang.category_id,
				hiddenName:'category_id',
				anchor:'100%',
				emptyText:GO.lang.strPleaseSelect,
				store: GO.notes.writableCategoriesStore,
				pageSize: parseInt(GO.settings.max_rows_list),
				valueField:'id',
				displayField:'name',
				mode: 'remote',
				triggerAction: 'all',
				editable: true,
				selectOnFocus:true,
				forceSelection: true,
				allowBlank: false
			}),
			this.selectLinkField,
			this.encryptCheckbox = new Ext.form.Checkbox({
				boxLabel: GO.lang.encryptContent,
				labelSeparator: '',
				name: 'encrypted',
				allowBlank: true,
				hideLabel:true,
				checked: false,
				disabled:false
			}),
			this.uiPassword1Field = new Ext.form.TextField({
				fieldLabel : GO.lang.password,
				inputType: 'password',
				name: 'userInputPassword1',
				value: '',
				allowBlank: false,
				disabled: true,
				hidden: true
			}),this.uiPassword2Field = new Ext.form.TextField({
				fieldLabel : GO.lang.passwordConfirm,
				xtype: 'textfield',
				inputType: 'password',
				name: 'userInputPassword2',
				value: '',
				allowBlank: false,
				disabled: true,
				hidden: true
			}),
			this.contentField = new Ext.form.TextArea({
				name: 'content',
				anchor: '100%',
				height: 300,
				hideLabel:true
			})]				
		});

		this.encryptCheckbox.on('check', function(cb,checked){
			this._toggleNewPasswordFields(checked);
		},this);

		this.addPanel(this.propertiesPanel);
	},
	
	_toggleNewPasswordFields : function(on) {
		this.uiPassword1Field.setDisabled(!on);
		this.uiPassword1Field.setVisible(on);
		this.uiPassword2Field.setDisabled(!on);
		this.uiPassword2Field.setVisible(on);
	}
});