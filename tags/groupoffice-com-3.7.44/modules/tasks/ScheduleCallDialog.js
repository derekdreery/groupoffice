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
 
GO.tasks.ScheduleCallDialog = function(config){
	
	
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
	config.title= GO.tasks.lang.scheduleCall;					
	config.items= this.formPanel;
	config.focus= focusFirstField.createDelegate(this);
	config.buttons=[{
			text: GO.lang['cmdOk'],
			handler: function(){
				this.submitForm();
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

	
	GO.tasks.ScheduleCallDialog.superclass.constructor.call(this, config);
	
	
	this.addEvents({'save' : true});	
}

Ext.extend(GO.tasks.ScheduleCallDialog, Ext.Window,{

	link_config : {},	
	show : function (linkConfig) {

		this.linkConfig = linkConfig;
		
		if(!this.rendered)
			this.render(Ext.getBody());		

		
		this.formPanel.form.reset();		
		
		
		this.selectTaskList.setValue(GO.tasks.defaultTasklist.id);
		this.selectTaskList.setRemoteText(GO.tasks.defaultTasklist.name);

		GO.tasks.ScheduleCallDialog.superclass.show.call(this);

	},
	
	submitForm : function(){
		this.formPanel.form.submit(
		{
			url:GO.settings.modules.tasks.url+'action.php',
			params: {
				'task' : 'schedule_call',
				'links' : Ext.encode(this.linkConfig.links),
				'name' : GO.tasks.lang.call+': '+this.linkConfig.name
			},
			waitMsg:GO.lang['waitMsgSave'],
			success:function(form, action){				
				
				if(this.linkConfig.callback)
				{					
					this.linkConfig.callback.call(this.linkConfig.scope);					
				}
				
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
	    		fieldLabel:GO.lang.strDate
	    		
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
	    	},{
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



GO.tasks.ScheduleCallMenuItem = Ext.extend(Ext.menu.Item,{
	linkConfig : { name : '', links: [{link_id:0, link_type:0}]},
	
	initComponent : function(){
		this.iconCls= 'tasks-call';
		this.text= GO.tasks.lang.scheduleCall;
		this.cls='x-btn-text-icon';
		this.disabled=true;
		this.handler= function()
		{
			if(!GO.tasks.scheduleCallDialog)
			{
				GO.tasks.scheduleCallDialog = new GO.tasks.ScheduleCallDialog();
			}
			GO.tasks.scheduleCallDialog.show(this.linkConfig);			
		};
		
		GO.tasks.ScheduleCallMenuItem.superclass.initComponent.call(this);
	},
	
	setLinkConfig : function(config){
		
		this.linkConfig = config;
		
		this.setDisabled(false);
	}
});