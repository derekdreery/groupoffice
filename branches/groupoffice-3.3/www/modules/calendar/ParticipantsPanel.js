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
	name : 'available',
	type : 'string'
}, {
	name : 'status',
	type : 'string'
}

]);

GO.calendar.ParticipantsPanel = function(eventDialog, config) {

	this.eventDialog = eventDialog;

	if (!config) {
		config = {};
	}

	/*if (GO.email) {
		tbar.push({
			iconCls : 'btn-invite',
			text : GO.calendar.lang.sendInvitation,
			cls : 'x-btn-text-icon',
			handler : function() {
				if (!GO.settings.modules.email) {
					Ext.Msg.alert(GO.lang.strError,
							GO.calendar.lang.emailSendingNotConfigured);
				} else {
					GO.email.Composer.show({
						loadUrl : GO.settings.modules.calendar.url + 'json.php',
						loadParams : {
							task : 'invitation',
							event_id : this.event_id
						},
						template_id : 0
					});
				}

			},
			scope : this
		});
	}*/

	config.hideMode = 'offsets';

	config.store = new GO.data.JsonStore({
		url : GO.settings.modules.calendar.url + 'json.php',
		baseParams : {
			task : "participants"
		},
		root : 'results',
		id : 'id',
		fields : ['id', 'name', 'email', 'available','status', 'user_id']
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


	this.inviteCheckbox = new Ext.form.Checkbox({
		name:'invitation',
		boxLabel:GO.calendar.lang.sendInvitation,
		hideLabel:true		
	})
	this.importCheckbox = new Ext.form.Checkbox({
		name:'import',
		boxLabel:GO.calendar.lang.importToCalendar,
		hideLabel:true		
	})
	
	this.checkPanel = new Ext.Panel({
		border : true,
		region:'north',
		height:40,
		layout:'column',
		defaults:{border:false,bodyStyle:'padding:5px'},
		items:[{
				columnWidth:.5,
				items:[this.inviteCheckbox]
			},{
				columnWidth:.5,
				items:[this.importCheckbox]
		}]
	});
	
	this.gridPanel = new GO.grid.GridPanel(
	{
		layout:'fit',
		split:true,
		store: config.store,
		border : true,
		region:'center',
		columns : [{
			header : GO.lang.strName,
			dataIndex : 'name',
			sortable : true
		}, {
			header : GO.lang.strEmail,
			dataIndex : 'email',
			sortable : true
		}, {
			header : GO.lang.strStatus,
			dataIndex : 'status',
			sortable : true,
			renderer : function(v) {
				switch (v) {
					case '2' :
						return GO.calendar.lang.declined;
						break;

					case '1' :
						return GO.calendar.lang.accepted;
						break;

					case '0' :
						return GO.calendar.lang.notRespondedYet;
						break;
				}
			}
		}, {
			header : GO.lang.strAvailable,
			dataIndex : 'available',
			sortable : false,
			renderer : function(v) {

				var className = 'img-unknown';
				switch (v) {
					case '1' :
						className = 'img-available';
						break;

					case '0' :
						className = 'img-unavailable';
						break;
				}

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
		layout : 'border',
		items:[this.checkPanel, this.gridPanel]
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
		this.inviteCheckbox.setValue(false);
		this.importCheckbox.setValue(false);

		if(this.isVisible()){
			this.store.reload();
		}
	},
	
	onShow : function() {
		if (!this.store.loaded) {
			if(this.store.baseParams.event_id > 0)
			{
				this.store.load();
			}else
			{
				this.addDefaultParticipant();
			}			
		}
		GO.calendar.ParticipantsPanel.superclass.onShow.call(this);
	},

	showAddParticipantsDialog : function() {
		if (!GO.addressbook) {
			var tpl = new Ext.XTemplate(GO.lang.moduleRequired);
			Ext.Msg.alert(GO.lang.strError, tpl.apply({
								module : GO.calendar.lang.addressbook
							}));
			return false;
		}
		if (!this.addParticipantsDialog) {
			this.addParticipantsDialog = new GO.dialog.SelectEmail({
				handler : function(grid, type) {
					if (grid.selModel.selections.keys.length > 0) {
						var selections = grid.selModel.getSelections();							

						var participants = [];

						for (var i = 0; i < selections.length; i++) {							
								participants.push(selections[i].get('email'));							
						}

						Ext.Ajax.request({
							url : GO.settings.modules.calendar.url + 'json.php',
							params : {
								task : 'check_availability',
								emails : participants.join(','),
								start_time : this.eventDialog.getStartDate()
										.format('U'),
								end_time : this.eventDialog.getEndDate()
										.format('U')
							},
							callback : function(options, success, response) {
								if (!success) {
									Ext.MessageBox.alert(GO.lang['strError'],
											GO.lang['strRequestError']);
								} else {
									var responseParams = Ext.decode(response.responseText);

									for (var i = 0; i < selections.length; i++) {
										var record = this.store.findBy(function(record, id){
											if(record.get('email')==selections[i].get('email'))
											{
												return true;
											}else
											{
												return false;
											}
										});
										
										if(record==-1){
											this.addParticipant({
												name : selections[i].get('name'),
												email : selections[i].get('email'),
												status : "0",
												user_id : type=='users' ? selections[i].get('id') : 0,
												available : responseParams[selections[i].get('email')]
											});
										}
									}
								}
							},
							scope : this
						});

					}
				},
				scope : this
			});
		}
		this.addParticipantsDialog.show();
	},
	
	addDefaultParticipant : function(){
		this.body.mask(GO.lang.waitMsgLoad);
		Ext.Ajax.request({
			url : GO.settings.modules.calendar.url + 'json.php',
			params : {
				task : 'get_default_participant',
				calendar_id : this.eventDialog.calendar_id,
				start_time : this.eventDialog.getStartDate().format('U'),
				end_time : this.eventDialog.getEndDate().format('U')
			},
			callback : function(options, success, response) {
				this.body.unmask();
				if (!success) {
					Ext.MessageBox.alert(GO.lang['strError'],
							GO.lang['strRequestError']);
				} else {
					var responseParams = Ext.decode(response.responseText);							
					this.addParticipant({
						name : responseParams.name,
						email : responseParams.email,
						status :  responseParams.status,
						user_id : responseParams.user_id,
						available : responseParams.available
					});
				}
			},
			scope : this
		});
	},
	
	addParticipant : function(config)
	{
		config.id='new_'+this.newId;
		var p = new GO.calendar.Participant(config);
		this.store.insert(this.store.getCount(), p);
		this.newId++;
		this.store.loaded=true;
	},
	
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
				var time = node.id.substr(4);

				var colonIndex = time.indexOf(':');

				var minutes = time.substr(colonIndex + 1);
				var hours = time.substr(0, colonIndex);

				var hourDiff = parseInt(d.endHour.getValue())
						- parseInt(d.startHour.getValue());
				var minDiff = parseInt(d.endMin.getValue())
						- parseInt(d.startMin.getValue());

				if (minDiff < 0) {
					minDiff += 60;
					hourDiff--;
				}

				minutes = minutes+"";
				if (minutes.length==1) {
					minutes = '0' + minutes;
				}

				d.startHour.setValue(hours);
				d.startMin.setValue(minutes);
				d.startDate.setValue(Date.parseDate(
						dataview.store.baseParams.date,
						GO.settings.date_format));

				var endHour = parseInt(hours) + hourDiff;
				var endMin = parseInt(minutes) + minDiff;
				
				if (endMin >= 60) {
					endMin -= 60;
					endHour++;
				}
				
				endMin = endMin+"";
				if (endMin.length==1) {
					endMin = '0' + endMin;
				}

				d.endHour.setValue(endHour);
				d.endMin.setValue(endMin);
				d.endDate.setValue(Date.parseDate(
						dataview.store.baseParams.date,
						GO.settings.date_format));

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