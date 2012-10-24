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
		
	}
	,
	buildForm : function(){
		
		this.addPanel({
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
			}]
		});
	}
});