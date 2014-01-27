GO.base.model.BatchEditModelDialog = Ext.extend(GO.dialog.TabbedFormDialog, {
	grid: false,
	editors : [],
	
	initComponent : function(){
		
		Ext.apply(this, {
			title:GO.lang.batchEdit,
			formControllerUrl: 'batchEdit',
			loadOnNewModel:false
		});
		
		GO.base.model.BatchEditModelDialog.superclass.initComponent.call(this);	
	},

	setModels : function(model_name, keys, grid, editors,exclude){
		this.formPanel.baseParams.model_name=model_name;
		this.store.baseParams.model_name=model_name;
		//this.formPanel.baseParams.exclude=exclude;
		this.store.baseParams.exclude=exclude;
		this.formPanel.baseParams.keys=Ext.encode(keys);
		this.grid = grid;
		this.editors = editors;
	},
	
	show : function(){
		this.store.load();
		GO.base.model.BatchEditModelDialog.superclass.show.call(this);	
	},
	
	setEditor : function(record){
		var col = this.editGrid.getColumnModel().getColumnById('value');
		var config ={};
		if(!GO.util.empty(record.get('regex')))
			config = {regex: new RegExp(record.get('regex'),record.get('regex_flags'))};
		
		var colName = record.get('name');
		if(this.editors[colName])
			var editor = new this.editors[colName](config);
		else 
			var editor = GO.base.form.getFormFieldByType(record.get('gotype'), record.get('name'), config);
		
		col.setEditor(editor);
	},
	
	afterSubmit : function(){
		if(this.grid)
			this.grid.store.reload();
	},
	
	getSubmitParams : function(){
		return {data:Ext.encode(this.editGrid.getGridData())}
	},
	
	buildForm : function(){
		
		var checkColumn = new GO.grid.CheckColumn({
			header: '&nbsp;',
			id:'edit',
			dataIndex: 'edit',
			width: 20,
			sortable:false,
			hideable:false
		});
	
		var fields ={
			fields:['name','label','edit','value','gotype','regex','regex_flags'],
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
		
		this.store = new GO.data.JsonStore({
			url: GO.url('batchEdit/attributesStore'),
			baseParams:{
				model_name: '' // config.modelType example: GO_Addressbook_Model_Company
			},
			fields: fields.fields,
			//fields: ['name','label','edit','value','gotype'],
			remoteSort: true
		});
	
		
		var columnModel =  new Ext.grid.ColumnModel({
			defaults:{
				sortable:true
			},
			columns:fields.columns
		});

		this.editGrid = new GO.grid.EditorGridPanel({
			fields:fields.fields,
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
					this.setEditor(e.record);
					return true;
				},scope:this,
				afteredit:function(e){
					var t = e.record.get('gotype');

					e.record.set('edit',true);
					
					if(t=='date' || t=='unixtimestamp' || t=='unixdate')
						e.record.set(e.field,e.value.format(GO.settings.date_format));
				}
			}
		});	
			
		this.addPanel(this.editGrid);
	}
});

GO.base.model.showBatchEditModelDialog=function(model_name, keys, grid, editors,exclude,title){
	
	if (keys.length<=0) {
			Ext.Msg.alert(GO.lang.batchSelectionError, GO.lang.batchSelectOne);
			return false;
	}
	
	if(!GO.base.model.batchEditModelDialog){
		GO.base.model.batchEditModelDialog = new GO.base.model.BatchEditModelDialog();
	}
	
	if(title){
		GO.base.model.batchEditModelDialog.setTitle(title);
	}
	
	GO.base.model.batchEditModelDialog.setModels(model_name, keys, grid, editors,exclude);
	GO.base.model.batchEditModelDialog.show();
}