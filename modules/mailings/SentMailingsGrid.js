GO.mailings.SentMailingsGrid = function(config){
	
	if(!config)
	{
		config = {};
	}
	
	
	config.layout='fit';
	config.autoScroll=true;
	config.split=true;
	config.store = new GO.data.JsonStore({
	    url: GO.settings.modules.mailings.url+ 'json.php',
	    baseParams: {
	    	task: 'sent_mailings',
	    	mailing_group_id: 0	    	
	    	},
	    root: 'results',
	    id: 'id',
	    totalProperty:'total',
	    fields: ['id', 'mailing_group','subject','user_name', 'ctime','status','sent','total','errors', 'hide_pause', 'hide_play', 'message_path'],
	    remoteSort: true
	});

	var action = new Ext.ux.grid.RowActions({
		header:'',
		hideMode:'display',
		keepSelection:true,
		actions:[{
			iconCls:'ml-btn-view',
			qtip:GO.mailings.lang.viewMessage
		},{
			iconCls:'ml-btn-view-log',
			qtip:GO.mailings.lang.viewLog
		},{
			iconCls:'ml-btn-pause',
			hideIndex:'hide_pause',
			qtip:GO.mailings.lang.pauseMailing
		},{
			iconCls:'ml-btn-play',
			hideIndex:'hide_play',
			qtip:GO.mailings.lang.resumeMailing
		}],
		width: 50
	});

	action.on({
		action:function(grid, record, action, row, col) {

			switch(action){
				case 'ml-btn-pause':
					grid.store.baseParams.pause_mailing_id=record.id;
					grid.store.load();
					delete grid.store.baseParams.pause_mailing_id;
					
					break;
				case 'ml-btn-play':
					grid.store.baseParams.start_mailing_id=record.id;
					grid.store.load();
					delete grid.store.baseParams.start_mailing_id;

					break;
				case 'ml-btn-view':
					GO.linkHandlers[9].call(this, 0, {path:record.get('message_path')});
					break;
				case 'ml-btn-view-log':
					document.location=GO.settings.modules.mailings.url+'log.php?mailing_id='+record.id;
					break;
			}
		}
	});

	config.plugins=action;
	
	config.paging=true;
	var columnModel =  new Ext.grid.ColumnModel({
		defaults:{
			sortable:true
		},
		columns:[
	   		{
			header: GO.mailings.lang.addresslist,
			dataIndex: 'mailing_group'
		},	{
			header: GO.mailings.lang.subject, 
			dataIndex: 'subject'
		},	{
			header: GO.lang.strOwner, 
			dataIndex: 'user_name'
		},	{
			header: GO.lang.strCtime, 
			dataIndex: 'ctime'
		},		{
			header: GO.mailings.lang['status'], 
			dataIndex: 'status'
		},		{
			header: GO.mailings.lang.sent, 
			dataIndex: 'sent',
			align:'center',
			width:60
		},		{
			header: GO.mailings.lang.total, 
			dataIndex: 'total',
			align:'center',
			width:60
		},		{
			header: GO.mailings.lang.errors, 
			dataIndex: 'errors',
			align:'center',
			width:60
		},
		action
	]
});
	
	config.cm=columnModel;
	
	config.view=new Ext.grid.GridView({
		autoFill: true,
		forceFit: true,
		emptyText: GO.lang['strNoItems']		
	}),
	config.sm=new Ext.grid.RowSelectionModel();
	config.loadMask=true;

	
	GO.mailings.SentMailingsGrid.superclass.constructor.call(this, config);
	
};

Ext.extend(GO.mailings.SentMailingsGrid, GO.grid.GridPanel,{
	setMailingId : function(mailing_group_id){
		this.store.baseParams.mailing_group_id=mailing_group_id;
		this.store.loaded=false;
	},
	afterRender : function(){
		GO.mailings.SentMailingsGrid.superclass.afterRender.call(this);
		this.store.load();
	}
});