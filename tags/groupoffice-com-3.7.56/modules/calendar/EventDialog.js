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

GO.calendar.EventDialog = function(calendar) {
	this.calendar = calendar;

	this.buildForm();

	this.beforeInit();

	this.goDialogId='event';

	this.resourceGroupsStore = new GO.data.JsonStore({
		url: GO.settings.modules.calendar.url+ 'json.php',
		baseParams: {
			task: 'resources'
		},
		root: 'results',
		id: 'id',
		totalProperty:'total',
		fields: ['id','resources','name','fields'],
		remoteSort: true
	});

	this.resourceGroupsStore.on('load', function()
	{		
		this.buildAccordion();
	}, this);

	var items  = [
	this.propertiesPanel,
	this.recurrencePanel,
	this.optionsPanel,
	this.participantsPanel,
	this.resourcesPanel
	];

	if(GO.customfields && GO.customfields.types["1"])
	{
		for(var i=0;i<GO.customfields.types["1"].panels.length;i++)
		{
			items.push(GO.customfields.types["1"].panels[i]);
		}
	}

	this.tabPanel = new Ext.TabPanel({
		activeTab : 0,
		deferredRender : false,
		border : false,
		anchor : '100% 100%',
		hideLabel : true,
		enableTabScroll : true,
		items : items,
		defaults:{
			forceLayout:true
		}
	});

	this.formPanel = new Ext.form.FormPanel({
		waitMsgTarget : true,
		url : GO.settings.modules.calendar.url + 'json.php',
		border : false,
		baseParams : {
			task : 'event'
		},
		items : this.tabPanel
	});

	this.initWindow();

	this.addEvents({
		'save' : true,
		'show' : true
	});

	this.win.render(Ext.getBody());

}

