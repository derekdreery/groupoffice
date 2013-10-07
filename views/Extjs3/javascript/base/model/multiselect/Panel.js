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
			update:function(store, record,operation){
				if(operation==Ext.data.Record.EDIT){
					GO.request({
						maskEl:this.getEl(),
						url: this.url+'/updateRecord',
						params: {
							model_id: this.model_id,
							record: Ext.encode(record.data)
						},
						success:function(){
							this.store.commitChanges();
						},
						fail:function(){
							this.store.rejectChanges();
						},
						scope: this
					});
				}
			},
			scope:this
		}
	});
	
		
	this.searchField = new GO.form.SearchField({
		store: config.store,
		width:320
	});
	
	if(typeof(config.paging)=='undefined')
		config.paging=true;

	config.view=new Ext.grid.GridView({
		autoFill: true,
		forceFit: true
	});

	

	config.sm=new Ext.grid.RowSelectionModel();
	Ext.apply(config,{
		loadMask:true,
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
							var params={add:Ext.encode(selected)};
							
							if(config.addAttributes){
								params.addAttributes=Ext.encode(config.addAttributes)
							}
							
							this.store.load({
								params: params
							});
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
		},
		GO.lang['strSearch'] + ':', this.searchField
	]
	});	

	GO.base.model.multiselect.panel.superclass.constructor.call(this, config);
	
	this.on('show',function(){	
			this.store.load();
	}, this);
};

Ext.extend(GO.base.model.multiselect.panel, GO.grid.EditorGridPanel, {

	autoLoadStore : true,
	
	model_id: 0,

	afterRender : function(){

		GO.base.model.multiselect.panel.superclass.afterRender.call(this);	

		if(this.autoLoadStore && !this.store.loaded && this.model_id)		
			this.store.load();
	},
	setModelId : function(model_id,load){
		this.store.loaded=false;
		this.model_id=this.store.baseParams.model_id=model_id;
		this.setDisabled(!model_id);
		
		if(load){
			if(model_id)
				this.store.load();
			else
				this.store.removeAll();
		}
	}
	//private
//	callHandler : function(hide){
//		if(this.handler)
//		{
//			if(!this.scope)
//			{
//				this.scope=this;
//			}
//			
//			var selectedIds = this.getSelectionModel().getSelections().keys;
//			
//			var handler = this.handler.createDelegate(this.scope, [this.grid, selectedIds]);
//			handler.call();
//		}
//		if(hide)
//		{
//			this.ownerCt.hide();
//		}
//	}	
	
});