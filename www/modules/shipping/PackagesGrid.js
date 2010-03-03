/** 
 * Copyright Intermesh
 * 
 * This file is part of Group-Office. You should have received a copy of the
 * Group-Office license along with Group-Office. See the file /LICENSE.TXT
 * 
 * If you have questions write an e-mail to info@intermesh.nl
 * 
 * @version $Id: PackagesGrid.js
 * @copyright Copyright Intermesh
 * @author Wilmar van Beusekom <wilmar@intermesh.nl>
 */
 
GO.shipping.PackagesGrid = function(config){

	if(!config)
	{
		config = {};
	}
	config.title = GO.shipping.lang.packages;
	config.autoScroll=true;

	var jobpackage = Ext.data.Record.create([
	{
		name: 'id'
	},

	{
		name: 'priority'
	},

	{
		name: 'destination'
	},

	{
		name: 'manager'
	},

	{
		name: 'packer'
	},

	{
		name: 'container_no'
	},

	{
		name: 'shipment_id'
	},

	{
		name: 'status'
	},

	{
		name: 'ctime'
	},

	{
		name: '_parent',
		type: 'auto'
	},

	{
		name: '_is_leaf',
		type: 'bool'
	},

	{
		name: '_id',
		type: 'auto'
	}
	]);

	var jsonReader = new Ext.data.JsonReader({
		totalProperty: 'total',
		root : 'results',
		id : '_id',
		successProperty : 'success'
	}, jobpackage);

	config.store = new Ext.ux.maximgb.tg.AdjacencyListStore({
		autoload : true,
		reader : jsonReader,
		url: GO.settings.modules.shipping.url+ 'json.php',
		baseParams: {
			task: 'job_packages'
		}
		//,remoteSort: true
	});

	config.master_column_id = 'id';
	//config.root_title = 'Job or package id:';
	config.autoExpandColumn = 'id';

	config.paging=true;

	config.columns=[
		{
			id : 'id',
			header: 'ID',
			dataIndex: 'id'
			,sortable : false
			,width : 50
		},{
			header: GO.shipping.lang.priority,
			dataIndex: 'priority'
			,sortable : true
			,width : 50
		},{
			header: GO.shipping.lang.destination,
			dataIndex: 'destination'
			,sortable : true
		},{
			header: GO.shipping.lang.managedBy,
			dataIndex: 'manager'
			,sortable : true
		},{
			header: GO.shipping.lang.packedBy,
			dataIndex: 'packer'
			,sortable : true
		}	,{
			header: GO.shipping.lang.containerNo,
			dataIndex: 'container_no'
			,sortable : true
		},{
			header: GO.shipping.lang.shipmentId,
			dataIndex: 'shipment_id'
			,sortable : true
		},{
			header: GO.shipping.lang.status,
			dataIndex: 'status'
			,sortable : true
		},{
			header: GO.shipping.lang.ctime,
			dataIndex: 'ctime'
			,sortable : true
		}
		];

	config.loadMask=true;
	
	this.searchField = new GO.form.SearchField({
		store: config.store,
		width:320
	});

	config.tbar = [GO.lang['strSearch'] + ':', this.searchField];

	config.bbar = new Ext.ux.maximgb.tg.PagingToolbar({
		store: config.store,
		displayInfo : true,
		pageSize : 30
	})

	if (config.menu) {
		config.menu.addItem('Shipping', new Ext.data.Record(
		{
			dataType : GO.shipping.lang.packages
		}));
	}

	GO.shipping.PackagesGrid.superclass.constructor.call(this, config);

	this.on({
		show : {
			scope:this,
			single:true,
			fn:function() {
				this.store.load({
					params:{
						limit:30
					}
				});
				//this.getSelectionModel().selectFirstRow();
			}
		}
	});

};

Ext.extend(GO.shipping.PackagesGrid, Ext.ux.maximgb.tg.GridPanel,{

	

	afterRender : function()
	{

		GO.shipping.PackagesGrid.superclass.afterRender.call(this);
		this.getSelectionModel().selectFirstRow();
	}
});