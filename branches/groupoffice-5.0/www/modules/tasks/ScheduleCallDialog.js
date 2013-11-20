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
			height:580,
			width:600,
			goDialogId:'task-schedule-call',
			title:GO.tasks.lang.scheduleCall,
			formControllerUrl: 'tasks/scheduleCall',
			submitAction : 'save',
			loadAction : 'load',
			enableApplyButton : false
		});
		
		GO.tasks.ScheduleCallDialog.superclass.initComponent.call(this);	
		
		this.formPanel.baseParams.remind_date=this.datePicker.getValue().format(GO.settings.date_format);
	},
	show : function (remoteModelId, config) {
		GO.tasks.ScheduleCallDialog.superclass.show.call(this);
		this.selectContact.clearLastSearch();
	},
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
			this.formPanel.baseParams.remind_date=this.formPanel.baseParams.start_time=this.formPanel.baseParams.due_time=DateObj.format(GO.settings.date_format);	
		},this);
				
		this.selectTaskList = new GO.tasks.SelectTasklist({
			fieldLabel: GO.tasks.lang.tasklist, 
			anchor:'100%'
		});
		
		this.timeField = new Ext.form.TimeField({
			name:'remind_time',
			width:220,
			format: GO.settings.time_format,
			value:eight.format(GO.settings['time_format']),
			fieldLabel:GO.lang.strTime,
			anchor:'100%'
		});
			
		this.descriptionField = new Ext.form.TextArea({
			name: 'description',
			anchor: '100%',
			width:300,
			height:45,
			fieldLabel: GO.lang.strDescription
		});		

		this.selectContact = new GO.addressbook.SelectContact ({
			hiddenName: 'contact_id',
			fieldLabel:GO.addressbook.lang.contact,
			enableKeyEvents : true,
			hasTyped:false,
			remoteSort: true,
			anchor: '100%'
		});
		
		this.phoneNumberField = new GO.form.ComboBoxReset({
			name: 'number',
			fieldLabel:GO.tasks.lang.phoneNr,
			anchor: '100%',
			mode:'local',
			triggerAction:'all',
			enableKeyEvents : true,
			selectOnFocus:true,
			displayField:'number',
			valueField: 'number',
			store: new Ext.data.ArrayStore({
				storeId: 'phoneNumberFieldStore',
				fields: ['id','number']
			})
		});
		
		this.btnAddContact = new Ext.Button ({
			text:GO.addressbook.lang.btnAddContact,
			anchor: '50%',
			disabled:true,
			style:{
				'margin-left':'105px',
				'margin-bottom':'5px'
			},
			handler:function(){
				var attrs = {};
				var name = this.selectContact.getRawValue();
				var number = this.phoneNumberField.getRawValue();
				var field = this.savePhoneNumberField.getValue();
				
				var nameParts = {};

				if(name){
					nameParts = name.split(" ");

					if(nameParts.length > 2){
						attrs.first_name = nameParts[0];
						attrs.middle_name = nameParts[1];
						attrs.last_name = nameParts[2];
					} else if(nameParts.length > 1){
						attrs.first_name = nameParts[0];
						attrs.last_name = nameParts[1];
					} else {
						attrs.first_name = nameParts[0];
					}
				}
				
				if(!GO.util.empty(field) && number){
					attrs[field] = number;
				} else if(number){
					attrs['work_phone'] = number;
				}
			
				GO.addressbook.showContactDialog(0, {values:attrs});
				
				GO.addressbook.contactDialog.on('save',this.setContact,this);
				GO.addressbook.contactDialog.on('hide',function(){
					GO.addressbook.contactDialog.un('save', this.setContact);
				},this, {single:true});
			},
			scope: this
		});

		this.savePhoneNumberField = new GO.form.ComboBox({
			hiddenName: 'save_as',
			fieldLabel:GO.tasks.lang.savePhoneNr,
			disabled:true,
			anchor: '100%',
			mode:'local',
			triggerAction:'all',
			selectOnFocus:true,
			displayField:'label',
			valueField: 'id',
			store: new Ext.data.ArrayStore({
				storeId: 'savePhoneNumberFieldStore',
				fields: ['id','label','number']
			})
		});
			
		this.selectContact.on('select', function(combo, record, index ){
			combo.hasTyped = false;
			this.populatePhoneFields(record);
			this.btnAddContact.setDisabled(true);
		},this);
		
		this.selectContact.on('keyup', function(combo){
			if(!combo.hasTyped){
				combo.hasTyped = true;
				this.populatePhoneFields();
				this.btnAddContact.setDisabled(false);
			}
			
		},this);
		
		this.phoneNumberField.on('keyup', function(combo,e){
			this.savePhoneNumberField.setDisabled(false);
		},this);
		
		this.phoneNumberField.on('select', function(combo,record,index){
			this.disableSavePhoneNumberField();
		},this);
		
