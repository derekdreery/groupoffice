Ext.ns('GO.advancedquery');

GO.advancedquery.AdvancedQueryPanel = function (config){
	if(!config)
	{
		config={};
	}

	config.tbar=[{
		handler: function()
		{
			Ext.Msg.prompt(GO.lang.searchQueryName, GO.lang.enterSearchQueryName, function(btn, text){
				Ext.Ajax.request({
					url:BaseHref +'action.php',
					params:{
						task:'save_advanced_query',
						sql: this.searchQueryPanel.queryField.getValue(),
						type: this.type,
						name: text
					},
					success: function(response, options)
					{
						var responseParams = Ext.decode(response.responseText);
						if(!responseParams.success)
						{
							alert(responseParams.feedback);
						}else
						{
							this.savedQueryGrid.store.load();
						}
					},
					scope:this
				})
			},this)
		},
		iconCls: 'btn-save',
		cls: 'x-btn-text-icon',
		text: GO.lang.cmdSave,
		scope: this
	},{
		handler: function()
		{
			this.searchQueryPanel.queryField.setValue('');
		},
		iconCls: 'btn-delete',
		cls: 'x-btn-text-icon',
		text: GO.lang.cmdReset,
		scope: this
	}
	];

	this.searchQueryPanel = new GO.advancedquery.SearchQueryPanel({
		region:'north',
		height:250,
		fieldsUrl:config.fieldsUrl,
		type:config.type,
		buttons: [{
			text: GO.lang.strSearch,
			handler: function(){		
				this.fireEvent('search', this.ownerCt, this.searchQueryPanel.queryField.getValue());
			},
			scope: this
		}]
	});

	config.items = [this.searchQueryPanel];

		
	this.savedQueryGrid = new GO.advancedquery.SavedQueriesGrid({
		region:'center',
		type:config.type
	});

	config.items.push(this.savedQueryGrid);
	


	config.forceLayout=true;
	config.layout='border';
	config.modal=false;
	config.resizable=true;
	config.closeAction='hide';
	//config.title= GO.filesearch.lang.advancedSearch;

	GO.advancedquery.AdvancedQueryPanel.superclass.constructor.call(this, config);

	this.addEvents({
		'search':true
	});

}

Ext.extend(GO.advancedquery.AdvancedQueryPanel, Ext.Panel);