/** 
 * Copyright Intermesh
 * 
 * This file is part of Group-Office. You should have received a copy of the
 * Group-Office license along with Group-Office. See the file /LICENSE.TXT
 * 
 * If you have questions write an e-mail to info@intermesh.nl
 * 
 * @version $Id$
 * @copyright Copyright Intermesh
 * @author Merijn Schering <mschering@intermesh.nl>
 */
 
/**
 * @class GO.grid.GridPanel
 * @extends Ext.grid.GridPanel
 * This class represents the primary interface of a component based grid control.
 * 
 * This extension of the default Ext grid implements some basic Group-Office functionality
 * like deleting items.
 *  
 * <br><br>Usage:
 * <pre><code>var grid = new Ext.grid.GridPanel({
    store: new Ext.data.Store({
        reader: reader,
        data: xg.dummyData
    }),
    columns: [
        {id:'company', header: "Company", width: 200, sortable: true, dataIndex: 'company'},
        {header: "Price", width: 120, sortable: true, renderer: Ext.util.Format.usMoney, dataIndex: 'price'},
        {header: "Change", width: 120, sortable: true, dataIndex: 'change'},
        {header: "% Change", width: 120, sortable: true, dataIndex: 'pctChange'},
        {header: "Last Updated", width: 135, sortable: true, renderer: Ext.util.Format.dateRenderer('m/d/Y'), dataIndex: 'lastChange'}
    ],
    viewConfig: {
        forceFit: true
    },
    sm: new Ext.grid.RowSelectionModel({singleSelect:true}),
    width:600,
    height:300,
    frame:true,
    title:'Framed with Checkbox Selection and Horizontal Scrolling',
    iconCls:'icon-grid'
});</code></pre>
 * <b>Note:</b> Although this class inherits many configuration options from base classes, some of them
 * (such as autoScroll, layout, items, etc) won't function as they do with the base Panel class.<br>
 * <br>
 * To access the data in a Grid, it is necessary to use the data model encapsulated
 * by the {@link #store Store}. See the {@link #cellclick} event.
 * @constructor
 * @param {Object} config The config object
 */


