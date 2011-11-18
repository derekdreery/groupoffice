GO.base.form.getFormFieldByType = function(gotype, colName, config){
	var editor;
	switch(gotype){
		case 'date':
		case 'unixtimestamp':
		case 'unixdate':
			editor = new Ext.form.DateField(config);
			break;
					
		case 'number':
			editor = new GO.form.NumberField(config);
			break;
					
		case 'boolean':
			editor = new Ext.form.Checkbox(config);
			break;
			
		case 'customfield':
			editor = new GO.customfields.getFormField(GO.customfields.columnMap[colName], config);
			//editor = new Ext.form.Checkbox();
			break;

		default:
			editor = new Ext.form.TextField(config);
			break;				
	}
	
	return editor;
}