/** 
 * Copyright Intermesh
 * 
 * This file is part of Group-Office. You should have received a copy of the
 * Group-Office license along with Group-Office. See the file /LICENSE.TXT
 * 
 * If you have questions write an e-mail to info@intermesh.nl
 * 
 * @copyright Copyright Intermesh
 * @version $Id: TaskDialog.js 2635 2008-07-18 11:00:31Z mschering $
 * @author Merijn Schering <mschering@intermesh.nl>
 */

 
GO.tasks.TaskDialog = function(){
	//this.tasklist=tasklist;
	
	
	//Create the standard GO linkspanel
	this.linksPanel = new GO.grid.LinksPanel({title: GO.lang['strLinks']});
	
	if(GO.files)
	{
		this.fileBrowser = new GO.files.FileBrowser({
			title: GO.lang.strFiles, 
			treeRootVisible:true, 
			treeCollapsed:true,
			disabled:true
			});
	}

	this.buildForm();
	
	var focusName = function(){
		this.nameField.focus();		
	};
	
	
	
	this.win = new Ext.Window({
			layout:'fit',
			modal:false,
			resizable:false,
			width:560,
			height:400,
			closeAction:'hide',
			title: GO.tasks.lang.task,					
			items: this.formPanel,
			focus: focusName.createDelegate(this),
			buttons:[{
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
						this.win.hide();
					},
					scope:this
				}					
			]/*,
			keys: [{
	            key: Ext.TaskObject.ENTER,
	            fn: function(){
	            	this.submitForm();
					this.win.hide();
	            },
	            scope:this
	        }]*/
		});

		this.win.render(Ext.getBody());
		
		GO.tasks.TaskDialog.superclass.constructor.call(this);
		
		this.addEvents({'save' : true});

	
}