GO.grid.GridPanel =Ext.extend(Ext.grid.GridPanel, {
	
	initComponent : function(){
		

		if(!this.keys)
		{
			this.keys=[];
		}
	
		if(!this.store)
		{
			this.store=this.ds;
		}
		
		if(this.store.model && GO.customfields && GO.customfields.columns[this.store.model]){
			for(var i=0;i<GO.customfields.columns[this.store.model].length;i++)
			{
				if(GO.customfields.nonGridTypes.indexOf(GO.customfields.columns[this.store.model][i].datatype)==-1){
					if(GO.customfields.columns[this.store.model][i].exclude_from_grid != 'true')
					{
            if(!this.columns){
							this.columns = this.cm.columns;
						}              
						this.columns.push(GO.customfields.columns[this.store.model][i]);
					}
				}
			}	
		}

		if(!this.noDelete){
			this.keys.push({
				key: Ext.EventObject.DELETE,
				fn: function(key, e){
					//sometimes there's a search input in the grid, so dont delete when focus is on an input
					if(e.target.tagName!='INPUT')
						this.deleteSelected(this.deletethis);
				},
				scope:this
			});
		}
    
		if(this.paging)
		{
			if(typeof(this.paging)=='boolean')
				this.paging=parseInt(GO.settings['max_rows_list']);

			if(!this.bbar)
			{
				this.bbar = new Ext.PagingToolbar({
					cls: 'go-paging-tb',
					store: this.store,
					pageSize: this.paging,
					displayInfo: true,
					displayMsg: GO.lang['displayingItems'],
					emptyMsg: GO.lang['strNoItems']
				});
			}
    
			if(!this.store.baseParams)
			{
				this.store.baseParams={};
			}
			this.store.baseParams['limit']=this.paging;
		}
		
		this.store.on('load', function(){
			this.changed=false;
			
			if(this.store.reader.jsonData && this.store.reader.jsonData.title)
				this.setTitle(this.store.reader.jsonData.title);
		}, this);
	
		if(typeof(this.loadMask)=='undefined')
			this.loadMask=true;
	
		if(!this.sm)
			this.sm=this.selModel=new Ext.grid.RowSelectionModel();
	
		if(this.standardTbar){
			this.tbar = this.tbar ? this.tbar : [];

			this.tbar.push({
				itemId:'add',
				iconCls: 'btn-add',							
				text: GO.lang['cmdAdd'],
				cls: 'x-btn-text-icon',
				handler: this.btnAdd,
				disabled:this.standardTbarDisabled,
				scope: this
			},{
				itemId:'delete',
				iconCls: 'btn-delete',
				text: GO.lang['cmdDelete'],
				cls: 'x-btn-text-icon',
				disabled:this.standardTbarDisabled,
				handler: function(){
					this.deleteSelected();
				},
				scope: this
			});
			
			if(!this.hideSearchField){
				this.tbar.push(
					'-',
					new GO.form.SearchField({
						store: this.store,
						width:150
					})
				);
			}
		}
		
		
		
		
		GO.grid.GridPanel.superclass.initComponent.call(this);
		
		//create a delayed rowselect event so that when a user repeatedly presses the
		//up and down button it will only load if it stays on the same record for 400ms
		this.addEvents({
			'delayedrowselect':true
		});



		this.on("rowcontextmenu", function(grid, rowIndex, e) {
			e.stopEvent();

			this.rowClicked=true;

			var sm =this.getSelectionModel();
			if(sm.isSelected(rowIndex) !== true) {
				sm.clearSelections();
				sm.selectRow(rowIndex);
			}
		}, this);

		this.on('rowclick', function(grid, rowIndex, e){
			var record = this.getSelectionModel().getSelected();

			if(!e.ctrlKey && !e.shiftKey)
			{
				if(record)
					this.fireEvent('delayedrowselect', this, rowIndex, record);
			}
		
			if(record)
				this.rowClicked=true;
		}, this);

		this.getSelectionModel().on("rowselect",function(sm, rowIndex, r){
			if(!this.rowClicked)
			{
				var record = this.getSelectionModel().getSelected();
				if(record==r)
				{
					this.fireEvent('delayedrowselect', this, rowIndex, r);
				}
			}
			this.rowClicked=false;
		}, this, {
			delay:250
		});
		
		//Load the datastore when render event fires if autoLoadStore is true
		this.on('render',function(grid)
		{
			if(this.autoLoadStore)
				grid.store.load();
		}, this);
	
		this.on('rowdblclick', function(grid, rowIndex){
			var record = grid.getStore().getAt(rowIndex);			
			this.dblClick(grid, record, rowIndex)		
		}, this);
	
	},

	deleteConfig : {},

	/**
	 *@cnf {Boolean} Load the datastore into the grid when it's rendered for the first time
	 */
	autoLoadStore: false,

	/**
	 * @cfg {Boolean} paging True to set the store's limit parameter and render a bottom
	 * paging toolbar.
	 */
	paging : false,

	/**
	 * Sends a delete request to the remote store. It will send the selected keys in json
	 * format as a parameter. (delete_keys by default.)
	 *
	 * @param {Object} options An object which may contain the following properties:<ul>
     * <li><b>deleteParam</b> : String (Optional)<p style="margin-left:1em">The name of the
     * parameter that will send to the store that holds the selected keys in JSON format.
     * Defaults to "delete_keys"</p>
     * </li>
	 *
	 */
	deleteSelected : function(config){

		config = config || {};

		Ext.apply(config, this.deleteConfig);

		if(!config['deleteParam'])
		{
			config['deleteParam']='delete_keys';
		}

		//var selectedRows = this.selModel.selections.keys;

		var params={}
		params[config.deleteParam]=Ext.encode(this.selModel.selections.keys);

		var deleteItemsConfig = {
			store:this.store,
			params: params,
			count: this.selModel.selections.keys.length,
			extraWarning: config.extraWarning || "",
			noConfirmation: config.noConfirmation
		};

		if(config.callback)
		{
			deleteItemsConfig['callback']=config.callback;
		}
		if(config.success)
		{
			deleteItemsConfig['success']=config.success;
		}
		if(config.failure)
		{
			deleteItemsConfig['failure']=config.failure;
		}
		if(config.scope)
		{
			deleteItemsConfig['scope']=config.scope;
		}

		this.getView().scrollToTopOnLoad=false;
		GO.deleteItems(deleteItemsConfig);
		
		this.changed=true;
	},

	getGridData : function(){

		var data = [];
		var record;

		for (var i = 0; i < this.store.data.items.length;  i++)
		{
			var r = this.store.data.items[i].data;

			record={};

			for(var key in r)
			{
				record[key]=r[key];
			}
			data.push(record);
		}

		return data;
	},

	numberRenderer : function(v)
	{
		return GO.util.numberFormat(v);
	},
	
	btnAdd : function(){
		if(this.editDialogClass){
			this.showEditDialog();
		}
	},
	
	dblClick : function(grid, record, rowIndex){
		if(this.editDialogClass){
			this.showEditDialog(record.id);
		}
	},
	
	showEditDialog : function(id){
		if(!this.editDialog){
			this.editDialog = new this.editDialogClass;

			this.editDialog.on('save', function(){   
				this.store.reload();   
				this.changed=true;
			}, this);	
		}
	
		if(this.relatedGridParamName)
			this.editDialog.formPanel.baseParams[this.relatedGridParamName]=this.store.baseParams[this.relatedGridParamName];
		
		this.editDialog.show(id);	  
	}
	
});


