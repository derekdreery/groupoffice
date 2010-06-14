

GO.LinkTypeFilterPanel = function(config)
{
	if(!config)
	{
		config = {};
	}

	config.split=true;
	config.resizable=true;
	config.autoScroll=true;
	config.collapsible=true;
	config.header=false;
	config.collapseMode='mini';
	
	var checkColumn = new GO.grid.CheckColumn({
		header: '&nbsp;',
		dataIndex: 'checked',
		width: 30
	});
	
	//config.title=GO.lang.strType;

	if(!GO.links.typesStore){
		GO.links.typesStore= new Ext.data.JsonStore({
				root: 'results',
				data: {"results":GO.linkTypes}, //defined in /default_scripts.inc.php
				fields: ['id','name', 'checked'],
				id:'id'
			});
	}
	
	this.filterGrid = new GO.grid.GridPanel({
		cls:'go-grid3-hide-headers',
		autoHeight:true,
		border:false,
		loadMask:true,
		store: GO.links.typesStore,
		columns: [
				checkColumn,
				{
					header: GO.lang.strName, 
					dataIndex: 'name',
					id:'name'
				}				
			],
		plugins: [checkColumn],
		autoExpandColumn:'name',
		listeners:{
			scope:this,
			delayedrowselect:function(grid, rowIndex, r){
				this.applyFilter(r.id);
			}
		}
	});


	config.tbar=[{
		iconCls:'btn-save',
		text:GO.lang.cmdApply,
		handler:function(){
			this.applyFilter(-1);
		},
		scope: this
	},{
		iconCls:'btn-delete',
		text:GO.lang.cmdReset,
		handler:function(){
			this.applyFilter(0);
		},
		scope: this
	}];
	
	config.items=[
	this.filterGrid/*,
	new Ext.Panel({
		border:false,
		cls:'go-form-panel',
		items:[
			new GO.form.HtmlComponent({html: '<br />'}), 
			applyButton
			]
	})*/];
	
	GO.LinkTypeFilterPanel.superclass.constructor.call(this, config);
	
	this.addEvents({change : true});
}

Ext.extend(GO.LinkTypeFilterPanel, Ext.Panel,{
	applyFilter : function(type_id){

		var types = [], checked, current_type_id;

		for (var i = 0; i < this.filterGrid.store.data.items.length;  i++)
		{
			current_type_id = this.filterGrid.store.data.items[i].get('id');
			if(type_id>-1 && type_id != current_type_id){
				checked=false;
				this.filterGrid.store.data.items[i].set('checked', "0");
			}else
			{
				if(type_id ==current_type_id){
					checked="1";
					this.filterGrid.store.data.items[i].set('checked', "1");
				}else
				{
					checked = this.filterGrid.store.data.items[i].get('checked');
				}
			}
			if(checked=="1")
			{
				types.push(this.filterGrid.store.data.items[i].get('id'));
			}
		}

		this.fireEvent('change', this, types);

		this.filterGrid.store.commitChanges();
		this.filterGrid.getSelectionModel().clearSelections();
	}
});

