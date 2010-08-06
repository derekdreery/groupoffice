GO.moduleManager.onModuleReady('customfields', function(){
    GO.customfields.nonGridTypes.push('user');
    GO.customfields.dataTypes.user={
		label : GO.lang.strUser,
		getFormField : function(customfield, config){

			var f = GO.customfields.dataTypes.text.getFormField(customfield, config);

			delete f.name;

			f=Ext.apply(f, {
				xtype: 'selectuser',
				idValuePair:true,
				startBlank:true,
				forceSelection:true,
				hiddenName:customfield.dataname,
				anchor:'-20',
				valueField:'cf'
			});

			return f;
		}
    }

}, this);