Ext.calendar.EventDialog = function(calendarGrid){
	
	this.calendarGrid=calendarGrid;
}

Ext.calendar.EventDialog.prototype = {

	
	show : function (eventId, values) {
		
		
		
		if(!this.dialog)
		{
			this.dialog = new Ext.LayoutDialog('event-dialog', {
					modal:true,
					shadow:false,
					resizable:false,
					proxyDrag: true,
					width:560,
					height:400,
					collapsible:false,
					shim:false,
					center: {
						autoScroll:true,
						tabPosition: 'top',
						closeOnTab: true,
						alwaysShowTabs: false
					},
					title: calLang['appointment']
	
				});
				this.dialog.addKeyListener(27, this.dialog.hide, this.dialog);
	
	
				layout = this.dialog.getLayout();
	
	
				layout.beginUpdate();
			
				
				layout.add('center', new Ext.ContentPanel('event-properties',{
					title: GOlang['strProperties'],
					autoScroll:true					
				}));
				
				
				layout.add('center', new Ext.ContentPanel('event-recurrence',{
					title: calLang['recurrence'],
					autoScroll:true					
				}));
				
				layout.add('center', new Ext.ContentPanel('event-options',{
					title: calLang['options'],
					autoScroll:true					
				}));
				
				this.buildForm();
				
				
				
				this.dialog.addButton({
				text: GOlang['cmdOk'],
				handler: function(){
					this.submitForm();
					this.dialog.hide();
				},
				scope:this
				
				}, this);
				
				this.dialog.addButton({
					text: GOlang['cmdApply'],
					handler: function(){
						this.submitForm();
					},
					scope:this
				}, this);
			
				this.dialog.addButton(GOlang['cmdClose'], this.dialog.hide, this.dialog);
				
	
				layout.endUpdate();
		
		}
		
		this.eventForm.reset();
		
		
		this.setEventId(eventId);
		
		if(eventId>0)	
		{
			this.eventForm.load({url : 'json.php?event_id='+eventId});				
		}
		
		
		if(values)
		{
			this.eventForm.setValues(values);
		}
		
		
		layout.showPanel(0);
		
		
		this.dialog.show();
		this.eventForm.findField("subject").focus();
	},
	setEventId : function(eventId)
	{
		this.eventForm.baseParams={event_id: eventId};
		this.eventId=eventId;
	},
	
	setCurrentDate : function()
	{
		var formValues={};
		
		var date = new Date();
				
		formValues['start_date'] = date.format(GOsettings['date_format']);					
		formValues['start_hour'] = date.format("H");
		formValues['start_min'] = '00';
		
		formValues['end_date'] = date.format(GOsettings['date_format']);
		formValues['end_hour'] = date.add(Date.HOUR,1).format("H");
		formValues['end_min'] = '00';
		
		this.eventForm.setValues(formValues);
		
	},
	
	submitForm : function(){
		
		//var task = this.eventId>0 ? 'update_event' : 'add_event';
		
		this.eventForm.submit(
		{
			url:'action.php',
			params: {'task' : 'save_event'},
			waitMsg:GOlang['waitMsgSave'],
			success:function(form, action){
				
				if(action.result.event_id)
				{
					this.setEventId(action.result.event_id);
					
					/*var startDate = new Date.parseDate(this.eventForm.findField('start_date').getValue()+' '+
						this.eventForm.findField('start_hour').getValue()+':'+
						this.eventForm.findField('start_min').getValue(), this.calendarGrid.dateTimeFormat);
						
					var endDate = new Date.parseDate(this.eventForm.findField('end_date').getValue()+' '+
						this.eventForm.findField('end_hour').getValue()+':'+
						this.eventForm.findField('end_min').getValue(), this.calendarGrid.dateTimeFormat);
					
					//add the event to the grid
					this.calendarGrid.addTimedEvent(action.result.event_id, this.eventForm.findField('subject').getValue(),startDate, endDate);
					*/
					//TODO dynamic update
					this.calendarGrid.reload();
				}					
			},		
			failure: function(form, action) {
				//Ext.MessageBox.alert(GOlang['Error'], action.result.errors);
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
		
		Ext.QuickTips.init();

	    // turn on validation errors beside the field globally
	    Ext.form.Field.prototype.msgTarget = 'qtip';
		
		this.eventForm = new Ext.BasicForm('event-form', {
			waitMsgTarget: 'event-dialog',
			reader: new Ext.data.JsonReader({
				root: 'event',
				id: 'id'
			}, [
			{name: 'subject'},
			{name: 'description'},
			{name: 'location'},
			{name: 'start_date'},
			{name: 'start_hour'},
			{name: 'start_min'},
			{name: 'end_date'},
			{name: 'end_hour'},
			{name: 'end_min'},
			{name: 'repeat_days_0'},
			{name: 'repeat_days_1'},
			{name: 'repeat_days_2'},
			{name: 'repeat_days_3'},
			{name: 'repeat_days_4'},
			{name: 'repeat_days_5'},
			{name: 'repeat_days_6'},
			{name: 'repeat_end_date'},
			{name: 'reminder_multiplier'},
			{name: 'reminder_value'},
			
			{name: 'repeat_type'},
			{name: 'status'},
			{name: 'repeat_forever'}
			])
		});
		
		this.eventForm.on('actioncomplete', function(form, action){
			if(action.type=='load')
			{
				this.changeRepeat(action.result.data.repeat_type);
			}		
		},this);
		
		var nameField = new Ext.form.TextField({
			id: 'subject',
            name: 'subject',
          	width:400,
            allowBlank:false
    	});    
    	
    	
    	var locationField = new Ext.form.TextField({
			id: 'location',
            name: 'location',
          	width:400,
            allowBlank:true
    	});       
    	
    	
    	var selectLinkField = new Ext.form.selectLink( 	
    	{
    		id: 'link_name',
    		name: 'link_name',
    		typeAhead: true,
    		triggerAction: 'all',
			width: 400,
			selectOnFocus:true
	        		
    	});
    	
    	selectLinkField.on("select", function(checkbox, record, index){
    		
    		this.eventForm.baseParams['link_id']=record.data.link_id;
    		this.eventForm.baseParams['link_type']=record.data.link_type;
    		
    	},this);
    	
    	        		
    	this.eventForm.add(nameField, locationField, selectLinkField,  	
        	new Ext.form.TextArea({
        	id: 'description',
            name: 'description',
          	width:400,
          	height:100,
            allowBlank:true
    	})
    	,        	
        	new Ext.form.DateField({
        	id: 'start_date',
            name: 'start_date',
            width:100,
            format: GOsettings['date_format'],
            allowBlank:false
        	})
    	);
    	
    	var startHour = new Ext.form.ComboBox({            
    		id: 'start_hour',
    		name: 'start_hour',
            store: new Ext.data.SimpleStore({
                fields: ['value','text'],
                data: hours
            }),
            displayField:'text',
            typeAhead: true,
            mode: 'local',
            triggerAction: 'all',
            selectOnFocus:true,
            
            width:40
        });
        
        this.eventForm.add(startHour);
        
        var startMin = new Ext.form.ComboBox({
        	id: 'start_min',   
        	name: 'start_min',         
            store: new Ext.data.SimpleStore({
                fields: ['value','text'],
                data: minutes
            }),
            displayField:'text',
            typeAhead: true,
            mode: 'local',
            triggerAction: 'all',
            selectOnFocus:true,
            width:40
        });		
        this.eventForm.add(startMin);
        
        var endDate = new Ext.form.DateField({
	        id: 'end_date',
            name: 'end_date',
            width:100,
            format: GOsettings['date_format'],
            allowBlank:false
        	});
    	this.eventForm.add(endDate);
    	
    	var endHour = new Ext.form.ComboBox({   
    		id: 'end_hour',         
            name:'end_hour',
            store: new Ext.data.SimpleStore({
                fields: ['value','text'],
                data: hours
            }),
            displayField:'text',
            typeAhead: true,
            mode: 'local',
            triggerAction: 'all',
            selectOnFocus:true,
            
            width:40
        });
        this.eventForm.add(endHour);
        
        var endMin = new Ext.form.ComboBox({
        	id:'end_min',            
            name:'end_min',
            store: new Ext.data.SimpleStore({
                fields: ['value','text'],
                data: minutes
            }),
            displayField:'text',
            typeAhead: true,
            mode: 'local',
            triggerAction: 'all',
            selectOnFocus:true,
            width:40
        });	
        this.eventForm.add(endMin);
        
        var allDayCB = new Ext.form.Checkbox({
            boxLabel:calLang['allDay'],
            id:'all_day_event',  
            name:'all_day_event',
            checked:false,
            width:'auto'
    	});
    	this.eventForm.add(allDayCB);
    	
    	allDayCB.on('check', function(checkbox, checked){
    		startHour.setDisabled(checked);
    		endHour.setDisabled(checked);
    		startMin.setDisabled(checked);
    		endMin.setDisabled(checked);
    	},this);
    	
    	
    	var eventStatus = new Ext.form.ComboBox({
        	id:'status_id',            
			triggerAction: 'all',
            transform:'status_id',
            editable:false,
            selectOnFocus:true,
            width:148,
            forceSelection:true
        });	
        this.eventForm.add(eventStatus);
        
         var busy = new Ext.form.Checkbox({
            boxLabel: calLang['busy'],
            id:'busy',  
            name:'busy',
            checked:true,
            width:'auto'
    	});
    	this.eventForm.add(busy);
    	
    	
    	
        
        
        
        
        //Start of recurrence tab
        
        var repeatEvery = new Ext.form.ComboBox({
        	id:'repeat_every',            
			triggerAction: 'all',
            transform:'repeat_every',
            selectOnFocus:true,
            width:40,
            forceSelection:true,
            disabled: true
        });	
        this.eventForm.add(repeatEvery);
        
        var repeatType = new Ext.form.ComboBox({
        	id:'repeat_type',            
			triggerAction: 'all',
			editable: false,
            transform:'repeat_type',
            selectOnFocus:true,
            width:200,
            forceSelection:true
        });	
        
        repeatType.on('select', function(combo, record){this.changeRepeat(record.data.value);}, this);
        this.eventForm.add(repeatType);
        	        
        
        var monthTime = new Ext.form.ComboBox({
        	id:'month_time',            
			triggerAction: 'all',
            transform:'month_time',
            selectOnFocus:true,
            disabled: true,
            width:80,
            forceSelection:true
        });	
        this.eventForm.add(monthTime);
        
        for(var day=0;day<7;day++)
        {
        	var cb = new Ext.form.Checkbox({
	            boxLabel:calLang['shortDays'][day],
	            id:'repeat_days_'+day,  
	            name:'repeat_days_'+day,
	            disabled: true,
	            checked:false,
	            width:'auto'
        	});
        	this.eventForm.add(cb);
        	
        }
        
        
        var repeatEndDate = new Ext.form.DateField({
        	id: 'repeat_end_date',
            name: 'repeat_end_date',
            width:100,
            disabled: true,
            format: GOsettings['date_format'],
            allowBlank:true
    	});
    	this.eventForm.add(repeatEndDate);
        
        var repeatForever = new Ext.form.Checkbox({
            boxLabel: calLang['repeatForever'],
            id:'repeat_forever',  
            name:'repeat_forever',
            checked:true,
            disabled:true,
            width:'auto'
    	});
    	this.eventForm.add(repeatForever);
        
        
        
        //start other options tab
        
        var calendarId = new Ext.form.ComboBox({
        	id:'calendar_id',            
			triggerAction: 'all',
            transform:'calendar_id',
            selectOnFocus:true,
            width:300,
            forceSelection:true
        });	
        this.eventForm.add(calendarId);
        
        var reminderValue = new Ext.form.ComboBox({
        	id:'reminder_value',            
			triggerAction: 'all',
            transform:'reminder_value',
            editable:false,
            selectOnFocus:true,
            width:148,
            forceSelection:true
        });	
        this.eventForm.add(reminderValue);
        
        
        var reminderMultiplier = new Ext.form.ComboBox({
        	id:'reminder_multiplier',            
			triggerAction: 'all',
            transform:'reminder_multiplier',
            editable:false,
            selectOnFocus:true,
            width:148,
            forceSelection:true
        });	
        this.eventForm.add(reminderMultiplier);
        
        
        var cp = new Ext.ColorPalette({value:'993300'});  // initial selected color
		cp.render('colorSelector');
		
		cp.on('select', function(palette, selColor){
		    // do something with selColor
		});
        
        
    	
    	
    	this.eventForm.render();
	},
	changeRepeat : function(value){
        switch(value)
		{
			case '0':
				this.disableDays(true);
				this.eventForm.findField('month_time').setDisabled(true);
				this.eventForm.findField('repeat_forever').setDisabled(true);
				this.eventForm.findField('repeat_end_date').setDisabled(true);					
				this.eventForm.findField('repeat_every').setDisabled(true);
			break;
	
			case '1':
				this.disableDays(true);
				this.eventForm.findField('month_time').setDisabled(true);
				this.eventForm.findField('repeat_forever').setDisabled(false);
				this.eventForm.findField('repeat_end_date').setDisabled(false);					
				this.eventForm.findField('repeat_every').setDisabled(false);
				
			break;
	
			case '2':
				this.disableDays(false);
				this.eventForm.findField('month_time').setDisabled(true);
				this.eventForm.findField('repeat_forever').setDisabled(false);
				this.eventForm.findField('repeat_end_date').setDisabled(false);					
				this.eventForm.findField('repeat_every').setDisabled(false);

			break;
	
			case '3':
				this.disableDays(true);
				this.eventForm.findField('month_time').setDisabled(true);
				this.eventForm.findField('repeat_forever').setDisabled(false);
				this.eventForm.findField('repeat_end_date').setDisabled(false);					
				this.eventForm.findField('repeat_every').setDisabled(false);
			
			break;
	
			case '4':
				this.disableDays(false);
				this.eventForm.findField('month_time').setDisabled(false);
				this.eventForm.findField('repeat_forever').setDisabled(false);
				this.eventForm.findField('repeat_end_date').setDisabled(false);					
				this.eventForm.findField('repeat_every').setDisabled(false);
			break;
	
			case '5':
				this.disableDays(true);
				this.eventForm.findField('month_time').setDisabled(true);
				this.eventForm.findField('repeat_forever').setDisabled(false);
				this.eventForm.findField('repeat_end_date').setDisabled(false);					
				this.eventForm.findField('repeat_every').setDisabled(false);
			break;
		}	        	
    },
    disableDays : function(disabled){
    	for(var day=0;day<7;day++)
        {
        	this.eventForm.findField('repeat_days_'+day).setDisabled(disabled);
        }
    		    	
    }


}