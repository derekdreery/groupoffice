GO.base.form.getFormFieldByType = function(gotype, colName){
	var editor;
	switch(gotype){
				case 'unixtimestamp':
		case 'unixdate':
			editor = new Ext.form.DateField();
			break;
					
		case 'number':
			editor = new GO.form.NumberField();
			break;
					
		case 'boolean':
			editor = new Ext.form.Checkbox();
			break;
			
		case 'customfield':
			editor = new GO.customfields.getFormField(GO.customfields.columnMap[colName]);
			//editor = new Ext.form.Checkbox();
			break;

		default:
			editor = new Ext.form.TextField();
			break;				
	}
	
	return editor;
}