Ext.extend(GO.tasks.TaskDialog, Ext.util.Observable,{

	
	show : function (config) {
		
		if(!config)
		{
			config={};
		}
		
		propertiesPanel.show();
		
		
		
		if(!config.task_id)
		{
			config.task_id=0;
			
		}
		
		
		
		
		
		
		this.setTaskId(config.task_id);
		
		if(config.task_id>0)
		{
			this.formPanel.load({
				url : GO.settings.modules.tasks.url+'json.php',
				waitMsg:GO.lang['waitMsgLoad'],
				success:function(form, action)
				{
										
					this.win.show();
					this.changeRepeat(action.result.data.repeat_type);
					this.setValues(config.values);
					
					this.selectTaskList.setRemoteText(action.result.data.tasklist_name);
					if(GO.files)
					{
						this.fileBrowser.setRootPath(action.result.data.files_path);
						this.fileBrowser.setDisabled(false);
					}
					this.setWritePermission(action.result.data.write_permission);

					
				},
				failure:function(form, action)
				{
					Ext.Msg.alert(GO.lang['strError'], action.result.feedback)
				},
				scope: this
				
			});
		}else
		{
			delete this.formPanel.form.baseParams['exception_task_id'];
			delete this.formPanel.form.baseParams['exceptionDate'];
			
			this.lastTaskListId=this.selectTaskList.getValue();
			
			this.formPanel.form.reset();
			this.linksPanel.setDisabled(true);
			
			
			this.setWritePermission(true);
			
			if(GO.files)
				this.fileBrowser.setDisabled(true);
			
			this.win.show();
			this.setValues(config.values);
			
			
			
		}
		
		//if the newMenuButton from another passed a linkTypeId then set this value in the select link field
		if(config.link_config)
		{
			this.link_config=config.link_config;
			if(config.link_config.type_id)
			{
				this.selectLinkField.setValue(config.link_config.type_id);
				this.selectLinkField.setRemoteText(config.link_config.text);
			}
		}else
		{
			delete this.link_config;
		}
		
		
		if(config.tasklist_id)
		{
			//this.formPanel.form.baseParams['tasklist_id']=config.tasklist_id;
			this.selectTaskList.setValue(config.tasklist_id);
			this.selectTaskList.setRawValue(config.tasklist_name);
		}else
		{
			this.selectTaskList.setValue(this.lastTaskListId);
		}
		
	},
	
	setWritePermission : function(writePermission)
	{
		this.win.buttons[0].setDisabled(!writePermission);
		this.win.buttons[1].setDisabled(!writePermission);
		this.linksPanel.setWritePermission(writePermission);
	},
	
	setValues : function(values)
	{
		if(values)
		{
			for(var key in values)
			{
				var field = this.formPanel.form.findField(key);
				if(field)
				{
					field.setValue(values[key]);
				}
			}
		}
		
	},
	setTaskId : function(task_id)
	{
		this.formPanel.form.baseParams['task_id']=task_id;
		this.task_id=task_id;
		this.linksPanel.loadLinks(task_id, 12);
	},
	
	setCurrentDate : function()
	{
		var formValues={};
		
		var date = new Date();
				
		formValues['start_date'] = date.format(GO.settings['date_format']);					
		formValues['start_hour'] = date.format("H");
		formValues['start_min'] = '00';
		
		formValues['end_date'] = date.format(GO.settings['date_format']);
		formValues['end_hour'] = date.add(Date.HOUR,1).format("H");
		formValues['end_min'] = '00';
		
		this.formPanel.form.setValues(formValues);
		
	},
	
	submitForm : function(hide){
		this.formPanel.form.submit(
		{
			url:GO.settings.modules.tasks.url+'action.php',
			params: {'task' : 'save_task'},
			waitMsg:GO.lang['waitMsgSave'],
			success:function(form, action){				
				
				if(action.result.task_id)
				{
					this.setTaskId(action.result.task_id);
					
					if(GO.files && action.result.files_path)
					{
						this.fileBrowser.setRootPath(action.result.files_path);
						this.fileBrowser.setDisabled(false);
					}					
				}
				
				if(this.link_config && this.link_config.callback)
				{					
					this.link_config.callback.call(this);					
				}
				
				this.fireEvent('save', this);	
				
				if(hide)
				{
					this.win.hide();
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
		var hours =[
                	['00','00'],
                	['01','01'],
                	['02','02'],
                	['03','03'],
                	['04','04'],
                	['05','05'],
                	['06','06'],
                	['07','07'],
                	['08','08'],
                	['09','09'],
                	['10','10'],
                	['11','11'],
                	['12','12'],
                	['13','13'],
                	['14','14'],
                	['15','15'],
                	['16','16'],
                	['17','17'],
                	['18','18'],
                	['19','19'],
                	['20','20'],
                	['21','21'],
                	['22','22'],
                	['23','23']];
    var minutes = [['00','00'],['15','15'],['30','30'],['45','45']];
		
	//	Ext.QuickTips.init();

    // turn on validation errors beside the field globally
    //Ext.form.Field.prototype.msgTarget = 'qtip';

		this.nameField = new Ext.form.TextField({
        name: 'name',
        allowBlank:false,
        fieldLabel: GO.lang.strSubject
  	});    

    this.selectLinkField = new GO.form.SelectLink();
    	
        		

    var description = new Ext.form.TextArea({
        name: 'description',
      	height:100,
        allowBlank:true,
        fieldLabel: GO.lang.strDescription
    	});
    	       	
    var now = new Date();
    
    var startDate = new Ext.form.DateField({    	
      name: 'start_date',
      format: GO.settings['date_format'],
      allowBlank:false,
      fieldLabel: GO.tasks.lang.startsAt,
      value: now.format(GO.settings.date_format)
  	});
    
    var dueDate = new Ext.form.DateField({    	
      name: 'due_date',
      format: GO.settings['date_format'],
      allowBlank:false,
      fieldLabel: GO.tasks.lang.dueAt,
      value: now.format(GO.settings.date_format)
  	});
    					
    	
    	


  	var taskStatus = new Ext.form.ComboBox({
  			name:'status_text',       
      	hiddenName:'status',
				triggerAction: 'all',
         editable:false,
        selectOnFocus:true,
        forceSelection:true,
        fieldLabel: GO.lang.strStatus,
        mode:'local',
        value:'ACCEPTED',
        valueField:'value',
        displayField:'text',
        store: new Ext.data.SimpleStore({
        fields: ['value', 'text'],
        data: [
        	['NEEDS-ACTION', GO.tasks.lang.needsAction],
        	['ACCEPTED', GO.tasks.lang.accepted],
        	['DECLINED', GO.tasks.lang.declined],
        	['TENTATIVE', GO.tasks.lang.tentative],
        	['DELEGATED', GO.tasks.lang.delegated],
        	['COMPLETED', GO.tasks.lang.completed],
        	['IN-PROCESS', GO.tasks.lang.inProcess]
         ]
        })
      });	
      
      
    this.selectTaskList = new GO.tasks.SelectTasklist({fieldLabel: GO.tasks.lang.tasklist});




		propertiesPanel = new Ext.Panel({
			hideMode:'offsets',
			title:GO.lang['strProperties'],
			defaults:{anchor:'-20'},
			//cls:'go-form-panel',
			bodyStyle: 'padding:5px',
			layout:'form',
			autoScroll:true,
			items:[
				this.nameField,	
	    	this.selectLinkField,
	    	description,
	    	startDate,
	    	dueDate,
	    	taskStatus,
	    	this.selectTaskList			
			]
				
			});
    	

				        
        //Start of recurrence tab
        
        this.repeatEvery = new Ext.form.ComboBox({
        	
            fieldLabel:GO.tasks.lang.repeatEvery,
						name:'repeat_every_text',
        		hiddenName: 'repeat_every',        
						triggerAction: 'all',
						editable: false,
			      selectOnFocus:true,
            width:50,
            forceSelection:true,
            mode:'local',
            value:'1',
            valueField:'value',
            displayField:'text',
            
            store: new Ext.data.SimpleStore({
	            fields: ['value', 'text'],
	            data: [
	            	['1', '1'],
	            	['2', '2'],
	            	['3', '3'],
	            	['4', '4'],
	            	['5', '5'],
	            	['6', '6'],
	            	['7', '7'],
	            	['8', '8'],
	            	['9', '9'],
	            	['10', '10'],
	            	['11', '11'],
	            	['12', '12']
	            ]
	        })
        });	

        
        this.repeatType = new Ext.form.ComboBox({
        	hiddenName: 'repeat_type',        
			triggerAction: 'all',
			editable: false,
            selectOnFocus:true,
            width:200,
            forceSelection:true,
            mode:'local',
            value:'0',
            valueField:'value',
            displayField:'text',
            store: new Ext.data.SimpleStore({
	            fields: ['value', 'text'],
	            data: [
	            	['0', GO.lang.noRecurrence],
	            	['1', GO.lang.strDays],
	            	['2', GO.lang.strWeeks],
	            	['3', GO.lang.monthsByDate],
	            	['4', GO.lang.monthsByDay],
	            	['5', GO.lang.strYears]
	            ]
	        }),
	        hideLabel:true,
	        laelSeperator:''
        });	
        
        this.repeatType.on('select', function(combo, record){this.changeRepeat(record.data.value);}, this);
	        
        
        this.monthTime = new Ext.form.ComboBox({
        	hiddenName:'month_time',                  
					triggerAction: 'all',
          selectOnFocus:true,
          disabled: true,
          width:80,
          forceSelection:true,
	        fieldLabel: GO.tasks.lang.atDays,
	        mode:'local',
          value:'1',
          valueField:'value',
          displayField:'text',
          store: new Ext.data.SimpleStore({
	        fields: ['value', 'text'],
	        data: [
	            	['1', GO.lang.strFirst],
	            	['2', GO.lang.strSecond],
	            	['3', GO.lang.strThird],
	            	['4', GO.lang.strFourth]
	            ]
	        })
        });	
   
   		var cb = [];
        for(var day=0;day<7;day++)
        {
        	cb[day] = new Ext.form.Checkbox({
	            boxLabel:GO.lang.shortDays[day],
	            name:'repeat_days_'+day,
	            disabled: true,
	            checked:false,
	            width:'auto',
		        hideLabel:true,
		        laelSeperator:''
        	});
        	
        	
        }
        
        this.repeatEndDate = new Ext.form.DateField({
            name: 'repeat_end_date',
            width:100,
            disabled: true,
            format: GO.settings['date_format'],
            allowBlank:true,
            fieldLabel:GO.tasks.lang.repeatUntil
    	});

        this.repeatForever = new Ext.form.Checkbox({
            boxLabel: GO.tasks.lang.repeatForever,
            name:'repeat_forever',
            checked:true,
            disabled:true,
            width:'auto',
	        hideLabel:true,
	        laelSeperator:''
    	});

        
        
        var recurrencePanel = new Ext.Panel({
		
			title:GO.tasks.lang.recurrence,
			bodyStyle:'padding: 5px',
			layout:'form',			
			hideMode:'offsets',
			autoScroll:true,
			items:[
			{
    		border:false,
    		layout:'table',
				defaults: { 
					border: false, 
					layout: 'form', 
					bodyStyle:'padding-right:3px' 
				},
				items: [
	    			{items:this.repeatEvery},
	    			{items:this.repeatType}
	    			]
	    	},{
	    		border:false,
	    		layout:'table',
				defaults: { 
					border: false, 
					layout: 'form', 
					bodyStyle:'padding-right:3px;white-space:nowrap'
				},
				
				items: [
	    			{items:this.monthTime},
	    			{items:cb[0]},
	    			{items:cb[1]},
	    			{items:cb[2]},
	    			{items:cb[3]},
	    			{items:cb[4]},
	    			{items:cb[5]},
	    			{items:cb[6]}	    			
	    			]
	    	},{
	    		border:false,
	    		layout:'table',
				defaults: { 
					border: false, 
					layout: 'form', 
					bodyStyle:'padding-right:3px' 
				},
				items: [
	    			{items:this.repeatEndDate},
	    			{items:this.repeatForever}
	    			]
	    	}
			
			]
        });
        
        
        
        
        
        //start other options tab
        

		var reminderValues = [['0','No reminder']];
		
		for(var i=1;i<60;i++)
		{
			reminderValues.push([i,i]);
		}
        
        var reminderValue = new Ext.form.ComboBox({
        	fieldLabel:GO.tasks.lang.reminder,
        	hiddenName:'reminder_value',          
			triggerAction: 'all',
            editable:false,
            selectOnFocus:true,
            width:148,
            forceSelection:true,
            mode:'local',
            value:'0',
            valueField:'value',
            displayField:'text',
            store: new Ext.data.SimpleStore({
	            fields: ['value', 'text'],
	            data: reminderValues
	        })
        });	

        
        var reminderMultiplier = new Ext.form.ComboBox({

        	hiddenName: 'reminder_multiplier',            
			triggerAction: 'all',
            editable:false,
            selectOnFocus:true,
            width:148,
            forceSelection:true,
            mode:'local',
            value:'60',
            valueField:'value',
            displayField:'text',
            store: new Ext.data.SimpleStore({
	            fields: ['value', 'text'],
	            data: [
	            	['60',GO.lang.strMinutes],
	            	['3600',GO.lang.strHours],
	            	['86400',GO.lang.strDays]
	            	
	            ]
	        }),
	        hideLabel:true,
	        labelSeperator:''
        });	
        
        
        


		
		var optionsPanel = new Ext.Panel({
		
			title:GO.tasks.lang.options,
			defaults:{anchor:'100%'},
			bodyStyle:'padding:5px',
			layout:'form',
			hideMode:'offsets',
			autoScroll:true,
			items:[
			{
	    		border:false,
	    		layout:'table',
					defaults: { 
					border: false, 
					layout: 'form', 
					bodyStyle:'padding-right:3px' 
				},
				items: [
	    			{items:reminderValue},
	    			{items:reminderMultiplier}
	    			]
	    	}
	    				
			]
        });

       
      var items = [
        	propertiesPanel,
        	recurrencePanel,
        	optionsPanel,
        	this.linksPanel        	
        ];
        
      if(GO.files)
      	items.push(this.fileBrowser);
        
      this.tabPanel = new Ext.TabPanel({
        activeTab: 0,
        deferredRender: false,
        //layoutOnTabChange:true,
      	border: false,
      	anchor: '100% 100%',
      	hideLabel:true,
        items: items
      }) ;
      
    
        

    this.formPanel = new Ext.form.FormPanel(
		{
			url: GO.settings.modules.tasks.url+'action.php',
			border: false,
			baseParams: {task: 'task'},
      items: this.tabPanel
		});
		
		/*this.formPanel.form.on('actioncomplete', function(form, action){
			if(action.type=='load')
			{
				this.formPanel.form.baseParams['tasklist_id']=action.result.data.tasklist_id;
				this.changeRepeat(action.result.data.repeat_type);
				//linksPanel.loadLinks(action.result.data['link_id'], 1);
			}		
		},this);*/
      

	},
	

	changeRepeat : function(value){
		
		var form = this.formPanel.form;
        switch(value)
		{
			case '0':
				this.disableDays(true);
				this.monthTime.setDisabled(true);
				this.repeatForever.setDisabled(true);
				this.repeatEndDate.setDisabled(true);					
				this.repeatEvery.setDisabled(true);
			break;
	
			case '1':
				this.disableDays(true);
				this.monthTime.setDisabled(true);
				this.repeatForever.setDisabled(false);
				this.repeatEndDate.setDisabled(false);					
				this.repeatEvery.setDisabled(false);
				
			break;
	
			case '2':
				this.disableDays(false);
				this.monthTime.setDisabled(true);
				this.repeatForever.setDisabled(false);
				this.repeatEndDate.setDisabled(false);					
				this.repeatEvery.setDisabled(false);

			break;
	
			case '3':
				this.disableDays(true);
				this.monthTime.setDisabled(true);
				this.repeatForever.setDisabled(false);
				this.repeatEndDate.setDisabled(false);					
				this.repeatEvery.setDisabled(false);
			
			break;
	
			case '4':
				this.disableDays(false);
				this.monthTime.setDisabled(false);
				this.repeatForever.setDisabled(false);
				this.repeatEndDate.setDisabled(false);					
				this.repeatEvery.setDisabled(false);
			break;
	
			case '5':
				this.disableDays(true);
				this.monthTime.setDisabled(true);
				this.repeatForever.setDisabled(false);
				this.repeatEndDate.setDisabled(false);					
				this.repeatEvery.setDisabled(false);
			break;
		}	        	
    },
    disableDays : function(disabled){
    	for(var day=0;day<7;day++)
        {
        	this.formPanel.form.findField('repeat_days_'+day).setDisabled(disabled);
        }
    		    	
    }


});