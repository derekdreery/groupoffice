GO.moduleManager.onModuleReady('addressbook',function(){
	GO.mainLayout.onReady(function(){
		GO.addressbook.contactDialog.height+=25;
		GO.addressbook.contactDialog.elements += ',tbar';

		GO.addressbook.contactDialog.topToolbar=new Ext.Toolbar({
			items:[{
				text:'Create user',
				handler:function(){
					GO.users.userDialog.show();

					var values = this.formPanel.form.getValues();
					values.username=values.email;

					GO.users.userDialog.formPanel.form.setValues(values);
				},
				scope:GO.addressbook.contactDialog
			}]
		});
	});
});