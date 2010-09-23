GO.moduleManager.onModuleReady('customfields', function(){
	GO.customfields.nonGridTypes.push('contact');
	GO.customfields.dataTypes.contact={
		label : GO.addressbook.lang.contact,
		getFormField : function(customfield, config){
			return {
				xtype: 'selectcontact',
       	fieldLabel: customfield.name,
        hiddenName:customfield.dataname,
				forceSelection:true,
        anchor:'-20',
				valueField:'cf'
			}
		}
	}

}, this);