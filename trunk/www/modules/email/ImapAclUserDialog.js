GO.email.ImapAclUserDialog = Ext.extend(GO.Window, {

	initComponent : function(){

		this.formPanel = new Ext.form.FormPanel({
			waitMsgTarget : true,
			url : GO.settings.modules.email.url + 'action.php',
			border : false,
			baseParams : {
				task : 'setacl',
				account_id : 0,
				mailbox:'',
				identifier:''
			},
			cls : 'go-form-panel',
			items : []
		});


		Ext.apply(this, {
			width:500,
			height:400,
			title:GO.email.lang.shareFolder,
			layout:'fit',
			items:[this.formPanel]
		});
		GO.email.ImapAclDialog.superclass.initComponent.call(this);
	},

	show : function(identifier) {
		if (!this.rendered) {
			this.render(Ext.getBody());
		}
		this.formPanel.form.reset();


		if (this.alias_id > 0) {
			this.formPanel.load({
				url : GO.settings.modules.email.url
				+ 'json.php',
				waitMsg : GO.lang['waitMsgLoad'],
				success : function(form, action) {
					GO.email.ImapAclUserDialog.superclass.show
					.call(this);
				},
				failure : function(form, action) {
					Ext.Msg.alert(GO.lang['strError'],
						action.result.feedback)
				},
				scope : this
			});
		} else {
			GO.email.ImapAclUserDialog.superclass.show.call(this);
		}
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
	}

});