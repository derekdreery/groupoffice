GO.dialog.MultiSelectAddDialog = function(config){
	
	Ext.apply(this, config);
	
	this.store = new GO.data.JsonStore({
		url: GO.url(config.url+'/selectNewStore'),
		baseParams:{model_id: config.model_id},
		fields: config.fields,
		remoteSort: true
	});
	
	this.grid = new GO.grid.GridPanel({
		paging:true,
		border:false,
		store: this.store,
		view: new Ext.grid.GridView({
			autoFill: true,
			forceFit: true
		}),
		columns: config.cm,
		sm: new Ext.grid.RowSelectionModel()	
	});
	
	GO.dialog.MultiSelectAddDialog.superclass.constructor.call(this, {
		layout: 'fit',
		modal:false,
		height:300,
		width:500,
		closeAction:'hide',
		title:'Selecteren', // TODO: De juiste titel in een vertaalbestand zetten
		items: this.grid,
		buttons: [
		{
			text: GO.lang['cmdOk'],
			handler: function (){
				this.callHandler(true);
			},
			scope:this
		},
		{
			text: GO.lang['cmdClose'],
			handler: function(){
				this.hide();
			},
			scope: this
		}]
	});
};

Ext.extend(GO.dialog.MultiSelectAddDialog, GO.Window, {

	show : function(){
		this.grid.store.removeAll();
		this.grid.store.load();
		GO.dialog.MultiSelectAddDialog.superclass.show.call(this);
	},
	//private
	callHandler : function(hide){
		if(this.handler)
		{
			if(!this.scope)
			{
				this.scope=this;
			}
			this.handler.call(this.scope, this.grid, this.grid.selModel.selections.keys);
		}
		if(hide)
		{
			this.hide();
		}
	}	
	
});