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

CalendarList = function(config){
	
	Ext.apply(config);
	var tpl = new Ext.XTemplate( 
		'<b>'+GO.calendar.lang.selectCalendarForAppointment+'</b>',
		'<tpl for=".">',
		'<div id="calendar-{id}" class="calendar-wrap">{name}</div>',
		'</tpl>'
	);
	
	CalendarList.superclass.constructor.call(this, {
		store: config.store,
        tpl: tpl,
        singleSelect:true,
        autoHeight:true,
        overClass:'x-view-over',
        itemSelector:'div.calendar-wrap'
	});	
}

Ext.extend(CalendarList,Ext.DataView, {
     onRender : function(ct, position){
          this.el = ct.createChild({
          	tag: 'div', 
          	id:"calendarList", 
          	cls:'calendar-list'
          });
          
          CalendarList.superclass.onRender.apply(this, arguments);
     }

});

SelectCalendarWindow = function(){
	

	return {

		show : function(event_id){
			
			this.calendarsStore = new GO.data.JsonStore({
				url: 'json.php',
				baseParams: {'task': 'calendars'},
				root: 'results',
				totalProperty: 'total',
				id: 'id',
				fields:['id','name','user_name'],
				remoteSort:true
			});
			this.calendarsStore.load();
			
			
			
			var calendarList= new CalendarList(
				{store: this.calendarsStore});				
				
			calendarList.on('click', function(dataview, index){
				
				Ext.Ajax.request({
					url: 'action.php',
					params:{
						task: 'accept', 
						calendar_id: dataview.store.data.items[index].id,
						event_id: event_id
					},
					callback: function(options, success, response){
						
						if(!success)
						{
							Ext.MessageBox.alert(GO.lang.strError, GO.lang.strRequestError);
						}else
						{						
							var responseParams = Ext.decode(response.responseText);
							if(responseParams.success)
							{
								Ext.MessageBox.alert(GO.lang.strSuccess, GO.calendar.lang.closeWindow);
								this.window.close();
							}else
							{
								Ext.MessageBox.alert(GO.lang.strError, responseParams.feedback);
								
							}
						}
												
					},
					scope: this		
				});
				
     		}, this);

			this.window = new Ext.Window({
					renderTo:document.body,
					title: GO.calendar.lang.selectCalendar,
					layout:'fit',
					modal:false,
					height:400,
					width:600,
					closable:false,					
					items: new Ext.Panel({
						items:calendarList,
						cls:'go-form-panel',waitMsgTarget:true,
						autoScroll:true
					})
				});
			
			this.window.show();
			
		}
	}
}