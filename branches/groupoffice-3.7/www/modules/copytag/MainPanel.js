GO.copytag.MainPanel = function(config){


  this.tagGrid = new GO.copytag.TagGrid();


	if(!config)
	{
		config = {};
	}
	config.labelWidth=150;
	config.border=false;
	config.padding= 10;
	config.items=[this.tagGrid];

	config.tbar=new Ext.Toolbar({
		cls:'go-head-tb',
		items: 
    [{
      iconCls: 'btn-save',
      text: GO.lang.cmdSave,
      cls: 'x-btn-text-icon',
      handler: function(){this.saveGrid()},
      scope: this
    },{
      iconCls: 'btn-delete',
      text: GO.lang.cmdCancel,
      cls: 'x-btn-text-icon',
      handler: function(){this.tagGrid.store.load()},
      scope: this
    }]
	});

	GO.copytag.MainPanel.superclass.constructor.call(this, config);
};

Ext.extend(GO.copytag.MainPanel, Ext.Panel, {
 saveGrid:function (){
   this.tagGrid.saveGridData(GO.settings.modules.copytag.url+'action.php');
 }
});

/*
 * This will add the module to the main tabpanel filled with all the modules
 */
GO.moduleManager.addModule('copytag', GO.copytag.MainPanel, {
	title : GO.copytag.lang.userTags,
	iconCls : 'go-tab-icon-copytag',
  admin :true
});