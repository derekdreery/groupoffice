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
GO.reminders.ReminderDialog = function(config){	
	if(!config)
	{
		config={};
	}
	this.buildForm();
	config.collapsible=true;
	config.maximizable=true;
	config.modal=false;
	config.resizable=true;
	config.width=700;
	config.height=500;
	config.layout='fit';

	
	config.closeAction='hide';
	config.title= GO.reminders.lang.reminder;					
	config.items= [this.formPanel];
	config.buttons=[{
		text: GO.lang['cmdOk'],
		handler: function(){
			this.submitForm(true);
		},
		scope: this
	},{
		text: GO.lang['cmdClose'],
		handler: function(){
			this.hide();
		},
		scope:this
	}
	];
	GO.reminders.ReminderDialog.superclass.constructor.call(this, config);
	this.addEvents({
		'save' : true
	});
}
Ext.extend(GO.reminders.ReminderDialog, GO.Window,{
	focus : function(){
		this.propertiesPanel.items.items[0].focus();
	},
	show : function (reminder_id, config) {
		if(!this.rendered)
		{
			this.render(Ext.getBody());
		}
		this.formPanel.form.reset();
		if(!reminder_id)
		{
			reminder_id=0;			
		}
		this.tabPanel.setActiveTab(0);
		this.setReminderId(reminder_id);
		if(this.reminder_id>0)
		{
			this.usersStore.baseParams.reminder_id=this.reminder_id;
			this.usersStore.load();
			this.usersGrid.setDisabled(false);
			
			this.formPanel.load({
				url : GO.settings.modules.reminders.url+'json.php',
				waitMsg:GO.lang['waitMsgLoad'],
				success:function(form, action)
				{					
					//this.selectUser.setRemoteText(action.result.data.user_name);
					//this.selectGroup.setRemoteText(action.result.data.group_name);
					this.selectLink.setRemoteText(action.result.data.link_name);

					

					GO.reminders.ReminderDialog.superclass.show.call(this);
				},
				failure:function(form, action)
				{
					Ext.Msg.alert(GO.lang['strError'], action.result.feedback)
				},
				scope: this				
			});
		}else 
		{
			this.usersGrid.setDisabled(true);
			this.usersStore.baseParams.reminder_id=0;
			this.usersStore.removeAll();
			GO.reminders.ReminderDialog.superclass.show.call(this);
		}
	},
	setReminderId : function(reminder_id)
	{
		this.formPanel.form.baseParams['reminder_id']=reminder_id;
		this.reminder_id=reminder_id;
	},
	submitForm : function(hide){
		this.formPanel.form.submit(
		{
			url:GO.settings.modules.reminders.url+'action.php',
			params: {
				'task' : 'save_reminder'
			},
			waitMsg:GO.lang['waitMsgSave'],
			success:function(form, action){
				if(action.result.reminder_id)
				{
					this.setReminderId(action.result.reminder_id);
					this.usersStore.baseParams.reminder_id=this.reminder_id;
					this.usersStore.load();
					this.usersGrid.setDisabled(false);

					this.tabPanel.setActiveTab(1);
				}else if(hide)
				{
					this.hide();
				}
			
				this.fireEvent('save', this, this.reminder_id);				
				
			},		
			failure: function(form, action) {
				if(action.failureType == 'client')
				{					
					Ext.MessageBox.alert(GO.lang['strError'], GO.lang['strErrorsInForm']);			
				} else {
					Ext.MessageBox.alert(GO.lang['strError'], action.result.feedback);
				}
			},
			scope: this
		});		
	},
	buildForm : function () {


		this.usersStore = new Ext.data.JsonStore({
			baseParams: {
				'task': 'reminder_users',
				reminder_id : 0
			},
			root: 'results',
			id: 'id',
			totalProperty: 'total',
			fields:['id','name'],
			url: GO.settings.modules.reminders.url+'json.php',
			remoteSort:true
		});

		this.usersGrid = new GO.grid.GridPanel( {
			disabled:true,
			layout:'fit',
			title:GO.lang.users,
			tbar:[{
				iconCls: 'btn-delete',
				text: GO.lang['cmdDelete'],
				cls: 'x-btn-text-icon',
				handler: function(){
					this.usersGrid.deleteSelected();
				},
				scope: this
			},{
				iconCls: 'btn-add',
				text: GO.reminders.lang.addUsers,
				cls: 'x-btn-text-icon',
				handler: function(){

					if(!this.selectUsersWindow){
						this.selectUsersWindow = new GO.dialog.SelectUsers({
							scope:this,
							handler:function(grid){
								var records = grid.getSelectionModel().getSelections();

								var addUsers=[];
								for(var i=0,max=records.length;i<max;i++){
									addUsers.push(records[i].id);
								}
								this.usersStore.baseParams.add_users=Ext.encode(addUsers);
								this.usersStore.load();
								delete this.usersStore.baseParams.add_users;
							}
						});
					}
					this.selectUsersWindow.show();
			
				},
				scope: this
			},{
				iconCls: 'btn-add',
				text: GO.reminders.lang.addUserGroups,
				cls: 'x-btn-text-icon',
				handler: function(){
					if(!this.selectGroupsWindow){
						this.selectGroupsWindow = new GO.dialog.SelectGroups({
							scope:this,
							handler:function(grid){
								var records = grid.getSelectionModel().getSelections();

								var addGroups=[];
								for(var i=0,max=records.length;i<max;i++){
									addGroups.push(records[i].id);
								}
								this.usersStore.baseParams.add_groups=Ext.encode(addGroups);
								this.usersStore.load();
								delete this.usersStore.baseParams.add_groups;
							}
						});
					}
					this.selectGroupsWindow.show();
				},
				scope: this
			}
			],
			paging:true,
			border:true,
			store: this.usersStore,
			columns:[
			{
				header:GO.lang.strName,
				dataIndex: 'name',
				id:'name'
			}],
			autoExpandColumn:'name',
			sm: new Ext.grid.RowSelectionModel(),
			loadMask: true
		});

		this.propertiesPanel = new Ext.Panel({
			layout:'form',
			border: false,
			title:GO.lang.strProperties,
			bodyStyle:'padding:5px',
			items:[
			/*this.selectUser = new GO.form.SelectUser({
				fieldLabel: GO.lang['strUser'],
				startBlank:true,
				anchor: '100%',
				allowBlank:true
			})
			,this.selectGroup = new  GO.form.SelectGroup({
				name: 'group_id',
				anchor: '100%',
				allowBlank:true
			})*/
			{
				xtype: 'textfield',
				name: 'name',
				anchor: '100%',
				fieldLabel: GO.lang.strName,
				allowBlank:false
			}
			,this.selectLink = new GO.form.SelectLink({
				anchor:'100%',
				listeners:{
					scope:this,
					select:function(cb,record, index){
						this.formPanel.form.findField('name').setValue(record.data.type_name);
					}
				}
			})
			,{
				xtype : 'compositefield',
				fieldLabel:GO.reminders.lang.time,
				anchor: '100%',
				items : [{
					xtype: 'datefield',
					name: 'date',
					value: new Date()
				},{
					xtype:'timefield',
					increment: 15,
					format:GO.settings.time_format,
					name:'time',
					value:'8:00',
					width:80,
					hideLabel:true,
					autoSelect :true,
					forceSelection:true
				}]
			}
			,{
				xtype:'combo',
				anchor: '100%',
				fieldLabel: GO.reminders.lang.snoozeTime,
				hiddenName : 'snooze_time',
				store : new Ext.data.ArrayStore({
					idIndex:0,
					fields : ['value', 'text'],
					data : GO.checkerSnoozeTimes
				}),
				value:7200,
				valueField : 'value',
				displayField : 'text',
				mode : 'local',
				triggerAction : 'all',
				editable : false,
				selectOnFocus : true,
				forceSelection : true
			},{
				xtype:'htmleditor',
				name:'text',
				fieldLabel:GO.reminders.lang.text,
				anchor:'100% -105'
			}]
		});

		this.tabPanel = new Ext.TabPanel({
			activeTab: 0,
			deferredRender: false,
			border: false,
			items: [this.propertiesPanel, this.usersGrid],
			anchor: '100% 100%'
		});

		this.formPanel = new Ext.form.FormPanel({
			waitMsgTarget:true,
			border: false,
			url: GO.settings.modules.reminders.url+'action.php',
			baseParams: {
				task: 'reminder'
			},
			items:this.tabPanel
		});
	}
});
