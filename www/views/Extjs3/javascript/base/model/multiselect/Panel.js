GO.base.model.multiselect.panel = function(config){
	
	config = config || {};
	
	config.store = new GO.data.JsonStore({
		url: GO.url(config.url+'/selectedStore'),
		baseParams:{
			model_id: config.model_id
			},
		fields: config.fields,
		remoteSort: true,
		listeners:{
			update:function(store, record){
				GO.request({
					url: this.url+'/updateRecord',
					params: {
						model_id: this.model_id,
						record: Ext.encode(record.data)
					},
					success:function(){
					//record.commit();
					},
					scope: this
				});
			},
			scope:this
		}
	});
	
	if(typeof(config.paging)=='undefined')
		config.paging=true;

	config.view=new Ext.grid.GridView({
		autoFill: true,
		forceFit: true
	});
	

	config.sm=new Ext.grid.RowSelectionModel();
	Ext.apply(config,{
		layout: 'fit',
		title:config.title,
		tbar : [
		{
			iconCls: 'btn-add',
			text: GO.lang['cmdAdd'],
			cls: 'add-btn-text-icon',
			handler: function(){
				if(!this.addDialog){
					if(!config.selectColumns)
						config.selectColumns = config.columns;
					
					this.addDialog = new GO.base.model.multiselect.addDialog({
						multiSelectPanel:this,
						url: config.url,
						fields: config.fields,
						cm: config.selectColumns,
						handler: function(grid, selected){ 
							this.store.load({
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
				this.deleteSelected();
			},
			scope: this
		}]
	});	

	GO.base.model.multiselect.panel.superclass.constructor.call(this, config);
};

Ext.extend(GO.base.model.multiselect.panel, GO.grid.EditorGridPanel, {

	model_id: 0,

	afterRender : function(){
		
		GO.base.model.multiselect.panel.superclass.afterRender.call(this);
		if(!this.store.loaded)		
			this.store.load();
	},
	setModelId : function(model_id){
		this.store.loaded=false;
		this.model_id=this.store.baseParams.model_id=model_id;
		this.setDisabled(!model_id);
	},
	//private
	callHandler : function(hide){
		if(this.handler)
		{
			if(!this.scope)
			{
				this.scope=this;
			}
			
			var selectedIds = this.getSelectionModel().getSelections().keys;
			
			var handler = this.handler.createDelegate(this.scope, [this.grid, selectedIds]);
			handler.call();
		}
		if(hide)
		{
			this.hide();
		}
	}	
	
});