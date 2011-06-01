

GO.ab2users.randomPassword = function(length)
{
	var chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890";
	var pass = "";
	var i;
	for(var x=0;x<length;x++)
	{
		i = Math.floor(Math.random() * 62);
		pass += chars.charAt(i);
	}
	return pass;
}

GO.moduleManager.onModuleReady('addressbook',function(){

	Ext.override(GO.addressbook.ContactDialog, {
		initComponent : GO.addressbook.ContactDialog.prototype.initComponent.createInterceptor(function(){
			this.height+=25;

			this.elements += ',tbar';


			this.tbar=[{
				iconCls:'btn-save',
				text:GO.ab2users.lang.createUser,
				handler:function(){
					GO.users.showUserDialog();

					var values = this.formPanel.form.getValues();
					values.username=values.last_name;
					values.password1=values.password2=GO.ab2users.randomPassword(8);
					values.company=this.formPanel.form.findField('company_id').getRawValue();

					GO.users.userDialog.formPanel.form.setValues(values);
				},
				scope:this
			}];
		})
	});


	Ext.override(GO.addressbook.CompanyDialog, {
		initComponent : GO.addressbook.CompanyDialog.prototype.initComponent.createInterceptor(function(){
			this.height+=25;

			//GO.addressbook.contactDialog.elements += ',tbar';

			this.tbar=[{
				iconCls:'btn-save',
				text:GO.ab2users.lang.createUser,
				handler:function(){
					GO.users.showUserDialog();

					var cv = this.companyForm.form.getValues();
					var values={};
					values.username=cv.email;
					values.password1=values.password2=GO.ab2users.randomPassword(8);
					values.email=cv.name;

					values.first_name=GO.ab2users.lang.companyUserFirstName;
					values.last_name=values.company=cv.name;

					values.address=values.work_address=cv.post_address;
					values.address_no=values.work_address_no=cv.post_address_no;
					values.zip=values.work_zip=cv.zip;
					values.city=values.work_city=cv.city;
					values.state=values.work_state=cv.state;
					values.country=values.work_country=cv.country;

					values.home_phone=values.work_phone=cv.phone;
					values.fax=values.work_fax=cv.fax;

					GO.users.userDialog.formPanel.form.setValues(values);
				},
				scope:this
			}];
		})
	});

});
