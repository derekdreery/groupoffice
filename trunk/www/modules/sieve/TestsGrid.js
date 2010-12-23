/**
 * Copyright Intermesh
 *
 * This file is part of Group-Office. You should have received a copy of the
 * Group-Office license along with Group-Office. See the file /LICENSE.TXT
 *
 * If you have questions write an e-mail to info@intermesh.nl
 *
 * @version $Id: TestsGrid.js 0000 2010-12-16 09:38:17Z wsmits $
 * @copyright Copyright Intermesh
 * @author Wesley Smits <wsmits@intermesh.nl>
 */

GO.sieve.TestsGrid = function(config){
	if(!config)
	{
		config = {};
	}
	config.autoScroll=true;
	config.height=180;
	config.style='margin: 5px;';
	config.border=true;
	var fields ={
		fields:['test','not','type','arg','arg1','arg2'],
		columns:[{
			header: GO.sieve.lang.test,
			dataIndex: 'test'			
		},{
			header: GO.sieve.lang.not,
			dataIndex: 'not'
		},{
			header: GO.sieve.lang.type,
			dataIndex: 'type'
		},{
			header: GO.sieve.lang.arg,
			dataIndex: 'arg'
		},{
			header: GO.sieve.lang.arg1,
			dataIndex: 'arg1'
		}

	/*
	  {
			header: GO.sieve.lang.arg1,
			dataIndex: 'arg1',
			renderer: function(v){
				var r = GO.sieve.cmbFieldStore.getById(v);
				return r ? r.get('field') : v;
			}
		}
	*/

		,{
			header: GO.sieve.lang.arg2,
			dataIndex: 'arg2'
		}
	]
	};
	config.store = new GO.data.JsonStore({
	    url: GO.settings.modules.sieve.url+ 'fileIO.php',
	    baseParams: {
	    	task: 'get_sieve_tests_json'
	    	},
	    root: 'tests',
	    id: 'id',
	    totalProperty:'total',
	    fields: fields.fields,
	    remoteSort: true
	});
	var columnModel =  new Ext.grid.ColumnModel({
		defaults:{
			sortable:true
		},
		columns:fields.columns
	});

	config.cm=columnModel;
	//config.disabled=true;
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
				this.store.remove(this.getSelectionModel().getSelections());
			},
			scope: this
		}];

	GO.sieve.TestsGrid.superclass.constructor.call(this, config);
};

Ext.extend(GO.sieve.TestsGrid, GO.grid.GridPanel,{

	setAccountId : function(account_id){
		this.store.baseParams.account_id = account_id;
	},
	setScriptName : function(name){
		this.store.baseParams.script_name = name;
	},
	setScriptIndex : function(index){
		this.store.baseParams.script_index = index;
	}
});