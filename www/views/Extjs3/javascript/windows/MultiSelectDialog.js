GO.dialog.MultiSelectDialog = function(config){
	
	Ext.apply(this, config);
	
	this.store = new GO.data.JsonStore({
		url: GO.url(config.url+'/selectedStore'),
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

	GO.dialog.MultiSelectDialog.superclass.constructor.call(this, {
		layout: 'fit',
		modal:false,
		height:400,
		width:600,
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
		}],
		tbar : [
		{
			iconCls: 'btn-add',
			text: GO.lang['cmdAdd'],
			cls: 'add-btn-text-icon',
			handler: function(){
				if(!this.addDialog){
					this.addDialog = new GO.dialog.MultiSelectAddDialog({
						model_id: config.model_id,
						url: config.url,
						fields: config.fields,
						cm: config.cm,
						handler: function(grid, selected){ 
							this.grid.store.load({
								params: {
									add:Ext.encode(selected)
								}
							})
						},
						scope: this
						
					});
//					this.addDialog.on('hide', function(){
//						this.store.reload();
//					}, this);
				}
				this.addDialog.show();

			},
			scope: this
		},{
			iconCls: 'btn-delete',
			text: GO.lang['cmdDelete'],
			cls: 'x-btn-text-icon',
			handler: function()
			{
				this.grid.deleteSelected();
			},
			scope: this
		}]
	});
};

Ext.extend(GO.dialog.MultiSelectDialog, GO.Window, {

	show : function(){
		if(!this.grid.store.loaded)
		{
			this.grid.store.load();
		}
		GO.dialog.MultiSelectDialog.superclass.show.call(this);
	},
	//private
	callHandler : function(hide){
		if(this.handler)
		{
			if(!this.scope)
			{
				this.scope=this;
			}
			
			var selectedIds = this.grid.getSelectionModel().getSelections().keys;
			
			var handler = this.handler.createDelegate(this.scope, [this.grid, selectedIds]);
			handler.call();
		}
		if(hide)
		{
			this.hide();
		}
	}	
	
});