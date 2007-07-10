selectUser = function(textElement, hiddenElement, user_id, text, width){


	var ds = new Ext.data.Store({

		proxy: new Ext.data.HttpProxy({
			url: GOmodules.users.url+'users_json.php'
		}),

		reader: new Ext.data.JsonReader({
			root: 'results',
			totalProperty: 'total',
			id: 'id'
		}, [
		{name: 'id'},
		{name: 'name'}
		]),
		// turn on remote sorting
		remoteSort: true
	});
	ds.setDefaultSort('name', 'asc');



	var userSelect = new Ext.form.ComboBox({
		store: ds,
		displayField:'name',
		hiddenName:'user_id',
		typeAhead: true,
		valueField: 'id',
		triggerAction: 'all',
		emptyText:GOlang['strPleaseSelect'],
		width: width,
		selectOnFocus:true
	});
	
	//this method can be used to select it from the dataset but this causes unnecessary load
	//on the database
	//ds.on('load', function() {userSelect.setValue(user_id);}, this, {single: true});
	//ds.load();
	
	Ext.get(textElement).set({'value': text});
	userSelect.applyTo(textElement);	
	Ext.get(hiddenElement).set({'value': user_id});
	
	return userSelect;
}