//		this.propertiesPanel = new Ext.Panel({
//			border: false,
//			//			baseParams: {date: tomorrow.format(GO.settings.date_format), name: 'TEST'},			
//			cls:'go-form-panel',
//			layout:'form',
//			waitMsgTarget:true,			
//			items:[
//			{
//				xtype:'fieldset',
//				title: GO.tasks.lang.task,
//				items:[
//				{	
//					items:this.datePicker,
//					width:240,
//					style:'margin:auto;'
//				},
//				new GO.form.HtmlComponent({
//					html:'<br />'
//				}),
//				this.timeField,
//				this.selectTaskList,
//				this.descriptionField
//			]},{
//				xtype:'fieldset',
//				title: GO.addressbook.lang.contact,
//				items:[
//					this.selectContact,
//					this.phoneNumberField,
//					this.savePhoneNumberField,
//					this.btnAddContact,
//				]}
//			]			
//		});
		
		this.propertiesPanel = new Ext.Panel({
			border: false,
			//			baseParams: {date: tomorrow.format(GO.settings.date_format), name: 'TEST'},			
			//cls:'go-form-panel',
			layout:'form',
			waitMsgTarget:true,			
			items:[
			{
				xtype:'fieldset',
				title: GO.tasks.lang.task,
				items:[
				{	
					items:this.datePicker,
					width:240,
					style:'margin:auto;'
				},
				{
					layout:'column',
					items:[{
							columnWidth:.5,
							items:[{
									layout:'form',
									labelWidth:55,
									items:[
										this.timeField,
										this.selectTaskList
									]
							}]
						},{
							columnWidth:.5,
							items:[{
									layout:'form',
									style:{
										'padding-left': '10px'
									},
									labelWidth:70,
									items:[
										this.descriptionField
									]
							}]
						}]
				}
			]},{
				xtype:'fieldset',
				title: GO.addressbook.lang.contact,
				items:[
					this.selectContact,
					this.phoneNumberField,
					this.savePhoneNumberField,
					this.btnAddContact,
				]}
			]			
		});
	
		this.addPanel(this.propertiesPanel);
	},
	
	beforeSubmit : function(params){
			this.formPanel.baseParams.is_contact = this.selectContact.hasTyped?false:true;
	},
	
	populatePhoneFields : function(record){

		var order = [
			'work_phone',
			'home_phone',
			'cellular',
			'cellular2'
		];
		
		if(GO.util.empty(record)){
			record = {};
			record.data = {};
			
			for(var i=0; i <order.length; i++)
				record.data[order[i]] = '';
			
			this.savePhoneNumberField.setDisabled(false);
		} else {
			this.savePhoneNumberField.setDisabled(true);
		}
		
		// Select the first found attribute that is not empty
		var currentNumber = '';
		var foundNumbers = [];
		var replaceNumbers = [];
		for(var i=0; i <order.length; i++){
			currentNumber = record.data[order[i]];
			if(!GO.util.empty(currentNumber)){
				replaceNumbers.push(new Ext.data.Record({'id':order[i],'label':this.createReplaceNumberLabel(order[i],currentNumber),'number':currentNumber},order[i]));
				foundNumbers.push(new Ext.data.Record({'id':order[i],'number':currentNumber},order[i]));
			} else {
				replaceNumbers.push(new Ext.data.Record({'id':order[i],'label':this.createReplaceNumberLabel(order[i],''),'number':''},order[i]));
			}
		}
		
		// Clear both stores
		this.phoneNumberField.getStore().removeAll();
		this.savePhoneNumberField.getStore().removeAll();
		
		// Fill the store for the phoneNumberField 
		if(foundNumbers.length > 0){
			this.phoneNumberField.getStore().add(foundNumbers);
			this.phoneNumberField.selectFirst();
		}
		// Fill the store for the savePhoneNumberField
		this.savePhoneNumberField.getStore().add(replaceNumbers);
		this.savePhoneNumberField.setRawValue('');
	},
	createReplaceNumberLabel: function(field,oldvalue){
		
		var label = '';
		
		if(!GO.util.empty(oldvalue))
			label = GO.tasks.lang.overwritePhoneNumber;
		else 
			label = GO.tasks.lang.addToPhoneNumber;
		
		var fieldname = 'contact'+this.capitalize(field);

		label = label.replace("{field}",GO.addressbook.lang[fieldname]);
		label = label.replace("{number}",oldvalue);

		return label;
	},
	capitalize : function(text) {
    return text.charAt(0).toUpperCase() + text.slice(1).toLowerCase();
	},
	setContact : function(dialog,contact_id){
		this.selectContact.getStore().load();
		this.selectContact.setValue(contact_id);
		this.selectContact.setRemoteText(this.getNameFromContactDialog(dialog));
		this.selectContact.hasTyped = false;
		
		this.btnAddContact.setDisabled(true);
		this.disableSavePhoneNumberField();
	},
	disableSavePhoneNumberField : function(){
		this.savePhoneNumberField.setDisabled(true);
		this.savePhoneNumberField.setRawValue('');
	},
	getNameFromContactDialog : function(dialog){
		var data = dialog.formPanel.getForm().getValues();
		var name = '';
		
		if(GO.settings.sort_name == 'last_name'){
			name = data.last_name+', ';
			name += data.first_name;
			
			if(!GO.util.empty(data.middle_name)){
				name += ' '+data.middle_name;
			}
			
		} else {
			name = data.first_name+' ';
			
			if(!GO.util.empty(data.middle_name)){
				name += data.middle_name+' ';
			}
			
			name += data.last_name;
		}
				
		return name;
	}
	
});