

GO.LinkTypeFilterPanel = function(config)
{
	if(!config)
	{
		config = {};
	}
	
	config.autoScroll=true;
	
	var checkColumn = new GO.grid.CheckColumn({
		header: '&nbsp;',
		dataIndex: 'checked',
		width: 30
	});
	
	config.title=GO.lang.strType;
	
	this.filterGrid = new GO.grid.GridPanel({
		cls:'go-grid3-hide-headers',
		autoHeight:true,
		border:false,
		loadMask:true,
		store: new Ext.data.JsonStore({
			root: 'results',
			data: {"results":GO.linkTypes},
			fields: ['id','name', 'checked']
		}),
		columns: [
				checkColumn,
				{
					header: GO.lang.strName, 
					dataIndex: 'name'					
				}				
			],
		plugins: [checkColumn],
		autoExpandColumn:1		
	});
	
	
	
	var applyButton = new Ext.Button({
		text:GO.lang.cmdApply,
		handler:function(){
			
			var types = [];
			
			for (var i = 0; i < this.filterGrid.store.data.items.length;  i++)
			{
				var checked = this.filterGrid.store.data.items[i].get('checked');
				if(checked=="1")
				{
					types.push(this.filterGrid.store.data.items[i].get('id'));	
				}				
			}
			
			this.fireEvent('change', this, types);
			
			this.filterGrid.store.commitChanges();			
		},
		scope: this
	});
	
	config.items=[
	this.filterGrid,
	new Ext.Panel({
		border:false,
		cls:'go-form-panel',
		items:[
			new GO.form.HtmlComponent({html: '<br />'}), 
			applyButton
			]
	})];
	
	GO.LinkTypeFilterPanel.superclass.constructor.call(this, config);
	
	this.addEvents({change : true});
}

Ext.extend(GO.LinkTypeFilterPanel, Ext.Panel);

