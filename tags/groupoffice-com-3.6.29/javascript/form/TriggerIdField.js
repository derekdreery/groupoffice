GO.form.TriggerIdField = Ext.extend(Ext.form.TriggerField, {

	onRender : function(ct, position){

		GO.form.TriggerIdField.superclass.onRender.call(this, ct, position);
		this.hiddenField = this.el.insertSibling({tag:'input', type:'hidden', name: this.name},'before', true);

		// prevent input submission
    this.el.dom.removeAttribute('name');
		
	},

	 // private
	initValue : function(){
		GO.form.TriggerIdField.superclass.initValue.call(this);
		if(this.hiddenField){
				this.hiddenField.value =
						this.hiddenValue !== undefined ? this.hiddenValue :
						this.value !== undefined ? this.value : '';
		}
	},

	setValue : function(v){
		if(this.hiddenField){
				this.hiddenField.value = v;
		}
		//GO.form.TriggerIdField.superclass.setValue.call(this, v);
	},

	setText : function(text){
		this.setRawValue(text);
	}
});