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

GO.tasks.ContinueTaskDialog = function(config){


	if(!config)
	{
		config={};
	}


	this.buildForm();

	var focusFirstField = function(){
		this.formPanel.items.items[0].focus();
	};

	config.layout='fit';
	config.modal=false;
	config.width=500;
	config.autoHeight=true;
	config.closeAction='hide';
	config.title= GO.tasks.lang.continueTask;
	config.items= this.formPanel;
	config.focus= focusFirstField.createDelegate(this);
	config.buttons=[{
			text: GO.lang['cmdOk'],
			handler: function(){
				this.submitForm();
			},
			scope: this
		},{
			text: GO.lang['cmdCancel'],
			handler: function(){
				this.hide();
			},
			scope:this
		}
	];


	GO.tasks.ContinueTaskDialog.superclass.constructor.call(this, config);


	this.addEvents({'save' : true});
}

Ext.extend(GO.tasks.ContinueTaskDialog, Ext.Window,{

	show : function (task) {

		this.task_id = task.id;

		if(!this.rendered)
			this.render(Ext.getBody());


		delete task.description;
		
		this.formPanel.form.reset();
		this.formPanel.form.setValues(task);

		this.selectTaskList.setValue(task.tasklist_id);
		this.selectTaskList.setRemoteText(task.tasklist_name);
		
		GO.tasks.ContinueTaskDialog.superclass.show.call(this);

	},

	submitForm : function(){
		this.formPanel.form.submit(
		{
			url:GO.settings.modules.tasks.url+'action.php',
			params: {
				'task' : 'continue_task',
				'task_id' : this.task_id
			},
			waitMsg:GO.lang['waitMsgSave'],
			success:function(form, action){
				this.fireEvent('save', this);
				this.hide();
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

		var now = new Date();
		var tomorrow = now.add(Date.DAY, 1);
		var eight = Date.parseDate(tomorrow.format('Y-m-d')+' 08:00', 'Y-m-d G:i' );

		var datePicker = new Ext.DatePicker({
	    		xtype:'datepicker',
	    		name:'remind_date',
	    		format: GO.settings.date_format,
	    		fieldLabel:GO.tasks.lang.dueDate
	    	});

	  datePicker.setValue(tomorrow);

	  datePicker.on("select", function(DatePicker, DateObj){
				this.formPanel.baseParams.date=DateObj.format(GO.settings.date_format);
		},this);

		this.formPanel = new Ext.form.FormPanel({
			url: GO.settings.modules.tasks.url+'action.php',
			border: false,
			baseParams: {task: 'note', date: tomorrow.format(GO.settings.date_format)},
			cls:'go-form-panel',
			waitMsgTarget:true,
			autoHeight:true,
			items:[{
					items:datePicker,
					width:220,
					style:'margin:auto;'
				},new GO.form.HtmlComponent({html:'<br />'}),{
	    		xtype:'timefield',
	    		name:'remind_time',
	    		format: GO.settings.time_format,
	    		value:eight.format(GO.settings['time_format']),
	    		fieldLabel:GO.lang.strTime,
	    		anchor:'100%'
	    	},new GO.tasks.SelectTaskStatus(),{
					xtype: 'textarea',
				  name: 'description',
					anchor: '100%',
					height:100,
				  fieldLabel: GO.lang.strDescription
				},
				this.selectTaskList = new GO.tasks.SelectTasklist({fieldLabel: GO.tasks.lang.tasklist, anchor:'100%'})]
		});

	}
});