GO.moduleManager.onModuleReady('customfields', function(){
    GO.customfields.nonGridTypes.push('user');
    GO.customfields.dataTypes.user={
	label : GO.lang.strUser,
	getFormField : function(customfield, config){
	    return {
		xtype: 'selectuser',
		idValuePair:true,
		startBlank:true,
		fieldLabel: customfield.name,
		hiddenName:customfield.dataname,
		forceSelection:true,
		anchor:'-20',
		valueField:'cf'
	    }
	}
    }

}, this);