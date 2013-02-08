/** 
 * Copyright Intermesh
 * 
 * This file is part of Group-Office. You should have received a copy of the
 * Group-Office license along with Group-Office. See the file /LICENSE.TXT
 * 
 * If you have questions write an e-mail to info@intermesh.nl
 * 
 * @version $Id: MainPanel.tpl 1913 2008-05-07 12:41:17Z mschering $
 * @copyright Copyright Intermesh
 * @author Merijn Schering <mschering@intermesh.nl>
 */
 
GO.{module}.MainPanel = function(config){
	
	if(!config)
	{
		config = {};
	}

	this.westPanel = new {WESTPANEL}({
    region:'west',
    title:'Menu',
		autoScroll:true,				
		width: 150,
		split:true
	});
	
	this.westPanel.on('rowclick', function(grid, rowIndex)
	{
		var record = grid.getStore().getAt(rowIndex);	
		this.centerPanel.store.baseParams.{centerpanel_related_field} = record.data.id;		
		this.centerPanel.store.load();		
	}, this);
	
	this.westPanel.store.on('load', function(){
		this.westPanel.selModel.selectFirstRow();
		
		GO.{module}.writable{centerpanel_related_friendly_multiple_ucfirst}Store.load();
		
		var record = this.westPanel.selModel.getSelected();
		if(record)
		{
			this.centerPanel.store.baseParams.{centerpanel_related_field} = record.data.id;
			this.centerPanel.store.load();
		}		
	}, this);

	this.centerPanel = new {CENTERPANEL}({
		region:'center',
		border:true
	});
	
	this.centerPanel.on("delayedrowselect",function(grid, rowIndex, r){
		this.eastPanel.load(r.data.id);		
	}, this);
	
	this.eastPanel = new {EASTPANEL}({
		region:'east',
		width:300,
		title:GO.{module}.lang.{centerpanel_friendly_single},
		border:true
	});

	config.items=[
		this.westPanel,
		this.centerPanel,
		this.eastPanel
	];	
	
	config.layout='border';
	GO.{module}.MainPanel.superclass.constructor.call(this, config);	
};


Ext.extend(GO.{module}.MainPanel, Ext.Panel, {
	afterRender : function(){
		GO.{module}.MainPanel.superclass.afterRender.call(this);

	}
});


GO.{module}.writable{centerpanel_related_friendly_multiple_ucfirst}Store = new GO.data.JsonStore({
	    url: GO.settings.modules.{module}.url+ 'json.php',
	    baseParams: {
	    	auth_type:'write',
	    	task: '{centerpanel_related_friendly_multiple}'
	    	},
	    root: 'results',
	    id: 'id',
	    totalProperty:'total',
	    fields: ['id', 'name'],
	    remoteSort: true
	});



/*
 * This will add the module to the main tabpanel filled with all the modules
 */
 
GO.moduleManager.addModule('{module}', GO.{module}.MainPanel, {
	title : GO.{module}.lang.{module},
	iconCls : 'go-tab-icon-{module}'
});
/*
 * If your module has a linkable item, you should add a link handler like this. 
 * The index (no. 1 in this case) should be a unique identifier of your item.
 * See classes/base/links.class.inc for an overview.
 * 
 * Basically this function opens a project window when a user clicks on it from a 
 * panel with links. 
 */

/* {LINKHANDLERS} */


/* {NEWMENUITEMS} */


