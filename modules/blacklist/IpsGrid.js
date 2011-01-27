/** 
 * Copyright Intermesh
 * 
 * This file is part of Group-Office. You should have received a copy of the
 * Group-Office license along with Group-Office. See the file /LICENSE.TXT
 * 
 * If you have questions write an e-mail to info@intermesh.nl
 * 
 * @version $Id: GridPanel.tpl 1858 2008-04-29 14:09:19Z mschering $
 * @copyright Copyright Intermesh
 * @author Merijn Schering <mschering@intermesh.nl>
 */
GO.blacklist.IpsGrid = function(config){
	if(!config)
	{
		config = {};
	}

	config.layout='fit';
	config.autoScroll=true;
	config.split=true;
	var fields ={
		fields:['ip','mtime','count'],
		columns:[		{
			header: GO.blacklist.lang.ip, 
			dataIndex: 'ip'
		}
		,		{
			header: GO.lang.strMtime, 
			dataIndex: 'mtime'
		}
		,		{
			header: GO.blacklist.lang.count, 
			dataIndex: 'count'
		}
		]
	};
	config.store = new GO.data.JsonStore({
		url: GO.settings.modules.blacklist.url+ 'json.php',
		baseParams: {
			task: 'ips'
		},
		root: 'results',
		id: 'ip',
		totalProperty:'total',
		fields: fields.fields,
		remoteSort: true
	});
	config.paging=true;
	var columnModel =  new Ext.grid.ColumnModel({
		defaults:{
			sortable:true
		},
		columns:fields.columns
	});
	config.cm=columnModel;
	config.view=new Ext.grid.GridView({
		autoFill: true,
		forceFit: true,
		emptyText: GO.lang['strNoItems']		
	});
	config.sm=new Ext.grid.RowSelectionModel();
	config.loadMask=true;
	config.tbar=[{
		iconCls: 'btn-delete',
		text: GO.lang['cmdDelete'],
		cls: 'x-btn-text-icon',
		handler: function(){
			this.deleteSelected();
		},
		scope: this
	}];
	config.listeners={
		scope:this
		,
		render:function(){
			this.store.load();
		}
		,
		rowdblclick:function(grid, rowIndex){
			var record = grid.getStore().getAt(rowIndex);
			this.ipDialog.show(record.data.id);
		}
	}
	GO.blacklist.IpsGrid.superclass.constructor.call(this, config);	
};
Ext.extend(GO.blacklist.IpsGrid, GO.grid.GridPanel,{
	});
