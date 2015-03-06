GO.cron.ParametersPanel = Ext.extend(Ext.Panel, {
	title:GO.cron.lang['parameters'],			
	cls:'go-form-panel',
	layout:'form',
	labelWidth: 90,
	buildForm : function(params) {

		console.log('buildForm');

		for(var key in params){
			this.addField(key,params[key],key)
		}
		
		this.doLayout();
	},
	addField : function(name,value,label){
		console.log('addField');
		var inputField = new Ext.form.TextField({
			name: name,
			value: value,
			fieldLabel: label
		});

		this.add(inputField);
		
	},
	reset : function(){
		console.log('reset');
		
		var f;
		while(f = this.items.first()){
			console.log('remove');
			this.remove(f, true);
		}
	}
});