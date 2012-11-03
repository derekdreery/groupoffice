GO.calendar.AttendanceWindow = Ext.extend(GO.dialog.TabbedFormDialog, {
	initComponent : function(){
		
		
		Ext.apply(this, {
			title:GO.calendar.lang.attendance,
			height: 220,
			width: 340,
			modal:true,
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
					boxLabel: GO.calendar.lang.iWillAttend,
					name: 'status',
					inputValue: 'ACCEPTED'
				},{
					boxLabel: GO.calendar.lang.iWillNotAttend,
					name: 'status',
					inputValue: 'DECLINED'
				},{
					boxLabel: GO.calendar.lang.iMightAttend,
					name: 'status',
					inputValue: 'TENTATIVE'
				},{
					boxLabel: GO.calendar.lang.iWillDecideLater,
					name: 'status',
					inputValue: 'NEEDS-ACTION'
				}
				]
			},{
				hideLabel:true,
				name:'notify_organizer',
				xtype:'xcheckbox',
				boxLabel:GO.calendar.lang.notifyOrganizer
			},{
				xtype:'plainfield',
				fieldLabel:GO.calendar.lang.organizer,
				name:'organizer'
			}]
		});
	}
});