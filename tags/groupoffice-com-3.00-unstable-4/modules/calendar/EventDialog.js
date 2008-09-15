/** 
 * Copyright Intermesh
 * 
 * This file is part of Group-Office. You should have received a copy of the
 * Group-Office license along with Group-Office. See the file /LICENSE.TXT
 * 
 * If you have questions write an e-mail to info@intermesh.nl
 * 
 * @version $Id: EventDialog.js 2507 2008-07-14 14:05:13Z mschering $
 * @copyright Copyright Intermesh
 * @author Merijn Schering <mschering@intermesh.nl>
 */




 
GO.calendar.EventDialog = function(calendar){
	this.calendar=calendar;
	
	
	//Create the standard GO linkspanel
	this.linksPanel = new GO.grid.LinksPanel({title: GO.lang['strLinks']});
	
	if(GO.files)
	{
		this.fileBrowser = new GO.files.FileBrowser({
			title: GO.lang.strFiles, 
			treeRootVisible:true, 
			loadDelayed:true,
			treeCollapsed:true,
			disabled:true
			});
	}

	this.buildForm();
	
	
	var focusSubject = function(){
		this.subjectField.focus();
	}
	
	this.win = new Ext.Window({
			layout:'fit',
			modal:false,
			resizable:false,
			width:560,
			height:400,
			closeAction:'hide',
			title: GO.calendar.lang.appointment,					
			items: this.formPanel,
			focus: focusSubject.createDelegate(this),
			buttons:[{
					text: GO.lang.cmdOk,
					handler: function(){
						this.submitForm(true);						
					},
					scope: this
				},{
					text: GO.lang.cmdApply,
					handler: function(){
						this.submitForm();
					},
					scope:this
				},{
					text: GO.lang.cmdClose,
					handler: function(){
						this.win.hide();
					},
					scope:this
				}					
			]
		});

		this.win.render(Ext.getBody());
		
		this.addEvents({'save' : true});
	
}

