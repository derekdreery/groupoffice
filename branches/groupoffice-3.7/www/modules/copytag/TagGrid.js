/**
 * Copyright Intermesh
 *
 * This file is part of Group-Office. You should have received a copy of the
 * Group-Office license along with Group-Office. See the file /LICENSE.TXT
 *
 * If you have questions write an e-mail to info@intermesh.nl
 *
 * @version $Id: json.php 6110 2011-08-12 15:27:17Z wsmits $
 * @copyright Copyright Intermesh
 * @author Wesley Smits <wsmits@intermesh.nl>
 */
GO.copytag.TagGrid = function(config){
	if(!config)
	{
		config = {};
  }
  config.height=200,
  
	config.layout='fit';
	config.autoScroll=true;		
	config.store = new GO.data.JsonStore({
    url: GO.settings.modules.copytag.url+ 'json.php',
    baseParams: {task: 'grid'},
    root: 'results',
    id: 'id',
    totalProperty:'total',
    fields: ['id','userid','user','tag'],
    remoteSort: true
	});

	var columnModel =  new Ext.grid.ColumnModel({
		defaults:{
			sortable:true
		},
		columns:
    [{
      header: GO.copytag.lang.user,
      dataIndex: 'user'
    },{
      header: GO.copytag.lang.tag,
      dataIndex: 'tag',
      editor:new Ext.form.TextField()
    }]
	});

	config.cm=columnModel;
	config.view=new Ext.grid.GridView({
		autoFill: true,
		forceFit: true,
		emptyText: GO.lang['strNoItems']		
	});
	config.sm=new Ext.grid.RowSelectionModel();
	//config.loadMask=true;
	
	GO.copytag.TagGrid.superclass.constructor.call(this, config);
};
Ext.extend(GO.copytag.TagGrid, Ext.grid.EditorGridPanel,{
	afterRender : function(){
		GO.copytag.TagGrid.superclass.afterRender.call(this);
		this.store.load();
	},
	getGridData : function(){

		var data = {};

		for (var i = 0; i < this.store.data.items.length;  i++)
		{
			var r = this.store.data.items[i].data;

			data[i]={};

			for(var key in r)
			{
				data[i][key]=r[key];
			}
		}

		return data;
	},
	setIds : function(ids)
	{
		for(var index in ids)
		{
			if(index!="remove")
			{
				this.store.getAt(index).set('id', ids[index]);
			}
		}
	},
  saveGridData : function(saveUrl){
    Ext.Ajax.request({
      url: saveUrl,
      params:{
        task:'save_tag_grid',
        gridData: Ext.encode(this.getGridData())
      },
      callback: function(options, success, response)
      {
        if(!success)
        {
          alert( GO.lang['strRequestError']);
        }else
        {
          var responseParams = Ext.decode(response.responseText);
          this.store.load();
        }
      },
      scope:this
    });
  }
  
});
