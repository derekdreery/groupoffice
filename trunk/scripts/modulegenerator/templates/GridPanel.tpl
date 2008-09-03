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
 
GO.{module}.{friendly_multiple_ucfirst}Grid = function(config){
	
	if(!config)
	{
		config = {};
	}
	
	config.title = GO.{module}.lang.{friendly_multiple_js};
	config.layout='fit';
	config.autoScroll=true;
	config.split=true;
	config.store = new GO.data.JsonStore({
	    url: GO.settings.modules.{module}.url+ 'json.php',
	    baseParams: {
	    	task: '{friendly_multiple}'<gotpl if="$relation">,
	    	{related_field_id}: 0
	    	</gotpl>
	    	},
	    root: 'results',
	    id: 'id',
	    totalProperty:'total',
	    fields: [{STOREFIELDS}],
	    remoteSort: true
	});

	
	<gotpl if="$paging">config.paging=true;</gotpl>

	var columnModel =  new Ext.grid.ColumnModel([
	   {COLUMNS}
	]);
	columnModel.defaultSortable = true;
	config.cm=columnModel;
	
	config.view=new Ext.grid.GridView({
		autoFill: true,
		forceFit: true,
		emptyText: GO.lang['strNoItems']		
	});
	config.sm=new Ext.grid.RowSelectionModel();
	config.loadMask=true;
	
	
	<gotpl if="!$link_type">this.{friendly_single_js}Dialog = new GO.{module}.{friendly_single_ucfirst}Dialog();
	    			    		
		this.{friendly_single_js}Dialog.on('save', function(){   
			this.store.reload();	    			    			
		}, this);</gotpl><gotpl if="$link_type &gt; 0">	    			    		
		GO.{module}.{friendly_single}Dialog.on('save', function(){   
			this.store.reload();	    			    			
		}, this);</gotpl>
	
	
	config.tbar=[{
			iconCls: 'btn-add',							
			text: GO.lang['cmdAdd'],
			cls: 'x-btn-text-icon',
			handler: function(){
				<gotpl if="!$link_type">
	    	this.{friendly_single_js}Dialog.show();<gotpl if="$relation">
	    	this.{friendly_single_js}Dialog.formPanel.form.setValues({{related_field_id}: this.store.baseParams.{related_field_id}});</gotpl>
	    	</gotpl>
	    	<gotpl if="$link_type &gt; 0">
	    	GO.{module}.{friendly_single_js}Dialog.show();<gotpl if="$relation">
	    	GO.{module}.{friendly_single_js}Dialog.formPanel.form.setValues({{related_field_id}: this.store.baseParams.{related_field_id}});</gotpl>
	    	</gotpl>
	    	
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
		}];
	
	
	
	GO.{module}.{friendly_multiple_ucfirst}Grid.superclass.constructor.call(this, config);
	
	this.on('rowdblclick', function(grid, rowIndex){
		var record = grid.getStore().getAt(rowIndex);	
		
		<gotpl if="!$link_type">this.{friendly_single_js}Dialog.show(record.data.id);</gotpl>
		<gotpl if="$link_type &gt; 0">GO.{module}.{friendly_single_js}Dialog.show(record.data.id);</gotpl>
		}, this);
	
};


Ext.extend(GO.{module}.{friendly_multiple_ucfirst}Grid, GO.grid.GridPanel,{
	<gotpl if="$autoload">
	loaded : false,
	
	afterRender : function()
	{
		GO.{module}.{friendly_multiple_ucfirst}Grid.superclass.afterRender.call(this);
		
		if(this.isVisible())
		{
			this.onGridShow();
		}
	},
	
	onGridShow : function(){
		if(!this.loaded && this.rendered)
		{
			this.store.load();
			this.loaded=true;
		}
	}
	</gotpl>
});