Ext.form.selectLink = function(config){
	
	Ext.apply(this, config);
	
	
	this.store = new Ext.data.Store({
		
				proxy: new Ext.data.HttpProxy({
					url: BaseHref+'links_json.php'
				}),
		
				baseParams: {"query": ''},
		
				reader: new Ext.data.JsonReader({
					root: 'results',
					totalProperty: 'total',
					id: 'link_id'
				}, [
				{name: 'link_id'},
				{name: 'link_type'},
				{name: 'type_name'}
				]),
		
				// turn on remote sorting
				remoteSort: true
			});
			
	this.displayField='type_name';
			
	Ext.form.selectLink.superclass.constructor.call(this);
	
}

Ext.extend(Ext.form.selectLink, Ext.form.ComboBox);