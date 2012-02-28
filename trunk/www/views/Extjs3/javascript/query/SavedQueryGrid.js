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

	GO.query.SavedQueryGrid.superclass.constructor.call(this, config);
	
	this.on('contextmenu',function(eventObject,target,object){
		if (!this.queryContextMenu)
			this.queryContextMenu = new GO.query.QueryContextMenu();
		
		this.queryContextMenu.showAt(eventObject.xy);
		this.queryContextMenu.callingGrid = this;
	},this);
	
}

Ext.extend(GO.query.SavedQueryGrid,GO.grid.GridPanel,{

//	saveQuery : function() {
//		Ext.Ajax.request({
//			url : GO.url('advancedSearch/submit'),
//			baseParams: {
//				modelName : this.modelName,
//				data : this.store.getGridData()	
//			},
//			callback : function(options,success,response) {
//				if (!GO.util.empty(success)) {
//					if (this.modelName=='GO_Addressbook_Model_Contact')
//						this.queryPanel._contactsQueryPanel.store.load();
//					else
//						this.queryPanel._companiesQueryPanel.store.load();
//				}
//			}
//		});
//	}

});