EventDialog = function(){
	
	var dialog;

	return{
		
		show : function (eventId, values) {
			
			this.eventId=eventId;
			
			if(!dialog)
			{
				dialog = new Ext.LayoutDialog('event-dialog', {
						modal:true,
						shadow:false,
						resizable:false,
						proxyDrag: true,
						width:550,
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
					dialog.addKeyListener(27, dialog.hide, dialog);
		
		
					layout = dialog.getLayout();
		
		
					layout.beginUpdate();
				
					
					layout.add('center', new Ext.ContentPanel('event-properties',{
						title: GOlang['strProperties'],
						autoScroll:true					
					}));
					
					
					layout.add('center', new Ext.ContentPanel('event-recurrence',{
						title: calLang['recurrence'],
						autoScroll:true					
					}));
					
					this.buildForm();
					
					
					
					dialog.addButton({
					text: GOlang['cmdOk'],
					handler: function(){
						this.submitForm();
						dialog.hide();
					},
					scope:this
					
					}, this);
					
					dialog.addButton({
						text: GOlang['cmdApply'],
						handler: function(){
							this.submitForm();
						},
						scope:this
					}, this);
				
					dialog.addButton(GOlang['cmdClose'], dialog.hide, dialog);
					
		
					layout.endUpdate();
			
			}
			
			if(values)
			{
				this.eventForm.setValues(values);
			}
			
			
			
			dialog.show();
			this.eventForm.findField("subject").focus();
		},
		
		setCurrentDate : function()
		{
			var formValues={};
			
			var date = new Date();
					
			formValues['startDate'] = date.format(GOsettings['date_format']);					
			formValues['startHour'] = date.format("H");
			formValues['startMinute'] = '00';
			
			formValues['endDate'] = date.format(GOsettings['date_format']);
			formValues['endHour'] = date.add(Date.HOUR,1).format("H");
			formValues['endMinute'] = '00';
			
			this.eventForm.setValues(formValues);
			
		},
		
		submitForm : function(){
			
			var task = this.eventId>0 ? 'update_event' : 'add_event';
			
			this.eventForm.submit(
			{
				url:'action.php',
				params: {'task' : task},
				waitMsg:GOlang['waitMsgSave'],
				success:function(form, action){
					
					if(action.result.event_id)
					{
						this.eventId=action.result.event_id;
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
		    Ext.form.Field.prototype.msgTarget = 'side';
			
			this.eventForm = new Ext.BasicForm('event-form', {
				waitMsgTarget: 'event-dialog'
			});
			
			var nameField = new Ext.form.TextField({
			id: 'subject',
            name: 'subject',
          	width:400,
            allowBlank:false
        	});        
        	
        	//nameField.applyTo('subject');
        		
        	this.eventForm.add(nameField,        	
        	new Ext.form.TextArea({
        	id: 'description',
            name: 'description',
          	width:400,
          	height:150,
            allowBlank:true
        	})
        	,        	
        	new Ext.form.DateField({
        	id: 'startDate',
            name: 'startDate',
            width:100,
            format: GOsettings['date_format'],
            allowBlank:false
        	})
        	);
        	
        	var startHour = new Ext.form.ComboBox({            
        		id: 'startHour',
        		name: 'startHour',
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
	        	id: 'startMinute',   
	        	name: 'startMinute',         
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
	        id: 'endDate',
            name: 'endDate',
            width:100,
            format: GOsettings['date_format'],
            allowBlank:false
        	});
        	this.eventForm.add(endDate);
        	
        	var endHour = new Ext.form.ComboBox({   
        		id: 'endHour',         
	            name:'endHour',
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
	        	id:'endMinute',            
	            name:'endMinute',
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
	            boxLabel:'Time is not applicable',
	            id:'allDay',  
	            name:'allDay',
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
        	
        	
        	var calendarId = new Ext.form.ComboBox({
	        	id:'calendarId',            
				triggerAction: 'all',
	            transform:'calendarId',
	            selectOnFocus:true,
	            width:300,
	            forceSelection:true
	        });	
	        this.eventForm.add(calendarId);
	        
	        
	        
	        
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
	        
	        repeatType.on('select', this.changeRepeat, this);
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
	        
	        
	        
        	
        	
        	this.eventForm.render();
		},
		changeRepeat : function(checkbox, record, index){
	        switch(record.data.value)
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
}