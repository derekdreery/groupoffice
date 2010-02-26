GO.moduleManager.onModuleReady('customfields', function(){
	GO.customfields.nonGridTypes.push('contact');
	GO.customfields.dataTypes.contact={
		label : GO.addressbook.lang.contact,
		getFormField : function(customfield, config){
			return {
				xtype: 'selectcontact',
       	fieldLabel: customfield.name,
        name:customfield.dataname,
        anchor:'-20'
			}
		}
	}

}, this);