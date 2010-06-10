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
	config.resizable=false;
	config.width=500;
	config.autoHeight=true;
	config.closeAction='hide';
	config.title= GO.reminders.lang.reminder;					
	config.items= this.formPanel;
	config.buttons=[{
		text: GO.lang['cmdOk'],
		handler: function(){
			this.submitForm(true);
		},
		scope: this
	},{
		text: GO.lang['cmdApply'],
		handler: function(){
			this.submitForm();
		},
		scope:this
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
		this.formPanel.items.items[0].focus();
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
		this.setReminderId(reminder_id);
		if(this.reminder_id>0)
		{
			this.formPanel.load({
				url : GO.settings.modules.reminders.url+'json.php',
				waitMsg:GO.lang['waitMsgLoad'],
				success:function(form, action)
				{					
					this.selectUser.setRemoteText(action.result.data.user_name);
					this.selectGroup.setRemoteText(action.result.data.group_name);
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
				}				
				this.fireEvent('save', this, this.reminder_id);				
				if(hide)
				{
					this.hide();	
				}
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
		this.formPanel = new Ext.FormPanel({
			waitMsgTarget:true,
			url: GO.settings.modules.reminders.url+'action.php',
			border: false,
			baseParams: {
				task: 'reminder'
			},
			bodyStyle:'padding:5px',
			items:[{
					html:GO.reminders.lang.text,
					bodyStyle:'font-size:12px;padding-bottom:10px;',
					border:false
			},
			this.selectUser = new GO.form.SelectUser({
				fieldLabel: GO.lang['strUser'],
				startBlank:true,
				anchor: '100%',
				allowBlank:true
			})
			,this.selectGroup = new  GO.form.SelectGroup({
				name: 'group_id',
				anchor: '100%',
				allowBlank:true
			})
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
				xtype: 'textfield',
				name: 'name',
				anchor: '100%',
				fieldLabel: GO.lang.strName,
				allowBlank:false
			}
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
			}
			
			]
		});
	}
});
