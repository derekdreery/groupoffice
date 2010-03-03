/** 
 * Copyright Intermesh
 * 
 * This file is part of Group-Office. You should have received a copy of the
 * Group-Office license along with Group-Office. See the file /LICENSE.TXT
 * 
 * If you have questions write an e-mail to info@intermesh.nl
 * 
 * @version $Id: JobsGrid.js
 * @copyright Copyright Intermesh
 * @author Wilmar van Beusekom <wilmar@intermesh.nl>
 */
 
GO.shipping.JobsGrid = function(config){
	
	if(!config)
	{
		config = {};
	}
	config.title = GO.shipping.lang.jobs;
	config.layout='fit';
	config.autoScroll=true;
	config.split=true;
	config.store = new GO.data.JsonStore({
		url: GO.settings.modules.shipping.url+ 'json.php',
		baseParams: {
			task: 'jobs'
		},
		root: 'results',
		id: 'id',
		totalProperty:'total',
		fields: ['id','destination','description','supplier','ctime','customer','order_by'],
		remoteSort: true
	});

	
	config.paging=true;

	var columnModel =  new Ext.grid.ColumnModel({
		defaults:{
			sortable:true
		},
		columns:[
		{
			header: 'ID',
			dataIndex: 'id'
		},{
			header: GO.shipping.lang.destination,
			dataIndex: 'destination'
		},{
			header: GO.shipping.lang.description,
			dataIndex: 'description'
		},{
			header: GO.shipping.lang.supplier,
			dataIndex: 'supplier'
		}	,{
			header: GO.shipping.lang.ctime,
			dataIndex: 'ctime'
		},{
			header: GO.shipping.lang.customer,
			dataIndex: 'customer'
		},{
			header: GO.shipping.lang.managedBy,
			dataIndex: 'order_by'
		}
		]
	});
	
	config.cm=columnModel;
	
	config.view=new Ext.grid.GridView({
		autoFill: true,
		forceFit: true,
		emptyText: GO.lang['strNoItems']		
	});
	
	config.sm=new Ext.grid.RowSelectionModel();
	config.loadMask=true;
	
	this.searchField = new GO.form.SearchField({
		store: config.store,
		width:320
	});
		    	
	config.tbar = [GO.lang['strSearch'] + ':', this.searchField];

	if (config.menu) {
		config.menu.addItem('Shipping', new Ext.data.Record(
		{
				dataType : GO.shipping.lang.jobs
		}));
	}

	GO.shipping.JobsGrid.superclass.constructor.call(this, config);

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
		}
	}
	});
};

Ext.extend(GO.shipping.JobsGrid, GO.grid.GridPanel,{

	

	afterRender : function()
	{
		if(!GO.notes.noteDialog.hasListener('save'))
		{
			GO.notes.noteDialog.on('save', function(){
				this.store.reload();
			}, this);
		}

		this.store.load();

		GO.shipping.JobsGrid.superclass.afterRender.call(this);
	}
});