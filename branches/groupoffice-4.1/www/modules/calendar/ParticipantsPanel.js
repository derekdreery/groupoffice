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

GO.calendar.Participant = Ext.data.Record.create([
// the "name" below matches the tag name to read, except "availDate"
// which is mapped to the tag "availability"
{
	name : 'id',
	type : 'string'
}, {
	name : 'name',
	type : 'string'
}, {
	name : 'email',
	type : 'string'
}, {
	name : 'create_permission',
	type : 'string'
},{
	name : 'available',
	type : 'string'
}, {
	name : 'status',
	type : 'string'
}, {
	name : 'is_organizer',
	type : 'int'
}

]);

GO.calendar.ParticipantsPanel = function(eventDialog, config) {

	this.eventDialog = eventDialog;

	if (!config) {
		config = {};
	}

	config.hideMode = 'offsets';

	config.store = new GO.data.JsonStore({
		url : GO.url('calendar/participant/store'),
		baseParams : {
			task : "participants"
		},
		fields : ['id', 'name', 'email', 'available','status', 'user_id', 'is_organizer','create_permission']
	});
		
	var tbar = [{
		iconCls : 'btn-add',
		text : GO.lang.cmdAdd,
		cls : 'x-btn-text-icon',
		handler : function() {
			this.showAddParticipantsDialog();
		},
		scope : this
	}, {
		iconCls : 'btn-delete',
		text : GO.lang.cmdDelete,
		cls : 'x-btn-text-icon',
		handler : function() {
			var selectedRows = this.gridPanel.selModel.getSelections();
			for (var i = 0; i < selectedRows.length; i++) {
				selectedRows[i].commit();
				
				if(selectedRows[i].data.is_organizer){
					alert(GO.calendar.lang.cantRemoveOrganizer);
					return;
				}
				
				this.store.remove(selectedRows[i]);
			}
		},
		scope : this
	}, {
		iconCls : 'btn-availability',
		text : GO.calendar.lang.checkAvailability,
		cls : 'x-btn-text-icon',
		handler : function() {
			this.checkAvailability();
		},
		scope : this
	}];


	/*
	this.inviteCheckbox = new Ext.form.Checkbox({
		name:'invitation',
		boxLabel:GO.calendar.lang.sendInvitation,
		hideLabel:true		
	})
	*/
//	this.importCheckbox = new Ext.form.Checkbox({
//		name:'add_to_participant_calendars',
//		boxLabel:GO.calendar.lang.importToCalendar,
//		hideLabel:true		
//	})
	
//	this.checkPanel = new Ext.Panel({
//		border : true,
//		region:'north',
//		height:40,
//		layout:'column',
//		defaults:{
//			border:false,
//			bodyStyle:'padding:5px'
//		},
//		items:[{
//			columnWidth:.5,
//			items:[this.importCheckbox]
//		}]
//	});
	
	this.gridPanel = new GO.grid.GridPanel(
	{
		layout:'fit',
		split:true,
		store: config.store,		
//		region:'center',
		columns : [{
			header : GO.lang.strName,
			dataIndex : 'name'
		}, {
			header : GO.lang.strEmail,
			dataIndex : 'email'
		}, {
			header : GO.lang.strStatus,
			dataIndex : 'status',
			renderer : function(v) {
				switch (v) {
					case 'TENTATIVE' :
						return GO.calendar.lang.tentative;
						break;
						
					case 'DECLINED' :
						return GO.calendar.lang.declined;
						break;

					case 'ACCEPTED' :
						return GO.calendar.lang.accepted;
						break;

					case 'NEEDS-ACTION' :
						return GO.calendar.lang.notRespondedYet;
						break;
				}
			}
		}, {
			header : GO.lang.strAvailable,
			dataIndex : 'available',
			renderer : function(v) {

				var className = 'img-unknown';
				if(v!='?')
					className = v ? 'img-available' : 'img-unavailable';
				
				return '<div class="' + className + '"></div>';
			}
		}, {
			header : "Create permission",
			dataIndex : 'create_permission',
			renderer : function(v) {

				var className = v ? 'img-available' : 'img-unavailable';
				
				return '<div class="' + className + '"></div>';
			}
		}, {
			header : GO.calendar.lang.isOrganizer,
			dataIndex : 'is_organizer',
			renderer : function(v) {
				var className = v ? 'img-available' : 'img-unavailable';		

				return '<div class="' + className + '"></div>';
			}
		}],
		view : new Ext.grid.GridView({
			autoFill : true,
			forceFit : true
		}),
		loadMask : {
			msg : GO.lang.waitMsgLoad
		},
		sm : new Ext.grid.RowSelectionModel()
	});
		
	
	Ext.apply(config, {
		title : GO.calendar.lang.participants,
		border : false,
		tbar:tbar,
		layout : 'fit',
		items:[this.gridPanel]
//		items:[this.checkPanel, this.gridPanel]
	});

	config.store.setDefaultSort('name', 'ASC');

	GO.calendar.ParticipantsPanel.superclass.constructor.call(this, config);

};

