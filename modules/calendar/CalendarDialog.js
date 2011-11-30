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

GO.calendar.CalendarDialog = function(config)
{
	if(!config)
	{
		config = {};
	}

	
	
	this.propertiesTab = new Ext.form.FormPanel({
		url: GO.settings.modules.calendar.url+'action.php',		
		defaultType: 'textfield',
		waitMsgTarget:true,
		title:GO.lang['strProperties'],
		layout:'form',
		anchor: '100% 100%',		
		autoHeight:true,
		cls:'go-form-panel',
		labelWidth: 120,
		items: [
		this.selectUser = new GO.form.SelectUser({
			fieldLabel: GO.lang.strUser,
			disabled : !GO.settings.has_admin_permission,
			value: GO.settings.user_id,
			anchor: '100%'
		}),
		this.name = new Ext.form.TextField({
			fieldLabel: GO.lang.strName,
			name: 'name',
			allowBlank:false,
			anchor: '100%'
		}),




		this.selectGroup = new GO.form.ComboBox({
			hiddenName:'group_id',
			fieldLabel:GO.calendar.lang.group,
			valueField:'id',
			displayField:'name',
			id:'resource_groups',
			emptyText: GO.lang.strPleaseSelect,
			store: GO.calendar.groupsStore,
			mode:'local',
			triggerAction:'all',
			editable:false,
			selectOnFocus:true,
			allowBlank:true,
			forceSelection:true,
			anchor:'100%'
		}),{
			xtype:'checkbox',
			name:'show_bdays',
			boxLabel:GO.calendar.lang.show_bdays,
			hideLabel:true
		},{
			xtype:'textarea',
			fieldLabel:GO.lang.strComment,
			name:'comment',
			anchor:'100%',
			height:50
		}
		]
	});

	if(GO.tasks)
	{
		this.tasklistsTab = new GO.calendar.TasklistsGrid({
			title:GO.tasks.lang.visibleTasklists
		});

		this.selectTasklist = new GO.form.ComboBoxReset({
			fieldLabel:'CalDAV '+GO.tasks.lang.tasklist,
				store:new GO.data.JsonStore({
				url: GO.settings.modules.tasks.url+'json.php',
				baseParams: {'task': 'tasklists', 'auth_type':'write'},
				root: 'results',
				totalProperty: 'total',
				id: 'id',
				fields:['id','name','user_name'],
				remoteSort:true
			}),
			displayField: 'name',
			valueField: 'id',
			triggerAction:'all',
			hiddenName:'tasklist_id',
			mode:'remote',
			editable: true,
			selectOnFocus:true,
			forceSelection: true,
			typeAhead: true,
			emptyText:GO.lang.none,
			pageSize: parseInt(GO.settings.max_rows_list)
		});

		this.propertiesTab.add(this.selectTasklist);
	}

	this.propertiesTab.add([{
			xtype:'plainfield',
			fieldLabel:'Direct URL',
			name:'url',
			anchor:'100%'
		},{
			xtype:'checkbox',
			hideLabel:true,
			boxLabel:GO.calendar.lang.publishICS,
			name:'public'
		},{
			xtype:'plainfield',
			fieldLabel:'iCalendar URL',
			name:'ics_url',
			anchor:'100%'
		},
		this.exportButton = new Ext.Button({
			text:GO.lang.cmdExport,
			disabled:true,
			handler:function(){
				document.location=GO.settings.modules.calendar.url+'export.php?calendar_id='+this.calendar_id;
			},
			scope:this
		})])

	this.readPermissionsTab = new GO.grid.PermissionsPanel({	
	});
	
	var uploadFile = new GO.form.UploadFile({
		inputName : 'ical_file',	   
		max:1 			
	});
	
	uploadFile.on('filesChanged', function(input, inputs){
		this.importButton.setDisabled(inputs.getCount()==1);
	}, this);
	

	this.importTab = new Ext.form.FormPanel({
		fileUpload:true,
		waitMsgTarget:true,
		disabled:true,
		title:GO.lang.cmdImport,
		items: [{
			xtype: 'panel',
			html: GO.calendar.lang.selectIcalendarFile,
			border:false	
		},uploadFile,this.importButton = new Ext.Button({
			xtype:'button',
			disabled:true,
			text:GO.lang.cmdImport,
			handler: function(){
				this.importTab.form.submit({
					waitMsg:GO.lang.waitMsgUpload,
					url:GO.settings.modules.calendar.url+'action.php',
					params: {
						task: 'import',
						calendar_id:this.calendar_id
					},
					success: function(form,action)
					{
						uploadFile.clearQueue();

						if(action.result.success)
						{
							Ext.MessageBox.alert(GO.lang.strSuccess,action.result.feedback);
							this.fireEvent('calendarimport', this);
						}else
						{
							Ext.MessageBox.alert(GO.lang.strError,action.result.feedback);
						}
					},
					failure: function(form, action) {
						Ext.MessageBox.alert(GO.lang.strError, action.result.feedback);
					},
					scope: this
				});
			},
			scope: this
		})],
		cls: 'go-form-panel'
	});


	var items = [this.propertiesTab];
	
	if(GO.tasks)
	{
		items.push(this.tasklistsTab);
	}

	items.push(this.readPermissionsTab);
	items.push(this.importTab);

	this.tabPanel = new Ext.TabPanel({
		hideLabel:true,	
		deferredRender:false,
		xtype:'tabpanel',
		activeTab: 0,
		border:false,
		anchor: '100% 100%',
		items:items
	});

	
	GO.calendar.CalendarDialog.superclass.constructor.call(this,{
		title: GO.calendar.lang.calendar,
		layout:'fit',
		modal:false,
		height:500,
		width:500,
		closeAction:'hide',
		items: this.tabPanel,
		buttons:[
		{
			text:GO.lang.cmdOk,
			handler: function(){
				this.save(true)
			},
			scope: this
		},
		{
			text:GO.lang.cmdApply,
			handler: function(){
				this.save(false)
			},
			scope: this
		},

		{
			text:GO.lang.cmdClose,
			handler: function(){
				this.hide()
			},
			scope: this
		}
		]
	});

	this.addEvents({calendarimport:true});
}