Ext.extend(GO.calendar.EventDialog, Ext.util.Observable,{

	
	show : function (config) {
		if(config.oldDomId)
		{
			this.oldDomId = config.oldDomId;
		}else
		{
			this.oldDomId=false;
		}

		propertiesPanel.show();

		if(!config.event_id)
		{
			config.event_id=0;
			
		}
		
		
		
		
		this.setEventId(config.event_id);
		
		if(config.event_id>0)
		{
			this.formPanel.load({
				url : GO.settings.modules.calendar.url+'json.php',
				//waitMsg:GO.lang.waitMsgLoad,
				success:function(form, action)
				{

					this.win.show();
					this.participants_event_id=action.result.data.participants_event_id;
					this.formPanel.form.baseParams['calendar_id']=action.result.data.calendar_id;
					this.changeRepeat(action.result.data.repeat_type);
					this.setValues(config.values);
					this.participantsPanel.setDisabled(false);
					//this.participantsPanel.store.load();
					if(GO.files)
					{
						this.fileBrowser.setRootPath(action.result.data.files_path);
						this.fileBrowser.setDisabled(false);
					}
					this.setWritePermission(action.result.data.write_permission);
				},
				failure:function(form, action)
				{
					Ext.Msg.alert(GO.lang.strError, action.result.feedback)
				},
				scope: this
				
			});
		}else if(config.exception_event_id)
		{
			
			this.formPanel.load({
				url : GO.settings.modules.calendar.url+'json.php',
				params: {event_id: config.exception_event_id},
				waitMsg:GO.lang.waitMsgLoad,
				success:function(form, action)
				{
					
					this.win.show();	
					this.participants_event_id=action.result.data.participants_event_id;
					this.formPanel.form.baseParams['exception_event_id']=config.exception_event_id;
					this.formPanel.form.baseParams['exceptionDate']=config.exceptionDate;
					this.formPanel.form.baseParams['calendar_id']=action.result.data.calendar_id;
					
					//set recurrence to none					
					this.formPanel.form.findField('repeat_type').setValue(0);
					this.changeRepeat(0);	
					
					this.setValues(config.values);	
					this.participantsPanel.setDisabled(false);		
					
					/*if(action.result.data.files_path)
					{
						this.fileBrowser.setRootPath(action.result.data.files_path);
						this.fileBrowser.setDisabled(false);
					}*/
					
					this.setWritePermission(action.result.data.write_permission);
				},
				failure:function(form, action)
				{
					Ext.Msg.alert(GO.lang.strError, action.result.feedback)
				},
				scope: this
			});
		}else
		{
			delete this.formPanel.form.baseParams['exception_event_id'];
			delete this.formPanel.form.baseParams['exceptionDate'];
			
			this.formPanel.form.reset();
			this.linksPanel.setDisabled(true);
			
			
			this.participantsPanel.setDisabled(true);
			this.setWritePermission(true);
			if(GO.files)
			{
				this.fileBrowser.setDisabled(true);
			}
			
			this.win.show();
			
			if(!config.values)
			{
				var date = new Date();
				
				config.values = {};
				
				var i = parseInt(date.format("i"));
				
				if(i>45)
				{
					i = '45';
				}else if(i>30)
				{
					i = '30';
				}else if(i>15)
				{
					i = '15';
				}else
				{
					i='00';				
				}
				
				config.values['start_date'] = new Date();					
				config.values['start_hour'] = date.format("H");
				config.values['start_min'] = i;
				
				config.values['end_date'] = new Date();
				config.values['end_hour'] = date.add(Date.HOUR,1).format("H");
				config.values['end_min'] = i;
			}
			this.setValues(config.values);
		}
		
		if(config.calendar_id)
		{
			this.selectCalendar.setValue(config.calendar_id);
			this.selectCalendar.container.up('div.x-form-item').setDisplayed(false);
		}
		this.selectCalendar.container.up('div.x-form-item').setDisplayed(this.event_id==0 && this.selectCalendar.getValue()==0 && !config.exception_event_id);
		
		//if the newMenuButton from another passed a linkTypeId then set this value in the select link field
		if(config && config.link_config)
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
	setEventId : function(event_id)
	{
		this.formPanel.form.baseParams['event_id']=event_id;
		this.event_id=event_id;
		this.linksPanel.loadLinks(event_id, 1);
		
		this.participantsStore.baseParams['event_id']=event_id;
		
		this.selectLinkField.container.up('div.x-form-item').setDisplayed(event_id==0);
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
			url:GO.settings.modules.calendar.url+'action.php',
			params: {'task' : 'save_event'},
			waitMsg:GO.lang.waitMsgSave,
			success:function(form, action){
				
				if(action.result.event_id)
				{
					this.setEventId(action.result.event_id);
					this.participants_event_id=action.result.event_id;
					this.participantsPanel.setDisabled(false);		
					
					
					if(GO.files && action.result.files_path)
					{
						this.fileBrowser.setRootPath(action.result.files_path);
						this.fileBrowser.setDisabled(false);
					}
				}
				
				var startDate = this.formPanel.form.findField('start_date').getValue();
				if(!this.formPanel.form.findField('all_day_event').getValue())
				{
					startDate=startDate.add(Date.HOUR, this.formPanel.form.findField('start_hour').getValue());
					startDate=startDate.add(Date.MINUTE, this.formPanel.form.findField('start_min').getValue());
				}
				
				var endDate = this.formPanel.form.findField('end_date').getValue();
				if(!this.formPanel.form.findField('all_day_event').getValue())
				{
					endDate=endDate.add(Date.HOUR, this.formPanel.form.findField('end_hour').getValue());
					endDate=endDate.add(Date.MINUTE, this.formPanel.form.findField('end_min').getValue());
				}
				
					
				var newEvent = {
					id : Ext.id(),
					calendar_id : this.formPanel.form.baseParams['calendar_id'],
					event_id : this.event_id,
					name : this.subjectField.getValue(),
					start_time : startDate.format('U'),
					end_time : endDate.format('U'),
					startDate : startDate,
					endDate : endDate,
					tooltip : '',
					location : this.formPanel.form.findField('location').getValue(),
					repeats : this.formPanel.form.findField('repeat_type').getValue()>0,
					'private' : false
				};
				
				this.fireEvent('save', newEvent, this.oldDomId);
				
				
				if(this.link_config && this.link_config.callback)
				{					
					this.link_config.callback.call(this);					
				}
				
				if(hide)
				{
					this.win.hide();
				}
				
								
			},		
			failure: function(form, action) {
				if(action.failureType=='client')
				{
					error = GO.lang.strErrorsInForm;
				}else
				{
					error = action.result.feedback;
				}
				Ext.MessageBox.alert(GO.lang.strError, error);
			},
			scope: this
		});
		
	},
	
	
	buildForm : function () {
		
		this.selectLinkField = new GO.form.SelectLink({
		});
		
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
    var minutes = [
    	['00','00'],
    	['05','05'],
    	['10','10'],
    	['15','15'],
    	['20','20'],
    	['25','25'],
    	['30','30'],
    	['35','35'],
    	['40','40'],
    	['45','45'],
    	['50','50'],
    	['55','55']    	
    	];
		
		
		this.subjectField = new Ext.form.TextField({
            name: 'subject',
            allowBlank:false,
            fieldLabel: GO.lang.strSubject
    	});    

  	var locationField = new Ext.form.TextField({
          name:'location',
          allowBlank:true,
          fieldLabel: GO.lang.strLocation
  	});       
  	
  
    var description = new Ext.form.TextArea({
        name: 'description',
      	height:100,
        allowBlank:true,
        fieldLabel: GO.lang.strDescription
    	});
    	
    var checkDateInput = function(){
    	
    	if(startDate.getValue()>endDate.getValue())
    	{
    		endDate.setValue(startDate.getValue());
    	}
    	
    	var sH = startHour.getValue();
    	var eH = endHour.getValue();
    	var sM = startMin.getValue();
    	var eM = endMin.getValue();
    	
    	if(sH>eH)
    	{
    		endHour.setValue(sH);
    	}
    	
    	if(sH==eH && sM>eM)
    	{
    		endMin.setValue(sM);
    	}
    	
    	
    	if(repeatType.getValue()>0)
    	{
    		if(repeatEndDate.getValue()=='')
    		{
    			repeatForever.setValue(true);
    		}else
    		{
    			var eD = endDate.getValue();
    			if(repeatEndDate.getValue()<eD)
    			{
    				repeatEndDate.setValue(eD.add(Date.DAY,1));
    			}    		
    		}
        
    	} 
    	
    }
    	       	
    var startDate = new Ext.form.DateField({
        name: 'start_date',
        width:100,
        format: GO.settings['date_format'],
        allowBlank:false,
        fieldLabel: GO.lang.strStart,
        listeners:{
        	change:{
        		fn:checkDateInput,
        		scope:this
        	}
        }
    	});
    	
    	
  	var startHour = new Ext.form.ComboBox({
  		name: 'start_hour',
      store: new Ext.data.SimpleStore({
        fields: ['value','text'],
        data: hours
      }),
      displayField:'text',
      mode: 'local',
      triggerAction: 'all',
      selectOnFocus:true,            
      width:40,
      labelSeparator: '',
			hideLabel: true,
        listeners:{
        	change:{
        		fn:checkDateInput,
        		scope:this
        	}
        }
      });
        
        
    var startMin = new Ext.form.ComboBox({
    	name: 'start_min',       
        store: new Ext.data.SimpleStore({
          fields: ['value','text'],
          data: minutes
        }),
        displayField:'text',
        mode: 'local',
        triggerAction: 'all',
        selectOnFocus:true,
        width:40,
        labelSeparator: '',
				hideLabel: true,
        listeners:{
        	change:{
        		fn:checkDateInput,
        		scope:this
        	}
        }
        });		

        
			var endDate = new Ext.form.DateField({
        name: 'end_date',
        width:100,
        format: GO.settings['date_format'],
        allowBlank:false,
        fieldLabel: GO.lang.strEnd,
        listeners:{
        	change:{
        		fn:checkDateInput,
        		scope:this
        	}
        }
       	});

    	
    	var endHour = new Ext.form.ComboBox({   
        name:'end_hour',
        store: new Ext.data.SimpleStore({
            fields: ['value','text'],
            data: hours
        }),
        displayField:'text',
        mode: 'local',
        triggerAction: 'all',
        selectOnFocus:true,            
        width:40,
        labelSeparator: '',
				hideLabel: true,
        listeners:{
        	change:{
        		fn:checkDateInput,
        		scope:this
        	}
        }
        });

        
      var endMin = new Ext.form.ComboBox({
        name: 'end_min',
        store: new Ext.data.SimpleStore({
            fields: ['value','text'],
            data: minutes
        }),
        displayField:'text',
        mode: 'local',
        triggerAction: 'all',
        selectOnFocus:true,
        width:40,
        labelSeparator: '',
				hideLabel: true,
        listeners:{
        	change:{
        		fn:checkDateInput,
        		scope:this
        	}
        }
      });	
 
        
    var allDayCB = new Ext.form.Checkbox({
        boxLabel:GO.calendar.lang.allDay,        
        name:'all_day_event',
        checked:false,
        width:'auto',
        labelSeparator: '',
				hideLabel: true
    	});
    	
    	allDayCB.on('check', function(checkbox, checked){
    		startHour.setDisabled(checked);
    		endHour.setDisabled(checked);
    		startMin.setDisabled(checked);
    		endMin.setDisabled(checked);
    	},this);
    	
    	
    	var eventStatus = new Ext.form.ComboBox({   
        	hiddenName:'status',
					triggerAction: 'all',
          editable:false,
          selectOnFocus:true,
          width:148,
          forceSelection:true,
          fieldLabel: 'Status',
          mode:'local',
          value:'ACCEPTED',
          valueField:'value',
          displayField:'text',
          store: new Ext.data.SimpleStore({
            fields: ['value', 'text'],
            data: [
            	['NEEDS-ACTION', GO.calendar.lang.needsAction],
            	['ACCEPTED', GO.calendar.lang.accepted],
            	['DECLINED', GO.calendar.lang.declined],
            	['TENTATIVE', GO.calendar.lang.tentative],
            	['DELEGATED', GO.calendar.lang.delegated]
            ]
	        })
        });	

        
		   var busy = new Ext.form.Checkbox({
		      boxLabel: GO.calendar.lang.busy,
		      name:'busy',
		      checked:true,
		      width:'auto',
		      labelSeparator: '',
					hideLabel: true
    	});



		propertiesPanel = new Ext.Panel({
			hideMode:'offsets',
			title:GO.lang.strProperties,
			defaults:{anchor:'-20'},
			//cls:'go-form-panel',waitMsgTarget:true,
			bodyStyle: 'padding:5px',
			layout:'form',
			autoScroll:true,
			items:[
				this.selectCalendar = new GO.form.ComboBox({
		       	fieldLabel: GO.calendar.lang.calendar,
		        hiddenName:'calendar_id',
		        anchor:'-20',
		        emptyText:GO.lang.strPleaseSelect,
		        store: new GO.data.JsonStore({
						    url: GO.settings.modules.calendar.url+ 'json.php',
						    baseParams: {						    	
						    	task: 'writable_calendars'
					    	},
				    root: 'results',
				    id: 'id',
				    totalProperty:'total',
				    fields: ['id', 'name'],
				    remoteSort: true
					}),
					pageSize: parseInt(GO.settings.max_rows_list),
	        valueField:'id',
	        displayField:'name',
	        typeAhead: true,
	        mode: 'remote',
	        triggerAction: 'all',
	        editable: false,
	        selectOnFocus:true,
	        forceSelection: true,
	        allowBlank: false
	    }),
				this.subjectField,	
				locationField,
		    	this.selectLinkField,
		    	description,
		    	{
		    		border:false,
		    		layout:'table',
						defaults: { 
						border: false, 
						layout: 'form', 
						bodyStyle:'padding-right:3px' 
					},
					items: [
		    			{items:startDate},
		    			{items:startHour},
		    			{items:startMin},
		    			{bodyStyle:'white-space:nowrap;', items:allDayCB}
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
		    			{items:endDate},
		    			{items:endHour},
		    			{items:endMin}
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
		    			{items:eventStatus},
		    			{items:busy}
		    			]
		    	}
			]
				
			});
    	

				        
        //Start of recurrence tab
        
        var repeatEvery = new Ext.form.ComboBox({
        	
            fieldLabel:GO.calendar.lang.repeatEvery,  
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

        
        var repeatType = new Ext.form.ComboBox({
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
            	['0', GO.calendar.lang.noRecurrence],
            	['1', GO.calendar.lang.days],
            	['2', GO.calendar.lang.weeks],
            	['3', GO.calendar.lang.monthsByDate],
            	['4', GO.calendar.lang.monthsByDay],
            	['5', GO.calendar.lang.years]
            ]
	        }),
	        hideLabel:true,
	        listeners:{
	        	'change':checkDateInput
	        }
	        
        });	
        
        repeatType.on('select', function(combo, record){this.changeRepeat(record.data.value);}, this);
	        
        
        var monthTime = new Ext.form.ComboBox({
        	hiddenName:'month_time',                  
					triggerAction: 'all',
          selectOnFocus:true,
          disabled: true,
          width:80,
          forceSelection:true,
	        fieldLabel: GO.calendar.lang.atDays,
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
	          id:'frm_repeat_days_'+day,
	          name:'repeat_days_'+day,
	          disabled: true,
	          checked:false,
	          width:'auto',
		        hideLabel:true,
		        laelSeperator:''
        	});
        	
        	
        }
        
        
        var repeatEndDate = new Ext.form.DateField({
            name: 'repeat_end_date',
            width:100,
            disabled: true,
            format: GO.settings['date_format'],
            allowBlank:true,
            fieldLabel:GO.calendar.lang.repeatUntil,
		        listeners:{
		        	change:{
		        		fn:checkDateInput,
		        		scope:this
		        	}
		        }
    	});

        var repeatForever = new Ext.form.Checkbox({
            boxLabel: GO.calendar.lang.repeatForever,
            name:'repeat_forever',
            checked:true,
            disabled:true,
            width:'auto',
	        hideLabel:true,
	        laelSeperator:''
    	});

        
        
        var recurrencePanel = new Ext.Panel({
		
			title:GO.calendar.lang.recurrence,
			//defaults:{anchor:'100%'},
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
	    			{items:repeatEvery},
	    			{items:repeatType}
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
	    			{items:monthTime},
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
	    			{items:repeatEndDate},
	    			{items:repeatForever}
	    			]
	    	}
			
			]
        });
        
        
        
        
        
        //start other options tab
        
      /*  var calendarId = new Ext.form.ComboBox({
        	id:'frm_calendar_id',
        	hiddenName:'calendar_id',            
			triggerAction: 'all',
            transform:'calendar_id',
            selectOnFocus:true,
            width:300,
            forceSelection:true
        });	*/

		var reminderValues = [['0',GO.calendar.lang.noReminder]];
		
		for(var i=1;i<60;i++)
		{
			reminderValues.push([i,i]);
		}
        
        var reminderValue = new Ext.form.ComboBox({
        	fieldLabel:GO.calendar.lang.reminder,
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
        
        
        
        this.participantsStore = new GO.data.JsonStore({			
			url: GO.settings.modules.calendar.url+'json.php',
			baseParams: {task: "participants"},
			root: 'results',
			//totalProperty: 'total',
			id: 'id',
			fields: ['id', 'name','email', 'available', 'status'],
			remoteSort: true
		});
		this.participantsStore.setDefaultSort('name','ASC');
        
     
     var tbar = [{
				iconCls: 'btn-add',
				text: GO.lang.cmdAdd,
				cls: 'x-btn-text-icon',
				handler: function(){
					this.showAddParticipantsDialog();
				},
				scope: this
			},{
				iconCls: 'btn-delete',
				text: GO.lang.cmdDelete,
				cls: 'x-btn-text-icon',
				handler: function(){
					this.participantsPanel.deleteSelected();				
				},
				scope:this						
			},{
				iconCls: 'btn-availability',
				text: GO.calendar.lang.checkAvailability,
				cls: 'x-btn-text-icon',
				handler: function(){
					this.checkAvailability();			
				},
				scope:this						
			}];
			
			if(GO.email)
			{
				tbar.push({
				iconCls: 'btn-invite',
				text: GO.calendar.lang.sendInvitation,
				cls: 'x-btn-text-icon',
				handler: function(){
					if(!GO.settings.modules.email)
					{
						Ext.Msg.alert(GO.lang.strError, GO.calendar.lang.emailSendingNotConfigured);
					}else
					{
						GO.email.Composer.show({
							loadUrl:GO.settings.modules.calendar.url+'json.php',
							loadParams:{task:'invitation',event_id: this.event_id},
							template_id: 0							
						});
					}
					
				},
				scope:this						
				});
			}
        
        this.participantsPanel = new GO.grid.GridPanel({
			title: GO.calendar.lang.participants,
			store: this.participantsStore,
			border:false,
			columns:[{
			       	header: GO.lang.strName,
					dataIndex: 'name',
					sortable: true
			    },{
			       	header: GO.lang.strEmail,
					dataIndex: 'email',
					sortable: true
			    },{
			       	header: GO.lang.strStatus,
					dataIndex: 'status',
					sortable: true,
					renderer: function(v){
						switch(v){
							case '2':
								return GO.calendar.lang.declined;
							break;
							
							case '1':
								return GO.calendar.lang.accepted;
							break;
							
							case '0':
								return GO.calendar.lang.notRespondedYet;
							break;
						}
					}
			    },{
			       	header: GO.lang.strAvailable,
					dataIndex: 'available',
					sortable: false,
					renderer: function(v)
						{
							var className = 'img-unknown';
							switch(v)
							{
								case '1':
									className = 'img-available';
								break;
								
								case '0':
									className = 'img-unavailable';
								break;
							}
							
							return '<div class="'+className+'"></div>';
						}
			    }],
			view: new Ext.grid.GridView({
	    		autoFill: true,
	    		forceFit: true
	    		}),
	    	loadMask: {msg: GO.lang.waitMsgLoad},
	    	sm: new Ext.grid.RowSelectionModel({}),
	    	//paging:true,
	    	layout:'fit',
	    	tbar: tbar
		
		});       
		
		this.participantsPanel.on('show', function(){
			if(!this.loadedParticipantsEventId || this.loadedParticipantsEventId!=this.participants_event_id)
			{
				this.participantsStore.baseParams['event_id']=this.participants_event_id;
				this.loadedParticipantsEventId=this.participants_event_id;
				this.participantsStore.load();
			}
		},this);
        
        
      /*  var cp = new Ext.ColorPalette({value:'993300'});  // initial selected color
		
		cp.on('select', function(palette, selColor){
		    // do something with selColor
		});
		*/
		
		 var privateCB = new Ext.form.Checkbox({
        boxLabel:GO.calendar.lang.privateEvent,
        name:'private',
        checked:false,
        width:'auto',
        labelSeparator: '',
				hideLabel: true
    	});
		
		
		var optionsPanel = new Ext.Panel({
		
			title:GO.calendar.lang.options,
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
	    	},
	    	privateCB//,
	    	//cp
			
			]
        });
   /*     
       GO.testPanel = function(config)
       {
       	if(!config)
       	{
       		config={};
       	}
       	this.northPanel = new Ext.Panel({
      		region:'north',
      		html:'North'
      	});
      	
      	this.centerPanel = new Ext.Panel({
      		region:'center',
      		html:'Center'
      	});
      	
      	config.layout='border'
      	config.title='Test'
      	config.items=[this.northPanel, this.centerPanel];
				
				GO.testPanel.superclass.constructor.call(this, config);
       	
       }
       
       Ext.extend(GO.testPanel, Ext.Panel);

      
      var testPanel = new GO.testPanel();*/
        
      var items = [
        	propertiesPanel,
        	recurrencePanel,
        	optionsPanel,
        	this.participantsPanel,
        	this.linksPanel        	
        ];
      if(GO.files)
 			{
 				items.push(this.fileBrowser);
 			}
 			
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
			waitMsgTarget:true,
			url: GO.settings.modules.calendar.url+'action.php',
			border: false,
			baseParams: {task: 'event'},
      items: this.tabPanel
		});
		
		/*this.formPanel.form.on('actioncomplete', function(form, action){
			if(action.type=='load')
			{
				this.formPanel.form.baseParams['calendar_id']=action.result.data.calendar_id;
				this.changeRepeat(action.result.data.repeat_type);
				//linksPanel.loadLinks(action.result.data['link_id'], 1);
			}		
		},this);*/
      

	},
	

	checkAvailability : function(){
		
		
		if(!this.availabilityWindow)
		{
		
			
			
			this.availabilityStore = new Ext.data.JsonStore({
			    url: GO.settings.modules.calendar.url+'json.php',
			    root: 'participants',
			    fields: ['name', 'email', 'freebusy'],
			    baseParams: {
			    	task: 'availability'		    	
			    	}
			});
			
			
			//var currentDate =  Date.parseDate(store.baseParams.date, 'Y-m-d');
			
			var tpl = new Ext.XTemplate(
				'<div id="availability_date"></div>', 
				'<table class="availability">',
				'<tr><td></td>',
				'<td colspan="4" class="availability_time">'+Date.parseDate("0", "G").format(GO.settings.time_format)+'</td>',
				'<td colspan="4" class="availability_time">'+Date.parseDate("1", "G").format(GO.settings.time_format)+'</td>',
				'<td colspan="4" class="availability_time">'+Date.parseDate("2", "G").format(GO.settings.time_format)+'</td>',
				'<td colspan="4" class="availability_time">'+Date.parseDate("3", "G").format(GO.settings.time_format)+'</td>',
				'<td colspan="4" class="availability_time">'+Date.parseDate("4", "G").format(GO.settings.time_format)+'</td>',
				'<td colspan="4" class="availability_time">'+Date.parseDate("5", "G").format(GO.settings.time_format)+'</td>',
				'<td colspan="4" class="availability_time">'+Date.parseDate("6", "G").format(GO.settings.time_format)+'</td>',
				'<td colspan="4" class="availability_time">'+Date.parseDate("7", "G").format(GO.settings.time_format)+'</td>',
				'<td colspan="4" class="availability_time">'+Date.parseDate("8", "G").format(GO.settings.time_format)+'</td>',
				'<td colspan="4" class="availability_time">'+Date.parseDate("9", "G").format(GO.settings.time_format)+'</td>',
				'<td colspan="4" class="availability_time">'+Date.parseDate("10", "G").format(GO.settings.time_format)+'</td>',
				'<td colspan="4" class="availability_time">'+Date.parseDate("11", "G").format(GO.settings.time_format)+'</td>',
				'<td colspan="4" class="availability_time">'+Date.parseDate("12", "G").format(GO.settings.time_format)+'</td>',
				'<td colspan="4" class="availability_time">'+Date.parseDate("13", "G").format(GO.settings.time_format)+'</td>',
				'<td colspan="4" class="availability_time">'+Date.parseDate("14", "G").format(GO.settings.time_format)+'</td>',
				'<td colspan="4" class="availability_time">'+Date.parseDate("15", "G").format(GO.settings.time_format)+'</td>',
				'<td colspan="4" class="availability_time">'+Date.parseDate("16", "G").format(GO.settings.time_format)+'</td>',
				'<td colspan="4" class="availability_time">'+Date.parseDate("17", "G").format(GO.settings.time_format)+'</td>',
				'<td colspan="4" class="availability_time">'+Date.parseDate("18", "G").format(GO.settings.time_format)+'</td>',
				'<td colspan="4" class="availability_time">'+Date.parseDate("19", "G").format(GO.settings.time_format)+'</td>',
				'<td colspan="4" class="availability_time">'+Date.parseDate("20", "G").format(GO.settings.time_format)+'</td>',
				'<td colspan="4" class="availability_time">'+Date.parseDate("21", "G").format(GO.settings.time_format)+'</td>',
				'<td colspan="4" class="availability_time">'+Date.parseDate("22", "G").format(GO.settings.time_format)+'</td>',
				'<td colspan="4" class="availability_time">'+Date.parseDate("23", "G").format(GO.settings.time_format)+'</td>',

				'<tpl for=".">',
				'<tr>',
			    '<td>{name}</td>',
			    '<tpl if="this.hasFreeBusy(freebusy)">',
			    	'<tpl for="freebusy">',
			        	'<td id="time{time}"class="time {[values.busy == 1 ? "busy" : "free"]}"></td>',
			    	'</tpl>',
			    '</tpl>',
			    '<tpl if="!this.hasFreeBusy(freebusy)">',
			    	'<td colspan="96">'+GO.calendar.lang.noInformationAvailable+'</td>',
			    '</tpl>',
			    '</tr>',
			  	'</tpl>',
			  	'</table>',{
			     hasFreeBusy: function(freebusy){
			         return freebusy.length>0;
			     }
			}
			);
			
			var dataView =  new Ext.DataView({
			        store: this.availabilityStore,
			        tpl: tpl,
			        autoHeight:true,
			        emptyText: GO.calendar.lang.noParticipantsToDisplay,
			        itemSelector: 'td.time',
			        overClass:'time-over'
			    });
			
			var panel = new Ext.Panel({
			   
			    layout:'fit',
					cls:'go-form-panel',waitMsgTarget:true,
			    items:dataView,
			    autoScroll:true
			});
					
			dataView.on('click', function(dataview, index, node){
									
				//alert(node.id);
				var time = node.id.substr(4);
				
				var colonIndex = time.indexOf(':');
				
				var minutes = time.substr(colonIndex+1);
				var hours = time.substr(0,colonIndex);
				
				var date = Date.parseDate(this.availabilityStore.baseParams.date, 'Y-m-d');	
				
							
				
				var frmStartHour = this.formPanel.form.findField('start_hour');
				var frmStartMin = this.formPanel.form.findField('start_min');
				var frmStartDate = this.formPanel.form.findField('start_date');
				
				var frmEndHour = this.formPanel.form.findField('end_hour');
				var frmEndMin = this.formPanel.form.findField('end_min');
				var frmEndDate = this.formPanel.form.findField('end_date');
				
				
				var hourDiff = frmEndHour.getValue()-frmStartHour.getValue();
				var minDiff = frmEndMin.getValue()-frmStartMin.getValue();
				
				
				frmStartHour.setValue(hours);
				frmStartMin.setValue(minutes);
				frmStartDate.setValue(date);
				
				var endHour = parseInt(hours)+hourDiff;
				var endMin = parseInt(minutes)+minDiff;
				if(endMin==0)
				{
					endMin="00";
				}
				
				frmEndHour.setValue(endHour);
				frmEndMin.setValue(endMin);
				frmEndDate.setValue(date);
				
				propertiesPanel.show();
				this.availabilityWindow.hide();
				
					
	     		}, this);
			
			this.availabilityStore.on('load', function(){
				var date = Date.parseDate(this.availabilityStore.baseParams.date, 'Y-m-d');					
				Ext.get("availability_date").update(date.format(GO.settings.date_format));
			}, this);
			
			this.availabilityWindow = new Ext.Window({
				layout: 'fit',
				modal:false,
				height:400,
				width:800,
				closeAction:'hide',
				title:GO.lang.strAvailability,
				items:panel ,
				tbar: [{
					iconCls: 'btn-left-arrow',
					text: GO.calendar.lang.previousDay,
					cls: 'x-btn-text-icon',
					handler: function(){
						var date = Date.parseDate(this.availabilityStore.baseParams.date, 'Y-m-d').add(Date.DAY, -1);					
						this.availabilityStore.baseParams.date=date.format('Y-m-d');
						this.availabilityStore.load();					
					},
					scope: this
					},{
					iconCls: 'btn-right-arrow',
					text: GO.calendar.lang.nextDay,
					cls: 'x-btn-text-icon',
					handler: function(){
						var date = Date.parseDate(this.availabilityStore.baseParams.date, 'Y-m-d').add(Date.DAY, 1);					
						this.availabilityStore.baseParams.date=date.format('Y-m-d');
						this.availabilityStore.load();					
					},
					scope: this
					}],
				buttons: [
					{
						text: GO.lang.cmdClose,
						handler: function(){this.availabilityWindow.hide();},
						scope: this
					}
				]
			});
		}
		
		var start_date = this.formPanel.form.findField('start_date');
		start_date = start_date.getValue();
		this.availabilityStore.baseParams['date']=start_date.format('Y-m-d');
		this.availabilityStore.baseParams['event_id']=this.event_id;
		this.availabilityStore.load();
		this.availabilityWindow.show();
		
	},

	showAddParticipantsDialog : function()
	{
		if(!this.addParticipantsDialog)
		{
			this.addParticipantsDialog = new GO.dialog.SelectEmail({
				handler:function(grid)
				{
					if(grid.selModel.selections.keys.length>0)
					{
						var selections = grid.selModel.getSelections();
						var participants = Array();
						
						
						for (var i=0;i<selections.length;i++)
						{
							participants.push({name: selections[i].data.name, email: selections[i].data.email});
						}
						this.participantsStore.baseParams['add_participants']=Ext.encode(participants);
						this.participantsStore.load();
						delete this.participantsStore.baseParams['add_participants'];
					}
				},
				scope:this				
			});
		}
		this.addParticipantsDialog.show();
	},
	
	
	changeRepeat : function(value){
		
		var form = this.formPanel.form;
        switch(value)
		{
			case '0':
				this.disableDays(true);
				form.findField('month_time').setDisabled(true);
				form.findField('repeat_forever').setDisabled(true);
				form.findField('repeat_end_date').setDisabled(true);					
				form.findField('repeat_every').setDisabled(true);
			break;
	
			case '1':
				this.disableDays(true);
				form.findField('month_time').setDisabled(true);
				form.findField('repeat_forever').setDisabled(false);
				form.findField('repeat_end_date').setDisabled(false);					
				form.findField('repeat_every').setDisabled(false);
				
			break;
	
			case '2':
				this.disableDays(false);
				form.findField('month_time').setDisabled(true);
				form.findField('repeat_forever').setDisabled(false);
				form.findField('repeat_end_date').setDisabled(false);					
				form.findField('repeat_every').setDisabled(false);

			break;
	
			case '3':
				this.disableDays(true);
				form.findField('month_time').setDisabled(true);
				form.findField('repeat_forever').setDisabled(false);
				form.findField('repeat_end_date').setDisabled(false);					
				form.findField('repeat_every').setDisabled(false);
			
			break;
	
			case '4':
				this.disableDays(false);
				form.findField('month_time').setDisabled(false);
				form.findField('repeat_forever').setDisabled(false);
				form.findField('repeat_end_date').setDisabled(false);					
				form.findField('repeat_every').setDisabled(false);
			break;
	
			case '5':
				this.disableDays(true);
				form.findField('month_time').setDisabled(true);
				form.findField('repeat_forever').setDisabled(false);
				form.findField('repeat_end_date').setDisabled(false);					
				form.findField('repeat_every').setDisabled(false);
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