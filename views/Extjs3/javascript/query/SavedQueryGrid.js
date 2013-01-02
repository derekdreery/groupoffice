GO.query.SavedQueryGrid = function(config) {
	
	config = config || {};
	
	config.title = GO.lang['queries'];
	
	config.width = 230;
	
	config.store = new GO.data.JsonStore({
		url : GO.url('advancedSearch/store'),
		root : 'results',
		baseParams:{
			model_name: config.modelName
		},
		totalProperty : 'total',
		fields : ['id','name','acl_id','user_id','data'],
		remoteSort : true
	});
	
	config.cm=new Ext.grid.ColumnModel({
		defaults:{
			sortable:true
		},
		columns: [{
			dataIndex : 'id',
			hidden: true,
			id: 'id'
		},
		{
			header: GO.lang['strName'],
			dataIndex : 'name',
			hidden: false,
			width: '230',
			id: 'name'
		}]
	});
	
	config.view=new Ext.grid.GridView({
		emptyText: GO.lang.strNoItems	
	});
	
	config.sm=new Ext.grid.RowSelectionModel();
	config.loadMask=true;
	
	config.listeners={
		render:function(){
			this.store.load();
		},
		scope:this
	}
	
	config.paging = true;
	
	this.searchField = new GO.form.SearchField({
		store: config.store,
		width:100
	});
	config.tbar = [GO.lang['strSearch'] + ':', this.searchField];

	GO.query.SavedQueryGrid.superclass.constructor.call(this, config);
	
	this.queryPanel.on('reset',function(){
		this.queryId=0;
	},this);
	
	this.on('rowdblclick',function(grid,rowId,e){
			var record = grid.store.getAt(rowId);
			this.queryId = record.data.id;
			this.queryPanel.setCriteriaStore(record);
			this.queryPanel.titleField.setValue('<b>'+record.data.name+'</b>');
		},this);
	
	this.on('contextmenu',function(eventObject,target,object){
		if (!this.queryContextMenu)
			this.queryContextMenu = new GO.query.QueryContextMenu();
		
		this.queryContextMenu.showAt(eventObject.xy);
		this.queryContextMenu.callingGrid = this;
	},this);
	
}

Ext.extend(GO.query.SavedQueryGrid,GO.grid.GridPanel,{
	
	queryId : 0,
	
	queryPanel : false,
	
	showSavedQueryDialog : function(queryId) {
		
		if(!queryId)
			queryId=this.queryId;
		
		if (!this.savedQueryDialog)
			this.savedQueryDialog = new GO.query.SavedQueryDialog({
				savedQueryGrid:this
			});
		
		this.savedQueryDialog.show(
			queryId, {
				'model_name' : this.modelName
			}
		);
	}
});