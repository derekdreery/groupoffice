/**
 * Copyright Intermesh
 *
 * This file is part of Group-Office. You should have received a copy of the
 * Group-Office license along with Group-Office. See the file /LICENSE.TXT
 *
 * If you have questions write an e-mail to info@intermesh.nl
 *
 * @version $Id: SieveGrid.js 0000 2010-12-15 09:38:17Z wsmits $
 * @copyright Copyright Intermesh
 * @author Wesley Smits <wsmits@intermesh.nl>
 */

GO.sieve.SieveGrid = function(config){
	
	this.selectScript = new Ext.form.ComboBox({
		hiddenName:'selectScript',
		valueField:'name',
		displayField:'name',
		store: new GO.data.JsonStore({
			url:GO.settings.modules.sieve.url+'fileIO.php',
			baseParams: {
				task: 'get_sieve_scripts',
				account_id: 0
			},
			fields: ['name'],
			root: 'results'
		}),
		mode:'local',
		triggerAction:'all',
		editable:false,
		selectOnFocus:true,
		forceSelection:true,
		allowBlank:false,
		width:140
	});
	
	if(!config)
	{
		config = {};
	}
	config.title=GO.sieve.lang.activatedrules;
	config.layout='fit';
	config.region='center';
	config.autoScroll=true;
	config.border=false;
	var fields ={
		fields:['id','name', 'index', 'script_name','disabled'],
		columns:[{
			header: GO.sieve.lang.name,
			dataIndex: 'name'
		},{
			header: GO.sieve.lang.index,
			dataIndex: 'index'
		},{
			header: GO.sieve.lang.scriptname,
			dataIndex: 'script_name'
		},{
			header: GO.sieve.lang.disabled,
			dataIndex: 'disabled'
		}
	]
	};
	config.store = new GO.data.JsonStore({
	    url: GO.settings.modules.sieve.url+ 'fileIO.php',
	    baseParams: {
	    	task: 'get_sieve_rules'
	    	},
	    root: 'results',
	    id: 'index',
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
	//config.disabled=true;
	config.view=new Ext.grid.GridView({
		autoFill: true,
		forceFit: true,
		emptyText: GO.lang['strNoItems']
	});
	config.sm=new Ext.grid.RowSelectionModel();
	config.loadMask=true;

	this.sieveDialog = new GO.sieve.SieveDialog();
	this.sieveDialog.on('save', function(){
			this.store.reload();
		}, this);

	config.tbar=[{
			iconCls: 'btn-add',
			text: GO.lang['cmdAdd'],
			cls: 'x-btn-text-icon',
			handler: function(){
	    	this.sieveDialog.show(-1,'groupoffice',this.store.baseParams.account_id);
			},
			scope: this
		},{
			iconCls: 'btn-delete',
			text: GO.lang['cmdDelete'],
			cls: 'x-btn-text-icon',
			handler: function(){
				this.deleteSelected();
			},
			scope: this
		},
		this.selectScript,{
			iconCls: 'btn-delete',
			text: 'activate',
			cls: 'x-btn-text-icon',
			handler: function(){
				// TODO: Activeer het huidige script in de combobox
				Ext.Ajax.request({
					 url: GO.settings.modules.sieve.url+ 'fileIO.php',
					 scope:this,
					 params: { 
						 task: 'set_active_script',
						 script_name: this.selectScript.getValue(),
						 account_id: this.store.baseParams.account_id
					 },
					 success: function(){
						 this.selectScript.store.reload();
						 this.store.reload();
					 },
					 failure: function(){
						 
					 }
				},this);
			},
			scope: this
		}];
	GO.sieve.SieveGrid.superclass.constructor.call(this, config);
	
	this.on('rowdblclick', function(grid, rowIndex){
		var record = grid.getStore().getAt(rowIndex);
		this.sieveDialog.show(record.data.index, record.data.script_name, this.store.baseParams.account_id);
		}, this);

	this.on('show', function(){
		//if(!this.store.loaded){
		this.store.load();
		this.selectScript.store.load({
			callback:function(){
				this.selectScript.setValue(this.selectScript.store.reader.jsonData.active);
			},
			scope:this
		});
		//}
	}, this);
};

Ext.extend(GO.sieve.SieveGrid, GO.grid.GridPanel,{
	setAccountId : function(account_id){
		this.store.baseParams.account_id = account_id;
		this.selectScript.store.baseParams.account_id = account_id;
	}

});