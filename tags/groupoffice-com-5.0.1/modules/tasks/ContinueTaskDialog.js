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
 
GO.tasks.ContinueTaskDialog = Ext.extend(GO.dialog.TabbedFormDialog , {
		
	initComponent : function(){
		
		Ext.apply(this, {
			//autoHeight:true,
			height:540,
			goDialogId:'continuetask',
			title:GO.tasks.lang.continueTask,
			formControllerUrl: 'tasks/task'
		});
		
		GO.tasks.ContinueTaskDialog.superclass.initComponent.call(this);
		
		this.formPanel.baseParams.remind = 'on';
	},
	beforeSubmit: function(params){
		this.formPanel.baseParams.remind_date = this.formPanel.baseParams.due_time;
	},
	
	beforeLoad: function(remoteModelId,config) {
		this.formPanel.baseParams.due_time=this.datePicker.getValue().format(GO.settings.date_format);
	},
	
	buildForm : function () {

		var now = new Date();
		var tomorrow = now.add(Date.DAY, 1);
		var eight = Date.parseDate(tomorrow.format('Y-m-d')+' 08:00', 'Y-m-d G:i' );

		this.datePicker = new Ext.DatePicker({
					internalRender:true,
					xtype:'datepicker',
					name:'due_time',
					format: GO.settings.date_format,
					fieldLabel:GO.lang.strDate
				});

		this.datePicker.setValue(tomorrow);

		this.datePicker.on("select", function(DatePicker, DateObj){
				this.formPanel.baseParams.due_time=DateObj.format(GO.settings.date_format);		
		},this);
		this.propertiesPanel = new Ext.Panel({
			border: false,
			cls:'go-form-panel',
			layout:'form',
			waitMsgTarget:true,			
			items:[{
					items:this.datePicker,
					width:240,
					style:'margin:auto;'
				},new GO.form.HtmlComponent({html:'<br />'}),{
					xtype:'timefield',
					name:'remind_time',
					width:220,
					format: GO.settings.time_format,
					value:eight.format(GO.settings['time_format']),
					fieldLabel:GO.lang.strTime,
				anchor:'100%'
				},new GO.tasks.SelectTaskStatus({anchor:'100%'}),{
					xtype: 'textarea',
					name: 'comment',
					anchor: '100%',
					width:300,
					height:100,
					fieldLabel: GO.lang.strDescription
				},
				this.selectTaskList = new GO.tasks.SelectTasklist({fieldLabel: GO.tasks.lang.tasklist, anchor:'100%'})]				
		});

		this.addPanel(this.propertiesPanel);
	}
});
