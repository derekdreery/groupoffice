GO.addressbook.SentMailingsGrid = function(config){
	
	if(!config)
	{
		config = {};
	}
	
	config.tbar = [{
				iconCls: 'ml-btn-mailings',
				text: GO.addressbook.lang.sendMailing,
				cls: 'x-btn-text-icon',
				handler: function(){
					if(!this.selectAddresslistWindow)
					{
						this.selectAddresslistWindow=new GO.addressbook.SelectAddresslistWindow();
						this.selectAddresslistWindow.on("select", function(win, addresslist_id){
							var composer = GO.email.showComposer({addresslist_id:addresslist_id});
							composer.on('hide', function(){
								this.store.load();
							}, this, {single:true});
						}, this);
					}
					this.selectAddresslistWindow.show();
				},
				scope: this
			},'-',{
			iconCls: 'btn-delete',
			text: GO.lang['cmdDelete'],
			cls: 'x-btn-text-icon',
			handler: function(){
				this.deleteSelected();
			},
			scope: this
		}];
	
	config.layout='fit';
	config.autoScroll=true;
	config.split=true;
	config.store = new GO.data.JsonStore({
	    url: GO.url('addressbook/sentMailing/store'),
	    baseParams: {
//	    	task: 'sent_mailings',
	    	addresslist_id: 0	    	
	    	},
	    root: 'results',
	    id: 'id',
	    totalProperty:'total',
	    fields: ['id', 'addresslist','subject','user_name', 'ctime','status','sent','total','errors', 'hide_pause', 'hide_play', 'message_path'],
	    remoteSort: true
	});

	var action = new Ext.ux.grid.RowActions({
		header:'',
		hideMode:'display',
		keepSelection:true,
		actions:[{
			iconCls:'ml-btn-view',
			qtip:GO.addressbook.lang.viewMessage
		},{
			iconCls:'ml-btn-view-log',
			qtip:GO.addressbook.lang.viewLog
		},{
			iconCls:'ml-btn-pause',
			hideIndex:'hide_pause',
			qtip:GO.addressbook.lang.pauseMailing
		},{
			iconCls:'ml-btn-play',
			hideIndex:'hide_play',
			qtip:GO.addressbook.lang.resumeMailing
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
					GO.linkHandlers["GO_Savemailas_Model_LinkedEmail"].call(this, 0, {action: "path", path:record.get('message_path')});
					break;
				case 'ml-btn-view-log':
					window.open(GO.url("addressbook/sentMailing/viewLog",{'mailing_id': record.id}));
					break;
			}
		}
	});

	config.plugins=action;
	
	config.paging=true;
	var columnModel =  new Ext.grid.ColumnModel({
		defaults:{
			sortable:false
		},
		columns:[
	   		{
			header: GO.addressbook.lang.addresslist,
			dataIndex: 'addresslist'
		},	{
			header: GO.addressbook.lang.subject, 
			dataIndex: 'subject'
		},	{
			header: GO.lang.strOwner, 
			dataIndex: 'user_name'
		},	{
			header: GO.lang.strCtime, 
			dataIndex: 'ctime'
		},		{
			header: GO.addressbook.lang['status'], 
			dataIndex: 'status',
			renderer:function(v){
				return GO.addressbook.lang.mailingStatus[v];
			}
		},		{
			header: GO.addressbook.lang.sent, 
			dataIndex: 'sent',
			align:'center',
			width:60
		},		{
			header: GO.addressbook.lang.total, 
			dataIndex: 'total',
			align:'center',
			width:60
		},		{
			header: GO.addressbook.lang.errors, 
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
	config.loadMask=false;

	
	GO.addressbook.SentMailingsGrid.superclass.constructor.call(this, config);
	
};

Ext.extend(GO.addressbook.SentMailingsGrid, GO.grid.GridPanel,{
	setMailingId : function(addresslist_id){
		this.store.baseParams.addresslist_id=addresslist_id;
		this.store.loaded=false;
	}
});