Ext.extend(GO.calendar.CalendarDialog, GO.Window, {

	resource: 0,
    
	initComponent : function(){
		
		this.addEvents({
			'save' : true
		});
		
		GO.calendar.CalendarDialog.superclass.initComponent.call(this);	
		
	},				
	show : function (calendar_id, resource){		
		if(!this.rendered)
			this.render(Ext.getBody());
			
		this.propertiesTab.show();       

		if(resource && !this.selectGroup.store.loaded)
		{
			this.selectGroup.store.load();
		}

		this.resource = (resource > 0) ? resource : 0;

		var title = (this.resource) ? GO.calendar.lang.resource : GO.calendar.lang.calendar;
		this.setTitle(title);

		if(calendar_id > 0)
		{
			if(calendar_id!=this.calendar_id)
			{
				this.loadCalendar(calendar_id);
			}else
			{
				GO.calendar.CalendarDialog.superclass.show.call(this);
			}                                   
		}else
		{
			this.calendar_id=0;
			this.propertiesTab.form.reset();

			if(resource){
				this.selectGroup.selectFirst();
			}else
			{
				this.selectGroup.setValue(0);
			}
            
			this.exportButton.setDisabled(true);
			this.importTab.setDisabled(true);	

			this.readPermissionsTab.setDisabled(true);

			this.showGroups(resource);

			GO.calendar.CalendarDialog.superclass.show.call(this);
		}
	},
	loadCalendar : function(calendar_id)
	{
		if(GO.tasks)
		{
			this.tasklistsTab.store.loaded = false;
			this.tasklistsTab.store.baseParams.calendar_id = calendar_id;
		}

		this.propertiesTab.form.load({
			url: GO.settings.modules.calendar.url+'json.php',
			params: {
				calendar_id:calendar_id,
				task: 'calendar'
			},
			waitMsg:GO.lang.waitMsgLoad,
			success: function(form, action) {
				this.calendar_id=calendar_id;
				this.selectUser.setRawValue(action.result.data.user_name);
				this.readPermissionsTab.setAcl(action.result.data.acl_id);
				this.exportButton.setDisabled(false);
				this.importTab.setDisabled(false);

				if(action.result.data.tasklist_name)
					this.selectTasklist.setRemoteText(action.result.data.tasklist_name);

				this.showGroups(action.result.data.group_id > 1);

				GO.calendar.CalendarDialog.superclass.show.call(this);
			},
			failure:function(form, action)
			{
				Ext.Msg.alert(GO.lang.strError, action.result.feedback)
			},
			scope: this
		});
	},
	save : function(hide)
	{        
		if(this.resource && this.name.getValue() && !this.selectGroup.getValue())
		{
			Ext.MessageBox.alert(GO.lang.strError, GO.calendar.lang.no_group_selected);
		}else
		{
			var tasklists = (GO.tasks && !this.resource) ? Ext.encode(this.tasklistsTab.getGridData()) : '';
		
			this.propertiesTab.form.submit({
				url:GO.settings.modules.calendar.url+'action.php',
				params: {
					'task' : 'save_calendar',
					'calendar_id': this.calendar_id,
					'tasklists':tasklists
				},
				waitMsg:GO.lang.waitMsgSave,
				success:function(form, action){

					if(action.result.calendar_id)
					{
						this.calendar_id=action.result.calendar_id;
						this.readPermissionsTab.setAcl(action.result.acl_id);
						this.exportButton.setDisabled(false);
						this.importTab.setDisabled(false);
					//this.loadAccount(this.calendar_id);
					}

					if(GO.tasks)
					{
						this.tasklistsTab.store.commitChanges();
					}

					this.fireEvent('save', this, this.selectGroup.getValue());

					if(hide)
					{
						this.hide();
					}
				},

				failure: function(form, action) {
					var error = '';
					if(action.failureType=='client')
					{
						error = GO.lang.strErrorsInForm;
					}else
					{
						error = action.result.feedback;
					}

					Ext.MessageBox.alert(GO.lang.strError, error);
				},
				scope:this

			});
		}
			
	},
	showGroups : function(resource)
	{
		var f = this.propertiesTab.form.findField('resource_groups');
		f.container.up('div.x-form-item').setDisplayed(resource);

		f = this.propertiesTab.form.findField('show_bdays');
		f.container.up('div.x-form-item').setDisplayed(!resource);

		if(GO.tasks)
		{
			if(resource)
			{
				this.tabPanel.hideTabStripItem('calendar_visible_tasklists');
			}else
			{
				this.tabPanel.unhideTabStripItem('calendar_visible_tasklists');
			}
		}
	}
});
