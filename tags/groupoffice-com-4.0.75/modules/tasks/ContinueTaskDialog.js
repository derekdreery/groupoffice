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
			autoHeight:true,
			goDialogId:'task',
			title:GO.tasks.lang.continueTask,
			formControllerUrl: 'tasks/task'
		});
		
		GO.tasks.ContinueTaskDialog.superclass.initComponent.call(this);
		
		this.formPanel.baseParams.remind = 'on';
	},
	beforeSubmit: function(params){
		this.formPanel.baseParams.remind_date = this.formPanel.baseParams.due_time;
	},
	buildForm : function () {

		var now = new Date();
		var tomorrow = now.add(Date.DAY, 1);
		var eight = Date.parseDate(tomorrow.format('Y-m-d')+' 08:00', 'Y-m-d G:i' );

		var datePicker = new Ext.DatePicker({
					xtype:'datepicker',
					name:'due_time',
					format: GO.settings.date_format,
					fieldLabel:GO.lang.strDate
				});

		datePicker.setValue(tomorrow);

		datePicker.on("select", function(DatePicker, DateObj){
				this.formPanel.baseParams.due_time=DateObj.format(GO.settings.date_format);		
		},this);
		this.propertiesPanel = new Ext.Panel({
			autoHeight:true,
			border: false,
			cls:'go-form-panel',
			layout:'form',
			waitMsgTarget:true,			
			items:[{
					items:datePicker,
					width:220,
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