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
 
GO.postfixadmin.VacationConfigsGrid = function(config){
	
	if(!config)
	{
		config = {};
	}
	
	config.title = GO.postfixadmin.lang.vacationConfigs;
	config.layout='fit';
	config.autoScroll=true;
	config.split=true;
	config.store = new GO.data.JsonStore({
	    url: GO.settings.modules.postfixadmin.url+ 'json.php',
	    baseParams: {
	    	task: 'vacation_configs'
	    	},
	    root: 'results',
	    id: 'id',
	    totalProperty:'total',
	    fields: ['email','subject','body','cache','domain','ctime','active'],
	    remoteSort: true
	});
	
	config.paging=true;
	var columnModel =  new Ext.grid.ColumnModel([
	   		{
			header: GO.postfixadmin.lang.email, 
			dataIndex: 'email'
		},		{
			header: GO.postfixadmin.lang.subject, 
			dataIndex: 'subject'
		},		{
			header: GO.postfixadmin.lang.body, 
			dataIndex: 'body'
		},		{
			header: GO.postfixadmin.lang.cache, 
			dataIndex: 'cache'
		},		{
			header: GO.postfixadmin.lang.domain, 
			dataIndex: 'domain'
		},		{
			header: GO.lang.strCtime, 
			dataIndex: 'ctime'
		},		{
			header: GO.postfixadmin.lang.active, 
			dataIndex: 'active'
		}
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
	
	
	this.vacationConfigDialog = new GO.postfixadmin.VacationConfigDialog();
	    			    		
		this.vacationConfigDialog.on('save', function(){   
			this.store.reload();	    			    			
		}, this);
	
	
	config.tbar=[{
			iconCls: 'btn-add',							
			text: GO.lang['cmdAdd'],
			cls: 'x-btn-text-icon',
			handler: function(){
				
	    	this.vacationConfigDialog.show();
	    	
	    	
	    	
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
	
	
	
	GO.postfixadmin.VacationConfigsGrid.superclass.constructor.call(this, config);
	
	this.on('rowdblclick', function(grid, rowIndex){
		var record = grid.getStore().getAt(rowIndex);	
		
		this.vacationConfigDialog.show(record.data.id);
		
		}, this);
	
};

Ext.extend(GO.postfixadmin.VacationConfigsGrid, GO.grid.GridPanel,{
	
});