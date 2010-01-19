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

GO.calendar.SummaryGroupPanel = function(config)
{
	if(!config)
	{
		config = {};
	}
	
	config.store = new Ext.data.GroupingStore({
		reader: new Ext.data.JsonReader({
			totalProperty: "count",
			root: "results",
			id: "event_id",
			fields: [
			'id',
			'event_id',
			'name',
			'time',
			'start_time',
			'end_time',
			'description',
			'location',
			'private',
			'repeats',
			'day',
			'calendar_name'
			]
		}),
		baseParams: {
			task:'summary',
			'user_id' : GO.settings.user_id,
			'portlet' : true
		},
		proxy: new Ext.data.HttpProxy({
			url: GO.settings.modules.calendar.url+'json.php'
		}),
		groupField:'day',
		sortInfo: {
			field: 'start_time',
			direction: 'ASC'
		},
		remoteGroup:true,
		remoteSort:true
	});

	config.store.on('load', function(){
		//do layout on Startpage
		this.ownerCt.ownerCt.ownerCt.doLayout();		
	}, this);
	  
	/*config.store = new Ext.data.JsonStore({
      totalProperty: "count",
	    root: "results",
	    id: "id",
	    fields: [
				'id',
				'event_id',
				'name',
				'start_time',
				'end_time',
				'tooltip',
				'private',
				'repeats',
				'day'			
			],
			baseParams: {task:'summary'},
			url: GO.settings.modules.calendar.url+'json.php'
		  
	  });*/
	
	
	config.paging=false,			
	config.autoExpandColumn='summary-calendar-name-heading';
	//config.enableColumnHide=false;
	//config.enableColumnMove=false;

	config.columns=[
	{
		header:GO.lang.strDay,
		dataIndex: 'day'
	},
	{
		header:GO.lang.strTime,
		dataIndex: 'time',
		width:100,
		align:'right',
		groupable:false
	},
	{
		id:'summary-calendar-name-heading',
		header:GO.lang.strName,
		dataIndex: 'name',
		renderer:function(value, p, record){
			p.attr = 'ext:qtip="'+Ext.util.Format.htmlEncode(GO.calendar.formatQtip(record.data))+'"';
			return value;
		},
		groupable:false
	},{
		header:GO.calendar.lang.calendar,
		dataIndex: 'calendar_name',
		width:140
	}];
		
	config.view=  new Ext.grid.GroupingView({
		scrollOffset: 2,
		hideGroupedColumn:true,
		groupTextTpl: '{text} ({[values.rs.length]} {[values.rs.length > 1 ? "'+GO.lang.items+'" : "'+GO.lang.item+'"]})',
		emptyText: GO.calendar.lang.noAppointmentsToDisplay,
		showGroupName:false
	});
	config.sm=new Ext.grid.RowSelectionModel();
	config.loadMask=true;
	config.autoHeight=true;
	
	GO.calendar.SummaryGroupPanel.superclass.constructor.call(this, config);

//with auto expand column this works better otherwise you'll get a big scrollbar
/*this.store.on('load', function(){
		this.addClass('go-grid3-hide-headers');
	}, this, {single:true})*/
	
};


Ext.extend(GO.calendar.SummaryGroupPanel, Ext.grid.GridPanel, {
	
		
	afterRender : function()
	{
		GO.calendar.SummaryGroupPanel.superclass.afterRender.call(this);
    
		GO.calendar.eventDialog.on('save', function(){
			this.store.reload()
			}, this);

		this.on("rowdblclick", function(grid, rowClicked, e){
			if(grid.selModel.selections.items[0].json.contact_id)
			{
				GO.linkHandlers[2].call(this, grid.selModel.selections.items[0].json.contact_id);
			}else
			{
				var event_id = grid.selModel.selections.keys[0];
				GO.calendar.eventDialog.show({
					event_id: event_id
				});
			}
		}, this);
		
		Ext.TaskMgr.start({
			run: this.store.load,
			scope:this.store,
			interval:900000
		});  
	}
	
});

GO.mainLayout.onReady(function(){
	
	if(GO.summary)
	{
		var calGrid = new GO.calendar.SummaryGroupPanel({
			id: 'summary-calendar-grid'
		});
		
		GO.summary.portlets['portlet-calendar']=new GO.summary.Portlet({
			id: 'portlet-calendar',
			//iconCls: 'go-module-icon-calendar',
			title: GO.calendar.lang.appointments,
			layout:'fit',
			tools: [{
				id: 'gear',
				handler: function(){
					if(!this.manageCalsWindow)
					{
						this.manageCalsWindow = new Ext.Window({
							layout:'fit',
							items:this.PortletSettings =  new GO.calendar.PortletSettings(),
							width:700,
							height:400,
							title:GO.calendar.lang.visibleCalendars,
							closeAction:'hide',
							buttons:[{
								text: GO.lang.cmdSave,
								handler: function(){
									var params={
										'task' : 'save_portlet'
									};
									if(this.PortletSettings.store.loaded){
										params['calendars']=Ext.encode(this.PortletSettings.getGridData());
									}
									Ext.Ajax.request({
										url: GO.settings.modules.calendar.url+'action.php',
										params: params,
										callback: function(options, success, response){
											if(!success)
											{
												Ext.MessageBox.alert(GO.lang['strError'], GO.lang['strRequestError']);
											}else
											{
												//var responseParams = Ext.decode(response.responseText);
												this.PortletSettings.store.reload();
												this.manageCalsWindow.hide();

												calGrid.store.reload();
											}
										},
										scope:this
									});
								},
								scope: this
							}],
							listeners:{
								show: function(){
									if(!this.PortletSettings.store.loaded)
									{
										this.PortletSettings.store.load();
									}
								},
								scope:this
							}
						});
					}
					this.manageCalsWindow.show();
				}
			},{
				id:'close',
				handler: function(e, target, panel){
					panel.removePortlet();
				}
			}],
			items: calGrid,
			autoHeight:true
			
		});
	}
});