GO.grid.EditorGridPanel = function(config)
{
	if(!config)
	{
		config={};
	}

	if(!config.keys)
	{
		config.keys=[];
	}

	if(!config.store)
	{
		config.store=config.ds;
	}

	config.keys.push({
		key: Ext.EventObject.DELETE,
		fn: function(key, e){
			//sometimes there's a search input in the grid, so dont delete when focus is on an input
			if(e.target.tagName!='INPUT')
				this.deleteSelected(this.deleteConfig);
		},
		scope:this
	});

	if(config.paging)
	{
		if(!config.bbar)
		{
			config.bbar = new Ext.PagingToolbar({
				cls: 'go-paging-tb',
				store: config.store,
				pageSize: parseInt(GO.settings['max_rows_list']),
				displayInfo: true,
				displayMsg: GO.lang['displayingItems'],
				emptyMsg: GO.lang['strNoItems']
			});
		}

		if(!config.store.baseParams)
		{
			config.store.baseParams={};
		}
		config.store.baseParams['limit']=parseInt(GO.settings['max_rows_list']);
	}

	GO.grid.EditorGridPanel.superclass.constructor.call(this, config);

	this.addEvents({
		delayedrowselect:true
	});

	this.on('rowclick', function(grid, rowIndex, e){
		if(!e.ctrlKey && !e.shiftKey)
		{
			var record = this.getSelectionModel().getSelected();
			this.fireEvent('delayedrowselect', this, rowIndex, record);
		}
		this.rowClicked=true;
	}, this);

	this.getSelectionModel().on("rowselect",function(sm, rowIndex, r){
		if(!this.rowClicked)
		{
			var record = this.getSelectionModel().getSelected();
			if(record==r)
			{
				this.fireEvent('delayedrowselect', this, rowIndex, r);
			}
		}
		this.rowClicked=false;
	}, this, {
		delay:250
	});
}

Ext.extend(GO.grid.EditorGridPanel, Ext.grid.EditorGridPanel, {

	deleteConfig : {},

	/**
	 * @cfg {Boolean} paging True to set the store's limit parameter and render a bottom
	 * paging toolbar.
	 */

	paging : false,

	/**
	 * Sends a delete request to the remote store. It will send the selected keys in json
	 * format as a parameter. (delete_keys by default.)
	 *
	 * @param {Object} options An object which may contain the following properties:<ul>
     * <li><b>deleteParam</b> : String (Optional)<p style="margin-left:1em">The name of the
     * parameter that will send to the store that holds the selected keys in JSON format.
     * Defaults to "delete_keys"</p>
     * </li>
	 *
	 */
	deleteSelected : GO.grid.GridPanel.prototype.deleteSelected,

	getGridData : GO.grid.GridPanel.prototype.getGridData,

	numberRenderer : GO.grid.GridPanel.prototype.numberRenderer,

	/**
	 * Checks if a grid cell is valid
	 * @param {Integer} col Cell column index
	 * @param {Integer} row Cell row index
	 * @return {Boolean} true = valid, false = invalid
	 */
	isCellValid:function(col, row) {
		if(!this.colModel.isCellEditable(col, row)) {
			return true;
		}
		var ed = this.colModel.getCellEditor(col, row);
		if(!ed) {
			return true;
		}
		var record = this.store.getAt(row);
		if(!record) {
			return true;
		}
		var field = this.colModel.getDataIndex(col);
		ed.field.setValue(record.data[field]);
		return ed.field.isValid(true);
	} // end of function isCellValid

	/**
	 * Checks if grid has valid data
	 * @param {Boolean} editInvalid true to automatically start editing of the first invalid cell
	 * @return {Boolean} true = valid, false = invalid
	 */
	,
	isValid:function(editInvalid) {
		var cols = this.colModel.getColumnCount();
		var rows = this.store.getCount();
		var r, c;
		var valid = true;
		for(r = 0; r < rows; r++) {
			for(c = 0; c < cols; c++) {
				valid = this.isCellValid(c, r);
				if(!valid) {
					break;
				}
			}
			if(!valid) {
				break;
			}
		}
		if(editInvalid && !valid) {
			this.startEditing(r, c);
		}
		return valid;
	} // end of function isValid
});