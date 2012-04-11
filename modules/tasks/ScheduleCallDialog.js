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
 
GO.tasks.ScheduleCallDialog = Ext.extend(GO.dialog.TabbedFormDialog , {
		
	initComponent : function(){
		
		Ext.apply(this, {
			autoHeight:true,
			goDialogId:'task',
			title:GO.tasks.lang.scheduleCall,
			formControllerUrl: 'tasks/task'
		});
		
		GO.tasks.ScheduleCallDialog.superclass.initComponent.call(this);	
		
		this.formPanel.baseParams.remind_date=this.datePicker.getValue().format(GO.settings.date_format);
	},
	beforeSubmit: function(params){
		params.name = GO.tasks.lang.call+': '+this.showConfig.link_config.name;
		params.link = this.showConfig.link_config.model_name + ':' + this.showConfig.link_config.model_id;
		params.remind = 'on';
	},
//	afterSubmit: function(action){
//		if(this.showConfig.callback)
//		{					
//			this.showConfig.callback.call(this.showConfig.scope);					
//		}
//	},
	buildForm : function () {

		var now = new Date();
		var tomorrow = now.add(Date.DAY, 1);
		var eight = Date.parseDate(tomorrow.format('Y-m-d')+' 08:00', 'Y-m-d G:i' );

		this.datePicker = new Ext.DatePicker({
					xtype:'this.datePicker',
					name:'remind_date',
					format: GO.settings.date_format,
					fieldLabel:GO.lang.strDate
				});

		this.datePicker.setValue(tomorrow);
		
		this.datePicker.on("select", function(datePicker, DateObj){						
				this.formPanel.baseParams.remind_date=this.formPanel.baseParams.due_time=DateObj.format(GO.settings.date_format);	
		},this);
		this.propertiesPanel = new Ext.Panel({
			autoHeight:true,
			border: false,
//			baseParams: {date: tomorrow.format(GO.settings.date_format), name: 'TEST'},			
			cls:'go-form-panel',
			layout:'form',
			waitMsgTarget:true,			
			items:[{
					items:this.datePicker,
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
				},{
					xtype: 'textarea',
					name: 'description',
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