GO.base.model.BatchEditModelDialog = Ext.extend(GO.dialog.TabbedFormDialog, {
	initComponent : function(){
		
		Ext.apply(this, {
			title:GO.lang.batchEdit,
			formControllerUrl: 'batchEdit',
			loadOnNewModel:false
		});
		
		GO.base.model.BatchEditModelDialog.superclass.initComponent.call(this);	
	},

	setModels : function(model_name, keys){
		this.formPanel.baseParams.model_name=model_name;
		this.store.baseParams.model_name=model_name;
		this.formPanel.baseParams.keys=keys;
	},
	
	show : function(){
		this.store.load();
		GO.base.model.BatchEditModelDialog.superclass.show.call(this);	
	},
	
	setEditor : function(gotype){
		var col = this.editGrid.getColumnModel().getColumnById('value');
		var editor = GO.base.form.getFormFieldByType(gotype);
		col.setEditor(editor);
	},
	
	buildForm : function(){
		this.store = new GO.data.JsonStore({
			url: GO.url('batchEdit/attributesStore'),
			baseParams:{
				model_name: '' // config.modelType example: GO_Addressbook_Model_Company
			},
			fields: ['name','label','edit','value','gotype'],
			remoteSort: true
		});
	
		var checkColumn = new GO.grid.CheckColumn({
			header: '&nbsp;',
			id:'edit',
			dataIndex: 'edit',
			width: 20,
			sortable:false,
			hideable:false
		});
	
		var fields ={
			fields:['name','label','edit','value','gotype'],
			columns:[
			checkColumn,{
				header:GO.lang['label'],
				dataIndex: 'label',
				sortable:false,
				hideable:false,
				editable:false,
				id:'label'
			},{
				header:GO.lang['value'],
				dataIndex: 'value',
				sortable:false,
				hideable:false,
				editable:true,
				editor: new Ext.form.TextField({}),
				id:'value'
			}		
			]
		};
	
		var columnModel =  new Ext.grid.ColumnModel({
			defaults:{
				sortable:true
			},
			columns:fields.columns
		});

		this.editGrid = new Ext.grid.EditorGridPanel({
			fields:fields,
			store:this.store,
			cm:columnModel,
			view:new Ext.grid.GridView({
				autoFill: true,
				forceFit: true,
				emptyText: GO.lang['strNoItems']
			}),
			sm:new Ext.grid.RowSelectionModel(),
			loadMask:true,
			clicksToEdit:1,
			listeners:{
				beforeedit:function(e){			
					this.setEditor(e.record.get('gotype'));
					return true;
				},scope:this
			}
		});	
		
		this.addPanel(this.editGrid);
	}
});

GO.base.model.showBatchEditModelDialog=function(model_name, keys){
	
	if (keys.length<=0) {
			Ext.Msg.alert(GO.lang.batchSelectionError, GO.lang.batchSelectOne);
			return false;
	}
	
	if(!GO.base.model.batchEditModelDialog){
		GO.base.model.batchEditModelDialog = new GO.base.model.BatchEditModelDialog();
	}
	GO.base.model.batchEditModelDialog.setModels(model_name, keys);
	GO.base.model.batchEditModelDialog.show();
}