Ext.extend(GO.calendar.EventDialog, Ext.util.Observable, {
	resources_options : '',
	beforeInit : function(){

	},

	initWindow : function() {
		var focusSubject = function() {
			this.subjectField.focus();
		}

		var tbar = [this.linkBrowseButton = new Ext.Button({
			iconCls : 'btn-link',
			cls : 'x-btn-text-icon',
			text : GO.lang.cmdBrowseLinks,
			disabled : true,
			handler : function() {
				if(!GO.linkBrowser){
					GO.linkBrowser = new GO.LinkBrowser();
				}
				GO.linkBrowser.show({
					link_id : this.event_id,
					link_type : "1",
					folder_id : "0"
				});
			},
			scope : this
		})];

		if (GO.files) {
			tbar.push(this.fileBrowseButton = new Ext.Button({
				iconCls : 'btn-files',
				cls : 'x-btn-text-icon',
				text : GO.files.lang.files,
				handler : function() {
					GO.files.openFolder(this.files_folder_id);
				},
				scope : this,
				disabled : true
			}));
		}

		this.win = new GO.Window({
			layout : 'fit',
			modal : false,
			tbar : tbar,
			resizable : true,
			collapsible:true,
			maximizable:true,
			width : 620,
			height : 450,
			id:'calendar_event_dialog',
			closeAction : 'hide',
			title : GO.calendar.lang.appointment,
			items : this.formPanel,
			focus : focusSubject.createDelegate(this),
			buttons : [{
				text : GO.lang.cmdOk,
				handler : function() {
					this.submitForm(true, { 
						'check_conflicts' : 1
					} );
				},
				scope : this
			}, {
				text : GO.lang.cmdApply,
				handler : function() {
					this.submitForm(false, { 
						'check_conflicts' : 1
					} );
				},
				scope : this
			}, {
				text : GO.lang.cmdClose,
				handler : function() {
					this.win.hide();
				},
				scope : this
			}]
		});
	},

	files_folder_id : 0,

	initCustomFields : function(group_id){

		var record, fields;
		if(group_id > 1)
		{
			record = GO.calendar.groupsStore.getById(group_id);
			fields = record.get('fields');
		}else
		{
			record = true;
			fields = GO.calendar.defaultGroupFields;
		}
		
		if(record)
		{
			if(fields == null)
				fields = '';
			fields = fields.split(',');

			if(GO.customfields && GO.customfields.types["1"])
			{
				this.tabPanel.items.each(function(p){
					if(p.category_id)
					{
						var visible = fields.indexOf('cf_category_'+p.category_id)>-1;

						if(visible)
						{
							this.tabPanel.unhideTabStripItem(p.id);
						}else
						{
							this.tabPanel.hideTabStripItem(p.id);
						}
					}
				}, this);
			}
		}

		if(this.resourceGroupsStore.data.items.length == 0 || group_id != '1')
			this.tabPanel.hideTabStripItem('resources-panel');
		else
			this.tabPanel.unhideTabStripItem('resources-panel');
	},

	initialized : false,

	show : function(config) {

		config = config || {};

		GO.dialogListeners.apply(this);

		this.win.show();

		if(!this.initialized){

			this.win.getEl().mask(GO.lang.waitMsgLoad);
			Ext.Ajax.request({
				url: GO.settings.modules.calendar.url+'json.php',
				params:{
					task:'init_event_window'
				},
				callback: function(options, success, response)
				{

					if(!success)
					{
						alert( GO.lang['strRequestError']);
					}else
					{
						var jsonData = Ext.decode(response.responseText);

						GO.calendar.groupsStore.loadData(jsonData.groups);
						//this.selectCalendar.store.loadData(jsonData.writable_calendars);
						this.resourceGroupsStore.loadData(jsonData.resources);

						if(!GO.calendar.categoriesStore.loaded)
							GO.calendar.categoriesStore.loadData(jsonData.categories);
						
						this.win.getEl().unmask();

						this.initialized=true;
						this.show(config);

					}
				},
				scope:this
			});
			return false;
		}

		/*if(!GO.calendar.groupsStore.loaded){
			GO.calendar.groupsStore.load({
				callback:function(){
					this.show(config);
				},
				scope:this
			});
			return false;
		}

		if(!this.selectCalendar.store.loaded){
			this.selectCalendar.store.load({
				callback:function(){
					this.show(config);
				},
				scope:this
			});
			return false;
		}
        
		if(!this.resourceGroupsStore.loaded){
			this.resourceGroupsStore.load({
				callback:function(){
					this.show(config);
				},
				scope:this
			});
			return false;
		}*/

		/*if(!GO.calendar.categoriesStore.loaded)
			GO.calendar.categoriesStore.load();*/
        
		if (config.oldDomId) {
			this.oldDomId = config.oldDomId;
		} else {
			this.oldDomId = false;
		}
		// propertiesPanel.show();

		delete this.link_config;

		//tmpfiles on the server ({name:'Name',tmp_file:/tmp/name.ext} will be attached)
		this.formPanel.baseParams.tmp_files = config.tmp_files ? Ext.encode(config.tmp_files) : '';

		this.formPanel.form.reset();
        
		this.tabPanel.setActiveTab(0);

		if (!config.event_id) {
			config.event_id = 0;
		}


		this.setEventId(config.event_id);

		if (config.event_id > 0) {
			this.formPanel.load({
				url : GO.settings.modules.calendar.url + 'json.php',
				waitMsg:GO.lang.waitMsgLoad,
				success : function(form, action) {
					//this.win.show();
					
					this.formPanel.form.baseParams['group_id'] = action.result.data.group_id;
					this.initCustomFields(action.result.data.group_id);
					this.changeRepeat(action.result.data.repeat_type);
					this.setValues(config.values);
					this.setWritePermission(action.result.data.write_permission);
					//this.selectCalendar.setValue(action.result.data.calendar_id);
					this.selectCalendar.setRemoteText(action.result.data.calendar_name);
					this.files_folder_id = action.result.data.files_folder_id;

					if(action.result.data.group_id == 1)
					{
						this.toggleFieldSets(action.result.data.resources_checked);
					}

					this.selectCategory.container.up('div.x-form-item').setDisplayed(this.formPanel.form.baseParams['group_id']==1);
					
					if(action.result.data.category_name)
						this.selectCategory.setRemoteText(action.result.data.category_name);
					/*if(this.formPanel.form.baseParams['group_id'] == 1)
					{
						this.selectCategory.setValue(GO.calendar.lang.selectCategory);
					}*/
					
					//this.colorField.setDisabled(this.formPanel.form.baseParams['group_id']==1);

					this.numParticipants=action.result.data.num_participants;

				},
				failure : function(form, action) {
					Ext.Msg.alert(GO.lang.strError, action.result.feedback)
				},
				scope : this

			});
		} else if (config.exception_event_id) {

			this.formPanel.load({
				url : GO.settings.modules.calendar.url + 'json.php',
				params : {
					event_id : config.exception_event_id
				},
				waitMsg : GO.lang.waitMsgLoad,
				success : function(form, action) {
					//this.win.show();

					//this.participantsPanel.setEventId(0);
					this.formPanel.form.baseParams['exception_event_id'] = config.exception_event_id;
					this.formPanel.form.baseParams['exceptionDate'] = config.exceptionDate;

					// set recurrence to none
					this.formPanel.form.findField('repeat_type').setValue(0);
					this.changeRepeat(0);

					this.setValues(config.values);

					this.setWritePermission(action.result.data.write_permission);
				},
				failure : function(form, action) {
					Ext.Msg.alert(GO.lang.strError, action.result.feedback)
				},
				scope : this
			});
		} else {
			delete this.formPanel.form.baseParams['exception_event_id'];
			delete this.formPanel.form.baseParams['exceptionDate'];
			delete this.formPanel.form.baseParams['group_id'];
			this.setWritePermission(true);

			config.values = config.values || {};

			var date = new Date();

			var i = parseInt(date.format("i"));

			if (i > 45) {
				i = '45';
			} else if (i > 30) {
				i = '30';
			} else if (i > 15) {
				i = '15';
			} else {
				i = '00';
			}

			if (!config.values.start_date)
				config.values['start_date'] = new Date();
			if (!config.values.start_time)
				config.values['start_time'] = date.format(GO.settings.time_format);

			if (!config.values.end_date)
				config.values['end_date'] = new Date();
			if (!config.values.end_time)
				config.values['end_time'] = date.add(Date.HOUR, 1).format(GO.settings.time_format);


			this.setValues(config.values);

			var group_id=1;

			if (GO.util.empty(config.calendar_id)){// || !this.selectCalendar.store.getById(config.calendar_id)) {
				config.calendar_id = GO.calendar.defaultCalendar.id;
				config.calendar_name = GO.calendar.defaultCalendar.name;
			}

			var calendarRecord = this.selectCalendar.store.getById(config.calendar_id);

            if(calendarRecord){
				group_id = calendarRecord.get('group_id');
			}
			this.formPanel.form.baseParams['group_id'] = group_id;
			this.initCustomFields(group_id);
			
			//this.colorField.setDisabled(group_id==1);
			this.selectCategory.container.up('div.x-form-item').setDisplayed(group_id==1);

			if(group_id == 1)
				this.toggleFieldSets();

			this.selectCalendar.setValue(config.calendar_id);

			if(config.calendar_name)
				this.selectCalendar.setRemoteText(config.calendar_name);
			
		/*if (config.calendar_name) {
                //this.selectCalendar.container.up('div.x-form-item').setDisplayed(true);
                this.selectCalendar.setRemoteText(config.calendar_name);
            }else
            {
                //this.selectCalendar.container.up('div.x-form-item').setDisplayed(false);
            }*/
		}
					
		
		// if the newMenuButton from another passed a linkTypeId then set this
		// value in the select link field
		if (config && config.link_config) {
			this.link_config = config.link_config;
			if (config.link_config.type_id) {
				this.selectLinkField.setValue(config.link_config.type_id);
				this.selectLinkField.setRemoteText(config.link_config.text);

				if(this.subjectField.getValue()=='')
					this.subjectField.setValue(config.link_config.text);
			}
		}

		this.fireEvent('show', this);
	},
	updateResourcePanel : function()
	{
		var values = {};
		var checked = [];		
		
		// save values before all items are removed (checkboxes + statuses)
		if(this.win.isVisible())
		{
			if(GO.customfields && GO.customfields.types["1"])
			{
				for(var i=0; i<this.resourceGroupsStore.data.items.length; i++)
				{
					var record = this.resourceGroupsStore.data.items[i].data;
					var resources = record.resources;

					for(var j=0; j<resources.length; j++)
					{
						var calendar_id = resources[j].id;
						values['status_'+calendar_id] = this.formPanel.form.findField('status_'+calendar_id).getValue();

						var p = this.resourcesPanel.getComponent('group_'+record.id);
						var c = p.getComponent('resource_'+calendar_id);
						if(!c.collapsed)
						{
							checked.push(calendar_id);
						}

						for(var k=0; k<record.fields.length; k++)
						{
							var field = record.fields[k];
							if(field)
							{
								for(var l=0; l<GO.customfields.types["1"].panels.length; l++)
								{
									var cfield = 'cf_category_'+GO.customfields.types["1"].panels[l].category_id;
									if(cfield == field)
									{
										var cf = GO.customfields.types["1"].panels[l].customfields;
										for(var m=0; m<cf.length; m++)
										{
											var name = 'resource_options['+calendar_id+']['+cf[m].dataname+']';
											var value = this.formPanel.form.findField(name).getValue();

											values[name] = value;
										}
									}
								}
							}
						}
					}
				}
			}
		}
        
		this.resourceGroupsStore.load({
			callback:function()
			{
				if(this.win.isVisible())
				{
					if(checked)
					{
						this.toggleFieldSets(checked);
					}

					// after reload store set the values we saved earlier
					this.setValues(values);

					if(this.resourceGroupsStore.data.items.length == 0)
					{
						this.tabPanel.hideTabStripItem('resources-panel');
						this.tabPanel.setActiveTab(0);
					} else
{
						this.tabPanel.unhideTabStripItem('resources-panel');												
					}
				}
			},
			scope:this
		});
	},
	toggleFieldSets : function(resources_checked)
	{
		for(var i=0; i<this.resourceGroupsStore.data.items.length; i++)
		{
			var record = this.resourceGroupsStore.data.items[i].data;
			var resources = record.resources;

			for(var j=0; j<resources.length; j++)
			{
				var p = this.resourcesPanel.getComponent('group_'+record.id);
				var r = 'resource_'+resources[j].id;
				var c = p.getComponent(r);

				if(resources_checked && (resources_checked.indexOf(resources[j].id) != -1))
				{
					c.expand();
				}else
				{
					var l = c.getComponent('status_'+resources[j].id);
					l.setValue(GO.calendar.lang.no_status);

					c.collapse();
				}
			}
		}
	},
	setWritePermission : function(writePermission) {
		this.win.buttons[0].setDisabled(!writePermission);
		this.win.buttons[1].setDisabled(!writePermission);
	},

	setValues : function(values) {
		if (values) {
			for (var key in values) {
				var field = this.formPanel.form.findField(key);
				if (field) {
					field.setValue(values[key]);
				}
			}
		}
	},
	setEventId : function(event_id) {
		this.formPanel.form.baseParams['event_id'] = event_id;
		this.event_id = event_id;

		this.participantsPanel.setEventId(event_id);

		this.selectLinkField.container.up('div.x-form-item').setDisplayed(event_id == 0);

		this.linkBrowseButton.setDisabled(event_id < 1);
		if (GO.files) {
			this.fileBrowseButton.setDisabled(event_id < 1);
		}
	},

	setCurrentDate : function() {
		var formValues = {};

		var date = new Date();

		formValues['start_date'] = date.format(GO.settings['date_format']);
		formValues['start_time'] = date.format(GO.settings.time_format);
		
		formValues['end_date'] = date.format(GO.settings['date_format']);
		formValues['end_time'] = date.add(Date.HOUR, 1).format(GO.settings.time_format);
		
		this.formPanel.form.setValues(formValues);
	},

	numParticipants:0,
	submitForm : function(hide, config) {

		if(!config)
		{
			config = {};
		}

		this.hide = hide;

		var params = {
			'task' : 'save_event'
			,
			'check_conflicts' : typeof(config.check_conflicts)!='undefined' ? config.check_conflicts : null
		};

		if(this.participantsPanel.store.loaded)
		{
			var gridData = this.participantsPanel.getGridData();
			params.participants=Ext.encode(gridData);

			this.numParticipants = this.participantsPanel.store.getCount();
//			for(var i=0; i<this.participantsPanel.store.data.items.length; i++)
//			{
//				if(this.participantsPanel.store.data.items[i].data.user_id != GO.settings.user_id)
//				{
//					this.numParticipants++;
//				}
//			}
		}

		//don't request invitation if import is enabled. TODO import is a bad name.
		//it's for direct scheduling.
		if(this.numParticipants>1 && !this.participantsPanel.importCheckbox.getValue())
		{
			var invitationMessage = (this.event_id) ? GO.calendar.lang.sendInvitationUpdate : GO.calendar.lang.sendInvitationInitial;
		
			params.send_invitation = (confirm(invitationMessage)) ? 1 : 0;
		}
		
		this.formPanel.form.submit({
			url : GO.settings.modules.calendar.url + 'action.php',
			params : params,
			waitMsg : GO.lang.waitMsgSave,
			success : function(form, action) {

				if (action.result.event_id) {
					this.files_folder_id = action.result.files_folder_id;
					this.setEventId(action.result.event_id);
				}

				var startDate = this.getStartDate();

				var endDate = this.getEndDate();

				var newEvent = {
					// id : Ext.id(),
					calendar_id : this.selectCalendar.getValue(),
					event_id : this.event_id,
					name : Ext.util.Format.htmlEncode(this.subjectField.getValue()),
					start_time : startDate.format('Y-m-d H:i'),
					end_time : endDate.format('Y-m-d H:i'),
					startDate : startDate,
					endDate : endDate,
					description : Ext.util.Format.htmlEncode(GO.util.nl2br(this.formPanel.form
						.findField('description').getValue()).replace(/\n/g,'')),
					background : this.formPanel.form.findField('background')
					.getValue(),
					location : this.formPanel.form.findField('location')
					.getValue(),
					repeats : this.formPanel.form.findField('repeat_type')
					.getValue() > 0,
					'private' : false,
					exception_event_id : this.formPanel.form.baseParams['exception_event_id'],
					num_participants: this.numParticipants
				};


				this.fireEvent('save', newEvent, this.oldDomId);

				if (this.link_config && this.link_config.callback) {
					this.link_config.callback.call(this);
				}

				if(action.result.feedback){
					Ext.MessageBox.alert(GO.lang.strError, action.result.feedback);
				}else	if (hide) {
					this.win.hide();
				}

				if (config && config.callback) {
					config.callback.call(this, this, true);
				}

			},
			failure : function(form, action) {
				if (action.failureType == 'client') {
					var error = GO.lang.strErrorsInForm;
				} else {
					var error = action.result.feedback;
				}

				if (error=='Ask permission') {
					Ext.Msg.show({
						title: GO.calendar.lang.ignoreConflictsTitle,
						msg: GO.calendar.lang.ignoreConflictsMsg,
						buttons: Ext.Msg.YESNO,
						fn: this.handlePrompt,
						animEl: 'elId',
						icon: Ext.MessageBox.QUESTION
					});
				} else if (error=='Resource conflict') {
					error = GO.calendar.lang.resourceConflictMsg;
					if (config && config.callback) {
						config.callback.call(this, this, false);
					}
					error = error+'<ul>';
					for (var i in action.result.resources) {
						if (!isNaN(i))
							error = error+'<li> - '+action.result.resources[i]+'</li>';
					}
					error = error+'</ul>';
					Ext.MessageBox.alert(GO.calendar.lang.resourceConflictTitle, error);
				} else {
					if (config && config.callback) {
						config.callback.call(this, this, false);
					}
					Ext.MessageBox.alert(GO.lang.strError, error);
				}
			},
			scope : this
		});
	},

	handlePrompt : function(btn) {
		if (btn=='yes') {
			GO.calendar.eventDialog.submitForm(GO.calendar.eventDialog.hide,{
				'check_conflicts':'0'
			});
		}
	},

	getStartDate : function() {

		var startDate = this.startDate.getValue();
		if (!this.formPanel.form.findField('all_day_event').getValue()) {
			startDate = Date.parseDate(startDate.format('Y-m-d')+' '+this.formPanel.form.findField('start_time').getValue(),'Y-m-d '+GO.settings.time_format);
		}

		return startDate;
	},

	getEndDate : function() {
		var endDate = this.endDate.getValue();
		if (!this.formPanel.form.findField('all_day_event').getValue()) {
			endDate = Date.parseDate(endDate.format('Y-m-d')+' '+this.formPanel.form.findField('end_time').getValue(),'Y-m-d '+GO.settings.time_format);
		}
		return endDate;
	},

	checkDateInput : function() {

		var eD = this.endDate.getValue();
		var sD = this.startDate.getValue();

		if (sD > eD) {
			this.endDate.setValue(sD);
		}

		if (sD.getElapsed(eD) == 0) {
			
			var sdWithTime = sD.format('Y-m-d')+' '+this.startTime.getValue();
			var sT = Date.parseDate(sdWithTime, 'Y-m-d '+GO.settings.time_format);

			var edWithTime = eD.format('Y-m-d')+' '+this.endTime.getValue();
			var eT = Date.parseDate(edWithTime, 'Y-m-d '+GO.settings.time_format);

			if(sT>=eT){
				this.endTime.setValue(sT.add(Date.HOUR, 1).format(GO.settings.time_format))
			}
		}

		if (this.repeatType.getValue() > 0) {
			if (this.repeatEndDate.getValue() == '') {
				this.repeatForever.setValue(true);
			} else {

				if (this.repeatEndDate.getValue() < eD) {
					this.repeatEndDate.setValue(eD.add(Date.DAY, 1));
				}
			}
		}

		this.participantsPanel.reloadAvailability();
	},

	buildForm : function() {

		this.selectLinkField = new GO.form.SelectLink({});

		this.subjectField = new Ext.form.TextField({
			name : 'subject',
			allowBlank : false,
			fieldLabel : GO.lang.strSubject
		});

		this.locationField = new Ext.form.TextField({
			name : 'location',
			allowBlank : true,
			fieldLabel : GO.lang.strLocation
		});
		this.startDate = new Ext.form.DateField({
			name : 'start_date',
			width : 100,
			format : GO.settings['date_format'],
			allowBlank : false,			
			listeners : {
				change : {
					fn : this.checkDateInput,
					scope : this
				}
			}
		});

		this.startTime = new Ext.form.TimeField({
			increment: 15,
			format:GO.settings.time_format,
			name:'start_time',
			width:80,
			hideLabel:true,
			autoSelect :true,			
			listeners : {
				change : {
					fn : this.checkDateInput,
					scope : this
				}
			}
		});

		this.endTime = new Ext.form.TimeField({
			increment: 15,
			format:GO.settings.time_format,
			name:'end_time',
			width:80,
			hideLabel:true,
			autoSelect :true,			
			listeners : {
				change : {
					fn : this.checkDateInput,
					scope : this
				}
			}
		});	

		this.endDate = new Ext.form.DateField({
			name : 'end_date',
			width : 100,
			format : GO.settings['date_format'],
			allowBlank : false,			
			listeners : {
				change : {
					fn : this.checkDateInput,
					scope : this
				}
			}
		});

		this.allDayCB = new Ext.form.Checkbox({
			boxLabel : GO.calendar.lang.allDay,
			name : 'all_day_event',
			checked : false,
			width : 'auto',
			labelSeparator : '',
			hideLabel : true
		});

		this.allDayCB.on('check', function(checkbox, checked) {
			this.startTime.setDisabled(checked);
			this.endTime.setDisabled(checked);
			
		}, this);

		this.eventStatus = new Ext.form.ComboBox({
			hiddenName : 'status',
			triggerAction : 'all',
			editable : false,
			selectOnFocus : true,
			width : 148,
			forceSelection : true,			
			mode : 'local',
			value : 'ACCEPTED',
			valueField : 'value',
			displayField : 'text',
			store : new Ext.data.SimpleStore({
				fields : ['value', 'text'],
				data : [
				['NEEDS-ACTION',
				GO.calendar.lang.needsAction],
				['ACCEPTED', GO.calendar.lang.accepted],
				['DECLINED', GO.calendar.lang.declined],
				['TENTATIVE',
				GO.calendar.lang.tentative],
				['DELEGATED',
				GO.calendar.lang.delegated]]
			}),
			listeners: {
				scope:this,
				change:function(cb, newValue){
					if(this.formPanel.form.baseParams['group_id']>1){
						if(newValue=='ACCEPTED'){
							this.colorField.setValue('CCFFCC');
						}else
						{
							this.colorField.setValue('FF6666');
						}
					}
				}
			}
		});

		this.busy = new Ext.form.Checkbox({
			boxLabel : GO.calendar.lang.busy,
			name : 'busy',
			checked : true,
			width : 'auto',
			labelSeparator : '',
			hideLabel : true
		});

		this.selectCategory = new GO.form.ComboBoxReset({
			hiddenName:'category_id',
			fieldLabel:GO.calendar.lang.category,
			value:'',
			valueField:'id',
			displayField:'name',
			store: GO.calendar.categoriesStore,
			mode:'local',
			triggerAction:'all',
			emptyText:GO.calendar.lang.selectCategory,
			editable:false,
			selectOnFocus:true,
			forceSelection:true,
			tpl:'<tpl for="."><div class="x-combo-list-item"><div style="float:left;width:20px;margin-right:5px;background-color:#{color}">&nbsp;</div>{name}</div></tpl>'
		});

		this.selectCategory.on('select', function(combo, record)
		{			
			this.colorField.setValue(record.data.color);
		}, this);

		this.propertiesPanel = new Ext.Panel({
			hideMode : 'offsets',
			title : GO.lang.strProperties,
			defaults : {
				anchor : '-20'
			},
			// cls:'go-form-panel',waitMsgTarget:true,
			bodyStyle : 'padding:5px',
			layout : 'form',
			autoScroll : true,
			items : [
			this.subjectField,
			this.locationField,
			this.selectLinkField,
			{	
				xtype : 'compositefield',
				fieldLabel:GO.lang.strStart,
				items : [this.startDate,this.startTime,this.allDayCB
				]
			},{
				fieldLabel:GO.lang.strEnd,
				xtype : 'compositefield',				
				items : [this.endDate, this.endTime
				]
			},{
				xtype : 'compositefield',
				fieldLabel : GO.calendar.lang.status,
				items : [
				this.eventStatus,this.busy
				]
			},
			this.selectCalendar = new GO.calendar.SelectCalendar({
				anchor : '-20',
				valueField : 'id',
				displayField : 'name',
				typeAhead : true,
				triggerAction : 'all',
				editable : false,
				selectOnFocus : true,
				forceSelection : true,
				allowBlank : false
			}),this.selectCategory,new GO.form.PlainField({
				fieldLabel: GO.lang.strOwner,
				value: GO.settings.name,
				name:'user_name'
			}),{
				xtype:'textarea',
				fieldLabel:GO.lang.strDescription,
				name : 'description',
				anchor:'-20 -240'
			}]

		});
		// Start of recurrence tab

		var data = new Array();

		for(var i=1;i<31;i++)
		{
			data.push([i]);
		}

		this.repeatEvery = new Ext.form.ComboBox({

			
			hiddenName : 'repeat_every',
			triggerAction : 'all',
			editable : false,
			selectOnFocus : true,
			width : 50,
			forceSelection : true,
			mode : 'local',
			value : '1',
			valueField : 'value',
			displayField : 'value',
			store : new Ext.data.SimpleStore({
				fields : ['value'],
				data : data
			})
		});

		this.repeatType = new Ext.form.ComboBox({
			hiddenName : 'repeat_type',
			triggerAction : 'all',
			editable : false,
			selectOnFocus : true,
			width : 200,
			forceSelection : true,
			mode : 'local',
			value : '0',
			valueField : 'value',
			displayField : 'text',
			store : new Ext.data.SimpleStore({
				fields : ['value', 'text'],
				data : [['0', GO.calendar.lang.noRecurrence],
				['1', GO.calendar.lang.days],
				['2', GO.calendar.lang.weeks],
				['3', GO.calendar.lang.monthsByDate],
				['4', GO.calendar.lang.monthsByDay],
				['5', GO.calendar.lang.years]]
			}),
			hideLabel : true

		});

		this.repeatType.on('select', function(combo, record) {
			this.checkDateInput();
			this.changeRepeat(record.data.value);
		}, this);

		this.monthTime = new Ext.form.ComboBox({
			hiddenName : 'month_time',
			triggerAction : 'all',
			selectOnFocus : true,
			disabled : true,
			width : 80,
			forceSelection : true,			
			mode : 'local',
			value : '1',
			valueField : 'value',
			displayField : 'text',
			store : new Ext.data.SimpleStore({
				fields : ['value', 'text'],
				data : [['1', GO.lang.strFirst],
				['2', GO.lang.strSecond],
				['3', GO.lang.strThird],
				['4', GO.lang.strFourth]]
			})
		});

		this.cb = [];
		for (var day = 0; day < 7; day++) {

			var display_day = day+parseInt(GO.settings.first_weekday);
			if(display_day==7)display_day=0;

			this.cb[display_day] = new Ext.form.Checkbox({
				boxLabel : GO.lang.shortDays[display_day],
				id : 'frm_repeat_days_' + display_day,
				name : 'repeat_days_' + display_day,
				disabled : true,
				checked : false,
				width : 'auto',
				hideLabel : true,
				laelSeperator : ''
			});
		}

		this.repeatEndDate = new Ext.form.DateField({
			name : 'repeat_end_date',
			width : 100,
			disabled : true,
			format : GO.settings['date_format'],
			allowBlank : true,			
			listeners : {
				change : {
					fn : this.checkDateInput,
					scope : this
				}
			}
		});

		this.repeatForever = new Ext.form.Checkbox({
			boxLabel : GO.calendar.lang.repeatForever,
			name : 'repeat_forever',
			checked : true,
			disabled : true,
			width : 'auto',
			hideLabel : true,
			listeners : {
				check : {
					fn : function(cb, checked){
						this.repeatEndDate.setDisabled(checked);
					},
					scope : this
				}
			}
		});
		this.recurrencePanel = new Ext.Panel({
			title : GO.calendar.lang.recurrence,
			bodyStyle : 'padding: 5px',
			layout : 'form',
			hideMode : 'offsets',
			defaults:{
				forceLayout:true,
				border:false
			},
			items : [{
				fieldLabel : GO.calendar.lang.repeatEvery,
				xtype : 'compositefield',
				items : [this.repeatEvery,this.repeatType]
			}, {
				xtype : 'compositefield',
				fieldLabel : GO.calendar.lang.atDays,
				items : [this.monthTime,this.cb[1],this.cb[2],this.cb[3],this.cb[4],this.cb[5],this.cb[6],this.cb[0]]
			}, {
				fieldLabel : GO.calendar.lang.repeatUntil,
				xtype : 'compositefield',
				items : [this.repeatEndDate,this.repeatForever]
			}
			]
		});

		var reminderValues = [['0', GO.calendar.lang.noReminder]];

		for (var i = 1; i < 60; i++) {
			reminderValues.push([i, i]);
		}

		this.reminderValue = new Ext.form.ComboBox({
			
			hiddenName : 'reminder_value',
			triggerAction : 'all',
			editable : false,
			selectOnFocus : true,
			width : 148,
			forceSelection : true,
			mode : 'local',
			value : GO.calendar.defaultReminderValue,
			valueField : 'value',
			displayField : 'text',
			store : new Ext.data.SimpleStore({
				fields : ['value', 'text'],
				data : reminderValues
			})
		});

		this.reminderMultiplier = new Ext.form.ComboBox({
			hiddenName : 'reminder_multiplier',
			triggerAction : 'all',
			editable : false,
			selectOnFocus : true,
			width : 148,
			forceSelection : true,
			mode : 'local',
			value : GO.calendar.defaultReminderMultiplier,
			valueField : 'value',
			displayField : 'text',
			store : new Ext.data.SimpleStore({
				fields : ['value', 'text'],
				data : [['60', GO.lang.strMinutes],
				['3600', GO.lang.strHours],
				['86400', GO.lang.strDays]

				]
			}),
			hideLabel : true,
			labelSeperator : ''
		});

		this.participantsPanel = new GO.calendar.ParticipantsPanel(this);


		this.privateCB = new Ext.form.Checkbox({
			boxLabel : GO.calendar.lang.privateEvent,
			name : 'private',
			checked : false,
			width : 'auto',
			labelSeparator : '',
			hideLabel : true
		});

		this.optionsPanel = new Ext.Panel({
			layout:"form",
			title : GO.calendar.lang.options,
			bodyStyle : 'padding:5px',
			hideMode : 'offsets',
			border:false,
			items:[{
				xtype : 'compositefield',
				fieldLabel : GO.calendar.lang.reminder,
				items : [this.reminderValue,this.reminderMultiplier]
			},this.colorField = new GO.form.ColorField({
				fieldLabel : GO.lang.color,
				value : GO.calendar.defaultBackground,
				name : 'background',
				colors : [
				'EBF1E2',
				'95C5D3',
				'FFFF99',
				'A68340',
				'82BA80',
				'F0AE67',
				'66FF99',
				'CC0099',
				'CC99FF',
				'996600',
				'999900',
				'FF0000',
				'FF6600',
				'FFFF00',
				'FF9966',
				'FF9900',
				'FF6666',
				'CCFFCC',
				/* Line 1 */
				'FB0467',
				'D52A6F',
				'CC3370',
				'C43B72',
				'BB4474',
				'B34D75',
				'AA5577',
				'A25E79',
				/* Line 2 */
				'FF00CC',
				'D52AB3',
				'CC33AD',
				'C43BA8',
				'BB44A3',
				'B34D9E',
				'AA5599',
				'A25E94',
				/* Line 3 */
				'CC00FF',
				'B32AD5',
				'AD33CC',
				'A83BC4',
				'A344BB',
				'9E4DB3',
				'9955AA',
				'945EA2',
				/* Line 4 */
				'6704FB',
				'6E26D9',
				'7033CC',
				'723BC4',
				'7444BB',
				'754DB3',
				'7755AA',
				'795EA2',
				/* Line 5 */
				'0404FB',
				'2626D9',
				'3333CC',
				'3B3BC4',
				'4444BB',
				'4D4DB3',
				'5555AA',
				'5E5EA2',
				/* Line 6 */
				'0066FF',
				'2A6ED5',
				'3370CC',
				'3B72C4',
				'4474BB',
				'4D75B3',
				'5577AA',
				'5E79A2',
				/* Line 7 */
				'00CCFF',
				'2AB2D5',
				'33ADCC',
				'3BA8C4',
				'44A3BB',
				'4D9EB3',
				'5599AA',
				'5E94A2',
				/* Line 8 */
				'00FFCC',
				'2AD5B2',
				'33CCAD',
				'3BC4A8',
				'44BBA3',
				'4DB39E',
				'55AA99',
				'5EA294',
				/* Line 9 */
				'00FF66',
				'2AD56F',
				'33CC70',
				'3BC472',
				'44BB74',
				'4DB375',
				'55AA77',
				'5EA279',
				/* Line 10 */
				'00FF00', '2AD52A',
				'33CC33',
				'3BC43B',
				'44BB44',
				'4DB34D',
				'55AA55',
				'5EA25E',
				/* Line 11 */
				'66FF00', '6ED52A', '70CC33',
				'72C43B',
				'74BB44',
				'75B34D',
				'77AA55',
				'79A25E',
				/* Line 12 */
				'CCFF00', 'B2D52A', 'ADCC33', 'A8C43B',
				'A3BB44',
				'9EB34D',
				'99AA55',
				'94A25E',
				/* Line 13 */
				'FFCC00', 'D5B32A', 'CCAD33', 'C4A83B',
				'BBA344', 'B39E4D',
				'AA9955',
				'A2945E',
				/* Line 14 */
				'FF6600', 'D56F2A', 'CC7033', 'C4723B',
				'BB7444', 'B3754D', 'AA7755',
				'A2795E',
				/* Line 15 */
				'FB0404', 'D52A2A', 'CC3333', 'C43B3B',
				'BB4444', 'B34D4D', 'AA5555', 'A25E5E',
				/* Line 16 */
				'FFFFFF', '949494', '808080', '6B6B6B',
				'545454', '404040', '292929', '000000']
			}),
			this.privateCB]
		});

		this.resourcesPanel = new Ext.Panel({
			id:'resources-panel',
			title:GO.calendar.lang.resources,
			border:false,
			layout:'accordion',
			forceLayout:true,
			layoutConfig:{
				titleCollapse:true,
				animate:false,
				activeOnTop:false
			},
			defaults:{
				forceLayout:true
			}
		});
		this.resourcesPanel.on('show', function(){
			this.tabPanel.doLayout();
		},this);

        
	},

	buildAccordion : function()
	{
		this.resourcesPanel.removeAll(true);
		this.resourcesPanel.forceLayout=true;
		
		var newFormField;
		for(var i=0; i<this.resourceGroupsStore.getCount(); i++)
		{
			var record = this.resourceGroupsStore.data.items[i].data;
			var resourceFieldSets = [];
			var resources = record.resources;

			for(var j=0; j<resources.length; j++)
			{
				var resourceOptions = [];

				var pfieldStatus = new GO.form.PlainField({
					id:'status_'+resources[j].id,
					name:'status_'+resources[j].id,
					fieldLabel: GO.calendar.lang.status
				});
				resourceOptions.push(pfieldStatus);
				this.formPanel.form.add(pfieldStatus);

				for(var k=0; k<record.fields.length; k++)
				{
					var field = record.fields[k];

					if(field && GO.customfields && GO.customfields.types["1"])
					{
						for(var l=0; l<GO.customfields.types["1"].panels.length; l++)
						{
							var cfield = 'cf_category_'+GO.customfields.types["1"].panels[l].category_id;
							if(cfield == field)
							{
								var cf = GO.customfields.types["1"].panels[l].customfields;
								for(var m=0; m<cf.length; m++)
								{
									newFormField = GO.customfields.getFormField(cf[m],{
										name:'resource_options['+resources[j].id+']['+cf[m].dataname+']',
										id:'resource_options['+resources[j].id+']['+cf[m].dataname+']'
									});


									/*
									 * Customfields might return a simple object instead of an Ext.component.
									 * So check if it has events otherwise create the Ext component.
									 */
									if(!newFormField.events){
										newFormField=Ext.ComponentMgr.create(newFormField, 'textfield');
									}

									resourceOptions.push(newFormField);
									this.formPanel.form.add(newFormField);
								}

								l = GO.customfields.types["1"].panels.length;
							}
						}
					}
					else
					{
						resourceOptions.push(new GO.form.PlainField({
							name:'no_fields_'+resources[j].id,
							hideLabel:true,
							value: GO.calendar.lang.no_custom_fields
						}));
					}
				}

				resourceFieldSets.push({
					xtype:'fieldset',
					checkboxToggle:true,
					checkboxName:'resources['+resources[j].id+']',
					title:resources[j].name,
					id:'resource_'+resources[j].id,
					autoHeight:true,
					collapsed:true,
					forceLayout:true,
					items:resourceOptions
				});
			}
			
			var resourcePanel = new Ext.Panel({
				cls:'go-form-panel',
				id:'group_'+record.id,
				layout:'form',
				autoScroll:true,
				forceLayout:true,
				title:record.name,
				items:resourceFieldSets
			});
            
			this.resourcesPanel.add(resourcePanel);			
		}		
		this.tabPanel.doLayout();
	},

	changeRepeat : function(value) {

		var repeatForever = this.repeatForever.getValue();

		var form = this.formPanel.form;
		switch (value) {
			case '0' :
				this.disableDays(true);
				form.findField('month_time').setDisabled(true);
				form.findField('repeat_forever').setDisabled(true);
				form.findField('repeat_end_date').setDisabled(true);
				form.findField('repeat_every').setDisabled(true);
				break;

			case '1' :
				this.disableDays(true);
				form.findField('month_time').setDisabled(true);
				form.findField('repeat_forever').setDisabled(false);
				form.findField('repeat_end_date').setDisabled(repeatForever);
				form.findField('repeat_every').setDisabled(false);

				break;

			case '2' :
				this.disableDays(false);
				form.findField('month_time').setDisabled(true);
				form.findField('repeat_forever').setDisabled(false);
				form.findField('repeat_end_date').setDisabled(repeatForever);
				form.findField('repeat_every').setDisabled(false);

				var weekday = form.findField('start_date').getValue().getDay();
				this.formPanel.form.findField('repeat_days_' + weekday).setValue(true);
				
				break;

			case '3' :
				this.disableDays(true);
				form.findField('month_time').setDisabled(true);
				form.findField('repeat_forever').setDisabled(false);
				form.findField('repeat_end_date').setDisabled(repeatForever);
				form.findField('repeat_every').setDisabled(false);

				break;

			case '4' :
				this.disableDays(false);
				form.findField('month_time').setDisabled(false);
				form.findField('repeat_forever').setDisabled(false);
				form.findField('repeat_end_date').setDisabled(repeatForever);
				form.findField('repeat_every').setDisabled(false);
				break;

			case '5' :
				this.disableDays(true);
				form.findField('month_time').setDisabled(true);
				form.findField('repeat_forever').setDisabled(false);
				form.findField('repeat_end_date').setDisabled(repeatForever);
				form.findField('repeat_every').setDisabled(false);
				break;
		}
	},
	disableDays : function(disabled) {
		for (var day = 0; day < 7; day++) {
			this.formPanel.form.findField('repeat_days_' + day)
			.setDisabled(disabled);
		}
	}
});