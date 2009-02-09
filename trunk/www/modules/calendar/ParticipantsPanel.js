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
					var selectedRows = this.selModel.getSelections();
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

	if (GO.email) {
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
	}

	Ext.apply(config, {
				title : GO.calendar.lang.participants,
				store : new GO.data.JsonStore({
							url : GO.settings.modules.calendar.url + 'json.php',
							baseParams : {
								task : "participants"
							},
							root : 'results',
							id : 'id',
							fields : ['id', 'name', 'email', 'available','status']
						}),
				border : false,
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
				sm : new Ext.grid.RowSelectionModel({}),
				// paging:true,
				layout : 'fit',
				tbar : tbar

			});

	config.store.setDefaultSort('name', 'ASC');

	GO.calendar.ParticipantsPanel.superclass.constructor.call(this, config);

};

Ext.extend(GO.calendar.ParticipantsPanel, GO.grid.GridPanel, {

	loaded : false,

	/*
	 * afterRender : function() {
	 * GO.calendar.ParticipantsPanel.superclass.afterRender.call(this);
	 * 
	 * if(this.store.baseParams.package_id>0) { this.store.load(); }
	 * this.loaded=true; },
	 */

	setEventId : function(event_id) {
		this.store.baseParams.event_id = event_id;
		this.loaded = false;
	},

	onShow : function() {
		if (!this.loaded && this.store.baseParams.event_id > 0) {
			this.store.load();
		}
		this.loaded = true;
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
				handler : function(grid) {
					if (grid.selModel.selections.keys.length > 0) {
						var selections = grid.selModel.getSelections();

						if (!this.newId)
							this.newId = 0;
						else
							this.newId++;

						var participants = [];
						
						for (var i = 0; i < selections.length; i++) {
							participants.push(selections[i].get('email'));							
						}
						
						Ext.Ajax.request({
								url : GO.settings.modules.calendar.url
										+ 'json.php',
								params : {
									task : 'check_availability',
									emails : participants.join(','),
									start_time : this.eventDialog.getStartDate().format('U'),
									end_time : this.eventDialog.getEndDate().format('U')
								},
								callback : function(options, success, response) {
									if (!success) {
										Ext.MessageBox.alert(
												GO.lang['strError'],
												GO.lang['strRequestError']);
									} else {
										var responseParams = GO.decode(response.responseText);

										for (var i = 0; i < selections.length; i++) {
											var p = new GO.calendar.Participant({
												id : 0,
												name : selections[i].get('name'),
												email : selections[i].get('email'),
												status : 0,
												available : responseParams[selections[i].get('email')]
											});
											this.store.insert(this.store.getCount(), p);
										}
									}
								},
								scope : this
							})

					}
				},
				scope : this
			});
		}
		this.addParticipantsDialog.show();
	}

});