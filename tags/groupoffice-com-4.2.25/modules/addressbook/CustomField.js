GO.moduleManager.onModuleReady('customfields', function(){
	//GO.customfields.nonGridTypes.push('contact');
	GO.customfields.dataTypes["GO_Addressbook_Customfieldtype_Contact"]={
		label : GO.addressbook.lang.contact,
		getFormField : function(customfield, config){

			var f = GO.customfields.dataTypes.GO_Customfields_Customfieldtype_Text.getFormField(customfield, config);

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
	
	GO.customfields.dataTypes["GO_Addressbook_Customfieldtype_Company"]={
		label : GO.addressbook.lang.company,
		getFormField : function(customfield, config){

			var f = GO.customfields.dataTypes.GO_Customfields_Customfieldtype_Text.getFormField(customfield, config);

			delete f.name;

			return Ext.apply(f, {
				xtype: 'selectcompany',
				idValuePair:true,
				hiddenName:customfield.dataname,
				forceSelection:true,				
				valueField:'cf'
			});
		}
	}

}, this);