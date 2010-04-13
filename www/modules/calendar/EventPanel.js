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

GO.calendar.EventPanel = Ext.extend(GO.DisplayPanel,{
	link_type : 1,

	loadParams : {task: 'event_with_items'},

	idParam : 'event_id',

	loadUrl : GO.settings.modules.calendar.url+'json.php',

	editHandler : function(){		
		GO.calendar.showEventDialog({event_id: this.link_id});
		this.addSaveHandler(GO.calendar.eventDialog);
	},

	initComponent : function(){

		this.template =
				'<table class="display-panel" cellpadding="0" cellspacing="0" border="0">'+
					'<tr>'+
						'<td colspan="2" class="display-panel-heading">{name}</td>'+
					'</tr>'+
					'<tr>'+
						'<td colspan="2"><table><tr><td>'+GO.calendar.lang.calendar+': </td><td>{calendar_name}</td></tr></table</td>'+
					'</tr>'+
					'<tr>'+
						'<td colspan="2">{html_event}</td>'+
					'</tr>'+					
				'</table>';

		this.template += GO.linksTemplate;


		if(GO.files)
		{
			Ext.apply(this.templateConfig, GO.files.filesTemplateConfig);
			this.template += GO.files.filesTemplate;
		}
		Ext.apply(this.templateConfig, GO.linksTemplateConfig);


		if(GO.comments)
		{
			this.template += GO.comments.displayPanelTemplate;
		}

		GO.calendar.EventPanel.superclass.initComponent.call(this);
	}
});