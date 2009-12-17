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

SelectCalendarWindow = function(){
	return {
		accept : function(event_id)
		{
			Ext.Ajax.request({
				url: 'action.php',
				params:{
					task: 'accept', 
					event_id: event_id,
					event_exists: 1
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
		},
		show : function(event_id, event_exists){

			this.selectCalendar = new GO.calendar.SelectCalendar({
				value:GO.calendar.defaultCalendar.id,
				remoteText:GO.calendar.defaultCalendar.name,
				fieldLabel:GO.calendar.lang.selectCalendarForAppointment
			});		
	

			this.window = new Ext.Window({
				renderTo:document.body,
				title: GO.calendar.lang.selectCalendar,
				modal:false,
				autoHeight:true,
				width:500,
				closable:false,
				items: new Ext.FormPanel({
					autoHeight:true,
					items:this.selectCalendar,
					labelAlign:'top',
					cls:'go-form-panel',
					waitMsgTarget:true
				}),
				buttons:[{
					text:GO.lang.cmdOk,
					handler: function(){
						Ext.Ajax.request({
							url: 'action.php',
							params:{
								task: 'accept',
								calendar_id: this.selectCalendar.getValue(),
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
								}							},
							scope: this
						});
					},
					scope: this
				}]
			});
			
			if(!event_exists)
			{
				this.window.show();
			}else
			{
				this.accept(event_id);
			}
			
		}
	}
}