Ext.extend(GO.calendar.ParticipantsPanel, Ext.Panel, {

	event_id : 0,
	
	newId: 0,
	
	loaded : false,

	/*
	 * afterRender : function() {
	 * GO.calendar.ParticipantsPanel.superclass.afterRender.call(this);
	 * 
	 * if(this.store.baseParams.package_id>0) { this.store.load(); }
	 * this.loaded=true; },
	 */

	getGridData : function(){
		return this.gridPanel.getGridData();
	},
	
	setEventId : function(event_id) {
		this.event_id = this.store.baseParams.event_id = event_id;
		this.store.loaded = false;
		if(this.event_id==0)
		{
			this.store.removeAll();
		}
		this.newId=0;		
		//this.inviteCheckbox.setValue(false);
//		this.importCheckbox.setValue(false);

//		if(this.isVisible()){
//			this.store.reload();
//		}
	},
	
//	onShow : function() {
//		if (!this.store.loaded) {
//			if(this.store.baseParams.event_id > 0)
//			{
//				this.store.load();
//			}else
//			{
//				this.addDefaultParticipant();
//			}			
//		}
//		GO.calendar.ParticipantsPanel.superclass.onShow.call(this);
//	},
	
	invitationRequired : function(){
		//invitation is required if there's a participant that is not the current user.
		
		if(this.store.getCount()>1)
			return true;
		
		var records = this.store.getRange();
		for(var i=0;i<records.length;i++)
		{
			if(!records[i].data.is_organizer)
				return true;
		}
	
		return false;
		
	},

	showAddParticipantsDialog : function() {
		/*if (!GO.addressbook) {
			var tpl = new Ext.XTemplate(GO.lang.moduleRequired);
			Ext.Msg.alert(GO.lang.strError, tpl.apply({
				module : GO.calendar.lang.addressbook
			}));
			return false;
		}*/
		if (!this.addParticipantsDialog) {
			this.addParticipantsDialog = new GO.dialog.SelectEmail({
				handler : function(grid, type) {
				
					if (grid.selModel.selections.keys.length > 0) {

						var selections = grid.selModel.getSelections();			
						
						var ids=[];
						for (var i=0; i<selections.length; i++) {
							ids.push(selections[i].data.id);
						}
						switch(type){
							
							case 'users':								
								

								GO.request({
									url:"calendar/participant/getUsers",
									params:{
										users: Ext.encode(ids),
										start_time : this.eventDialog.getStartDate().format('U'),
										end_time : this.eventDialog.getEndDate().format('U')
									},
									success:function(response, options, result){
										this.store.loadData(result, true);
									},
									scope:this
								});								
							break;
							
							case 'contacts':
								GO.request({
									url:"calendar/participant/getContacts",
									params:{
										contacts: Ext.encode(ids),
										start_time : this.eventDialog.getStartDate().format('U'),
										end_time : this.eventDialog.getEndDate().format('U')
									},
									success:function(response, options, result){
										this.store.loadData(result, true);
									},
									scope:this
								});	
								break;
								
							case 'companies':
								GO.request({
									url:"calendar/participant/getCompanies",
									params:{
										companies: Ext.encode(ids),
										start_time : this.eventDialog.getStartDate().format('U'),
										end_time : this.eventDialog.getEndDate().format('U')
									},
									success:function(response, options, result){
										this.store.loadData(result, true);
									},
									scope:this
								});	
								break;
								
							case 'mailings':
								GO.request({
									url:"calendar/participant/getAddresslists",
									params:{
										addresslists: Ext.encode(ids),
										start_time : this.eventDialog.getStartDate().format('U'),
										end_time : this.eventDialog.getEndDate().format('U')
									},
									success:function(response, options, result){
										this.store.loadData(result, true);
									},
									scope:this
								});	
								break;							
								
							case 'usergroups':
								GO.request({
									url:"calendar/participant/getUserGroups",
									params:{
										groups: Ext.encode(ids),
										start_time : this.eventDialog.getStartDate().format('U'),
										end_time : this.eventDialog.getEndDate().format('U')
									},
									success:function(response, options, result){
										this.store.loadData(result, true);
									},
									scope:this
								});				
								
								break;
							
						}
					
					}
				},
				scope : this
			});
		}
		this.addParticipantsDialog.show();
	},
	
//	addDefaultParticipant : function(){
//				
//		GO.request({
//			maskEl:this.body,
//			url :'calendar/participant/loadOrganizer',
//			params : {
//				calendar_id : this.eventDialog.selectCalendar.getValue(),
//				start_time : this.eventDialog.getStartDate().format('U'),
//				end_time : this.eventDialog.getEndDate().format('U')
//			},
//			success : function(options, response, result) {
//			
//				this.addParticipant({
//					name : result.name,
//					email : result.email,
//					status :  result.status,
//					user_id : result.user_id,
//					available : result.available,
//					is_organizer : result.is_organizer
//				});
//				
//			},
//			scope : this
//		});
//	},
//	
//	addParticipant : function(config)
//	{
//		config.id='new_'+this.newId;
//		var p = new GO.calendar.Participant(config);
//		this.store.insert(this.store.getCount(), p);
//		this.newId++;
//		this.store.loaded=true;
//	},
	
	reloadAvailability : function(){
		
		var selections = this.store.getRange();
		if(selections.length)
		{
			var participants = [];
			for (var i = 0; i < selections.length; i++) {
				participants.push(selections[i].get('email'));
			}
			
			Ext.Ajax.request({
				url : GO.settings.modules.calendar.url + 'json.php',
				params : {
					task : 'check_availability',
					emails : participants.join(','),
					start_time : this.eventDialog.getStartDate().format('U'),
					end_time : this.eventDialog.getEndDate().format('U')
				},
				callback : function(options, success, response) {
					if (!success) {
						Ext.MessageBox.alert(GO.lang['strError'],
							GO.lang['strRequestError']);
					} else {
						var responseParams = Ext.decode(response.responseText);
	
						for (var i = 0; i < selections.length; i++) {
							selections[i].set('available', responseParams[selections[i].get('email')]);
								
						}
						this.store.commitChanges();
					}
				},
				scope : this
			});
		}
	},
	
	checkAvailability : function() {
		if (!this.availabilityWindow) {
			this.availabilityWindow = new GO.calendar.AvailabilityCheckWindow();
			this.availabilityWindow.on('select', function(dataview, index, node) {
				var d = this.eventDialog;				
				d.startDate.setValue(Date.parseDate(
					dataview.store.baseParams.date,
					GO.settings.date_format));
				d.endDate.setValue(Date.parseDate(
					dataview.store.baseParams.date,
					GO.settings.date_format));
					
				var oldStartTime = Date.parseDate(d.startTime.getValue(), GO.settings.time_format);
				var oldEndTime = Date.parseDate(d.endTime.getValue(), GO.settings.time_format);
				var elapsed = oldEndTime.getElapsed(oldStartTime);


				var time = Date.parseDate(node.id.substr(4), 'G:i');
				d.startTime.setValue(time.format(GO.settings.time_format));
				d.endTime.setValue(time.add(Date.MILLI, elapsed).format(GO.settings.time_format));
				
				d.tabPanel.setActiveTab(0);
				this.reloadAvailability();
				this.availabilityWindow.hide();
			}, this);
		}
		var records = this.store.getRange();
		var emails = [];
		var names = [];
		for (var i = 0; i < records.length; i++) {
			emails.push(records[i].get('email'));
			names.push(records[i].get('name'));
		}
		this.availabilityWindow.show({
			date : this.eventDialog.startDate.getRawValue(),
			event_id : this.event_id,
			emails : Ext.encode(emails),
			names : Ext.encode(names)
		});
	}

});