GO.addressbook.AdvancedSearchWindow = function(config){	
	if(!config)
	{
		config={};
	}	
	var store = new GO.data.JsonStore({
			url: GO.settings.modules.addressbook.url+'json.php',			
			baseParams: {task: "fields", type:""},
			root: 'results',			
			id: 'field',
			fields: ['field', 'label', 'value', 'type', 'options'],
			remoteSort: true
		});
	
	var colModel = new Ext.grid.ColumnModel({
		columns: [{
			header: GO.lang.field,
			dataIndex:'label'
		},{
			header: GO.lang.value,
			dataIndex: 'value',
			editable: true
		}],
		editors: {
			'text': new Ext.grid.GridEditor(new Ext.form.TextField({})),
			'textarea': new Ext.grid.GridEditor(new Ext.form.TextArea({})),
			'number': new Ext.grid.GridEditor(new GO.form.NumberField({})),
			'date': new Ext.grid.GridEditor(new Ext.form.DateField({format: GO.settings.date_format})),
			'checkbox': new Ext.grid.GridEditor(new Ext.form.ComboBox({	       		        	        
	        store: new Ext.data.SimpleStore({
		            fields: ['value','text'],
		            data : [
		            	['', GO.lang.strNA],
		            	['1', GO.lang.on],
		            	['0', GO.lang.off]
		            ]	            
		        }),	        
		      valueField:'value',
	        displayField:'text',
	        mode: 'local',
	        triggerAction: 'all',
	        editable: true,
	        selectOnFocus:true,
	        forceSelection:true
		    })),
			'country': new Ext.grid.GridEditor(new GO.form.SelectCountry({}))
			
		},
		getCellEditor: function(colIndex, rowIndex) {
			var r = store.getAt(rowIndex);
			
			if(!this.editors[r.get('type')] && r.get('options'))
			{
				this.editors[r.get('type')]=new Ext.grid.GridEditor(new Ext.form.ComboBox({	       		        	        
	        store: new Ext.data.SimpleStore({
		            fields: ['text'],
		            data : r.get('options')		            
		        }),	        
	        displayField:'text',
	        mode: 'local',
	        triggerAction: 'all',
	        editable: true,
	        selectOnFocus:true
		    }));
			}			
			return this.editors[r.get('type')] || this.editors['text'];
		}
	});

	
	this.grid = new Ext.grid.EditorGridPanel({
		cm:colModel,
		layout:'fit',
		view:new Ext.grid.GridView({
			autoFill:true
		}),
		loadMask : {msg: GO.lang['waitMsgLoad']},
		clicksToEdit:1,
		store: store	
	});
	
	config.items = this.grid;

	
	//config.iconCls='go-link-icon-4';
	config.collapsible=true;
	config.layout='fit';
	config.modal=false;
	config.resizable=true;
	config.width=500;
	config.height=400;
	config.closeAction='hide';
	config.title= GO.addressbook.lang.advancedSearch;		
	config.buttons=[{
			text: GO.lang['cmdOk'],
			handler: function(){
				this.fireEvent('ok', this);
				this.hide();
			},
			scope: this
		},{
			text: GO.lang['cmdClose'],
			handler: function(){
				this.hide();
			},
			scope:this
		}					
	];
	
	
	GO.addressbook.AdvancedSearchWindow.superclass.constructor.call(this, config);	
	
	this.addEvents({'ok' : true});
}

Ext.extend(GO.addressbook.AdvancedSearchWindow, GO.Window,{
	
	editor : new Ext.form.TextField(),
	
	getCellEditor : function(colIndex, rowIndex){
		console.log(this);
		return this.editor;
	},
	show : function(type){
	
		if(type!=this.grid.store.baseParams.type)
		{
			this.grid.store.baseParams.type=type;
			this.grid.store.load();
		}
		GO.addressbook.AdvancedSearchWindow.superclass.show.call(this);
	},
	
	getGridData : function(){
		
		var data = {};
		
		for (var i = 0; i < this.grid.store.data.items.length;  i++)
		{
			var r = this.grid.store.data.items[i];
			
						
			if(!GO.util.empty(r.get('value')))
			{
				data[r.get('field')]=r.get('value');
			}
		}
		
		return data;		
	}
});