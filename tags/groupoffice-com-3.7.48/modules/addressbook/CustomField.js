GO.moduleManager.onModuleReady('customfields', function(){
	//GO.customfields.nonGridTypes.push('contact');
	GO.customfields.dataTypes.contact={
		label : GO.addressbook.lang.contact,
		getFormField : function(customfield, config){

			var f = GO.customfields.dataTypes.text.getFormField(customfield, config);

			delete f.name;

			return Ext.apply(f, {
				xtype: 'selectcontact',
				idValuePair:true,
				hiddenName:customfield.dataname,
				forceSelection:true,				
				valueField:'cf'
			});
		}
	}

}, this);