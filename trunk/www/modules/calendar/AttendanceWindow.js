GO.calendar.AttendanceWindow = Ext.extend(GO.dialog.TabbedFormDialog, {
	initComponent : function(){
		
		
		Ext.apply(this, {
			title:"Attendance",
			height: 150,
			width: 300,
			enableApplyButton:false,
			formControllerUrl: 'calendar/attendance'
		});
		

		GO.calendar.AttendanceWindow.superclass.initComponent.call(this);
		
	},
	setExceptionDate : function(date){
		if(!date)
			delete this.formPanel.baseParams.exception_date;
		else
			this.formPanel.baseParams.exception_date=date;
	},
	buildForm : function(){
		
		this.addPanel({
			cls:'go-form-panel',
			layout:'form',
			items:[{
				xtype:'radiogroup',
				hideLabel:true,
				columns:1,
				items:[
				{
					boxLabel: "I will attend",
					name: 'status',
					inputValue: 'ACCEPTED'
				},{
					boxLabel: "I will not attend",
					name: 'status',
					inputValue: 'DECLINED'
				}
				]
			},{
				hideLabel:true,
				name:'notify_organizer',
				xtype:'xcheckbox',
				boxLabel:"Notify organizer by e-mail about my decision"
			}]
		});
	}
});